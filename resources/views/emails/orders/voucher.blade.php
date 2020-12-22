@extends('emails.master')

@section('content')
<div>
	<img src="https://www.collectionstock.com/uploads/card/9f8c25666153bd2c76a10de779b4b90407283e2e.jpg" style="width: 100%;" />
    <p>
        @lang('emails.voucher.dear', ['name' => $voucher->to_name])<br/>
    </p>
    <p>
        @lang('emails.voucher.greeting')<br/>
    </p>
    <p>
        @lang('emails.voucher.message')
        <strong>{{ $voucher->message }}</strong>
        <br>
        @lang('emails.voucher.code')
        <strong>{{ $voucher->code }}</strong>
        <br>
        @lang('emails.voucher.value')
        <strong>{{ $voucher->amount }}</strong>
    </p>
    <p>
        @lang('emails.voucher.redeem')
    </p>
    @include('emails.ending')
    <p style="font-size: 12px; color: #717171;">
        @lang('emails.voucher.disclaimer', ['name' => $username])
    </p>
</div>
@endsection
