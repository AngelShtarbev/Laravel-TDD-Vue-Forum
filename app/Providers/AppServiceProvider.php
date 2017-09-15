<?php

namespace App\Providers;

use App\Channel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        View::composer('*', function ($view) {
            $channels = Cache::rememberForever('channels', function () {
                return Channel::all();
            });
            $view->with('channels', $channels);
        });

        Validator::extend('blockspam','App\Rules\BlockSpam@passes');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        if($this->app->isLocal()) {
//            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
//        }
    }
}
