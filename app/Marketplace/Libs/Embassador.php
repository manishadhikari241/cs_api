<?php

namespace App\Marketplace\Libs;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Embassador extends Model
{
    protected $table = 'embassador';

    public function translations()
    {
        return $this->hasMany(EmbassadorsTranslation::class, 'id');
    }

    public function inspirations()
    {
        return $this->hasMany(LibInspiration::class);
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
