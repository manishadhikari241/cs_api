<?php

namespace App\Marketplace\Libs;

class ManageLibMonth
{
    public function updateTitle($libMonth, $titles = [])
    {
        foreach ($titles as $key => $value) {
            $translations = $libMonth->translations()->where('id', $libMonth->id)->where('lang', $key);
            if ($translations->count() == 0) {
                LibMonthsTranslation::forceCreate(['id' => $libMonth->id, 'title' => $value, 'lang' => $key]);
            } else {
                LibMonthsTranslation::where(['id' => $libMonth->id, 'lang' => $key])->update(['title' => $value]);
            }
        }
    }

    public function updateDescription($libMonth, $descriptions = [])
    {
        foreach ($descriptions as $key => $value) {
            $translations = $libMonth->translations()->where('id', $libMonth->id)->where('lang', $key);
            if ($translations->count() == 0) {
                LibMonthsTranslation::forceCreate(['id' => $libMonth->id, 'description' => $value, 'lang' => $key]);
            } else {
                LibMonthsTranslation::where(['id' => $libMonth->id, 'lang' => $key])->update(['description' => $value]);
            }
        }
    }

    public function updateTranslations($libMonth, $field, $titles = [])
    {
        foreach ($titles as $key => $value) {
            $translations = $libMonth->translations()->where('id', $libMonth->id)->where('lang', $key);
            if ($translations->count() == 0) {
                LibMonthsTranslation::forceCreate(['id' => $libMonth->id, $field => $value, 'lang' => $key]);
            } else {
                LibMonthsTranslation::where(['id' => $libMonth->id, 'lang' => $key])->update([$field => $value]);
            }
        }
    }
}
