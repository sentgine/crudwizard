<?php

namespace Sentgine\Crudwizard\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Sentgine\Crudwizard\Traits\File;
use Illuminate\Support\Str;

class CrudwizardDelete extends CrudwizardGenerate
{
    use File;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:delete {--resource= : The name of the resource}
                                              {--prefix= : The route prefix for your resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete the CRUD resource';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Get the resource name from the command option
        $resourceName = $this->option('resource');
        $prefix = $this->hasOption('prefix') ? $this->option('prefix') : '';

        // Ask for the resource name if not provided
        if (!$resourceName) {
            $resourceName = $this->ask('What is the resource name?');
        }

        // Ask one more time if the user wants to continue
        $isContinue = $this->ask("Are you sure you want to delete the $resourceName resource? <fg=yellow>[yes/no]</>");
        if (in_array($isContinue, ['no', 'NO', 'No', 'nO'])) {
            $this->info('Exiting...');
            return;
        }

        // Display an info message about removing the specified resource
        $this->info("Removing the " . $this->replaceDirectorySlash($resourceName) . " resource...");
        $data = $this->setData($resourceName, [], $prefix);

        // The files to delete
        $extension = '.php';
        $files = [
            'controllerClassPath' => $this->setControllerClassPath($data),
            'controllerApiClassPath' => $this->setControllerClassPath($data, true),
            'modelClassPath' => app_path('Models/' . $data['model_class'] . $extension),
            'requestClassPath' => app_path('Http/Requests/' . $data['request_class'] . $extension),
            'factoryClassPath' => database_path('factories/' . $data['factory_class'] . $extension),
            'testClassPath' => base_path('tests/Feature/' . $data['test_class'] . $extension),
            'searchClassPath' => app_path('Services/Search/' . $data['search_service_class'] . $extension),
            'index' => $data['resource_directory'] . '/index.blade' . $extension,
            'show' => $data['resource_directory'] . '/show.blade' . $extension,
            'edit' => $data['resource_directory'] . '/edit.blade' . $extension,
            'create' => $data['resource_directory'] . '/create.blade' . $extension,
            '_search-form' => $data['resource_directory'] . '/_search-form.blade' . $extension,
            '_edit-form' => $data['resource_directory'] . '/_edit-form.blade' . $extension,
            '_create-form' => $data['resource_directory'] . '/_create-form.blade' . $extension,
        ];

        // Remove the files
        foreach ($files as $path) {
            // Remove the file
            if ($this->removeFile($path)) {
                // Display an error message indicating the removal of the file
                $this->error('Removed ' . $this->replaceDirectorySlash($path) . ' file.');
            }
        }

        if (!empty($prefix)) {
            // Remove controllers directory
            $this->removeNestedDirectories($data['controller_prefix'], app_path('Http/Controllers/'));

            // Remove views directory
            $this->removeNestedDirectories($prefix, base_path(config('crudwizard.path.view')));
        }

        // Display a comment explaining that the migration file was not removed
        $migrationFilename = 'create_' .  $data['resource_name_plural'] . '_table' . $extension;
        $this->comment("\nDid not remove migration file containing " . $migrationFilename . ' under database/migrations/, as it contains historical data.');
    }

    /**
     * Sets the file path for the controller class based on the given resource data.
     *
     * @param array $resourceData The data of the resource.
     *  @param bool $isApi Determines if the API controller class path should be used.
     * @return string The file path for the controller class.
     */
    private function setControllerClassPath(array $resourceData, bool $isApi = false): string
    {
        $defaultControllerClassPath = 'Http/Controllers/';
        if ($isApi) {
            $defaultControllerClassPath = 'Http/Controllers/Api/';
        }

        $controllerClassPath = app_path($defaultControllerClassPath . $resourceData['controller_class'] . '.php'); // Default controller class path

        // Check if a controller prefix is specified
        if (!empty($resourceData['controller_prefix'])) {
            $controllerClassPath = app_path($defaultControllerClassPath . $resourceData['controller_prefix'] . $this->SEPARATOR . $resourceData['controller_class'] . '.php'); // Controller class path with prefix
        }

        return $controllerClassPath; // Return the controller class path
    }

    /**
     * Removes the nested directories.
     *  
     * @param string|null $routePrefix The prefix for the route.
     * @param string|null $basePath The path to the directory.
     * @return void
     */
    protected function removeNestedDirectories(string|null $routePrefix, string|null $basePath = ""): void
    {
        $pathList = $this->buildDirectoryPath($routePrefix, $basePath);
        foreach ($pathList as $path) {
            $this->removeDirectory($path);
        }
    }
}
