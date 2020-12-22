@extends('emails.master')

@section('content')
    <div>
        <p>
            @lang('emails.hi')<br />
        </p>

        @if($user->profile->subscribe)
            @lang('emails.preference.content.yes')
        @else
            @lang('emails.preference.content.no')
        @endif

        @include('emails.ending')
    </div>
@endsection