<?php

namespace App\Marketplace\Shopping;

use App\General\Representative\RepresentativePayment;
use App\General\Representative\RepresentativeSubscription;

class RepSubscriptionReportManager
{
    public $rep;
    public $commission_fee             = 0;
    public $representative_fee         = 0;
    public $total_commission_fee       = 0;
    public $total_representative_fee   = 0;
    public $paid;
    public $payment_date;
    public $payment_reference;

    public function __construct($rep, $filter)
    {
        $this->rep    = $rep;
        $this->user   = $rep->user;
        $this->calculateFee($rep, $filter);
        $this->getPaymentStatus($rep, $filter);
    }

    protected function calculateFee($rep, $filter)
    {
        $representativeGroup = $rep->representativeGroup;

        // design sales
        $this->records = RepresentativeSubscription::filter($filter)
                        ->where('representative_id', $rep->id)->get();

        $this->commission_fee     = $this->records->sum('commission');
        $this->representative_fee = $this->records->sum('representative_fee');

        // summary
        $this->total_commission_fee     = $this->commission_fee;
        $this->total_representative_fee = $this->representative_fee;
    }

    protected function getPaymentStatus($rep, $filter)
    {
        $this->payment = RepresentativePayment::where('month', $filter->input('month'))
                      ->where('year', $filter->input('year'))
                      ->where('representative_id', $rep->id)
                      ->first();
        $this->paid              = !!$this->payment;
        $this->payment_date      = $this->payment ? $this->payment->created_at : null;
        $this->payment_reference = $this->payment ? $this->payment->transaction_id : null;

        $this->address = $rep->user->addresses()->with('nation.translations')->default()->first();
    }

    /**
     * @return Array of result on the sales
     */
    public function all()
    {
        return [
          'user'                       => $this->user,
          'records'                    => $this->records,
          'commission_fee'             => $this->commission_fee,
          'representative_fee'         => $this->representative_fee,
          'total_commission_fee'       => $this->total_commission_fee,
          'total_representative_fee'   => $this->total_representative_fee,
          'paid'                       => !!$this->payment,
          'payment_date'               => $this->payment ? $this->payment->created_at : null,
          'payment_reference'          => $this->payment ? $this->payment->transaction_id : null,
          'address'                    => $this->address,
        ];
    }
}
