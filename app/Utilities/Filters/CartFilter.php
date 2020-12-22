<?php

namespace App\Utilities\Filters;

use App\Marketplace\Common\TagsTranslation;
use App\Marketplace\Designs\ColorsTranslation;

class CartFilter extends QueryFilter
{
    public function scope($scopes = [])
    {
        $relatable = [
            'product' => 'product',
            'product.designer' => 'product.designer',
            'product.designer.profile' => 'product.designer.profile',
            'product.studio' => 'product.studio.translations',
        ];
        $relations = [];
        foreach ($scopes as $key => $value) {
            if (isset($relatable[$value])) {
                array_push($relations, $relatable[$value]);
            }
        }
        return $this->builder->with($relations);
    }
    public function createdFrom($date)
    {
        if ($date) {
            return $this->builder->whereDate('member_cart.created_at', '>=', $date);
        }
    }
    public function createdTo($date)
    {
        if ($date) {
            return $this->builder->whereDate('member_cart.created_at', '<=', $date);
        }
    }
    public function createdBetween($data)
    {
        return $this->builder->whereDate('member_cart.created_at', '>=', $data['0'])->whereDate('member_cart.created_at', '<=', $data['1']);
    }
}