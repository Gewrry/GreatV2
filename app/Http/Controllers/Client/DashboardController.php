<?php
// app/Http/Controllers/Client/DashboardController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();

        $applications = $client->applications()
            ->with('business')
            ->latest()
            ->take(10)
            ->get();

        $counts = [
            'draft' => $client->applications()->where('workflow_status', 'draft')->count(),
            'submitted' => $client->applications()->where('workflow_status', 'submitted')->count(),
            'for_payment' => $client->applications()->where('workflow_status', 'payment')->count(),
            'approved' => $client->applications()->where('workflow_status', 'approved')->count(),
        ];

        // Identify applications with pending installments (Paid or Approved but OR count < installment total)
        $pendingInstallmentApps = $client->applications()
            ->whereIn('workflow_status', ['paid', 'approved'])
            ->with(['orAssignments' => function($q) { $q->where('status', 'unpaid'); }])
            ->get()
            ->filter(function($app) {
                return $app->orAssignments->isNotEmpty();
            });

        return view('client.dashboard', compact('client', 'applications', 'counts', 'pendingInstallmentApps'));
    }
}