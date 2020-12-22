<?php

namespace App\General;

use App\General\UploadManyFiles;

class ManagePromotion
{
    public function updateTitle($promotion, $titles = [])
    {
        if (!is_array($titles)) {$titles = [$titles];}
        $promotion->load('translations');
        foreach ($titles as $key => $value) {
            $translation = $promotion->translations->where('id', $promotion->id)->where('lang', $key)->first();
            if (!$translation) {
                $promotion->translations()->save(new PromotionsTranslation(['id' => $promotion->id, 'title' => $value, 'lang' => $key]));
            } else {
                PromotionsTranslation::where(['id' => $promotion->id, 'lang' => $key])->update(['title' => $value]);
            }
        }
    }

    public function updateContent($promotion, $contents = [])
    {
        if (!is_array($contents)) {$contents = [$contents];}
        $promotion->load('translations');
        foreach ($contents as $key => $value) {
            $translation = $promotion->translations->where('id', $promotion->id)->where('lang', $key)->first();
            if (!$translation) {
                $promotion->translations()->save(new PromotionsTranslation(['id' => $promotion->id, 'content' => $value, 'lang' => $key]));
            } else {
                PromotionsTranslation::where(['id' => $promotion->id, 'lang' => $key])->update(['content' => $value]);
            }
        }
    }

}
