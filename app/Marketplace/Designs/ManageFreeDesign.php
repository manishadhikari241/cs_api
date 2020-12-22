<?php

namespace App\Marketplace\Designs;

use App\Marketplace\Designs\FreeDesignsTranslation;

class ManageFreeDesign
{
    public function updateName($free, $names = [])
    {
        foreach ($names as $key => $value) {
            $translations = $free->translations()->where('id', $free->id)->where('lang', $key);
            if ($translations->count() == 0) {
                $free->translations()->save(new FreeDesignsTranslation(['id' => $free->id, 'name' => $value, 'lang' => $key]));
            } else {
                FreeDesignsTranslation::where(['id' => $free->id, 'lang' => $key])->update(['name' => $value]);
            }
        }
    }

}
