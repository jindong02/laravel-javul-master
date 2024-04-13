@extends('layout.email')
@section('content')
<h3>Hi {{$userObj->first_name.' '.$userObj->last_name}},</h3>
<p>Subject : {{$subject}}</p>
<p>Message : {!! $message_text !!}</p>

<p>Regards,</p>
<p>info@javul.org</p>
@endsection
