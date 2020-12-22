<?php

namespace App\Http\Controllers\API\CMS;

use App\General\Premium\Season;
use App\General\Premium\SeasonsTranslation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SeasonsController extends Controller {

    public function index(Request $request) {
        $seasons = Season::with('translations');

        if ($request->has('all'))
            return $seasons->get();

        if ($request->term)
            $seasons = $seasons->whereHas('translations', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->term.'%');
            });

        $count = $request->has('take') ? $request->input('take') : 20;
        return $seasons->paginate($count);
    }

    public function show(Request $request, $id) {
        $season = Season::with('translations');
        return $season->findOrFail($id);
    }

    public function create(Request $request) {
        $season = new Season();
        $season->save();

        $englishTranslation = new SeasonsTranslation();
        $englishTranslation->id = $season->id;
        $englishTranslation->name = $request->nameEN;
        $englishTranslation->lang = 'en';
        $englishTranslation->save();

        $chineseTranslation = new SeasonsTranslation();
        $chineseTranslation->id = $season->id;
        $chineseTranslation->name = $request->nameCH;
        $chineseTranslation->lang = 'zh-CN';
        $chineseTranslation->save();

        $season->translations;

        return response()->json($season, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id) {
        $season = Season::findOrFail($id);

        SeasonsTranslation::where('id', $id)->where('lang', 'en')->update(['name' => $request->nameEN]);
        SeasonsTranslation::where('id', $id)->where('lang', 'zh-CN')->update(['name' => $request->nameCH]);

        $season->translations;

        return $season;
    }

    public function delete(Request $request, $id) {
        SeasonsTranslation::where('id', $id)->delete();
        Season::findOrFail($id)->delete();

        return respondOK();
    }
}
