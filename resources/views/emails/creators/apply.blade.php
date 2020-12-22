@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        @lang('emails.creator.apply.message')
    </p>
    <p>
        @lang('emails.creator.apply.thank')
    </p>
    @include('emails.ending')
</div>
@endsection