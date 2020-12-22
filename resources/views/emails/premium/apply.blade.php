@extends('emails.master')

@section('content')
<style>
    table{
        background-color: none;
    }
</style>
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>

    <p>@lang('emails.studio.applied.message', ['name' => $studio && $studio->translations ? $studio->translations[0]->name ?? '' : ''])</p>

    <p>@lang('emails.studio.applied.contact')</p>


    {{-- <p>
        @lang('emails.premium.heading')<br/>
    </p> --}}

    {{-- <table style="background-color: white; font-family: Arial, 'Times New Roman', sans-serif;">
        <tr>
            <td>Member Name:</td>
            <td>{{$name}}</td>
        </tr>
        <tr>
            <td>Email:</td>
            <td>{{$email}}</td>
        </tr>
        <tr>
            <td>Company Name:</td>
            <td>{{$company_name }}</td>
        </tr>
        <tr>
            <td>Number of Design Buy Per Year:</td>
            <td>{{$design_per_year}}</td>
        </tr>
        <tr>
            <td>Type:</td>
            <td>{{$business}}</td>
        </tr>
        <tr>
            <td>Website:</td>
            <td>{{$website or '' }}</td>
        </tr>
        <tr>
            <td>No of Employee:</td>
            <td>{{ $employees or '' }}</td>
        </tr>
        <tr>
            <td>No of Internal Designer:</td>
            <td>{{ $internal_designers or '' }}</td>
        </tr>
        <tr>
            <td>Exclusive Design Explanation:</td>
            <td>
                {{ $exclusive_design_detail or '' }}
            </td>
        </tr>
        <tr>
            <td>Premium Member Exp:</td>
            <td>{{ $consider_you or '' }}</td>
        </tr>
    </table> --}}
 @include('emails.ending')
</div>
@endsection
