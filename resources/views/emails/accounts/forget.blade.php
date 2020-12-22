@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br />
    </p>
    <p>
       @lang('emails.request.email')
    </p>
    <p>
        @lang('emails.password.change')
    </p>

    <a href="{{ url('/account/password/reset?token='. urlencode($user->resetToken()) .'&email=' . urlencode($user->email)) }}" target="_blank" class="button">
        @lang('emails.change.click')
    </a>
    
    <p>
        @lang('emails.privacy.change')
    </p>
    @include('emails.ending')
</div>
@endsection