<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/8/16
 * Time: 12:34 AM
 */

namespace App\Helpers;


use Exception;
use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    //TODO: create enum for these status codes.
    private static $errors = [
        200 => 'green',
        400 => 'orange',
    ];

    /**
     * @param string[] $messages
     * @param int $code
     * @param array $data
     * @param bool $json
     * @return array|string
     */
    public static function create($messages, $code, $data = [], $json = false)
    {
        $color = '';
        try {
            $color = self::$errors[$code];
        } catch (Exception $e) {
            $color = 'red';
        }
        $resultMessages = [];
        foreach ($messages as $message => $parameters) {
            if (gettype($parameters) === "array")
                $resultMessages[] = trans($message, $parameters);
            else
                $resultMessages[] = trans($parameters);
        }
        $response = [
            'transmission' => [
                'messages' => $resultMessages,
                'color' => $color,
                'code' => $code
            ],
            'data' => $data
        ];
        return $json ? json_encode($response) : $response;
    }

    /**
     * @param $messages
     * @param $code
     * @param array $data
     * @param bool $json
     * @return JsonResponse
     */
    public static function jsonResponse($messages, $code, $data = [], $json = false)
    {
        return response()->json(self::create($messages, $code, $data, $json), $code);
    }

    public static function createWithValidationMessages($messages, $code, $data = [], $json = false)
    {
        $resultArray = [];
        foreach ($messages as $key => $value) {
            $resultArray[] = $value[0];
        }
        return self::create($resultArray, $code, $data, $json);
    }
}