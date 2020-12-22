@extends('emails.master')

@section('content')

<div>
     <p>
        @lang('emails.hi')<br/>
    </p> 
    {{--  <h1>@lang('emails.grace.period')</h1>  --}}
    {{--  <h2>{{ $payment->id }}</h2>  --}}

    <p>
      @lang('emails.demo.request.content')
    </p>
    
    <br>
    <a href="https://calendly.com/collectionstock/collectionstock-demonstration" target="_blank" class="button">
      @lang('emails.demo.request.book.now')
    </a>
    <br>
    <br>

    <p>@lang('emails.first_name'):  <b>{{ $firstName }}</b></p>
    <p>@lang('emails.last_name'): <b>{{ $lastName }}</b></p>
    @if($skypeId)
      <p>@lang('emails.skype_id'): <b>{{ $skypeId }}</b></p>
    @endif
    @if($mobile)
      <p>@lang('emails.mobile'): <b>{{ $mobile }}</b></p>
    @endif
    @if($wechat)
      <p>@lang('emails.wechat'): <b>{{ $wechat }}</b></p>
    @endif
    @if($country)
      <p>@lang('emails.country'): <b>{!! getTrans($country, 'name') !!}</b></p>
    @endif
    {{-- <p>@lang('emails.email'): <b>{{ $email }}</b></p> --}}
    
    <br>
    @include('emails.ending')
</div>
@endsection
