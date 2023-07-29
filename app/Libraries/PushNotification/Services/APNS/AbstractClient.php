<?php

namespace App\Libraries\PushNotification\Services\APNS;

use App\Utils\PushNotification\Services\APNS\Exception;
use Config;

class AbstractClient
{
	/**
	 * Default APN service URI
	 * 
	 * @var string
	 */
	protected $uri = 'ssl://gateway.push.apple.com:2195';

	/**
	 * Is Connected
	 * 
	 * @var boolean
	 */
	protected $isConnected = false;
	
	/**
	 * Stream Socket
	 * 
	 * @var Resource
	 */
	protected $socket;
	
	/**
	 * Connection with apn service
	 * 
	 * @throws Exception
	 * @return \App\PushNotification\Driver\APNS\AbstractClient
	 */
	protected function connect() 
	{
		/*
		 * Socket content creation
		 */
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', config('pushnotification.aps.certificate'));
		stream_context_set_option($ctx, 'ssl', 'passphrase', config('pushnotification.aps.passPhrase'));
		
		/*
		 * Open connection with apns server
		 */
		$this->socket = stream_socket_client(
			$this->uri, 
			$err,
			$errstr, 
			60, 
			STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, 
			$ctx
		);
        
        if (!$this->socket) {
            throw new \Exception('Unable to connect with APN service: '.$this->uri.' - '.$err);
        }
        
        /*
         * Sign it how to connected
         */
        $this->isConnected = true;
        
        return $this;
	}

	/**
	 * Close Connection
	 *
	 * @return AbstractClient
	 */
	public function close()
	{
		if ($this->isConnected && is_resource($this->socket)) {
			fclose($this->socket);
		}
		
		$this->isConnected = false;
		
		return $this;
	}

	/**
	 * Read data sent from the Server
	 *
	 * @param int $length
	 * @return string
	 */
	protected function read($length = 6)
	{
		if (!$this->isConnected) {
			throw new \Exception('You must open the connection prior to reading data for APNS server');
		}
		
		$data = false;
		$read = array($this->socket);
		$null = null;
		
		if (0 < @stream_select($read, $null, $null, 1, 0)) {
			$data = @fread($this->socket, (int) $length);
		}
		
		return $data;
	}
	
	/**
	 * Write Payload to the Server
	 *
	 * @param  string $payload
	 * @return int
	 */
	protected function write($payload)
	{
		if (!$this->isConnected) {
			throw new \Exception('You must open the connection prior to writing data for APNS server');
		}
		
		return fwrite($this->socket, $payload, strlen($payload));
	}
	
    /**
     * Is Connected
     *
     * @return boolean
     */
    public function isConnected()
    {
        return $this->isConnected;
    }

	/**
	 * Destructor
	 *
	 * @return void
	 */
	public function __destruct()
	{
		$this->close();
	}
}