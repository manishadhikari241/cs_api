<?php

namespace App\General\Premium;

use App\General\UploadManyFiles;

class ManageSeason
{
    public function updateName($season, $names = [])
    {
        if (!is_array($names)) {$names = [$names];}
        $season->load('translations');
        foreach ($names as $key => $value) {
            $translation = $season->translations->where('lang', $key)->where('id', $season->id)->first();
            if (!$translation) {
                $season->translations()->save(new SeasonsTranslation(['id' => $season->id, 'name' => $value, 'lang' => $key]));
            } else {
                SeasonsTranslation::where(['id' => $season->id, 'lang' => $key])->update(['name' => $value]);
            }
        }
    }

    public function updateDescription($season, $descriptions = [])
    {
        if (!is_array($descriptions)) {$descriptions = [$descriptions];}
        $season->load('translations');
        foreach ($descriptions as $key => $value) {
            $translation = $season->translations->where('id', $season->id)->where('lang', $key)->first();
            if (!$translation) {
                $season->translations()->save(new SeasonsTranslation(['id' => $season->id, 'description' => $value, 'lang' => $key]));
            } else {
                SeasonsTranslation::where(['id' => $season->id, 'lang' => $key])->update(['description' => $value]);
            }
        }
    }

    public function attach($season, $trend)
    {
        $season->trends()->syncWithoutDetaching([$trend->id]);
        return $season;
    }

    public function detach($season, $trend)
    {
        $season->trends()->detach($trend);
        return $season;
    }

}
