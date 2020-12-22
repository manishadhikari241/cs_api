@extends('emails.master')

@section('content')
<style>
    table {
        background-color: none;
    }
</style>
<div>
    {{--  <p>
        @lang('emails.hi')<br/>
    </p>  --}}
    <h1>@lang('emails.recurring.invoice')</h1>
    {{--  <h2>{{ $payment->id }}</h2>  --}}
    <table style="background-color: white; font-family: Arial, 'Times New Roman', sans-serif;">
        @if($address)
        <tr>
            <th style="text-align: left;">@lang('emails.company_name'):</th>
            <td>{{ $address->company }}</td>
        </tr>
            @if($address->vat_number)
            <tr>
                <th style="text-align: left;">@lang('emails.vat'):</th>
                <td>{{ $address->vat_number }}</td>
            </tr>
            @endif
        <tr>
            <th style="text-align: left;">@lang('emails.first_name'):</th>
            <td>{{ $address->first_name }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.last_name'):</th>
            <td>{{ $address->last_name }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.billing_address'):</th>
            <td>
                {{ $address->address1 }}
                {{ $address->address2 }}
                <br>
                {{ $address->city }}
                {{ isset($address->nation) && isset($address->nation->translations[0]) ? $address->nation->translations[0]->name : '' }}
                @if($address->post_code)
                    - {{ $address->post_code }}
                @endif
            </td>
        </tr>
        @else
        <tr>
            <th style="text-align: left;">@lang('emails.first_name'):</th>
            <td>{{ $payment->user->first_name }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.last_name'):</th>
            <td>{{ $payment->user->last_name }}</td>
        </tr>
        @endif
        <tr>
            <th style="text-align: left;">@lang('emails.payment_date'):</th>
            <td>{{ \Carbon\Carbon::parse($payment->created_at)->format("d/m/Y") }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.next_billing_at'):</th>
            <td>{{ \Carbon\Carbon::parse($payment->libPlanUser->next_billing_at)->format("d/m/Y") }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">@lang('emails.payment_method'):</th>
            <td>@lang('emails.' . $payment->payment_method)</td>
        </tr>
        @if ($payment->payment_method == 'credit_card')
        <tr>
            <th style="text-align: left;">@lang('emails.transaction_id'):</th>
            <td>{{ $payment->transaction_id }}</td>
        </tr>
        @endif
    </table>
    <br />
    <br />
    <table style="font-family: Arial, 'Times New Roman', sans-serif; background-color: white;" width="100%" cellpadding="2">
        <tr style="border-top: 1px solid grey; border-bottom: 1px solid grey;">
            <th style="text-align: left;">@lang('emails.plan')</th>
            @if((int) $payment->discount)
            <th style="text-align: left;">@lang('emails.amount')</th>
            <th style="text-align: left;">@lang('emails.discount')</th>
            @endif
            <th style="text-align: left;">@lang('emails.total')</th>
            <th style="text-align: left;">@lang('emails.recurring_type')</th>
        </tr>
        <tr>
            <td>
                @if($payment->status === 2)
                <span>{{ __('emails.plan.upgrade_to') }}</span>
                @endif
                <span>{{ __('emails.plan.' . $payment->libPlanUser->libPlan->key) }}</span>
                <br>
                <span>
                    <strong>{{  $payment->libPlanUser->libPlan->quota }}</strong>
                    {{ __('emails.downloads_per_month') }}
                </span>
            </td>
            @if((int) $payment->discount)
            <td>USD$ {{ (int) $payment->amount }}</td>
            <td>USD$ {{ (int) $payment->discount }}</td>
            @endif
            <td>USD$ {{ (int) $payment->total }}</td>
            <td>{{ __('emails.' . 
                ($payment->libPlanUser->libPlan->month_cycle === 12
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
        @if($payment->libPlanUser->created_at->gt('2018-10-18'))
        {{-- new user --}}
            @if($payment->libPlanUser->payment_method === 'credit_card')
                @if($payment->libPlanUser->libPlan->month_cycle == 1)
                    @lang('emails.recurring.remark2', ['total' => (int) $payment->libPlanUser->libPlan->price])
                @else
                    @lang('emails.recurring.remark3', ['total' => (int) $payment->libPlanUser->libPlan->price])
                @endif
            @else
                @lang('emails.recurring.remark4', ['total' => (int) $payment->libPlanUser->libPlan->price])
            @endif
        @else
        {{-- old user --}}
            @if($payment->libPlanUser->payment_method === 'credit_card')
                @if($payment->libPlanUser->libPlan->month_cycle == 1)
                    @lang('emails.old.recurring.remark2', ['total' => (int) $payment->libPlanUser->libPlan->price])
                @else
                    @lang('emails.old.recurring.remark3', ['total' => (int) $payment->libPlanUser->libPlan->price])
                @endif
            @else
                @lang('emails.old.recurring.remark4', ['total' => (int) $payment->libPlanUser->libPlan->price])
            @endif
        @endif
    </p>
    <br>
    <table width="215">
      <tr><td width="215" style="text-align: center;padding: 10px 0;background: #717171;">
        <a href="{{ url('/account/lib-plan-history/' . $payment->libPlanUser->id) }}" target="_blank" style="text-decoration: none; display: block; font-family: Arial, 'Times New Roman', sans-serif; font-size: 16px; line-height: 18px; width: 215px;  text-align: center; color: #fff; -webkit-border-radius: 3px; border-radius: 3px; display: inline-block;">
            @lang('emails.recurring.goto_payment')
        </a>
      </td></tr>
    </table>
    <br>
    @include('emails.ending')
</div>
@endsection
