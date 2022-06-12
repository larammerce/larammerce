<?php

namespace App\Models;


/**
 * @property integer id
 * @property string name
 *
 * @property Article[] articles
 * @property Product[] products
 * @property WebPage[] webPages
 * @property bool needs_review
 * @property integer edit_count
 *
 * Class Tag
 * @package App\Models
 */
class Review extends BaseModel
{
    protected $table = 'reviewables';

    protected $fillable = [
        'reviewable_id', 'reviewable_type', 'edit_count', 'needs_review'
    ];

    public $timestamps = true;

    protected static array $SORTABLE_FIELDS = ['id', 'needs_review', 'edit_count', 'reviewable_type', 'reviewable_id',
        'updated_at'];

    protected static array $SEARCHABLE_FIELDS = [];


    /*
     * Relation Methods
     */

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function setAsChecked()
    {
        $this->needs_review = false;
        $this->save();
    }

    public function increaseEditCount()
    {
        $this->edit_count++;
        $this->needs_review = true;
        $this->save();
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }
}
