<?php
// app/Http/Controllers/AuditLogController.php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuditLogController extends Controller
{
    // Allowed modules for the filter dropdown — extend as needed
    const MODULES = [
        'BPLS',
        'RPTA',
        'Settings',
        'Auth',
        'OR Assignment',
    ];

    // Allowed actions for the filter dropdown
    const ACTIONS = [
        'created',
        'updated',
        'deleted',
        'viewed',
        'payment',
        'status_change',
        'login',
        'logout',
        'export',
    ];

    // =========================================================================
    // INDEX — Main audit log viewer page
    // GET /audit-logs
    // =========================================================================

    public function index()
    {
        return view('modules.audit-logs.index', [
            'modules' => self::MODULES,
            'actions' => self::ACTIONS,
        ]);
    }

    // =========================================================================
    // DATA — JSON endpoint for Alpine.js / DataTables
    // GET /audit-logs/data
    // =========================================================================

    public function data(Request $request): JsonResponse
    {
        try {
            $query = AuditLog::query()->with('user');

            // ── Filters ──────────────────────────────────────────────────────

            if ($request->filled('module')) {
                $query->where('module', $request->module);
            }

            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->filled('user_name')) {
                $query->where('user_name', 'like', '%' . $request->user_name . '%');
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('description', 'like', "%{$s}%")
                        ->orWhere('user_name', 'like', "%{$s}%")
                        ->orWhere('ip_address', 'like', "%{$s}%")
                        ->orWhere('url', 'like', "%{$s}%");
                });
            }

            // ── Model-specific lookup (e.g. show all logs for a BusinessEntry) ─
            if ($request->filled('model_type') && $request->filled('model_id')) {
                $query->where('model_type', $request->model_type)
                    ->where('model_id', $request->model_id);
            }

            // ── Sorting ───────────────────────────────────────────────────────

            $sortBy = in_array($request->sort_by, ['created_at', 'module', 'action', 'user_name', 'status'])
                ? $request->sort_by
                : 'created_at';
            $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortBy, $sortDir);

            // ── Pagination ────────────────────────────────────────────────────

            $perPage = min((int) ($request->per_page ?? 25), 100);
            $records = $query->paginate($perPage);

            return response()->json([
                'data' => $records->items(),
                'total' => $records->total(),
                'per_page' => $records->perPage(),
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch audit logs: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================================
    // SHOW — Detail view of a single log entry
    // GET /audit-logs/{auditLog}
    // =========================================================================

    public function show(AuditLog $auditLog): JsonResponse
    {
        return response()->json($auditLog->load('user'));
    }

    // =========================================================================
    // PURGE — Delete logs older than N days (admin only)
    // DELETE /audit-logs/purge
    // =========================================================================

    public function purge(Request $request): JsonResponse
    {
        $request->validate([
            'older_than_days' => 'required|integer|min:7|max:3650',
        ]);

        $cutoff = now()->subDays((int) $request->older_than_days);
        $deleted = AuditLog::where('created_at', '<', $cutoff)->delete();

        // Record the purge itself
        AuditLog::record(
            'Settings',
            'deleted',
            "Purged {$deleted} audit log entries older than {$request->older_than_days} days.",
            null,
            ['extra' => ['cutoff' => $cutoff->toDateTimeString(), 'deleted_count' => $deleted]]
        );

        return response()->json([
            'success' => true,
            'deleted_count' => $deleted,
            'cutoff' => $cutoff->toDateTimeString(),
        ]);
    }

    // =========================================================================
    // EXPORT — Download as CSV
    // GET /audit-logs/export
    // =========================================================================

    public function export(Request $request)
    {
        $query = AuditLog::query();

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderByDesc('created_at')->limit(10000)->get();

        // Log the export action
        AuditLog::record('Settings', 'export', 'Exported audit logs to CSV.');

        $filename = 'audit-logs-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $fh = fopen('php://output', 'w');

            // CSV header row
            fputcsv($fh, [
                'ID',
                'Date',
                'User',
                'Module',
                'Action',
                'Description',
                'Model',
                'Model ID',
                'IP Address',
                'Method',
                'URL',
                'Status',
            ]);

            foreach ($logs as $log) {
                fputcsv($fh, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user_name,
                    $log->module,
                    $log->action,
                    $log->description,
                    $log->model_type,
                    $log->model_id,
                    $log->ip_address,
                    $log->method,
                    $log->url,
                    $log->status,
                ]);
            }

            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =========================================================================
    // STATS — Summary counts for dashboard widgets
    // GET /audit-logs/stats
    // =========================================================================

    public function stats(): JsonResponse
    {
        $today = now()->toDateString();

        return response()->json([
            'total' => AuditLog::count(),
            'today' => AuditLog::whereDate('created_at', $today)->count(),
            'failed_today' => AuditLog::whereDate('created_at', $today)->where('status', 'failed')->count(),
            'by_module' => AuditLog::selectRaw('module, count(*) as cnt')->groupBy('module')->pluck('cnt', 'module'),
            'by_action' => AuditLog::selectRaw('action, count(*) as cnt')->groupBy('action')->pluck('cnt', 'action'),
        ]);
    }
}