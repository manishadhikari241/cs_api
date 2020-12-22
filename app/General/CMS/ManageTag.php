<?php

namespace App\General\CMS;

use App\Marketplace\Common\TagsTranslation;

class ManageTag
{
    public function updateName($tag, $names = [])
    {
        foreach ($names as $key => $value) {
            $name        = TagsTranslation::tagify($value);
            $translations = $tag->translations()->where('id', $tag->id)->where('lang', $key);
            if ($key == 'zh-CN' || $key == 'zh-HK') {
                $name = TagsTranslation::removeChineseSpace($name);
            }
            if ($translations->count() == 0) {
                $tag->translations()->save(new TagsTranslation(['id' => $tag->id, 'name' => $name, 'lang' => $key]));
            } else {
                TagsTranslation::where(['id' => $tag->id, 'lang' => $key])->update(['name' => $value]);
            }
        }
    }

}
