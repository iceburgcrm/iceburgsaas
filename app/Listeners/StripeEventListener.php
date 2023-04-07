<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class StripeEventListener
{
    /**
     * Handle received Stripe webhooks.
     *
     * @param  \Laravel\Cashier\Events\WebhookReceived  $event
     * @return void
     */
    public function handle(WebhookReceived $event)
    {
        Log::emergency('debug aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
        if ($event->payload['type'] === 'invoice.payment_succeeded') {
            // Handle the incoming event...
            print "sdsdsdsdsds";
            Log::emergency('test');
        }
    }
}
