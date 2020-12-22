<?php

namespace App\Marketplace\Shopping;

use Auth;
use Carbon\Carbon;
use App\Utilities\Emails\Email;
use App\Marketplace\Designs\Design;
use App\Marketplace\Studio\StudioPermit;
use App\General\Representative\Representative;
use App\General\Representative\RepresentativeOrder;
use App\Marketplace\Payments\Gateways\Gateway as PaymentGateway;

class CartsManager
{
    /* This traits also get all member carts in __construct */
    use CartableItems;

    public function __construct($filter = null)
    {
        $this->constructCartables($filter);
    }

    public function store($input)
    {
        return $this->cartableStore($input);
    }

    /** delete all items at once **/
    public function destroy()
    {
        $this->carts->each(function ($cart) {
            $cart->delete();
        });
        return $this->carts;
    }

    public function checkoutVerification()
    {
        $subTotal     = $this->subTotal();
        return $this->validateCheckout($subTotal);
    }

    public function checkout($info = [])
    {
        $address      = $this->ensureAddressExists($info['address_id']);
        $subTotal     = $this->subTotal();
        $this->validateCheckout($subTotal);
        $discount     = $this->discount($subTotal);
        $grandTotal   = $this->grandTotal($subTotal, $discount);
        $usage        = $this->getUsage($grandTotal);
        $shouldCharge = $this->total($grandTotal, $usage);
        $method       = $info['payment_method'] ?? 'credit_card';

        $order                 = new Order();
        $order->user_id        = Auth::id();
        $order->total          = $shouldCharge;
        $order->currency       = 'USD';
        $order->payment_method = $shouldCharge ? $method : 'free_checkout';
        $order->coupon_id      = $this->cartCoupon ? $this->cartCoupon->coupon->id : null;
        $order->coupon_data    = $this->cartCoupon ? json_encode($this->cartCoupon->coupon->data($discount)) : null;
        $order->voucher_id     = $this->cartVoucher ? $this->cartVoucher->voucher->id : null;
        $order->voucher_data   = $this->cartVoucher ? json_encode($this->cartVoucher->voucher->data($usage)) : null;

        $order->payment_first_name = $address->first_name;
        $order->payment_last_name  = $address->last_name;
        $order->payment_country    = $address->country;
        $order->payment_city       = $address->city;
        $order->payment_address1   = $address->address1;
        $order->payment_address2   = $address->address2;
        $order->payment_post_code  = $address->post_code;
        $order->payment_company    = $address->company;
        $order->payment_vat_code   = $address->vat_number;

        $payment = PaymentGateway::via($order['payment_method'])->settle($order, $info);

        $order->transaction_id = $payment->transaction_id;
        $order->card_tail      = $payment->card_tail  ?? null;
        $order->card_brand     = $payment->card_brand ?? null;

        $order->save();

        if (isset($payment->finalize)) {
            // if the payment method can associate order, save it
            $payment->finalize->order()->associate($order)->save();
        }

        if ($usage) {
            $history = $this->cartVoucher->consume($usage, $order);
        }

        if ($discount) {
            $history = $this->cartCoupon->apply($discount, $order);
        }

        return $this->handleSuccessCheckout($order);
    }

    /* add up sub total */
    public function subTotal()
    {
        $designs  = $this->designs ? $this->designs->sum('price') : 0;
        $licences = $this->licences ? $this->licences->sum('licence_price') : 0;
        $permits  = $this->permits ? $this->permits->sum('item') : 0;
        $cards    = $this->cards->sum('item');
        return $designs + $licences + $cards + $permits;
        // return $this->discount($this->cartCoupon, $subTotal);
    }

    public function grandTotal($subTotal = null, $discount = null)
    {
        if (!$subTotal) {
            $subTotal = $this->subTotal();
        }
        if (!$discount) {
            $discount = $this->discount($subTotal);
        }
        return $subTotal - $discount;
    }

    /* apply coupon, if any */
    public function discount($subTotal)
    {
        return $this->cartCoupon ? $this->cartCoupon->getDiscount($subTotal) : 0;
    }

    /* last: apply voucher usage */
    public function total($grandTotal = null, $usage = null)
    {
        if (!$grandTotal) {
            $grandTotal = $this->grandTotal();
        }
        if (!$usage) {
            $usage = $this->getUsage($grandTotal);
        }
        return $shouldCharge = $grandTotal - $usage;
    }

    public function getUsage($subTotal = null)
    {
        if (!$subTotal) {
            $subTotal = $this->subTotal();
        }
        return $this->cartVoucher ? $this->cartVoucher->getUsage($subTotal) : 0;
    }

