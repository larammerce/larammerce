<?php

namespace App\Utils\PushNotification\Services\APNS;

class Feedback
{
	/**
	 * APNS Token
	 * 
	 * @var string
	 */
	protected $token;
	
	/**
	 * Time
	 * 
	 * @var int
	 */
	protected $time;
	
	/**
	 * Constructor
	 *
	 * @param  string   $rawResponse
	 * @return Feedback
	 */
	public function __construct($rawResponse = null)
	{
		if ($rawResponse !== null) {
			$this->parseRawResponse($rawResponse);
		}
	}
	
	/**
	 * Get Token
	 *
	 * @return string
	 */
	public function getToken()
	{
		return $this->token;
	}
	
	/**
	 * Set Token
	 *
	 * @return Feedback
	 */
	public function setToken($token)
	{
		if (!is_scalar($token)) {
			throw new \Exception('Token must be a scalar value');
		}
		
		$this->token = $token;
		
		return $this;
	}
	
	/**
	 * Get Time
	 *
	 * @return int
	 */
	public function getTime()
	{
		return $this->time;
	}
	
	/**
	 * Set Time
	 *
	 * @param  int      $time
	 * @return Feedback
	 */
	public function setTime($time)
	{
		$this->time = (int) $time;
		return $this;
	}
	
	/**
	 * Parse Raw Response
	 *
	 * @return Feedback
	 */
	public function parseRawResponse($rawResponse)
	{
		$rawResponse = trim($rawResponse);
		$token = unpack('Ntime/nlength/H*token', $rawResponse);
		
		$this->setTime($token['time']);
		$this->setToken(substr($token['token'], 0, $token['length'] * 2));
		
		return $this;
	}
}