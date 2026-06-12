<?php

namespace App\Http\Controllers;

use App\Models\User;
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
}
