<?php

namespace App\Libraries\PushNotification\Services\APNS;

use App\Libraries\PushNotification\Contracts\Payload;
use App\Libraries\PushNotification\Services\ServiceInterface;
use App\Utils\PushNotification\Services\APNS\InvalidArgumentException;

class ApnService extends AbstractClient implements ServiceInterface
{
	/**
	 * Name of platform
	 * 
	 * @var string
	 */
	protected $platform = 'ios';
	
	/**
	 * APN service URI
	 * 
	 * @var string
	 */
	protected $uri = 'ssl://gateway.push.apple.com:2195';

	/**
	 * Send notification to devices tokens
	 *
	 * @param \App\Libraries\PushNotification\Contracts\Payload $payload
     * @param array $tokens
	 *@throws InvalidArgumentException
	 */
	public function send(Payload $payload, $tokens)
	{
    	if(!count($tokens)>0)
    		return true;

		//Open connection
		$this->connect();

		// Encode payload as JSON
		$json_payload = json_encode($payload->getApsFormat());
		
		// Build the binary notification to each token
		$data = '';
		foreach($tokens as $tk){
			$data .= chr(0) . pack('n', 32) . pack('H*', str_replace(' ', '', $tk)) . pack('n', strlen($json_payload)) . $json_payload;
		}
		
		// Send data to the server
		return $this->write($data);
	}
	
	/**
	 * Accessor for platform name
	 * 
	 * @return string
	 */
	public function getPlatform() 
	{
		return $this->platform;
	}
}