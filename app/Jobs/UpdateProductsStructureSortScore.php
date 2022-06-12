<?php

namespace App\Jobs;

use App\Models\PStructureAttrKey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateProductsStructureSortScore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private PStructureAttrKey $p_structure_attribute_key;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PStructureAttrKey $key)
    {
        $this->p_structure_attribute_key = $key;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->p_structure_attribute_key->products as $related_product) {
            $saved = $related_product->buildStructureSortScore($this->p_structure_attribute_key);
            if(!$saved)
                Log::error("saving product failed: ".json_encode($related_product).
                    ":p_structure_attr_key: ".json_encode($this->p_structure_attribute_key));
        }
    }
}
