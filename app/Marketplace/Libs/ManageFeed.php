<?php

namespace App\Marketplace\Libs;

class ManageFeed
{
    public function updateTranslations($feed, $field, $titles = [])
    {
        foreach ($titles as $lang => $value) {
            $translations = $feed->translations()->where('id', $feed->id)->where('lang', $lang);
            if ($translations->count() == 0) {
                \Log::info('feed', (array) [$lang, $value ]);
                FeedsTranslation::forceCreate(['id' => $feed->id, $field => $value, 'lang' => $lang]);
            } else {
                FeedsTranslation::where(['id' => $feed->id, 'lang' => $lang])->update([$field => $value]);
            }
        }
    }
}
