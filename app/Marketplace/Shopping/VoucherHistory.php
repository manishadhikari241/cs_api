<?php

namespace App\Marketplace\Shopping;

use App\User;
use Illuminate\Database\Eloquent\Model;

class VoucherHistory extends Model
{

    protected $table = "voucher_history";

    protected $fillable = [ 'order_id', 'amount' ];

    public function voucher () {
      $this->belongsTo(Voucher::class);
    }

    public function order () {
      $this->belongsTo(Order::class);
    }

}