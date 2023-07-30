<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 1/9/19
 * Time: 11:22 AM
 */

namespace App\Utils\CMS\RobotTxt;


use App\Common\BaseEnum;

class UserAgent extends BaseEnum
{
    const ALL = "*";
    const GOOGLE_BOT = "Googlebot";
    const GOOGLE_BOT_MOBILE = "Googlebot-Mobile";
    const GOOGLE_BOT_IMAGE = "Googlebot-Image";
    const GOOGLE_MEDIA_PARTNERS = "Mediapartners-Google";
    const GOOGLE_ADS_BOT = "Adsbot-Google";
    const SLURP = "Slurp";
    const MSN_BOT = "msnbot";
    const MSN_BOT_MEDIA = "msnbot-media";
    const TEOMA = "Teoma";
}
