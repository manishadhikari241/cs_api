@extends('emails.master')

@section('content')
<div>
    <p>
        @lang('emails.hi')<br/>
    </p>
    <p>
      A new product request has been submitted. 
    </p>
    <br><br>
    <img src="{{ uploadsPath('uploads/good-request/' . $goodRequest->image) }}" width="215" height="215">
    @include('emails.ending')
</div>
@endsection
