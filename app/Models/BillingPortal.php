<?php

namespace App\Models;

use app\Concerns\ResolvesActions;
use app\Concerns\ResolvesAuthorization;
use app\Concerns\ResolvesBillable;

class BillingPortal
{
    use ResolvesActions;
    use ResolvesAuthorization;
    use ResolvesBillable;

    /**
     * Wether the proration should occur when swapping between plans.
     *
     * @var bool
     */
    protected static $proratesOnSwap = true;

    /**
     * Don't prorate on swapping.
     *
     * @return void
     */
    public static function dontProrateOnSwap()
    {
        static::$proratesOnSwap = false;
    }

    /**
     * Wether the proration should occur when swapping.
     */
    public static function proratesOnSwap(): bool
    {
        return static::$proratesOnSwap;
    }
}
