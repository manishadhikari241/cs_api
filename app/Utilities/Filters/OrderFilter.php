<?php

namespace App\Utilities\Filters;

use App\User;

class OrderFilter extends QueryFilter
{
    public function transactionId($trans_id)
    {
        return $this->builder->where('transaction_id', $trans_id);
    }

    public function user($useremail)
    {
        $userids = User::where('email', 'like', "%{$useremail}%")->pluck('id');
        return $this->builder->where('user_id', $userids[0] ?? 0);
    }

    public function transaction($trans)
    {
        return $this->builder->where('transaction_id', $trans);
    }

    // public function telex($telex)
    // {
    //     // TODO false
    //     if ($telex == 'true') {
    //         return $this->builder->has('telexTransfer');
    //     } else {
    //         return $this->builder->doesntHave('telexTransfer');
    //     }
    // }

    public function dateFrom($fromdate)
    {
        if ($fromdate) {
            return $this->builder->whereDate('created_at', '>=', $fromdate);
        }
    }

    public function dateTo($enddate)
    {
        if ($enddate) {
            return $this->builder->whereDate('created_at', '<=', $enddate);
        }
    }

    public function dateBetween($data)
    {
        return $this->builder->WhereDate('created_at', '>=', $data['0'])->WhereDate('created_at', '<=', $data['1']);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'products'                       => 'products',
            'products.designer'              => 'products.designer',
            'products.designer.profile'      => 'products.designer.profile',
            'sales'                          => 'sales',
            'sales.product'                  => 'sales.product',
            'sales.product.designer.profile' => 'sales.product.designer.profile',
            'sales.product.studio'           => 'sales.product.studio.translations',
            'sales.voucher'                  => 'sales.voucher',
            'permits.voucher'                => 'permits.voucher',
            'permits.studio'                 => 'permits.studio.translations',
            'nation'                         => 'nation.translations',
            'user'                           => 'user',
            // 'telex'                          => 'telexTransfer',
            // 'telexTransfer'                  => 'telexTransfer',
            'representative'                 => 'representativeOrder.representative.user',
            'voucher'                        => 'voucher',
            'coupon'                         => 'coupon',
            'giftCard'                       => 'giftCard',
        ];
        $relations = [];
        foreach ($scopes as $key => $value) {
            if (isset($relatable[$value])) {
                array_push($relations, $relatable[$value]);
            }
        }
        return $this->builder->with($relations);
    }
}
