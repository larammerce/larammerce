<?php

namespace App\Libraries\PushNotification\Services;

use App\Libraries\PushNotification\Contracts\Payload;

interface ServiceInterface
{	
	public function getPlatform();
	public function send(Payload $payload, $tokens);
}