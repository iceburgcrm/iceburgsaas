<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use Billable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'email', 'password', 'signup_plan', 'first_login', 'google_id', 'github_id', 'twitter_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function crm_limit()
    {
        if (auth()->user()->subscribed('default')) {
            return 10;
        }

        return 2;
    }

    public function setPlan()
    {

        $planId = 1;
     //   dd(CrmPlan::where('id', 2)->value('stripe_id'));
      //  dd(auth()->user()->subscribed('default'));
        if (auth()->user()->subscription() &&
            auth()->user()->subscription()->stripe_price == CrmPlan::where('id', 2)->value('stripe_id')
        ) {
            $planId = 2;
        } elseif (auth()->user()->subscription() &&
            auth()->user()->subscription()->stripe_price == CrmPlan::where('id', 3)->value('stripe_id')) {
            $planId = 3;
        }

        $this->plan_id = $planId;
        $this->save();
    }

    public function is_subscribed()
    {
        if ($this->subscribed('default')) {
            return true;
        }

        return false;
    }

    public function plan()
    {
        return $this->hasOne(CrmPlan::class, 'id', 'plan_id');
    }
}
