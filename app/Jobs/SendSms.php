<?php

namespace App\Jobs;

use App\Utils\SMSManager\Exceptions\SMSDriverInvalidConfigurationException;
use App\Utils\SMSManager\Factory as SmsFactory;
use App\Utils\SMSManager\Models\TextMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSms extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private TextMessage $text_message;

    /**
     * Create a new job instance.
     *
     * @param $template
     * @param $receiver_number
     * @param $data
     * @param $mixed_data
     */
    public function __construct($template, $receiver_number, $data, $mixed_data = [])
    {
        $this->text_message = new TextMessage($template, $receiver_number, $data, $mixed_data);
        $this->queue = config('queue.names.customer');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $sent = SmsFactory::driver()->sendSMS($this->text_message);
            if (!$sent)
                Log::error("message faced error : " . $this->text_message->receiver_number . " message: " .
                    $this->text_message->template . ":data:" . json_encode($this->text_message->data));
        }catch (SMSDriverInvalidConfigurationException $exception){
            Log::error("message faced error : Invalid driver configuration: " . $this->text_message->receiver_number . " message: " .
                $this->text_message->template . ":data:" . json_encode($this->text_message->data));
        }
    }
}
