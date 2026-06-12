<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Package;
use App\Models\Session;
use App\Http\Requests\StorePackageRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PackageController extends Controller
{
    public function create(Request $request, Client $client)
    {
        $this->authorize('view', $client);
        return view('packages.create', compact('client'));
    }

    public function store(StorePackageRequest $request, Client $client)
    {
        $this->authorize('view', $client);

        $data = $request->validated();
        $data['is_paid'] = $request->boolean('is_paid');

        $package = $client->packages()->create($data);

        // Auto-generate sessions based on training_days
        $this->generateSessionsPublic($package, $client);

        $upcomingCount = $package->sessions()
            ->where(fn($q) => $q->whereDate('scheduled_date', today())->orWhereDate('scheduled_date', today()->addDay()))
            ->count();

        $msg = "Пакет для «{$client->full_name}» создан, занятия сгенерированы.";
        if ($upcomingCount > 0) {
            $msg .= " На сегодня/завтра: {$upcomingCount} занятий.";
        }

        return redirect()->route('dashboard')->with('success', $msg);
    }

    public function generateSessionsPublic(Package $package, Client $client, int $completedCount = 0, ?string $trainingTime = null): void
    {
        $days = $client->training_days ?? [];
        if (empty($days)) return;

        $dayMap = ['Sun' => 0, 'Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6];
        $dayNumbers = array_map(fn($d) => $dayMap[$d] ?? -1, $days);

        $total = $package->total_sessions;
        $completedCount = min($completedCount, $total);
        $scheduledCount = $total - $completedCount;

        // Create completed sessions in the past
        if ($completedCount > 0) {
            $past = Carbon::today()->subDays(1);
            $pastGenerated = 0;
            $pastDates = [];

            // Go back to find enough past training days
            $lookback = Carbon::today()->subDays($completedCount * 7 + 30);
            $current = clone $lookback;
            while ($current->lessThan(Carbon::today())) {
                if (in_array($current->dayOfWeek, $dayNumbers)) {
                    $pastDates[] = $current->toDateString();
                }
                $current->addDay();
            }

            // Take the last N dates
            $pastDates = array_slice($pastDates, -$completedCount);
            foreach ($pastDates as $date) {
                Session::create([
                    'package_id'     => $package->id,
                    'client_id'      => $client->id,
                    'scheduled_date' => $date,
                    'scheduled_time' => $trainingTime,
                    'status'         => 'completed',
                ]);
            }

            // If not enough past training days found, create with today's date minus days
            $missing = $completedCount - count($pastDates);
            for ($i = 0; $i < $missing; $i++) {
                Session::create([
                    'package_id'     => $package->id,
                    'client_id'      => $client->id,
                    'scheduled_date' => Carbon::today()->subDays($missing - $i)->toDateString(),
                    'scheduled_time' => $trainingTime,
                    'status'         => 'completed',
                ]);
            }
        }

        // Create scheduled sessions in the future
        $current = Carbon::today();
        $generated = 0;

        while ($generated < $scheduledCount) {
            if (in_array($current->dayOfWeek, $dayNumbers)) {
                Session::create([
                    'package_id'     => $package->id,
                    'client_id'      => $client->id,
                    'scheduled_date' => $current->toDateString(),
                    'scheduled_time' => $trainingTime,
                    'status'         => 'scheduled',
                ]);
                $generated++;
            }
            $current->addDay();
        }
    }

    public function edit(Package $package)
    {
        $this->authorize('update', $package);
        $package->load('client');
        return view('packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $this->authorize('update', $package);

        $data = $request->validate([
            'total_sessions' => 'required|integer|min:1|max:100',
            'price'          => 'required|numeric|min:0',
            'payment_date'   => 'required|date',
            'is_paid'        => 'boolean',
            'training_days'  => 'nullable|array',
            'training_days.*'=> 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
        ]);

        $package->update([
            'total_sessions' => $data['total_sessions'],
            'price'          => $data['price'],
            'payment_date'   => $data['payment_date'],
            'is_paid'        => $request->boolean('is_paid'),
        ]);

        // Update client training days if changed
        if (!empty($data['training_days'])) {
            $package->client->update(['training_days' => $data['training_days']]);
        }

        return redirect()->route('clients.show', $package->client)
            ->with('success', 'Пакет обновлён.');
    }

    public function sessions(Request $request, Package $package)
    {
        $this->authorize('view', $package);

        $package->load('client');
        $sessions = $package->sessions()->orderBy('scheduled_date')->get();

        $completedCount = $sessions->where('status', 'completed')->count();
        $missedCount    = $sessions->where('status', 'missed')->count();
        $remaining      = $package->total_sessions - $completedCount - $missedCount;

        return view('packages.sessions', compact('package', 'sessions', 'completedCount', 'missedCount', 'remaining'));
    }

    public function addSession(Request $request, Package $package)
    {
        $this->authorize('update', $package);

        $request->validate([
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
        ]);

        Session::create([
            'package_id'     => $package->id,
            'client_id'      => $package->client_id,
            'scheduled_date' => $request->scheduled_date,
            'scheduled_time' => $request->scheduled_time ?: null,
            'status'         => 'scheduled',
        ]);

        return back()->with('success', 'Занятие добавлено.');
    }
}
