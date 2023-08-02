<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/2/17
 * Time: 4:22 PM
 */

namespace App\Interfaces;

use App\Models\Directory;
use App\Traits\Fileable;

interface CMSExposedNodeInterface {
    public function parentField();

    public function getName();

    public function getAdminUrl();

    public function getFrontUrl();

    public function attachFileTo(?Directory $dest): void;

    /**
     * fill $dest param to detach more efficiently.
     * @param Directory|null $dest
     * @return mixed
     */
    public function detachFile($dest = null);

    /**
     * @return Fileable
     */
    public function cloneFile();

    /**
     * @param $dest
     * @return void
     */
    public function generateNewUrls($dest);
}
