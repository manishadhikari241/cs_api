@extends('emails.master')

@section('content')
    <div>
        <p>
            @lang('emails.hi')<br/>
        </p>

        <p>
            @lang('emails.invite.join', [ 'name' => $name ])
        </p>

        {{-- Link also at free design zip txt, frontend share vue, invite email --}}
        <table width="215">
          <tr><td width="215" style="text-align: center;padding: 10px 0;background: #717171;">
            <a href="{{ url('/invite?referral_code=' . $user->referral_code) }}" target="_blank"  class="button">
                @lang('emails.explore.now')
            </a>
          </td></tr>
        </table>

        @include('emails.ending')
    </div>
@endsection