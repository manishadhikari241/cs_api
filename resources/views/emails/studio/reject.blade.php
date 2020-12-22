@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>

	<p>
		@lang('emails.studio.reject.message', ['name' => $studio->translations[0]->name])
	</p>

	<p>@lang('emails.studio.reject.try')</p>

    @include('emails.ending')
</div>
@endsection
