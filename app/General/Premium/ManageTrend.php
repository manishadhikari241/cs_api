<?php

namespace App\General\Premium;

use App\General\UploadManyFiles;

class ManageTrend
{
    public function updateName($trend, $names = [])
    {
        if (!is_array($names)) {$names = [$names];}
        $trend->load('translations');
        foreach ($names as $key => $value) {
            $translation = $trend->translations->where('lang', $key)->where('id', $trend->id)->first();
            if (!$translation) {
                $trend->translations()->save(new TrendsTranslation(['id' => $trend->id, 'name' => $value, 'lang' => $key]));
            } else {
                TrendsTranslation::where(['id' => $trend->id, 'lang' => $key])->update(['name' => $value]);
            }
        }
    }

    public function updateDescription($trend, $descriptions = [])
    {
        if (!is_array($descriptions)) {$descriptions = [$descriptions];}
        $trend->load('translations');
        foreach ($descriptions as $key => $value) {
            $translation = $trend->translations->where('id', $trend->id)->where('lang', $key)->first();
            if (!$translation) {
                $trend->translations()->save(new TrendsTranslation(['id' => $trend->id, 'description' => $value, 'lang' => $key]));
            } else {
                TrendsTranslation::where(['id' => $trend->id, 'lang' => $key])->update(['description' => $value]);
            }
        }
    }

    public function attach($trend, $moodBoards)
    {
        (new UploadManyFiles($moodBoards))->to($trend, 'moodBoards')->save("name");
        return $trend;
    }

}
