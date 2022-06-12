<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 12/19/17
 * Time: 6:57 PM
 */

namespace App\Utils\CMS\Template\WebForm;


use Illuminate\Http\Request;
use JsonSerializable;
use Serializable;
abstract class FormField implements JsonSerializable, Serializable
{

    protected $name;
    protected $title;
    protected $rules;

    public function __construct(array $attributes = [])
    {
        if (key_exists('name', $attributes))
            $this->name = $attributes['name'];
        if (key_exists('title', $attributes))
            $this->title = $attributes['title'];
        if (key_exists('rules', $attributes))
            $this->rules = $attributes['rules'];
    }

    abstract public function getValidationRules();
    abstract public function getIdentifier();
    abstract public function getDisplayTitle();
    abstract public function getValue(Request $request);
    abstract public static function getTag();
}