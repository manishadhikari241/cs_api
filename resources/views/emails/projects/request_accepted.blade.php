@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>

    <p>@lang('emails.request.accept.intro', ['studio' => $request->studio->translations[0]->name, 'project' => $request->name])</p>

    <table style="font-family: Arial, 'Times New Roman', sans-serif; background-color: white;" width="100%" cellpadding="2">
        <tr>
            <td>@lang('emails.package.price')</td>
            <td>{{ $request->projectPackage->price }} USD</td>
        </tr>
        <tr>
            <td>@lang('emails.package.design')</td>
            <td>{{ $request->projectPackage->expected_quantity }}</td>
        </tr>
        <tr>
            <td>@lang('emails.package.revision')</td>
            <td>{{ $request->projectPackage->max_revision }}</td>
        </tr>
        <tr>
            <td>@lang('emails.package.moodboard')</td>
            <td>{{ $request->projectPackage->has_moodboard ? __('emails.yes') : __('emails.no') }}</td>
        </tr>
        <tr>
            <td>@lang('emails.package.deliver')</td>
            <td>{{ Carbon\Carbon::parse($request->expected_at)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <p>@lang('emails.request.accept.charge', ['price' => $request->projectPackage->price])</p>

    <br>
    <table width="215">
      <tr><td width="215" style="text-align: center;padding: 10px 0;background: #717171;">
        <a href="{{ url('/account/premium/project-payments/' . $request->payment->id) }}" target="_blank" style="text-decoration: none; display: block; font-family: Arial, 'Times New Roman', sans-serif; font-size: 16px; line-height: 18px; width: 215px;  text-align: center; color: #fff; background-color: #717171; -webkit-border-radius: 3px; border-radius: 3px; display: inline-block;">
            @lang('emails.download.invoice')
        </a>
      </td></tr>
    </table>
    <br>

    @include('emails.ending')
</div>
@endsection
