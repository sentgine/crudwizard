<?php

namespace Sentgine\Crudwizard\Console\Commands;

use Illuminate\Support\Str;

class CrudwizardModel extends CrudwizardGenerate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudwizard:generate-model {--resource= : The name of the resource}
                                                      {--fields= : The fields of the resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the model class based on the given resource.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("\n=== Generating the Model Class ===");

        $resourceName = $this->hasOption('resource') ? $this->option('resource') : '';
        $fields = $this->hasOption('fields') ? $this->option('fields') : [];
        $resourceName = $this->askTheResourceName($resourceName);
        $fields = $this->askTheFields($fields);
        $data = $this->setData($resourceName, $fields);

        $this->info('Generating the' . $data['model_namespace'] . '\\$className...');

        $destination = app_path('Models/' . $data['model_class'] . '.php');

        // Exit method if the file exists.
        if ($this->isFileExists($destination) && !$this->isForced) {
            $this->comment("The " . $data['model_class'] . " model class already exists. --skipping");
            return;
        }

        // Create a model class.
        $this->callSilent("make:model", [
            'name' => $data['resource_name']
        ]);

        $this->comment("Adding fillable properties to the " . $data['model_class'] . " class...");

        // Prepare the content.
        $indentation = "\t";
        $content = PHP_EOL . $indentation . 'protected $fillable = ' . $this->buildFillableFields($data['fields']) . ';';
        $somewhere = 'use HasFactory;';

        // Append content to the file.
        $this->appendContentToAFile($destination, $destination, $content, $somewhere);
    }
}
