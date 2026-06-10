<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $trainer = $request->user();

        $totalClients = $trainer->clients()->count();

        $clientIds = $trainer->clients()->pluck('id');

        $activePackages = \App\Models\Package::whereIn('client_id', $clientIds)
            ->withCount(['sessions as scheduled_count' => fn($q) => $q->where('status', 'scheduled')])
            ->get()
            ->filter(fn($p) => $p->scheduled_count > 0)
            ->count();

        // Count all today's sessions (any status)
        $todaySessions = Session::whereIn('client_id', $clientIds)
            ->whereDate('scheduled_date', Carbon::today())
            ->count();

        // Expiring packages: ≤2 scheduled sessions left
        $expiringPackages = \App\Models\Package::whereIn('client_id', $clientIds)
            ->with('client')
            ->withCount([
                'sessions as scheduled_count' => fn($q) => $q->where('status', 'scheduled'),
            ])
            ->get()
            ->filter(fn($p) => $p->scheduled_count > 0 && $p->scheduled_count <= 2);

        // Upcoming sessions today and tomorrow (all statuses, sorted by time)
        $upcomingSessions = Session::with(['client', 'package'])
            ->whereIn('client_id', $clientIds)
            ->where(function ($q) {
                $q->whereDate('scheduled_date', Carbon::today())
                  ->orWhereDate('scheduled_date', Carbon::tomorrow());
            })
            ->orderBy('scheduled_date')
            ->orderByRaw('CASE WHEN scheduled_time IS NULL THEN 1 ELSE 0 END')
            ->orderBy('scheduled_time')
            ->get();

        // Daily earnings: sum of price-per-session for completed sessions today
        $todayCompleted = Session::with('package')
            ->whereIn('client_id', $clientIds)
            ->whereDate('scheduled_date', Carbon::today())
            ->where('status', 'completed')
            ->get();

        $todayEarnings = $todayCompleted->sum(function ($session) {
            $pkg = $session->package;
            if (!$pkg || $pkg->total_sessions == 0) return 0;
            return round($pkg->price / $pkg->total_sessions);
        });

        $todayCompletedCount = $todayCompleted->count();

        // Clients for "add session" modal — all clients with at least one package
        $addClients = $trainer->clients()
            ->whereHas('packages')
            ->with(['packages' => fn($q) => $q->latest()])
            ->orderBy('full_name')
            ->get();

        return view('dashboard', compact(
            'totalClients', 'activePackages', 'todaySessions',
            'expiringPackages', 'upcomingSessions',
            'todayEarnings', 'todayCompletedCount', 'addClients'
        ));
    }
}
