<?php
// app/Http/Controllers/Client/PaymentController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\onlineBPLS\BplsApplication;
use App\Models\onlineBPLS\BplsOnlinePayment;
use App\Models\onlineBPLS\BplsActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // -----------------------------------------------------------------------
    // SHOW — payment page with assessment breakdown
    // -----------------------------------------------------------------------
    public function show(BplsApplication $application)
    {
        $this->authorize($application);

        $application->load(['business', 'assessment', 'payment']);

        return view('client.applications.payment', compact('application'));
    }

    // -----------------------------------------------------------------------
    // INITIATE — create payment record, redirect to gateway
    // -----------------------------------------------------------------------
    public function initiate(Request $request, BplsApplication $application)
    {
        $this->authorize($application);

        $request->validate([
            'payment_method' => 'required|in:gcash,maya,landbank,over_the_counter',
        ]);

        $assessment = $application->assessment;
        if (!$assessment) {
            return back()->with('error', 'No assessment found for this application.');
        }

        // If payment already exists and is pending, reuse it
        $payment = $application->payment;
        if (!$payment) {
            // Generate unique reference number
            $ref = 'PAY-' . date('Ymd') . '-' . str_pad(BplsOnlinePayment::count() + 1, 5, '0', STR_PAD_LEFT);

            $payment = BplsOnlinePayment::create([
                'bpls_application_id' => $application->id,
                'reference_number' => $ref,
                'amount_paid' => $assessment->total_due,
                'payment_year' => now()->year,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
            ]);
        } else {
            // Update method if switching
            $payment->update(['payment_method' => $request->payment_method]);
        }

        // For OTC / LandBank — just show instructions
        if (in_array($request->payment_method, ['over_the_counter', 'landbank'])) {
            return redirect()
                ->route('client.payment.show', $application->id)
                ->with('success', 'Please proceed to the Municipal Treasury to pay ' . $assessment->formatted_total . '. Reference No: ' . $payment->reference_number);
        }

        // For GCash / Maya — PayMongo integration
        // Uncomment when PayMongo keys are configured
        /*
        try {
            $response = Http::withBasicAuth(config('services.paymongo.secret_key'), '')
                ->post('https://api.paymongo.com/v1/links', [
                    'data' => [
                        'attributes' => [
                            'amount'      => (int)($assessment->total_due * 100), // centavos
                            'description' => 'BPLS Business Permit - ' . $application->application_number,
                            'remarks'     => $payment->reference_number,
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json('data');
                $payment->update([
                    'gateway_transaction_id' => $data['id'],
                    'gateway_response'       => $data,
                ]);
                return redirect($data['attributes']['checkout_url']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Payment gateway error. Please try again or choose another method.');
        }
        */

        // Placeholder redirect — replace with PayMongo checkout URL
        return redirect()
            ->route('client.payment.show', $application->id)
            ->with('success', 'Payment initiated. Reference No: ' . $payment->reference_number . '. Complete the payment using your selected e-wallet.');
    }

    // -----------------------------------------------------------------------
    // CONFIRM (Manual OR entry) — for OTC payments after treasurer records it
    // -----------------------------------------------------------------------
    public function confirm(Request $request, BplsApplication $application)
    {
        $this->authorize($application);

        $request->validate([
            'or_number' => 'required|string|max:50',
        ]);

        $payment = $application->payment;
        if (!$payment) {
            return back()->with('error', 'No payment record found.');
        }

        $payment->update([
            'or_number' => $request->or_number,
            'status' => 'pending', // Treasurer still needs to verify OTC
        ]);

        return redirect()
            ->route('client.applications.show', $application->id)
            ->with('success', 'OR Number ' . $request->or_number . ' submitted. The Treasury will verify your payment shortly.');
    }

    // -----------------------------------------------------------------------
    // SUCCESS — webhook/redirect handler (PayMongo)
    // -----------------------------------------------------------------------
    public function success(Request $request, BplsApplication $application)
    {
        $this->authorize($application);

        $payment = $application->payment;

        if (!$payment || !$payment->gateway_transaction_id) {
            return redirect()->route('client.applications.show', $application->id);
        }

        // Verify with PayMongo
        // Uncomment when keys are configured
        /*
        try {
            $response = Http::withBasicAuth(config('services.paymongo.secret_key'), '')
                ->get('https://api.paymongo.com/v1/links/' . $payment->gateway_transaction_id);

            if ($response->successful()) {
                $data = $response->json('data');
                if ($data['attributes']['status'] === 'paid') {
                    $payment->update([
                        'status'           => 'paid',
                        'paid_at'          => now(),
                        'gateway_response' => $data,
                    ]);

                    // Transition workflow: payment confirmed
                    $application->update(['paid_at' => now()]);

                    BplsActivityLog::create([
                        'bpls_application_id' => $application->id,
                        'actor_type'          => 'client',
                        'actor_id'            => Auth::guard('client')->id(),
                        'action'              => 'paid',
                        'from_status'         => 'payment',
                        'to_status'           => 'payment',
                        'remarks'             => 'Payment confirmed via ' . $payment->payment_method_label,
                    ]);

                    return redirect()
                        ->route('client.applications.show', $application->id)
                        ->with('success', '✅ Payment confirmed! Your application is now pending final approval.');
                }
            }
        } catch (\Exception $e) {
            // log error
        }
        */

        return redirect()->route('client.applications.show', $application->id);
    }

    // -----------------------------------------------------------------------
    // Authorization helper
    // -----------------------------------------------------------------------
    private function authorize(BplsApplication $application): void
    {
        if ($application->client_id !== Auth::guard('client')->id()) {
            abort(403, 'Unauthorized.');
        }

        if ($application->workflow_status !== 'payment') {
            abort(403, 'Payment is not available at this stage.');
        }
    }
}