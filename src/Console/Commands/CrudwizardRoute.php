<?php

namespace Sentgine\Crudwizard\Console\Commands;

use Illuminate\Support\Str;

class CrudwizardRoute extends CrudwizardGenerate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:generate-route {--resource= : The name of the resource}
                                                      {--prefix= : The route prefix for your resource}
                                                      {--type= : The route type (default is web)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the route resource based on the given resource.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("\n=== Generating the Route Resource ===");

        $resourceName = $this->hasOption('resource') ? $this->option('resource') : '';
        $prefix = $this->hasOption('prefix') ? $this->option('prefix') : '';
        $type = $this->hasOption('type') ? $this->option('type') : '';
        $resourceName = $this->askTheResourceName($resourceName);
        $data = $this->setData($resourceName, [], $prefix);

        // Build the link
        $link = $data['resource_name_plural'];
        if (!empty($data['route_prefix'])) {
            $link = $data['route_prefix'] . '/' . $data['resource_name_plural'];
        }

        switch ($type) {
            case 'api':

                $this->info('Appending the route to the routes/api.php');

                // Build the controller class
                $controllerClass = $data['controller_api_class'];
                if (!empty($data['controller_api_prefix'])) {
                    $controllerClass = Str::replace('/', '\\', $data['controller_api_prefix']) . '\\' . $data['controller_api_class'];
                }

                // Create the resource path and web route path.
                $routeResourcePath = base_path('routes/api.php');
                $routeResourceContent = "\nRoute::apiResource('" . $link . "', " . $data['controller_api_namespace'] . '\\' . $controllerClass . '::class);';

                break;
            case 'web':
            default:
                $this->info('Appending the route to the routes/web.php');

                // Build the controller class
                $controllerClass = $data['controller_class'];
                if (!empty($data['controller_prefix'])) {
                    $controllerClass = Str::replace('/', '\\', $data['controller_prefix']) . '\\' . $data['controller_class'];
                }

                // Create the resource path and web route path.
                $routeResourcePath = base_path('routes/web.php');
                $routeResourceContent = "\nRoute::resource('/" . $link . "', " . $data['controller_namespace'] . '\\' . $controllerClass . '::class);';
                break;
        }


        // Add route resource to web.php in the resource path.
        $this->appendTheContentToTheFile(
            $routeResourcePath,
            $routeResourceContent,
            "Adding route resource '" . $data['resource_name_plural'] . "' to $routeResourcePath...",
            "The '" . $data['resource_name_plural'] . "' resource already exists. --skipping"
        );
    }
}
