<?php

namespace App\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class ImageResizer extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $mainImagePath;
    protected $destinationPath;
    protected $imageType;
    protected $imageWidth;
    protected $imageHeight;


    /**
     * Create a new job instance.
     *
     * @param $mainImagePath
     * @param $destinationPath
     * @param $imageType
     * @param $imageWidth
     * @param $imageHeight
     */
    public function __construct($mainImagePath, $destinationPath, $imageType, $imageWidth, $imageHeight)
    {
        $this->mainImagePath = $mainImagePath;
        $this->destinationPath = $destinationPath;
        $this->imageType = $imageType;
        $this->imageWidth = $imageWidth;
        $this->imageHeight = $imageHeight;
        $this->queue = config('queue.names.admin');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);

        if (file_exists($this->mainImagePath)) {
            $image = Image::make($this->mainImagePath);
            Log::info("image initialized \n");
            $image->resize($this->imageWidth, $this->imageHeight);
            if ($image->extension == 'jpg') {
                $image->encode('jpg', 80);
            }
            Log::info("saving file " . $this->imageType . " to [" . public_path() .
                "{$this->destinationPath}/{$image->filename}-{$this->imageType}.{$image->extension}" . "]\n");
            try {
                $image->save(
                    public_path() . "{$this->destinationPath}/{$image->filename}-{$this->imageType}.{$image->extension}");
            } catch (Exception $exception) {
                Log::error("image.resize.exception : " . $exception->getMessage());
            }
        } else {
            Log::error("The image '{$this->mainImagePath}' was not found!\n");
        }

        ini_set('memory_limit', '128M');
        ini_set('max_execution_time', 30);
    }
}
