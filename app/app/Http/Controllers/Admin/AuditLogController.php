<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Activity::class);

        $query = Activity::query()
            ->with(['causer', 'subject'])
            ->latest();

        if ($request->filled('user_id')) {
            $query->where('causer_id', (int) $request->input('user_id'));
        }

        if ($request->filled('subject_type')) {
            // Filtra por nombre corto o fully qualified — basta con un substring
            // case-insensitive del subject_type guardado (ej. App\Models\News).
            $query->where('subject_type', 'like', '%'.$request->input('subject_type').'%');
        }

        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->input('to').' 23:59:59');
        }

        return view('admin.audit-log.index', [
            'logs'    => $query->paginate(50)->withQueryString(),
            'users'   => User::orderBy('name')->get(['id', 'name', 'email']),
            'filters' => [
                'user_id'      => $request->input('user_id'),
                'subject_type' => $request->input('subject_type'),
                'from'         => $request->input('from'),
                'to'           => $request->input('to'),
            ],
        ]);
    }
}
