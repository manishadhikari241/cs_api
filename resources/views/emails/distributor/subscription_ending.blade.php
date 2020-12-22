@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    
    <p>Your subscription will end at {{ $userPlan->ended_at }}. If you consider to undo the cancellation, please go to your account and reactivate it.</p>

    @include('emails.ending')
</div>
@endsection