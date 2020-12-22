<?php

namespace App\General\CMS;

use App\Marketplace\Common\CountriesTranslation;

class ManageCountry
{
    public function updateName($country, $names = [])
    {

        foreach ($names as $key => $value) {
            $translation = $country->translations()->where('id', $country->id)->where('lang', $key);
            if (!$translation) {
                $country->translations()->save(new CountriesTranslation(['id' => $country->id, 'name' => $value, 'lang' => $key]));
            } else {
                CountriesTranslation::where(['id' => $country->id, 'lang' => $key])->update(['name' => $value]);
            }
        }
    }

}
