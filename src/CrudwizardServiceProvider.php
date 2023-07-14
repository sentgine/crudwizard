<?php

namespace Sentgine\Crudwizard;

use Illuminate\Support\ServiceProvider;
use Sentgine\Crudwizard\Console\Commands;

class CrudwizardServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->publishThings();
    }

    /**
     * Register the commands.
     * 
     * @return void
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\CrudwizardGenerate::class,
                Commands\CrudwizardDelete::class,
                Commands\CrudwizardRoute::class,
                Commands\CrudwizardController::class,
                Commands\CrudwizardModel::class,
                Commands\CrudwizardRequest::class,
                Commands\CrudwizardMigration::class,
                Commands\CrudwizardFactory::class,
                Commands\CrudwizardTest::class,
                Commands\CrudwizardBaseSearch::class,
                Commands\CrudwizardSearch::class,
                Commands\CrudwizardLayout::class,
                Commands\CrudwizardView::class,
            ]);
        }
    }

    /**
     * Register the publishables.
     * 
     * @return void
     */
    private function publishThings(): void
    {
        $files = [
            __DIR__ . '/../config/crudwizard.php' => config_path('crudwizard.php'),
            __DIR__ . '/../stubs' => base_path('stubs/'),
        ];

        $this->publishes($files, 'crudwizard');
    }
}
