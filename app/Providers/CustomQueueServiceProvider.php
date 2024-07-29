<?php

namespace App\Providers;

use App\Common\CustomListener;
use App\Services\Queue\QueueService;
use Illuminate\Queue\QueueServiceProvider;

class CustomQueueServiceProvider extends QueueServiceProvider
{
    public function register()
    {
        parent::configureSerializableClosureUses();

        parent::registerManager();
        parent::registerConnection();
        parent::registerWorker();
        $this->registerListener();
        parent::registerFailedJobServices();
    }

    protected function registerListener()
    {
        $this->app->singleton('queue.listener', function ($app) {
            return new CustomListener($app->basePath(), $app->make(QueueService::class));
        });
    }
}
