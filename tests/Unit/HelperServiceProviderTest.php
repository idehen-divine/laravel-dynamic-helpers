<?php

namespace L0n3ly\LaravelDynamicHelpers\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use L0n3ly\LaravelDynamicHelpers\HelperServiceProvider;
use L0n3ly\LaravelDynamicHelpers\Tests\TestCase;

class HelperServiceProviderTest extends TestCase
{
    /**
     * Test that the HelperServiceProvider is registered.
     */
    public function test_it_registers_the_service_provider()
    {
        $providers = $this->app->getLoadedProviders();

        $this->assertArrayHasKey(HelperServiceProvider::class, $providers);
    }

    /**
     * Test that the make:helper command is registered.
     */
    public function test_it_registers_the_make_helper_command()
    {
        $exitCode = Artisan::call('list', ['--format' => 'json']);
        $output = Artisan::output();
        $this->assertStringContainsString('make:helper', $output);
    }

    /**
     * Test that helper functions are loaded.
     */
    public function test_it_loads_helper_functions()
    {
        $this->assertTrue(function_exists('helpers'));
    }

    /**
     * Test that the helpers() function returns a Helper instance.
     */
    public function test_helpers_function_returns_helper_instance()
    {
        $instance = helpers();

        $this->assertInstanceOf(\L0n3ly\LaravelDynamicHelpers\Helper::class, $instance);
    }

    /**
     * Test that the helpers() function returns a singleton instance.
     */
    public function test_helpers_function_returns_singleton()
    {
        $instance1 = helpers();
        $instance2 = helpers();

        $this->assertSame($instance1, $instance2);
    }
}
