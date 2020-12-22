<?php

namespace App\Marketplace\Payments\Gateways;

use App\General\Premium\PremiumCredit;
use App\Marketplace\Payments\Gateways\DistributorGateway;
use App\Marketplace\Payments\Gateways\RepresentativeGranted;

class Gateway
{
    const acceptedGateways = ['credit_card', 'free_checkout', 'telex_transfer', 'premium_credit', 'pos', 'alipay', 'wechatpay', 'google_pay', 'trial', 'distributor', 'representative_granted'];

    public static function mocking()
    {
        return config('mocking') !== 0;
    }

    public static function noMocking()
    {
        return config(['mocking' => 0]);
    }

    public static function via($gateway = 'credit_card')
    {
        if (($gateway === 'credit_card' || $gateway === 'testing') && app()->environment('testing') && Gateway::mocking()) {
            return new TestingGateway;
        }

        if (!in_array($gateway, Gateway::acceptedGateways)) {
            abort(400, 'INVALID_GATEWAY');
        }

        switch ($gateway) {
            case 'credit_card':
                return new Braintree;
                break;
            case 'google_pay':
                return new Braintree;
                break;
            case 'free_checkout':
                return new Freecheckout;
                break;
            case 'telex_transfer':
                return new TelexTransfer;
                break;
            case 'premium_credit':
                return new PremiumCredit;
                break;
            case 'alipay':
                return new StripeAlipay;
                break;
            case 'wechatpay':
                return new StripeWechatpay;
                break;
            case 'distributor':
                return new DistributorGateway;
                break;
            case 'trial':
                return new Trial;
                break;
            case 'representative_granted':
                return new RepresentativeGranted;
                break;
            case 'pos':
                return Pos::where('user_id', \Auth::id())
                    ->where([
                        'is_active' => 1,
                        'status'    => Pos::AUTHORIZED,
                    ])
                    ->first();
                break;
        }

        return null;
    }
}
