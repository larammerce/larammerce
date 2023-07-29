<?php

namespace App\Console\Commands;

use App\Features\CartNotification\CartNotificationConfig;
use App\Helpers\EmailHelper;
use App\Helpers\SMSHelper;
use App\Models\CartRow;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CartNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command notifies customers to check their abandoned carts,
     the job should be called in 1 hour time interval';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cart_notification_config = CartNotificationConfig::getRecord();
        if ($cart_notification_config->getIsActive()) {
            $delay_hours = $cart_notification_config->getDefaultDelayHours();
            $time_interval = 1;
            $un_notified_cart_rows = $this->getUnNotifiedCartRows($delay_hours, $time_interval);
            if ($un_notified_cart_rows != null and count($un_notified_cart_rows) > 0) {
                $tmp_customer_user_id = null;
                foreach ($un_notified_cart_rows as $cart_row) {
                    if ($tmp_customer_user_id != $cart_row->customer_user_id) {
                        $this->notifyCustomer($cart_row,
                            $cart_notification_config->getNotifyWithSMS(),
                            $cart_notification_config->getNotifyWithEmail());
                        $tmp_customer_user_id = $cart_row->customer_user_id;
                    }
                    $cart_row->update([
                        "customer_notified_at" => Carbon::now()
                    ]);
                }
            }
        }
        return 0;
    }

    /**
     * @param CartRow $cart_row
     * @param bool $with_sms
     * @param bool $with_email
     * @return bool
     */
    public function notifyCustomer(CartRow $cart_row, bool $with_sms, bool $with_email): bool
    {
        $is_notified = false;
        $customer = $cart_row->customer;
        if ($customer != null) {
            if ($with_sms) {
                $product_titles = $cart_row->product->title . " و ...";
                SMSHelper::send("sms-customer-cart", $customer->main_phone,
                    [],
                    [
                        "customerName" => $customer->user->name,
                        "productsJoinedTitles" => $product_titles,
                    ]);
                $is_notified = true;
            }
            if ($with_email) {
                $customer_full_name = $customer->user->full_name;
                EmailHelper::send([
                    "cartRows" => [$cart_row],
                    "customerFullName" => $customer_full_name,
                ],
                    "public.mail-customer-cart",
                    $customer->user->email,
                    $customer_full_name,
                    "اطلاع رسانی سبد خرید"
                );
                $is_notified = true;
            }
        }
        return $is_notified;
    }

    /**
     * @param int $time_interval
     * @param int $delay_hours
     * @return mixed
     */
    public function getUnNotifiedCartRows(int $delay_hours, int $time_interval)
    {
        return CartRow::where("created_at", "!=", null)->
        where("customer_notified_at", null)->get()->filter(function ($cart_row)
        use ($time_interval, $delay_hours) {
            return (Carbon::now()->diffInDays($cart_row->created_at) == 0 and
                Carbon::now()->diffInHours(Carbon::parse($cart_row->created_at)->addHours($delay_hours))
                < $time_interval / 2.0);
        })->sortBy("customer_user_id");
    }
}
