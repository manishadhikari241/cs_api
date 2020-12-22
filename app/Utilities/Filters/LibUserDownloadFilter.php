<?php

namespace App\Utilities\Filters;

class LibUserDownloadFilter extends QueryFilter
{
    public function isActive(bool $bool)
    {
        $this->builder->where('is_active', $bool);
    }

    public function package($package)
    {
        if ($package !== 'all') {
            if ($package == 'extended')
                $this->builder->where('package', $package)->orWhereNull('package');
            else
                $this->builder->where('package', $package);
        }
    }
}
