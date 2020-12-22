<?php

namespace App\Marketplace\Shopping;

use Auth;
use App\Marketplace\Designs\Design;

trait CartValidations
{
    public function validateCheckout($total = 0)
    {
        $err = null;
        if ($this->cartCoupon) {
            $coupon = $this->cartCoupon->coupon;
            if (!$this->verifyMinTotal($coupon, $total)) {
                $this->cartCoupon->delete();
                $err = 'MIN_TOTAL_REQUIRED';
            }
            if (!$this->verifyStudioDesigns($coupon, $this->designs)) {
                $err = 'INVALID_STUDIO_DESIGNS';
            }
            try {
                $this->verifyCoupon($coupon);
            } catch (\Exception $e) {
                $this->cartCoupon->delete();
                $err = $e->getMessage();
            }
        }

        if ($this->cartVoucher) {
            $merged = $this->designs->merge($this->licences);
            if (!$this->verifyStudioDesigns($this->cartVoucher->voucher, $merged)) {
                $err = 'INVALID_STUDIO_DESIGNS';
            }
        }

        if ($this->designs) {
            foreach ($this->designs as $design) {
                try {
                    $this->verifyDesign($design);
                } catch (\Exception $e) {
                    // find the cart design and delete
                    $this->cartDesign->where('type', 'product')->where('item', $design->id)->first()->delete();
                    $err = $e->getMessage();
                }
            }
        }

        if ($this->licences) {
            foreach ($this->licences as $licence) {
                try {
                    $this->verifyLicence($licence);
                } catch (\Exception $e) {
                    // find the cart licence and delete
                    $this->cartLicence->where('type', 'product-licence')->where('item', $licence->id)->first()->delete();
                    $err = $e->getMessage();
                }
            }
        }
        if (!$this->designs->count() && !$this->licences->count() && !$this->cards->count() && !$this->permits->count()) {
            $err = 'EMPTY_CHECKOUT';
        }

        if ($err) {
            abort(422, $err);
        }
        return true;
    }

    // $coupon mixed, int / Object
    protected function verifyCoupon($coupon)
    {
        $coupon = $coupon instanceof Coupon ? $coupon : Coupon::where('code', $coupon)->first();
        // dd($coupon->fresh());
        if (!$coupon) {
            abort(404, 'COUPON_NOT_FOUND');
        }
        $coupon->validate(Auth::id());
        return $coupon;
    }

    // $coupon mixed, int / Object
    protected function verifyMinTotal($coupon, $total)
    {
        if ($coupon->min_total && $total < $coupon->min_total) {
            \Log::info('cart value', [$coupon->min_total, $total, $coupon->min_total, $total < $coupon->min_total]);
            return false;
        }
        return true;
    }

    // $instance Object - Coupon / Voucher
    protected function verifyStudioDesigns($instance, $designs)
    {
        if (!$instance->studio_id) {
            return true;
        }
        if (!$designs) {
            return false;
        }

        $designsActualCount = $designs->where('studio_id', $instance->studio_id)->count();
        return $designsActualCount === $designs->count();
    }

    // $coupon mixed, int / Object
    protected function verifyDesign($design)
    {
        $design = $design instanceof Design ? $design : Design::find($design);
        $this->ensureNotYourOwnDesign($design);
        $this->ensureDesignAvailable($design);
        $this->ensureNotLicensing($design);
        return true;
    }

    // $coupon mixed, int / Object
    protected function verifyLicence($licence)
    {
        $licence = $licence instanceof Design ? $licence : Design::find($licence);
        $this->ensureNotYourOwnDesign($licence);
        $this->ensureLicenseAvailable($licence);
        $this->ensureNotInYourOrder($licence);
        return true;
    }

    protected function ensureNotYourOwnDesign($design)
    {
        if ($design->designer_id === \Auth::id()) {
            abort(422, 'CANNOT_ADD_OWN_DESIGN');
        }
    }

    protected function ensureDesignAvailable($design)
    {
        $this->ensureDesignNotSold($design);
        $this->ensureDesignNotRejected($design);
        $this->ensureDesignNotDownloadOnly($design);
        $this->ensureDesignNotLibrary($design);
        if (!in_array($design->licence_type, ['all', 'exclusive'])) {
            abort(422, 'DESIGN_NOT_ALLOW_EXCLUSIVE');
        }
    }

    protected function ensureLicenseAvailable($design)
    {
        $this->ensureDesignNotSold($design);
        $this->ensureDesignNotRejected($design);
        $this->ensureDesignNotDownloadOnly($design);
        $this->ensureDesignNotLibrary($design);
        if (!in_array($design->licence_type, ['all', 'non-exclusive'])) {
            abort(422, 'DESIGN_NOT_ALLOW_LICENSE');
        }
    }

    protected function ensureNotInYourOrder($design)
    {
        if (Sales::where('product_id', $design->id)->whereHas('order', function ($query) {
            return $query->where('user_id', Auth::id());
        })->exists()) {
            abort(422, 'CANNOT_LICENSE_AGAIN');
        }
    }

    protected function ensureDesignNotSold($design)
    {
        if ($design->status === Design::IS_SOLD || $design->buyer_id) {
            abort(422, 'DESIGN_SOLD_OUT');
        }
    }

    protected function ensureDesignNotRejected($design)
    {
        if (in_array($design->status, [Design::IS_REJECTED])) {
            abort(422, 'CANNOT_ADD_REJECTED_DESIGN');
        }
    }

    protected function ensureDesignNotDownloadOnly($design)
    {
        if ($design->status === Design::IS_DOWNLOAD_ONLY) {
            abort(422, 'DESIGN_DOWNLOAD_ONLY');
        }
    }

    protected function ensureDesignNotLibrary($design)
    {
        if ($design->status === Design::IS_LIBRARY_ONLY) {
            abort(422, 'DESIGN_LIBRARY_ONLY');
        }
    }

    protected function ensureNotLicensing($product)
    {
        if ($product->is_licensing) {
            abort(422, 'PRODUCT_LICENSE_ONLY');
        }
    }
}
