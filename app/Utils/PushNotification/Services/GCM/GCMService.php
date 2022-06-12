<?php

namespace App\Utils\PushNotification\Services\GCM;

use App\Utils\PushNotification\Services\ServiceInterface;
use App\Utils\PushNotification\Contracts\Payload;

class GCMService implements ServiceInterface
{
	/**
	 * Name of platform
	 * 
	 * @var string
	 */
	protected $platform = ['android', 'web'];
	
	/**
	 * GCM server endpoint
	 * 
	 * @var string
	 */
	protected $uri = 'https://android.googleapis.com/gcm/send';

    /**
     * Send notification to devices tokens
     *
     *
     * @param Payload|AbstractPayload $payload
     * @param array $tokens
     * @return bool|mixed
     * @throws \Exception
     */
	public function send(Payload $payload, $tokens)
	{
		if(!is_array($tokens)){
			throw new \InvalidArgumentException('Tokens must be an array');
		}
		
    	if(!count($tokens)>0){
    		return true;
    	}
    	
		$gcm_message = [
				"registration_ids" => $tokens,
                "notification" => $payload->getGcmFormat()
		];
		
		$headers = [
				"Authorization: key=".config('pushnotification.gcm.apiKey'),
				"Content-Type: application/json"
		];
		
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL, $this->uri);
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($gcm_message));
		$result=curl_exec($ch);

        echo $result;
		if($result === false){
			throw new \Exception("Curl failed: ".curl_error($ch));
		}
		
		curl_close($ch);
		
		return $result;
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
?>