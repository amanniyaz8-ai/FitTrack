<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Package;
use App\Policies\ClientPolicy;
use App\Policies\PackagePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Package::class, PackagePolicy::class);
    }
}
