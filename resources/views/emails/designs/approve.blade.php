@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        @lang('emails.design.congrates')
        @if (isset($design->design_name))
            <b>{{ $design->design_name }}</b> /
        @endif

        @lang('emails.design.with.code')
        <b>{{$design->code}}</b>

        {{-- @if (isset($design->request->custom_id))
            (<a href="{{ url('/design/' . $design->code) }}">{{ $design->request->custom_id }}</a>)
        @endif --}}
        @lang('emails.design.congrates.2')
        <br/>
        <br/>
        <img src="{{ url('/api/v1/image/thumbnail/design/' . $design->code) }}" width="200" height="200">
        <br/>
    </p>
    <p>
        @lang('emails.design.overview.message')
    </p>
    @include('emails.ending')
</div>
@endsection
