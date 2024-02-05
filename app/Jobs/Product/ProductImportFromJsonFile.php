<?php

namespace App\Jobs\Product;

use App\Enums\Queue\QueueDispatchType;
use App\Enums\Queue\QueuePriority;
use App\Helpers\Common\StringHelper;
use App\Jobs\Job;
use App\Models\Color;
use App\Models\Directory;
use App\Models\Product;
use App\Models\PStructureAttrValue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductImportFromJsonFile extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels;

    private int $p_structure_id;
    private string $json_path;
    private string $images_path;
    private int $price_ratio;

    public function __construct(int $p_structure_id, string $json_path, string $images_path) {
        $this->p_structure_id = $p_structure_id;
        $this->json_path = $json_path;
        $this->images_path = $images_path;
        $this->price_ratio = env("FIN_MAN_PRICE_RATIO", 30000);
        $this->queue = config('queue.names.admin_automatic_default');
    }

    public function handle() {
        $file_content = file_get_contents($this->json_path);
        $data = json_decode($file_content);

        $image_paths = [];
        $sec_flag = false;
        $main_photo = "";
        $secondary_photo = "";
        foreach (scandir($this->images_path) as $image_file_name) {
            $tmp_lower = strtolower($image_file_name);
            if (!str_ends_with($tmp_lower, "jpg") and !str_ends_with($tmp_lower, "jpeg"))
                continue;

            $path = str_replace(public_path(), "", $this->images_path);
            $is_main = str_contains($tmp_lower, "main");
            if ($is_main) {
                $main_photo = "{$path}/{$image_file_name}";
            }

            if (!$is_main and !$sec_flag) {
                $sec_flag = true;
                $is_secondary = true;
                $secondary_photo = "{$path}/{$image_file_name}";
            } else {
                $is_secondary = false;
            }

            $image_paths[] = [
                "path" => $path,
                "real_name" => $image_file_name,
                "extension" => "jpg",
                "is_main" => $is_main,
                "is_secondary" => $is_secondary
            ];
        }

        Product::whereRaw(DB::raw("code like '{$data->code}%'"))->update(["count" => 0, "is_active" => 0]);
        $parent_directory = $this->detectParentDirectory($data->categories);
        if ($parent_directory == null)
            return;

        $model_id = $this->findMainProductByData($data);
        $parent_directory_ids = $this->getParentDirectoryIds($data->categories);
        $color_ids = $this->detectColors($data->color);
        $brand = $this->detectBrand($data->urls);
        $latest_price = $this->calculatePrice($data->price, implode("-", $data->categories));
        $previous_price = $this->calculatePrice($data->old_price, implode("-", $data->categories));

        foreach ($data->sizes as $iter_size) {
            $product = Product::where("code", $iter_size->stock_id)->first();
            if ($product == null) {
                $product = Product::create([
                    "title" => $data->title,
                    "latest_price" => $previous_price !== 0 ? $previous_price : $latest_price,
                    "latest_special_price" => $previous_price !== 0 ? $latest_price : 0,
                    "count" => 10,
                    "has_discount" => $previous_price !== 0,
                    "extra_properties" => [],
                    "directory_id" => $parent_directory->id,
                    "p_structure_id" => $this->p_structure_id,
                    "description" => $data->description,
                    "code" => $iter_size->stock_id,
                    "average_rating" => 4.5,
                    "rates_count" => 20,
                    "is_active" => true,
                    "min_allowed_count" => 0,
                    "max_purchase_count" => 1,
                    "min_purchase_count" => 1,
                    "model_id" => $model_id,
                    "is_accessory" => false,
                    "is_visible" => true,
                    "priority" => $data->priority,
                    "notice" => $iter_size->message,
                    "main_photo" => $main_photo,
                    "secondary_photo" => $secondary_photo,
                    "metadata" => $this->json_path
                ]);

                if ($model_id == 0) {
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
                    "is_active" => 1,
                    "metadata" => $this->json_path
                ]);
            }

            $product->colors()->sync($color_ids);
            $product->directories()->sync($parent_directory_ids);

            $current_images_real_name = $product->images()->pluck("real_name")->toArray();
            foreach ($image_paths as $image_path) {
                if (!in_array($image_path["real_name"], $current_images_real_name)) {
                    $product->images()->create($image_path);
                }
            }
            $product->save();
        }
    }


    private function detectBrand($urls): ?PStructureAttrValue {
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

    private function detectSize($size): PStructureAttrValue {
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

    private function detectParentDirectory($categories): ?Directory {
        foreach ($categories as $category) {
            $directory = Directory::where("metadata", $category)
                ->whereRaw(DB::raw("not exists (select ds1.id from directories as ds1 where ds1.directory_id = directories.id)"))
                ->first();
            if ($directory !== null)
                return $directory;
        }
        return null;
    }

    private function getParentDirectoryIds($categories): array {
        $result = [];
        $directories = Directory::whereIn("metadata", $categories)->get();
        foreach ($directories as $directory) {
            foreach ($directory->getParentDirectories() as $sub_directory) {
                $result[] = $sub_directory->id;
            }
        }
        return $result;
    }

    private function detectColors($color) {
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

    private function findMainProductByData($data): int {
        $product = $this->findMainProductByRef($data->ref);
        if ($product != null)
            return $product->model_id;
        foreach ($data->related_colors ?? [] as $ref) {
            $product = $this->findMainProductByRef($ref);
            if ($product !== null) {
                return $product->model_id;
            }
        }
        return 0;
    }

    private function findMainProductByRef($ref): ?Product {
        $code = "pd-" . $ref;
        return Product::whereRaw(DB::Raw("code like '{$code}%'"))->first();
    }

    private function getColors(): array {
        $cache_key = StringHelper::getCacheKey([static::class, __FUNCTION__]);
        if (!Cache::has($cache_key)) {
            Cache::put($cache_key, json_decode(file_get_contents(public_path("primary_data/colors.json"))));
        }
        return Cache::get($cache_key);
    }

    private function getBrands(): array {
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

    private function getBannedBrands(): array {
        return [
            "babolat",
            "head",
            "nike",
            "asics"
        ];
    }

    private function isBrandBanned($brand_title): bool {
        return in_array(strtolower(trim($brand_title)), $this->getBannedBrands());
    }

    public function calculatePrice($base_price, $keywords): float|int {
        if (is_string($base_price))
            $base_price = floatval(trim(str_replace("£", "", $base_price)));

        if ($base_price == 0)
            return 0;

        if (str_contains($keywords, "boot") or str_contains($keywords, "shoe") or
            (str_contains($keywords, "tennis") and (str_contains($keywords, "racket") or str_contains($keywords, "bag") or str_contains($keywords, "pack")))) {
            $result = intval($base_price * 0.90 * 1.75 * $this->price_ratio) + 3000000;
        } else if (str_contains($keywords, "nit") or str_contains($keywords, "glasses") or str_contains($keywords, "hat") or str_contains($keywords, "cap") or str_contains($keywords, "beanie")) {
            $result = intval($base_price * 0.85 * 1.75 * $this->price_ratio) + 500000;
        } else if (((str_contains($keywords, "cloth") or str_contains($keywords, "watch")) and $base_price < 70)) {
            $result = intval($base_price * 0.85 * 1.75 * $this->price_ratio) + 800000;
        } else {
            $result = intval($base_price * 0.90 * 1.75 * $this->price_ratio) + 1200000;
        }

        return intval($result / 1000) * 1000;
    }

    /**
     * @return int|null
     */
    public function getDispatchType(): ?int
    {
        return QueueDispatchType::AUTOMATIC;
    }

    /**
     * @return int|null
     */
    public function getQueuePriority(): ?int
    {
        return QueuePriority::DEFAULT;
    }
}
