<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Catatan tindakan staff dan admin. Hanya admin. Penanggung jawab: Marcel.
 */
class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'from'    => ['nullable', 'date'],
            'to'      => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $logs = ActivityLog::query()
            ->with('user')
            ->when($filters['user_id'] ?? null, fn ($q, $id) => $q->where('user_id', $id))
            ->when($filters['from'] ?? null, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        $actors = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF])->orderBy('name')->get(['id', 'name', 'role']);

        return view('dashboard.activity-logs.index', compact('logs', 'actors', 'filters'));
    }
}
