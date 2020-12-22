@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>

	<p>
        @lang('emails.studio.invite.message', ['name' => $studio->translations[0]->name])
    </p>

    <br>
    <table width="215">
      <tr><td width="215" style="text-align: center;padding: 10px 0;background: #717171;">
        <a href="{{ url('/premium/studios/' . $studio->id . '?invitation_code=' . $studio->invitation_code) }}" target="_blank" style="text-decoration: none; display: block; font-family: Arial, 'Times New Roman', sans-serif; font-size: 16px; line-height: 18px; width: 215px;  text-align: center; color: #fff; background-color: #717171; -webkit-border-radius: 3px; border-radius: 3px; display: inline-block;">
            @lang('emails.discover.now')
        </a>
      </td></tr>
    </table>
    <br>

    @include('emails.ending')
    
    {{-- <p style="font-size: 12px;">
        @lang('emails.studio.wrong.invite')
    </p> --}}
</div>
@endsection
