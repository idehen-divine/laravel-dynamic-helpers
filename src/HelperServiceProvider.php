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

        // Auto-create global functions for all existing helpers
        $this->createHelperFunctions();
    }

    protected function createHelperFunctions(): void
    {
        $helpersPath = app_path('Helpers');

        if (! is_dir($helpersPath)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($helpersPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $relativePath = str_replace($helpersPath.'/', '', $file->getPathname());
                $relativePath = str_replace('.php', '', $relativePath);

                $parts = explode('/', $relativePath);
                $className = array_pop($parts);
                $namespaceParts = $parts;

                $functionName = \Illuminate\Support\Str::camel(implode('', $namespaceParts).$className);

                if (! function_exists($functionName)) {
                    $code = "if (!function_exists('{$functionName}')) { function {$functionName}(...\$args) { return \\L0n3ly\\LaravelDynamicHelpers\\HelperProxy::{$functionName}(...\$args); } }";
                    eval($code);
                }
            }
        }
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
