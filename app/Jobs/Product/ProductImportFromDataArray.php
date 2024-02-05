<?php

namespace App\Jobs\Product;

use App\Exceptions\Directory\DirectoryNotFoundException;
use App\Exceptions\Product\ProductNotFoundException;
use App\Jobs\Job;
use App\Models\PStructure;
use App\Services\Product\ProductImporterService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProductImportFromDataArray extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels;

    private PStructure $p_structure;
    private array $data_array;

    public function __construct(PStructure $p_structure, array $data_array) {
        $this->p_structure = $p_structure;
        $this->data_array = $data_array;
    }

    public function handle() {
        try {
            ProductImporterService::importFromDataArray($this->p_structure, $this->data_array);
        } catch (DirectoryNotFoundException $e) {
            Log::error("jobs.product.product_import_from_data_array.directory_not_found." . json_encode($this->data_array) . "." . json_encode($this->p_structure) . "." . $e->getMessage());
        } catch (ProductNotFoundException $e) {
            Log::error("jobs.product.product_import_from_data_array.product_not_found." . json_encode($this->data_array) . "." . json_encode($this->p_structure) . "." . $e->getMessage());
        }
    }

    /**
     * @return int|null
     */
    public function getDispatchType(): ?int
    {
        return null;
    }

    /**
     * @return int|null
     */
    public function getQueuePriority(): ?int
    {
        return null;
    }
}
