<?php

namespace App\Marketplace\Libs;

class ManageEmbassador
{
    public function updateTranslations($embassador, $field, $titles = [])
    {
        foreach ($titles as $key => $value) {
            $translations = $embassador->translations()->where('id', $embassador->id)->where('lang', $key);
            if ($translations->count() == 0) {
                EmbassadorsTranslation::forceCreate(['id' => $embassador->id, $field => $value, 'lang' => $key]);
            } else {
                EmbassadorsTranslation::where(['id' => $embassador->id, 'lang' => $key])->update([$field => $value]);
            }
        }
    }
}
