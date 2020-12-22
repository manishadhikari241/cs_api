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
    {{--  <h1>@lang('emails.grace.period')</h1>  --}}
    {{--  <h2>{{ $payment->id }}</h2>  --}}

    @if($userPlan->payment_method === 'credit_card')
        <p>
        @lang('emails.libs.remind_free_trial', [
            'amount' => $userPlan->trialPlanUpgrade->libPlan->price,
            'period' => $userPlan->trialPlanUpgrade->libPlan->month_cycle === 12 ? __('emails.libs.yr') : __('emails.libs.mo'),
            'date' => Carbon\Carbon::parse($userPlan->trial_ends_at)->format('d/m/Y'),
            'last_date' => Carbon\Carbon::parse($userPlan->trial_ends_at)->subDay()->format('d/m/Y'),
        ])
        </p>
    @else
        <p>
        @lang('emails.libs.remind_free_trial.manual', [
            'date' => Carbon\Carbon::parse($userPlan->trial_ends_at)->format('d/m/Y'),
        ])
        </p>
    @endif
    
    <br>
    @include('emails.ending')
</div>
@endsection
