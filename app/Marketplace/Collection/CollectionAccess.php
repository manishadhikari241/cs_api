<?php

namespace App\Marketplace\Collection;

use Illuminate\Database\Eloquent\Model;

class CollectionAccess extends Model
{
    protected $table = "collection_access";

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public static function grant($user, $collection)
    {
        if (!$user) {throw new \Exception("USER_NOT_FOUND", 1);}
        if ($collection->accesses()->where(['user_id' => $user->id])->exists()) {
            throw new \Exception("ACCESS_ALREADY_GIVEN", 1);
        }
        $access = CollectionAccess::forceCreate([
            'user_id'       => $user->id,
            'collection_id' => $collection->id,
        ]);
        return $access;
    }
}
