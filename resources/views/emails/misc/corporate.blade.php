@extends('emails.master')

@section('content')
    <div>
        <p>
            Dear Collectionstock,
        </p>
        <p>
            Received Corporare Deals Enquiry as follow:
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
                <td>Company:</td>
                <td>{{ $company }}</td>
            </tr>
            <tr>
                <td>Country:</td>
                {{-- <td>{{ $country->translations[0]->name }}</td> --}}
                <td>{{ $country }}</td>
            </tr>
            <tr>
                <td>Number of designs per year:</td>
                <td>{{ $number }}</td>
            </tr>
            <tr>
                <td>Phone Number:</td>
                <td>{{ $phone }}</td>
            </tr>
        </table>
        <br>
        @include('emails.ending')
    </div>
@endsection
