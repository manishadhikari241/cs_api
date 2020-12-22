@extends('master')

@section('content')
    <div>
        <p>Hi!</p>
        <br>
        <p>Welcome to Collectionstock and thank you for joining us!</p>
        <br>
        <div>
            <div>You are now having full access to:</div>
            <div>+ Our Market and Trend aware Collections</div>
            <div>+ Our Prints, Patterns and Graphics</div>
            <div>+ The Product Simulator</div>
            <div>+ The Complimentary Design Request Service</div>
        </div>
        <br>
        <div>
            <a href="{{ env('APP_PUBLIC_URL') }}">Collectionstock</a>
        </div>
        <br>
        <div>
            <div>Warm regards</div>
            <div>Collectionstock.com</div>
        </div>
    </div>
@endsection
