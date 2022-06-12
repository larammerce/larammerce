<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/12/18
 * Time: 12:19 PM
 */

namespace App\Utils\FinancialManager\Models;

class Customer extends BaseModel
{
    public $id;
    public $code;
    public $nickName;
    public $name;
    public $family;
    public $email;
    public $nationalCode;
    public $mobile;
    public $phone;
    public $birthday;
    public $gender;
    public $isLegal;
    public $economicalCode;
    public $nationalId;
    public $registrationCode;
    public $address;
    public $stateId;
    public $companyName;
}
