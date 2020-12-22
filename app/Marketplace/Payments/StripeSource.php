<?php

namespace App\Marketplace\Payments;

use App\User;
use Illuminate\Http\Request;

class StripeSource
{
    protected $source = null;
    public $user      = null;

    public function __construct($sourceEvent)
    {
        $this->source       = $sourceEvent->data;
        $this->user         = $this->findUserFromSource();
        // @todo report user not find
    }

    // this will simulate the request all to get payment object
    public function all()
    {
        return [
          'payment_method'     => 'wechatpay',
          'address_id'         => $this->user->addresses()->orderBy('is_default', 'desc')->first()->id,
          'token'              => $this->source->object->id,
          'api_token'          => $this->user->api_token,
        ];
    }

    public function findUserFromSource()
    {
        // \Log::info('source:', (array) $this->source);
        // dd(User::where('stripe_source', $this->source->object->id)->first());
        return User::where('stripe_source', $this->source->object->id)->first();
    }
}
