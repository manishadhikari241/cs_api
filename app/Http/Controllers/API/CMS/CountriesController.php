<?php

namespace App\Http\Controllers\API\CMS;

use App\Http\Controllers\Controller;
use App\Marketplace\Common\CountriesTranslation;
use App\Marketplace\Common\Country;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CountriesController extends Controller {

    public function index(Request $request) {
        $countries = Country::with('translations');
        if ($request->has('all'))
            return $countries->get();

        if ($request->term)
            $countries = $countries->whereHas('translations', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->term.'%');
            });
        return $countries->orderBy('id', 'desc')->paginate(20);
    }

    public function show(Request $request, $id) {
        $country = Country::findOrFail($id);
        $country->translations;
        return $country;
    }

    public function create(Request $request) {
        $country = new Country();
        $country->iso2 = $request->iso2;
        $country->save();

        $englishTranslation = new CountriesTranslation();
        $englishTranslation->id = $country->id;
        $englishTranslation->name = $request->nameEN;
        $englishTranslation->lang = 'en';
        $englishTranslation->save();

        $chineseTranslation = new CountriesTranslation();
        $chineseTranslation->id = $country->id;
        $chineseTranslation->name = $request->nameCH;
        $chineseTranslation->lang = 'zh-CN';
        $chineseTranslation->save();

        $country->translations;

        return response()->json($country, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id) {
        $country = Country::findOrFail($id);

        $country->iso2 = $request->iso2;
        $country->save();

        CountriesTranslation::where('id', $id)->where('lang', 'en')->update(['name' => $request->nameEN]);
        CountriesTranslation::where('id', $id)->where('lang', 'zh-CN')->update(['name' => $request->nameCH]);

        $country->translations;

        return $country;
    }

    public function delete(Request $request, $id) {
        $country = Country::findOrFail($id);
        $country->delete();

        return respondOK();
    }

}
