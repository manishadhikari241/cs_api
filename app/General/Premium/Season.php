<?php

namespace App\General\Premium;

use App\Utilities\Filters\QueryFilter;
use App\Marketplace\Libs\LibCollection;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    const IS_WAITING_APPROVAL = 1;
    const IS_STARTED          = 2;
    const IS_COMPLETED        = 3;
    const IS_EXPIRED          = 7;
    const IS_REJECTED         = 8;
    const IS_DELETED          = 9;

    protected $table = 'season';

    protected $fillable = ['expired_at', 'is_active'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function collection()
    {
        return $this->hasOne(LibCollection::class);
    }

    public function trends()
    {
        return $this->hasMany(Trend::class);
    }

    public function translations()
    {
        return $this->hasMany(SeasonsTranslation::class, 'id');
    }
}
