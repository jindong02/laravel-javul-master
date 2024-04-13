@extends('layout.email')
@section('content')
<h3>Hi {{$userObj->first_name.' '.$userObj->last_name}},</h3>
<p>You have received fund : {{$amount}}.</p>
<p>Please check item by clicking this <a href="{{\Config::get('app.url').'/account'}}">link</a></p>
<p>Regards,</p>
<p>info@javul.org</p>
@endsection
