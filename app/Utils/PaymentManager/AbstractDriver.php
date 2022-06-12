<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/10/19
 * Time: 9:28 AM
 */

namespace App\Utils\PaymentManager;

use App\Utils\PaymentManager\Exceptions\PaymentDriverNotConfiguredException;
use Illuminate\Contracts\Routing\UrlGenerator;

/**
 * Class AbstractDriver
 * @property string|null logo
 * @property string|null name
 * @property string|null id
 * @package App\Utils\PaymentManager
 */
abstract class AbstractDriver implements BaseDriver
{
    const CALLBACK_URL_PREFIX = "/payment-callback";

    public final function getCallbackUrl():UrlGenerator|string
    {
        return url(self::getCallbackUri());
    }

    public final function getCallbackUri(): string{
        return self::CALLBACK_URL_PREFIX . "/" . $this->getId();
    }

    public final function getName(): string
    {
        return "payment.drivers." . $this->getId();
    }

    public final function getLogo(): string
    {
        try {
            $config = ConfigProvider::getConfig($this->getId());
            return $config->getLogoPath();
        } catch (PaymentDriverNotConfiguredException $e) {
            return "";
        }
    }

    public function __get($name)
    {
        return match ($name) {
            'id' => $this->getId(),
            'name' => $this->getName(),
            'logo' => $this->getLogo(),
            default => null,
        };
    }
}
