<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Redis;

class Stats
{
    public function __call($method, $args)
    {
        return call_user_func_array([Redis::connection('stats'), $method], $args);
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array([Redis::connection('stats'), $method], $args);
    }
}
