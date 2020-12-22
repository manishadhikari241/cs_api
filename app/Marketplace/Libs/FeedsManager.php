<?php

namespace App\Marketplace\Libs;

use Auth;
use App\Marketplace\Goods\Good;
use App\Marketplace\Goods\GoodsTranslation;
use App\Marketplace\Libs\LibCategory;
use App\Marketplace\Libs\LibCategoryTranslation;

class FeedsManager
{
    public function __construct($request = null)
    {
        $this->request = $request;
    }

    public function suggest($key)
    {
        if (!$key) {
            return [];
        }
        $catsTrans = LibCategoryTranslation::where('name', 'like', "%{$key}%");
        $catsTrans = $this->detectLangSpecific($catsTrans);
        $catsTrans = $catsTrans->take(10)->get();
        $exact     = $catsTrans->where('name', $key)->first()
        ?: $this->detectLangSpecific(LibCategoryTranslation::where('name', "{$key}"))->first();
        if ($exact) {
            $catsTrans[0] = $exact;
        }
        $cats = $catsTrans->map(function ($cat) {
            $cat->name = trim(mb_strtolower(($cat->name)));
            return $cat;
        });

        $goodsTrans = GoodsTranslation::where('name', 'like', "%{$key}%")->whereHas('Good', function ($q) {
            $q->whereNull('user_id');
        });
        $goodsTrans = $this->detectLangSpecific($goodsTrans);
        $goodsTrans = $goodsTrans->take(10)->get();
        $exact     = $goodsTrans->where('name', $key)->first()
        ?: $this->detectLangSpecific(LibCategoryTranslation::where('name', "{$key}"))->first();
        if ($exact) {
            $goodsTrans[0] = $exact;
        }
        $goods = $goodsTrans->map(function ($good) {
            $good->name = trim(mb_strtolower(($good->name)));
            return $good;
        });

        return $cats->merge($goods)->unique(function ($result) {
            return $result->name;
        })->take(10)->values()->all();
    }

    public function exactKeyword($key)
    {
        if (!$key) {
            return [];
        }
        $cat = LibCategory::whereHas('translations', function ($query) use ($key) {
            return $query->where('name', "{$key}");
        })->with('translations')->first();
        if ($cat) {
            return $cat;
        }
        $good = Good::whereHas('translations', function ($query) use ($key) {
            return $query->where('name', "{$key}");
        })->with('translations')->first();
        if ($good) {
            return $good;
        }
    }

    protected function detectLangSpecific($TagsTrans)
    {
        if ($this->request && $this->request->input('lang')) {
            $TagsTrans->where('lang', $this->request->input('lang'));
        }
        return $TagsTrans;
    }
}
