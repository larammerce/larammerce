<?php

namespace App\Services\Directory;

use App\Exceptions\Directory\DirectoryNotFoundException;
use App\Helpers\Common\StringHelper;
use App\Models\Directory;
use App\Utils\CMS\AdminRequestService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Ixudra\Curl\Facades\Curl;
use stdClass;

class DirectoryService {

    private static string $admin_tag = 'Admin';
    private static string $customer_tag = 'Customer';

    /**
     * @throws DirectoryNotFoundException
     */
    public static function findDirectoryById(int $directory_id): Directory {
        try {
            return Directory::findOrFail($directory_id);
        } catch (Exception $e) {
            throw new DirectoryNotFoundException("The directory with id `{$directory_id}` not found int the database.");
        }
    }

    public static function clearCache(): void {
        Cache::tags([static::$admin_tag, static::$customer_tag])->flush();
    }

    public static function buildDirectoriesTree(?Directory $root = null, array $conditions = [], array $order = []): array {
        $tag = static::getCacheTag();
        $cache_key = StringHelper::getCacheKey([static::class, __FUNCTION__], $root?->id ?? 0, json_encode($conditions), json_encode($order));
        if (!Cache::tags([$tag])->has($cache_key)) {
            $directories = Directory::permitted()->where($conditions)
                ->orderBy($order["column"] ?? "priority", $order["direction"] ?? "ASC")->get();
            $branch = [];
            $parts = [];
            $map = [];

            foreach ($directories as $directory) {
                $map[$directory->id] = $directory;
                $directory->setRelation("directories", []);
                if (!isset($parts[$directory->directory_id]))
                    $parts[$directory->directory_id] = [];
                $parts[$directory->directory_id][] = $directory;
            }

            foreach ($parts as $parent_id => $children) {
                if (isset($map[$parent_id]))
                    $map[$parent_id]->setRelation("directories", $children);
                else {
                    $branch = array_merge($branch, $children);
                }
            }

            Cache::tags([$tag])->put($cache_key, ($root == null ? $branch : ($map[$root->id]->directories ?? [])));
        }

        return Cache::tags([$tag])->get($cache_key);
    }

    public static function syncWithUpstream(Directory $directory): void {
        $metadata = $directory->metadata;
        if (!filter_var($metadata, FILTER_VALIDATE_URL)) {
            return;
        }

        $curl_result = Curl::to(env("STOCK_HOST", "http://localhost:8080") . "/api/root?url=" . base64_encode($metadata))
            ->asJson()
            ->get();

        foreach ($curl_result as $sub_node) {
            static::createProductDirectoryByNodeData($directory, $sub_node);
        }
    }

    private static function createProductDirectoryByNodeData(Directory $parent_directory, stdClass $node_data) {
        $title = strip_tags($node_data?->data?->title);
        $url_part = static::buildUrlPartFromString($title);

        $head_directory = $parent_directory->directories()->where("url_part", $url_part)->first();
        if (is_null($head_directory)) {
            /** @var Directory $head_directory */
            $head_directory = $parent_directory->directories()->create([
                "title" => $title, "url_part" => $url_part, "is_internal_link" => false, "is_anonymously_accessible" => true,
                "has_web_page" => false, "priority" => 0, "content_type" => 3, "directory_id" => null,
                "show_in_navbar" => true, "show_in_footer" => false, "cover_image_path" => null, "description" => "",
                "data_type" => 1, "show_in_app_navbar" => false, "is_location_limited" => false,
                "cmc_id" => null, "force_show_landing" => false, "inaccessibility_type" => 1, "notice" => "",
                "metadata" => $node_data?->data?->url
            ]);
            $head_directory->setUrlFull();
        }

        foreach ($node_data->sub_nodes as $sub_node) {
            static::createProductDirectoryByNodeData($head_directory, $sub_node);
        }
    }

    private static function getCacheTag(): string
    {
        return AdminRequestService::isInAdminArea() ?
            static::$admin_tag : static::$customer_tag;
    }

    public static function buildUrlPartFromString(string $title): string {
        $title = preg_replace('!\s+!', "-", strtolower($title));
        return preg_replace("/[^a-zA-Z0-9\-]/", "", $title);
    }

    /**
     * @param Directory[] $directories
     * @param int $parent_id
     * @return array
     */
    public static function buildDirectoryGraph(Collection|array $directories, int $parent_id = 0): array {
        $directories_count = count($directories);
        if ($directories_count == 0)
            return [];

        if ($parent_id === 0) {
            $roots = [];
            $root_ids = [];
            for ($i = 0; $i < count($directories) and !in_array($directories->get($i)->directory_id, $root_ids); $i++) {
                $root_directory = $directories->get($i);
                $root_directory->child_nodes = static::buildDirectoryGraph($directories, $root_directory->id);
                $roots[] = $root_directory;
                $root_ids[] = $root_directory->id;
            }

            return $roots;
        } else {
            $children = [];

            for ($i = 0; $i < count($directories); $i++) {
                $directory = $directories->get($i);
                if ($directory->directory_id === $parent_id) {
                    $directory->child_nodes = static::buildDirectoryGraph($directories, $directory->id);
                    $children[] = $directory;
                }
            }

            return $children;
        }
    }
}
