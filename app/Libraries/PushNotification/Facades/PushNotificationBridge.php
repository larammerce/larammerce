<?php

namespace App\Libraries\PushNotification\Facades;

use Illuminate\Support\Facades\Facade;

class PushNotificationBridge extends Facade {

	protected static function getFacadeAccessor() { return 'bridge'; }

}