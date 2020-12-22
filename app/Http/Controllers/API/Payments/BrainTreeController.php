<?php

namespace App\Http\Controllers\API\Payments;

use App\Http\Controllers\Controller;
use Braintree\Gateway;
use Illuminate\Http\Request;

class BrainTreeController extends Controller {

    private function gateway() {
        return new Gateway([
            'environment' => config('services.braintree.environment'),
            'merchantId' => config('services.braintree.merchantId'),
            'publicKey' => config('services.braintree.publicKey'),
            'privateKey' => config('services.braintree.privateKey')
        ]);
    }

    public function token(Request $request) {
        return ['token' => $this->gateway()->clientToken()->generate()];
    }

}
