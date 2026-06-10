<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Client;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $userId    = Auth::id();
        $clientIds = Client::where('trainer_id', $userId)->pluck('id');

        $now        = Carbon::now();
        $thisWeekStart  = $now->copy()->startOfWeek()->format('Y-m-d');
        $thisMonthStart = $now->copy()->startOfMonth()->format('Y-m-d');
        $thisYearStart  = $now->copy()->startOfYear()->format('Y-m-d');
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth()->format('Y-m-d');
        $lastMonthEnd   = $now->copy()->subMonth()->endOfMonth()->format('Y-m-d');
        $today          = $now->format('Y-m-d');

        // --- Оплачено за текущий месяц (сумма пакетов с payment_date в этом месяце) ---
        $paidThisMonth = Package::whereIn('client_id', $clientIds)
            ->where('is_paid', true)
            ->whereYear('payment_date', $now->year)
            ->whereMonth('payment_date', $now->month)
            ->sum('price');

        // --- Тренировки в месяце ---
        $sessionsThisMonth = Session::whereIn('client_id', $clientIds)
            ->where('status', 'completed')
            ->whereDate('scheduled_date', '>=', $thisMonthStart)
            ->whereDate('scheduled_date', '<=', $today)
            ->count();

        $sessionsLastMonth = Session::whereIn('client_id', $clientIds)
            ->where('status', 'completed')
            ->whereDate('scheduled_date', '>=', $lastMonthStart)
            ->whereDate('scheduled_date', '<=', $lastMonthEnd)
            ->count();

        // --- Все выполненные сессии с пакетами (для заработка) ---
        $allCompleted = Session::with('package')
            ->whereIn('client_id', $clientIds)
            ->where('status', 'completed')
            ->get();

        $calcEarnings = fn($sessions) => $sessions->sum(function ($s) {
            $p = $s->package;
            return ($p && $p->total_sessions > 0) ? round($p->price / $p->total_sessions) : 0;
        });

        $earningsWeek  = $calcEarnings($allCompleted->filter(
            fn($s) => $s->scheduled_date->format('Y-m-d') >= $thisWeekStart
        ));
        $earningsMonth = $calcEarnings($allCompleted->filter(
            fn($s) => $s->scheduled_date->format('Y-m-d') >= $thisMonthStart
        ));
        $earningsYear  = $calcEarnings($allCompleted->filter(
            fn($s) => $s->scheduled_date->format('Y-m-d') >= $thisYearStart
        ));

        // --- Фильтр заработка по произвольным датам ---
        $filterFrom = $request->input('from', $thisMonthStart);
        $filterTo   = $request->input('to', $today);
        $earningsCustom = $calcEarnings($allCompleted->filter(
            fn($s) => $s->scheduled_date->format('Y-m-d') >= $filterFrom
                   && $s->scheduled_date->format('Y-m-d') <= $filterTo
        ));
        $sessionsCustom = $allCompleted->filter(
            fn($s) => $s->scheduled_date->format('Y-m-d') >= $filterFrom
                   && $s->scheduled_date->format('Y-m-d') <= $filterTo
        )->count();

        // --- Динамика по месяцам (последние 6) ---
        $monthlyStats    = collect();
        $monthlyEarnings = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $ms    = $month->copy()->startOfMonth()->format('Y-m-d');
            $me    = $month->copy()->endOfMonth()->format('Y-m-d');

            $cnt = Session::whereIn('client_id', $clientIds)
                ->where('status', 'completed')
                ->whereDate('scheduled_date', '>=', $ms)
                ->whereDate('scheduled_date', '<=', $me)
                ->count();

            $earned = $calcEarnings($allCompleted->filter(
                fn($s) => $s->scheduled_date->format('Y-m-d') >= $ms
                       && $s->scheduled_date->format('Y-m-d') <= $me
            ));

            $locale = in_array(app()->getLocale(), ['kk', 'ru']) ? app()->getLocale() : 'ru';
            $label = $month->locale($locale)->translatedFormat('M Y');
            $monthlyStats->push(['label' => $label, 'month' => $month->format('m.Y'), 'count' => $cnt]);
            $monthlyEarnings->push(['label' => $label, 'month' => $month->format('m.Y'), 'earned' => $earned]);
        }

        // --- Рейтинг клиентов ---
        $clients = Client::where('trainer_id', $userId)->with(['sessions', 'packages'])->get();

        $clientScores = $clients->map(function ($client) {
            $sessions = $client->sessions;
            $total    = $sessions->count();
            if ($total === 0) return null;

            $completed = $sessions->where('status', 'completed')->count();
            $missed    = $sessions->where('status', 'missed')->count();
            $cancelled = $sessions->where('status', 'cancelled')->count();
            $paidPkgs  = $client->packages->where('is_paid', true)->count();
            $totalPkgs = $client->packages->count();
            $score     = ($completed * 2) - ($missed * 1) - ($cancelled * 0.5) + ($paidPkgs * 1);

            return [
                'client'     => $client,
                'score'      => $score,
                'completed'  => $completed,
                'missed'     => $missed,
                'cancelled'  => $cancelled,
                'total'      => $total,
                'paid_pkgs'  => $paidPkgs,
                'total_pkgs' => $totalPkgs,
                'attend_pct' => $total > 0 ? round($completed / $total * 100) : 0,
            ];
        })->filter()->sortByDesc('score');

        $bestClient  = $clientScores->first();
        $clientRanks = $clientScores->values()->take(5);

        return view('statistics', compact(
            'paidThisMonth',
            'sessionsThisMonth', 'sessionsLastMonth',
            'monthlyStats', 'monthlyEarnings',
            'earningsWeek', 'earningsMonth', 'earningsYear',
            'earningsCustom', 'sessionsCustom',
            'filterFrom', 'filterTo',
            'bestClient', 'clientRanks'
        ));
    }
}
