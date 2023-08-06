<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/5/17
 * Time: 12:32 PM
 */

namespace App\Interfaces;


interface ImageOwnerInterface
{
    public function hasImage();

    public function getImagePath();

    public function setImagePath();

    public function removeImage();

    public function getDefaultImagePath();

    public function getImageCategoryName();

    public function isImageLocal();
}
