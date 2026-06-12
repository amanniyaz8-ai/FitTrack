<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Http\Requests\UpdateSessionRequest;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function update(UpdateSessionRequest $request, Session $session)
    {
        $this->authorize('update', $session->package);

        $session->update($request->validated());

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'status' => $session->status]);
        }

        return back()->with('success', 'Статус занятия обновлён.');
    }

    public function reschedule(Request $request, Session $session)
    {
        $this->authorize('update', $session->package);

        $request->validate([
            'new_date' => 'required|date|after_or_equal:today',
            'new_time' => 'nullable|date_format:H:i',
        ]);

        $oldDate = $session->scheduled_date->format('d.m.Y');

        // Переносим то же занятие на новую дату — количество не меняется
        $session->update([
            'scheduled_date' => $request->new_date,
            'scheduled_time' => $request->new_time ?: $session->scheduled_time,
            'status'         => 'scheduled',
            'notes'          => 'Перенесено с ' . $oldDate,
        ]);

        return back()->with('success', 'Занятие перенесено с ' . $oldDate . ' на ' . \Carbon\Carbon::parse($request->new_date)->format('d.m.Y') . '.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'session_ids'   => 'required|array',
            'session_ids.*' => 'integer|exists:training_sessions,id',
            'status'        => 'required|in:scheduled,completed,missed,cancelled',
        ]);

        $sessions = Session::whereIn('id', $request->session_ids)->get();

        foreach ($sessions as $session) {
            $this->authorize('update', $session->package);
            $session->update(['status' => $request->status]);
        }

        return back()->with('success', 'Статус обновлён для ' . count($request->session_ids) . ' занятий.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'session_ids'   => 'required|array',
            'session_ids.*' => 'integer|exists:training_sessions,id',
        ]);

        $sessions = Session::whereIn('id', $request->session_ids)->get();
        foreach ($sessions as $session) {
            $this->authorize('update', $session->package);
            $session->delete();
        }

        return redirect()->route('dashboard')->with('success', 'Удалено занятий: ' . count($request->session_ids));
    }

    public function updatePackageTime(Request $request, Session $session)
    {
        $this->authorize('update', $session->package);

        $request->validate(['scheduled_time' => 'required|date_format:H:i']);

        // Update all future scheduled sessions in the same package
        Session::where('package_id', $session->package_id)
            ->where('status', 'scheduled')
            ->whereDate('scheduled_date', '>=', today())
            ->update(['scheduled_time' => $request->scheduled_time]);

        // Also update the client's default training time
        $session->package->client->update(['training_time' => $request->scheduled_time]);

        return redirect()->route('dashboard')->with('success', 'Время обновлено для всех предстоящих тренировок.');
    }

    public function destroy(Session $session)
    {
        $this->authorize('update', $session->package);
        $session->delete();
        return back()->with('success', 'Занятие удалено.');
    }
}
