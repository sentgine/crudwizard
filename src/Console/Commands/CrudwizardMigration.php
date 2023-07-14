<?php

namespace Sentgine\Crudwizard\Console\Commands;

use Illuminate\Support\Str;

class CrudwizardMigration extends CrudwizardGenerate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:generate-migration {--resource= : The name of the resource}
                                                          {--fields= : The fields of the resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the migration class based on the given resource.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("\n=== Generating the Migration File ===");

        $resourceName = $this->hasOption('resource') ? $this->option('resource') : '';
        $fields = $this->hasOption('fields') ? $this->option('fields') : [];
        $resourceName = $this->askTheResourceName($resourceName);
        $fields = $this->askTheFields($fields);
        $data = $this->setData($resourceName, $fields);
        $migrationFileName = 'create_' . $data['resource_name_plural'] . '_table';

        // Create a migration file.
        $this->callSilent("make:migration", [
            'name' => $migrationFileName
        ]);

        // Get the latest migration filename.
        $createdMigrationName = $this->getLatestMigrationFilename();
        $destination = database_path('migrations/' . $createdMigrationName);

        // Output the migration file name.
        $this->info('Created migration: ' . $createdMigrationName);

        // Specify the content to be inserted between the lines.
        $insertedContent = $this->buildMigrationFields($data['fields']);

        // Call the injectSpecificContentToAFile function.
        $this->appendContentToAFile($destination, $destination, $insertedContent, '$table->id();');
    }
}
