@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    
    <h1>@lang('emails.recurring.invoice')</h1>
    {{--  <h2>{{ $payment->id }}</h2>  --}}
    <table style="background-color: white; font-family: Arial, 'Times New Roman', sans-serif;">
        <tr>
            <th style="text-align: left;">@lang('emails.company_name'):</th>
            <td>Distributor Ltd.</td>
        </tr>
            <tr>
                <th style="text-align: left;">@lang('emails.vat'):</th>
                <td>dist-VAT-12345</td>
            </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.first_name'):</th>
            <td>dist Jacky</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.last_name'):</th>
            <td>dist Chan</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.billing_address'):</th>
            <td>
                address 1234 . ABC Road. China
            </td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.first_name'):</th>
            <td>{{ $user->first_name }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.last_name'):</th>
            <td>{{ $user->last_name }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.payment_date'):</th>
            <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format("d/m/Y") }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.next_billing_at'):</th>
            <td>{{ \Carbon\Carbon::parse($invoice->libPlanUser->next_billing_at)->format("d/m/Y") }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.payment_method'):</th>
            <td>@lang('emails.' . $invoice->libPlanUser->payment_method)</td>
        </tr>
    </table>
    <br />
    <br />

    <table style="font-family: Arial, 'Times New Roman', sans-serif; background-color: white;" width="100%" cellpadding="2">
        <tr style="border-top: 1px solid grey; border-bottom: 1px solid grey;">
            <th style="text-align: left;">@lang('emails.plan')</th>
            <th style="text-align: left;">@lang('emails.amount')</th>
            <th style="text-align: left;">@lang('emails.recurring_type')</th>
        </tr>
        <tr>
            <td>
                <span>{{ __('emails.plan.' . $invoice->libPlanUser->libPlan->key) }}</span>
                <br>
                <span>
                    <strong>{{  $invoice->libPlanUser->libPlan->quota }}</strong>
                    {{ __('emails.downloads_per_month') }}
                </span>
            </td>
            <td>USD$ {{ (int) $invoice->price }}</td>
            <td>{{ __('emails.' . 
                ($invoice->libPlanUser->libPlan->month_cycle === 12
                ? __('libs.yr')
                : __('libs.mo'))
            ) }}</td>
        </tr>
    </table>

    <h3>
        @lang('emails.important')
    </h3>
    <p>
        @lang('emails.recurring.remark1')
        <br>
        @lang('emails.invoice.remark2')
        <br>
        @lang('emails.invoice.remark3')
        <br>
        @lang('emails.recurring.remark4', ['total' => (int) $invoice->price])
    </p>

    @include('emails.ending')
</div>
@endsection