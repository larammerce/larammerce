<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/28/2017 AD
 * Time: 13:14
 */

namespace App\Utils\CMS\SiteMap;


use App\Models\Article;
use App\Models\Directory;
use App\Models\Product;
use Illuminate\Support\Collection;

abstract class BaseDriver
{
    protected string $target_file = "sitemap.xxx";
    protected array $urls;
    protected string $result;

    public function __construct()
    {
        $this->urls = [];
        $this->result = "";
    }

    public final function save(): void
    {
        $siteMap = $this->generate();
        file_put_contents(public_path($this->getTargetFile()), $siteMap);
    }

    public final function generate()
    {
        $tree = build_directories_tree();
        foreach ($tree as $directory)
            $this->result .= $this->getDirectorySection($directory);

        Product::chunk(500,
            /**
             * @param Collection|Product[] $products
             */
            function (Collection|array $products) {
                foreach ($products as $product) {
                    $this->result .= $this->getProductSection($product);
                }
            });

        Article::chunk(500,
            /**
             * @param Collection|Article[] $articles
             */
            function (Collection|array $articles) {
                foreach ($articles as $article) {
                    $this->result .= $this->getArticleSection($article);
                }
            });

        return $this->formatResult();
    }

    protected function getDirectorySection(Directory $directory): string
    {
        if ($directory->is_internal_link or in_array($directory->getFrontUrl(), $this->urls)) {
            return "";
        } else {
            $this->urls[] = $directory->getFrontUrl();
        }

        $title = $this->getDirectoryTitle($directory);
        $content = "";
        $sub_directories = $directory->directories;
        if (count(is_countable($sub_directories) ? $sub_directories : []) > 0) {
            foreach ($sub_directories as $subDirectory)
                $content .= $this->getDirectorySection($subDirectory);
        }

        return $this->formatDirectorySection($title, $content);
    }


    protected function getTargetFile()
    {
        return $this->target_file;
    }

    abstract protected function formatResult();

    abstract protected function formatDirectorySection($title, $content);

    abstract protected function getDirectoryTitle(Directory $directory);

    abstract protected function getProductSection(Product $product);

    abstract protected function getArticleSection(Article $article);
}
