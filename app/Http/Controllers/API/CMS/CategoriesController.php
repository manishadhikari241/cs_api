<?php

namespace App\Http\Controllers\API\CMS;

use App\Http\Controllers\Controller;
use App\Marketplace\Common\CategoriesTranslation;
use App\Marketplace\Common\Category;
use App\Marketplace\Libs\LibCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoriesController extends Controller {

    public function index(Request $request) {
        $categories = Category::with('translations');
        if ($request->has('all'))
            return $categories->get();

        if ($request->term)
            $categories = $categories->whereHas('translations', function ($q) use ($request) {
                $q->where('tags', 'like', '%'.$request->term.'%');
            });

        return $categories->orderBy('id', 'asc')->paginate(20);
    }

    public function show(Request $request, $id) {
        $category = Category::findOrFail($id);
        $category->translations;
        return $category;
    }

    public function create(Request $request) {
        $category = new Category();
        $category->save();

        $englishTranslation = new CategoriesTranslation();
        $englishTranslation->id = $category->id;
        $englishTranslation->tags = $request->nameEN;
        $englishTranslation->lang = 'en';
        $englishTranslation->save();

        $chineseTranslation = new CategoriesTranslation();
        $chineseTranslation->id = $category->id;
        $chineseTranslation->tags = $request->nameCH;
        $chineseTranslation->lang = 'zh-CN';
        $chineseTranslation->save();

        $category->translations;

        return response()->json($category, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id) {
        $category = Category::findOrFail($id);

        CategoriesTranslation::where('id', $id)->where('lang', 'en')->update(['tags' => $request->nameEN]);
        CategoriesTranslation::where('id', $id)->where('lang', 'zh-CN')->update(['tags' => $request->nameCH]);

        $category->translations;

        return $category;
    }

    public function delete(Request $request, $id) {
        CategoriesTranslation::where('id', $id)->delete();
        Category::findOrFail($id)->delete();

        return respondOK();
    }




    public function libIndex(Request $request) {
        $libCategories = LibCategory::with('translations');
        return $libCategories->orderBy('id', 'asc')->get();
    }

}
