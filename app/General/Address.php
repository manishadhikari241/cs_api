<?php

namespace App\General;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $table = 'sf_guard_user_address';

    protected $fillable = [
      'first_name', 'last_name', 'company', 'vat_number', 'address1', 'address2', 'city', 'country', 'is_default', 'post_code'
    ];

    protected $casts = [
      'is_default' => 'boolean'
    ];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function user()
    {
        return $this->belongsTo(App\User::class);
    }

    public function nation()
    {
        return $this->belongsTo('App\Marketplace\Common\Country', 'country', 'id');
    }

    public function scopeDefault($query)
    {
        $query->where('is_default', true);
    }
}
