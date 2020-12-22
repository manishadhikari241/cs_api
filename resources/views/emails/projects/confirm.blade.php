@extends('emails.master')

@section('content')
    <div>
        <p>
            @lang('emails.hi')<br/>
        </p>

        <p>@lang('emails.congratulations')</p>

        <p>@lang('emails.project.confirm.select')<b><a href="{{ url('/premium/projects/' . $project->id) }}">{{ $project->translations[0]->name }}</a></b></p>

        <table style="font-family: Arial, 'Times New Roman', sans-serif; background-color: white;" width="100%" cellpadding="2">
            <tr style="border-top: 1px solid grey; border-bottom: 1px solid grey;">
                <th style="text-align: left;">@lang('emails.design')</th>
                <th style="text-align: left;">@lang('emails.creator_code')</th>
                <th style="text-align: left;">@lang('emails.code')</th>
                <th style="text-align: left;">@lang('emails.usage')</th>
            </tr>
            <tr style="border-bottom: 1px solid grey;">
                <td><img src="{{ url('/api/v1/image/thumbnail/design/' . $design->code) }}" width="50" height="50"></td>
                <td>{{ $project->studio->translations[0]->name }}</td>
                <td>{{ $design->code }}<br>{{ $design->design_name }}</td>
                <td>@lang('emails.usage.product')</td>
            </tr>
        </table>

        <p>@lang('emails.project.confirm.help')</p>

        @include('emails.ending')
    </div>
@endsection
