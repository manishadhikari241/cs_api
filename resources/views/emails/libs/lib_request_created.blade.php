@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        @lang('emails.lib_request_created.info')

    </p>
    @include('emails.ending')
</div>
@endsection
