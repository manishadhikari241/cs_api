<?php

namespace App\Marketplace\Payments;

use App\Marketplace\Payments\Gateways\Pos;
use App\Setting;

class PosAdmin
{

    public function __construct($adminUser)
    {
        $this->admin = $adminUser;
    }

    public function pay($pos, $data)
    {
        if (!in_array($pos->status, [Pos::CREATED])) {
            abort(422, 'POS_NOT_PAYABLE');
        }
        $pos->cashier()->associate($this->admin);
        $pos->status     = (int) $data['value'] > (int) Setting::key('max_cashier_amount') ? Pos::NEED_SECOND_AUTH : Pos::AUTHORIZED;
        $pos->value      = $data['value'];
        $pos->expired_at = $data['expired_at'] ?? null;
        $pos->save();

        return $pos;
    }

    public function secondAuth($pos, $data)
    {
        if (!in_array($pos->status, [Pos::CREATED, Pos::NEED_SECOND_AUTH, Pos::AUTHORIZED])) {
            abort(422, 'POS_NOT_AUTHENTICATABLE');
        }
        // check must be in manager list
        $pos->manager()->associate($this->admin);
        $pos->status = Pos::AUTHORIZED;
        $pos->value  = $data['value'];
        $pos->save();

        return $pos;
    }

}
