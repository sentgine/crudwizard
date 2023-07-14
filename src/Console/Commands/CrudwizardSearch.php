<?php

namespace Sentgine\Crudwizard\Console\Commands;

use Illuminate\Support\Str;

class CrudwizardSearch extends CrudwizardGenerate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:generate-search {--resource= : The name of the resource}
                                                       {--fields= : The fields of the resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the search class based on the given resource.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Generate the base search class
        $this->call('crudwizard:generate-base-search');
        $resourceName = $this->hasOption('resource') ? $this->option('resource') : '';
        $fields = $this->hasOption('fields') ? $this->option('fields') : [];
        $resourceName = $this->askTheResourceName($resourceName);
        $fields = $this->askTheFields($fields);
        $data = $this->setData($resourceName, $fields);

        $this->info("Generating " . $data['search_service_class'] . " search class...");

        // Generate the child search class file from the stub.
        $this->generateMyFileFromTheStub(
            [
                'namespace' => $data['service_namespace'],
                'className' => $data['search_service_class'],
                'modelClassName' => $data['model_namespace'] . '\\' . $data['model_class'],
                'parentClassName' => $data['search_service_base_class'],
                'fields' => $this->buildFillableFields($data['fields'], false)
            ],
            $data['search_service_class'],
            'child_search_class'
        );
    }
}
