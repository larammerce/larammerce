<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/12/18
 * Time: 12:09 PM
 */

namespace App\Utils\FinancialManager;


use App\Models\Invoice;
use App\Models\User;
use App\Utils\FinancialManager\Models\BaseFinancialConfig;
use App\Utils\FinancialManager\Models\Customer;
use App\Utils\FinancialManager\Models\Product;

interface BaseDriver
{
    public function getId(): string;

    public function getDefaultConfig(): BaseFinancialConfig;

    /**
     * @return Customer[]
     */
    public function getAllCustomers();

    /**
     * @param string $phone_number
     * @return Customer|boolean
     */
    public function getCustomerByPhone($phone_number);

    /**
     * @param $relation
     * @return Customer|boolean
     */
    public function getCustomerByRelation($relation);

    /**
     * @param User $user
     * @param boolean $is_legal
     * @return string|boolean
     */
    public function addCustomer($user, $is_legal);

    /**
     * @param User $user
     * @param boolean $is_legal
     * @param array $user_config
     * @return boolean
     */
    public function editCustomer($user, $is_legal, $user_config = []);

    /**
     * @return Product[]
     */
    public function getAllProducts();

    /**
     * @param string $code
     * @return Product|boolean
     */
    public function getProduct($code);

    /**
     * @param string $code
     * @return integer|boolean
     */
    public function getProductCount($code);

    /**
     * @param Invoice $invoice
     * @return string|boolean
     */
    public function addPreInvoice($invoice);

    /**
     * @param string $fin_relation
     * @return boolean
     */
    public function deletePreInvoice($fin_relation);

    /**
     * @param string $fin_relation
     * @return string|boolean
     */
    public function submitWarehousePermission($fin_relation);

    /**
     * @param string $warehouse_permission_data
     * @return boolean
     */
    public function checkExitTab($warehouse_permission_data);

    /**
     * @param integer $standard_price
     * @return integer
     */
    public function convertPrice($standard_price);
}
