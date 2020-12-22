@extends('emails.master')

@section('content')
<style>
    table {
        background-color: none;
    }
</style>
<div>
     <p>
        @lang('emails.hi')<br/>
    </p> 
    {{--  <h1>@lang('emails.grace.period')</h1>  --}}
    {{--  <h2>{{ $payment->id }}</h2>  --}}

    {{-- @if($quota)
    <p>
      @lang('emails.libs.remind_quota', [ 'quota' => $quota ])
    </p>
    @endif --}}
    
    <p>
        @lang('emails.libs.overview_list')
    </p>
    
    @if($downloads->count())
        <table style="background-color: white; font-family: Arial, 'Times New Roman', sans-serif;" border="1" bordercolor="grey">
            <tr>
                <th>@lang('emails.design')</th>
                <th>@lang('emails.code')</th>
                <th>@lang('emails.date')</th>
                <th>@lang('emails.usage')</th>
            </tr>
            @foreach ($downloads as $download)
            <tr>
                <td style="text-align: center;">
                    <img src="{{ url('/api/v1/image/thumbnail/design/' . $download->design->code) }}" width="50" height="50">
                    <br>
                </td>
                <td style="text-align: center;">
                    {{ $download->design->design_name }}
                </td>
                <td style="text-align: center;">{{ date_format($download->created_at, "d/m/Y") }}</td>
                <td style="text-align: center;">
                    @lang('emails.usage.product-licence' )
                </td>
            </tr>
            @endforeach
        </table>
    @endif

    <br>
    @include('emails.ending')
</div>
@endsection
