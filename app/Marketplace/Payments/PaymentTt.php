<?php

namespace App\Marketplace\Payments;

use App\User;
use App\Marketplace\Libs\LibPlan;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTt extends Model
{
    use SoftDeletes;

    protected $table = 'payment_tt';

    protected $fillable = ['user_id', 'lib_plan_id', 'address_id', 'code', 'type', 'amount'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function libPlan()
    {
        return $this->belongsTo(LibPlan::class);
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
