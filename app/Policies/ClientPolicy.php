<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function view(User $user, Client $client): bool
    {
        return $user->id === $client->trainer_id;
    }

    public function update(User $user, Client $client): bool
    {
        return $user->id === $client->trainer_id;
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->id === $client->trainer_id;
    }
}
