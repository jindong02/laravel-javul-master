@extends('layout.default')
@section('page-meta')
<title>Donation Success - Javul.org</title>
@endsection
@section('page-css')
<style>
    hr, p{margin:0 0 10px !important;}
    .files_image:hover{text-decoration: none;}
    .file_documents{display: inline-block;padding: 10px;}
    select[name='exp_month']{width:80px;display: inline-block;}
    select[name="exp_year"]{width:100px;display: inline-block;}

</style>
@endsection
@section('content')

<div class="container">
    <div class="row form-group">
        <div class="col-sm-12">
            @if($messageType)
                <div class="text-center">
                    <h2 style="margin-bottom: 20px;">Thank you for your donation.</h2>
                    <div style="margin-bottom: 25px;">
                        <i class="fa fa-check-circle fa-3x text-success" style="margin:10px;position:relative;bottom:-7px"></i>
                        <span style="font-size:16px;">Success</span>
                    </div>
                </div>
                <div class="payment_response_page form-group">
                    <div class="row from-group">
                        <div class="col-sm-6 text-right">
                            <label for="payment_id"  class="control-lable">Payment ID : </label>
                            <lable>{{$payment_id}}</lable>
                        </div>
                        <div class="col-sm-6">
                            <label for="Result"  class="control-lable">Amount :</label>
                            <lable>@if(isset($obj)) {{$obj->amount}} @else {{ $amount }} @endif</lable>
                        </div>
                    </div>
                    <div class="row from-group">
                        <div class="col-sm-6 text-right">
                            <label for="date"  class="control-lable">Date Time: </label>
                            <lable>{{date('Y-m-d H:i:s')}}</lable>
                        </div>
                        <div class="col-sm-6">
                            <label for="Ref"  class="control-lable">Type: </label>
                            <lable>Success</lable>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center">
                    <h2 style="margin-bottom: 20px;">
                        @if(isset($err_message))
                            {{ $err_message }}
                        @else
                            Something goes wrong. Please try again later.
                        @endif
                    </h2>
                    <div style="margin-bottom: 25px;">
                        <i class="fa fa-times-circle-o fa-3x text-danger" style="margin:10px;position:relative;bottom:-7px"></i>
                        <span style="font-size:16px;">Failure</span>
                    </div>
                </div>
                <div class="payment_response_page form-group">
                    <div class="row from-group">
                        <div class="col-sm-6 text-right">
                            <label for="payment_id"  class="control-lable">Payment ID : </label>
                            <lable>@if(isset($obj)) {{$obj->payment_id}} @else {{ $payment_id }} @endif</lable>
                        </div>
                        <div class="col-sm-6">
                            <label for="Result"  class="control-lable">Amount :</label>
                            <lable>@if(isset($obj)) {{$obj->amount}} @else {{ $amount }} @endif</lable>
                        </div>
                    </div>
                    <div class="row from-group">
                        <div class="col-sm-6 text-right">
                            <label for="date"  class="control-lable">Date Time: </label>
                            <lable>{{date('Y-m-d H:i:s')}}</lable>
                        </div>
                        <div class="col-sm-6">
                            <label for="Ref"  class="control-lable">Type: </label>
                            <lable>Fail</lable>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@include('elements.footer')
@endsection
