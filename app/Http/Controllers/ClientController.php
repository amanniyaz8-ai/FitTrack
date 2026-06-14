<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Package;
use App\Models\Session;
use App\Http\Requests\StoreClientRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = $request->user()->clients()
            ->withCount('packages')
            ->withCount(['packages as active_packages_count' => function ($q) {
                $q->whereHas('sessions', fn($s) => $s->where('status', 'scheduled'));
            }]);

        if ($search) {
            $query->where('full_name', 'like', "%{$search}%");
        }

        $allClients = $query->with('packages')->latest()->get();

        // Не оплачено — нет пакетов вообще ИЛИ все пакеты неоплаченные
        $unpaidClients = $allClients->filter(function ($c) {
            if ($c->packages->isEmpty()) return true;
            return $c->packages->every(fn($p) => !$p->is_paid);
        });

        $unpaidIds = $unpaidClients->pluck('id');

        // Активные — есть оплаченный пакет + есть запланированные тренировки
        $activeClients = $allClients->reject(fn($c) => $unpaidIds->contains($c->id))
            ->filter(fn($c) => $c->active_packages_count > 0);

        // Неактивные — есть оплаченный пакет, но все тренировки отходил
        $inactiveClients = $allClients->reject(fn($c) => $unpaidIds->contains($c->id))
            ->filter(fn($c) => $c->active_packages_count == 0);

        return view('clients.index', compact('activeClients', 'inactiveClients', 'unpaidClients', 'search'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(StoreClientRequest $request)
    {
        $client = $request->user()->clients()->create($request->validated());

        if ($request->boolean('create_package')) {
            $package = $client->packages()->create([
                'total_sessions' => $request->input('pkg_total_sessions', 10),
                'price'          => $request->input('pkg_price', 0),
                'payment_date'   => $request->input('pkg_payment_date', today()->toDateString()),
                'is_paid'        => $request->boolean('pkg_is_paid'),
            ]);

            $completedSessions = (int) $request->input('pkg_completed_sessions', 0);
            $trainingTime = $client->training_time;
            app(\App\Http\Controllers\PackageController::class)->generateSessionsPublic($package, $client, $completedSessions, $trainingTime);

            // Count upcoming sessions for flash message
            $upcomingCount = $package->sessions()
                ->where(fn($q) => $q->whereDate('scheduled_date', today())->orWhereDate('scheduled_date', today()->addDay()))
                ->count();

            $msg = "Клиент «{$client->full_name}» добавлен, пакет создан, занятия сгенерированы.";
            if ($upcomingCount > 0) {
                $msg .= " На сегодня/завтра запланировано занятий: {$upcomingCount}.";
            }

            return redirect()->route('dashboard')->with('success', $msg);
        }

        return redirect()->route('dashboard')->with('success', "Клиент «{$client->full_name}» успешно добавлен!");
    }

    public function show(Request $request, Client $client)
    {
        $this->authorize('view', $client);

        $packages = $client->packages()
            ->withCount([
                'sessions as completed_count' => fn($q) => $q->where('status', 'completed'),
                'sessions as missed_count'    => fn($q) => $q->where('status', 'missed'),
            ])
            ->latest()
            ->get();

        return view('clients.show', compact('client', 'packages'));
    }

    public function edit(Client $client)
    {
        $this->authorize('update', $client);
        return view('clients.edit', compact('client'));
    }

    public function update(StoreClientRequest $request, Client $client)
    {
        $this->authorize('update', $client);

        $data = $request->validated();
        $client->update($data);

        // Sync training_time to all future scheduled sessions
        if (isset($data['training_time'])) {
            Session::whereHas('package', fn($q) => $q->where('client_id', $client->id))
                ->where('status', 'scheduled')
                ->whereDate('scheduled_date', '>=', today())
                ->update(['scheduled_time' => $data['training_time'] ?: null]);
        }

        return redirect()->route('clients.show', $client)->with('success', 'Данные клиента обновлены!');
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Клиент удалён.');
    }
}
