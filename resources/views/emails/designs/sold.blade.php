@extends('emails.master')

@section('content')
<div>
    <p style="font-family: Arial, 'Times New Roman', sans-serif; font-size: 14px; line-height: 20px; margin-bottom: 20px;">
        @lang('emails.design.sold.congrats')<br/>
    </p>

    <p style="font-family: Arial, 'Times New Roman', sans-serif; font-size: 14px; line-height: 20px; margin-bottom: 20px;">
        {{ ucfirst(trans('emails.design')) }}:
        {{ $design->product->code }}
        @if (isset($design->request->custom_id))
            ({{ $design->request->custom_id }})
        @endif
        <br>
        {{ ucfirst(trans('emails.usage.type')) }}:
        @lang('emails.usage.' . $design->type)
        <br>
        {{ ucfirst(trans('emails.selling_price')) }}:
        ${{ $design->price }}
        <br>
        {{ ucfirst(trans('emails.creator_fee')) }}:
        ${{ $design->creator_fee }}
        <br>
        @lang('emails.cs_commission_p'):
        ${{ $design->commission }}
    </p>

    {{-- <table style="background-color: white; font-family: Arial, 'Times New Roman', sans-serif;" border="1" bordercolor="grey">
        <tr>
            <th>@lang('emails.design')</th>
            <th>@lang('emails.code')</th>
            <th>@lang('emails.date')</th>
            <th>@lang('emails.usage')</th>
            <th>@lang('emails.commission')</th>
            <th>@lang('emails.creator_fee')</th>
        </tr>
        <tr>
            <td style="text-align: center;">
                <img src="{{ url('/api/v1/image/thumbnail/design/' . $design->product->code) }}" width="50" height="50">
                <br>
                @lang('emails.order_id'): {{ $design->order_id }}
            </td>
            <td style="text-align: center;">
                {{ $design->product->code }}<br>{{ $design->product->design_name }}
            </td>
            <td style="text-align: center;">{{ date_format($design->order->created_at, "d/m/Y") }}</td>
            <td style="text-align: center;">
                @lang('emails.usage.' . $design->type )
                <br>
                US${{ number_format($design->commission + $design->creator_fee, 2, '.', '') }}
            </td>
            <td style="text-align: center;">${{ $design->commission }}</td>
            <td style="text-align: center;">${{ $design->creator_fee }}</td>
        </tr>
    </table> --}}

    <p style="font-family: Arial, 'Times New Roman', sans-serif; font-size: 14px; line-height: 20px; margin-bottom: 20px;">
        @lang('emails.design.sold.more')
        <br />
    </p>
    <p style="font-family: Arial, 'Times New Roman', sans-serif; font-size: 14px; line-height: 20px; margin-bottom: 20px;">
       @lang('emails.design.sold.footer')
    </p>
    @include('emails.ending')
</div>
@endsection