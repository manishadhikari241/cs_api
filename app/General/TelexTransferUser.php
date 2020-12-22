<?php

namespace App\General;

use Illuminate\Database\Eloquent\Model;

class TelexTransferUser extends Model
{
    protected $table = "telex_transfer_user";

    protected $fillable = [ 'is_active', 'capacity' ];

    public function user() {
      return $this->belongsTo('App\User');
    }
    public function transfers () {
      return $this->hasMany('App\Marketplace\Payments\Gateways\TelexTransfer', 'telex_user_id');
    }
    public function outstanding () {
      $outstandings = $this->transfers()->outstanding()->with('order')->get();
      // dd($this->transfers()->get());
      $orders = $outstandings->pluck('order');
      return $orders->sum('total');
    }
}
