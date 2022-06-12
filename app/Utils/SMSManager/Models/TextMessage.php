<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/16/18
 * Time: 8:52 PM
 */

namespace App\Utils\SMSManager\Models;


class TextMessage extends BaseModel
{
    public $receiver_number;
    public $template;
    public $data;
    public $mixed_data;

    /**
     * TextMessage constructor.
     * @param string $template
     * @param string $receiver_number
     * @param mixed $data
     * @param $mixed_data
     */
    public function __construct($template, $receiver_number, $data, $mixed_data = [])
    {
        $this->receiver_number = $receiver_number;
        $this->template = $template;
        $this->data = $data;
        $this->mixed_data = $mixed_data;
    }
}
