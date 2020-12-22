<?php

namespace App\Marketplace\Admin;

// use Carbon\Carbon;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class VoucherPrice extends Model
{

    protected $table = "voucher_price";

    public $timestamps = false;

    protected $casts = ['is_active' => 'boolean', 'price'];

    protected $fillable = ['is_active', 'price'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

}
