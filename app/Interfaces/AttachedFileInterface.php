<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/5/17
 * Time: 12:32 PM
 */

namespace App\Interfaces;

interface AttachedFileInterface
{
    public function hasFile();

    public function getFilePath();

    public function setFilePath(array $input);

    public function removeFile();
}
