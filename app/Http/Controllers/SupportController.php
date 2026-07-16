<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function show()
    {
        return view('support.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'nullable|email|max:100',
            'phone'   => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        SupportTicket::create(array_merge(
            $request->only('name', 'email', 'phone', 'message'),
            ['user_id' => Auth::id()]
        ));

        return back()->with('success', true);
    }

    // Страница поддержки внутри кабинета
    public function cabinet()
    {
        $user    = Auth::user();
        $tickets = SupportTicket::where('user_id', $user->id)->latest()->get();
        return view('support.cabinet', compact('tickets', 'user'));
    }
}
