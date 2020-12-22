@extends('emails.master')

@section('content')
<style>
    table table {
        background: none;
        width: 100%;
    }
    table table td {
        vertical-align: top;
    }
    .mb-0 {
        margin-bottom: 0px;
    }
    .desc p {
        margin-top: 0px;
        /*margin-bottom: 0px;*/
    }
    /*.desc br:first-child {
        display: none;
    }*/
</style>
<div>
    <p>@lang('emails.hi')</p>

    @lang('emails.inquiry.greeting')

    @foreach($purchases as $purchase)
    <p>
        <div style="/*width: 110px; height: 110px;*/ margin-bottom: 20px;">
            <img src="{{ url('/api/v1/image/thumbnail/design/' . $purchase->design->code . '/with-good/' . $purchase->product->id) }}" width="200" height="200">
        </div>
        <table>
            <tr>
                <td>
                    <p>
                        @lang('emails.inquiry.product') {{ getTrans($purchase->product, 'name') }}<br>
                        @lang('emails.inquiry.code') {{ $purchase->design->code }}
                    </p>
                    <div class="desc">{!! getTrans($purchase->product, 'description') !!}</div>
                </td>
                <td>
                    <p>
                        @lang('emails.inquiry.price') @lang("emails.inquiry.region." . $purchase->product->region)<br>
                        @foreach($purchase->product->prices->sortBy('min_unit') as $price)
                            @if(!$loop->first)>@endif{{ $price->min_unit }}@lang('emails.inquiry.pcs') US${{ $price->unit_price }}<br>
                        @endforeach
                    </p>
                </td>
            </tr>
        </table>
    </p>
    @endforeach

    @lang('emails.inquiry.details')

    {{-- <h3 style="color: grey;">
        <a href="{{ url('/my-purchases') }}">@lang('emails.offerSheet.goto_purchases')</a>
    </h3> --}}

    @include('emails.ending')
</div>
@endsection
