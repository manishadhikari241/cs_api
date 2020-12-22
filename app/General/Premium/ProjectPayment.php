<?php

namespace App\General\Premium;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class ProjectPayment extends Model
{
    protected $table = 'project_payment';

    const IS_PENDING    = 0;
    const IS_CREATED    = 1;
    const IS_ACCEPTED   = 2;
    // const IS_REJECTED   = 8; // it will direct refund
    const IS_REFUNDED   = 9;

    // protected $fillable = ['is_active', 'price', 'expected_quantity', 'expected_days', 'max_revision', 'has_moodboard'];
    protected $casts = [
        'transaction_data' => 'json',
        'package_data'     => 'json'
    ];

    public function updateAddress($address)
    {
        $this->address_id = $address->id;
        $this->save();
        return $this;
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function address()
    {
        return $this->belongsTo('App\General\Address');
    }

    public function request()
    {
        return $this->belongsTo('App\General\Premium\ProjectRequest', 'project_request_id');
    }

    public function projectPackage()
    {
        return $this->belongsTo(ProjectPackage::class);
    }

    public function studio()
    {
        return $this->belongsTo('App\Marketplace\Studio\Studio');
    }
}
