@extends('layout.email')
@section('content')
    <h3>Hi {{$userObj->first_name.' '.$userObj->last_name}},</h3>
    <p>{!! $content !!}</p><br/>
    <p>{!! $post !!}</p>
    <p>Regards,</p>
    <p>info@javul.org</p>
@endsection
