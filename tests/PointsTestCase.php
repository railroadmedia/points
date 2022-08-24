<?php

namespace Railroad\Points\Tests;

use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\DatabaseManager;
use Illuminate\Routing\Router;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Railroad\Points\Providers\PointsServiceProvider;

class PointsTestCase extends BaseTestCase
{
    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * @var AuthManager
     */
    protected $authManager;

    /**
     * @var Router
     */
    protected $router;

    protected function setUp():void
    {
        parent::setUp();

        $this->artisan('migrate:fresh', []);
        $this->artisan('cache:clear', []);

        $this->faker = $this->app->make(Generator::class);

        $this->databaseManager = $this->app->make(DatabaseManager::class);
        $this->authManager = $this->app->make(AuthManager::class);
        $this->router = $this->app->make(Router::class);

        Carbon::setTestNow(Carbon::now());
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set(
            'database.connections.testbench',
            [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]
        );

        config(
            [
                'points' => [
                    'database_connection_name' => 'testbench',
                    'data_mode' => 'host', // 'host' or 'client'
                    'brand' => 'brand',
                    'table_prefix' => 'points_',
                    'tables' => [
                        'user_points' => 'user_points',
                    ],
                ],
            ]
        );

        // resora
        config(
            [
                'resora' => [
                    'database_connection_name' => 'testbench',
                ],
            ]
        );

        // register providers
        $app->register(PointsServiceProvider::class);
    }

}