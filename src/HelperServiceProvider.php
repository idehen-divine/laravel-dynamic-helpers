<?php

namespace L0n3ly\LaravelDynamicHelpers;

use Illuminate\Support\ServiceProvider;
use L0n3ly\LaravelDynamicHelpers\Console\MakeHelperCommand;

class HelperServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Load helper functions
        require_once __DIR__.'/../helpers.php';
    }

    public function boot()
    {
        // Register artisan command
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeHelperCommand::class,
            ]);
        }
    }
}
