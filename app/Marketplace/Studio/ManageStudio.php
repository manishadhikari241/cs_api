<?php

namespace App\Marketplace\Studio;

use App\Marketplace\Common\TagsTranslation;
use App\Marketplace\Designs\Design;
use App\User;

class ManageStudio
{
    public function __construct($request = null)
    {
        $this->request = $request;
    }

    public function updateName($studio, $names = [])
    {
        if (!is_array($names)) {
            $names = [$names];
        }
        $studio->load('translations');
        foreach ($names as $key => $value) {
            $translation = $studio->translations->where('lang', $key)->where('id', $studio->id)->first();
            if (!$translation) {
                $studio->translations()->save(new StudiosTranslation(['id' => $studio->id, 'name' => $value, 'lang' => $key]));
            } else {
                StudiosTranslation::where(['id' => $studio->id, 'lang' => $key])->update(['name' => $value]);
            }
        }
    }

    public function updateDescription($studio, $descriptions = [])
    {
        if (!is_array($descriptions)) {
            $descriptions = [$descriptions];
        }
        $studio->load('translations');
        foreach ($descriptions as $key => $value) {
            $translation = $studio->translations->where('id', $studio->id)->where('lang', $key)->first();
            if (!$translation) {
                $studio->translations()->save(new StudiosTranslation(['id' => $studio->id, 'description' => $value, 'lang' => $key]));
            } else {
                StudiosTranslation::where(['id' => $studio->id, 'lang' => $key])->update(['description' => $value]);
                // $translation->update(['description' =>$value]);
            }
        }
    }

    public function suggest($key)
    {
        if (!$key) {
            return [];
        }
        $user      = \Auth::user();
        $studioids = StudioAccess::where(['user_id' => \Auth::id(), 'is_active' => true])->pluck('studio_id');
        $TagsTrans = TagsTranslation::where('name', 'like', "%{$key}%");
        $TagsTrans->whereHas('designs', function ($query) use ($studioids) {
            $query->whereIn('studio_id', $studioids)->where('status', Design::IS_PREMIUM_ONLY);
        });
        $TagsTrans = $this->detectLangSpecific($TagsTrans);
        $TagsTrans = $TagsTrans->take(10)->get();

        // $exact     = $TagsTrans->where('name', $key)->first()
        // ?: $this->detectLangSpecific(TagsTranslation::where('name', "{$key}"))->first();
        // $exact = $TagsTrans->has('designs', function ($query) use ($studioids) {
        //     $query->whereIn('studio_id', $studioids)->where('status', Design::IS_PREMIUM_ONLY);
        // });
        // if ($exact) {
        //     $TagsTrans[0] = $exact;
        // }
        $tags = $TagsTrans->map(function ($tag) {
            $tag->name = trim(mb_strtolower(($tag->name)));
            return $tag;
        });
        $tags =  $tags->unique(function ($result) {
            return $result->name;
        })->values()->all();

        $designs     = Design::whereIn('status', [Design::IS_PREMIUM_ONLY])->where('design_name', 'like', "%{$key}%")->whereIn('studio_id', $studioids)->take(10)->get();
        $designs     = $designs->unique(function ($design) {
            return $design->design_name;
        })->map(function ($design) {
            $design->name = $design->design_name;
            return $design;
        })
        ->values()->all();

        return array_merge($tags, $designs);
    }

    protected function detectLangSpecific($TagsTrans)
    {
        if ($this->request && $this->request->input('lang')) {
            $TagsTrans->where('lang', $this->request->input('lang'));
        }
        return $TagsTrans;
    }
}
