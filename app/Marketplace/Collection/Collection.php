<?php

namespace App\Marketplace\Collection;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use SoftDeletes;

    protected $table = "collection";

    protected $fillable = [ 'name' ];

    public function colors()
    {
        return $this->hasMany(CollectionColor::class);
    }

    public function items()
    {
        return $this->hasMany(CollectionItem::class);
    }

    public function groups()
    {
        return $this->hasMany(CollectionItemGroup::class);
    }

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function accesses()
    {
        return $this->hasMany(CollectionAccess::class);
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function isGrantedTo($user)
    {
        return $this->accesses()->where('user_id', $user->id)->exists();
    }
}
