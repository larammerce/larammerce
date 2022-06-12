<?php

namespace App\Utils\PushNotification;

trait TokenTrait
{
	/**
	 * Column name into DB table to store device information
	 * 
	 * @var array
	 */
	protected $columnName = [
			"platform" => "platform",
			"device_token" => "device_token",
	];
	
	/* public $platform;
	public $deviceId; */
	
	public function _getTokenArray()
	{
		$platform = $this->columnName['platform'];
		$token = $this->columnName['device_token'];
		
		return [
				'platform' => $this->$platform,
				'device_token' => $this->$token,
		];
	}
	
	/**
	 * Call trait methods in static way
	 * 
	 * @param $method
	 * @param $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		$instance = new static();
		$overload_method = '_'.$method;
		if (method_exists($instance, $overload_method)) {
			return call_user_func_array([$instance, $overload_method], $parameters);
		}
		return parent::__callStatic($method, $parameters);
	}
	
    /**
     * Catch static calls call from within a class. Example : static::method();
     * 
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $overload_method = '_'.$method;
        if (method_exists($this, $overload_method)) {
            return call_user_func_array([$this, $overload_method], $parameters);
        }
        return parent::__call($method, $parameters);
    }
}