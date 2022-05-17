<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    // public function boot(UrlGenerator $url)
    // {
    //     if (env('APP_ENV') === 'ngrok') {
    //         $url->forceScheme('https');
    //     }
    // }
    public function boot()
    {
        if ($this->app->environment() === 'production') {
            URL::forceScheme('https');
        }
    }
}
