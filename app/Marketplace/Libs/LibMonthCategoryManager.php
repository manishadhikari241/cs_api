<?php

namespace App\Marketplace\Libs;

use Illuminate\Support\Facades\DB;

class LibMonthCategoryManager
{
    protected $libMonth;

    public function __construct(LibMonth $libMonth)
    {
        $this->libMonth = $libMonth;
    }

    /* Get List of count of category of current month group by plans */
    public function stats()
    {
        $pro = DB::table('lib_month_design')
                ->selectRaw('lib_category_id, count(*) as aggregate')
                ->where('lib_month_id', $this->libMonth->id)
                ->where('pro', 1)
                ->groupBy('lib_category_id')
                ->get();
        $basic = DB::table('lib_month_design')
                ->selectRaw('lib_category_id, count(*) as aggregate')
                ->where('lib_month_id', $this->libMonth->id)
                ->where('basic', 1)
                ->groupBy('lib_category_id')
                ->get();
        // $is_trial = DB::table('lib_month_design')
        //         ->selectRaw('lib_category_id, count(*) as aggregate')
        //         ->where('lib_month_id', $this->libMonth->id)
        //         ->where('is_trial', 1)
        //         ->groupBy('lib_category_id')
        //         ->get();
        return (object)[
          'pro'      => $pro,
          'basic'    => $basic,
        //   'is_trial' => $is_trial
        ];
    }
}
