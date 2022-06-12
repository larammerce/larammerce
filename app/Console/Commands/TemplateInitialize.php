<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Utils\CMS\Template\TemplateService;

/**
 * @package App\Console\Commands
 * @author  Arash Khajelou <a.khajelou@gmail.com>
 * @link    https://github.com/a-khajelou
 */
class TemplateInitialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hctemplate:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command initialized ecommerce template';

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
        TemplateService::initializeTemplate();
        echo "<br/><br/>=============<br/><a href='/developer'>go back</a>";
    }
}
