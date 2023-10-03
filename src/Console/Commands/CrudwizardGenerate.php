<?php

namespace Sentgine\Crudwizard\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Sentgine\Crudwizard\Traits\FieldBuilder;
use Sentgine\Crudwizard\Traits\File;
use Symfony\Component\Console\Helper\Table;

class CrudwizardGenerate extends Command
{
    use File, FieldBuilder;

    protected string $SEPARATOR = '/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:generate {--resource= : The name of the resource}
                                                {--fields= : The fields for your resource}
                                                {--prefix= : The route prefix for your resource}
                                                {--force : This will overwrite existing resource scaffold!}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the CRUD resource';

    /**
     * Whether or not you want to overwrite the existing scaffold.
     *
     * @var bool
     */
    protected bool $isForced;

    /**
     * Constructor method.
     * Initializes a new instance of the class.
     */
    public function __construct()
    {
        parent::__construct();
        $this->isForced = false;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // The flag to overwrite everything.
        if ($this->option('force')) {
            $this->isForced = true;
        }

        // Gather the inputs
        $data = $this->gatherData();

        // Ask the user for the resource name, fields, route prefix
        $resourceName = $data['resource_name'];
        $fields = $data['fields'];
        $routePrefix = $data['route_prefix'];

        // Generate the route resource
        $this->call('crudwizard:generate-route', [
            '--resource' => $resourceName,
            '--prefix' => $routePrefix,
        ]);

        // Generate the controller file
        $this->call('crudwizard:generate-controller', [
            '--resource' => $resourceName,
            '--prefix' => $routePrefix,
        ]);

        // Generate the model file
        $this->call('crudwizard:generate-model', [
            '--resource' => $resourceName,
            '--fields' => $fields
        ]);

        // Generate the request file
        $this->call('crudwizard:generate-request', [
            '--resource' => $resourceName,
            '--fields' => $fields
        ]);

        // Generate the migration file
        $this->call('crudwizard:generate-migration', [
            '--resource' => $resourceName,
            '--fields' => $fields
        ]);

        // Generate the factory file
        $this->call('crudwizard:generate-factory', [
            '--resource' => $resourceName,
            '--fields' => $fields
        ]);

        // Generate the test case file
        $this->call('crudwizard:generate-test', [
            '--resource' => $resourceName,
            '--fields' => $fields,
            '--prefix' => $routePrefix,
        ]);

        // Generate the search class
        $this->call('crudwizard:generate-search', [
            '--resource' => $resourceName,
            '--fields' => $fields
        ]);

        // Generate the views
        $this->call('crudwizard:generate-views', [
            '--resource' => $resourceName,
            '--fields' => $fields,
            '--prefix' => $routePrefix,
        ]);

        // Generate the rest api endpoint
        $this->call('crudwizard:generate-rest-api', [
            '--resource' => $resourceName,
            '--fields' => $fields,
            '--prefix' => $routePrefix,
        ]);
    }

    /**
     * Common database migration data types.
     *
     * @return array An array of common database migration data types.
     */
    public static function commonDatabaseMigrationDataTypes(): array
    {
        return [
            'string',
            'email',
            'integer',
            'text',
            'boolean',
            'date',
            'dateTime',
            'decimal',
            'enum',
            'foreignId',
        ];
    }

    /**
     * Gather the data needed for the command.
     *
     * @return array The gathered data.
     */
    protected function gatherData(): array
    {
        // Get the resource name and the fields from the command option
        $resourceName = $this->hasOption('resource') ? $this->option('resource') : '';
        $fields = $this->hasOption('fields') ? $this->option('fields') : [];
        $prefix = $this->hasOption('prefix') ? $this->option('prefix') : '';
        $resourceName = $this->askTheResourceName($resourceName);
        $fields = $this->askTheFields($fields);

        return [
            'resource_name' => Str::ucfirst(Str::singular($resourceName)),
            'fields' => $fields,
            'route_prefix' => $prefix,
        ];
    }

    /**
     * Asks for the resource name.
     *
     * @param string|null $resourceName The optional default resource name.
     * @return string The entered resource name.
     */
    protected function askTheResourceName(string|null $resourceName = ''): string
    {
        if (empty($resourceName)) {
            // Ask for the resource name until a non-empty value is provided
            do {
                $resourceName = $this->ask('What is the resource name?');
            } while (empty($resourceName));
        }

        return Str::ucfirst(Str::singular($resourceName));
    }

