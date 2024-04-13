@extends('layout.email')
@section('content')
<h3>Hi {{$userObj->first_name.' '.$userObj->last_name}},</h3>
@if($type == "added")
    <p>You have added item : {{$itemObj->name}} to watchlist.</p>
@else
    <p>You have removed item : {{$itemObj->name}} from watchlist.</p>
@endif
<p>Please check item by clicking this <a href="{{\Config::get('app.url').'/my_watchlist'}}">link</a></p>
<p>Regards,</p>
<p>info@javul.org</p>
@endsection
