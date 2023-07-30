<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/20/17
 * Time: 9:44 PM
 */

namespace App\Enums\Setting;


use App\Common\BaseEnum;

/**
 * TODO: system should add these settings to db as default settings.
 *
 * Class CMSSettingKey
 * @package App\Utils\CMS\Enums
 */
class CMSSettingKey extends BaseEnum
{
    const MINIMUM_PURCHASE = 'minimum_purchase';
    const TOLL_PERCENTAGE = 'toll_percentage';
    const TAX_PERCENTAGE = 'tax_percentage';
    const SHIPMENT_PRODUCT_CODE = 'shipment_product_code';
    const DISABLE_PRODUCT_ON_MIN = 'disable_product_on_min';
    const INQUIRY_CALL_NUMBER = 'inquiry_call_number';
    const CUSTOMER_CAN_EDIT_PROFILE = 'customer_can_edit_profile';
    const NEW_PRODUCT_DELAY_DAYS = 'new_product_delay_days';
}
