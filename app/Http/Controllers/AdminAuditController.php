<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuditController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('actor');

        if ($request->filled('action')) {
            $query->where('action', $request->query('action'));
        }

        if ($request->filled('actor_id')) {
            $query->where('actor_id', $request->query('actor_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->query('date_to'));
        }

        if ($request->filled('q')) {
            $term = '%' . $request->query('q') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('action', 'like', $term)
                  ->orWhere('auditable_type', 'like', $term)
                  ->orWhere('meta', 'like', $term);
            });
        }

        $logs = $query->latest('id')->paginate(25)->withQueryString();
        $actors = User::whereIn('id', AuditLog::select('actor_id')->distinct())->orderBy('name')->get();
        $actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.audit.index', [
            'logs' => $logs,
            'actors' => $actors,
            'actions' => $actions,
            'filters' => $request->only(['action', 'actor_id', 'date_from', 'date_to', 'q']),
        ]);
    }
}
