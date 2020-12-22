<?php

namespace App\General;

class ManageContact
{
    public function updateTranslations($contact, $field, $titles = [])
    {
        foreach ($titles as $lang => $value) {
            $translations = $contact->translations()->where('id', $contact->id)->where('lang', $lang);
            if ($translations->count() == 0) {
                ContactsTranslation::forceCreate(['id' => $contact->id, $field => $value, 'lang' => $lang]);
            } else {
                ContactsTranslation::where(['id' => $contact->id, 'lang' => $lang])->update([$field => $value]);
            }
        }
    }
}
