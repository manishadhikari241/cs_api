@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        @lang('emails.design.decline.heading')
        @if (isset($upload->design_name))
            <b>{{ $upload->design_name }}</b>
        @endif
        @lang('emails.design.decline.heading2')
        <br>
        <br>
        <img src="{{ url('/api/v1/image/thumbnail/request/' . $upload->id) }}" width="200" height="200">
        <br>
    </p>
    <p>
        @if ($reason == 'f')
            <b>{{ $bodyMessage }}</b>
            <br>
            {!! trans("emails.design.decline.f") !!}
        @else
            {!! trans("emails.design.decline.$reason") !!}
        @endif
    </p>
    @include('emails.ending')
</div>
@endsection