    protected function handleSuccessCheckout($order)
    {
        // @todo send email here, cc admin
        if ($this->designs) {
            $this->designs->map(function ($design) use (&$order) {
                $createrGroup         = $design->designer->profile->creatorGroup;
                $design->buyer_id     = $order->user_id;
                $design->owner_id     = $order->user_id;
                $design->status       = Design::IS_SOLD;
                $design->purchased_at = Carbon::now()->toDateTimeString();
                $design->earned       = $design->earned + $createrGroup->creatorFee($design->price);
                $design->save();
                $sales = Sales::forceCreate([
                    'order_id'    => $order->id,
                    'product_id'  => $design->id,
                    'owner_id'    => $order->user_id,
                    'price'       => $design->price,
                    'type'        => 'product',
                    'commission'  => $createrGroup->commission($design->price),
                    'creator_fee' => $createrGroup->creatorFee($design->price),
                ]);
                try {
                    (new Email('design-sold'))->send($design->designer, ['sales_id' => $sales->id]);
                } catch (\Exception $e) {
                    \Log::error('Design Sold cannot be sent', $order->toArray());
                }
            });
        }

        if ($this->licences) {
            $this->licences->map(function ($design) use (&$order) {
                $createrGroup         = $design->designer->profile->creatorGroup;
                $design->is_licensing = true;
                $design->licence_type = Design::licence_type[2];
                $design->earned       = $design->earned + $createrGroup->creatorFee($design->licence_price);
                $design->save();
                $sales = Sales::forceCreate([
                    'order_id'    => $order->id,
                    'product_id'  => $design->id,
                    'owner_id'    => $order->user_id,
                    'price'       => $design->licence_price,
                    'type'        => 'product-licence',
                    'commission'  => $createrGroup->commission($design->licence_price),
                    'creator_fee' => $createrGroup->creatorFee($design->licence_price),
                ]);
                try {
                    (new Email('design-sold'))->send($design->designer, ['sales_id' => $sales->id]);
                } catch (\Exception $e) {
                    \Log::error('Design Sold cannot be sent', $order->toArray());
                }
            });
        }

        if ($this->cards) {
            foreach ($this->cards as $card) {
                $voucher = Voucher::forceCreate([
                    'order_id'  => $order->id,
                    'code'      => str_random(10),
                    'amount'    => $card->item,
                    'to_name'   => $card->to_name,
                    'to_email'  => $card->to_email,
                    'message'   => $card->message,
                    'is_active' => true,
                ]);
                // dd();
                $sales = Sales::forceCreate([
                    'order_id'   => $order->id,
                    'product_id' => null,
                    'owner_id'   => $order->user_id,
                    'price'      => $voucher->amount,
                    'code'       => $voucher->code,
                    'type'       => 'voucher',
                ]);
                // dd($sale);
                // dd($order->sales);
                try {
                    (new Email('gift-card'))->send($order->user, ['voucher_id' => $voucher->id]);
                } catch (\Exception $e) {
                    \Log::error('Voucher cannot be sent', $voucher->toArray());
                    // throw new \Exception($e->getMessage(), 1);
                }
            }
        }

        if ($this->permits) {
            foreach ($this->permits as $permit) {
                $voucher = Voucher::forceCreate([
                    'order_id'         => $order->id,
                    'user_id'          => $order->user_id,
                    'studio_id'        => $permit->studio_id,
                    'amount'           => $permit->item,
                    'code'             => str_random(10),
                    'is_active'        => 1,
                ]);

                $order->user->vouchers()->save($voucher);

                $permit = StudioPermit::forceCreate([
                    'studio_id'    => $permit->studio_id,
                    'user_id'      => $order->user_id,
                    'order_id'     => $order->id,
                    'voucher_id'   => $voucher->id,
                ]);

                $sales = Sales::forceCreate([
                    'order_id'   => $order->id,
                    'product_id' => null,
                    'owner_id'   => $order->user_id,
                    'price'      => $voucher->amount,
                    'code'       => $voucher->code,
                    'type'       => 'permit',
                ]);

                $sales = $sales;
                try {
                    // @todo send permit with coupon code email
                    // (new Email('gift-card'))->send($order->user, ['permit_id' => $permit->id]);
                } catch (\Exception $e) {
                    \Log::error('Permit cannot be sent', $permit->toArray());
                    // throw new \Exception($e->getMessage(), 1);
                }
            }
        }

        if (Auth::user()->representative_id && Auth::user()->subscription_years) {
            $rep                 = Representative::find(Auth::user()->representative_id);
            // if ($rep->record_order) {
            $representativeGroup = $rep->group;
            // @todo note: should use after discount ... ?
            $years               = (Auth::user()->subscription_years - 1) ?: 0;
            $price               = $order->subTotal($years);
            $discount            = $price - $order->total;
            $sharable            = $order->total;
            // dd($order->toArray(), $sharable, $representativeGroup->commission($sharable), $representativeGroup->representativeFee($sharable));
            RepresentativeOrder::forceCreate([
                'price'              => $price,
                'discount'           => $discount,
                'subscription_years' => $years,
                'commission'         => $representativeGroup->commission($sharable),
                'representative_fee' => $representativeGroup->representativeFee($sharable),
                'representative_id'  => Auth::user()->representative_id,
                'order_id'           => $order->id,
            ]);
            // }
        }

        try {
            (new Email('order-invoice'))->send($order->user, ['order_id' => $order->id]);
        } catch (\Exception $e) {
            \Log::error("Invoice Created cannot be sent:{$e->getMessage()}", $order->toArray());
        }

        $this->carts->each(function ($cart) {
            $cart->delete();
        });

        return $order;
    }
}
