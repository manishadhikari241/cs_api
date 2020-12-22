<?php

namespace App\Http\Controllers\API\CMS;

use App\Http\Controllers\Controller;
use App\Marketplace\Common\TagsTranslation;
use App\Marketplace\Designs\Color;
use App\Marketplace\Designs\ColorsTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ColorsController extends Controller {

    public function index(Request $request) {
        $colors = Color::with('translations');
        if ($request->has('all'))
            return $colors->get();

        if ($request->term)
            $colors = $colors->where('code', 'like', '%'.$request->term.'%')->orWhereHas('translations', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->term.'%');
            });

        return $colors->orderBy('id', 'asc')->paginate(20);
    }

    public function show(Request $request, $id) {
        $color = Color::findOrFail($id);
        $color->translations;
        return $color;
    }

    public function create(Request $request) {
        $color = new Color();
        $color->code = $request->code;
        $color->save();

        $englishTranslation = new ColorsTranslation();
        $englishTranslation->id = $color->id;
        $englishTranslation->name = $request->nameEN;
        $englishTranslation->lang = 'en';
        $englishTranslation->save();

        $chineseTranslation = new ColorsTranslation();
        $chineseTranslation->id = $color->id;
        $chineseTranslation->name = $request->nameCH;
        $chineseTranslation->lang = 'zh-CN';
        $chineseTranslation->save();

        $color->translations;

        return response()->json($color, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id) {
        $color = Color::findOrFail($id);
        $color->code = $request->code;
        $color->save();

        ColorsTranslation::where('id', $id)->where('lang', 'en')->update(['name' => $request->nameEN]);
        ColorsTranslation::where('id', $id)->where('lang', 'zh-CN')->update(['name' => $request->nameCH]);

        $color->translations;

        return $color;
    }

    public function delete(Request $request, $id) {
        ColorsTranslation::where('id', $id)->delete();
        Color::findOrFail($id)->delete();

        return respondOK();
    }

}
