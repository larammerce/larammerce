<?php

namespace App\Jobs;

use App\Models\Directory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ActionDirectoryChildrenBadges extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;
    protected Directory $directory;
    protected int $badge_id;
    protected int $action;

    const ATTACH=0;
    const DETACH=1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($directory, $badge_id, $action)
    {
        $this->directory = $directory;
        $this->badge_id = $badge_id;
        $this->action = $action;
        $this->queue = config('queue.names.admin');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->directory->content_type== \App\Enums\Directory\DirectoryType::PRODUCT){
            if ($this->action == self::ATTACH){
                $this->directory->leafProducts()->chunk(100, function ($products) {
                    foreach ($products as $product) {
                        $product->badges()->attach($this->badge_id);
                    }});
            }
            elseif($this->action == self::DETACH){
                $this->directory->leafProducts()->chunk(100, function ($products) {
                    foreach ($products as $product) {
                        $product->badges()->detach($this->badge_id);
                    }});
            }
        }
        elseif ($this->directory->content_type== \App\Enums\Directory\DirectoryType::BLOG){
            if ($this->action == self::ATTACH){
                $this->directory->articles()->chunk(100, function ($articles) {
                    foreach ($articles as $article) {
                        $article->badges()->attach($this->badge_id);
                    }});
            }
            elseif($this->action == self::DETACH){
                $this->directory->articles()->chunk(100, function ($articles) {
                    foreach ($articles as $article) {
                        $article->badges()->detach($this->badge_id);
                    }});
            }
        }

    }
}
