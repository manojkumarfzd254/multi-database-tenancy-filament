<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Illuminate\Support\ServiceProvider;

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
        Filament::serving(function () {
            Filament::registerUserMenuItems([
                'Profile' => UserMenuItem::make()
                    ->label('My Profile')
                    ->url(route('filament.admin.pages.edit-profile')) // Adjust this URL as per your route setup
                    ->icon('heroicon-o-user'),
            ]);
        });
    }
}
