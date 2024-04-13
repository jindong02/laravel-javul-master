@extends('layout.email',['report_concern'=>true])
@section('content')
    <h3>Hi Admin,</h3>
    <p>Visited URL: <a href="{{$url}}">{{$url}}</a></p>
    <p>Message:{{$messages}}</p>
    <p>Submitted by: {{$name}}</p>
    <p><i>Submitted through "Report a concern" webform.</i></p>
    <p>Regards,</p>
    <p>info@javul.org</p>
@endsection
