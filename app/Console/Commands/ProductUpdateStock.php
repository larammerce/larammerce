<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class ProductUpdateStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:update-stock {--code=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates specific product from fin manager server';

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
     * @return integer
     */
    public function handle()
    {
        $this->output->write("Updating product [code: " . $this->option('code') . "] ... \t ", false);
        $product = Product::where("code", $this->option('code'))->first();
        if ($product == null) {
            $this->output->writeln("[<fg=red>✘</>]");
            return 1; // there are no product with this code.
        } else {
            $result = $product->updateFinData();
            if ($result) {
                $this->output->writeln("[<fg=green>✔</>]");
                return 0; // success.
            } else {
                $this->output->writeln("[<fg=red>✘</>]");
                return 3; // there were error in fetching data from fin server of saving it on local database.
            }
        }
    }
}
