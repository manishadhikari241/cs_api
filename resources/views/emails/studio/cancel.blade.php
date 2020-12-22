@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>

    {{-- old --}}

	<p>We are sorry to inform you that the Studio <strong> {{ $studio->translations[0]->name }}</strong> has cancelled your access.</p>

	<p>You can try it later another time.</p>

    @include('emails.ending')
</div>
@endsection
