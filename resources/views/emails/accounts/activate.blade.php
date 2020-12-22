@extends('emails.master')

@section('content')
    <div>
        <p>
            @lang('emails.hi')<br />
        </p>
        <p>
            @lang('emails.activate.greet')
        <br />
        </p>
        <p>
           @lang('emails.activate.ready')
        </p>
        @include('emails.ending')
    </div>
@endsection