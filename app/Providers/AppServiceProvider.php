<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\GlobalActivityLogObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot(): void
    {
        // Daftar model yang mau di-log otomatis
        $models = [
            \App\Models\Item::class, // contoh: model barang
            \App\Models\User::class, // contoh: model user
            // tambahkan model lain di sini
        ];

        foreach ($models as $model) {
            $model::observe(GlobalActivityLogObserver::class);
        }
    }


    
}
