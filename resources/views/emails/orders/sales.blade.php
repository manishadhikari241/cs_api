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
        @lang('emails.sales.heading')<br/>
    </p>

    <h1 style="text-align: center;">
        @lang('emails.sales')
    </h1>

    <table style="background-color: white; font-family: Arial, 'Times New Roman', sans-serif;">
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
    </table>

    <br />

    <table style="background-color: white; font-family: Arial, 'Times New Roman', sans-serif;" border="1" bordercolor="grey">
        <tr>
            <th>@lang('emails.design')</th>
            <th>@lang('emails.code')</th>
            <th>@lang('emails.date')</th>
            <th>@lang('emails.usage')</th>
            <th>@lang('emails.commission')</th>
            <th>@lang('emails.creator_fee')</th>
        </tr>
        @foreach ($records as $record)
        <tr>
            <td style="text-align: center;">
                <img src="{{ url('/api/v1/image/thumbnail/design/' . $record->product->code) }}" width="50" height="50">
                <br>
                @lang('emails.order_id'): {{ $record->order->id }}
            </td>
            <td style="text-align: center;">
                {{ $record->product->code }}<br>{{ $record->product->design_name }}
            </td>
            <td style="text-align: center;">{{ date_format($record->order->created_at, "d/m/Y") }}</td>
            <td style="text-align: center;">
                @lang('emails.usage.' . $record->type )
                <br>
                ${{ number_format($record->commission + $record->creator_fee, 2, '.', '') }}
            </td>
            <td style="text-align: center;">${{ $record->commission }}</td>
            <td style="text-align: center;">${{ $record->creator_fee }}</td>
        </tr>
        @endforeach
        <tr>
            <th>@lang('emails.grand_total'):</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th style="text-align: right;">&nbsp;</th>
            <th>${{ number_format($commission_fee, 2, '.', '') }}</th>
            <th>${{ number_format($creator_fee, 2, '.', '') }}</th>
        </tr>
    </table>

    <h3>
        @lang('emails.important')<br/>
    </h3>

    <p>
        @lang('emails.sales.remark')<br/>
    </p>

    <p>
        @lang('emails.sales.footer')<br/>
    </p>
    @include('emails.ending')
</div>
@endsection
