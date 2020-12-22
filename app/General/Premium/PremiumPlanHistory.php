<?php

namespace App\General\Premium;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class PremiumPlanHistory extends Model
{
    protected $table = "premium_plan_history";

    const CREATED      = 0;
    const ADVANCE_PAID = 1;
    const FULL_PAID    = 2;
    const IS_EXPIRED   = 7;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function plan()
    {
        return $this->belongsTo(PremiumPlan::class, 'premium_plan_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function confirm(array $opt)
    {
        if ($this->status === self::FULL_PAID) {
            return;
        }
        $this->status = self::FULL_PAID;
        $this->save();
        PremiumCredit::forceCreate([
            "user_id"        => $this->user_id,
            "value"          => $this->plan->credit,
            "status"         => 2,
            "transaction_id" => $opt['transaction_id'],
        ]);
        return $this;
    }
}
