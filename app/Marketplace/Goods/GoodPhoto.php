<?php

namespace App\Marketplace\Goods;

use Illuminate\Database\Eloquent\Model;

class GoodPhoto extends Model
{
    protected $table = 'good_photo';

    public function getUploadPath()
    {
        return 'uploads/good-photo/';
    }
}
