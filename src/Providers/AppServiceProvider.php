<?php

namespace Uccello\ExportLink\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * App Service Provider
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        // Views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'export-link');

        // Translations
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'export-link');

        // Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Routes
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        // Publish assets
        $this->publishes([
            __DIR__ . '/../../public' => public_path('vendor/uccello/export-link'),
        ], 'export-link-assets');

        // Config
        $this->publishes([
            __DIR__ . '/../../config/export-link.php' => config_path('export-link.php'),
        ], 'export-link-config');
    }

    public function register()
    {
        // Config
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/export-link.php',
            'export-link'
        );
    }
}
