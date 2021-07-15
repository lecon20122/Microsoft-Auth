<?php

namespace App\Jobs;

use App\Models\Assets;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreJsonFile implements ShouldQueue
{
    use Batchable , Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $chunkedData;
    protected $company_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chunkedData)
    {
        $this->chunkedData = $chunkedData;


    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        foreach ($this->chunkedData as $key => $value) {
            Assets::create([
                'name' => $this->chunkedData[$key]->name,
                'asset_type' => $this->chunkedData[$key]->asset_type,
                'resource' => $this->chunkedData[$key]->resource,
                'ancestors' => $this->chunkedData[$key]->ancestors,
                'update_time' => $this->chunkedData[$key]->update_time,
            ]);
        }
    }
}
