<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/28/2017 AD
 * Time: 13:14
 */

namespace App\Utils\CMS\SiteMap\Drivers;


use App\Models\Article;
use App\Models\Directory;
use App\Models\Product;
use App\Utils\CMS\SiteMap\BaseDriver;

class Html extends BaseDriver
{
    protected $targetFile = "sitemap.html";

    protected function formatResult($result)
    {
        return "<html><head><meta charset='utf-8'></head><body><ul>{$result}</ul></body></html>";
    }

    protected function formatDirectorySection($title, $content)
    {
        return "<li>{$title}" . (($content != '') ? ("<ul>{$content}</ul>") : "") . "</li>";
    }

    protected function getDirectoryTitle(Directory $directory)
    {
        return "<a href='".str_replace("-&", '',$directory->getFrontUrl())."'>{$directory->title}</a>";
    }

    protected function getProductSection(Product $product)
    {
        return "<li><a href='".str_replace("-&", '', $product->getFrontUrl())."'>{$product->title}</a></li>";
    }

    protected function getArticleSection(Article $article)
    {
        return "<li><a href='".str_replace("-&", '', $article->getFrontUrl())."'>{$article->title}</a></li>";
    }
}