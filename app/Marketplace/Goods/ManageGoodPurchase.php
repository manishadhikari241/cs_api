<?php

namespace App\Marketplace\Goods;

use App\Marketplace\Designs\Design;
use App\Marketplace\Goods\Good;
use App\Marketplace\Goods\GoodPurchase;
use App\Utilities\Emails\Email;

class ManageGoodPurchase
{
    public function store($input)
    {
        $design = $this->getDesign($input);
        // each available
        $purchases = $input['purchases'];
        foreach ($purchases as $key => $value) {
            $purchases[$key] = json_decode($value);
            $this->goodMustBePurchasable($purchases[$key]->good_id);
            $design = isset($purchases[$key]->design_id) ? Design::find($purchases[$key]->design_id) : Design::code($purchases[$key]->code);
            $purchases[$key]->design_id = $design->id;
            $this->designMustBeAvaliable($design);
        }
        $inquiry = Inquiry::forceCreate([
            'name'       => $input['name'],
            'surname'    => $input['surname'],
            'country'    => $input['country'],
            'message'    => $input['message'] ?? null,
            'quantity'   => $input['quantity'],
            'country'    => $input['country'] ?? null,
            'contact_no' => $input['contact_no'],
            'user_id'    => \Auth::id(),
            'status'     => Inquiry::IS_PENDING,
        ]);
        foreach ($purchases as $purchase) {
            $goodPurchase = GoodPurchase::forceCreate([
                'good_id'    => $purchase->good_id,
                'design_id'  => $purchase->design_id,
                'inquiry_id' => $inquiry->id,
            ]);
        }
        (new Email('inquiry-created'))->send(\Auth::user(), ['inquiry_id' => $inquiry->id]);
        return $inquiry;
    }
    public function update($input, $id)
    {
        $design = $this->getDesign($input);
        if ($design) {
            $input['design_id'] = $design->id;
            $this->designMustBeAvaliable($design);
        }
        $purchase = GoodPurchase::find($id);
        if (isset($input['style'])) {$input['is_tuned'] = true;}
        $purchase->update($input);
        return $purchase;
    }
    protected function getDesign($input)
    {
        if (!isset($input['design_id'])) {
            return isset($input['code']) ? Design::code($input['code']) : null;
        }
        return Design::find($input['design_id']);
    }
    protected function goodMustBePurchasable($id)
    {
        if (!Good::find($id)->is_purchasable) {abort(422, 'PRODUCT_NOT_PURCHASABLE');}
    }
    protected function designMustBeAvaliable($design)
    {
        if ($design->status === Design::IS_SOLD && $design->buyer_id !== \Auth::id()) {abort(422, 'DESIGN_NOT_AVAILABLE');}
    }
}
