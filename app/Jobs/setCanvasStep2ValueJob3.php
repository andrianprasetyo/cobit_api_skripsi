<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helpers\CobitHelper;

class setCanvasStep2ValueJob3 implements ShouldQueue
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
        CobitHelper::setCanvasStep2Value($this->id);
    }
}
