<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/29/18
 * Time: 2:15 PM
 */

namespace App\Console\Commands;


use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProductUpdatePurePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:update-pure-price';

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (Product::all() as $product) {
            $this->output->write("updating product {$product->id} : ");
            $product->updateTaxAmount();
            $product->save();
            $this->output->writeLn("[<fg=green>✔</>]");
        }
        return 0;
    }
}