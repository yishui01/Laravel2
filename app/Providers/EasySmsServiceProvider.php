<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Overtrue\EasySms\EasySms;
class EasySmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EasySms::class, function ($app) {
            return new EasySms(config('easysms'));
        }); //绑定一个单例到app中

        $this->app->alias(EasySms::class, 'easysms'); //取个别名
    }
}