    /**
     * Asks for the fields.
     *
     * @param array|null $fields The optional default fields.
     * @return array The entered fields.
     */
    protected function askTheFields(array|null $fields = []): array
    {
        // Gather the fields if they are empty and not an array
        if (empty($fields)) {
            // Call the gatherFields() method to gather the fields
            $fields = $this->gatherFields();
        }

        return $fields;
    }

    /**
     * Set the data for generating the CRUD files.
     *
     * @param string $resourceName The resource name.
     * @param array $fields The fields of the resource.
     * @param string $routePrefix The route prefix name.
     * @return array The data for generating the CRUD files.
     */
    protected function setData(string $resourceName = "", array $fields = [], string|null $routePrefix = ""): array
    {
        // Always set the resource name to singular first
        $resource_name = Str::singular($resourceName);
        $routePrefix = Str::replace('\\', '/', Str::lower($routePrefix));

        // Prepare various string transformations of the resource name
        $resource_name_ucfirst = Str::ucfirst($resource_name);
        $resource_name_lower = Str::lower($resource_name);
        $resource_name_singular = Str::lower($resource_name);
        $resource_name_plural = Str::plural($resource_name_lower);

        // Set the actual directory path
        $resource_directory = base_path(config('crudwizard.path.view') . $resource_name_plural);
        $controller_directory = base_path(config('crudwizard.path.controller'));
        $controller_prefix = '';
        $controller_api_prefix = '';
        $view_prefix = '';
        if (!empty($routePrefix)) {
            $resource_directory = base_path(config('crudwizard.path.view') . $routePrefix . $this->SEPARATOR . $resource_name_plural);
            $controller_prefix = $this->setStudlyDirectory($routePrefix);
            $controller_api_prefix = $this->setStudlyDirectory($routePrefix);
            $view_prefix = $this->setViewDirectory($routePrefix);
        }

        return [
            'route_prefix' => $routePrefix,
            'controller_prefix' =>  $controller_prefix,
            'controller_api_prefix' =>  $controller_api_prefix,
            'view_prefix' => $view_prefix,
            'fields' => $fields,
            'layout_name' => config('crudwizard.blade.layout'),
            'resource_directory' =>  $resource_directory,
            'controller_directory' =>  $controller_directory,
            'resource_name' => $resourceName,
            'resource_name_ucfirst' => $resource_name_ucfirst,
            'resource_name_lower' => $resource_name_lower,
            'resource_name_singular' => $resource_name_singular,
            'resource_name_plural' => $resource_name_plural,
            'controller_namespace' => config('crudwizard.namespace.controller'),
            'controller_api_namespace' => config('crudwizard.namespace.controller_api'),
            'request_namespace' => config('crudwizard.namespace.request'),
            'resource_namespace' => config('crudwizard.namespace.resource'),
            'model_namespace' => config('crudwizard.namespace.model'),
            'test_namespace' => config('crudwizard.namespace.tests'),
            'service_namespace' => config('crudwizard.namespace.service'),
            'factory_namespace' => config('crudwizard.namespace.factory'),
            'model_class' =>  $resource_name_ucfirst,
            'controller_class' => $resource_name_ucfirst . 'Controller',
            'controller_api_base_class' => 'ApiController',
            'controller_api_class' => $resource_name_ucfirst . 'Controller',
            'request_class' => $resource_name_ucfirst . 'Request',
            'resource_class' => $resource_name_ucfirst . 'Resource',
            'factory_class' => $resource_name_ucfirst . 'Factory',
            'test_class' => $resource_name_ucfirst . 'ControllerTest',
            'search_service_base_class' => 'SearchService',
            'search_service_class' => $resource_name_ucfirst . 'SearchService',
            'use_request_class' => config('crudwizard.namespace.request') . '\\' . $resourceName . 'Request',
            'use_resource_class' => config('crudwizard.namespace.resource') . '\\' . $resourceName . 'Resource',
            'use_model_class' => config('crudwizard.namespace.model') . '\\' . $resource_name_ucfirst,
            'use_search_service_class' => config('crudwizard.namespace.service') . '\\' . $resource_name_ucfirst . 'SearchService',
        ];
    }

