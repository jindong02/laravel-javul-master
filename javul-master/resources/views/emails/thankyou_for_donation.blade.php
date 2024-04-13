@extends('layout.email')
@section('content')
<h3>Hi, {{$userObj->first_name.' '.$userObj->last_name}}</h3>
<p>Thank you for the donation!!. We have received $@if($mailFrom == "PAYPAL"){{ number_format($fundObj->amount,2) }}@else{{ number_format($zcashTransaction->amount,2) }}@endif and the transaction details are below.</p>
<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align:left;">Transaction Details</th>
        </tr>
    </thead>
    <tbody>
        @if($mailFrom == "PAYPAL")
            <tr>
                <td>Transaction Id</td>
                <td>{{$paypalTransaction->donate_paypal_id}}</td>
            </tr>
            <tr>
                <td>Amount</td>
                <td>${{ number_format($fundObj->amount,2) }}</td>
            </tr>
        @else
            <tr>
                <td>Address</td>
                <td>{{$zcashTransaction->zcash_address}}</td>
            </tr>
            <tr>
                <td>Amount</td>
                <td>${{ number_format($zcashTransaction->amount,2) }}</td>
            </tr>
            <tr>
                <td>Transaction Id</td>
                <td style="font-size:13px;">{{$zcashTransaction->transaction_id}}</td>
            </tr>
        @endif
    </tbody>
</table>
<p>Regards,</p>
<p>info@javul.org</p>
@endsection