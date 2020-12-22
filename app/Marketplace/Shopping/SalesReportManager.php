<?php

namespace App\Marketplace\Shopping;

use App\General\Premium\ProjectPayment;
use App\Marketplace\Payments\CreatorPayment;

class SalesReportManager
{
    public $commission_fee         = 0;
    public $creator_fee            = 0;
    public $project_commission_fee = 0;
    public $project_creator_fee    = 0;
    public $total_commission_fee   = 0;
    public $total_creator_fee      = 0;
    public $paid;
    public $payment_date;
    public $payment_reference;
    public $project_payments;

    public function __construct($user, $filter)
    {
        $this->user             = $user;
        $this->calculateFee($user, $filter);
        $this->getPaymentStatus($user, $filter);
    }

    protected function calculateFee($user, $filter)
    {
        $creatorGroup                 = $this->user->profile->creatorGroup;

        // design sales
        $this->records = Sales::filter($filter)->whereHas('product', function ($query) use ($user) {
            return $query->where('designer_id', $user->id);
        })->get();
        $subTotal                        = $this->records->sum('price');
        $this->commission_fee            = $this->records->sum('commission');
        $this->creator_fee               = $subTotal - $this->commission_fee;

        // project
        $this->project_payments = ProjectPayment::where('status', ProjectPayment::IS_ACCEPTED)
            ->whereMonth('created_at', $filter->input('month'))
            ->whereYear('created_at', $filter->input('year'))
            ->whereHas('studio', function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->with('user', 'request.project', 'request.projectPackage')->get();
        if ($this->project_payments) {
            $this->project_commission_fee = $this->project_payments->sum('commission_fee');
            $this->project_creator_fee    = $this->project_payments->sum('creator_fee');
        }

        // summary
        $this->total_commission_fee = $this->commission_fee      + $this->project_commission_fee;
        $this->total_creator_fee    = $this->project_creator_fee + $this->creator_fee;
    }

    protected function getPaymentStatus($user, $filter)
    {
        $this->payment = CreatorPayment::where('month', $filter->input('month'))
            ->where('year', $filter->input('year'))
            ->where('user_id', $user->id)
            ->first();
        $this->paid              = !!$this->payment;
        $this->payment_date      = $this->payment ? $this->payment->created_at : null;
        $this->payment_reference = $this->payment ? $this->payment->transaction_id : null;

        $this->address = $user->addresses()->with('nation.translations')->default()->first();
    }

    /**
     * @return Array of result on the sales
     */
    public function all()
    {
        return [
            'user'                   => $this->user,
            'records'                => $this->records,
            'commission_fee'         => $this->commission_fee,
            'creator_fee'            => $this->creator_fee,
            'project_payments'       => $this->project_payments,
            'project_commission_fee' => $this->project_commission_fee,
            'project_creator_fee'    => $this->project_creator_fee,
            'total_commission_fee'   => $this->total_commission_fee,
            'total_creator_fee'      => $this->total_creator_fee,
            'paid'                   => !!$this->payment,
            'payment_date'           => $this->payment ? $this->payment->created_at : null,
            'payment_reference'      => $this->payment ? $this->payment->transaction_id : null,
            'address'                => $this->address,
        ];
    }

    public static function previousSales($month, $year)
    {
        $sales = Sales::with(['product.designer'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereIn('type', ['product', 'product-licence'])
            ->get();
        //
        // List creator ids by product
        $products = $sales->pluck('product');
        $creators = $products->pluck('designer')->unique('id');
        // $creatorsId = $creators->pluck('id');
        // dd($creatorsId);

        if (!$creators->count()) {
            return null;
        }
        // dd($products->groupBy('designer_id'));
        $grouped = $products->groupBy('designer_id');
        return (object) [
            'products' => $products,
            'creators' => $creators,
        ];
    }
}
