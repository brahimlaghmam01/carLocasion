<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogsController extends Controller
{
    public function index(Request $request): Response
    {
        $user = Auth::user();
        $search = $request->string('search')->toString();
        $action = $request->string('action')->toString();

        $logs = ActivityLog::query()
            ->with('user:id,name')
            ->when($user->isAgencyAdmin(), fn ($q) => $q->where('agency_id', $user->agency_id))
            ->when($search, fn ($q) => $q->where('description', 'like', "%{$search}%"))
            ->when($action, fn ($q) => $q->where('action', $action))
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->through(fn ($log) => [
                'id' => $log->id,
                'user' => $log->user?->name ?? 'System',
                'action' => $log->action,
                'model' => class_basename($log->model),
                'record_id' => $log->record_id,
                'description' => $log->description,
                'created_at' => $log->created_at?->format('M j, Y H:i'),
            ]);

        $actions = ActivityLog::query()
            ->when($user->isAgencyAdmin(), fn ($q) => $q->where('agency_id', $user->agency_id))
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return Inertia::render('Admin/ActivityLogs/Index', [
            'logs' => $logs,
            'actions' => $actions,
            'filters' => ['search' => $search, 'action' => $action],
        ]);
    }
}
