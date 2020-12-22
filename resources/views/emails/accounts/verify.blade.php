@extends('emails.master')

@section('content')
    <div>
        <h1>Hello!</h1>
        <p>Please click the button below to verify your email address.</p>
        <br>
        <a href="{{ $actionUrl }}" rel="noopener" style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';border-radius:4px;color:#fff;display:inline-block;overflow:hidden;text-decoration:none;background-color:#2d3748;border-bottom:8px solid #2d3748;border-left:18px solid #2d3748;border-right:18px solid #2d3748;border-top:8px solid #2d3748" target="_blank">Verify Email Address</a>
        <br>
        <p>If you did not create an account, no further action is required.</p>
        <br>
        <p>Regards,</p>
        <p>Collectionstock</p>
        <br>
        <hr>
        <br>
        <p>
            If you’re having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser: <a href="{{ $actionUrl }}">{{ $actionUrl }}</a>
        </p>
    </div>
@endsection
