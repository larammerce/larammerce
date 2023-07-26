<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/28/18
 * Time: 1:01 PM
 */

namespace App\Traits;

/**
 * Trait Taggable
 * @
 * @package App\Models\Traits
 */
trait Taggable
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = parent::toArray();
        $result['text'] = $this->getText();
        $result['value'] = (string)$this->getValue();
        return $result;
    }
}
