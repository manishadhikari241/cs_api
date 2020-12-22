<?php

namespace App\General\Premium;

use Illuminate\Database\Eloquent\Model;
use App\Marketplace\Studio\Studio;

class ProjectPackage extends Model
{
    protected $table = 'project_package';

    protected $fillable = ['is_active', 'price', 'expected_quantity', 'expected_days', 'expiry_days', 'max_revision', 'has_moodboard'];

    protected $casts = [
        'transaction_data' => 'json',
        'package_data'     => 'json'
    ];

    public function total()
    {
        return $this->price;
    }

    public function createPayment($user, $transaction)
    {
        $commission                  = $this->commission();

        $payment                       = new ProjectPayment;
        $payment->project_package_id   = $this->id;
        $payment->user_id              = $user->id;
        $payment->studio_id            = $this->studio_id;
        $payment->price                = $this->price;
        $payment->commission_fee       = $commission;
        $payment->creator_fee          = $this->price - $commission;
        $payment->transaction_id       = $transaction->transaction_id;
        $payment->payment_method       = $transaction->payment_method;
        // $payment->transaction_data     = json_encode($transaction);
        // $payment->package_data         = $this->toJson();
        $payment->transaction_data     = $transaction;
        $payment->package_data         = $this;
        $payment->save();
        return $payment;
    }

    public function commission()
    {
        $group = $this->studio->user->profile->creatorGroup;
        return $this->price * ($group->percentage / 100);
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }
}