    /**
     * Gather fields that will be used for
     * the model and the migration files.
     *
     * @return array An array of fields containing field type and field name.
     */
    protected function gatherFields(): array
    {
        $fields = [];
        $fieldOptions = array_merge($this->commonDatabaseMigrationDataTypes(), ['exit']);

        do {
            $this->info('=== Please type \'exit\' when you\'re done adding all the fields. ===');

            // Display the fields in a table.
            if (!empty($fields)) {
                $this->info('=== Fields to generate (Database, migrations, factory, & forms) ====');
                $this->displayArray($fields);
                $this->info("");
            }

            // Gather the field type and field name.
            $fieldType = $this->choice('Field type', $fieldOptions);

            // Check if the user wants to exit.
            if ($fieldType === 'exit') {
                break;
            }

            $fieldName = $this->ask("Field name [$fieldType]");

            $fields = array_merge($fields, [
                [
                    'field_type' => $fieldType,
                    'field_name' => $fieldName
                ]
            ]);
        } while (true);

        return $fields;
    }

    /**
     * These are the files from the stub to generate.
     *
     * @param string $filename The filename of the file to be generated.
     * @param string $viewType The filename type of the file to be generated.
     * @return array An array of files to generate.
     */
    protected function fileToGenerate(string $filename, string $viewType = "blade"): array
    {
        $basePathOrigin = "stubs/crudwizard/$viewType/classes/";

        return [
            'controller' => [
                'origin' => base_path($basePathOrigin . 'Controller.stub'),
                'destination' => app_path('Http/Controllers/' . $filename . '.php')
            ],
            'request' => [
                'origin' => base_path($basePathOrigin . 'Request.stub'),
                'destination' => app_path('Http/Requests/' . $filename . '.php')
            ],
            'factory' => [
                'origin' => base_path($basePathOrigin . 'Factory.stub'),
                'destination' => database_path('factories/' . $filename . '.php')
            ],
            'tests' => [
                'origin' => base_path($basePathOrigin . 'Test.stub'),
                'destination' => base_path('tests/Feature/' . $filename . '.php')
            ],
            'parent_search_class' => [
                'origin' => base_path($basePathOrigin . 'SearchServiceParentClass.stub'),
                'destination' => app_path('Services/Search/' . $filename . '.php')
            ],
            'child_search_class' => [
                'origin' => base_path($basePathOrigin . 'SearchServiceChildClass.stub'),
                'destination' => app_path('Services/Search/' . $filename . '.php')
            ],
            'base_rest_api_class' => [
                'origin' => base_path($basePathOrigin . 'ApiController.stub'),
                'destination' => app_path('Http/Controllers/Api/' . $filename . '.php')
            ],
            'rest_api_class' => [
                'origin' => base_path($basePathOrigin . 'RestApiController.stub'),
                'destination' => app_path('Http/Controllers/' . $filename . '.php')
            ],
        ];
    }

    /**
     * Returns an array of file paths for the views to be generated.
     *
     * @param string $resourcePath The path to the resource.
     * @param string $filename The filename for the view.
     * @param string $viewType The type of view (default: 'blade').
     * @return array An array of file paths for the views.
     */
    protected function viewToGenerate(string $resourcePath, string $filename, string $viewType = 'blade'): array
    {
        $pathToTheViewFile = $resourcePath . $this->SEPARATOR . $filename . '.blade.php';
        $basePathOrigin = "stubs/crudwizard/$viewType/views/";

        return [
            'layout' => [
                'origin' => base_path($basePathOrigin . 'layout.stub'),
                'destination' => base_path('resources/views/layouts/' . $filename . '.blade.php')
            ],
            'index' => [
                'origin' => base_path($basePathOrigin . 'index.stub'),
                'destination' => $pathToTheViewFile
            ],
            'show' => [
                'origin' => base_path($basePathOrigin . 'show.stub'),
                'destination' => $pathToTheViewFile
            ],
            'edit' => [
                'origin' => base_path($basePathOrigin . 'edit.stub'),
                'destination' => $pathToTheViewFile
            ],
            'create' => [
                'origin' => base_path($basePathOrigin . 'create.stub'),
                'destination' => $pathToTheViewFile
            ],
            '_search-form' => [
                'origin' => base_path($basePathOrigin . '_search-form.stub'),
                'destination' => $pathToTheViewFile
            ],
            '_edit-form' => [
                'origin' => [
                    'string' => base_path($basePathOrigin . 'form-fields/edit-form/text.stub'),
                    'text' => base_path($basePathOrigin . 'form-fields/edit-form/text.stub'),
                    'date' => base_path($basePathOrigin . 'form-fields/edit-form/date.stub'),
                ],
                'destination' => $pathToTheViewFile
            ],
            '_create-form' => [
                'origin' => [
                    'string' =>  base_path($basePathOrigin . 'form-fields/create-form/text.stub'),
                    'text' =>  base_path($basePathOrigin . 'form-fields/create-form/text.stub'),
                    'date' =>  base_path($basePathOrigin . 'form-fields/create-form/date.stub'),
                ],
                'destination' => $pathToTheViewFile
            ],
        ];
    }

