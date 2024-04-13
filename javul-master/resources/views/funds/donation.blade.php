@extends('layout.default')
@section('page-meta')
<title>Donation - Javul.org</title>
@endsection
@section('page-css')
<link href="{!! url('assets/plugins/bootstrap-star-rating-master/css/star-rating.css') !!}" media="all" rel="stylesheet" type="text/css" />
<style>
    hr, p{margin:0 0 10px !important;}
    .files_image:hover{text-decoration: none;}
    .file_documents{display: inline-block;padding: 10px;}
    select[name='exp_month']{width:80px;display: inline-block;}
    select[name="exp_year"]{width:100px;display: inline-block;}

</style>
@endsection
@section('content')
@php $obj_identifier = get_class($obj); @endphp
<div class="container">
    <div class="row form-group" style="margin-bottom:15px;">
        @include('elements.user-menu',array('page'=>'home'))
    </div>
    <div class="row form-group">
        <div class="col-sm-12">
            <div class="col-sm-12 grey-bg unit_description">
                @if(\Request::segment(3) == "user")
                    @include('users.user-profile',['userObj'=>$obj])
                @else
                <h2 class="unit-heading"><span class="glyphicon glyphicon-edit"></span> &nbsp; <strong>{{$obj->name}}</strong></h2>
                <div class="panel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-12">
                                <strong>{{ucfirst(trim($donateTo))}} Information<span class="caret"></span> </strong>
                            </div>
                            <div class="col-xs-5">{{{ucfirst(trim($donateTo))}}} Name</div>
                            <div class="col-xs-7 text-right">{{$obj->name}}</div>
                            <div class="col-xs-5">{!! trans('messages.funds') !!}</div>
                            <div class="col-xs-7 text-right">{!! trans('messages.available') !!}: {{number_format($availableFunds,2)}}$</div>
                            <div class="col-xs-5">{!! trans('messages.awarded') !!}</div>
                            <div class="col-xs-7 text-right">{{number_format($awardedFunds,2)}}$</div>
                        </div>

                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-12">
            <!--<h3>Available Balance : $<span class="availableLabel">{{$availableBalance}}</span></h3>-->
        </div>
    </div>

    <!--<div class="radio radio-primary">
        <input type="radio" name="credit_available_bal" id="radio1" value="availablebalance" @if($availableBalance == 0 ||
        $availableBalance < 0) disabled @endif>
        <label for="radio1">
            Use available balance
        </label>
    </div>-->
    <!--<div class="radio radio-primary">
        <input type="radio" name="credit_available_bal" id="radio2" value="credit_card">
        <label for="radio2">
            Use credit card
        </label>
    </div>-->
    @if($availableBalance > 0)
    <!--<div class="row form-group donationDiv availablebalance"  style="display: none;">
        <div class="col-sm-12">
            <div style="background-color: #eeeeee;padding:20px;">
                <label class="control-label">
                    <span style="display: inline-block;font-size: 24px;">{{$availableBalance}}</span>
                    <span style="display: inline-block;font-size: 24px;padding:10px">-</span>
                    <div style="display:inline-block;width: 180px;font-size: 24px;position:relative;top:9px" class="">
                        <input type="text" data-numeric name="amount_from_available_bal" class="form-control"/>
                    </div>
                    <input type="button" value="Pay now" id="pay_now" class="btn orange-bg">
                </label>

            </div>
        </div>
    </div>-->
    @endif

    @if($obj_identifier != 'App\Objective')
    <form accept-charset="UTF-8" action="{!! url('funds/donate-amount') !!}" class="simple_form form-horizontal" method="post"
          novalidate="novalidate" id="donate_amount_form">
        {{ csrf_field() }}
        @if(count($errors->all()) > 0)
            <?php $err_msg ='';?>
            @foreach($errors->all() as $err)
            <?php $err_msg.='<span>'.$err.'</span>';?>
            @endforeach

            <div class="alert alert-danger">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <img src="{!! url('assets/images/error-icon.png') !!}"> <strong>Error!</strong> {!!$err_msg!!}
            </div>

        @endif
        @if($current_payment_method == "PAYPAL")
        <div class="row form-group donationDiv credit_card"  >
            <div class="col-sm-4">
                <label for="amount" class="control-label">Amount to Donate</label>
                <input type="text" value="" name="donate_amount" id="donate_amount" data-numeric
                       placeholder="Amount" class="form-control" required autocomplete="off" maxlength="10">

                <label id="paypal-fees" class="control-label"></label>
            </div>
        </div>
        @endif
        <div class="row form-group donationDiv credit_card">
            <div class="col-sm-4">
                @if($current_payment_method == "Zcash")
                    <button id="submit_donate" class="btn black-btn">Donate With Zcash</button>
                @else
                    <input type="image" id="submit_donate"  src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif"/>
                @endif
                <input type="hidden" id="paymentMethod" name="paymentMethod" value="{{ $current_payment_method }}"/>
            </div>
        </div>
    </form>
    @else
        <form accept-charset="UTF-8" action="{!! url('funds/transfer-from-unit') !!}" class="simple_form form-horizontal" method="post"
              novalidate="novalidate" id="donate_amount_form">
            {{ csrf_field() }}
            @if(count($errors->all()) > 0)
                <?php $err_msg ='';?>
                @foreach($errors->all() as $err)
                    <?php $err_msg.='<span>'.$err.'</span>';?>
                @endforeach

                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <img src="{!! url('assets/images/error-icon.png') !!}"> <strong>Error!</strong> {!!$err_msg!!}
                </div>

            @endif
            <div class="row form-group donationDiv credit_card"  >
                <div class="col-sm-4">
                    <label for="amount" class="control-label">Amount to Donate For User</label>
                    <input type="text" value="" name="donate_amount" id="donate_amount" data-numeric
                           placeholder="Amount" class="form-control" required autocomplete="off" maxlength="10">

                    <label id="paypal-fees" class="control-label" @if($current_payment_method == "Zcash") style="display:none" @endif></label>
                </div>
            </div>
            <div class="row form-group donationDiv credit_card"  >
                <div class="col-sm-2 col-xs-12">
                    @if($current_payment_method == "Zcash")
                        <button id="submit_donate" class="btn black-btn" style="padding: 6px 12px;line-height: unset !important;">Donate With Zcash</button>
                    @else
                        <input type="image" id="submit_donate"  src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif"/>
                    @endif
                    <input type="hidden" id="paymentMethod" name="paymentMethod" value="{{ $current_payment_method }}"/>
                </div>
                <div class="col-sm-2 col-xs-12">
                    <button class="btn btn-primary">Transfer from Unit</button>
                </div>
            </div>
        </form>
    @endif
