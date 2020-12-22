<?php

namespace App\Marketplace\Payments\Gateways;

use Auth;
use App\User;
use Exception;
use Carbon\Carbon;
use App\Marketplace\Libs\LibPlan;
use App\Exceptions\PaymentException;
use App\Marketplace\Payments\PaymentTt;
use Illuminate\Database\Eloquent\Model;
use App\Marketplace\Libs\Customer as CsCustomer;
use App\Marketplace\Payments\ChargeableCustomer;

class TelexTransfer extends Model
{
  // const IS_OUTSTANDING = 1;
  // const IS_SETTLED = 2;

  // protected $table = 'telex_transfer';

  // protected $fillable = [ 'status', 'settled_at', 'transaction_id' ];

    //  payableInstance: $order, $premiumPlan
    // public function settle($payableInstance, $input)
    // {
    //     $charge = Charge::create([
    //         'amount'   => (float) $payableInstance->total() * 7.8 * 100, // in cent
    //         'source'   => $input['token'],
    //         'currency' => 'hkd',
    //     ]);
    //     \Log::notice('User Alipay Payment:', (array) $charge);
    //     // dd($result, json_encode($result));
    //     if ($charge->status !== 'succeeded') {
    //         throw new \Exception($charge->status, 1);
    //     }
    //     return (object) [
    //         'transaction_id' => $charge->id,
    //     ];
    // }

    public function findOrCreateCustomer($input)
    {
        $csCustomer = CsCustomer::where('user_id', $input['user']->id)->where('payment_method', $input['payment_method'])->first();

        if (!$csCustomer) {
            $result = $this->createAndStoreCustomer($input);
            $csCustomer = $result->cs_customer;
        }

        // return new ChargeableCustomer($customer, $csCustomer, $input);
        return new ChargeableCustomer(null, $csCustomer, $input);
    }

    public function createAndStoreCustomer($input)
    {
        $csCustomer = CsCustomer::forceCreate([
            'status'         => CsCustomer::IS_PAYING,
            'user_id'        => $input['user']->id,
            'customer_id'    => 'tt_'. str_random(10),
            'payment_method' => $input['payment_method'],
        ]);
        return (object)[
            'cs_customer'   => $csCustomer
        ];
    }

    public function subscribe(ChargeableCustomer $customer, $plan, array $options = [])
    {
        $discount = isset($options['discount']) ? (float) $options['discount'] : 0;
        $tt   = PaymentTt::where([
            'user_id' => Auth::id(),
            //   'id'      => $customer->getToken()
        ])->whereNotNull('approved_at')->first();

        // \Log::notice('User TT Subscription:', (array)$charge);

        // @todo check the price difference?
        // @todo check the lib_plan correct?
        // dd($customer->getToken());

        if (!$tt) {
            throw new PaymentException('INVALID_TT_TOKEN');
        }

        if ($tt->lib_plan_id !== $plan->id) {
            throw new PaymentException('INVALID_PLAN');
        }

        $tt->delete();
        
        return (object)[
            'id'             => $customer->getToken(),
            'amount'         => $tt->amount,
            'transaction_id' => $customer->getToken(),
        ];
    }

    public function unsubscribe($plan)
    {
        \Log::info('TT plan canceled (no further actions required)');
        return true;
    }

    //  payableInstance: $order, $premiumPlan
    public function chargeDifference(LibPlan $plan, $input)
    {
        // $tt   = PaymentTt::where([
        //   'code' => $input['token']
        // ])->first();
        $tt   = PaymentTt::where([
          'user_id' => $input['user']->id
        ])->whereNotNull('approved_at')->first();

        \Log::notice('User tt Payment Charge difference:', (array)$tt);
        // dd($result, json_encode($result));
        if (!$tt) {
            throw new PaymentException('INVALID_TT_TOKEN');
        }

        if ($tt->lib_plan_id !== $plan->id) {
            throw new PaymentException('INVALID_PLAN');
        }

        $tt->delete();

        return $tt;
    }

    public function nextBillingDate($plan)
    {
        return Carbon::parse($plan->payment_required_until);
    }

  // // only order, not payable instance!
  // public function settle($order, $input)
  // {
  //   $telexUser = \Auth::user()->telex()->first();
  //   if (!$telexUser->is_active) { abort(422, 'TELEX_USER_INACTIVE'); }
  //   $this->ensureOutstandingPaymentWithinCapacity($telexUser, $order->total);
  //   $order->save();
  //   $this->order_id       = $order->id;
  //   $this->transaction_id = str_random(10);
  //   $this->telex_user_id  = $telexUser->id;
  //   $this->save();
  //   return (object) [
  //     'transaction_id' => $this->transaction_id
  //   ];
  // }

  // protected function ensureOutstandingPaymentWithinCapacity ($telexUser, $additionalAmount)
  // {
  //   $outstandingTransfer = Order::whereHas('telexTransfer', function ($query) use ($telexUser) {
  //     return $query->where([
  //       'status'        => TelexTransfer::IS_OUTSTANDING,
  //       'telex_user_id' => $telexUser->id
  //     ]);
  //   })->sum('total');
  //   if ($outstandingTransfer + $additionalAmount > $telexUser->capacity) {
  //     abort(422, 'TELEX_AMOUNT_EXCESS_CAPACITY');
  //   }
  // }

  // public function order ()
  // {
  //   return $this->belongsTo('App\Marketplace\Shopping\Order');
  // }

  // public function telexUser ()
  // {
  //   return $this->belongsTo('App\General\TelexTransferUser');
  // }

  // public function scopeOutstanding ($query)
  // {
  //   $query->where('status', TelexTransfer::IS_OUTSTANDING);
  // }

}
