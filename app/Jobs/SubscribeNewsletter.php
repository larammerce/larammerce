<?php

namespace App\Jobs;

use App\Utils\NewsletterManager\Factory as NewsletterFactory;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SubscribeNewsletter extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $data;

    /**
     * Create a new job instance.
     *
     * @param $subscriber
     */
    public function __construct($subscriber)
    {
        $this->data = $subscriber;
        $this->queue = config('queue.names.customer');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        NewsletterFactory::driver()->addSubscriber($this->data);
    }
}
