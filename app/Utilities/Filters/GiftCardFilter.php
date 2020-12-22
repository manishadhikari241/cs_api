<?php

namespace App\Utilities\Filters;

class GiftCardFilter extends QueryFilter
{

    public function code($code) {
     if($amount){
      return $this->builder->where('price',$amount);
     }
    }
    public function amount($value) {
     if(!$value){
        return null;
     }
      return $this->builder->where('amount',$value);
    }
    
     public function toName($name) {
     if(!$name){
        return null;
     }
      return $this->builder->where('to_name',$name);
    }
     public function toEmail($email) {
     if(!$email){
        return null;
     }
      return $this->builder->where('to_email',$email);
    }

    public function isActive($value) {
     if($value == "true"){
        return $this->builder->where('is_active', true);
     }else{
        return $this->builder->where('is_active', false);
     }
    }

    public function calimed($value)
    {
        if($value == "true"){
            return $this->builder->whereHas('user', null);
        }else{
            return $this->builder->whereDoesntHave('user', null);
        }
    }

    public function used($value)
    {
        if($value == "true"){
            return $this->builder->whereHas('histories', null);
        }else{
            return $this->builder->whereDoesntHave('histories', null);
        }
    }


    public function dateFrom($fromdate){
        if($fromdate){
            return $this->builder->whereDate('created_at','>=',$fromdate);   
        }
    }
    public function dateTo($enddate){
        if($enddate){
           return $this->builder->whereDate('created_at','<=',$enddate);   
        }   
    }
    public function dateBetween($data){
        return $this->builder->WhereDate('created_at','>=',$data['0'])->WhereDate('created_at','<=',$data['1']);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'user'      => 'user',
            'order'     => 'order.user',
            'histories' => 'histories',
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
