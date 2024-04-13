@extends('layout.default')
@section('content')
<div class="container" style="min-height: calc(63vh - 60px);margin-top: 100px;">
    <div class="row">
        <div class="col-sm-offset-3 col-sm-6">
            <form class="form-horizontal {{ $errors->any() ? ' has-error' : '' }}" role="form" method="POST" action="{{ url('/login') }}">
                <div class="row form-group">
                    <div class="col-sm-12">
                        <h2 class="form-signin-heading">{!! Lang::get('messages.please_signin') !!}</h2>
                    </div>
                </div>

                {!! csrf_field() !!}

                <div class="row form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <div class="col-sm-12">
                        <input name="email" type="text" id="email" class="form-control" placeholder="{!! Lang::get('messages.email_address') !!}"
                               required="" autofocus="" value="{{ old('email') }}">

                        @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="row form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <div class="col-sm-12">
                        <input name="password" type="password" id="password" class="form-control" placeholder="{!! Lang::get('messages.enter_password') !!}"
                               required="">

                        @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                @if ($errors->any())
                    <span class="help-block">
                        <strong>{{ implode('', $errors->all(':message')) }}</strong>
                    </span>
                @endif

                <div class="row form-group">
                    <div class="col-md-12 text-center">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember" value="remember-me"> {!! Lang::get('messages.remember_me') !!}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-12">
                        <button class="btn btn-lg black-btn btn-block" type="submit">
                            <i class="fa fa-sign-in" aria-hidden="true"></i>
                            {!! Lang::get('messages.sign_in') !!}
                        </button>
                    </div>
                    <div class="col-sm-12 text-center">
                        <a class="btn btn-link" href="{{ url('/password/reset') }}">{!! Lang::get('messages.forgot_password') !!}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('elements.footer')
@endsection
