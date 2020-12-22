@extends('emails.master')

@section('content')
    <div>
        <p>
            Dear Customer,
        </p>
        <p>
            Here is your payment invoice
        </p>
        <br>
        @include('emails.ending')
    </div>
@endsection
