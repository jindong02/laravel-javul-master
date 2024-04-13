@extends('layout.email')
@section('content')
<h3>Hi, {{$userObj->first_name.' '.$userObj->last_name}}</h3>
<p>{{$user_message}}</p>
<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align:left;">Transaction Details</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Amount</td>
            <td>${{ number_format($zcashTransaction->amount,2) }}</td>
        </tr>
        <tr>
            <td>Address</td>
            <td>{{$zcashTransaction->zcash_address}}</td>
        </tr>
        @if(isset($zcashTransaction->status) && $zcashTransaction->status == "approved")
            <tr>
                <td>Transaction Id</td>
                <td style="font-size:12px;">{{$zcashTransaction->transfer_transaction_id}}</td>
            </tr>
        @endif
    </tbody>
</table>
<p>Regards,</p>
<p>info@javul.org</p>
@endsection