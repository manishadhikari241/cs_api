<?php

namespace App\Http\Controllers\API\CMS;

use App\Http\Controllers\Controller;
use App\Pricing;
use Illuminate\Http\Request;

class PricingController extends Controller {

    public function index(Request $request) {
        return Pricing::first();
    }

    public function update(Request $request) {
        $pricing = Pricing::first();

        $pricing->standard_min_count = $request->standard_min_count;
        $pricing->standard_min_price = $request->standard_min_price;
        $pricing->standard_max_count = $request->standard_max_count;
        $pricing->standard_max_price = $request->standard_max_price;
        $pricing->extended_min_count = $request->extended_min_count;
        $pricing->extended_min_price = $request->extended_min_price;
        $pricing->extended_max_count = $request->extended_max_count;
        $pricing->extended_max_price = $request->extended_max_price;
        $pricing->exclusive_min_count = $request->exclusive_min_count;
        $pricing->exclusive_min_price = $request->exclusive_min_price;
        $pricing->exclusive_max_count = $request->exclusive_max_count;
        $pricing->exclusive_max_price = $request->exclusive_max_price;
        $pricing->simulator_min_count = $request->simulator_min_count;
        $pricing->simulator_min_price = $request->simulator_min_price;
        $pricing->simulator_max_count = $request->simulator_max_count;
        $pricing->simulator_max_price = $request->simulator_max_price;

        $pricing->save();

        return $pricing;
    }

}
