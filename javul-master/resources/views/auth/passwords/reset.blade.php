@extends('layout.default')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-offset-3 col-sm-6">

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
                {!! csrf_field() !!}

                <div class="row form-group">
                    <div class="col-sm-12">
                        <h2 class="form-signin-heading">{!! Lang::get('messages.reset_your_password') !!}</h2>
                    </div>
                </div>
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="row form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <div class="col-md-12">
                        <input type="email" class="form-control" name="email" value="{{ $email or old('email') }}" placeholder="{!! Lang::get('messages.email_address') !!}">
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <div class="col-md-12">
                        <input name="password" type="password" id="password" class="form-control" placeholder="{!! Lang::get('messages.enter_password') !!}"
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
                               placeholder="{!! Lang::get('messages.confirm_password') !!}" required="" />

                        @if ($errors->has('password_confirmation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-sm-12">
                        <button type="submit" class="btn orange-bg btn-block">
                            <i class="fa fa-btn fa-refresh"></i>&nbsp;{!! Lang::get('messages.reset_password') !!}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('elements.footer')
@endsection
