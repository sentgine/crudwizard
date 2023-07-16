<?php

namespace Sentgine\Crudwizard\Console\Commands;

use Illuminate\Support\Str;

class CrudwizardRestApi extends CrudwizardGenerate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:generate-rest-api {--resource= : The name of the resource}
                                                     {--fields= : The fields of the resource}
                                                     {--prefix= : The route prefix for your resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the rest api class based on the given resource.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("\n=== Generating the Rest API Class ===");

        $resourceName = $this->hasOption('resource') ? $this->option('resource') : '';
        $fields = $this->hasOption('fields') ? $this->option('fields') : [];
        $prefix = $this->hasOption('prefix') ? $this->option('prefix') : '';
        $resourceName = $this->askTheResourceName($resourceName);
        $fields = $this->askTheFields($fields);
        $data = $this->setData($resourceName, $fields, $prefix);
        $namespace = $data['controller_api_namespace'];
        $controllerClass = 'Api/' . $data['controller_api_class'];

        // Create the base rest api first if applicable
        $this->callSilent('crudwizard:generate-base-rest-api');

        // Create the resource class
        $this->callSilent("make:resource", [
            'name' => $data['resource_class']
        ]);

        // Create the resource class
        $this->callSilent("crudwizard:generate-route",  [
            '--resource' => $resourceName,
            '--prefix' => $data['route_prefix'],
            '--type' => 'api'
        ]);

        // Generate the model file
        $this->callSilent('crudwizard:generate-model', [
            '--resource' => $resourceName,
            '--fields' => $fields
        ]);

        if (!empty($data['controller_api_prefix'])) {
            $namespace = $data['controller_api_namespace'] . '\\' . $data['controller_api_prefix'];
            $controllerClass = 'Api/' . $data['controller_api_prefix'] . $this->SEPARATOR . $data['controller_api_class'];
        }

        $this->info('Controller API class: ' . $controllerClass);
        $directoryFullPath = $this->replaceDirectorySlash(app_path('Http/Controllers/Api/' . $data['controller_api_prefix']));
        $this->createDirectoryRecursively($directoryFullPath);

        $this->info("Generating " . $data['controller_api_prefix'] . "\\" . $data['controller_api_class'] . "...");
        // Generate the file by replacing placeholders in the stub with actual values
        $this->generateMyFileFromTheStub(
            [
                'namespace' => Str::replace('/', '\\', $namespace),
                'useRequestClass' => $data['use_request_class'],
                'useResourceClass' => $data['use_resource_class'],
                'useModelClass' => $data['use_model_class'],
                'controllerApiClass' => $data['controller_api_class'],
                'controllerApiBaseClass' => $data['controller_api_base_class'],
                'resourceClass' => $data['resource_class'],
                'modelClass' => $data['model_class'],
                'resourceNameSingular' => $data['resource_name_singular'],
                'requestClass' => $data['request_class'],
            ],
            $controllerClass, // Filename
            'rest_api_class'
        );
    }
}
