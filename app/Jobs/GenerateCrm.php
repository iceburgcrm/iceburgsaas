<?php

namespace App\Jobs;

use App\Crm\CrmCreator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateCrm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $crm;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CrmCreator $crm, $user)
    {
        $this->crm = $crm;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->crm->create();
    }
}
