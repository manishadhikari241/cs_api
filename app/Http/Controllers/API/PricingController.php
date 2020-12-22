<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Pricing;
use Illuminate\Http\Request;

class PricingController extends Controller {

    public function index(Request $request) {
        return Pricing::first();
    }

}
