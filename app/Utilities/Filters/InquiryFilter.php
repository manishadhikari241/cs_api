<?php

namespace App\Utilities\Filters;

use App\User;

class InquiryFilter extends QueryFilter
{
    public function quantity($quantity)
    {
        return $this->builder->where('quantity',$quantity);
    }

    public function user($useremail)
    {
        $userid = User::where('email',$useremail)->pluck('id');
        if(defined($userid)){
            return $this->builder->where('user_id',$userid[0]);
        }
    }

    public function transactionId($id)
    {
        return $this->builder->where('transaction_id', $id);
    }

    public function dateFrom($fromdate){
        if($fromdate){
            return $this->builder->whereDate('created_at', '>=', $fromdate);   
        }
    }

    public function dateTo($enddate){
        if($enddate){
           return $this->builder->whereDate('created_at', '<=', $enddate);   
        }   
    }

    public function dateBetween($data){
        return $this->builder->WhereDate('created_at','>=',$data['0'])->WhereDate('created_at','<=',$data['1']);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'user'       => 'user',
            'purchases'  => 'purchases.product.translations',
            'design'     => 'purchases.design',
            // 'product' => 'product.translations',
            // 'good'    => 'product.translations',
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
