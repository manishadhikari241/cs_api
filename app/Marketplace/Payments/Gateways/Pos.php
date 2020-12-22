<?php

namespace App\Marketplace\Payments\Gateways;

use Carbon\Carbon;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Pos extends Model
{
    const CREATED          = 0;
    const NEED_SECOND_AUTH = 1;
    const AUTHORIZED       = 2;
    const CONSUMED         = 3;
    const EXPIRED          = 7;
    const REJECT           = 8;
    const DELETED          = 9;

    protected $table = 'pos';

    protected $fillable = ['expired_at', 'is_active'];

    /* end Consumer */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function order()
    {
        return $this->belongsTo('App\Marketplace\Shopping\Order');
    }

    public function cashier()
    {
        return $this->belongsTo('App\User', 'cashier_id');
    }

    /* Second auth manager id */
    public function manager()
    {
        return $this->belongsTo('App\User', 'manager_id');
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = sprintf("%06d", $value);
    }

    public function validateInput($order, $input)
    {
        if ((float) $order->total !== (float) $this->value) {
            abort(422, 'POS_VALUE_MISTACH');
        }
        if ($input['token'] !== $this->code) {
            abort(422, 'POS_CODE_MISTACH');
        }
        if (!$this->is_active) {
            abort(422, 'POS_RECORD_INACTIVE');
        }
        if ($this->expired_at && $this->expired_at < Carbon::now()->toDateTimeString()) {
            abort(422, 'POS_RECORD_EXPIRED');
        }
    }

    public function settle($order, $input)
    {
        $this->validateInput($order, $input);

        $this->forceFill([
            'is_active'      => 0,
            'status'         => self::CONSUMED,
            'consumed_at'    => Carbon::now()->toDateTimeString(),
            'transaction_id' => str_random(4) . "-" . $this->code,
            'method'         => $input['method'] ?? 'default',
        ])->save();

        return (object) [
            'transaction_id' => $this->transaction_id,
            'finalize'       => $this,
        ];
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

}
