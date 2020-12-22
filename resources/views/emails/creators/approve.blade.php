@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        @lang('emails.creator.approve.congrats')
    </p>
    <p>
        @lang('emails.creator.approve.commission', [ 'percentage' => $user->profile->creatorGroup->percentage ])
    </p>
    <p>
        @lang('emails.creator.approve.login')
    </p>
    @include('emails.ending')
</div>
@endsection