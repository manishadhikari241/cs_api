<?php

namespace App\General\CMS;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;

class Uploads extends Model
{
    protected $table = "cms_uploads";
    protected $fillable = ['id','name'];

    public function getUploadPath()
    {
        return "/uploads/cms/";
    }
}