<?php

namespace App\Utils\PushNotification\Services;

use App\Utils\PushNotification\Contracts\Payload;

interface ServiceInterface
{	
	public function getPlatform();
	public function send(Payload $payload, $tokens);
}