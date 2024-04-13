@extends('layout.default')

<!-- Main Content -->
@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-offset-3 col-sm-6">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
                {!! csrf_field() !!}
                <div class="row form-group">
                    <div class="col-sm-12">
                        <h2 class="form-signin-heading">{!! Lang::get('messages.enter_registered_email') !!}</h2>
                    </div>
                </div>
                <div class="row form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <div class="col-md-12">
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{!! Lang::get('messages.email_address') !!}">
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-sm-12">
                        <button class="btn btn-lg orange-bg btn-block" type="submit">
                            <i class="fa fa-btn fa-envelope"></i>&nbsp;{!! Lang::get('messages.send_password_reset_link') !!}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('elements.footer')
@endsection
