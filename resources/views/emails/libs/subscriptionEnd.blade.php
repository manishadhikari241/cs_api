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
      @lang('emails.libs.ended', [
        'plan' => $isFree ? __('emails.plan.free') : __('emails.plan.' . $userPlan->libPlan->key),
        'date' => $userPlan->ended_at
      ])
    </p>
    
    @if($isFree)
      <p>
        @lang('emails.subscription.free_ended_manual')
        {{-- @if($userPlan->payment_method === 'credit_card')
          @lang('emails.subscription.free_ended')
        @else
          @lang('emails.subscription.free_ended_manual')
        @endif --}}
      </p>

      <br>

      <a href="{{ url('/libs/plans?hide_free=1') }}" target="_blank"  class="button">
          @lang('emails.subscription.goto_library')
      </a>
    @endif

    <p>
      @lang('emails.libs.licence')
    </p>
    <p>
      @lang('emails.libs.bye')
    </p>

    {{--  <p>
      Also, your cumulated designs will be removed from library.
    </p>  --}}

    <br>
    @include('emails.ending')
</div>
@endsection
