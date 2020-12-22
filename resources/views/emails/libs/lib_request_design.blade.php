@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        @lang('emails.lib_request_design.congrates')

        <br><br>

        <a href="{{ url('/libs/request-design/all-request') }}" target="_blank"  class="button">
            @lang('emails.lib_request_design.download.now')
        </a>

        <br><br>

        @foreach ($designs as $design)
          <br/>
          <img src="{{ url('/api/v1/image/thumbnail/design/' . $design->code) }}" width="215" height="215">
          <br/>
        @endforeach

    </p>
    @include('emails.ending')
</div>
@endsection