</div>

@include('elements.footer')
@endsection
@section('page-scripts')
<script src="{!! url('assets/plugins/bootstrap-star-rating-master/js/star-rating.js') !!}" type="text/javascript"></script>
<script type="text/javascript">
    $(function(){
        $('#input-3').rating({displayOnly: true, step: 0.1,size:'xs'});
        $('#tabs').tab();
    })
</script>
<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "1000",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    var url = '{{url("assets/images")}}';
    var avlblamt = {{$availableBalance}};
    var msg_flag ='{{ $msg_flag }}';
    var msg_type ='{{ $msg_type }}';
    var msg_val ='{{ $msg_val }}';

    $('#donate_amount').on('input propertychange', function() {
        var value = Number($(this).val());
        var result = (value + 0.30) / (1 - 0.029);

        if($(this).val().length == 0 || value == 0) {
            $('#paypal-fees').text('');
        } else {
            $('#paypal-fees').text('Subtotal with PayPal fees: ' + result.toFixed(2));
        }
    });

    $('#transferFromUnit').on('click', function(e) {
        e.preventDefault();

        $('#donate_amount_form').attr('action', '{!! url('funds/transfer-from-unit') !!}');
    });
</script>
<script src="{!! url('assets/js/custom_tostr.js') !!}" type="text/javascript"></script>
<script type="text/javascript" src="{!! url('assets/js/jquery.payment.js') !!}"></script>
<script type="text/javascript" src="{!! url('assets/js/donations.js') !!}"></script>

@endsection