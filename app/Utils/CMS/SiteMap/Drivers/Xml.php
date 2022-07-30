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
use DateTime;
use Exception;

class Xml extends BaseDriver
{
    protected string $targetFile = "sitemap.xml";

    protected function formatResult($result)
    {
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" .
            "<urlset" .
            " xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"" .
            " xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"" .
            " xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9" .
            " http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">" .
            $result .
            "\n</urlset>";
    }

    protected function formatDirectorySection($title, $content)
    {
        return "{$title}{$content}";
    }

    /**
     * @throws Exception
     */
    protected function getDirectoryTitle(Directory $directory)
    {
        $loc = htmlspecialchars($directory->getFrontUrl());
        $lastMod = date('c', (new DateTime($directory->updated_at))->getTimestamp());
        $changeFreq = "weekly";
        $priority = ($directory->directory_id == null) ? "0.8" : "0.6";

        return $this->formatData($loc, $lastMod, $changeFreq, $priority);
    }

    /**
     * @throws Exception
     */
    protected function getProductSection(Product $product)
    {
        $loc = htmlspecialchars($product->getFrontUrl());
        $lastMod = date('c', (new DateTime($product->updated_at))->getTimestamp());
        $changeFreq = "weekly";
        $priority = "1";

        return $this->formatData($loc, $lastMod, $changeFreq, $priority);
    }

    protected function getArticleSection(Article $article)
    {
        $loc = htmlspecialchars($article->getFrontUrl());
        $lastMod = date('c', (new DateTime($article->updated_at))->getTimestamp());
        $changeFreq = "monthly";
        $priority = "0.5";

        return $this->formatData($loc, $lastMod, $changeFreq, $priority);
    }

    private function formatData($loc, $lastMod, $changeFreq, $priority)
    {
        return "\n<url>" .
            "<loc>{$loc}</loc>" .
            "<lastmod>{$lastMod}</lastmod>" .
            "<changefreq>{$changeFreq}</changefreq>" .
            "<priority>{$priority}</priority>" .
            "</url>";
    }
}
