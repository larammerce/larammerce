<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/29/18
 * Time: 2:15 PM
 */

namespace App\Console\Commands;


use App\Models\DiscountCard;
use Illuminate\Console\Command;

class DiscountFixRelation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discount:fix-relation';

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
        DiscountCard::chunk(100,
            /**
             * @param DiscountCard[] $discount_cards
             */
            function ($discount_cards) {
            foreach ($discount_cards as $discount_card) {
                if($discount_card->invoice_id != null){
                    $invoice = $discount_card->invoice;
                    $invoice->discount_card_id = $discount_card->id;
                    $invoice->save();
                }
            }
        });
        return 0;
    }
}