<?php

namespace L0n3ly\LaravelDynamicHelpers\Tests\Feature;

use Illuminate\Support\Facades\File;
use L0n3ly\LaravelDynamicHelpers\Tests\TestCase;

class MakeHelperCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        $helpersPath = app_path('Helpers');
        if (is_dir($helpersPath)) {
            File::deleteDirectory($helpersPath);
        }
        parent::tearDown();
    }

    /**
     * Test that the make:helper command creates a helper class successfully.
     */
    public function test_it_creates_a_helper_class()
    {
        $this->artisan('make:helper', ['name' => 'MoneyHelper'])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Helpers/MoneyHelper.php'));

        $content = file_get_contents(app_path('Helpers/MoneyHelper.php'));

        $this->assertStringContainsString('namespace App\\Helpers;', $content);
        $this->assertStringContainsString('class MoneyHelper', $content);
        $this->assertStringContainsString('extends Helper', $content);
        $this->assertStringContainsString('use L0n3ly\\LaravelDynamicHelpers\\Helper;', $content);
    }

    /**
     * Test that the make:helper command creates nested helper classes.
     */
    public function test_it_creates_nested_helper_classes()
    {
        $this->artisan('make:helper', ['name' => 'Store/CreateHelper'])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Helpers/Store/CreateHelper.php'));

        $content = file_get_contents(app_path('Helpers/Store/CreateHelper.php'));

        $this->assertStringContainsString('namespace App\\Helpers\\Store;', $content);
        $this->assertStringContainsString('class CreateHelper', $content);
    }

    /**
     * Test that the make:helper command creates deeply nested helper classes.
     */
    public function test_it_creates_deeply_nested_helper_classes()
    {
        $this->artisan('make:helper', ['name' => 'Store/Product/CreateHelper'])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Helpers/Store/Product/CreateHelper.php'));

        $content = file_get_contents(app_path('Helpers/Store/Product/CreateHelper.php'));

        $this->assertStringContainsString('namespace App\\Helpers\\Store\\Product;', $content);
        $this->assertStringContainsString('class CreateHelper', $content);
    }

    /**
     * Test that the make:helper command normalizes helper names to StudlyCase.
     */
    public function test_it_normalizes_helper_name_to_studly_case()
    {
        $this->artisan('make:helper', ['name' => 'money-helper'])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Helpers/MoneyHelper.php'));

        $content = file_get_contents(app_path('Helpers/MoneyHelper.php'));
        $this->assertStringContainsString('class MoneyHelper', $content);
    }

    /**
     * Test that the make:helper command prevents creating duplicate helpers.
     */
    public function test_it_prevents_duplicate_helper_creation()
    {
        $this->artisan('make:helper', ['name' => 'DuplicateHelper'])
            ->assertSuccessful();

        $this->artisan('make:helper', ['name' => 'DuplicateHelper'])
            ->assertFailed();
    }

    /**
     * Test that the make:helper command creates the Helpers directory if it doesn't exist.
     */
    public function test_it_creates_helpers_directory_if_not_exists()
    {
        $helpersPath = app_path('Helpers');

        if (is_dir($helpersPath)) {
            File::deleteDirectory($helpersPath);
        }

        $this->artisan('make:helper', ['name' => 'NewHelper'])
            ->assertSuccessful();

        $this->assertDirectoryExists($helpersPath);
        $this->assertFileExists($helpersPath.'/NewHelper.php');
    }

    /**
     * Test that the make:helper command outputs success message with access information.
     */
    public function test_it_outputs_success_message_with_access_info()
    {
        $this->artisan('make:helper', ['name' => 'TestHelper2'])
            ->expectsOutput('Helper created: app/Helpers/TestHelper2.php')
            ->expectsOutput('You can now access it using helpers()->testHelper2();')
            ->assertSuccessful();
    }

    /**
     * Test that the make:helper command outputs correct access name for nested helpers.
     */
    public function test_it_outputs_correct_access_name_for_nested_helpers()
    {
        $this->artisan('make:helper', ['name' => 'Store/CreateHelper2'])
            ->expectsOutput('Helper created: app/Helpers/Store/CreateHelper2.php')
            ->expectsOutput('You can now access it using helpers()->storeCreateHelper2();')
            ->assertSuccessful();
    }

    /**
     * Test that the make:helper command handles multiple word helper names correctly.
     */
    public function test_it_handles_multiple_word_helper_names()
    {
        $this->artisan('make:helper', ['name' => 'PermissionHelper'])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Helpers/PermissionHelper.php'));

        $content = file_get_contents(app_path('Helpers/PermissionHelper.php'));
        $this->assertStringContainsString('class PermissionHelper', $content);
    }
}
