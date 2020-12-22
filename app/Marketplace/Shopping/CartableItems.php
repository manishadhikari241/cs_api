<?php

namespace App\Marketplace\Shopping;

use App\Marketplace\Designs\Design;
use Auth;

trait CartableItems
{
    public $carts;
    public $cartCoupon;
    public $cartVoucher;
    public $permits;
    protected $cards;
    protected $cartDesign;
    protected $designs;
    protected $cartLicence;
    protected $licences;

    use CartValidations {
        CartValidations::verifyCoupon as validateCoupon;
        CartValidations::verifyDesign as validateDesign;
        CartValidations::verifyLicence as validateLicence;
        CartValidations::verifyMinTotal as validateMinTotal;
    }

    /* Called by parent class to init cart items data */
    public function constructCartables($filter = null)
    {
        $this->carts = Auth::user()->carts();

        $this->carts = $filter ? $this->carts->filter($filter)->get() : $this->carts->get();

        $this->cards = $this->carts->filter(function ($cart) {
            return $cart->type === 'card';
        });

        $this->cartCoupon = $this->carts->filter(function ($cart) {
            return $cart->type === 'coupon';
        })->first();

        if ($this->cartCoupon) {
            $this->cartCoupon->load('coupon');
        }

        $this->cartVoucher = $this->carts->filter(function ($cart) {
            return $cart->type === 'voucher';
        })->first();

        if ($this->cartVoucher) {
            $this->cartVoucher->load('voucher');
        }

        $this->cartDesign = $this->carts->filter(function ($cart) {
            return $cart->type === 'product';
        });

        $this->cartLicence = $this->carts->filter(function ($cart) {
            return $cart->type === 'product-licence';
        });

        $this->permits = $this->carts->filter(function ($cart) {
            return $cart->type === 'permit';
        });

        if ($this->cartDesign) {
            $this->designs = Design::whereIn('id', $this->cartDesign->pluck('item'))->with(['designer.profile.creatorGroup', 'studio.translations'])->get();
        }
        if ($this->cartLicence) {
            $this->licences = Design::whereIn('id', $this->cartLicence->pluck('item'))->with(['designer.profile.creatorGroup', 'studio.translations'])->get();
        }
    }

    public function cartableStore($input)
    {
        switch ($input['type']) {
            case 'product':
                return $this->putProduct($input);
                break;
            case 'product-licence':
                return $this->putLicense($input);
                break;
            case 'card':
                return $this->putCard($input);
                break;
            case 'coupon':
                return $this->putCoupon($input);
                break;
            case 'voucher':
                return $this->putVoucher($input);
                break;
            case 'permit':
                return $this->putPermit($input);
                break;
            default:
                abort(422, 'INVALID_TYPE');
                break;
        }
    }

    public function putProduct($input)
    {
        $data = [
            'user_id' => Auth::id(),
            'type'    => 'product',
            'item'    => $input['item'],
        ];
        $this->ensureNotExistsInCart($data);
        if ($this->validateDesign($data['item'])) {
            return $this->updateProductType('product', $input) ?: MemberCart::forceCreate($data);
        }
        return null;
    }

    public function putLicense($input)
    {
        $data = [
            'user_id' => Auth::id(),
            'type'    => 'product-licence',
            'item'    => $input['item'],
        ];
        $this->ensureNotExistsInCart($data);
        if ($this->validateLicence($data['item'])) {
            return $this->updateProductType('product-licence', $input) ?: MemberCart::forceCreate($data);
        }
        return null;
    }

    public function updateProductType($type, $input)
    {
        $oldType    = $type === 'product-licence' ? 'product' : 'product-licence';
        $memberCart = MemberCart::where([
            'type'    => $oldType,
            'item'    => $input['item'],
            'user_id' => Auth::id(),
        ])->first();
        if ($memberCart) {
            $memberCart->update(['type' => $type]);
            return $memberCart;
        }
        return null;
    }

    public function putCard($input)
    {
        $data = [
            'user_id'  => Auth::id(),
            'type'     => 'card',
            'item'     => $input['item'],
            'message'  => $input['message'],
            'to_email' => $input['to_email'],
            'to_name'  => $input['to_name'],
        ];
        return MemberCart::forceCreate($data);
    }

    public function putCoupon($input)
    {
        $coupon    = $this->validateCoupon($input['item']);
        if (!$this->validateMinTotal($coupon, $this->subTotal())) {
            abort(422, 'MIN_TOTAL_REQUIRED');
        }
        $oldCoupon = Auth::user()->carts()->where('type', 'coupon')->first();
        if (!$oldCoupon) {
            return MemberCart::forceCreate([
                'user_id' => Auth::id(),
                'type'    => 'coupon',
                'item'    => $coupon->id,
            ]);
        }
        $oldCoupon->item = $coupon->id;
        $oldCoupon->save();
        return $oldCoupon;
    }

    public function putVoucher($input)
    {
        // current cart sum must be bigger than 0
        $voucher = Voucher::where('code', $input['item'])->first();
        if (!$voucher) {
            abort(404, 'VOUCHER_NOT_FOUND');
        }
        $voucher->validate(Auth::id());
        // replace last voucher
        $data = [
            'user_id' => Auth::id(),
            'type'    => 'voucher',
            'item'    => $voucher->id,
        ];

        Auth::user()->vouchers()->syncWithoutDetaching([$voucher->id]);
        $oldVoucher = Auth::user()->carts()->where('type', 'voucher')->first();
        if (!$oldVoucher) {
            return MemberCart::forceCreate($data);
        }
        $oldVoucher->item = $voucher->id;
        $oldVoucher->save();
        return $oldVoucher;
    }

    public function putPermit($input)
    {
        $oldCoupon = Auth::user()->carts()->where('type', 'permit')->where('studio_id', $input['studio_id'])->first();
        if (!$oldCoupon) {
            return MemberCart::forceCreate([
                'user_id'      => Auth::id(),
                'type'         => 'permit',
                'item'         => $input['item'],
                'studio_id'    => $input['studio_id'],
            ]);
        }
        $oldCoupon->item = $input['item'];
        $oldCoupon->save();

        return $oldCoupon;
    }

    public function ensureNotExistsInCart($data)
    {
        // make sure that product license and design not put together
        if (MemberCart::where($data)->first()) {
            abort(422, 'ITEM_ALREADY_ADDED');
        }
    }

    public function ensureAddressExists($id)
    {
        $address = Auth::user()->addresses()->find($id);
        if (!$address) {
            abort(422, 'ADDRESS_NOT_FOUND');
        }
        return $address;
    }
}
