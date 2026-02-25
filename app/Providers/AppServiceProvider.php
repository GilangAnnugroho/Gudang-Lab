<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrap();
        Paginator::defaultView('pagination.labkesda');
        Paginator::defaultSimpleView('pagination.labkesda-simple');
    }
}
