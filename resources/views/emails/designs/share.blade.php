@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>

    <p>
        @lang('emails.shared.design', [ 'name' => $name ])
    </p>

    <p>
        <img src="{{ url('/api/v1/image/thumbnail/design/' . $design->code) }}" width="200" height="200">
    </p>

    <table width="215">
      <tr><td width="215" style="text-align: center;padding: 10px 0;background: #717171;">
        <a href="{{ url('/invite?referral_code=' . $user->referral_code) }}" target="_blank"  class="button">
            @lang('emails.explore.now')
        </a>
      </td></tr>
    </table>

    @include('emails.ending')
    {{-- <p style="font-size: 12px; color: #717171;">
        @lang('emails.free.disclaimer', ['name' => $username])
    </p> --}}
</div>
@endsection
