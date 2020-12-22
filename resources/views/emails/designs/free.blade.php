@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi_with_name', [ 'name' => $to_name ])<br/>
    </p>
    <p>
        @lang('emails.free.greet', [ 'name' => $username ])
    </p>
    <p>
        <img src="{{ url('/api/v1/image/thumbnail/design/' . $design->design->code) }}" width="200" height="200">
    </p>
    <p>
        @lang('emails.free.message')
    </p>
    <p>
        {{ $bodyMessage }}
    </p>
    <p>
        @lang('emails.free.click', [ 'date' => Carbon\Carbon::parse($design->expired_at)->format('d/m') ])
    </p>
    <table width="215">
      <tr><td width="215" style="text-align: center;padding: 10px 0;background: #717171;">
        <a href="{{ url('/free-design/' . $design->design->code . '/' . $design->code) }}" target="_blank"  class="button">
            @lang('emails.get.now')
        </a>
      </td></tr>
    </table>
    @include('emails.ending')
    <p style="font-size: 12px; color: #717171;">
        @lang('emails.free.disclaimer', ['name' => $username])
    </p>
</div>
@endsection
