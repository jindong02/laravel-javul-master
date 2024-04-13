@extends('layout.default')
@section('page-meta')
<title>My Contributions - Javul.org</title>
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
        .panel-body.list-group>.list-group-item>div.row>.col-xs-12>div:last-child>.border-main>.last-site-activity{ display:none !important; }
        .panel-body.list-group>.list-group-item>div.row>.col-xs-12>div:last-child>.border-main>div{ height: 65px; }
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
                                <h4>ACTIVITY LOG</h4>
                            </div>
                            <div class="panel-body list-group">
                                @if(count($site_activities) > 0)
                                    @foreach($site_activities as $index=>$activity)
                                        <div class="list-group-item" style="padding: 0px;padding-bottom:4px">
                                            <div class="row" style="padding: 7px 15px">
                                                <div class="col-xs-12" style="display: table">
                                                    <div style="display:table-row">
                                                        <div class="div-table-first-cell">
                                                            {!! \App\Library\Helpers::timetostr($activity->created_at) !!}
                                                        </div>
                                                        <div class="div-table-second-cell">
                                                            <div class="circle activity-refresh">
                                                                <i class="fa fa-refresh"></i>
                                                            </div>
                                                        </div>
                                                        <div class="div-table-third-cell">
                                                            {!! $activity->comment !!}

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
                                    @if($site_activities->lastPage() > 1 && $site_activities->lastPage() != $site_activities->currentPage())
                                        <div class="list-group-item text-right more-btn">
                                            <a href="#"class="btn black-btn more_site_activity_btn" data-from_page='user'
                                               data-url="{{$site_activities->url($site_activities->currentPage()+1) }}"
                                               type="button">MORE ACTIVITY <span class="more_dots">...</span>
                                            </a>
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
