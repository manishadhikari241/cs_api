<?php

namespace App\Marketplace\Shopping;

use App\User;
use App\Marketplace\Studio\Studio;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table    = 'voucher';
    protected $fillable = ['code', 'amount', 'to_name', 'to_email', 'is_active', 'message', 'order_id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'sf_guard_user_voucher', 'voucher_id', 'user_id');
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class, 'studio_id');
    }

    public function histories()
    {
        return $this->hasMany(VoucherHistory::class);
    }

    public function validate($user_id)
    {
        if ($this->balance() <= 0) {
            abort(422, 'VOUCHER_NO_BALANCE');
        }
        if (!$this->is_active) {
            abort(422, 'VOUCHER_INACTIVE');
        }
        // voucher is not owned
        return $this;
    }

    public function balance()
    {
        return $this->amount - $this->histories->sum('amount');
    }

    public function getUsage($total)
    {
        $balance = $this->balance();
        return ($balance - $total) < 0 ? $balance : $total;
    }

    public function consume($usage, $order)
    {
        $history = new VoucherHistory([
        'order_id' => $order->id,
        'amount'   => $usage,
    ]);
        $this->histories()->save($history);
        return $history;
    }

    public function data($usage)
    {
        return [
            'code'   => $this->code,
            'amount' => $usage,
            'cost'   => $usage
        ];
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
