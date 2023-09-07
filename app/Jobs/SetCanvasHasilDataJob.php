<?php

namespace App\Jobs;

use App\Helpers\CobitHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SetCanvasHasilDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id;
    /**
     * Create a new job instance.
     */
    public function __construct($id)
    {
        $this->id=$id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        CobitHelper::setAssesmentHasilAvg($this->id);
        CobitHelper::assesmentDfWeight($this->id);
        CobitHelper::setCanvasStep2Value($this->id);
        CobitHelper::setCanvasStep3Value($this->id);
        CobitHelper::updateCanvasAdjust($this->id);
        CobitHelper::generateTargetLevelDomain($this->id);
    }
}
