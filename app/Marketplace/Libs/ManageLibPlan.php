<?php

namespace App\Marketplace\Libs;

class ManageLibPlan
{
    public function updateName($plan, $names = [])
    {
        if (!is_array($names)) {
            $names = [$names];
        }
        $plan->load('translations');
        foreach ($names as $key => $value) {
            $translation = $plan->translations->where('lang', $key)->where('id', $plan->id)->first();
            if (!$translation) {
                $plan->translations()->save(new LibPlansTranslation(['id' => $plan->id, 'name' => $value, 'lang' => $key]));
            } else {
                LibPlansTranslation::where(['id' => $plan->id, 'lang' => $key])->update(['name' => $value]);
            }
        }
    }

    public function updateDescription($plan, $descriptions = [])
    {
        if (!is_array($descriptions)) {
            $descriptions = [$descriptions];
        }
        $plan->load('translations');
        foreach ($descriptions as $key => $value) {
            $translation = $plan->translations->where('id', $plan->id)->where('lang', $key)->first();
            if (!$translation) {
                $plan->translations()->save(new LibPlansTranslation(['id' => $plan->id, 'description' => $value, 'lang' => $key]));
            } else {
                LibPlansTranslation::where(['id' => $plan->id, 'lang' => $key])->update(['description' => $value]);
            }
        }
    }
}
