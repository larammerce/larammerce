<?php

namespace App\Libraries\PushNotification;

use App\Libraries\PushNotification\Services\APNS\FeedbackService;
use Illuminate\Support\ServiceProvider;

class PushNotificationProvider extends ServiceProvider
{
    /**
     * Bootstrap the PushNotification services.
     *
     * @return void
     */
    public function boot()
    {
    	$this->publishes([
        	__DIR__.'/config/pushnotification.php' => config_path('pushnotification.php'),
    	], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    	/*
    	 * To retrieve configuration width "dot notation" Es: "pushnotification.ios.xxx"
    	 */
    	$this->mergeConfigFrom( __DIR__.'/config/pushnotification.php', 'pushnotification');
    	
    	$this->app['bridge'] = $this->app->share(function($app) {
    		return new PushNotificationBridge($app);
    	});
    	
    	$this->app['feedback'] = $this->app->share(function() {
    		return new FeedbackService();
    	});
    }
}
