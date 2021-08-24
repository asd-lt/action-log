<?php

namespace Asd\ActionLog\Providers;

use Illuminate\Cache\Console\CacheTableCommand;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Asd\ActionLog\Console\TableCommand;

class ArtisanServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.action-log', function ($app) {
            return new TableCommand($app['files'], $app['composer']);
        });

        $this->commands('command.action-log');
    }
}