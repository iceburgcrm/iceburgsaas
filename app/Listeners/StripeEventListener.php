<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class StripeEventListener
{
    /**
     * Handle received Stripe webhooks.
     *
     * @return void
     */
    public function handle(WebhookReceived $event)
    {
        Log::emergency('debug aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
        if ($event->payload['type'] === 'invoice.payment_succeeded') {
            // Handle the incoming event...
            echo 'sdsdsdsdsds';
            Log::emergency('test');
        }
    }
}
