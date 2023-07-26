<?php

namespace App\Console\Commands;

use App\Enums\Directory\DirectoryType;
use App\Models\Directory;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use JsonMachine\Exception\InvalidArgumentException;
use stdClass;

class ProductImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import';

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
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws InvalidArgumentException
     */
    public function handle() {
        /** @var Directory[] $head_product_directories */
        $head_product_directories = Directory::from(DirectoryType::PRODUCT)->get();

        foreach ($head_product_directories as $head_product_directory) {
            $menu_data = file_get_contents(env("STOCK_HOST") . "/api/root?url=" . base64_encode($head_product_directory->metadata));
            dd($menu_data);
        }
        /*
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

                }*/

        return 0;
    }

    private function createProductDirectory(stdClass $node, Directory|Model $directory) {
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
}
