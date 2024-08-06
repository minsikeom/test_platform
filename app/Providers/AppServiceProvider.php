<?php

namespace App\Providers;

use App\Services\FileManageService;
use App\Services\MakeRandCode;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FileManageService::class, function ($app) {
            return new FileManageService();
        });

        $this->app->singleton(MakeRandCode::class, function ($app) {
            return new MakeRandCode();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
    }
}
