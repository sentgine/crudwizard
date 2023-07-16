<?php

namespace Sentgine\Crudwizard\Console\Commands;

class CrudwizardBaseRestApi extends CrudwizardGenerate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:generate-base-rest-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the base rest api class.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("\n=== Generating the Base Rest API Class ===");
        $data = $this->setData();

        // Create the Services path directory.
        $this->createTheDirectory(app_path('Http/Controllers/Api'));

        // Generate my file from the stub.
        $this->info("Generating the " . $data['controller_api_base_class'] . " parent REST API class...");
        $this->generateMyFileFromTheStub(
            [
                'namespace' => $data['controller_api_namespace'],
                'className' => $data['controller_api_base_class'],
            ],
            $data['controller_api_base_class'], // Filename
            'base_rest_api_class'
        );
    }
}
