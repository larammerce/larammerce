<?php

namespace App\Console\Commands;

use App\Models\Color;
use App\Models\Directory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\PStructureAttrValue;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use JsonMachine\Items;
use stdClass;

class ProductImport extends Command
{
    const BASE_CURRENCY_UNIT = 28000;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import {--with-dirs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected array $colors;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->colors = [];
    }

    public function handle()
    {
        if ($this->option("with-dirs")) {
            $this->importDirs();
        }

        $this->importProducts();

        return 0;
    }

    private function importProducts()
    {
        $products_root_dir = public_path("uploads/data/");

        foreach (scandir($products_root_dir) as $index => $products_sub_dir) {
            $sub_dir_full_path = $products_root_dir . $products_sub_dir;
            if ($products_sub_dir == "menu" or str_starts_with($products_sub_dir, ".") or is_file($sub_dir_full_path)) {
                continue;
            }

            $data_file_path = $sub_dir_full_path . "/data.json";
            if (!is_file($data_file_path)) {
                echo "No data file exists in $data_file_path\n";
                continue;
            }

            $data = json_decode(file_get_contents($data_file_path));
            if (!isset($data->code) or !isset($data->ref) or !isset($data->urls) or !isset($data->categories)) {
                echo "The product data file is old and must not be added to the database: $data_file_path \n";
                continue;
            }

            $image_paths = [];
            $sec_flag = false;
            foreach (scandir($sub_dir_full_path) as $product_specific_file) {
                $tmp_lower = strtolower($product_specific_file);
                $is_main = str_contains($tmp_lower, "main");
                if (str_ends_with($tmp_lower, "jpg") or str_ends_with($tmp_lower, "jpeg")) {
                    if (!$is_main and !$sec_flag) {
                        $sec_flag = true;
                        $is_secondary = true;
                    } else {
                        $is_secondary = false;
                    }
                    $image_paths[] = [
                        "path" => str_replace(public_path(), "", $sub_dir_full_path),
                        "real_name" => $product_specific_file,
                        "extension" => "jpg",
                        "is_main" => $is_main,
                        "is_secondary" => $is_secondary
                    ];
                }
            }

            Product::whereRaw(DB::raw("code like \"$data->code\""))->update(["count" => 0, "is_active" => 0]);

            $parent_directory = $this->detectParentDirectory($data->categories);
            if ($parent_directory == null)
                continue;

            $product = $this->findMainProductByData($data);
            $model_id = $product?->model_id;
            $parent_directory_ids = $this->getParentDirectoryIds($data->categories);
            $color_ids = $this->detectColors($data->color);
            $brand = $this->detectBrand($data->urls);
            $latest_price = $this->calculatePrice($data->price, implode("-", $data->categories));
            $previous_price = $this->calculatePrice($data->old_price, implode("-", $data->categories));

            foreach ($data->sizes as $iter_size) {
                $tmp_code = "prodirect-{$data->ref}-" . str_replace(["/", " ", "(", ")", "\\", "_"], "-", trim($iter_size->eu));
                $product = Product::where("code", $tmp_code)->first();
                if ($product == null) {
                    $product = Product::create([
                        "title" => $data->title,
                        "latest_price" => $previous_price !== 0 ? $previous_price : $latest_price,
                        "latest_special_price" => $previous_price !== 0 ? $latest_price : 0,
                        "count" => 10,
                        "has_discount" => $previous_price !== 0,
                        "extra_properties" => [],
                        "directory_id" => $parent_directory->id,
                        "p_structure_id" => 2,
                        "description" => $data->description,
                        "code" => $tmp_code,
                        "average_rating" => 4.5,
                        "rates_count" => 20,
                        "is_active" => true,
                        "min_allowed_count" => 0,
                        "max_purchase_count" => 1,
                        "min_purchase_count" => 1,
                        "model_id" => $model_id,
                        "is_accessory" => false,
                        "is_visible" => true,
                        "priority" => (-1 * intval($data->ref)),
                    ]);

                    if ($model_id == null) {
                        $model_id = $product->id;
                        $product->update(["model_id" => $model_id]);
                    }

                    if ($brand !== null) {
                        $product->pAttributes()->create([
                            "p_structure_attr_key_id" => $brand->p_structure_attr_key_id,
                            "p_structure_attr_value_id" => $brand->id
                        ]);
                    }

                    $size = $this->detectSize($iter_size);
                    $product->pAttributes()->create([
                        "p_structure_attr_key_id" => $size->p_structure_attr_key_id,
                        "p_structure_attr_value_id" => $size->id
                    ]);

                } else {
                    $product->update([
                        "latest_price" => $previous_price !== 0 ? $previous_price : $latest_price,
                        "latest_special_price" => $previous_price !== 0 ? $latest_price : 0,
                        "count" => 10,
                    ]);
                }

                $product->colors()->sync($color_ids);
                $product->directories()->sync($parent_directory_ids);

                foreach ($image_paths as $image_path) {
                    if ($product->images()->where("path", $image_path["path"])->where("real_name", $image_path["real_name"])->count() == 0) {
                        $product->images()->create($image_path);
                    }
                }
                $product->save();
            }
        }
    }

