<?php

namespace App\Marketplace\Goods;

use Illuminate\Database\Eloquent\Model;

class GoodPrice extends Model
{
    protected $table = 'good_price';

    protected $fillable = ['unit_price', 'min_unit'];
}
