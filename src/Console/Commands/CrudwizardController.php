<?php

namespace Sentgine\Crudwizard\Console\Commands;

use Illuminate\Support\Str;

class CrudwizardController extends CrudwizardGenerate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:generate-controller {--resource= : The name of the resource}
                                                           {--prefix= : The route prefix for your resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the controller class based on the given resource.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("\n=== Generating the Controller Class ===");

        $resourceName = $this->hasOption('resource') ? $this->option('resource') : '';
        $prefix = $this->hasOption('prefix') ? $this->option('prefix') : '';
        $resourceName = $this->askTheResourceName($resourceName);
        $data = $this->setData($resourceName, [], $prefix);
        $namespace = $data['controller_namespace'];
        $controllerClass = $data['controller_class'];
        $routePrefix = !empty($data['route_prefix']) ? '/' . $data['route_prefix'] . '/' : '';

        if (!empty($data['controller_prefix'])) {
            $namespace = $data['controller_namespace'] . '\\' . $data['controller_prefix'];
            $controllerClass = $data['controller_prefix'] . $this->SEPARATOR . $data['controller_class'];
        }

        $this->info('Controller class: ' . $controllerClass);
        $directoryFullPath = $this->replaceDirectorySlash(app_path('Http/Controllers/' . $data['controller_prefix']));
        $this->createDirectoryRecursively($directoryFullPath);

        // Generate the file by replacing placeholders in the stub with actual values.
        $this->generateMyFileFromTheStub(
            [
                'namespace' => Str::replace('/', '\\', $namespace),
                'useControllerClass' => config('crudwizard.namespace.controller'),
                'useRequestClass' => $data['use_request_class'],
                'useModelClass' => $data['use_model_class'],
                'useSearchServiceClass' => $data['use_search_service_class'],
                'controllerClass' => $data['controller_class'],
                'resourceNameSingular' => $data['resource_name_singular'],
                'resourceNamePlural' => $data['resource_name_plural'],
                'requestClass' => $data['request_class'],
                'modelClass' => $data['model_class'],
                'searchServiceClass' => $data['search_service_class'],
                'viewPrefix' => $this->viewPrefix($data['route_prefix']),
                'routePrefix' => $routePrefix,
            ],
            $controllerClass,
            'controller'
        );
    }

    /**
     * Generates a view prefix based on the given prefix string.
     *
     * @param string|null $prefix The prefix string.
     * @return string|null The generated view prefix.
     */
    protected function viewPrefix(string|null $prefix = ""): string|null
    {
        if (empty($prefix)) {
            return $prefix;
        }

        $prefix = Str::replace('\\', '.',  $prefix); // Replaces '\\' with '.'
        $prefix = Str::replace('/', '.',  $prefix); // Replaces '/' with '.'

        return $prefix . '.'; // Appends '.' to the prefix and returns it

    }
}
