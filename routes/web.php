<?php

use App\Crm\CrmCreator;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CreateController;
use App\Http\Controllers\WebhookController;
use App\Jobs\GenerateCrm;
use App\Mail\SupportMail;
use App\Models\Connection;
use App\Models\Crm;
use App\Models\CrmPlan;
use App\Models\CrmTheme;
use App\Models\User;
use iamcal\SQLParser;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/tos', function () {
    return Inertia::render('TermsOfService', []);
});

Route::get('/privacy', function () {
    return Inertia::render('PrivacyPolicy', []);
});

Route::get('/remote', [CreateController::class, 'getRemoteDatabase']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::any('/subscribe', function () {

        if (auth()->user()->subscribed('default')) {
            return redirect()->route('dashboard')->with(['message' => 'Professional plan changed to Standard']);
        }
        $plan = CrmPlan::where('id', 2)->firstOrFail();

        return auth()->user()->newSubscription('default', $plan->stripe_id)
            ->checkout([
                'success_url' => route('dashboard'),
                'cancel_url' => route('dashboard'),
            ]);
        /*
        $plan = CrmPlan::where('id', $planId)->firstOrFail();
        if ($planId == 2){


            if(auth()->user()->subscribed('default')){
                return redirect()->route('dashboard')->with(['message' => 'Professional plan changed to Standard']);
            }
            if(auth()->user()->subscribedToProduct(CrmPlan::where('id', 3)->value('name'), 'default')) {

                //     auth()->user()-> subscription('professional')->cancel();
                auth()->user()->subscription('default')->swap($plan->stripe_id);
                return redirect()->route('dashboard')->with(['message' => 'Professional plan changed to Standard']);
            }
        }
      //  dd($plan);
        if ($plan->id == 1) {

            auth()->user()->subscription('default')->cancel();


            if(auth()->user()->subscribed('default'))
            {

                $subscriptions = auth()->user()->subscriptions()->active()->get();
                dd($subscriptions);
                $subscriptions->map(function($subscription){
                   $subscription->cancel();
                });
                //auth()->user()->subscription('default')->cancel();
                dd(auth()->user()->subscription());
            }

            return redirect()->route('dashboard')->with(['message' => 'Free plan selected.  Warning some crms may expire if they were created before a week ago']);
        }
        elseif ($plan->id == 2){


            if(auth()->user()->subscribed('default')){

            }
            if(auth()->user()->subscribedToProduct(CrmPlan::where('id', 3)->value('name'), 'default')) {

           //     auth()->user()-> subscription('professional')->cancel();
                auth()->user()->subscription('default')->swap($plan->stripe_id);
                return redirect()->route('dashboard')->with(['message' => 'Professional plan changed to Standard']);
            }
          }
        elseif ($plan->id == 3){
            if(auth()->user()->subscribedToProduct(CrmPlan::where('id', 2)->value('name'), 'default')) {
               // auth()->user()-> subscription('standard')->cancel();
                auth()->user()->subscription('default')->swap($plan->stripe_id);
               // auth()->user()->subscription('standard')->swap($plan->stripe_id);
              //  print "done swap";
             //   var_dump(auth()->user()->subscribed('standard'));
            //    var_dump(auth()->user()->subscribed('professional'));
             //   exit;
                return redirect()->route('dashboard')->with(['message' => 'Standard plan changed to Professional']);
            }
        }
        /*
        elseif (auth()->user()->subscribed('standard')) {
            if ($plan->id != auth()->user()->plan_id){
                auth()->user()->subscribed('standard')->swap($plan->name);
                User::where('id', auth()->user()->id)
                    ->update([
                        'plan_id', $plan->id
                    ]);
            }
        }
        elseif (auth()->user()->subscribed('professional')) {
            if ($plan->id != auth()->user()->plan_id){
                auth()->user()->subscribed('professional')->swap($plan->name);
                User::where('id', auth()->user()->id)
                    ->update([
                        'plan_id', $plan->id
                    ]);
            }
        }
        else {
            if(!isset($plan->id))  CrmPlan::where('id', 2)->first();
            return auth()->user()->newSubscription($plan->name, $plan->stripe_id)
                ->checkout([
                    'success_url' => route('dashboard'),
                    'cancel_url' => route('plans')
                ]);
        }
        */
/*
        User::where('id', auth()->user()->id)
            ->update([
                'plan_id', $plan->id
            ]);
*/
        /* $plan->name */

    })->name('billing');

    Route::get('/dashboard', function () {
        auth()->user()->setPlan();
        if (auth()->user()->first_login == 1) {
            User::where('id', auth()->user()->id)->update([
                'first_login' => 0,
            ]);
            $signup_plan = strtolower(auth()->user()->signup_plan);
            $plan = CrmPlan::where('name', 'like', strtolower($signup_plan))->first();
            if (app()->environment('production')) {
                if (isset($plan) && auth()->user()->plan_id == 1) {
                    return auth()->user()->newSubscription($plan->name, $plan->stripe_id)
                        ->checkout([
                            'success_url' => route('dashboard'),
                            'cancel_url' => route('plans'),
                        ]);
                }
            }

        }

        return Inertia::render('Dashboard',
        [

            'crms' => Crm::where('user_id', auth()->user()->id)
                ->with('type')
                ->with('status')
                ->orderBy('created_at', 'desc')
                ->get(),
            'crm_limit' => auth()->user()->crm_limit(),
            'signup_plan_data' => isset($plan) ? $plan : null,
            'connections' => Connection::where('user_id', auth()->user()->id)->get(),
            'plan' => auth()->user()->subscribed('default') ? true : false,
        ]);
    })->name('dashboard');

    Route::get('/plans', function () {
        auth()->user()->setPlan();

        return Inertia::render('Plans',
            [
                'plans' => CrmPlan::all(),
                'plan_id' => auth()->user()->plan_id,
            ]);
    })->name('plans');

    /*
    Route::post('/data/plan', function (Request $request) {
        $status=1;
        $paymentMethods = auth()->user()->paymentMethods();

        $plan=new CrmPlan();
        //$status=$plan->changePlan($request->plan_id);
        return json_encode(['status' => $status]);
    })->name('create');
*/

    Route::post('/data/crm/delete/{id}', function (Request $request) {
        $status = 0;
        $id = $request->input('id', 0);
        if (intval($id) > 0) {
            $status = Crm::deleteCRM($id);
        }

        return json_encode(['status' => $status]);
    })->name('delete_crms');

    Route::post('/data/support_message', function (Request $request) {
        $status = 1;
        $mail = 'support@iceburgcrm.com';
        Mail::to($mail)->send(
            new SupportMail($request->input('subject'), $request->input('message'))
        );

        return json_encode(['status' => $status]);
    })->name('support_message');

    Route::get('/create', function (Request $request) {

        return Inertia::render('Create', [
            'modules' => '',
            'themes' => CrmTheme::all(),
        ]);
    })->name('create');

    Route::get('/support', function (Request $request) {
        return Inertia::render('Support', [
            'modules' => '',
        ]);
    })->name('support');

    Route::post('/create', function (Request $request) {
        $status = 0;
        $message = 'You have reached your limit.  Delete a crm or upgrade your plan';
        $crms = Crm::where('user_id', auth()->user()->id)->count();

        if ($crms < auth()->user()->crm_limit()) {

           $parser = null;
           $data = $request->all();

          // $crm=new CrmCreator($data,auth()->user()->id, $parser);
          //  $crm->create();
            if ($data['type'] == 'uploadschema') {
                if (isset($data['input_file'])) {
                    $sql = $data['input_file']->get();
                    $parser = new SQLParser();
                    $parser->parse($sql);
                }
                unset($data['input_file']);
               // $crm=new CrmCreator($data,auth()->user()->id, $parser);
              //  $crm->create();
            }
           // else {
                $crm = new CrmCreator($data, auth()->user()->id, $parser);
                GenerateCrm::dispatch($crm, auth()->user()->id);
          //  }

            $message = '';
            $status = 1;
        }

        return response()->json(['status' => $status, 'message' => $message]);
    })->name('data_create');

    Route::get('/admin/delete_databases', function (Request $request) {
        $crm = new CrmCreator();
        $crm->deleteDatabases();

    })->name('data_delete');

    Route::get('/data/crms', function (Request $request) {
        return json_encode(Crm::where('user_id', auth()->user()->id)
            ->with('type')
            ->with('status')
            ->orderBy('created_at', 'desc')
            ->get());
    })->name('data_crms');
});

/*
Route::post(
    'stripe_webhook',
    [WebhookController::class, 'handleWebhook']
);
*/
/*
Route::any(
    'stripe/webhook',
    '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook'
);
*/

Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth_google');
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

Route::get('auth/github', [LoginController::class, 'redirectToGithub'])->name('auth_github');
Route::get('auth/github/callback', [LoginController::class, 'handleGithubCallback']);

Route::get('auth/twitter', [LoginController::class, 'redirectToTwitter'])->name('auth_twitter');
Route::get('auth/twitter/callback', [LoginController::class, 'handleTwitterCallback']);
