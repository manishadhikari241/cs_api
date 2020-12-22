<?php

namespace App\General;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $table = 'contact';

    protected $fillable = [ 'name', 'skype', 'wechat', 'whatsapp', 'qq', 'email', 'region','image' ];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function translations()
    {
        return $this->hasMany(ContactsTranslation::class, 'id', 'id');
    }
}