    private function detectBrand($urls): ?PStructureAttrValue
    {
        foreach ($urls as $url) {
            foreach ($this->getBrands() as $brand) {
                $tmp_brand = str_replace(" ", "-", strtolower(trim($brand))) . "-";
                if (str_contains($url, $tmp_brand)) {
                    $p_structure_attr_value = PStructureAttrValue::where("name", $brand)->where("p_structure_attr_key_id", 3)->first();
                    if ($p_structure_attr_value == null) {
                        $p_structure_attr_value = PStructureAttrValue::create([
                            "name" => $brand,
                            "en_name" => $brand,
                            "p_structure_attr_key_id" => 3
                        ]);
                    }
                    return $p_structure_attr_value;
                }
            }
        }
        return null;
    }

    private function detectSize($size): PStructureAttrValue
    {
        $p_structure_attr_value = PStructureAttrValue::where("name", $size->eu)->where("p_structure_attr_key_id", 5)->first();
        if ($p_structure_attr_value == null) {
            $p_structure_attr_value = PStructureAttrValue::create([
                "name" => $size->eu,
                "en_name" => $size->eu,
                "p_structure_attr_key_id" => 5
            ]);
        }
        return $p_structure_attr_value;
    }

    private function detectParentDirectory($categories): ?Directory
    {
        foreach ($categories as $category) {
            $directory = Directory::where("metadata", $category)
                ->whereRaw(DB::raw("not exists (select ds1.id from directories as ds1 where ds1.directory_id = directories.id)"))
                ->first();
            if ($directory !== null)
                return $directory;
        }
        return null;
    }

    private function getParentDirectoryIds($categories): array
    {
        $result = [];
        $directories = Directory::whereIn("metadata", $categories)->get();
        foreach ($directories as $directory) {
            foreach ($directory->getParentDirectories() as $sub_directory) {
                $result[] = $sub_directory->id;
            }
        }
        return $result;
    }

    private function detectColors($color)
    {
        $color_words = array_filter(explode("-",
            str_replace(["/", "\\", "_", "%", "'", "\"", " "], "-", strtolower(trim($color)))),
            function ($color_word) {
                return strlen($color_word) !== 0;
            });
        $existing_colors = [];
        foreach ($color_words as $color_word) {
            foreach ($this->getColors() as $iter_color) {
                if (str_contains($iter_color->name, $color_word)) {
                    $existing_colors[] = $iter_color;
                    break;
                }
            }
        }

        $color_ids = [];
        foreach ($existing_colors as $existing_color) {
            $persisted_color = Color::where("hex_code", $existing_color->hex)->first();
            if ($persisted_color === null) {
                $persisted_color = Color::create([
                    "name" => $existing_color->name,
                    "hex_code" => $existing_color->hex
                ]);
            }
            $color_ids[] = $persisted_color->id;
        }

        return $color_ids;
    }

    private function findMainProductByData($data): ?Product
    {
        $product = $this->findMainProductByRef($data->ref);
        if ($product != null)
            return $product;
        foreach ($data->related_colors ?? [] as $ref) {
            $product = $this->findMainProductByRef($ref);
            if ($product !== null) {
                return $product;
            }
        }
        return null;
    }

    private function findMainProductByRef($ref): ?Product
    {
        $ref = "prodirect-" . $ref;
        return Product::whereRaw(DB::Raw("code like \"$ref%\""))->first();
    }

    private function importDirs()
    {
        $menu_file_dir = base_path("data/output/menu/");

        foreach (scandir($menu_file_dir) as $index => $menu_file_path) {
            $menu_file_full_path = $menu_file_dir . $menu_file_path;
            if (is_dir($menu_file_full_path))
                continue;

            $site_address = str_replace(["menu.", ".json"], "", $menu_file_path);
            $site_title = str_replace(["www.", ".com"], "", $site_address);

            $head_directory = Directory::where("url_full", "/" . $site_title)->first();
            if ($head_directory == null) {
                $head_directory = Directory::create([
                    "title" => $site_title, "url_part" => $site_title, "is_internal_link" => false, "is_anonymously_accessible" => true,
                    "has_web_page" => false, "priority" => $index, "content_type" => 3, "directory_id" => null,
                    "show_in_navbar" => true, "show_in_footer" => false, "cover_image_path" => null, "description" => "",
                    "data_type" => 1, "show_in_app_navbar" => false, "has_discount" => false, "is_location_limited" => false,
                    "cmc_id" => null, "force_show_landing" => false, "inaccessibility_type" => 1, "notice" => "",
                    "metadata" => $site_address
                ]);
                $head_directory->setUrlFull();
            }

            foreach (Items::fromFile($menu_file_full_path) as $head) {
                $this->createProductDirectory($head, $head_directory);
            }

        }
    }

