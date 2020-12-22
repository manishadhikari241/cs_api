<?php

namespace App\Pages;

use App\General\UploadFile;
use App\Pages\PMSTranslation;

class ManagePMS
{
    public function updatePms($request, $page, $pms)
    {
        foreach ($request->all() as $key => $value) {
            $keypms      = explode("_", $key);
            $translation = PMSTranslation::where(['id' => $keypms[0], 'lang' => $keypms[1]]);
            if (!$translation) {
                PMSTranslation::create(['id' => $keypms[0], 'lang' => $keypms[1], 'content' => $value]);
            } else {
                $translation->update(['content' => $value]);
            }
        }
        if ($request->file()) {
            foreach ($request->file() as $key => $value) {
                $keyfile        = explode("_", $key);
                $PMSTranslation = PMSTranslation::where(['id' => $keyfile[0], 'lang' => $keyfile[1]])->first();
                $pmsimage       = (new UploadFile($value))->to($PMSTranslation)->save('content');
                $pmsupdate      = PMSTranslation::where(['lang' => $keyfile[1], 'id' => $keyfile[0]])->update(['content' => $pmsimage->content]);
            }
        }
    }
}
