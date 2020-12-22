<?php

namespace App\Marketplace\Libs;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class LibInspiration extends Model
{
    protected $table    = 'lib_inspiration';
    protected $fillable = ['is_active', 'lib_month_id', 'lib_category_id', 'embassador_id', 'moodboard', 'mobile_moodboard'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function translations()
    {
        return $this->hasMany(LibInspirationsTranslation::class, 'id', 'id');
    }

    public function moodboards()
    {
        return $this->hasMany(LibInspirationsMoodboard::class);
    }

    public function embassador()
    {
        return $this->belongsTo(Embassador::class);
    }

    public function designs()
    {
        return $this->hasMany(LibMonthDesign::class, 'lib_month_id', 'lib_month_id');
    }

    public function month()
    {
        return $this->belongsTo(LibMonth::class, 'lib_month_id');
    }

    public function category()
    {
        return $this->belongsTo(LibCategory::class, 'lib_category_id');
    }

    // public function getUploadPath()
    // {
    //     return 'uploads/lib/inspiration/';
    // }
}
