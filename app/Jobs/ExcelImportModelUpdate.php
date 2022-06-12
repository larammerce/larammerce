<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExcelImportModelUpdate extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;
    protected string $model_name;
    protected array $valid_rows;
    protected array $importable_attributes;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $model_name, array $valid_rows, array $IMPORTABLE_ATTRIBUTES)
    {
        $this->model_name = $model_name;
        $this->valid_rows = $valid_rows;
        $this->importable_attributes = $IMPORTABLE_ATTRIBUTES;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->valid_rows as $row){
            $updating_values = array_combine($this->importable_attributes, $row);
            $this->model_name::where($this->importable_attributes[0], $row[0])->update($updating_values);
        }
    }
}
