<?php

namespace App\Console\Commands;

use App\Services\Directory\DirectoryService;
use Illuminate\Console\Command;

class DirectoryCacheClear extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'directory:cache-clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        DirectoryService::clearCache();
        return 0;
    }

}
