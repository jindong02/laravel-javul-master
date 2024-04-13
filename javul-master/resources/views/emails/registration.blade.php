@extends('layout.email')
@section('content')
<h3>Hi, {{$userObj->first_name.' '.$userObj->last_name}}</h3>
<p>Welcome to Javul.org</p><br/>
<p>Regards,</p>
<p>info@javul.org</p>
@endsection
