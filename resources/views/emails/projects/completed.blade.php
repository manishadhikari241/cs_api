@extends('emails.master')

@section('content')
    <div>
        <p>
            @lang('emails.hi')<br/>
        </p>
        <p>
            Your Design Project <strong>{{ $project->translations[0]->name }}</strong> is ready now!
        </p>

        <a href="{{ url('/premium/projects/' . $project->id) }}" class="button">
            Go to the Design Project
        </a>

        <p>
            Note: Your Design Project will expire on {{ $expiryDate }}. Please check and download your customised designs before that date.
        </p>
        <br>
        @include('emails.ending')
    </div>
@endsection
