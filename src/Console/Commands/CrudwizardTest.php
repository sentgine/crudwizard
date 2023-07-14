<?php

namespace Sentgine\Crudwizard\Console\Commands;

use Illuminate\Support\Str;

class CrudwizardTest extends CrudwizardGenerate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:generate-test {--resource= : The name of the resource}
                                                     {--fields= : The fields of the resource}
                                                     {--prefix= : The route prefix for your resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the test class based on the given resource.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("\n=== Generating the Test Class ===");

        $resourceName = $this->hasOption('resource') ? $this->option('resource') : '';
        $fields = $this->hasOption('fields') ? $this->option('fields') : [];
        $prefix = $this->hasOption('prefix') ? $this->option('prefix') : '';
        $resourceName = $this->askTheResourceName($resourceName);
        $fields = $this->askTheFields($fields);
        $data = $this->setData($resourceName, $fields, $prefix);
        $routePrefix = !empty($data['route_prefix']) ? '/' . $data['route_prefix'] . '/' : '';

        $this->info("Generating " . $data['test_namespace'] . "\\" . $data['test_class'] . "...");

        // Generate the file by replacing placeholders in the stub with actual values.
        $this->generateMyFileFromTheStub(
            [
                'namespace' => $data['test_namespace'],
                'routePrefix' => $routePrefix,
                'viewPrefix' => $data['view_prefix'],
                'useModelClass' => $data['use_model_class'],
                'testClass' => $data['test_class'],
                'resourceNamePlural' => $data['resource_name_plural'],
                'resourceNameSingular' => $data['resource_name_singular'],
                'fakeFactoryFields' => $this->buildFactoryFields($data['fields']),
                'resourceNameSingularCapitalFirst' => $data['resource_name_ucfirst'],
            ],
            $data['test_class'],
            'tests'
        );
    }
}
