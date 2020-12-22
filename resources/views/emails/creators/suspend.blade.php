@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        @lang('emails.creator.suspend.greet')
    </p>
    <p>
        @lang('emails.creator.warning.' . $reason)
    </p>
    @include('emails.ending')
</div>
@endsection