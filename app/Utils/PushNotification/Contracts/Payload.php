<?php

namespace App\Utils\PushNotification\Contracts;

abstract class Payload
{
	/**
	 * IOS payload structure
	 * 
	 * @var array
	 */
    protected $apsPayload = [];
    
    /**
     * Android payload structure
     * 
     * @var array
     */
    protected $gcmPayload = [];
    
    /**
	 * Basic mandatory attributes for ios
     *
     * @var array
     */
    private $apsMandatoryFields = ['title', 'body'];
    
    /**
	 * Basic mandatory attributes for android
     *
     * @var array
     */
    private $gcmMandatoryFields = ['title', 'message'];
	
	/**
	 * Generate payload for ios plaform
	 * 
	 * @return array
	 */
	final public function getApsFormat()
	{
		$this->checkApsMandatoryFields();
		
		return ["aps" => $this->rawFilter($this->apsPayload)];
	}
	
	/**
	 * Generate payload for android plaform
	 * 
	 * @return array
	 */
	final public function getGcmFormat()
	{
		$this->checkGcmMandatoryFields();
		
		return $this->rawFilter($this->gcmPayload);
	}
	
	/**
	 * Send Payload to devices list
	 * 
	 * @param Collection $tok
	 * @param string $queue
	 * @return void
	 */
	protected function send($tokens, $queue = null)
	{
		\NotificationBridge::queue($this, $tokens, $queue);
	}
	
	/**
	 * Check if exists mandatory field to compose essential notification payload
	 * 
	 * @throws \Exception
	 * @return boolean
	 */
	public function checkApsMandatoryFields()
	{
		foreach ($this->apsMandatoryFields as $field){
			if(! array_key_exists($field, $this->apsPayload) )
				return false;
		}
		
		return true;
	}
	
	/**
	 * Check if exists mandatory field to compose essential notification payload
	 * 
	 * @throws \Exception
	 * @return boolean
	 */
	public function checkGcmMandatoryFields()
	{
		foreach ($this->gcmMandatoryFields as $field){
			if(! array_key_exists($field, $this->gcmPayload) )
				return false;
		}
		
		return true;
	}

    /**
     * Recursive methods to strip html tags from payload attributes
     *
     * @param array $arr
     * @return array
     * @internal param array $payload
     */
	public function rawFilter($arr)
	{
		foreach ($arr as $key => $value){
			if(is_array($value))
				$arr[$key] = $this->rawFilter($value);
			else
				$arr[$key] = strip_tags($value);
		}
		
		return $arr;
	}
}