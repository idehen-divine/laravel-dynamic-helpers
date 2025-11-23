<?php

namespace L0n3ly\LaravelDynamicHelpers\Tests\Unit;

use Illuminate\Support\Facades\File;
use L0n3ly\LaravelDynamicHelpers\Helper;
use L0n3ly\LaravelDynamicHelpers\Tests\TestCase;

class HelperTest extends TestCase
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
     * Test that Helper::getInstance() returns a singleton instance.
     */
    public function test_it_returns_singleton_instance()
    {
        $instance1 = Helper::getInstance();
        $instance2 = Helper::getInstance();

        $this->assertSame($instance1, $instance2);
    }

    /**
     * Test that Helper::make() creates a new instance each time.
     */
    public function test_it_creates_new_instance_with_make()
    {
        $instance1 = Helper::make();
        $instance2 = Helper::make();

        $this->assertNotSame($instance1, $instance2);
        $this->assertInstanceOf(Helper::class, $instance1);
        $this->assertInstanceOf(Helper::class, $instance2);
    }

    /**
     * Test that helpers() dynamically resolves helper classes.
     */
    public function test_it_resolves_helper_class_dynamically()
    {
        $helpersPath = app_path('Helpers');
        if (! is_dir($helpersPath)) {
            mkdir($helpersPath, 0755, true);
        }

        $helperContent = <<<'PHP'
<?php

namespace App\Helpers;

use L0n3ly\LaravelDynamicHelpers\Helper;

class TestHelper extends Helper
{
    public function getValue()
    {
        return 'test-value';
    }
}
PHP;

        file_put_contents($helpersPath.'/TestHelper.php', $helperContent);

        require_once $helpersPath.'/TestHelper.php';

        $helper = helpers()->testHelper();

        $this->assertInstanceOf(\App\Helpers\TestHelper::class, $helper);
        $this->assertEquals('test-value', $helper->getValue());
    }

    /**
     * Test that helper instances are cached after first resolution.
     */
    public function test_it_caches_helper_instances()
    {
        $helpersPath = app_path('Helpers');
        if (! is_dir($helpersPath)) {
            mkdir($helpersPath, 0755, true);
        }

        $helperContent = <<<'PHP'
<?php

namespace App\Helpers;

use L0n3ly\LaravelDynamicHelpers\Helper;

class CacheTestHelper extends Helper
{
    public static $instanceCount = 0;

    public static function make()
    {
        self::$instanceCount++;
        return parent::make();
    }
}
PHP;

        file_put_contents($helpersPath.'/CacheTestHelper.php', $helperContent);
        require_once $helpersPath.'/CacheTestHelper.php';

        \App\Helpers\CacheTestHelper::$instanceCount = 0;

        $helper1 = helpers()->cacheTestHelper();
        $helper2 = helpers()->cacheTestHelper();
        $helper3 = helpers()->cacheTestHelper();

        $this->assertEquals(1, \App\Helpers\CacheTestHelper::$instanceCount);
        $this->assertSame($helper1, $helper2);
        $this->assertSame($helper2, $helper3);
    }

    /**
     * Test that an exception is thrown when a helper class is not found.
     */
    public function test_it_throws_exception_when_helper_class_not_found()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Helper class not found');

        helpers()->nonExistentHelper();
    }

    /**
     * Test that nested helper classes can be resolved using flattened camelCase names.
     */
    public function test_it_resolves_nested_helper_classes()
    {
        $storePath = app_path('Helpers/Store');
        if (! is_dir($storePath)) {
            mkdir($storePath, 0755, true);
        }

        $helperContent = <<<'PHP'
<?php

namespace App\Helpers\Store;

use L0n3ly\LaravelDynamicHelpers\Helper;

class CreateHelper extends Helper
{
    public function create()
    {
        return 'created';
    }
}
PHP;

        file_put_contents($storePath.'/CreateHelper.php', $helperContent);
        require_once $storePath.'/CreateHelper.php';

        $helper = helpers()->storeCreateHelper();

        $this->assertInstanceOf(\App\Helpers\Store\CreateHelper::class, $helper);
        $this->assertEquals('created', $helper->create());
    }

    /**
     * Test that callable helpers can be invoked with arguments.
     */
    public function test_it_handles_callable_helpers_with_arguments()
    {
        $helpersPath = app_path('Helpers');
        if (! is_dir($helpersPath)) {
            mkdir($helpersPath, 0755, true);
        }

        $helperContent = <<<'PHP'
<?php

namespace App\Helpers;

use L0n3ly\LaravelDynamicHelpers\Helper;

class CallableHelper extends Helper
{
    public function __invoke($arg1, $arg2)
    {
        return $arg1 + $arg2;
    }
}
PHP;

        file_put_contents($helpersPath.'/CallableHelper.php', $helperContent);
        require_once $helpersPath.'/CallableHelper.php';

        $result = helpers()->callableHelper(5, 10);

        $this->assertEquals(15, $result);
    }
}
