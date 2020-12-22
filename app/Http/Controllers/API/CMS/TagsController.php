<?php

namespace App\Http\Controllers\API\CMS;

use App\Http\Controllers\Controller;
use App\Marketplace\Common\Tag;
use App\Marketplace\Common\TagsTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TagsController extends Controller {

    public function index(Request $request) {
        $tags = Tag::with('translations');

        if ($request->term)
            $tags = $tags->whereHas('translations', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->term.'%');
            });

        return $tags->orderBy('id', 'asc')->paginate(20);
    }

    public function show(Request $request, $id) {
        $tag = Tag::findOrFail($id);
        $tag->translations;
        return $tag;
    }

    public function create(Request $request) {
        $tag = new Tag();
        $tag->save();

        $englishTranslation = new TagsTranslation();
        $englishTranslation->id = $tag->id;
        $englishTranslation->name = $request->nameEN;
        $englishTranslation->lang = 'en';
        $englishTranslation->save();

        $chineseTranslation = new TagsTranslation();
        $chineseTranslation->id = $tag->id;
        $chineseTranslation->name = $request->nameCH;
        $chineseTranslation->lang = 'zh-CN';
        $chineseTranslation->save();

        $tag->translations;

        return response()->json($tag, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id) {
        $tag = Tag::findOrFail($id);

        if (!TagsTranslation::where('id', $id)->where('lang', 'en')->exists()) {
            $tagTranslationEN = new TagsTranslation();
            $tagTranslationEN->id = $id;
            $tagTranslationEN->name = $request->nameEN;
            $tagTranslationEN->lang = 'en';
            $tagTranslationEN->save();
        } else {
            TagsTranslation::where('id', $id)->where('lang', 'en')->update(['name' => $request->nameEN]);
        }
        if (!TagsTranslation::where('id', $id)->where('lang', 'zh-CN')->exists()) {
            $tagTranslationEN = new TagsTranslation();
            $tagTranslationEN->id = $id;
            $tagTranslationEN->name = $request->nameCH;
            $tagTranslationEN->lang = 'zh-CN';
            $tagTranslationEN->save();
        } else {
            TagsTranslation::where('id', $id)->where('lang', 'zh-CN')->update(['name' => $request->nameCH]);
        }

        $tag->translations;

        return $tag;
    }

    public function delete(Request $request, $id) {
        TagsTranslation::where('id', $id)->delete();
        Tag::findOrFail($id)->delete();

        return respondOK();
    }

}
