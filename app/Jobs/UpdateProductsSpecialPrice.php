<?php

namespace App\Jobs;

use App\Models\Directory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateProductsSpecialPrice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private Directory $directory;
    private int $descent_percentage;

    public function __construct(Directory $directory, $descent_percentage)
    {
        $this->directory = $directory;
        $this->descent_percentage = $descent_percentage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->directory->leafProducts()->chunk(100,
            function ($products) {
                foreach ($products as $product) {
                    $product->update([
                        "latest_special_price" => $product->latest_price - ($product->latest_price * $this->descent_percentage / 100),
                        "has_discount" => true
                    ]);
                }
            });
    }
}
