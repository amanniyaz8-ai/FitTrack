<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Package;
use App\Models\Session;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $trainer = User::create([
            'name' => 'Алексей Тренер',
            'email' => 'trainer@fittrack.kz',
            'password' => Hash::make('password'),
            'specialization' => 'fitness',
        ]);

        $clientsData = [
            ['full_name' => 'Анна Иванова', 'phone' => '+7 777 123 4567', 'goal' => 'Похудеть на 10 кг', 'training_days' => ['Mon', 'Wed', 'Fri']],
            ['full_name' => 'Дмитрий Петров', 'phone' => '+7 777 234 5678', 'goal' => 'Набор мышечной массы', 'training_days' => ['Tue', 'Thu', 'Sat']],
            ['full_name' => 'Мария Сидорова', 'phone' => '+7 777 345 6789', 'goal' => 'Улучшить выносливость', 'training_days' => ['Mon', 'Wed']],
            ['full_name' => 'Игорь Козлов', 'phone' => '+7 777 456 7890', 'goal' => 'Подготовка к марафону', 'training_days' => ['Tue', 'Thu', 'Sat', 'Sun']],
            ['full_name' => 'Светлана Новикова', 'phone' => '+7 777 567 8901', 'goal' => 'Общая физическая подготовка', 'training_days' => ['Mon', 'Fri']],
        ];

        $dayMap = ['Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6, 'Sun' => 0];

        foreach ($clientsData as $idx => $data) {
            $client = Client::create([
                'trainer_id' => $trainer->id,
                'full_name' => $data['full_name'],
                'phone' => $data['phone'],
                'goal' => $data['goal'],
                'contraindications' => $idx === 0 ? 'Боли в спине' : null,
                'training_days' => $data['training_days'],
            ]);

            $totalSessions = 10;
            $price = [60000, 55000, 50000, 65000, 45000][$idx];

            $package = Package::create([
                'client_id' => $client->id,
                'total_sessions' => $totalSessions,
                'price' => $price,
                'payment_date' => Carbon::now()->subDays(rand(5, 20)),
                'is_paid' => true,
            ]);

            $sessionsGenerated = 0;
            $startDate = Carbon::now()->subWeeks(3);
            $current = $startDate->copy();

            while ($sessionsGenerated < $totalSessions) {
                $dayOfWeek = $current->dayOfWeek; // 0=Sun,1=Mon,...
                $matchingDay = collect($data['training_days'])->first(fn($d) => $dayMap[$d] === $dayOfWeek);

                if ($matchingDay !== null) {
                    $status = 'scheduled';
                    if ($current->isPast() && !$current->isToday()) {
                        $status = rand(0, 4) > 0 ? 'completed' : 'missed';
                    }

                    Session::create([
                        'package_id' => $package->id,
                        'client_id' => $client->id,
                        'scheduled_date' => $current->toDateString(),
                        'status' => $status,
                        'notes' => $status === 'completed' ? 'Хорошая тренировка, клиент доволен.' : null,
                    ]);

                    $sessionsGenerated++;
                }

                $current->addDay();
                if ($sessionsGenerated >= $totalSessions) break;
            }
        }
    }
}
