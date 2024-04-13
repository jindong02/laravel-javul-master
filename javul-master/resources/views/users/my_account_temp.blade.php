@extends('layout.default')
@section('page-css')
<link href="{!! url('assets/css/account.css') !!}" type="text/css" rel="stylesheet"/>
@endsection
@section('content')

<div class="container">
    <div class="row form-group">
        @include('elements.user-menu',array('page'=>'home'))
    </div>
    <!--<div class="row form-group">
        <div class="col-sm-12">
            <h2><strong>Objective: Change the World</strong></h2>
            <div>Explore projects, everywhere</div>
        </div>
    </div>-->
    <div class="row">
        <div class="col-md-3">
            <div class="profile-sidebar">
                <!-- SIDEBAR USERPIC -->
                <div class="profile-userpic">
                    @if(!empty(Auth::user()->profile_pic))
                        <img src="{!! url('assets/images/user.png') !!}" class="img-responsive" alt="">
                    @else
                        <img src="{!! url('assets/images/user.png') !!}" class="img-responsive" alt="">
                    @endif
                </div>
                <!-- END SIDEBAR USERPIC -->
                <!-- SIDEBAR USER TITLE -->
                <div class="profile-usertitle">
                    <div class="profile-usertitle-name">

                    </div>
                    <!--<div class="profile-usertitle-job">
                        Developer
                    </div>-->
                </div>
                <!-- END SIDEBAR USER TITLE -->
                <!-- SIDEBAR BUTTONS -->
                <!--<div class="profile-userbuttons">
                    <button type="button" class="btn btn-success btn-sm">Follow</button>
                    <button type="button" class="btn btn-danger btn-sm">Message</button>
                </div>-->
                <!-- END SIDEBAR BUTTONS -->
                <!-- SIDEBAR MENU -->
                <div class="profile-usermenu">
                    <ul class="nav">
                        <li class="active">
                            <a class="tablinks" data-id="overview">
                                <i class="glyphicon glyphicon-home"></i>
                                Overview
                            </a>
                        </li>
                        <li>
                            <a class="tablinks" data-id="account_settings">
                                <i class="glyphicon glyphicon-user"></i>
                                Account Settings
                            </a>
                        </li>
                        <!--<li>
                            <a href="#" class="tablinks" onclick="openTab(event, 'London')">
                                <i class="glyphicon glyphicon-ok"></i>
                                Tasks
                            </a>
                        </li>
                        <li>
                            <a href="#" class="tablinks" onclick="openTab(event, 'London')">
                                <i class="glyphicon glyphicon-flag"></i>
                                Help
                            </a>
                        </li>-->
                    </ul>
                </div>
                <!-- END MENU -->
            </div>
        </div>
        <div class="col-md-9">
            <div class="profile-content active tabcontent" id="overview">
                <h3>{{Auth::user()->first_name.' '.Auth::user()->last_name}}</h3>
                <div class="user-header">
                    <span class="glyphicon glyphicon-envelope"></span>
                    {{Auth::user()->email}}
                </div>
                <div class="user-header">
                    <span class="glyphicon glyphicon-thumbs-up"></span>
                    Skills:
                    @if(!empty($skills))
                        @foreach($skills as $skill)
                            <span class="label label-info tags">{{$skill->skill_name}}</span>
                        @endforeach
                    @endif
                </div>
                <div class="user-header">
                    <span class="glyphicon glyphicon-bookmark"></span>
                    Area of Interest:
                    @if(!empty($interestObj))
                        @foreach($interestObj as $interest)
                            <span class="label label-info tags">{{$interest->title}}</span>
                        @endforeach
                    @endif
                </div>
                <span class="glyphicon glyphicon-map-marker"></span>
                {{\App\Country::getName(Auth::user()->country_id)}}
                <span class="glyphicon glyphicon-menu-right"></span>
                {{\App\State::getName(Auth::user()->state_id)}}
                <span class="glyphicon glyphicon-menu-right"></span>
                {{\App\City::getName(Auth::user()->city_id)}}
            </div>
            <div class="profile-content tabcontent" id="account_settings">
                Account settings
            </div>
        </div>
    </div>
</div>
@include('elements.footer')
@endsection
@section('page-scripts')
<script>
    $(function(){
        $(".tablinks").on('click',function(){
            $(".tabcontent").hide();
            $(".nav").find("li[class='active']").removeClass('active');
            $(this).parent('li').addClass('active');
            $("#"+$(this).attr('data-id')).show().addClass('active');
            return false;
        })
    });
</script>
@endsection