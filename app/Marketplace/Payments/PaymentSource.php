<?php

namespace App\Marketplace\Payments;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentSource extends Model
{
    use SoftDeletes;
    
    protected $table = "payment_source";

    protected $fillable = [ 'token', 'payment_method', 'type', 'address_id', 'lib_plan_id', 'code' ];

    public function user () {
        return $this->belongsTo(User::class);
    }
}
