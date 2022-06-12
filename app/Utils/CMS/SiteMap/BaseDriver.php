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
use App\Models\Enums\DirectoryType;
use App\Models\Product;

abstract class BaseDriver
{
    protected $targetFile = "sitemap.xxx";

    public final function save()
    {
        $siteMap = $this->generate();
        file_put_contents(public_path($this->getTargetFile()), $siteMap);
    }

    public final function generate()
    {
        $result = "";
        foreach (Directory::roots()->with('directories')->orderBy('priority')->get() as $directory)
            $result .= $this->getDirectorySection($directory);
        return $this->formatResult($result);
    }

    protected function getDirectorySection(Directory $directory)
    {
        $title = $this->getDirectoryTitle($directory);
        $content = "";
        $sub_directories = $directory->directories;
        if (count(is_countable($sub_directories)?$sub_directories :[]) > 0) {
            foreach ($sub_directories as $subDirectory)
                $content .= $this->getDirectorySection($subDirectory);
        } else if ($directory->content_type == DirectoryType::PRODUCT) {
            foreach ($directory->products()->mainModels()->visible()->get() as $product)
                $content .= $this->getProductSection($product);
        } else if ($directory->content_type == DirectoryType::BLOG) {
            foreach ($directory->articles as $article)
                $content .= $this->getArticleSection($article);
        }

        return $this->formatDirectorySection($title, $content);
    }


    protected function getTargetFile()
    {
        return $this->targetFile;
    }

    abstract protected function formatResult($result);

    abstract protected function formatDirectorySection($title, $content);

    abstract protected function getDirectoryTitle(Directory $directory);

    abstract protected function getProductSection(Product $product);

    abstract protected function getArticleSection(Article $article);
}