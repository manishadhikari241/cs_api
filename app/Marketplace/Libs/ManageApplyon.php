<?php

namespace App\Marketplace\Libs;

class ManageApplyon
{
    public function updateTranslations($applyon, $field, $titles = [])
    {
        foreach ($titles as $lang => $value) {
            $translations = $applyon->translations()->where('id', $applyon->id)->where('lang', $lang);
            if ($translations->count() == 0) {
                \Log::info('feed', (array)[$lang, $value]);
                ApplyonsTranslation::forceCreate(['id' => $applyon->id, $field => $value, 'lang' => $lang]);
            } else {
                ApplyonsTranslation::where(['id' => $applyon->id, 'lang' => $lang])->update([$field => $value]);
            }
        }
    }
}
