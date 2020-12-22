<?php

namespace App\General\Representative;

use App\General\Representative\Representative;
use Carbon\Carbon;

class RepresentativeReportList
{

    public function listPayment($id, $records)
    {
        $representative = Representative::find($id);
        $sales          = [];
        $year           = Carbon::parse($records[0]['created_at'])->year;
        $month          = Carbon::parse($records[0]['created_at'])->month;
        $last_match     = 0;
        foreach ($records as $key => $value) {
            // var_dump($key>0);
            if (Carbon::parse($value['created_at'])->year == $year && Carbon::parse($value['created_at'])->month == $month && $key > 0 && $key < count($records) - 1) {
                continue;
            }
            $sale    = new RepresentativeReportManager(collect(array_slice($records, $last_match, $key - $last_match + ($key == count($records) - 1 ? 1 : 0))));
            $payment = RepresentativePayment::where('month', $month)
                ->where('year', $year)
                ->where('representative_id', $representative->id)
                ->first();
            if ($sale->representative_fee > 0) {
                $sales[] = [
                    'year'               => $year,
                    'month'              => $month,
                    'records'            => array_slice($records, $last_match, $key - $last_match + ($key == count($records) - 1 ? 1 : 0)),
                    'commission_fee'     => $sale->commission_fee,
                    'representative_fee' => $sale->representative_fee,
                    'paid'               => !!$payment,
                    'payment_date'       => $payment ? $payment->created_at : null,
                    'payment_reference'  => $payment ? $payment->transaction_id : null,
                    'address'            => $representative->user->addresses()->default()->first(),
                ];
            }
            $year       = Carbon::parse($value['created_at'])->year;
            $month      = Carbon::parse($value['created_at'])->month;
            $last_match = $key;
        }
        return $sales;

}

}
