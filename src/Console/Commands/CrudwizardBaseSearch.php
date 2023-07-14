<?php

namespace Sentgine\Crudwizard\Console\Commands;

class CrudwizardBaseSearch extends CrudwizardGenerate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:generate-base-search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the base search class based on the given resource.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("\n=== Generating the Base Search Class ===");
        $data = $this->setData();

        // Create the Services path directory.
        $this->createTheDirectory(app_path('Services'));

        // Create the Search/Services path directory.
        $this->createTheDirectory(app_path('Services/Search'));

        // Generate my file from the stub.
        $this->info("Generating " . $data['search_service_base_class'] . " parent search class...");
        $this->generateMyFileFromTheStub(
            [
                'namespace' => $data['service_namespace'],
                'className' => $data['search_service_base_class'],
            ],
            $data['search_service_base_class'],
            'parent_search_class'
        );
    }
}
