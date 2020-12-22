@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>

    <p>@lang('emails.request.reminder.day', ['day' => 7 - Carbon\Carbon::now()->diffInDays($request->created_at)])</p>

    <p>@lang('emails.request.reminder.check')</p>

    <p>@lang('emails.request.reminder.name', ['name' => $request->name])</p>

    <br>
    <table width="215">
      <tr><td width="215" style="text-align: center;padding: 10px 0;background: #717171;">
        <a href="{{ url('/account/studio/requests/' . $request->id) }}" target="_blank" style="text-decoration: none; display: block; font-family: Arial, 'Times New Roman', sans-serif; font-size: 16px; line-height: 18px; width: 215px;  text-align: center; color: #fff; background-color: #717171; -webkit-border-radius: 3px; border-radius: 3px; display: inline-block;">
            @lang('emails.go.to.design.project')
        </a>
      </td></tr>
    </table>
    <br>

    <p>@lang('emails.request.reminder.attention')</p>

    @include('emails.ending')
</div>
@endsection
