@section('page-meta')
<title>User: {{$userObj->first_name.' '.$userObj->last_name}} - Javul.org</title>
@endsection
<div class="grey-bg" style="padding-top:20px;margin-bottom: 20px; ">
        <div class="row">
            <div class="col-sm-4 text-center form-group">
                <div>
                @if(!empty($userObj->profile_pic) )
                    <img src="{{ $userObj->profile_pic }} " class="img-rounded-circle" style="width: 160px;"/>
                @else
                    <img src="{!! url('assets/images/user.png')!!}" class="img-rounded-circle"/>
                @endif
                </div>

                <label class="control-label" style="margin-bottom:0px">Task Completion Ratings
                    <input id="input-3" name="input-3" value="{{$rating_points}}" class="rating-loading">
                    ({{$rating_points}}/5)
                </label>



            </div>
            <div class="col-sm-8 hidden-xs">
                <div class="user-header">
                    <h3>{{$userObj->first_name.' '.$userObj->last_name}}</h3>
                </div>
                <div class="user-header">
                    <span class="glyphicon glyphicon-time"></span>
                    Account age: {{$userObj->age}}</label>
                </div>
                <div class="user-header">
                    <span class="glyphicon glyphicon-thumbs-up"></span>
                    <?php $job_skills = explode(",",$userObj->job_skills); ?>
                    Skills:
                    @if(!empty($job_skills))
                        @foreach($job_skills as $skill)
                            <span class="label label-info tags">{{\App\JobSkill::getName($skill)}}</span>
                        @endforeach
                    @endif
                </div>
                <div class="user-header">
                    <span class="glyphicon glyphicon-bookmark"></span>
                    Area of Interest:
                    <?php $area_of_interest = explode(",",$userObj->area_of_interest); ?>
                    @if(!empty($area_of_interest))
                        @foreach($area_of_interest as $interest)
                            <span class="label label-info tags">{{\App\AreaOfInterest::getName($interest)}}</span>
                        @endforeach
                    @endif
                </div>
                <span class="glyphicon glyphicon-map-marker"></span>
                {{\App\Country::getName($userObj->country_id)}}
                <span class="glyphicon glyphicon-menu-right"></span>
                {{\App\State::getName($userObj->state_id)}}
                <span class="glyphicon glyphicon-menu-right"></span>
                {{\App\City::getName($userObj->city_id)}}
            </div>
            <div class="col-xs-12 visible-xs text-center">
                <div class="user-header">
                    <h3>{{$userObj->first_name.' '.$userObj->last_name}}</h3>
                </div>
            </div>
            <div class="col-xs-12 visible-xs">
                <div class="user-header">
                    <span class="glyphicon glyphicon-time"></span>
                    Account age: {{$userObj->created_at}}</label>
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
                {{\App\Country::getName($userObj->country_id)}}
                <span class="glyphicon glyphicon-menu-right"></span>
                {{\App\State::getName($userObj->state_id)}}
                <span class="glyphicon glyphicon-menu-right"></span>
                {{\App\City::getName($userObj->city_id)}}
            </div>
        </div>

    </div>