<?php

namespace App\Marketplace\Common;

class TagsManager
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
        $TagsTrans = TagsTranslation::where('name', 'like', "%{$key}%");
        $user      = \Auth::user();

        // @depreciated is_exclusive tag concept
        // if (!$user || !$user->is_super_admin) {
        //     $TagsTrans->whereHas('tag', function ($query) use ($key) {
        //         $query->where('is_exclusive', false);
        //     });
        // }

        $TagsTrans = $this->detectLangSpecific($TagsTrans);
        $TagsTrans = $TagsTrans->take(10)->get();
        $exact     = $TagsTrans->where('name', $key)->first()
        ?: $this->detectLangSpecific(TagsTranslation::where('name', "{$key}"))->first();
        if ($exact) {
            $TagsTrans[0] = $exact;
        }
        // dd($key);
        $results = $TagsTrans->map(function ($tag) {
            $tag->name = trim(mb_strtolower(($tag->name)));
            return $tag;
        });
        return $results->unique(function ($result) {
            return $result->name;
        })->values()->all();
    }

    protected function detectLangSpecific($TagsTrans)
    {
        if ($this->request && $this->request->input('lang')) {
            $TagsTrans->where('lang', $this->request->input('lang'));
        }
        return $TagsTrans;
    }
}
