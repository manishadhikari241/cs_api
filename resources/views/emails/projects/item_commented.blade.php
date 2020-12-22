@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
        You received a Comment for Design Project - <b>{{ $item->project->translations[0]->name }}</b>.
    </p>

    <a href="{{ url('/premium/projects/' . $item->project->id) }}" class="button">
        Go to the Design Project
    </a>

    <br>
    @include('emails.ending')
</div>
@endsection
