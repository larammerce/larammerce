<?php

namespace App\Console\Commands;

use App\Models\ShortLink;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;


class ShortLinkViewsStore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'short-links:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command stores short link views from redis to mysql';

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
        $short_links = ShortLink::all();
        //$redis  = Redis::connection();
        //$cache = Cache::get();
        //$views_count = $redis->get('short-link:views:'.$link_id);
        foreach ($short_links as $short_link){
            //$redis_key = 'short-link:views:'.$short_link->id;
            $cache_key = 'short-link:views:'.$short_link->id;
            //$cache_views = json_decode($redis->get($redis_key),true);
            $cache_views = Cache::get($cache_key);
            if ($cache_views != null){
                $short_link_stats = $short_link->statistics->first();
                $json_data = json_decode($short_link_stats->json_data, true);

                $today = Carbon::today();
                $current_month = $today->month;
                $current_year = $today->year;

                $redis_total_views = $cache_views['total_views'];
                $redis_countries = $cache_views['countries'];


                foreach ($redis_countries as $key => $value){
                    $json_data['daily'][$current_year.'-'.$current_month.'-'.$today->day][$key] = $value;
                    $daily_data_array = $json_data['daily'];

                    if (count(is_countable($daily_data_array)?$daily_data_array:[])>7){
                        $new_data_array = array_slice($daily_data_array,-7);

                        $json_data['daily'] = $new_data_array;
                    }

                    if (array_key_exists($current_year.'-'.$current_month, $json_data['monthly'])){
                        $json_data['monthly'][$current_year.'-'.$current_month][$key] += $value;
                    }
                    else{
                        $json_data['monthly'][$current_year.'-'.$current_month][$key] = $value;
                    }
                    if (array_key_exists($current_year, $json_data['yearly'])){
                        $json_data['yearly'][$current_year][$key] += $value;
                    }
                    else{
                        $json_data['yearly'][$current_year][$key] = $value;
                    }

                }

                $json_data = json_encode($json_data);
                $short_link_stats->views_count += $redis_total_views;
                $short_link_stats->json_data = $json_data;
                $short_link_stats->save();
                Cache::forget($cache_key);

            }
        }
    }
}
