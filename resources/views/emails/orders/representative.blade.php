@extends('emails.master')

@section('content')
<style>
    table{
        background-color: none;
    }
</style>
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>

    <p>
        @lang('emails.representative.heading')<br/>
    </p>

    <h1 style="text-align: center;">
        @lang('emails.representative')
    </h1>

    {{--  <table style="background-color: white; font-family: Arial, 'Times New Roman', sans-serif;">
        <tr>
            <td>@lang('emails.month'):</td>
            <td>@lang('emails.sales.date', ['year' => $year, 'month' => $month])</td>
        </tr>
        <tr>
            <td>@lang('emails.company_name'):</td>
            <td>{{ $user->addresses->company or '' }}</td>
        </tr>
        <tr>
            <td>@lang('emails.vat'):</td>
            <td>{{ $user->addresses->vat_number or '' }}</td>
        </tr>
        <tr>
            <td>@lang('emails.first_name'):</td>
            <td>{{ $user->addresses->first_name or '' }}</td>
        </tr>
        <tr>
            <td>@lang('emails.last_name'):</td>
            <td>{{ $user->addresses->last_name or '' }}</td>
        </tr>
        <tr>
            <td>@lang('emails.creator_code'):</td>
            <td>{{ $user->profile->code or '' }}</td>
        </tr>
        <tr>
            <td>@lang('emails.billing_address'):</td>
            <td>
                {{ $user->addresses->address1 or '' }}
                {{ $user->addresses->address2 or '' }}
                {{ $user->addresses->city or '' }}
                {{ $user->addresses->nation->translations[0]->name or '' }}
            </td>
        </tr>
    </table>  --}}

    <br />

    <table style="background-color: white; font-family: Arial, 'Times New Roman', sans-serif;" border="1" bordercolor="grey">
        <tr>
            {{--  <th>@lang('emails.items')</th>  --}}
            {{--  <th>@lang('emails.code')</th>  --}}
            <th>@lang('emails.order')</th>
            <th>@lang('emails.order_date')</th>
            {{--  <th>@lang('emails.usage')</th>  --}}
            {{--  <th>@lang('emails.selling_price')</th>  --}}
            <th>@lang('emails.cs_commission')</th>
            <th>@lang('emails.representative_fee')</th>
        </tr>
        @foreach ($orders as $order)
        <tr>
            {{--  <td style="text-align: center;"><img src="{{ url('/api/v1/image/thumbnail/design/' . $order->product->code) }}" width="50" height="50"></td>  --}}
            {{--  <td style="text-align: center;">{{ $order->product->code }}</td>  --}}
            <td style="text-align: center;">{{ $order->id }}</td>
            <td style="text-align: center;">{{ date_format($order->created_at, "d/m/Y") }}</td>
            {{--  <td style="text-align: center;">@lang('emails.usage.' . $order->type )</td>  --}}
            {{--  <td style="text-align: center;">${{ number_format($order->commission + $order->creator_fee, 2, '.', '') }}</td>  --}}
            <td style="text-align: center;">${{ $order->commission }}</td>
            <td style="text-align: center;">${{ $order->representative_fee }}</td>
        </tr>
        @endforeach
        <tr>
            {{--  <th>&nbsp;</th>  --}}
            {{--  <th>&nbsp;</th>  --}}
            <th>&nbsp;</th>
            {{--  <th>&nbsp;</th>  --}}
            {{--  <th>&nbsp;</th>  --}}
            <th style="text-align: right;">@lang('emails.grand_total'):</th>
            <th>${{ number_format($orders->sum('commission'), 2, '.', '') }}</th>
            <th>${{ number_format($orders->sum('representative_fee'), 2, '.', '') }}</th>
        </tr>
    </table>

    <h3>
        @lang('emails.important')<br/>
    </h3>

    <p>
        @lang('emails.representative.remark')<br/>
    </p>

    <p>
        @lang('emails.representative.footer')<br/>
    </p>
    @include('emails.ending')
</div>
@endsection
