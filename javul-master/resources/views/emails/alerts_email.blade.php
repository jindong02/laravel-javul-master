@extends('layout.email')
@section('content')
    <h3>Hi @if($userObj->first_name && $userObj->last_name){{$userObj->first_name.' '.$userObj->last_name.','}}@else{{ $userObj->username.',' }}@endif</h3>
    <p>{!! $content !!}</p>
    <br/>
    <p style="display: inline-block;text-align: center;width:100%;font-size: 13px;">
        <a href="{!! url('account#account_settings') !!}" style="display:inline-block">Manage Notification Settings</a>&nbsp;|&nbsp;
        <a href="{!! url('my_watchlist')!!}" style="display:inline-block">Edit Watchlist</a>
    </p>
    <p>Regards,</p>
    <p>info@javul.org</p>
@endsection
