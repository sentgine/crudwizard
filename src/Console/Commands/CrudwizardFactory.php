<?php

namespace Sentgine\Crudwizard\Console\Commands;

use Illuminate\Support\Str;

class CrudwizardFactory extends CrudwizardGenerate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:generate-factory {--resource= : The name of the resource}
                                                        {--fields= : The fields of the resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the factory class based on the given resource.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("\n=== Generating the Factory Class ===");

        $resourceName = $this->hasOption('resource') ? $this->option('resource') : '';
        $fields = $this->hasOption('fields') ? $this->option('fields') : [];
        $resourceName = $this->askTheResourceName($resourceName);
        $fields = $this->askTheFields($fields);
        $data = $this->setData($resourceName, $fields);

        $this->info("Generating " . $data['factory_namespace'] . "\\" . $data['factory_class'] . "...");

        // Set the name of the model class.
        $modelClass = '\\' . $data['model_namespace'] . '\\' . $data['resource_name'];

        // Generate the file by replacing placeholders in the stub with actual values.
        $this->generateMyFileFromTheStub(
            [
                'namespace' => $data['factory_namespace'],
                'modelClass' => $modelClass,
                'factoryClass' => $data['factory_class'],
                'fields' => $this->buildFactoryFields($data['fields']),
            ],
            $data['factory_class'],
            'factory'
        );
    }
}
