<?php

namespace AestheticStudio\CsvImporter\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use AestheticStudio\CsvImporter\CsvImporterServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Set up the environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run database migrations for testing
        $this->setUpDatabase($this->app);
    }

    /**
     * Load the service providers.
     */
    protected function getPackageProviders($app)
    {
        return [
            CsvImporterServiceProvider::class,
        ];
    }

    /**
     * Define the environment config.
     */
    protected function defineEnvironment($app)
    {
        // Set up clean database connections for SQLite memory testing
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Mock routes / session / app settings if needed
        $app['config']->set('app.key', 'base64:yW962bH37Pq6d2hF1W6r6J/yvU634z/t8K7e/8pX6+Q=');
    }

    /**
     * Helper to dynamically set up testing schema.
     */
    protected function setUpDatabase($app)
    {
        $schema = $app['db']->connection()->getSchemaBuilder();

        // Create a mock contacts table for our import tests
        $schema->create('contacts', function ($table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('age')->nullable();
            $table->timestamps();
        });
    }
}
