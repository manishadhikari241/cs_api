@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        @lang('emails.creator.group.change', ['percentage' => $user->profile->creatorGroup->percentage])
    </p>
    @include('emails.ending')
</div>
@endsection