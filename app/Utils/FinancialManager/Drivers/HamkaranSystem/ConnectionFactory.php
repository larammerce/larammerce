<?php namespace App\Utils\FinancialManager\Drivers\HamkaranSystem;

use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Builder;
use Ixudra\Curl\Facades\Curl;
use phpseclib\Crypt\RSA;
use phpseclib\Math\BigInteger;

class ConnectionFactory
{
    private static function authenticate(Config $config, $renew = false): HamkaranAuthDataInterface
    {
        $auth_config = HamkaranAuthService::getRecord();
        if ($renew or $auth_config == null or $auth_config->getCreatedAt()->diffInMinutes(Carbon::now()) > 15) {
            $auth_config = self::readAuthConfig($config);
            if ($auth_config !== null) {
                $session_plus_password = $auth_config->getId() . "**" . $config->password;
                $rsa = new RSA();
                $rsa->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
                $rsa->loadKey([
                    "e" => new BigInteger($auth_config->getExponent(), 16),
                    "n" => new BigInteger($auth_config->getModulus(), 16)
                ]);
                $password = bin2hex($rsa->encrypt($session_plus_password));
                $request_data = [
                    "sessionId" => $auth_config->getId(),
                    "username" => $config->username,
                    "password" => $password
                ];
                $result = self::createBasic("/Services/Framework/AuthenticationService.svc/login", $config)
                    ->withData($request_data)->withResponseHeaders()->returnResponseObject()->post();

                if (isset($result->headers) and isset($result->headers["Set-Cookie"])) {
                    $auth_config->setCookies($result->headers["Set-Cookie"]);
                    try {
                        HamkaranAuthService::setRecord($auth_config);
                    } catch (NotValidSettingRecordException $e) {
                        Log::error("HamkaranSystem.ConnectionFactory.authenticate.setRecord." . $e->getMessage());
                    }
                }
            }
        }
        return $auth_config;
    }

    private static function readAuthConfig(Config $config): ?HamkaranAuthDataInterface
    {
        $result = self::createBasic("/Services/Framework/AuthenticationService.svc/session", $config)->get();
        if (isset($result->id) and isset($result->rsa) and isset($result->rsa->M) and isset($result->rsa->E)) {
            $auth_config = new HamkaranAuthDataInterface($result->id, $result->rsa->M, $result->rsa->E);
            try {
                HamkaranAuthService::setRecord($auth_config);
            } catch (NotValidSettingRecordException $e) {
                return null;
            }
            return $auth_config;
        }
        Log::error("fin_manager.hamkaran.connection_factory.read_auth_config." . json_encode($result));
        return null;
    }

    private static function createBasic(string $address, Config $config): Builder
    {
        $address = strpos($address, "/") == 0 ? $address : "/" . $address;
        $address = "https://" . $config->host . ":" .
            $config->port . "/" . $config->prefix . $address;
        return Curl::to($address)
            ->withHeader("content-type: application/json")
            ->asJson();
    }

    public static function create(string $address, Config $config, bool $renew = false): Builder
    {
        $auth_config = self::authenticate($config, $renew);
        $cookies = join(",", $auth_config->getCookies());
        return self::createBasic($address, $config)->enableDebug(storage_path("logs/hamkaran_curl.log"))
            ->withHeader("Set-Cookie: ${cookies}");
    }
}
