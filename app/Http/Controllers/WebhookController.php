<?php


namespace App\Http\Controllers;

use App\Models\CrmPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookHandled;
use Laravel\Cashier\Events\WebhookReceived;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Laravel\Cashier\Payment;
use Laravel\Cashier\Subscription;
use Stripe\Customer as StripeCustomer;
use Stripe\Subscription as StripeSubscription;

class WebhookController extends CashierController2
{
    public function handleWebhook(Request $request)
    {
        return response()
            ->json([
                'code'      =>  500,
                'message'   =>  'custom error'
            ], 500);
        $payload = json_decode($request->getContent(), true);
        $method = 'handle'.Str::studly(str_replace('.', '_', $payload['type']));

        WebhookReceived::dispatch($payload);

        if (method_exists($this, $method)) {
            $this->setMaxNetworkRetries();

            $response = $this->{$method}($payload);

            WebhookHandled::dispatch($payload);

            return $response;
        }

        return $this->missingMethod($payload);
    }
    public function handleCheckoutSessionCompleted(array $payload)
    {
        return response()
            ->json([
                'code'      =>  500,
                'message'   =>  'custom error'
            ], 500);
        $data = $payload['data']['object'];
        $user = User::findOrFail($data['client_reference_id']);

            throw new \Exception();
        print "in here2";
        if(isset($data['lines']['data'][0]['plan']['id']))
        {
            print "in here";
            $plan=CrmPlan::where('stripe_id', $data['lines']['data'][0]['plan']['id'])->first();
            if(isset($plan->id)){
                $user->plan_id=$plan->id;
                $user->save();
            }
        }

        DB::transaction(function () use ($data, $user) {
            $user->update(['stripe_id' => $data['customer']]);

            $user->subscriptions()->create([
                'name' => 'default',
                'stripe_id' => $data['subscription'],
                'stripe_status' => 'active'
            ]);
        });

        return $this->successMethod();
    }

}
