@extends('layout.default')
@section('page-css')
    <style>
        .time_digit{position:inherit;}
        .div-table-first-cell{width: 110px;}
        .list-item-main{width: 140px;}
        .border-main{left:140px;}
        @media (max-width: 520px){
            .div-table-first-cell{width: 65px;}
            .border-main{left:94px;}
            .list-item-main{width: 94px;}
        }
        .list-group-item:nth-last-child(2) .border-main div{height:50px;}
    </style>
@endsection
@section('content')
<div class="container">
    <div class="row form-group" style="margin-bottom:15px;">
        @include('elements.user-menu',['page'=>'units'])
    </div>
    <div class="row form-group">
        <div class="col-md-12">
            <div class="left">
                <div class="site_activity_loading loading_dots" style="position: absolute;top:20%;left:43%;z-index: 9999;display: none;">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="site_activity_list">
                    <div class="panel panel-grey panel-default">
                        <div class="panel-heading">
                            <h4>{{$site_activity_text}}</h4>
                        </div>
                        <div class="panel-body list-group">
                            @if(count($site_activity) > 0)
                                <?php
                                $timezone = 'UTC';
                                if(!empty(\Auth::check() && \Auth::user()->timezone))
                                    $timezone = Auth::user()->timezone;
                                ?>
                                @foreach($site_activity as $index=>$activity)
                                    <div class="list-group-item" style="padding: 0px;padding-bottom:4px">
                                        <div class="row" style="padding: 7px 15px">
                                            <div class="col-xs-12" style="display: table">
                                                <div style="display:table-row">
                                                    <div class="div-table-first-cell">
                                                        <span class="tooltipster" title='{!! $activity->created_at->timezone($timezone)->format('Y-m-d H:i:s') !!}'>{!! \App\Library\Helpers::timetostr($activity->created_at->timezone($timezone)->format('Y-m-d H:i:s')) !!}</span>
                                                    </div>
                                                    <div class="div-table-second-cell">
                                                        <div class="circle activity-refresh">
                                                            <i class="fa fa-refresh"></i>
                                                        </div>
                                                    </div>
                                                    <div class="div-table-third-cell">
                                                        @if($type=="activities")
                                                            {!! $activity->comment !!}
                                                        @else
                                                            {!! $activity->content !!}
                                                        @endif
                                                    </div>
                                                    <div class="border-main child_{{$index}}">
                                                        <div></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-item-main child_{{$index}}"></div>
                                    </div>
                                @endforeach
                                @if($site_activity->lastPage() > 1 && $site_activity->lastPage() != $site_activity->currentPage())
                                    <div class="list-group-item text-right more-btn">
                                        <a href="#"class="btn black-btn more_site_activity_btn" data-from_page='global'
                                           data-url="{{$site_activity->url($site_activity->currentPage()+1) }}"
                                           type="button">MORE ACTIVITY <span class="more_dots">...</span>
                                        </a>
                                    </div>
                                @else
                                    <div class="list-group-item text-right more-btn" style="border-top:0px;">
                                    </div>
                                @endif
                            @else
                                <div class="list-group-item">
                                    No activity found.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('elements.footer')
@endsection
