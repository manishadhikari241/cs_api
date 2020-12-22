@extends('emails.master')

@section('content')
<style>
    table {
        background-color: none;
    }
</style>
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        @lang('emails.invoice.congrats')
    </p>
    <h1>@lang('emails.invoice')</h1>
    <h2>{{ $order->id }}</h2>
    <table style="background-color: white; font-family: Arial, 'Times New Roman', sans-serif;">
        <tr>
            <th style="text-align: left;">@lang('emails.date'):</th>
            <td>{{ date_format($order->created_at, "d/m/Y") }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.company_name'):</th>
            <td>{{ $order->payment_company }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.vat'):</th>
            <td>{{ $order->payment_vat_code }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.first_name'):</th>
            <td>{{ $order->payment_first_name }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.last_name'):</th>
            <td>{{ $order->payment_last_name }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.billing_address'):</th>
            <td>
                {{ $order->payment_address1 }}
                {{ $order->payment_address2 }}
                <br>
                {{ $order->payment_city }}
                {{-- TODO Chinese --}}
                {{ $order->nation->translations[0]->name }}
            </td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.payment_method'):</th>
            <td>@lang('emails.' . $order->payment_method)</td>
        </tr>
        @if ($order->payment_method == 'credit_card')
        <tr>
            <th style="text-align: left;">@lang('emails.transaction_id'):</th>
            <td>{{ $order->transaction_id }}</td>
        </tr>
        @endif
    </table>
    <br />
    <br />
    <table style="font-family: Arial, 'Times New Roman', sans-serif; background-color: white;" width="100%" cellpadding="2">
        <tr style="border-top: 1px solid grey; border-bottom: 1px solid grey;">
            <th style="text-align: left;">@lang('emails.design')</th>
            <th style="text-align: left;">@lang('emails.code')</th>
            <th style="text-align: left;">@lang('emails.creator_code')</th>
            <th style="text-align: left;">@lang('emails.usage')</th>
            <th style="text-align: left;">@lang('emails.selling_price')</th>
        </tr>
        @foreach ($sales as $sale)
            <tr style="border-bottom: 1px solid grey;">
                @if ($sale->type == 'voucher')
                    <td>
                        <img src="https://www.collectionstock.com/uploads/card/list/c80e7d9d91d1b1d3356fe31be2f19770820cf16c.jpg" width="50" height="50">
                        <br>@lang('emails.gift.card')
                    </td>
                    <td>
                        <small>@lang('emails.gift.card.to'): {{ $sale->voucher->to_name }}</small>
                    </td>
                    <td>
                        <small>@lang('emails.gift.card.email'): {{ $sale->voucher->to_email }}</small>
                    </td>
                    <td></td>
                    <td>${{ $sale->price }}</td>
                @elseif ($sale->type == 'product' || $sale->type == 'product-licence')
                    <td><img src="{{ url('/api/v1/image/thumbnail/design/' . $sale->product->code) }}" width="50" height="50"></td>
                    <td>{{ $sale->product->code }}<br>{{ $sale->product->design_name }}</td>
                    <td>{{ $sale->product->studio->translations[0]->name }}</td>
                    <td>@lang('emails.usage.' . $sale->type )</td>
                    <td>${{ $sale->price }}</td>
                @elseif ($sale->type == 'permit')
                    <td><img src="https://www.collectionstock.com/images/become-premium/premium-logo.png" width="80" height="40"></td>
                    <td>{{ $sale->code }}</td>
                    <td></td>
                    <td>@lang('emails.permit')</td>
                    <td>${{ $sale->price }}</td>
                @endif
            </tr>
        @endforeach
        {{--  @foreach ($permits as $permit)
            <tr style="border-bottom: 1px solid grey;">
                <td>
                    <img src="https://www.collectionstock.com/images/become-premium/premium-logo.png" width="80" height="40">
                </td>
                <td>
                    <small>{{ $permit->voucher->code }}</small>
                </td>
                <td>
                    <small>@lang('emails.studio') {{ $permit->studio->translations->where('lang', $locale)->first()->name }}</small>
                </td>
                <td>@lang('emails.permit')</td>
                <td>${{ $permit->voucher->amount }}</td>
            </tr>
        @endforeach  --}}
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td></td>
            <td style="text-align: left;">@lang('emails.invoice.sub_total')</td>
            @if ($order->coupon)
                @if ($order->coupon->discount_type == 1)
                <td>${{ number_format($order->total / ( 1 - json_decode($order->coupon_data)->amount / 100), 2, '.', '') }}</td>
                @else
                <td>${{ number_format($order->total + json_decode($order->coupon_data)->cost, 2, '.', '') }}</td>
                @endif
            @else
                @if ($order->voucher)
                    <td>${{ number_format($order->total + json_decode($order->voucher_data)->cost, 2, '.', '') }}</td>
                @else
                    <td>${{ $order->total }}</td>
                @endif
            @endif
        </tr>
        @if ($order->coupon)
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td></td>
            <td style="text-align: left;">@lang('emails.invoice.coupon')
                <br>[{{json_decode($order->coupon_data)->code}}]
                <br>(@lang('emails.coupon.' . $order->coupon->discount_type ))
            </td>
            @if ($order->coupon->discount_type == 1)
            <td>${{ number_format($order->total / ( 1 - json_decode($order->coupon_data)->amount / 100) - $order->total, 2, '.', '') }}</td>
            @else
            <td>${{ number_format(json_decode($order->coupon_data)->cost, 2, '.', '') }}</td>
            @endif
        </tr>
        @endif
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td></td>
            <td style="text-align: left;">@lang('emails.invoice.total')</td>
            <td style="text-align: left;">${{ $order->total }}</td>
        </tr>
        @if ($order->payment_method == 'alipay' || $order->payment_method == 'wechatpay')
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td></td>
            <td>&nbsp;</td>
            <td>HK${{ $order->total * 7.8 }}</td>
        </tr>
        @endif
    </table>

    @if ($order->voucher)
    <p>
        @lang('emails.invoice.applied') {{ $order->voucher->code }}
        <br>
        @lang('emails.amount'): ${{ number_format(json_decode($order->voucher_data)->cost, 2, '.', '') }}
    </p>
    @endif

    <h3>
        @lang('emails.important')
    </h3>
    <p>
        @lang('emails.invoice.remark1')
        <br>
        @lang('emails.invoice.remark2')
    </p>
    <br>
    <table width="215">
      <tr><td width="215" style="text-align: center;padding: 10px 0;background: #717171;">
        <a href="{{ url('/account/purchase-history/' . $order->id) }}" target="_blank" style="text-decoration: none; display: block; font-family: Arial, 'Times New Roman', sans-serif; font-size: 16px; line-height: 18px; width: 215px;  text-align: center; color: #fff; background-color: #717171; -webkit-border-radius: 3px; border-radius: 3px; display: inline-block;">
            @lang('emails.invoice.goto_order')
        </a>
      </td></tr>
    </table>
    <br>
    <p>
        @lang('emails.invoice.re-download')
    </p>
    @include('emails.ending')
</div>
@endsection
