<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    // protected $policies = [
    //     // App\Models\Model::class => App\Policies\ModelPolicy::class,
    // ];

    // public function boot(): void
    // {
    //     $this->registerPolicies();

    //     // Hindari error di artisan command
    //     if (!app()->runningInConsole()) {
    //         Gate::define('isSuperadmin', fn($user) => $user->role === 'superadmin');
    //         Gate::define('isAdmin', fn($user) => $user->role === 'admin');
    //         Gate::define('isUser', fn($user) => $user->role === 'user');
    //     }
    // }
}
