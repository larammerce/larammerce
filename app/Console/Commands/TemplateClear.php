<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Utils\CMS\Template\TemplateService;

/**
 * @package App\Console\Commands
 * @author  Arash Khajelou <a.khajelou@gmail.com>
 * @link    https://github.com/a-khajelou
 */
class TemplateClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hctemplate:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command clears ecommerce template';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @SuppressWarnings(PHPMD)
     * @return mixed
     */
    public function handle()
    {
        TemplateService::clearAllViews();
    }
}