    /**
     * Displays the array into beautiful tables.
     *
     * @param mixed $array The array to display in a table format.
     * @param array $headers Optional headers for the table.
     *
     * @return void
     */
    protected function displayArray($array = [], $headers = ['Field Type', 'Field Name']): void
    {
        $table = new Table($this->output);
        $table->setHeaders($headers)
            ->setRows($array)
            ->render();
    }

    /**
     * Creates the specified directory.
     *
     * @param string $pathToDirectory The path to the directory.
     * @return void
     */
    protected function createTheDirectory(string $pathToDirectory): void
    {
        // Display a message indicating the directory creation.
        $this->info("Creating " . $this->replaceDirectorySlash($pathToDirectory) . " directory...");

        // Check if the directory already exists.
        if (!$this->createDirectory($pathToDirectory)) {
            $this->comment("The directory " . $this->replaceDirectorySlash($pathToDirectory) . " exists. --skipping");
        }
    }

    /**
     * Creates a file with the specified path and content.
     *
     * @param string $pathToFile The path to the file.
     * @param string $content The content of the file.
     * @return void
     */
    protected function createTheFile(string $pathToFile, string $content): void
    {
        // Display a message indicating the file creation.
        $this->info("Creating " . $this->replaceDirectorySlash($pathToFile) . "...");

        // Check if the file already exists.
        if (!$this->createFile($pathToFile, $content)) {
            $this->comment("The " . $this->replaceDirectorySlash($pathToFile) . " exists. --skipping");
        }
    }

    /**
     * Appends the specified content to a file.
     *
     * @param string $pathToFile The path to the file.
     * @param string $content The content to append.
     * @return void
     */
    protected function appendTheContentToTheFile(string $pathToFile, string $content, string $info = "", string $errorInfo = ""): void
    {
        // Display a message indicating the route resource creation.
        if (!empty($displayInfo)) {
            $this->info($displayInfo);
        }

        // Check if the content is successfully appended to the file.
        if (!$this->appendToFile($pathToFile, $content, true)) {
            $displayErrorInfo = empty($errorInfo) ? "The '" . $this->replaceDirectorySlash($pathToFile) . "' resource already exists. --skipping" : $errorInfo;
            $this->comment($displayErrorInfo);
        }
    }

    /**
     * Generates a file from a stub using the specified replacements.
     *
     * @param array $replacements The text replacements for the stub.
     * @param string $filename The filename of the file to be generated.
     * @param string $key The key of the file paths in the 'fileToGenerate' array.
     * @param bool $viewMode The flag indicating whether it's in view mode.
     * @param string $resourceName The resourceName of the file to be generated.
     * @param string $pathToDirectory The path to directory for the file to be generated.
     * @return void
     */
    protected function generateMyFileFromTheStub(array $replacements, string $filename, string $key, bool $viewMode = false, string $resourceName = "", string $pathToDirectory = ""): void
    {
        // Specify the file paths for the stub file and the destination controller file.
        $fileToGenerate = $viewMode ? $this->viewToGenerate($pathToDirectory, $filename) : $this->fileToGenerate($filename);

        $origin =  $fileToGenerate[$key]['origin'];
        $destination =  $fileToGenerate[$key]['destination'];

        $this->comment("Adding $filename to " . $this->replaceDirectorySlash($destination));
        // Exit method if the file exists.
        if ($this->isFileExists($destination) && !$this->isForced) {
            $this->comment("The " . $this->replaceDirectorySlash($destination) . " file already exists. --skipping");
            return;
        }

        // Generate the file by replacing placeholders in the stub with actual values.
        $this->generateFileFromStub(
            $origin,
            $destination,
            $replacements
        );
    }

