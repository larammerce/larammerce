<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/8/16
 * Time: 12:34 AM
 */

namespace App\Utils\Common;


use App\Interfaces\ImageOwnerInterface;
use App\Jobs\ImageResizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageService
{
    public static function getImageConfig($category, $size = null)
    {
        if (isset($size))
            return config("cms.images.{$category}.{$size}");
        else
            return config("cms.images.{$category}");
    }

    public static function getImage(ImageOwnerInterface $model = null, $type = 'original', $absolute = false)
    {
        if ($model == null)
            return '';

        if (!$model->isImageLocal())
            return $model->getImagePath();

        $prefix = ($absolute ? env('APP_URL') : '');
        if ($model->hasImage()) {
            $imagePath = $model->getImagePath();
            $lastDotPosition = strrpos($imagePath, '.');
            $resultImage = substr_replace($imagePath, '-' . $type, $lastDotPosition, 0);

            if (file_exists(public_path() . $resultImage))
                return $prefix . $resultImage;
            else if (file_exists(public_path() . $imagePath))
                return $prefix . $imagePath;
        }
        return $prefix . $model->getDefaultImagePath();
    }

    public static function saveImage($category, $input_name = "image", UploadedFile $file = null)
    {
        $upload_file = $file ?? request()->file($input_name);
        $image_name = $upload_file->getClientOriginalName();
        $image_name = str_replace(' ', '-', $image_name);
        $destination_path = '/uploads/' . $category . '/' . (string)microtime(true) . Str::random(10);
        $extension = $upload_file->getClientOriginalExtension();
        $upload_file->move(public_path() . $destination_path, $image_name);

        $main_image_path = public_path() . $destination_path . '/' . $image_name;
        foreach (self::getImageConfig($category) as $key => $value) {
            if ($key != 'ratio' and in_array(strtolower($extension), ['jpg', 'png', 'webp'])) {
                $job = new ImageResizer($main_image_path, $destination_path, $key, $value["width"], $value["height"]);
                dispatch($job);
            }
        }

        $image_obj = new \stdClass();
        $image_obj->name = $image_name;
        $image_obj->destinationPath = $destination_path;
        $image_obj->extension = $extension;
        return $image_obj;
    }
}
