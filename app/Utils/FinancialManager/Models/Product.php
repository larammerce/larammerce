<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/12/18
 * Time: 12:32 PM
 */

namespace App\Utils\FinancialManager\Models;


class Product extends BaseModel
{
    public $id;
    public $code;
    public $name;
    public $price;
    public $count;
}
