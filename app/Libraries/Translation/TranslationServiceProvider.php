<?php

namespace App\Libraries\Translation;

use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->registerMigrationGenerator();
    }

    private function registerMigrationGenerator()
    {
        $this->app->singleton('command.translation.generate', function ($app) {
            return $app['App\Libraries\Translation\Commands\GenerateFilesCommand'];
        });

        $this->commands('command.translation.generate');
    }
}
