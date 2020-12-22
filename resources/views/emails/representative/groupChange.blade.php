@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        @lang('emails.representative.group.change', ['percentage' => $representative])
    </p>
    @include('emails.ending')
</div>
@endsection