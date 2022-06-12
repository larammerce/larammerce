<?php

namespace App\Utils\PushNotification\Services\APNS;

class FeedbackService extends AbstractClient
{
	/**
	 * override $uri with APNS feedback service URI
	 * 
	 * @var string
	 */
	protected $uri = 'ssl://feedback.push.apple.com:2196';

	/**
	 * Get Feedback
	 *
	 * @return array
	 */
	public function feedback()
	{
		$this->connect();
		
		/*
		 * Read from socket
		 */
		$tokens = [];
		while ($token = $this->read(38)) {
			$tokens[] = new Feedback($token);
		}
		
		return $tokens;
	}
}