<?php

namespace App\General;

use Illuminate\Database\Eloquent\Model;

class CreatorGroup extends Model
{

    protected $table = "sf_guard_user_creator_group";

    public function logs () {
      return $this->hasMany(CreatorGroupLog::class, 'percentage', 'percentage');
    }

    public function commission ($price) {
      return $price * (float)$this->percentage / 100;
    }

    public function creatorFee ($price) {
      return $price - $this->commission($price);
    }

}
