@extends('emails.master')

@section('content')
    <div>
        <p>
            Dear Collectionstock,
        </p>
        <p>
            User wants to revise a design in the project
        </p>

        <br>
        @include('emails.ending')
    </div>
@endsection
