<?php

namespace App\Marketplace\Payments\Gateways;

/**
 * It can only unsubscribe
 */
class Trial
{
    public function unsubscribe($plan)
    {
        return true;
    }
}
