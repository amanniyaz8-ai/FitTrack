<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::whereNotNull('trial_ends_at')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.users', compact('users'));
    }

    public function grantAccess(Request $request, User $user)
    {
        $request->validate(['months' => 'required|integer|in:1,6,12']);

        $months = (int) $request->months;

        // Extend from today or from current subscription end (whichever is later)
        $base = $user->subscription_ends_at && $user->subscription_ends_at->isFuture()
            ? $user->subscription_ends_at
            : now();

        $user->update(['subscription_ends_at' => $base->addMonths($months)]);

        return back()->with('success', "Доступ выдан: {$user->name} на {$months} мес. до " . $user->fresh()->subscription_ends_at->format('d.m.Y'));
    }

    public function revokeAccess(User $user)
    {
        $user->update(['subscription_ends_at' => now()->subDay()]);
        return back()->with('success', "Доступ отозван у {$user->name}.");
    }

    public function tickets()
    {
        $tickets = SupportTicket::orderByRaw("FIELD(status,'new','in_progress','resolved')")
            ->orderByDesc('created_at')
            ->get();
        return view('admin.tickets', compact('tickets'));
    }

    public function updateTicketStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate(['status' => 'required|in:new,in_progress,resolved']);
        $ticket->update(['status' => $request->status]);
        return back();
    }
}
