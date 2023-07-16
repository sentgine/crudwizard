<?php

namespace Sentgine\Crudwizard\Console\Commands;

use Illuminate\Support\Str;

class CrudwizardView extends CrudwizardGenerate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:generate-views {--resource= : The name of the resource}
                                                      {--fields= : The fields of the resource}
                                                      {--prefix= : The route prefix for your resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the view based on the given resource.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $resourceName = $this->hasOption('resource') ? $this->option('resource') : '';
        $fields = $this->hasOption('fields') ? $this->option('fields') : [];
        $prefix = $this->hasOption('prefix') ? $this->option('prefix') : '';
        $resourceName = $this->askTheResourceName($resourceName);
        $fields = $this->askTheFields($fields);
        $data = $this->setData($resourceName, $fields, $prefix);
        $resourceName = $data['resource_name'];
        $fields = $data['fields'];
        $resourceDirectory = $data['resource_directory'];

        $this->createDirectoryRecursively($resourceDirectory);

        // Call the generate layout command
        $this->call('crudwizard:generate-layout', [
            '--resource' => $resourceName
        ]);

        $this->info("\n=== Generating the View ===");

        // Prepare the variables
        $layoutName = $data['layout_name'];
        $viewPrefix = $data['view_prefix'];
        $resourceNameSingularCapitalFirst = Str::ucfirst($data['resource_name_singular']);
        $resourceNamePluralCapitalFirst = Str::ucfirst($data['resource_name_plural']);
        $resourceNameSingular = $data['resource_name_singular'];
        $resourceNamePlural = $data['resource_name_plural'];
        $resourceDirectory = $data['resource_directory'];

        // Create the main directory for the resource.
        $this->createTheDirectory($resourceDirectory);

        // Generate the index file.
        $this->info("Creating the index file.");
        $this->generateMyFileFromTheStub(
            [
                'layoutName' => $layoutName,
                'viewPrefix' => $viewPrefix,
                'resourceNamePluralCapitalFirst' => $resourceNamePluralCapitalFirst,
                'resourceNameSingular' => $resourceNameSingular,
                'resourceNamePlural' => $resourceNamePlural,
                'fieldHeaders' => $this->buildHeaderFields($fields),
                'fieldData' => $this->buildTableDataFields($fields, $resourceNameSingular),
            ],
            'index', // Filename
            'index', // Key
            true,     // View mode,
            $resourceName,
            $resourceDirectory
        );

        // Generate the show file.
        $this->info("Creating the show file.");
        $this->generateMyFileFromTheStub(
            [
                'layoutName' => $layoutName,
                'resourceNameSingularCapitalFirst' => $resourceNameSingularCapitalFirst,
                'resourceNameSingular' => $resourceNameSingular,
                'resourceNamePlural' => $resourceNamePlural,
            ],
            'show', // Filename
            'show', // Key
            true,     // View mode,
            $resourceName,
            $resourceDirectory
        );

        // Generate the edit file.
        $this->info("Creating the edit file.");
        $this->generateMyFileFromTheStub(
            [
                'layoutName' => $layoutName,
                'viewPrefix' => $viewPrefix,
                'resourceNameSingularCapitalFirst' => $resourceNameSingularCapitalFirst,
                'resourceNameSingular' => $resourceNameSingular,
                'resourceNamePlural' => $resourceNamePlural,
            ],
            'edit', // Filename
            'edit', // Key
            true,     // View mode,
            $resourceName,
            $resourceDirectory
        );

        // Generate the create file.
        $this->info("Creating the create file.");
        $this->generateMyFileFromTheStub(
            [
                'layoutName' => $layoutName,
                'viewPrefix' => $viewPrefix,
                'resourceNameSingularCapitalFirst' => $resourceNameSingularCapitalFirst,
                'resourceNameSingular' => $resourceNameSingular,
                'resourceNamePlural' => $resourceNamePlural,
            ],
            'create', // Filename
            'create', // Key
            true,     // View mode,
            $resourceName,
            $resourceDirectory
        );

        // Generate the search form file.
        $this->info("Creating the search form file.");
        $this->generateMyFileFromTheStub(
            [],
            '_search-form', // Filename
            '_search-form', // Key
            true,     // View mode,
            $resourceName,
            $resourceDirectory
        );

        // Generate the edit form file.
        $this->buildTheFormFields(
            '_create-form',
            $fields,
            $resourceName,
            $resourceDirectory
        );

        // Generate the edit form file.
        $this->buildTheFormFields(
            '_edit-form',
            $fields,
            $resourceName,
            $resourceDirectory
        );

        $this->info("Done creating the views!");
    }
}
