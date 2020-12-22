<?php

namespace App\Marketplace\Libs;

class ManageLibInspiration
{
    public function updateTitle($libInspiration, $titles = [])
    {
        foreach ($titles as $key => $value) {
            $translations = $libInspiration->translations()->where('id', $libInspiration->id)->where('lang', $key);
            if ($translations->count() == 0) {
                LibInspirationsTranslation::forceCreate(['id' => $libInspiration->id, 'title' => $value, 'lang' => $key]);
            } else {
                LibInspirationsTranslation::where(['id' => $libInspiration->id, 'lang' => $key])->update(['title' => $value]);
            }
        }
    }

    public function updateDescription($libInspiration, $descriptions = [])
    {
        foreach ($descriptions as $key => $value) {
            $translations = $libInspiration->translations()->where('id', $libInspiration->id)->where('lang', $key);
            if ($translations->count() == 0) {
                LibInspirationsTranslation::forceCreate(['id' => $libInspiration->id, 'description' => $value, 'lang' => $key]);
            } else {
                LibInspirationsTranslation::where(['id' => $libInspiration->id, 'lang' => $key])->update(['description' => $value]);
            }
        }
    }

    public function updateTranslations($inspiration, $field, $titles = [])
    {
        foreach ($titles as $key => $value) {
            $translations = $inspiration->translations()->where('id', $inspiration->id)->where('lang', $key);
            if ($translations->count() == 0) {
                LibInspirationsTranslation::forceCreate(['id' => $inspiration->id, $field => $value, 'lang' => $key]);
            } else {
                LibInspirationsTranslation::where(['id' => $inspiration->id, 'lang' => $key])->update([$field => $value]);
            }
        }
    }
}
