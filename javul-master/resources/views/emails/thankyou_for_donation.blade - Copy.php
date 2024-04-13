@extends('layout.email')
@section('content')
<h3>Hi, {{$userObj->first_name.' '.$userObj->last_name}}</h3>
<p>{{$user_message}}</p>
<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align:left;">Transfer Request Details</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Amount</td>
            <td><img src="{!! url('assets/images/small-zcash-icon.png') !!}" style="width:15px;position: relative;top: 2px;"> {{ number_format($zcashTransaction->amount,2) }}</td>
        </tr>
        <tr>
            <td>Address</td>
            <td style="font-size:13px;">{{$zcashTransaction->zcash_address}}</td>
        </tr>
    </tbody>
</table>
<p>Regards,</p>
<p>info@javul.org</p>
@endsection