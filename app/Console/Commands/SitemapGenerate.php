<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/29/18
 * Time: 2:15 PM
 */

namespace App\Console\Commands;


use App\Models\Product;
use App\Utils\CMS\SiteMap\Provider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SitemapGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerates sitemap files.';

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
     * @return mixed
     */
    public function handle()
    {
        Provider::save();
        return 0;
    }
}