    private function createProductDirectory(stdClass $node, Directory|Model $directory)
    {
        $title = strip_tags($node?->data?->title);
        $url_part = strtolower(str_replace([" • ", "\"", "'", " ", ".", "_", "\t", "\n", "@", "#", "%", "!", "?", "^", "&", "*", "(", ")", "=", "+", "•"], "-", $title));

        $head_directory = Directory::where("url_full", $directory->url_full . "/" . $url_part)->first();
        if ($head_directory == null) {
            $head_directory = $directory->directories()->create([
                "title" => $title, "url_part" => $url_part, "is_internal_link" => false, "is_anonymously_accessible" => true,
                "has_web_page" => false, "priority" => 0, "content_type" => 3, "directory_id" => null,
                "show_in_navbar" => true, "show_in_footer" => false, "cover_image_path" => null, "description" => "",
                "data_type" => 1, "show_in_app_navbar" => false, "is_location_limited" => false,
                "cmc_id" => null, "force_show_landing" => false, "inaccessibility_type" => 1, "notice" => "",
                "metadata" => $node?->data?->url
            ]);
            $head_directory->setUrlFull();
        }

        foreach ($node->sub_nodes as $sub_node) {
            $this->createProductDirectory($sub_node, $head_directory);
        }
    }

    private function getColors(): array
    {
        if (count($this->colors) == 0)
            $this->colors = json_decode(file_get_contents(public_path("primary_data/colors.json")));
        return $this->colors;
    }

    private function getBrands(): array
    {
        return [
            "admiral",
            "altra",
            "asics",
            "bv sport",
            "babolat",
            "bjorn borg",
            "brooks",
            "calvin klein",
            "canterbury",
            "castore",
            "champion",
            "ciele",
            "concave",
            "converse",
            "diadora",
            "diamond",
            "dickies",
            "eastpak",
            "ellesse",
            "errea",
            "falke",
            "fila",
            "football flick",
            "g-form",
            "gglab",
            "gore",
            "havaiana",
            "head",
            "hi-tec",
            "hoka",
            "hummel",
            "hurley",
            "hydrogen",
            "inov-8",
            "joma",
            "jordan",
            "k-swiss",
            "kappa",
            "lacoste",
            "le coq sportif",
            "lotto",
            "lyle & scott",
            "macron",
            "maurten",
            "mcdavid",
            "merrell",
            "metasox",
            "mitchell & ness",
            "mitre",
            "mizuno",
            "moving comfort",
            "new balance",
            "new era",
            "nike",
            "nuun",
            "oneglove",
            "oofos",
            "pantofola d'oro",
            "peak performance",
            "playbrave",
            "precision",
            "pro-direct",
            "puma",
            "rwlk",
            "reebok",
            "reusch",
            "ronhill",
            "runderwear",
            "sixpad",
            "saucony",
            "score draw",
            "sells",
            "sergio tacchini",
            "shock absorber",
            "skechers",
            "skins",
            "sneakers er",
            "sneaky",
            "soccerbible",
            "stance",
            "storelli",
            "superga",
            "tuto",
            "the north face",
            "tommy sport",
            "trusleeve",
            "trusox",
            "uhlsport",
            "umbro",
            "under armour",
            "veja",
            "vans",
            "wilson",
            "yonex",
            "adidas",
            "adidas originals",
            "gloveglu",
            "sis",
            "2xu",
            "ab1",
            "amo",
            "ho",
            "do",
            "on"
        ];
    }

    private function getBannedBrands(): array
    {
        return [
            "babolat",
            "head",
            "nike",
            "asics"
        ];
    }

    private function isBrandBanned($brand_title): bool
    {
        return in_array(strtolower(trim($brand_title)), $this->getBannedBrands());
    }

    public function calculatePrice($base_price, $keywords): float|int
    {
        if (is_string($base_price))
            $base_price = floatval(trim(str_replace("£", "", $base_price)));

        if ($base_price == 0)
            return 0;

        if (str_contains($keywords, "boot") or
            (str_contains($keywords, "tennis") and (str_contains($keywords, "racket") or str_contains($keywords, "bag") or str_contains($keywords, "pack")))) {
            $result = intval($base_price * 0.90 * 1.75 * static::BASE_CURRENCY_UNIT) + 850000;
        } else if (str_contains($keywords, "nit") or str_contains($keywords, "glasses") or str_contains($keywords, "hat") or str_contains($keywords, "cap") or str_contains($keywords, "beanie")) {
            $result = intval($base_price * 0.85 * 1.75 * static::BASE_CURRENCY_UNIT) + 150000;
        } else if (((str_contains($keywords, "cloth") or str_contains($keywords, "watch")) and $base_price < 70)) {
            $result = intval($base_price * 0.85 * 1.75 * static::BASE_CURRENCY_UNIT) + 270000;
        } else {
            $result = intval($base_price * 0.90 * 1.75 * static::BASE_CURRENCY_UNIT) + 300000;
        }

        return intval($result / 1000) * 1000;
    }

}
