<?php

namespace App\Policies;

use App\Models\Package;
use App\Models\User;

class PackagePolicy
{
    public function view(User $user, Package $package): bool
    {
        $package->loadMissing('client');
        return $user->id === $package->client->trainer_id;
    }

    public function update(User $user, Package $package): bool
    {
        $package->loadMissing('client');
        return $user->id === $package->client->trainer_id;
    }
}
