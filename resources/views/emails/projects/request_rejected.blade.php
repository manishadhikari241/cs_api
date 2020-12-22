@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>@lang('emails.request.reject.intro', ['studio' => $request->studio->translations[0]->name, 'project' => $request->name])</p>

    <p>
        @if ($request->reason == 'e')
            <b>{{ $request->message }}</b>
        @else
            <b>{!! trans("emails.request.reject.$request->reason") !!}</b>
        @endif
    </p>

    <p>@lang('emails.request.reject.charge')</p>

    <p>@lang('emails.request.reject.apply')</p>

    @include('emails.ending')
</div>
@endsection
