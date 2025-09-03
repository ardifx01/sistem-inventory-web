<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\GlobalActivityLogObserver;
use App\Observers\ItemObserver;
use App\Models\Item;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;


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
    
        // Share notifCount dan notifList ke semua view
        View::composer('*', function ($view) {
            if (Auth::check() && Auth::user()->role === 'superadmin') {
                $user = Auth::user();
                $notifCount = $user->unreadNotifications()->count();
                $notifList = $user->unreadNotifications()->take(5)->get(); // tampilkan 5 terbaru
                $view->with(compact('notifCount', 'notifList'));
            } else {
                $view->with([
                    'notifCount' => 0,
                    'notifList' => collect(),
                ]);
            }
        });

        
                // Global activity log observer  
        $models = [
            \App\Models\User::class,
            \App\Models\Item::class,
            \App\Models\Category::class,
        ];

        foreach ($models as $model) {
            $model::observe(GlobalActivityLogObserver::class);
        }

        // Register ItemObserver untuk rack location logic
        Item::observe(ItemObserver::class);

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
