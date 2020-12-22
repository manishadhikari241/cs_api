<?php

namespace App\Marketplace\Admin;

// use Carbon\Carbon;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{

    protected $table = "currency";

    public $timestamps = false;

    protected $fillable = ['is_active', 'code', 'rate', 'symbol'];
    protected $casts    = ['is_active' => 'boolean'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
    public function translations()
    {
        return $this->hasMany('App\Marketplace\Common\CurrencyTranslation', 'id', 'id');
    }
    public static function autoUpdate ($rates)
    {
        foreach ($rates as $code => $rate) {
            Currency::where('code', $code)->update([ 'rate' => $rate ]);
        }
    }
}
