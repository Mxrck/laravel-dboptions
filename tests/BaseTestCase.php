<?php
namespace Nitro\Options\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Nitro\Options\Facades\OptionsFacade;
use Nitro\Options\OptionsServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class BaseTestCase extends TestCase
{

    use DatabaseMigrations;

    /**
     * Define environment setup.
     *
     * @param  Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set(
            'database.connections.testbench', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]
        );
    }
    /**
     * Setup the test environment.
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__.'/../Migrations');
    }
    /**
     * Get package providers.
     *
     * @param  Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            OptionsServiceProvider::class,
        ];
    }
    /**
     * Get package aliases.
     *
     * @param  Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Options'   => OptionsFacade::class,
        ];
    }

}
