<?php
/**
 * Created by PhpStorm.
 * User: amirhosein
 * Date: 2/3/19
 * Time: 12:05 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class ModifiedUrl
 * @package App\Models
 * @property integer id
 * @property string url_old
 * @property string url_new
 * @property integer object_id
 * @property RobotTxtRecord robotTxtRecord
 */
class ModifiedUrl extends Model
{
    protected $table = "modified_urls";
    protected $fillable = [
        "object_id", "url_old", "url_new",
    ];
    public $timestamps = false;

    public function robotTxtRecord(): HasOne
    {
        return $this->hasOne(RobotTxtRecord::class, "modified_url_id", "id");
    }
}