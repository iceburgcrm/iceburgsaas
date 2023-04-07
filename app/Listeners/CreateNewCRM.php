<?php

namespace App\Listeners;

use App\Events\NewCrm;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateNewCRM
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\NewCrm  $event
     * @return void
     */
    public function handle(NewCrm $event)
    {
        $crm=new CrmCreator();
        $status=$crm->create($event->data);
    }
}
