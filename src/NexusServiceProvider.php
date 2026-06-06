<?php

namespace Nexus\Admin;

use Illuminate\Support\ServiceProvider;

class NexusServiceProvider extends ServiceProvider
{
    /**
     * Register bindings / merge config.
     * v0.1: tidak ada config (GUIDELINES #3 & #4) — tidak ada mergeConfigFrom.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap: loadViewsFrom + publishes (asset & view).
     */
    public function boot(): void
    {
        // View namespace 'nexus' → nexus::layouts.app, nexus::partials.*, nexus::icons.*,
        // dan anonymous component <x-nexus::...> otomatis (GUIDELINES #1, #4, #5).
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nexus');

        if ($this->app->runningInConsole()) {
            // Tag nexus-assets: CSS/JS pre-compiled → public/nexus/assets (GUIDELINES #2, #3).
            $this->publishes([
                __DIR__ . '/../resources/assets' => public_path('nexus/assets'),
            ], 'nexus-assets');

            // Tag nexus-views: views → resources/views/vendor/nexus untuk override (GUIDELINES #3, #8).
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/nexus'),
            ], 'nexus-views');
        }
    }
}
