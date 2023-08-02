<?php
/**
 * Created by PhpStorm.
 * User: amirhosein
 * Date: 1/22/19
 * Time: 4:14 PM
 */

namespace App\Utils\CMS\File;


use App\Enums\Directory\DirectoryType;
use App\Models\Directory;

class ClipBoardService
{
    /**
     * @param $tickedFilesList
     * @throws InvalidActionException
     * @throws InvalidIdsException
     * @throws InvalidTypeException
     */
    public static function setFiles($tickedFilesList)
    {
        if (!key_exists('action', $tickedFilesList) or !(in_array(trim($tickedFilesList['action']), FileAction::values())))
            throw new InvalidActionException("action not found in FileAction enums " . json_encode($tickedFilesList));
        elseif (!key_exists('type', $tickedFilesList) or !(in_array(trim($tickedFilesList['type']), FileType::values())))
            throw new InvalidTypeException("type not found in FileType enums. " . json_encode($tickedFilesList));
        elseif (!key_exists('ids', $tickedFilesList) or sizeof($tickedFilesList['ids']) == 0)
            throw new InvalidIdsException("ids not found in FileType enums. " . json_encode($tickedFilesList));
        else
            session()->put('clip-board', $tickedFilesList);
    }

    /**
     * @return mixed
     * @throws EmptyClipBoardException
     */
    public static function getFiles()
    {
        if (self::hasFiles())
            return session()->get('clip-board');
        throw new EmptyClipBoardException("session has not 'clip-board' key.");
    }

    public static function hasFiles()
    {
        return session()->has('clip-board');
    }

    /**
     * @return integer
     * @throws EmptyClipBoardException
     * @throws InvalidActionException
     */
    public static function getFilesAction()
    {
        $items = self::getFiles();
        if (isset($items['action']) and strlen($items['action']) > 0)
            return $items['action'];
        throw new InvalidActionException("action is'nt set or null");
    }

    /**
     * @return mixed
     * @throws EmptyClipBoardException
     * @throws InvalidTypeException
     */
    public static function getFilesType()
    {
        $items = self::getFiles();
        if (isset($items['type']) and strlen($items['type']) > 0)
            return $items['type'];
        throw new InvalidTypeException("type is'nt set or null");
    }

    /**
     * @return mixed
     * @throws EmptyClipBoardException
     * @throws InvalidIdsException
     */
    public static function getFilesIds()
    {
        $items = self::getFiles();
        if (isset($items['ids']) and sizeof($items['ids']) > 0)
            return $items['ids'];
        throw new InvalidIdsException("ids is'nt set or null");
    }

    /**
     * @param Directory $directory
     * @return bool
     * @throws EmptyClipBoardException
     * @throws InvalidTypeException
     */
    public static function isPastePossible($directory)
    {
        $filesType = self::getFilesType();
        return self::hasFiles() and
            (
                ($filesType === FileType::DIRECTORY and ($directory === null or
                        ($directory->products()->count() === 0 and $directory->articles()->count() === 0))) or
                ($filesType === FileType::PRODUCT and $directory->content_type == DirectoryType::PRODUCT and
                    $directory->directories()->count() === 0) or
                ($filesType === FileType::ARTICLE and $directory->content_type == DirectoryType::BLOG and
                    $directory->directories()->count() === 0)
            );
    }

    public static function flush()
    {
        session()->forget("clip-board");
    }
}
