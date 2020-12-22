<?php

namespace App\Marketplace\Shopping;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class VoucherPrice extends Model
{
    protected $table    = "voucher_price";
    protected $fillable = ["is_active", "price"];
    public $timestamps  = false;
    protected $casts    = ['is_active' => 'boolean'];
    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

}
