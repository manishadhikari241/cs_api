<?php

namespace App\General\Premium;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class PremiumPlan extends Model
{
    protected $table = "premium_plan";

    protected $fillable = ['is_active', 'credit', 'price', 'price_in_advance'];

    public function histories()
    {
        return $this->hasMany(PremiumPlanHistory::class);
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function total()
    {
        return $this->price_in_advance;
    }

    public function translations()
    {
        return $this->hasMany(PremiumPlansTranslation::class, 'id');
    }

    public function subscribe($user, $payment)
    {
        $history = PremiumPlanHistory::forceCreate([
            'status'           => PremiumPlanHistory::ADVANCE_PAID,
            'user_id'          => $user->id,
            'premium_plan_id'  => $this->id,
            'price_in_advance' => $this->total(),
            'transaction_id'   => $payment->transaction_id ?? null,
        ]);
        $user->is_premium      = true;
        $user->premium_plan_id = $this->id;
        $user->save();
        return $history;
        // if ($user->telex) {
        //     $this->telex->forceFill(['capacity' => $this->credit])->save();
        //     // upgrade
        //     return;
        // }
        // $user->telex()->forceCreate([
        //     'user_id'  => $user->id,
        //     'capacity' => $this->credit,
        // ]);
        return;
    }
}
