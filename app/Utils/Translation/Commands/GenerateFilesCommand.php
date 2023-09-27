<?php

namespace App\Utils\Translation\Commands;

use App\Utils\CMS\Setting\Language\LanguageSettingService;
use App\Utils\Translation\TranslationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Translation Model and migration for models that use Translatable Trait.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (LanguageSettingService::isMultiLangSystem()) {
            $this->alert("Starting Translation File Generator ... ");
            foreach (TranslationService::getTranslatableModels() as $class) {
                $this->info("Try to generate files for {$class->getClassName()}");
                TranslationService::makeTranslationMigration($class);
                TranslationService::makeTranslationModel($class);
            }
            Artisan::call("migrate", [
                "--path" => TranslationService::getTranslationMigrationsBasePath(),
                "--force" => true
            ]);
        }

        return 0;
    }


}
