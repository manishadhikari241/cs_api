@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>

    <p>
    	@lang('emails.studio.accept.message', ['name' => $studio->translations[0]->name])
    </p>
    
    <table width="215">
        <tr><td width="215" style="text-align: center;padding: 10px 0;background: #717171;">
            <a href="{{ url('/premium/studios/' . $studio->id) }}" target="_blank" style="text-decoration: none; display: block; font-family: Arial, 'Times New Roman', sans-serif; font-size: 16px; line-height: 18px; width: 215px;  text-align: center; color: #fff; background-color: #717171; -webkit-border-radius: 3px; border-radius: 3px; display: inline-block;">
                Visit Studio Now
            </a>
        </td></tr>
    </table>
    
    {{-- <p>
        @lang('emails.studio.wrong.register')
    </p> --}}

    @include('emails.ending')
</div>
@endsection
