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
      @lang('emails.libs.welcome', [
        'plan' => $isFree ? __('emails.plan.free') : __('emails.plan.' . $userPlan->libPlan->key)
      ])
    </p>

    @if($isFree)
      @if($userPlan->payment_method === 'credit_card')
        @lang('emails.libs.free_benefit')
      @else
        @lang('emails.libs.free_benefit_manual')
      @endif
    @else
      {{-- {!! $userPlan->libPlan->translations[0]->description !!} --}}
      {!! getTrans($userPlan->libPlan, 'short_description') !!}

    <p>
      @if($userPlan->payment_method === 'credit_card')
          @if($userPlan->libPlan->month_cycle == 1)
              @lang('emails.recurring.remark2', ['total' => (int) $userPlan->libPlan->price])
          @else
              @lang('emails.recurring.remark3', ['total' => (int) $userPlan->libPlan->price])
          @endif
      @else
          @lang('emails.recurring.remark4', ['total' => (int) $userPlan->libPlan->price])
      @endif
    </p>
    @endif

    {{-- <p>

      @if($isFree)
        @if($userPlan->payment_method === 'credit_card')
          @lang('emails.upgrade.from_free', [ 'date' => $isFree 
            ? \Carbon\Carbon::parse($userPlan->trial_ends_at)->format("d/m/Y")
            : \Carbon\Carbon::parse($userPlan->ended_at)->format("d/m/Y")
          ])
        @else
          @lang('emails.upgrade.from_free_manual', [ 'date' => $isFree 
            ? \Carbon\Carbon::parse($userPlan->trial_ends_at)->format("d/m/Y")
            : \Carbon\Carbon::parse($userPlan->ended_at)->format("d/m/Y")
          ])
        @endif
        <a href="{{ url('/account/lib-plan') }}">
         @lang('emails.upgrade.plan')
        </a>
        @lang('emails.upgrade.why')
      @endif
    </p> --}}

    <table width="215">
      <tr><td width="215" style="text-align: center;padding: 10px 0;background: #717171;">
        <a href="{{ url('/') }}" target="_blank" style="text-decoration: none; display: block; font-family: Arial, 'Times New Roman', sans-serif; font-size: 16px; line-height: 18px; width: 215px;  text-align: center; color: #fff; -webkit-border-radius: 3px; border-radius: 3px; display: inline-block;">
            @lang('emails.subscription.goto_library')
        </a>
      </td></tr>
    </table>
    <br>
    @include('emails.ending')
</div>
@endsection
