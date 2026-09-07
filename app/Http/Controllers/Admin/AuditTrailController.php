<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AuditTrailController extends Controller implements HasMiddleware
{
    /** AMI entities whose changes are recorded. */
    private const TYPES = [
        \App\Models\PeriodeAmi::class => 'Periode AMI',
        \App\Models\JadwalAmi::class => 'Jadwal AMI',
        \App\Models\PenugasanAmi::class => 'Penugasan',
        \App\Models\TemuanAmi::class => 'Temuan',
        \App\Models\TindakLanjut::class => 'Tindak Lanjut',
        \App\Models\Auditor::class => 'Auditor',
        \App\Models\StandarMutu::class => 'Standar Mutu',
        \App\Models\ButirInstrumen::class => 'Butir Instrumen',
    ];

    public static function middleware(): array
    {
        return [
            new Middleware('permission:audit-trail.view', only: ['index']),
        ];
    }

    public function index(Request $request)
    {
        $query = AuditTrail::with('user')->latest('created_at')->latest('id');

        if ($request->filled('type')) {
            $query->where('auditable_type', $request->type);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $trails = $query->paginate(30)->withQueryString();

        $typeOptions = collect(self::TYPES)
            ->filter(fn ($label, $class) => class_exists($class));

        $eventOptions = ['created', 'updated', 'deleted', 'verified', 'approved', 'rejected', 'closed', 'reopened', 'accepted', 'activated', 'completed'];

        $users = \App\Models\User::whereIn('id', AuditTrail::whereNotNull('user_id')->distinct()->pluck('user_id'))
            ->orderBy('name')->get(['id', 'name']);

        return view('admin.ami.audit-trail.index', compact('trails', 'typeOptions', 'eventOptions', 'users'));
    }
}
