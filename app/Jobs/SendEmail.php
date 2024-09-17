<?php

namespace App\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $data;
    private $template;
    private $email;
    private $name;
    private $subject;


    /**
     * Create a new job instance.
     * @param $data
     * @param $template
     * @param $email
     * @param $name
     * @param $subject
     */
    public function __construct($data, $template, $email, $name, $subject)
    {
        $this->data = $data;
        $this->template = $template;
        $this->email = $email;
        $this->name = $name;
        $this->subject = $subject;
        $this->data["email"] = $email;
        $this->queue = config('queue.names.customer');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = $this->email;
        $name = $this->name;
        $subject = $this->subject;

        if ($email != null) {
            try {
                Mail::send($this->template, $this->data,
                    function ($m) use ($email, $name, $subject) {
                        $m->from(env("MAIL_FROM_ADDRESS"), env('MAIL_FROM_NAME')." Support");
                        $m->to($email, $name)->subject($subject);
                    }
                );
            } catch (Exception $exception) {
                Log::error("mail.send.exception : " . $exception->getMessage());
            }
        }
    }

    /**
     * @return int|null
     */
    public function getDispatchType(): ?int
    {
        return null;
    }

    /**
     * @return int|null
     */
    public function getQueuePriority(): ?int
    {
        return null;
    }
}
