@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi_with_name', [ 'name' => $to_name ])
        <br/>
    </p>
    <p>
        @lang('emails.share.collection.greet', [ 'name' => $username ])
    </p>
    <p>
        @lang('emails.share.collection.message')
    </p>
    <p>{{ $bodyMessage }}</p>
    <p>
        @lang('emails.share.collection.click')
    </p>
    <table width="215">
      <tr><td width="215" style="text-align: center;padding: 10px 0;background: #717171;">
        <a href="{{ url('/view-collection/' . $collection->view_token) }}" class="button">
            @lang('emails.go.to.collection')
        </a>
      </td></tr>
    </table>
    @include('emails.ending')
    <p style="font-size: 12px; color: #717171;">
        @lang('emails.share.list.disclaimer', ['name' => $username])
    </p>
</div>
@endsection
