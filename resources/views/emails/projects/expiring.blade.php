@extends('emails.master')

@section('content')
    <div>
        <p>
            @lang('emails.hi')<br/>
        </p>
        <p>@lang('emails.project.expiring.intro', ['day' => Carbon\Carbon::parse($project->expired_at)->format('d/m/Y'), 'project' => $project->translations[0]->name])</p>

        <p>@lang('emails.project.expiring.recommend')</p>

        <a href="{{ url('/premium/projects/' . $project->id) }}" class="button">
            @lang('emails.go.to.design.project')
        </a>
        
        <br>
        @include('emails.ending')
    </div>
@endsection
