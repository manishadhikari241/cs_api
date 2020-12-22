@extends('emails.master')

@section('content')
    <div>
        <p>
            Dear Collectionstock,
        </p>
        <p>
            Received User Contacts as follow:
        </p>
        <table style="background: white; font-family: Arial, 'Times New Roman', sans-serif; font-size: 14px; line-height: 20px;">
            <tr>
                <td>Name:</td>
                <td>{{ $name }}</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>{{ $email }}</td>
            </tr>
            <tr>
                <td>Type:</td>
                <td>@lang('emails.contacts.type.' . $type)</td>
            </tr>
            <tr>
                <td>Message:</td>
                <td>{{ $bodyMessage }}</td>
            </tr>
        </table>
        <br>
        @include('emails.ending')
    </div>
@endsection
