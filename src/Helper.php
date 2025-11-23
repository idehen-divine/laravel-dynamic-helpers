<?php

namespace L0n3ly\LaravelDynamicHelpers;

use Illuminate\Support\Str;

class Helper
{
    protected static $instance;

    protected $helpers = [];

    public static function make()
    {
        return new static;
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new static;
    }

    public function __call($name, $arguments)
    {
        // Try direct path first: App\Helpers\{Name}
        $class = 'App\\Helpers\\'.Str::studly($name);

        // If not found, try searching in subdirectories
        // Convert camelCase to possible nested paths
        // e.g., "storeCreateHelper" -> try "App\Helpers\Store\CreateHelper"
        if (! class_exists($class)) {
            $foundClass = $this->findHelperClass($name);
            if ($foundClass) {
                $class = $foundClass;
            }
        }

        if (! class_exists($class)) {
            throw new \Exception("Helper class not found for [{$name}]. Tried: App\\Helpers\\".Str::studly($name));
        }

        if (! isset($this->helpers[$name])) {
            $this->helpers[$name] = $class::make();
        }

        $instance = $this->helpers[$name];

        if (! empty($arguments) && is_callable($instance)) {
            return $instance(...$arguments);
        }

        return $instance;
    }

    /**
     * Find helper class by searching in subdirectories
     * Converts camelCase method names to possible class paths
     */
    protected function findHelperClass(string $name): ?string
    {
        $basePath = app_path('Helpers');

        if (! is_dir($basePath)) {
            return null;
        }

        // Convert camelCase to StudlyCase parts
        // "storeCreateHelper" -> ["Store", "Create", "Helper"]
        $parts = preg_split('/(?=[A-Z])/', Str::studly($name), -1, PREG_SPLIT_NO_EMPTY);

        if (empty($parts)) {
            return null;
        }

        // Try different combinations of nested paths
        // For "storeCreateHelper", try:
        // - App\Helpers\Store\CreateHelper
        // - App\Helpers\StoreCreateHelper (fallback)

        // If we have multiple parts, try grouping them
        for ($i = 1; $i < count($parts); $i++) {
            $namespaceParts = array_slice($parts, 0, $i);
            $className = implode('', array_slice($parts, $i));

            $namespace = 'App\\Helpers\\'.implode('\\', $namespaceParts);
            $class = $namespace.'\\'.$className;

            if (class_exists($class)) {
                return $class;
            }
        }

        return null;
    }
}
