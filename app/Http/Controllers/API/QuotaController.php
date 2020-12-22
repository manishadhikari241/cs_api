<?php

namespace App\Http\Controllers\API;

use App\Constants\ErrorCodes;
use App\CSCoupon;
use App\Http\Controllers\Controller;
use App\Http\Requests\BuyQuotaRequest;
use App\Payment;
use App\Pricing;
use App\Quota;
use App\User;
use Braintree\Gateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuotaController extends Controller {

    private function gateway() {
        return new Gateway([
            'environment' => config('services.braintree.environment'),
            'merchantId' => config('services.braintree.merchantId'),
            'publicKey' => config('services.braintree.publicKey'),
            'privateKey' => config('services.braintree.privateKey')
        ]);
    }

    public function buy(BuyQuotaRequest $request) {
        $pricing = Pricing::first()->toArray();

        if ($request->amount != floatval($pricing[$request->package.'_'.$request->package_type.'_count'])*floatval($pricing[$request->package.'_'.$request->package_type.'_price']))
            return respondError(ErrorCodes::VALIDATION_FAILED, Response::HTTP_UNPROCESSABLE_ENTITY, 'Provided amount does not match the package\'s price');

        $result = $this->gateway()->transaction()->sale([
            'amount' => $request->amount,
            'paymentMethodNonce' => $request->nonce,
            'options' => [
                'submitForSettlement' => true
            ]
        ]);

        if ($result->success || !is_null($result->transaction)) {
            $payment = new Payment();
            $payment->user_id = Auth::guard('api')->id();
            $payment->amount = $request->amount;
            $payment->package = $request->package;
            $payment->quantity = $pricing[$request->package.'_'.$request->package_type.'_count'];
            $payment->transaction_id = $result->transaction->id;
            $payment->billing_details = $request->billing_details;
            $payment->save();

            $pkg = $payment->package;

            $quota = Auth::guard('api')->user()->quota;
            if (!$quota) {
                $quota = Quota::createEmpty(Auth::guard('api')->id());
                $quota->save();
            }
            $quota->{$pkg} = $quota->{$pkg} + $payment->quantity;
            $quota->{$pkg.'_expiry'} = Carbon::now()->addYear();
            $quota->save();

            return $quota;
        } else {
            $errors = [];
            foreach ($result->errors->deepAll() as $error) {
                array_push($errors, $error->code.': '.$error->message);
            }
            return respondError(ErrorCodes::PAYMENT_FAILED, Response::HTTP_EXPECTATION_FAILED, implode(', ', $errors));
        }
    }

}
