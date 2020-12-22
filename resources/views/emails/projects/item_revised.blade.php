@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    
    <p>@lang('emails.project.revise.intro', ['name' => $item->project->translations[0]->name])</p>

    <a href="{{ url('/premium/projects/' . $item->project->id) }}" class="button">
        @lang('emails.go.to.design.project')
    </a>

    <br>
    @include('emails.ending')
</div>
@endsection
