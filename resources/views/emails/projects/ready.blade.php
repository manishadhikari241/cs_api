@extends('emails.master')

@section('content')
    <div>
        <p>
            @lang('emails.hi')<br/>
        </p>
        <p>@lang('emails.project.ready.intro', ['project' => $project->translations[0]->name])</p>

        <a href="{{ url('/premium/projects/' . $project->id) }}" class="button">
            @lang('emails.go.to.design.project')
        </a>
        
        @if($project->projectPackage->max_revision > 0)
            <p>@lang('emails.project.ready.revision', ['day' => Carbon\Carbon::parse($project->expired_at)->format('d/m/Y')])</p>
        @endif
        @include('emails.ending')
    </div>
@endsection
