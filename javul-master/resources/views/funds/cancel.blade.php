@extends('layout.default')
@section('page-meta')
<title>Payment Cancelled - Javul.org</title>
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
			<div class="text-center">
				<h2 style="margin-bottom: 20px;">{{$message}}</h2>
				<div style="margin-bottom: 25px;">
					<i class="fa fa-times-circle-o fa-3x text-danger" style="margin:10px;position:relative;bottom:-7px"></i>
					<span style="font-size:16px;">Cancelled</span>
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
						<lable>{{$obj->amount}}</lable>
					</div>
				</div>
				<div class="row from-group">
					<div class="col-sm-6 text-right">
						<label for="date"  class="control-lable">Date Time: </label>
						<lable>{{date('Y-m-d H:i:s')}}</lable>
					</div>
					<div class="col-sm-6">
						<label for="Ref"  class="control-lable">Type: </label>
						<lable>Cancelled</lable>
					</div>
				</div>
			</div>            
        </div>
    </div>
</div>

@include('elements.footer')
@endsection
