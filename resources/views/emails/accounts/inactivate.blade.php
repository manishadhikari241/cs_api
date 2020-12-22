@extends('emails.master')

@section('content')
    <div>
        <p>
            @lang('emails.hi')<br />
        </p>
        <p>
            @lang('emails.inactivate.info')
        <br />
        </p>
        <p>
           @lang('emails.inactivate.undo', [ 'erased_at' => \Carbon\Carbon::parse($inactivation->erased_at)->format('d/m/Y') ])
        </p>
        @include('emails.ending')
    </div>
@endsection