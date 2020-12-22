@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>

    <p>
        @lang('emails.good_request_rejected.reason')
    </p>

    {{ $goodRequest->message }}

    @include('emails.ending')
</div>
@endsection
