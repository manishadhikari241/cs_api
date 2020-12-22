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
    <p>
      {{ __('emails.libs.teminate_plan_date', [
        'plan' => __('emails.plan.' . $userPlan->libPlan->key),
        'date' => $userPlan->grace_period_until
      ]) }}
    </p>

    <p>
      {{ __('emails.libs.teminate_warning') }}
    </p>

    <p>
      {{ __('emails.libs.teminate_check') }}
    </p>

    <table width="215">
      <tr><td width="215" style="text-align: center;padding: 10px 0;background: #717171;">
        <a href="{{ url('/account/lib-plan/') }}" target="_blank" style="text-decoration: none; display: block; font-family: Arial, 'Times New Roman', sans-serif; font-size: 16px; line-height: 18px; width: 215px;  text-align: center; color: #fff; -webkit-border-radius: 3px; border-radius: 3px; display: inline-block;">
            @lang('emails.recurring.goto_payment')
        </a>
      </td></tr>
    </table>
    <br>
    @include('emails.ending')
</div>
@endsection
