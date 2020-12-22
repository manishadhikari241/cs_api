<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Marketplace\Common\Country;
use Illuminate\Http\Request;

class CountriesController extends Controller {

    public function index(Request $request) {
        return Country::with('translations')->get();
    }

    public function show(Request $request, $id) {
        $country = Country::findOrFail($id);
        $country->translations;
        return $country;
    }

}
