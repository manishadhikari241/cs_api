@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        @lang('emails.creator.reject.heading')
    </p>
    <p>
    	@lang('emails.creator.reject.' . $reason)
    </p>
    @include('emails.ending')
</div>
@endsection