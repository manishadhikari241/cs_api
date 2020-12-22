@extends('emails.master')

@section('content')
    <div>
        <p>
            Dear Collectionstock,
        </p>
        <p>
            Expiring Project Request as follow:
        </p>
        <table style="background: white; font-family: Arial, 'Times New Roman', sans-serif; font-size: 14px; line-height: 20px;">
            <tr>
                <td>User:</td>
                <td>{{ $request->user->username }}</td>
            </tr>
            <tr>
                <td>Project Name:</td>
                <td>{{ $request->name }}</td>
            </tr>
            <tr>
                <td>Message:</td>
                <td>{{ $request->message }}</td>
            </tr>
        </table>
        <br>
        @include('emails.ending')
    </div>
@endsection
