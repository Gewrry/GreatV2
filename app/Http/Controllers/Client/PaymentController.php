<?php
// app/Http/Controllers/Client/PaymentController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\onlineBPLS\BplsApplication;
use App\Models\onlineBPLS\BplsOnlinePayment;
use App\Models\onlineBPLS\BplsActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // -----------------------------------------------------------------------
    // INDEX — assessed/paid/approved applications grouped by payment status
    // -----------------------------------------------------------------------
    public function index()
    {
        $clientId = Auth::guard('client')->id();

        $applications = BplsApplication::where('client_id', $clientId)
            ->whereIn('workflow_status', ['assessed', 'paid', 'approved'])
            ->whereNotNull('assessment_amount')
            ->with(['payment', 'business'])
            ->latest()
            ->get();

        $applications->each(function ($app) {
            $app->installments = $this->buildInstallments($app);
        });

        $grouped = [
            'pending' => $applications->filter(fn($a) => $a->workflow_status === 'assessed'),
            'partial' => $applications->filter(fn($a) => $a->workflow_status === 'paid'),
            'paid'    => $applications->filter(fn($a) => $a->workflow_status === 'approved'),
        ];

        return view('client.payments.index', compact('grouped'));
    }

    // -----------------------------------------------------------------------
    // SHOW — payment page
    // -----------------------------------------------------------------------
    public function show(BplsApplication $application)
    {
        $this->authorizeClient($application);
        $application->load(['business', 'payment']);
        $installments = $this->buildInstallments($application);

        return view('client.applications.payment', compact('application', 'installments'));
    }

    // -----------------------------------------------------------------------
    // INITIATE — create PayMongo payment link, redirect to checkout
    // -----------------------------------------------------------------------
    public function initiate(Request $request, BplsApplication $application)
    {
        $this->authorizeClient($application);

        $request->validate([
            'payment_method'     => 'required|in:gcash,maya,card,landbank,over_the_counter',
            'installment_number' => 'nullable|integer|min:1',
        ]);

        if (!$application->assessment_amount) {
            return back()->with('error', 'No assessment found for this application.');
        }

        $installmentNumber = (int) ($request->installment_number ?? 1);
        $installmentAmount = round($application->installment_amount, 2);

        // ── Over the Counter: no gateway, just record and instruct ────────
        if ($request->payment_method === 'over_the_counter') {
            $payment = $this->findOrCreatePayment($application, $installmentNumber, $installmentAmount, 'over_the_counter');

            return redirect()
                ->route('client.payment.show', $application->id)
                ->with('success', 'Please proceed to the Municipal Treasury with your Application No. ' . $application->application_number . '. Reference: ' . $payment->reference_number);
        }

        // ── LandBank: similar manual flow ─────────────────────────────────
        if ($request->payment_method === 'landbank') {
            $payment = $this->findOrCreatePayment($application, $installmentNumber, $installmentAmount, 'landbank');

            return redirect()
                ->route('client.payment.show', $application->id)
                ->with('success', 'Please proceed to LandBank and use Reference No. ' . $payment->reference_number . ' when paying.');
        }

        // ── PayMongo: GCash, Maya, Card ───────────────────────────────────
        $payment = $this->findOrCreatePayment($application, $installmentNumber, $installmentAmount, $request->payment_method);

        // Map method to PayMongo source type
        $sourceType = match($request->payment_method) {
            'gcash' => 'gcash',
            'maya'  => 'paymaya',
            'card'  => 'card',
            default => 'gcash',
        };

        try {
            $response = Http::withBasicAuth(config('services.paymongo.secret_key'), '')
                ->post('https://api.paymongo.com/v1/links', [
                    'data' => [
                        'attributes' => [
                            'amount'      => (int) round($installmentAmount * 100), // centavos
                            'description' => 'Business Permit Fee — ' . $application->application_number
                                           . ($application->installment_count > 1 ? ' (Installment ' . $installmentNumber . ' of ' . $application->installment_count . ')' : ''),
                            'remarks'     => $payment->reference_number,
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json('data');

                $payment->update([
                    'gateway_transaction_id' => $data['id'],
                    'gateway_response'       => $data,
                    'status'                 => 'pending',
                ]);

                // Redirect client to PayMongo hosted checkout
                return redirect($data['attributes']['checkout_url']);
            }

            $errorMsg = $response->json('errors.0.detail') ?? 'Payment gateway error.';
            return back()->with('error', $errorMsg . ' Please try again or choose another method.');

        } catch (\Exception $e) {
            Log::error('PayMongo initiate error', ['error' => $e->getMessage(), 'application' => $application->id]);
            return back()->with('error', 'Could not connect to payment gateway. Please try again later.');
        }
    }

    // -----------------------------------------------------------------------
    // SUCCESS — PayMongo redirects here after checkout
    // -----------------------------------------------------------------------
    public function success(Request $request, BplsApplication $application)
    {
        // Guard: must belong to logged-in client
        if ($application->client_id !== Auth::guard('client')->id()) {
            abort(403);
        }

        $linkId = $request->query('link_id');

        // Find the payment by gateway transaction ID
        $payment = BplsOnlinePayment::where('bpls_application_id', $application->id)
            ->where('gateway_transaction_id', $linkId)
            ->first();

        if (!$payment) {
            return redirect()
                ->route('client.applications.show', $application->id)
                ->with('error', 'Payment record not found.');
        }

        // Verify with PayMongo
        try {
            $response = Http::withBasicAuth(config('services.paymongo.secret_key'), '')
                ->get('https://api.paymongo.com/v1/links/' . $linkId);

            if ($response->successful()) {
                $data   = $response->json('data');
                $status = $data['attributes']['status'] ?? null;

                if ($status === 'paid') {
                    $this->confirmPayment($payment, $application, $data);

                    return redirect()
                        ->route('client.applications.show', $application->id)
                        ->with('success', '✅ Payment confirmed! Your application has been forwarded for final approval.');
                }
            }
        } catch (\Exception $e) {
            Log::error('PayMongo verify error', ['error' => $e->getMessage()]);
        }

        return redirect()
            ->route('client.applications.show', $application->id)
            ->with('error', 'Payment could not be verified. If you were charged, the Treasury will reconcile it shortly.');
    }

    // -----------------------------------------------------------------------
    // WEBHOOK — PayMongo server-to-server event (reliable confirmation)
    // -----------------------------------------------------------------------
    public function webhook(Request $request)
    {
        // Verify PayMongo webhook signature
        $sigHeader = $request->header('Paymongo-Signature');
        $secret    = config('services.paymongo.webhook_secret');

        if ($secret && $sigHeader) {
            [$tPart, $tePart] = array_pad(explode(',', $sigHeader), 2, '');
            $timestamp  = ltrim($tPart, 't=');
            $testSig    = ltrim($tePart, 'te=');
            $payload    = $timestamp . '.' . $request->getContent();
            $computed   = hash_hmac('sha256', $payload, $secret);

            if (!hash_equals($computed, $testSig)) {
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $event = $request->json('data.attributes.type');
        $data  = $request->json('data.attributes.data');

        if ($event === 'link.payment.paid') {
            $linkId  = $data['id'] ?? null;
            $payment = BplsOnlinePayment::where('gateway_transaction_id', $linkId)->first();

            if ($payment && !$payment->isPaid()) {
                $application = $payment->application;
                $this->confirmPayment($payment, $application, $data);
                Log::info('PayMongo webhook: payment confirmed', ['ref' => $payment->reference_number]);
            }
        }

        return response()->json(['received' => true]);
    }

    // -----------------------------------------------------------------------
    // CONFIRM — Manual OR entry (OTC/LandBank after treasurer records it)
    // -----------------------------------------------------------------------
    public function confirm(Request $request, BplsApplication $application)
    {
        $this->authorizeClient($application);

        $request->validate(['or_number' => 'required|string|max:50']);

        $payment = $application->payment;
        if (!$payment) {
            return back()->with('error', 'No payment record found.');
        }

        $payment->update([
            'or_number' => $request->or_number,
            'status'    => 'pending',
        ]);

        return redirect()
            ->route('client.applications.show', $application->id)
            ->with('success', 'OR Number ' . $request->or_number . ' submitted. The Treasury will verify your payment shortly.');
    }

    // -----------------------------------------------------------------------
    // Build installments array for display
    // -----------------------------------------------------------------------
    public function buildInstallments(BplsApplication $app): array
    {
        $total   = (float) $app->assessment_amount;
        $count   = $app->installment_count;
        $perAmt  = round($total / $count, 2);

        $labels = match($app->mode_of_payment) {
            'quarterly'   => ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'],
            'semi_annual' => ['1st Half', '2nd Half'],
            default       => ['Annual Payment'],
        };

        $payments = BplsOnlinePayment::where('bpls_application_id', $app->id)->get();

        $installments = [];
        for ($i = 1; $i <= $count; $i++) {
            $payment = $payments->firstWhere('installment_number', $i);
            $installments[] = [
                'number'    => $i,
                'label'     => $labels[$i - 1] ?? "Installment #{$i}",
                'amount'    => $perAmt,
                'status'    => $payment?->status ?? 'unpaid',
                'payment'   => $payment,
                'paid_at'   => $payment?->paid_at,
                'or_number' => $payment?->or_number ?? ($i === 1 ? $app->or_number : null),
            ];
        }

        return $installments;
    }

    // -----------------------------------------------------------------------
    // Private: find or create a BplsOnlinePayment for an installment
    // -----------------------------------------------------------------------
    private function findOrCreatePayment(
        BplsApplication $application,
        int $installmentNumber,
        float $amount,
        string $method
    ): BplsOnlinePayment {
        $payment = BplsOnlinePayment::where('bpls_application_id', $application->id)
            ->where('installment_number', $installmentNumber)
            ->whereIn('status', ['pending', 'unpaid'])
            ->first();

        if (!$payment) {
            $ref = 'PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $payment = BplsOnlinePayment::create([
                'bpls_application_id' => $application->id,
                'reference_number'    => $ref,
                'amount_paid'         => $amount,
                'payment_year'        => now()->year,
                'payment_method'      => $method,
                'installment_number'  => $installmentNumber,
                'installment_total'   => $application->installment_count,
                'status'              => 'pending',
            ]);
        } else {
            $payment->update(['payment_method' => $method]);
        }

        return $payment;
    }

    // -----------------------------------------------------------------------
    // Private: mark a payment as paid and transition application if needed
    // -----------------------------------------------------------------------
    private function confirmPayment(BplsOnlinePayment $payment, BplsApplication $application, array $gatewayData): void
    {
        $payment->update([
            'status'                 => 'paid',
            'paid_at'                => now(),
            'gateway_response'       => $gatewayData,
        ]);

        // Check if ALL installments are paid
        $totalInstallments = $application->installment_count;
        $paidCount = BplsOnlinePayment::where('bpls_application_id', $application->id)
            ->where('status', 'paid')
            ->count();

        if ($paidCount >= $totalInstallments && $application->workflow_status === 'assessed') {
            $application->update([
                'workflow_status' => 'paid',
                'paid_at'         => now(),
            ]);

            BplsActivityLog::create([
                'bpls_application_id' => $application->id,
                'actor_type'          => 'client',
                'actor_id'            => $application->client_id,
                'action'              => 'payment_confirmed',
                'from_status'         => 'assessed',
                'to_status'           => 'paid',
                'remarks'             => 'Payment confirmed via ' . $payment->payment_method_label . '. Ref: ' . $payment->reference_number,
            ]);
        }
    }

    // -----------------------------------------------------------------------
    // Private: authorize client owns this application and it's payable
    // -----------------------------------------------------------------------
    private function authorizeClient(BplsApplication $application): void
    {
        if ($application->client_id !== Auth::guard('client')->id()) {
            abort(403, 'Unauthorized.');
        }

if (!in_array($application->workflow_status, ['assessed', 'paid', 'approved'])) {
    abort(403, 'Payment is not available at this stage.');
}
    }
}