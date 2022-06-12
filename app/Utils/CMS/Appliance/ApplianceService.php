<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/5/17
 * Time: 7:21 PM
 */

namespace App\Utils\CMS\Appliance;

use Exception;

/**
 * Class ApplianceService
 * @package App\Utils\CMS\Appliance
 */
class ApplianceService
{
    /**
     * @var string
     */
    private static $applianceSource = 'cms.appliances';
    /**
     * @var string
     */
    private static $settingApplianceKey = 'setting_appliances';
    /**
     * @var string
     */
    private static $exploreApplianceKey = 'explore_appliances';
    /**
     * @var string
     */
    private static $shopApplianceKey = 'shop_appliances';
    /**
     * @var string
     */
    private static $analyticApplianceKey = 'analytic_appliances';
    /**
     * @var string
     */
    private static $showToolbarKey = 'show_in_toolbar';
    /**
     * @var ApplianceModel[]
     */
    private static $allAppliances = [];
    /**
     * @var ApplianceModel[]
     */
    private static $toolbarAppliances = [];
    /**
     * @var ApplianceModel[]
     */
    private static $settingAppliances = [];
    /**
     * @var ApplianceModel[]
     */
    private static $exploreAppliances = [];
    /**
     * @var ApplianceModel[]
     */
    private static $analyticAppliances = [];
    /**
     * @var ApplianceModel[]
     */
    private static $shopAppliances = [];

    public static function init()
    {
        try {
            $disabled_appliances = get_disabled_setting_appliances();
            $appliancesFlat = config(self::$applianceSource);
            foreach ($appliancesFlat as $appliance) {
                if (isset($appliance["properties"]) and isset($appliance["properties"]["id"]) and
                    in_array($appliance["properties"]["id"], $disabled_appliances))
                    continue;

                //inserting allowed appliances of the project
                $applianceObj = new ApplianceModel($appliance);
                array_push(self::$allAppliances, $applianceObj);
                if ($appliance[self::$showToolbarKey])
                    array_push(self::$toolbarAppliances, $applianceObj);

                //inserting setting appliances
                if (key_exists(self::$settingApplianceKey, $appliance)) {
                    foreach ($appliance[self::$settingApplianceKey] as $setting) {
                        if (isset($setting["properties"]) and isset($setting["properties"]["id"]) and
                            in_array($setting["properties"]["id"], $disabled_appliances))
                            continue;
                        $settingAppliance = new ApplianceModel($setting);
                        array_push(self::$settingAppliances, $settingAppliance);
                    }
                }

                //inserting explore appliances
                if (key_exists(self::$exploreApplianceKey, $appliance)) {
                    foreach ($appliance[self::$exploreApplianceKey] as $explore) {
                        if (isset($explore["properties"]) and isset($explore["properties"]["id"]) and
                            in_array($explore["properties"]["id"], $disabled_appliances))
                            continue;
                        $exploreAppliance = new ApplianceModel($explore);
                        array_push(self::$exploreAppliances, $exploreAppliance);
                    }
                }

                //inserting shop appliances
                if (key_exists(self::$shopApplianceKey, $appliance)) {
                    foreach ($appliance[self::$shopApplianceKey] as $shop) {
                        if (isset($shop["properties"]) and isset($shop["properties"]["id"]) and
                            in_array($shop["properties"]["id"], $disabled_appliances))
                            continue;
                        $shopAppliance = new ApplianceModel($shop);
                        array_push(self::$shopAppliances, $shopAppliance);
                    }
                }

                //inserting analytic appliances
                if (key_exists(self::$analyticApplianceKey, $appliance)) {
                    foreach ($appliance[self::$analyticApplianceKey] as $analytic) {
                        if (isset($analytic["properties"]) and isset($analytic["properties"]["id"]) and
                            in_array($analytic["properties"]["id"], $disabled_appliances))
                            continue;
                        $analyticAppliance = new ApplianceModel($analytic);
                        array_push(self::$analyticAppliances, $analyticAppliance);
                    }
                }

            }
        } catch (Exception $e) {
        }
    }

    /**
     * @return ApplianceModel[]
     */
    public static function getSettingAppliances()
    {
        return self::$settingAppliances;
    }

    /**
     * @return ApplianceModel[]
     */
    public static function getExploreAppliances()
    {
        return self::$exploreAppliances;
    }

    /**
     * @return ApplianceModel[]
     */
    public static function getShopAppliances()
    {
        return self::$shopAppliances;
    }

    /**
     * @return ApplianceModel[]
     */
    public static function getAnalyticAppliances()
    {
        return self::$analyticAppliances;
    }

    /**
     * @return ApplianceModel[]
     */
    public static function getAllAppliances()
    {
        return self::$allAppliances;
    }

    /**
     * @return ApplianceModel[]
     */
    public static function getToolbarAppliances()
    {
        return self::$toolbarAppliances;
    }
}