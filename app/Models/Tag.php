<?php

namespace App\Models;

use App\Interfaces\TagInterface as TaggableContract;
use App\Traits\Taggable;
use App\Utils\Translation\Traits\Translatable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property integer id
 * @property string name
 *
 * @property Article[] articles
 * @property Product[] products
 * @property WebPage[] webPages
 *
 * Class Tag
 * @package App\Models
 */
class Tag extends BaseModel implements TaggableContract
{
    use Taggable, Translatable;

    protected $table = 'tags';

    protected $fillable = [
        'name'
    ];

    public $timestamps = false;

    protected static array $SORTABLE_FIELDS = ['id', 'name'];

    protected static array $SEARCHABLE_FIELDS = ['name'];

    protected static array $TRANSLATABLE_FIELDS = [
        'name' => ['string', 'input:text']
    ];
    /*
     * Relation Methods
     */

    /**
     * @return MorphToMany
     */
    public function articles(): MorphToMany
    {
        return $this->morphedByMany(Article::class, 'taggable');
    }

    /**
     * @return MorphToMany
     */
    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'taggable');
    }

    /**
     * @return MorphToMany
     */
    public function webPages(): MorphToMany
    {
        return $this->morphedByMany(WebPage::class, 'taggable');
    }

    public function getText(): string
    {
        return $this->name;
    }

    public function getValue(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }
}
