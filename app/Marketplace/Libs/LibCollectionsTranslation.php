<?php

namespace App\Marketplace\Libs;

use Illuminate\Database\Eloquent\Model;

class LibCollectionsTranslation extends Model
{
    protected $table = 'lib_collection_translation';

    protected $fillable = ['name', 'description', 'lang'];

    public function libCollection()
    {
        return $this->belongsTo(LibCollection::class, 'id');
    }
}
