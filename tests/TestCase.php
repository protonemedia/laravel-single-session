<?php

namespace Pbmedia\SingleSession\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp()
    {
        parent::setUp();

        // $this->app['config']->set('session.driver', 'array');
    }
    protected function setUpDatabase()
    {
        // $this->app['config']->set('database.default', 'sqlite');
        // $this->app['config']->set('database.connections.sqlite', [
        //     'driver'   => 'sqlite',
        //     'database' => ':memory:',
        //     'prefix'   => '',
        // ]);

        // include_once __DIR__ . '/../database/migrations/add_session_id_to_users_table.php.stub';
        // include_once __DIR__ . '/../database/migrations/create_specifications_scores_table.php.stub';
        // include_once __DIR__ . '/create_products_table.php';

        // (new \CreateSpecificationsAttributesTable)->up();
        // (new \CreateSpecificationsScoresTable)->up();
        // (new \CreateProductsTable)->up();
    }
}
