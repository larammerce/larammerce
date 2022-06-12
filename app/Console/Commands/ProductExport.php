<?php

namespace App\Console\Commands;

use App\ProtectedModels\Product;
use Illuminate\Console\Command;

class ProductExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:export {--type=} {--cols=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function explodeCols($cols){
        $delimiter = ',';
        $cols = explode($delimiter ,$cols);
        $result = [];
        foreach ($cols as $col){
            if(strlen($col) > 0){
                $result[]=$col;
            }
        }
        return $result;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cols = $this->option('cols');
        $type = $this->option('type');
        switch (strtolower($type)) {
            case 'json' :
                if($cols != null and strlen($cols) > 0){
                    $cols = $this->explodeCols($cols);
                    $this->info(Product::all($cols)->toJson());
                }else{
                    $this->info(Product::all()->toJson());
                }
                break;
            default :
                break;
        }
        return 0;
    }
}
