<?php

namespace App\Marketplace\Libs;

use App\General\Premium\Season;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class LibCollection extends Model
{
    protected $table = 'lib_collection';

    protected $fillable = ['expired_at', 'is_active', 'season_id', 'embassador_id'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function translations()
    {
        return $this->hasMany(LibCollectionsTranslation::class, 'id');
    }

    public function embassador()
    {
        return $this->belongsTo(Embassador::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
