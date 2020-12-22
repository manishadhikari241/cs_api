<?php

namespace App\Http\Controllers\API;

use App\General\Address;
use App\Http\Controllers\Controller;
use App\Marketplace\Designs\Design;
use App\Pricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller {

    public function init(Request $request) {
        $response = [
            'top100' => Design::orderBy('pseudo_downloads', 'desc')->skip(99)->first()->pseudo_downloads ?? 0,
            'pricing' => Pricing::first()
        ];

        if (Auth::guard('api')->check()) {
            $response['lists'] = Auth::guard('api')->user()->memberLists()->with(['products.tags.translations'])->orderBy('created_at', 'DESC')->get();
            $response['quota'] = Auth::guard('api')->user()->quota;
            $response['addresses'] = Address::where('user_id', Auth::guard('api')->id())->get();
        }

        return $response;
    }

}
