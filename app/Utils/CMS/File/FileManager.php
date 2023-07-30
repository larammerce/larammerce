<?php
/**
 * Created by PhpStorm.
 * User: a.morteza
 * Date: 2/28/19
 * Time: 1:50 PM
 */

namespace App\Utils\CMS\File;


use App\Interfaces\CMSExposedNodeInterface;
use App\Models\Article;
use App\Models\Directory;
use App\Models\Product;

class FileManager
{
    /**
     * @param integer|null $directory_id
     * @throws EmptyClipBoardException
     * @throws EmptyObjectCollectionException
     * @throws FileBadDestinationException
     * @throws FileSameDestinationException
     * @throws InvalidActionException
     * @throws InvalidIdsException
     * @throws InvalidTypeException
     */
    public static function paste($directory_id)
    {
        $newParentDirectory = Directory::find($directory_id);

        if (!ClipBoardService::isPastePossible($newParentDirectory))
            throw new FileBadDestinationException("The destination directory has not same type to these files.");

        $filesType = ClipBoardService::getFilesType();
        $objectsClassReference = self::getObjectsClassReference($filesType);
        $fileIds = ClipBoardService::getFilesIds();
        $fileObjects = self::getObjects($fileIds, $objectsClassReference);

        $filesAction = ClipBoardService::getFilesAction();
        if ($filesAction == FileAction::MOVE)
            self::move($fileObjects, $newParentDirectory);
        elseif ($filesAction == FileAction::COPY)
            self::copy($fileObjects, $newParentDirectory);
        ClipBoardService::flush();
    }

    /**
     * @param CMSExposedNodeInterface[]|Directory[]|Product[]|Article[] $movingObjects
     * @param $newParentDirectory
     * @throws FileSameDestinationException
     */
    public static function move($movingObjects, $newParentDirectory)
    {
        foreach ($movingObjects as $movingObject)
            $movingObject->moveTo($newParentDirectory);
    }

    /**
     * @param CMSExposedNodeInterface[]|Directory[]|Product[]|Article[] $copyingObjects
     * @param $newParentDirectory
     */
    public static function copy($copyingObjects, $newParentDirectory)
    {
        foreach ($copyingObjects as $copyingObject)
            $copyingObject->copyTo($newParentDirectory);
    }

    /**
     * @param $fileIds
     * @param $FileType
     * @return CMSExposedNodeInterface[]|Directory[]|Product[]|Article[]
     * @throws EmptyObjectCollectionException
     */
    public static function getObjects($fileIds, $FileType)
    {
        $fileObjects = $FileType::find($fileIds);
        if (!isset($fileObjects) or count($fileObjects) == 0)
            throw new EmptyObjectCollectionException(json_encode($FileType) .
                " objects not found " . json_encode($fileIds));
        return $fileObjects;
    }

    /**
     * @param $fileType
     * @return mixed
     * @throws InvalidTypeException
     */
    public static function getObjectsClassReference($fileType)
    {
        if (in_array($fileType, FileType::values()))
            return $fileType;
        throw new InvalidTypeException(json_encode($fileType) . " not found in FileType enums.");
    }
}
