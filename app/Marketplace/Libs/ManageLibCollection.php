<?php

namespace App\Marketplace\Libs;

class ManageLibCollection
{
    public function updateName($collection, $names = [])
    {
        if (!is_array($names)) {
            $names = [$names];
        }
        $collection->load('translations');
        foreach ($names as $key => $value) {
            $translation = $collection->translations->where('lang', $key)->where('id', $collection->id)->first();
            if (!$translation) {
                $collection->translations()->save(new LibCollectionsTranslation(['id' => $collection->id, 'name' => $value, 'lang' => $key]));
            } else {
                LibCollectionsTranslation::where(['id' => $collection->id, 'lang' => $key])->update(['name' => $value]);
            }
        }
    }

    public function updateIntro($collection, $intros = [])
    {
        if (!is_array($intros)) {
            $intros = [$intros];
        }
        $collection->load('translations');
        foreach ($intros as $key => $value) {
            $translation = $collection->translations->where('lang', $key)->where('id', $collection->id)->first();
            if (!$translation) {
                $collection->translations()->save(new LibCollectionsTranslation(['id' => $collection->id, 'intro' => $value, 'lang' => $key]));
            } else {
                LibCollectionsTranslation::where(['id' => $collection->id, 'lang' => $key])->update(['intro' => $value]);
            }
        }
    }

    public function updateDescription($collection, $descriptions = [])
    {
        if (!is_array($descriptions)) {
            $descriptions = [$descriptions];
        }
        $collection->load('translations');
        foreach ($descriptions as $key => $value) {
            $translation = $collection->translations->where('id', $collection->id)->where('lang', $key)->first();
            if (!$translation) {
                $collection->translations()->save(new LibCollectionsTranslation(['id' => $collection->id, 'description' => $value, 'lang' => $key]));
            } else {
                LibCollectionsTranslation::where(['id' => $collection->id, 'lang' => $key])->update(['description' => $value]);
            }
        }
    }
}
