<?php

namespace App\Utilities\Filters;

class PremiumRequestFilter extends QueryFilter
{
	public function studioId($id)
	{
		if (!$id) {return null;}
		return $this->builder->where('studio_id',$id);
    }
   
}
