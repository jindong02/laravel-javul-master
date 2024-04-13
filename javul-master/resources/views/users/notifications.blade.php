@extends('layout.default')
@section('page-meta')
<title>Notifications - Javul.org</title>
@endsection
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
                                <h4>Notifications</h4>
                            </div>
                            <div class="panel-body list-group">
                                @if(count($notifications) > 0)
                                    <?php
                                    $timezone = 'UTC';
                                    if(!empty(\Auth::check() && \Auth::user()->timezone))
                                        $timezone = Auth::user()->timezone;
                                    ?>
                                    @foreach($notifications as $index=>$notification)
                                        @for($v=0;$v<10;$v++)
                                        <div class="list-group-item" style="padding: 0px;padding-bottom:4px">
                                            <div class="row" style="padding: 7px 15px">
                                                <div class="col-xs-12" style="display: table">
                                                    <div style="display:table-row">
                                                        <div class="div-table-first-cell">
                                                            <span class="tooltipster" title='{!! $notification->created_at->timezone($timezone)->format('Y-m-d H:i:s') !!}'>{!! \App\Library\Helpers::timetostr($notification->created_at->timezone($timezone)->format('Y-m-d H:i:s')) !!}</span>
                                                        </div>
                                                        <div class="div-table-second-cell">
                                                            <div class="circle activity-refresh">
                                                                <i class="fa fa-refresh"></i>
                                                            </div>
                                                        </div>
                                                        <div class="div-table-third-cell">
                                                            {!! $notification->content !!}

                                                        </div>
                                                        <div class="border-main child_{{$index}}">
                                                            <div></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="list-item-main child_{{$index}}"></div>
                                        </div>
                                        @endfor
                                    @endforeach

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
