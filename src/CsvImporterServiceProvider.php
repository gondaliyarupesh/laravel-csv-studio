<?php

namespace AestheticStudio\CsvImporter;

use Illuminate\Support\ServiceProvider;

class CsvImporterServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge package configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/csv-importer.php', 'csv-importer'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load package routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Load package views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'csv-importer');

        // Allow publishing config
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/csv-importer.php' => config_path('csv-importer.php'),
            ], 'csv-importer-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/csv-importer'),
            ], 'csv-importer-views');
        }
    }
}
