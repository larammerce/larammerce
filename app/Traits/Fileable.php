<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/28/18
 * Time: 12:26 PM
 */

namespace App\Traits;

use App\Models\Directory;
use App\Models\ModifiedUrl;
use App\Models\RobotTxtRecord;
use App\Utils\CMS\File\FileSameDestinationException;
use App\Utils\CMS\RobotTxt\Permission;
use App\Utils\CMS\RobotTxt\Type;
use App\Utils\CMS\RobotTxt\UserAgent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Trait Fileable
 * @package App\Models\Traits
 */
trait Fileable
{
    public $linkedLeaves;

    public function toArray(): array
    {
        $parentResult = parent::toArray();
        $parentResult["url"] = $this->getFrontUrl();
        return $parentResult;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopePermitted(Builder $builder)
    {
        $system_user = get_system_user();

        if ($system_user?->is_super_user)
            return $builder;

        $directory_id = $this->table === "directories" ? "directories.id" : "{$this->table}.{$this->parentField()}";

        return $builder->whereRaw(DB::raw("not exists (select dsr1.directory_id from directory_system_role as dsr1 where dsr1.directory_id = {$directory_id})"))
            ->orWhereRaw(DB::raw("exists (select dsr2.directory_id from directory_system_role as dsr2 inner join system_user_system_role as susr1 on dsr2.system_role_id = susr1.system_role_id where dsr2.directory_id = {$directory_id} and susr1.system_user_id={$system_user->id})"));
    }

    /**
     * @param Directory|null $dest
     * @throws FileSameDestinationException
     */
    public function moveTo($dest)
    {
        //TODO: Add skip and replace options in this case.
        if (($dest != null and $this->directory_id == $dest->id) or
            ($this->directory_id == null and $dest == null))
            throw new FileSameDestinationException(get_class($this) . " id: $this->id already exists in destination file!");
        $this->detachFile($dest);
        $this->attachFileTo($dest);
        $this->generateNewUrls($dest);
    }

    /**
     * @param Directory|null $dest
     */
    public function copyTo($dest)
    {
        $clone = $this->cloneFile();
        $clone->attachFileTo($dest);
        $clone->generateNewUrls($dest);
    }

    private function addToModifiedUrls($oldUrl, $newUrl)
    {
        $modifiedUrl = ModifiedUrl::where("object_id", $this->id)->first();
        if (is_null($modifiedUrl)) {
            $modifiedUrl = new ModifiedUrl();
            $modifiedUrl->object_id = $this->id;
        }
        $modifiedUrl->url_old = $oldUrl;
        $modifiedUrl->url_new = $newUrl;
        $modifiedUrl->save();
        return $modifiedUrl;
    }

    private function addToRobotsTxtRecords(ModifiedUrl $modifiedUrl)
    {
        $record = RobotTxtRecord::where("modified_url_id", $modifiedUrl->id)->first();
        if (is_null($record)) {
            $record = new RobotTxtRecord();
            $record->modified_url_id = $modifiedUrl->id;
        }
        $record->type = Type::AUTO;
        $record->user_agent = UserAgent::ALL;
        $record->permission = Permission::DISALLOW;
        $record->url = $modifiedUrl->url_old;
        $record->save();
        return $record;
    }
}
