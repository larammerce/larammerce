<?php

namespace App\Console\Commands;

use App\Models\PStructure;
use App\ProtectedModels\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ProductExportStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:export-structure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private int $counter;
    private array $result;


    public function __construct()
    {
        $this->counter = 0;
        $this->result = [];

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set("memory_limit", -1);
        foreach (PStructure::all() as $p_structure) {
            $result[$p_structure->title] = [];
            foreach ($p_structure->products as $product) {
                $this->counter++;
                $product_attributes = [];
                foreach ($p_structure->attributeKeys as $attribute_key) {
                    $product_attributes[$attribute_key->id] = ["key_title" => $attribute_key->title, "values" => []];
                }

                foreach ($product->attributeValues()
                             ->orderBy("p_structure_attr_key_id", "asc")->get()->toArray() as $value) {
                    if (!isset($product_attributes[$value["p_structure_attr_key_id"]]) or
                        !isset($product_attributes[$value["p_structure_attr_key_id"]]["values"])) {
                        continue;
                    }

                    $product_attributes[$value["p_structure_attr_key_id"]]["values"][] = $value["name"];
                }

                $record_attributes = [];
                foreach ($product_attributes as $attribute) {
                    $record_attributes[$attribute["key_title"]] = implode(", ", $attribute["values"]);
                }

                $this->result[$p_structure->title][] = array_merge([
                    "#" => $product->id,
                    "name" => $product->title,
                    "code" => $product->code
                ], $record_attributes);
            }
        }

        $this->info(json_encode($this->result));
    }
}
