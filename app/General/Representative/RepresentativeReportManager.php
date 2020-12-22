<?php

namespace App\General\Representative;

use App\General\Representative;

class RepresentativeReportManager
{

    public $commission_fee;
    public $representative_fee;

    public function __construct($orders)
    {
        $this->orders = $orders;
        $this->calculateFee();
    }

    protected function calculateFee()
    {
        $this->commission_fee     = $this->orders->sum('commission');
        $this->representative_fee = $this->orders->sum('representative_fee');
    }
    public static function salesReport($month, $year)
    {
        $sales = RepresentativeOrder::with(['representative'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();
        $representator = $sales->pluck('representative')->unique('id');
        if (!$representator->count()) {
            return null;
        }
        $grouped = $sales->groupBy('representative_id');
        return (object) [
            'representative' => $representator,
        ];
    }

}
