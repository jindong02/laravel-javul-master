@extends('layout.default')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-offset-3 col-sm-6">
            <form class="form-horizontal" id="register-form" role="form" method="POST" action="{{ url('/register') }}">
                {!! csrf_field() !!}

                <div class="form-group row">
                    <div class="col-sm-12">
                        <h2 class="form-signin-heading">{!! \Lang::get('messages.please_signup') !!}</h2>
                    </div>
                </div>

				<div class="row form-group{{ $errors->has('user_name') || $errors->has('username_duplicate') ? ' has-error' : '' }}">
                    <div class="col-md-12" style="padding: ">
                        <input type="text" id="uname" class="form-control" required="" name="user_name" value="{{ old('user_name') }}"  placeholder="Enter User Name*">
                       @if ($errors->has('user_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('user_name') }}</strong>
                            </span>
                       @elseif($errors->has('username_duplicate'))
                            <span class="help-block ">
                                <strong>{{ $errors->first('username_duplicate') }}</strong>
                            </span>
                       @endif
                    </div>
                    <img id="user_img" src="" style="float: right;position: absolute;margin-top: 7px">
                </div>
                <div class="row form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                    <div class="col-md-12">
                        <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}"  placeholder="{!!
                        \Lang::get('messages.enter_firstname') !!}*" required>
                        @if ($errors->has('first_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('first_name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                    <div class="col-md-12">
                        <input name="last_name" type="text" id="last_name" value="{{ old('last_name') }}" class="form-control"
                               placeholder="{!! \Lang::get('messages.enter_lastname') !!}*" required />
                        @if ($errors->has('last_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('last_name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <div class="col-md-12">
                        <input name="email" type="email" value="{{ old('email') }}" id="email" class="form-control" placeholder="{!! Lang::get('messages.email_address') !!}*"
                               required="" >

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                    <img id="email_img" src="" style="float: right;position: absolute;margin-top: 7px">
                </div>

                <!--<div class="row form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                    <div class="col-md-12">
                        <textarea name="address" value="{{ old('address') }}" id="address" class="form-control"
                                  placeholder="{!! Lang::get('messages.address') !!}"></textarea>
                        @if ($errors->has('address'))
                            <span class="help-block">
                                <strong>{{ $errors->first('address') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>-->

                <!--<div class="row form-group{{ ($errors->has('country') || $errors->has('state') || $errors->has('city')) ? 'has-error': '' }}">
                    <div class="col-sm-4">
                        <select name="country" id="country" class="form-control">
                            <option value="">Select Country</option>
                            <option value="1">Australia</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select name="state" id="state" class="form-control">
                            <option value="">Select State</option>
                            <option value="1">Melbourne</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select name="city" id="city" class="form-control">
                            <option value="">Select City</option>
                            <option value="1">Melbourne</option>
                        </select>
                    </div>
                </div>-->

                <div class="row form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <div class="col-md-12">
                        <input name="password" type="password" id="password" class="form-control" placeholder="{!! Lang::get('messages.enter_password') !!}*"
                               required="">
                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <div class="col-md-12">
                        <input name="password_confirmation" type="password" id="password_confirmation" class="form-control"
                               placeholder="{!! Lang::get('messages.confirm_password') !!}*" required="" />

                        @if ($errors->has('password_confirmation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <input type="hidden" name="g-recaptcha-response-name" id="g-recaptcha-response">
                <div class="row form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                    <div class="g-recaptcha col-sm-7"
                         data-sitekey="6LdqlBAnAAAAAKfLVMR-3BC4vWv35c4Z-2rvSP30"
                         data-callback='onSubmit'
                         data-action='register-form' onclick="sendToken(event)">
                    </div>
                    <div class="col-sm-5">
                        @if( $errors->has('g-recaptcha-response'))
                            <span style="color:#a94442">{{ $errors->first('g-recaptcha-response')}}</span>
                        @endif
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-12">
                        <button class="btn btn-lg orange-bg btn-block" type="submit">
                            <i class="fa fa-btn fa-user"></i> {!! Lang::get('messages.signup') !!}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('elements.footer')
@endsection
@section('page-scripts')
    <script>
        $(function(){
            $("#uname").keyup(function(){
                var username= $.trim($(this).val());
                var url ='{!! url("") !!}';
                if(username.length>5){
                    $("#user_img").attr({src:"{!! url('assets/images/loader.gif') !!}"});
                    $.ajax({
                        type:'post',
                        url:'{!! url("check_username") !!}',
                        dataType:'json',
                        data:{_token:'{{csrf_token()}}',check:username},
                        success: function(data){
                            if(data.success==true){
                                $("#user_img").attr({src: url+"/assets/images/not-available.png"});
                                $("#uname").parent('.col-md-12').find(".help-block").remove();
                                $("#uname").after('<span class="help-block "><strong>The username already exist in system.</strong></span>');
                                $("#uname").parent('.col-md-12').addClass('has-error');
                            }else{
                                $("#user_img").attr({src:url+"/assets/images/available.png"});
                                $("#uname").parent('.col-md-12').find(".help-block").remove();
                                $("#uname").parent('.col-md-12').removeClass('has-error');
                            }
                        }
                    });
                }
                if(username.length<6){$("#user_img").attr({src:""});}
            });
            $("#email").keyup(function(){
                var email = $.trim($(this).val());
                var url ='{!! url("") !!}';
                if(email !== ''){
                    $("#email_img").attr({src:"{!! url('assets/images/loader.gif') !!}"});
                    $.ajax({
                        type:'post',
                        url:'{!! url("check_email") !!}',
                        dataType:'json',
                        data:{_token:'{{csrf_token()}}',email:email},
                        success: function(data){
                            if(data.success==true){
                                $("#email_img").attr({src: url+"/assets/images/not-available.png"});
                                $("#email").parent('.col-md-12').find(".help-block").remove();
                                $("#email").after('<span class="help-block "><strong>'+data.error[0]+'</strong></span>');
                                $("#email").parent('.col-md-12').addClass('has-error');
                            }else{
                                $("#email_img").attr({src:url+"/assets/images/available.png"});
                                $("#email").parent('.col-md-12').find(".help-block").remove();
                                $("#email").parent('.col-md-12').removeClass('has-error');
                            }
                        }
                    });
                }
                if(email.length<6){$("#email_img").attr({src:""});}
            });
        });
    </script>

    <script>
        function sendToken(e) {
            e.preventDefault();
            grecaptcha.ready(function() {
                grecaptcha.execute('6LdqlBAnAAAAAKfLVMR-3BC4vWv35c4Z-2rvSP30', {action: 'register-form'}).then(function(token) {
                    console.log(token);
                    document.getElementById('g-recaptcha-response').value = token;
                    document.getElementById('register-form').submit();
                });
            });
        }
    </script>
@endsection

