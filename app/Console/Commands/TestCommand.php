<?php

namespace App\Console\Commands;

use App\Models\Directory;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Hello world !");

        $data = json_decode(file_get_contents(base_path("data/menu.json")));

        foreach ($data as $row) {
            if(!isset($row->title)){
                echo $row->id . "->" . $row->metadata . "\n";
            }else{
                Directory::where("id", $row->id)
                    ->update([
                        "title" => $row->title
                    ]);
            }
        }
    }
}
