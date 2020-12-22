@extends('emails.master')

@section('content')
    <div>
        <p>
            Dear Collectionstock,
        </p>
        <p>
            Received Suggestion as follow:
        </p>
        <table style="background: white; font-family: Arial, 'Times New Roman', sans-serif; font-size: 14px; line-height: 20px;">
            <tr>
                <td>Email:</td>
                <td>{{ $suggestion->email }}</td>
            </tr>
            <tr>
                <td>Name:</td>
                <td>{{ $suggestion->name }}</td>
            </tr>
            <tr>
                <td>Message:</td>
                <td>{{ $suggestion->message }}</td>
            </tr>
        </table>
        <br>
        @include('emails.ending')
    </div>
@endsection
