<?php

namespace App\Models;

use DateTime;

/**
 * @property integer id
 * @property integer web_form_id
 * @property integer|null user_id
 * @property string data
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * @property WebForm form
 *
 * Class WebFormMessage
 * @package App\Models
 */
class WebFormMessage extends BaseModel
{
    protected $table = 'web_form_messages';

    protected $fillable = [
        'web_form_id', 'data', 'user_id'
    ];


    /*
     * Relation Methods
     */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form()
    {
        return $this->belongsTo('\\App\\Models\\WebForm', 'web_form_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('\\App\\Models\\User', 'user_id');
    }


    /*
     * Accessor Methods
     */
    public function getDataObjAttribute()
    {
        return json_decode($this->data);
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }
}
