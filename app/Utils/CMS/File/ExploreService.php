<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/5/17
 * Time: 7:22 PM
 */

namespace App\Utils\CMS\File;

use App\Models\Directory;

/**
 * Class ExploreService
 * @package App\Utils\CMS
 */
class ExploreService
{
    private static $currentDirectory = "cms_user_explore_current_directory";
    private static $selectedDirectories = "cms_user_explore_selected_directories";

    /**
     * @return mixed
     */
    public static function getCurrentDirectory()
    {
        return request()->session()->get(self::$currentDirectory);
    }

    /**
     * @param $directoryId
     */
    public static function setCurrentDirectory($directoryId)
    {
        request()->session()->put(self::$currentDirectory, $directoryId);
        self::generateSelectedDirectories();
    }

    private static function generateSelectedDirectories(){
        $selectedDirectory = self::getCurrentDirectory();
        $selectedDirectories = [];
        if($selectedDirectory != null and $selectedDirectory != 0){
            $directory = Directory::find($selectedDirectory);
            $selectedDirectories[] = $directory->id;
            while($directory->directory_id != null){
                $selectedDirectories[] = $directory->directory_id;
                $directory = Directory::find($directory->directory_id);
            }
        }
        request()->session()->put(self::$selectedDirectories, $selectedDirectories);
    }

    public static function getSelectedDirectories(){
        $result = request()->session()->get(self::$selectedDirectories);
        return $result ? $result : [];
    }
}