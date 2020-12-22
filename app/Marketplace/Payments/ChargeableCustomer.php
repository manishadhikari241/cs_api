<?php

namespace App\Marketplace\Payments;

use App\Marketplace\Libs\Customer;

class ChargeableCustomer
{
    public $id;
    public $customer;
    public $csCustomer;
    public $payment_method;
    public $input;

    /*
     * @param $customer mixed gateway customer
     */
    public function __construct($customer = null, Customer $csCustomer, array $input)
    {
        $this->id               = $customer->id ?? $csCustomer->customer_id;
        $this->customer         = $customer;
        $this->csCustomer       = $csCustomer;
        $this->payment_method   = $input['payment_method'] ?? $csCustomer->payment_method;
        $this->input            = $input;
    }

    // get the customers chargeable source / token
    public function getToken()
    {
        return $this->{$this->payment_method}();
    }

    public function credit_card()
    {
        $methods = $this->customer->paymentMethods;
        foreach ($methods as $m) {
            if (method_exists($m, 'isDefault') && $m->isDefault()) {
                return $m->token;
            }
        }
        return $this->customer->paymentMethods[0]->token;
    }

    public function testing()
    {
        return $this->customer->paymentMethods[0]->token;
    }

    public function alipay()
    {
        return $this->input['token'];
    }

    public function wechatpay()
    {
        return $this->input['token'];
    }

    public function telex_transfer()
    {
        return $this->input['token'];
    }

    public function distributor()
    {
        return '';
    }
}
