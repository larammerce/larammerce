<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 1/9/19
 * Time: 11:29 AM
 */

namespace App\Models;


use App\Utils\CMS\RobotTxt\Permission;
use App\Utils\CMS\RobotTxt\UserAgent;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer id
 * @property integer type
 * @property integer modified_url_id
 * @property string user_agent
 * @property string permission
 * @property string url
 */
class RobotTxtRecord extends Model
{
    protected $table = "robot_txt_records";
    protected $fillable = [
        "type", "user_agent", "permission", "url", "modified_url_id"
    ];
    public $timestamps = false;


    public function modifiedUrl(): BelongsTo
    {
        return $this->belongsTo(ModifiedUrl::class, "modified_url_id", "id");
    }

    /**
     * @throws Exception
     */
    public static function withLine(string $line): RobotTxtRecord
    {
        $pattern = "/^(.*)\|(.*)\:(.*)$/";
        $matches = [];
        preg_match($pattern, $line, $matches);

        if (count(is_countable($matches)?$matches :[]) != 4)
            throw new Exception("Passed line does not satisfy needed data.");

        list(, $userAgent, $permission, $url) = $matches;

        if (!in_array($userAgent, UserAgent::values()))
            throw new Exception("Passed user agent is not supported.");

        $newRecord = new RobotTxtRecord();
        $newRecord->user_agent = $userAgent;

        if (!in_array($permission, Permission::values()))
            throw new Exception("Passed permission is not valid.");

        $newRecord->permission = $permission;
        $newRecord->url = $url;

        return $newRecord;
    }

    public function getLine(): string
    {
        return "{$this->user_agent}|{$this->permission}:{$this->url}";
    }

}
