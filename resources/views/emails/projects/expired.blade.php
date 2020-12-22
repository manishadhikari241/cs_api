@extends('emails.master')

@section('content')
    <div>
        <p>
            @lang('emails.hi')<br/>
        </p>

        <p>@lang('emails.project.expired.intro', ['name' => $project->translations[0]->name])</p>
        
        <p>@lang('emails.project.expired.body')</p>

        <a href="{{ url('/premium/projects/' . $project->id) }}" class="button">
            @lang('emails.go.to.design.project')
        </a>

        @include('emails.ending')
    </div>
@endsection
