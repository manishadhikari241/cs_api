@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        @lang('emails.creator.warning.greet')
    </p>
    <p>
        @lang('emails.creator.warning.' . $reason)
    </p>
    <p>
        @lang('emails.creator.warning.last')
    </p>
    @include('emails.ending')
</div>
@endsection