<?php

namespace App\Http\Controllers;

use App\Models\TrainerExpense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'type'        => 'required|in:rent,percent',
            'rent_amount' => 'nullable|numeric|min:0',
            'gym_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        TrainerExpense::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        return back()->with('success', 'Расходы сохранены.');
    }
}
