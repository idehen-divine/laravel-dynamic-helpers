<?php

namespace L0n3ly\LaravelDynamicHelpers\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeHelperCommand extends Command
{
    protected $signature = 'make:helper {name}';

    protected $description = 'Create a new helper class in app/Helpers';

    public function handle()
    {
        $input = $this->argument('name');

        // Parse the input to handle nested paths like "Store/CreateHelper"
        $parts = explode('/', $input);
        $parts = array_map(fn ($part) => Str::studly($part), $parts);

        $className = array_pop($parts); // Last part is the class name
        $namespaceParts = $parts; // Remaining parts are namespace directories

        // Build the full namespace
        $namespace = 'App\\Helpers';
        if (! empty($namespaceParts)) {
            $namespace .= '\\'.implode('\\', $namespaceParts);
        }

        // Build the file path
        $directory = app_path('Helpers');
        if (! empty($namespaceParts)) {
            $directory .= '/'.implode('/', $namespaceParts);
        }
        $path = $directory.'/'.$className.'.php';

        if (file_exists($path)) {
            $this->error("Helper {$className} already exists!");

            return Command::FAILURE;
        }

        // Ensure directory exists
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Load stub
        $stubPath = __DIR__.'/../../stubs/helper.stub';

        if (! file_exists($stubPath)) {
            $this->error("Stub file not found at: {$stubPath}");

            return Command::FAILURE;
        }

        $stub = file_get_contents($stubPath);

        // Replace placeholders
        $content = str_replace('{{ namespace }}', $namespace, $stub);
        $content = str_replace('{{ class }}', $className, $content);

        file_put_contents($path, $content);

        $relativePath = str_replace(base_path().'/', '', $path);
        $this->info("Helper created: {$relativePath}");

        // Generate access method name (flatten path: Store/CreateHelper -> storeCreateHelper)
        $accessName = Str::camel(implode('', $namespaceParts).$className);
        $this->info("You can now access it using helpers()->{$accessName}();");

        return Command::SUCCESS;
    }
}
