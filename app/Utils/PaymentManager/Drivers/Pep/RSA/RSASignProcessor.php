<?php

namespace App\Utils\PaymentManager\Drivers\Pep\RSA;

use App\Utils\PaymentManager\Exceptions\PaymentInvalidParametersException;

class RSASignProcessor
{
    private $public_key = null;
    private $private_key = null;
    private $modulus = null;
    private $key_length = "1024";

    /**
     * RSASignProcessor constructor.
     * @param $xml_rsa_key
     * @param null $type
     * @throws PaymentInvalidParametersException
     */
    public function __construct($xml_rsa_key, $type = null)
    {
        try {
            $xml_obj = null;
            if ($type == RSAKeyType::XMLFile) {
                $xml_obj = simplexml_load_file($xml_rsa_key);
            } else {
                $xml_obj = simplexml_load_string($xml_rsa_key);
            }
            $this->modulus = RSA::binary_to_number(base64_decode($xml_obj->Modulus));
            $this->public_key = RSA::binary_to_number(base64_decode($xml_obj->Exponent));
            $this->private_key = RSA::binary_to_number(base64_decode($xml_obj->D));
            $this->key_length = strlen(base64_decode($xml_obj->Modulus)) * 8;
        } catch (\Throwable $exception) {
            throw new PaymentInvalidParametersException($exception->getMessage());
        }
    }

    public function getPublicKey()
    {
        return $this->public_key;
    }

    public function getPrivateKey()
    {
        return $this->private_key;
    }

    public function getKeyLength()
    {
        return $this->key_length;
    }

    public function getModulus()
    {
        return $this->modulus;
    }

    public function encrypt($data)
    {
        return base64_encode(RSA::rsa_encrypt($data, $this->public_key, $this->modulus, $this->key_length));
    }

    public function decrypt($data)
    {
        return RSA::rsa_decrypt($data, $this->private_key, $this->modulus, $this->key_length);
    }

    public function sign($data)
    {
        return RSA::rsa_sign($data, $this->private_key, $this->modulus, $this->key_length);
    }

    public function verify($data)
    {
        return RSA::rsa_verify($data, $this->public_key, $this->modulus, $this->key_length);
    }
}

