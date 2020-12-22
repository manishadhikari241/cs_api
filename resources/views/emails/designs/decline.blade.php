@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        @lang('emails.design.decline.heading')
        @if (isset($design->design_name))
            <b>{{ $design->design_name }}</b> /
        @endif
        @lang('emails.design.with.code')
        <b>{{ $design->code }}</b>
        {{-- @if (isset($design->request->custom_id))
            (<a href="{{ url('/design/' . $design->code) }}">{{ $design->request->custom_id }}</a>)
        @endif --}}
        @lang('emails.design.decline.heading2')
        <br>
        <br>
        <img src="{{ url('/api/v1/image/thumbnail/design/' . $design->code) }}" width="200" height="200">
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
