<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Quota extends Model {

    public $timestamps = false;

    public static function createEmpty($userId) {
        $quota = new Quota();
        $quota->user_id = $userId;
        $quota->standard = 0;
        $quota->extended = 0;
        $quota->simulator = 0;
        $quota->exclusive = 0;
        $quota->standard_expiry = Carbon::NOW();
        $quota->extended_expiry = Carbon::NOW();
        $quota->simulator_expiry = Carbon::NOW();
        $quota->exclusive_expiry = Carbon::NOW();
        return $quota;
    }

}
