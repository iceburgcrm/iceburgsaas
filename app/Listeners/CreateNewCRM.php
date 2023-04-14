<?php

namespace App\Listeners;

use App\Events\NewCrm;

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
     * @return void
     */
    public function handle(NewCrm $event)
    {
        $crm = new CrmCreator();
        $status = $crm->create($event->data);
    }
}
