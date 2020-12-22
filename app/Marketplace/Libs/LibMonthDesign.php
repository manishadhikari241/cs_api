<?php

namespace App\Marketplace\Libs;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use App\Marketplace\Designs\Design;

/*
 * @attribute String level "half" / "full"
 */
class LibMonthDesign extends Model
{
    protected $table    = 'lib_month_design';
    protected $fillable = ['design_id', 'lib_month_id', 'lib_category_id', 'basic', 'pro', 'is_trial'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function libMonth()
    {
        return $this->belongsTo(LibMonth::class);
    }

    public function design()
    {
        return $this->belongsTo(Design::class, 'design_id');
    }

    public function category()
    {
        return $this->belongsTo(LibCategory::class, 'lib_category_id');
    }

    public function libCategory()
    {
        return $this->belongsTo(LibCategory::class, 'lib_category_id');
    }
}
