<?php

namespace Sentgine\Crudwizard\Console\Commands;



class CrudwizardRequest extends CrudwizardGenerate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:generate-request {--resource= : The name of the resource}
                                                        {--fields= : The fields of the resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the request class based on the given resource.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("\n=== Generating the Request Class ===");

        $resourceName = $this->hasOption('resource') ? $this->option('resource') : '';
        $fields = $this->hasOption('fields') ? $this->option('fields') : [];
        $resourceName = $this->askTheResourceName($resourceName);
        $fields = $this->askTheFields($fields);
        $data = $this->setData($resourceName, $fields);

        $this->info("Generating " . $data['request_namespace'] . "\\" . $data['request_class'] . "...");

        $directoryFullPath = $this->replaceDirectorySlash(app_path('Http/Requests/' . $data['request_class']));
        $this->createDirectoryRecursively($directoryFullPath);
        
        // Generate the file by replacing placeholders in the stub with actual values.
        $this->generateMyFileFromTheStub(
            [
                'namespace' => $data['request_namespace'],
                'requestClass' => $data['request_class'],
                'fields' => $this->buildRequestFields($data['fields']),
            ],
            $data['request_class'],
            'request'
        );
    }
}
