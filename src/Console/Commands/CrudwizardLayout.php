<?php

namespace Sentgine\Crudwizard\Console\Commands;

class CrudwizardLayout extends CrudwizardGenerate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:generate-layout {--resource= : The name of the resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the layout view.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("\n=== Generating the View Layout ===");

        $resourceName = $this->hasOption('resource') ? $this->option('resource') : '';
        $resourceName = $this->askTheResourceName($resourceName);

        // Create the layout path directory.
        $this->createTheDirectory(base_path(config('crudwizard.path.layout')));

        // Generate the layout file.
        $this->generateMyFileFromTheStub(
            [],
            config('crudwizard.file_name.layout'), // Filename
            'layout', // Key
            true, // View mode
            $resourceName
        );
    }
}
