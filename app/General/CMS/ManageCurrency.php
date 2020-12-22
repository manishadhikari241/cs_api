<?php

namespace App\General\CMS;

use App\Marketplace\Common\CurrencyTranslation;

class ManageCurrency
{
    public function updateName($currency, $names = [])
    {

        foreach ($names as $key => $value) {
            $translation = $currency->translations()->where('id', $currency->id)->where('lang', $key);
            if (!$translation) {
                $currency->translations()->save(new CurrencyTranslation(['id' => $currency->id, 'name' => $value, 'lang' => $key]));
            } else {
                CurrencyTranslation::where(['id' => $currency->id, 'lang' => $key])->update(['name' => $value]);
            }
        }
    }

}
