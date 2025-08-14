<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\GlobalActivityLogObserver;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\View;


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

        View::composer('*', function ($view) {
        $barangBaru = Activity::where('subject_type', \App\Models\Item::class)
            ->where('description', 'created') // atau sesuai log description kamu
            ->latest()
            ->take(10)
            ->get();

        $view->with('barangBaru', $barangBaru);
    });
    }


    
}
