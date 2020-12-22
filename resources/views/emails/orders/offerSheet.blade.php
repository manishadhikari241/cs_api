@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    
    {{-- <p>Offer Sheet</p> --}}

    <p>
        @lang('emails.offer.thank')
    </p>

    <p>{{ $bodyMessage }}</p>

    <h3 style="color: grey;">
        <a class="button" href="{{ url('/my-purchases') }}">@lang('emails.offer.goto_purchases')</a>
    </h3>
    @include('emails.ending')
</div>
@endsection
