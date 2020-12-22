<?php

namespace App\General\Distributor;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    protected $table = 'distributor';

    protected $fillable = ['is_active', 'user_id', 'distributor_group_id'];

    // (my self)
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function group()
    {
        return $this->belongsTo(DistributorGroup::class, 'distributor_group_id');
    }

    public function invoices()
    {
        return $this->hasMany(DistributorInvoice::class);
    }

    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function distributorGroupLog()
    {
        return $this->hasMany(DistributorGroupLog::class, 'distributor_id');
    }

    public function distributorPayments()
    {
        return $this->hasMany(DistributorPayment::class, 'distributor_id');
    }

    public static function initiate($user, $data)
    {
        $distributor = self::where('user_id', $user->id)->first();
        if (!$distributor) {
            $group = DistributorGroup::find($data['distributor_group_id']);
            $distributor = self::forceCreate([
                'user_id' => $user->id,
                'distributor_group_id' => $group->id,
                'code' => self::findUniqueCode(),
            ]);
            DistributorGroupLog::forceCreate([
                'percentage' => $group->percentage,
                'distributor_id' => $distributor->id,
            ]);
        }
        return $distributor;
    }

    public static function findUniqueCode()
    {
        $isUnique = false;
        while (!$isUnique) {
            $code = rand(100000, 999999);
            if (!self::where('code', $code)->exists()) {
                return $code;
            }
        }
    }
}