    /**
     * Build the form fields for the specified filename and fields.
     *
     * @param string $filename The filename of the form.
     * @param array $fields The array of fields.
     * @param string $resourceName The resourceName of the file to be generated.
     * @param string $pathToDirectory The path to directory for the file to be generated.
     * @return void
     */
    protected function buildTheFormFields(string $filename, array $fields, string $resourceName, string $pathToDirectory = ""): void
    {
        // Get the file paths for the specified filename
        $fileToGenerate = $this->viewToGenerate($pathToDirectory, $filename);
        $destination = $fileToGenerate[$filename]['destination'];

        // Create the file
        $this->createTheFile($destination, "");

        // Build the content for the form fields
        $content = '';
        foreach ($fields as $value) {

            // Set to text if the field type doesn't exist
            if (!isset($fileToGenerate[$filename]['origin'][$value['field_type']])) {
                $origin = $fileToGenerate[$filename]['origin']['text'];
            } else {
                // Dynamically change the field type to generate
                $origin = $fileToGenerate[$filename]['origin'][$value['field_type']];
            }

            // Then append the content
            $content .= $this->getFileAndReplaceContent($origin, [
                'label' => Str::title(Str::replace('_', ' ', $value['field_name'])),
                'fieldName' => Str::lower($value['field_name']),
                'resourceNameSingular' => Str::lower(Str::singular($resourceName)),
            ]);
        }

        // Append the content to the file
        $this->appendTheContentToTheFile($destination, $content, '');
    }

    /**
     * Prepares the directory structure for the route group.
     *
     * @param string $routePrefix The prefix for the route.
     * @param string $basePath The base path for creating directories.
     * @return void
     */
    protected function createNestedDirectories(string|null $routePrefix, string $basePath = ""): void
    {
        $path = $this->buildDirectoryPath($routePrefix, $basePath);

        foreach ($path as $value) {
            $this->createTheDirectory($value);
        }
    }

    /**
     * Builds the directory path based on the route prefix.
     *
     * @param string|null $routePrefix The prefix for the route.
     * @param string $basePath The base path to prepend to the directory paths.
     * @return array The array of directory paths.
     */
    protected function buildDirectoryPath(string|null $routePrefix = "", $basePath = ""): array
    {
        if (Str::contains($routePrefix, '\\')) {
            $routePrefix = Str::replace('\\', $this->SEPARATOR, $routePrefix);
        }

        $pathList = array();
        $pathBuild = $basePath;

        $directoryLevel = explode($this->SEPARATOR, $routePrefix);
        foreach ($directoryLevel as $path) {
            $pathBuild .= $path .  $this->SEPARATOR;
            $pathList[] = $pathBuild;
        }

        return $pathList;
    }

    /**
     * Converts a directory path to studly case.
     *
     * @param string $directoryPath The directory path.
     * @return string The converted string in studly case.
     */
    protected function setStudlyDirectory(string $directoryPath): string
    {
        $parts = explode('/', $directoryPath); // Split the string by '/'

        // Capitalize each part of the string
        $capitalized = array_map(function ($part) {
            return ucfirst($part);
        }, $parts);

        // Join the parts back together with '/'
        $convertedString = implode('/', $capitalized);

        return $convertedString;
    }

    protected function setViewDirectory(string $prefix): string
    {
        $prefix =  Str::replace('/', '.', $prefix);
        $prefix =  Str::replace('\\', '.', $prefix);

        return $prefix . '.';
    }

    /**
     * Replaces forward slashes with backslashes in the given path.
     *
     * @param string $pathToFile The path to be modified.
     * @return string The modified path with backslashes.
     */
    protected function replaceDirectorySlash(string $pathToFile): string
    {
        return Str::replace('\\', $this->SEPARATOR, $pathToFile);
    }
}
