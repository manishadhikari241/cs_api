@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        @lang('emails.good_request.congrates')

        <br><br>

        <a href="{{ url('/libs/simulate') }}" target="_blank"  class="button">
            @lang('emails.good_request.try.now')
        </a>

        <br><br>
        @foreach ($goods as $good)
          <br/>
            <img src="{{ url('/api/v1/goods/photos/' . $good->image) }}" width="215" height="215">
          <br/>
        @endforeach

    </p>
    @include('emails.ending')
</div>
@endsection
