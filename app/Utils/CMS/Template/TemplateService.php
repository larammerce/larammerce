<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 9/27/17
 * Time: 4:21 PM
 */

namespace App\Utils\CMS\Template;

use App\Models\WebPage;
use Exception;

class TemplateService {
    private static $TEMPLATE_VIEWS_DIR = "hc-template";
    private static $FINAL_VIEWS_DIR = "views/public";
    private static $ORIGINAL_VIEWS = [];

    /**
     * when new template is uploaded to the system, this method must be called to define template entries
     * @SuppressWarnings(PHPMD)
     */
    public static function initializeTemplate() {
        echo "<h1>Initializing template</h1>";
        static::clearGeneratedViews();
        static::loadViews();
        $initialized_templated = [];
        foreach (static::$ORIGINAL_VIEWS as $blade) {
            if (!in_array($blade, $initialized_templated)) {
                echo "│<br/>" . PHP_EOL;
                echo "└── initializing blade \"${blade}\"<br/>" . PHP_EOL;
                $template = new TemplateModel(
                    $blade,
                    static::getBladePath($blade),
                    static::getOriginalBladePath($blade)
                );
                $template->initialize();

                $blade_name = str_replace(".blade.php", "", $blade);
                foreach (RelativeBladeType::values() as $relative_blade_postfix) {
                    $relative_blade = $blade_name . $relative_blade_postfix . ".blade.php";
                    if (in_array($relative_blade, static::$ORIGINAL_VIEWS)) {
                        echo "│      └── initializing blade \"${relative_blade}\"<br/>"
                            . PHP_EOL;
                        $relative_template = new TemplateModel(
                            $relative_blade,
                            static::getBladePath($relative_blade),
                            static::getOriginalBladePath($relative_blade)
                        );
                        $relative_template->initialize();
                        if (static::isPartialView($blade)) {
                            echo "│      └── detected partial blade, saving it ... <br/>"
                                . PHP_EOL;
                            $relative_template->savePublic(static::getViewPath($relative_blade));
                        }

                        if (static::isSystemView($blade)) {
                            echo "│      └── detected system needle blade, saving it ... " .
                                "<br/>" . PHP_EOL;
                            $relative_template->savePublic(static::getViewPath($relative_blade));
                        }
                        array_push($initialized_templated, $relative_blade);
                    }
                }
                static::rebuildTemplateWebPage($template, $blade_name);
                array_push($initialized_templated, $blade);
            }
        }
    }

    private static function loadViews() {
        static::$ORIGINAL_VIEWS = static::getOriginalBlades();
    }

    private static function rebuildTemplateWebPage(TemplateModel $template, $blade_name) {

        if (static::isPartialView($blade_name)) {
            echo "│      └── detected partial blade, saving it ... <br/>" . PHP_EOL;
            $template->savePublic(static::getViewPath($blade_name));
        }

        if (static::isSystemView($blade_name)) {
            echo "│      └── detected system needle blade, saving it ... <br/>" . PHP_EOL;
            $template->savePublic(static::getViewPath($blade_name));
        }

        $webPages = WebPage::whereBladeName($blade_name)->get();
        foreach ($webPages as $webPage) {
            echo "│      └── rebuilding web page with id : " . $webPage->id .
                "<br/>" . PHP_EOL;
            $webPage->update(["data" => []]);
        }
    }

    /**
     * returns the list of galleries in specific blade file
     *
     * @param string $bladeName
     * @param null $directoryId
     * @return array
     */
    public static function getGalleries($bladeName, $directoryId = null) {
        $template = new TemplateModel($bladeName, static::getOriginalBladePath($bladeName));
        return $template->getGalleries($directoryId);
    }

    /**
     * this method returns the list of all blades which are places in the /resources/hc-template directory
     *
     * @param bool $system
     * @return array
     */
    public static function getOriginalBlades(bool $system = false): array {
        $result = [];
        $extension = "/originals";
        foreach (scandir(resource_path(static::$TEMPLATE_VIEWS_DIR . $extension)) as $file) {
            if (strpos($file, ".blade.php") !== false and (!$system or static::isSystemView($file))) {
                $result[] = $file;
            }
        }

        return $result;
    }

    /**
     * this method returns the path for template blade,
     * placed in the directory /resources/hc-template
     *
     * @param $bladeName
     * @return string
     */
    public static function getBladePath($blade_name) {
        $blade_name = strpos($blade_name, ".blade.php") !== false ? $blade_name : $blade_name . ".blade.php";
        return resource_path(static::$TEMPLATE_VIEWS_DIR . "/${blade_name}");
    }

    public static function copyBlade($from_blade_name, $to_blade_name) {
        $from_blade_content = static::getBladeContent($from_blade_name);
        static::setBladeContent($to_blade_name, $from_blade_content);
    }

    /**
     * this method returns the path for template blade,
     * placed in the directory /resources/hc-template
     *
     * @param $bladeName
     * @return string
     */
    public static function getOriginalBladePath($blade_name) {
        $blade_name = strpos($blade_name, ".blade.php") !== false ? $blade_name : $blade_name . ".blade.php";
        return resource_path(static::$TEMPLATE_VIEWS_DIR . "/originals/${blade_name}");
    }

    /**
     * this method returns the path for view blade,
     * placed in the directory /resources/views/public
     *
     * @param $bladeName
     * @return string
     */
    public static function getViewPath($bladeName) {
        $bladeName = strpos($bladeName, ".blade.php") !== false ? $bladeName : $bladeName . ".blade.php";
        try {
            mkdir(resource_path(static::$FINAL_VIEWS_DIR));
        } catch (Exception $e) {
        }

        return resource_path(static::$FINAL_VIEWS_DIR . "/${bladeName}");
    }

    public static function getBladeContent($bladePath) {
        if (file_exists($bladePath)) {
            $bladeFile = fopen($bladePath, "r");
            if ($bladeFile !== false) {
                return fread($bladeFile, filesize($bladePath));
            }
            fclose($bladeFile);
        }
        return "";
    }

    public static function setBladeContent($bladePath, $bladeContent) {
        $bladeFile = fopen($bladePath, "w");
        if ($bladeFile !== false) {
            fwrite($bladeFile, $bladeContent);
        }
        fclose($bladeFile);
    }

    public static function clearGeneratedViews() {
        static::deleteAll(resource_path(static::$FINAL_VIEWS_DIR));
        static::deleteAll(resource_path(static::$TEMPLATE_VIEWS_DIR));
    }

    public static function clearAllViews() {
        static::deleteAll(resource_path(static::$FINAL_VIEWS_DIR));
        static::deleteAll(resource_path(static::$TEMPLATE_VIEWS_DIR));
        static::deleteAll(resource_path(static::$TEMPLATE_VIEWS_DIR . "/originals"));
    }

    /**
     * @param string $str_path
     * @param int $depth
     * @return void
     */
    private static function deleteAll($str_path, $depth = 1): void {
        if (is_file($str_path)) {
            unlink($str_path);
        } elseif (is_dir($str_path) and $depth > 0) {
            $scan = glob(rtrim($str_path, "/") . "/*");
            foreach ($scan as $path) {
                static::deleteAll($path, $depth - 1);
            }
        }
    }

    /**
     * @param string $blade_name
     * @return bool
     */
    private static function isPartialView(string $blade_name): bool {
        return strpos($blade_name, "_") === 0;
    }

    /**
     * @param string $blade_name
     * @return bool
     */
    private static function isSystemView(string $blade_name): bool {
        return strpos($blade_name, "_") === false;
    }
}
