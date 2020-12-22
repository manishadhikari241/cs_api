<?php

namespace App\General\CMS;

use App\Marketplace\Designs\ColorsTranslation;

class ManageColor
{
    public function updateName($color, $names = [])
    {
        foreach ($names as $key => $value) {
            $translation = $color->translations()->where('id', $color->id)->where('lang', $key);
            if (!$translation) {
                $color->translations()->save(new ColorsTranslation(['id' => $color->id, 'name' => $value, 'lang' => $key]));
            } else {
                ColorsTranslation::where(['id' => $color->id, 'lang' => $key])->update(['name' => $value]);
            }
        }
    }

}
