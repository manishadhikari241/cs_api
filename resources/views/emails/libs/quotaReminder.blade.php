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
      @lang('emails.libs.remind_quota', [
          'plan' => __('emails.plan.' . $userPlan->libPlan->key),
          'downloads' => $userPlan->libPlan->quota,
          'downloaded' => $userPlan->libPlan->quota - $quota,
          'quota' => $quota,
          'expiry_date' => $expiry_date,
      ])
    </p>
    

    <br>
    @include('emails.ending')
</div>
@endsection
