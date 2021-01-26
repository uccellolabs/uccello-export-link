<?php

namespace Uccello\UrlExport\Providers;

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
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'url-export');

        // Translations
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'url-export');

        // Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Routes
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        // Publish assets
        $this->publishes([
            __DIR__ . '/../../public' => public_path('vendor/uccello/url-export'),
        ], 'url-export-assets');

        // Config
        $this->publishes([
            __DIR__ . '/../../config/url-export.php' => config_path('url-export.php'),
        ], 'url-export-config');
    }

    public function register()
    {
        // Config
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/url-export.php',
            'url-export'
        );
    }
}
