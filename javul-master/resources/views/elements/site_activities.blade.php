@if(!$ajax)
    <div class="panel panel-grey panel-default">
        <div class="panel-heading loading_content_hide">
            <h4>{{$site_activity_text}}</h4>
        </div>
        <div class="panel-body list-group loading_content_hide">
            @if(count($site_activity) > 0)
                <?php
                    $timezone = 'UTC';
                    if(!empty(\Auth::user()->timezone))
                        $timezone = Auth::user()->timezone;
                ?>
                @foreach($site_activity as $index=>$activity)
                    <?php
                    /*if(!empty(\Auth::user()->timezone)){
                        $tz = new DateTimeZone(\Auth::user()->timezone);
                        $date = new DateTime($activity->created_at);
                        $date->setTimezone($tz);
                        $activity->created_at = $date->format('Y-m-d H:i:s');
                    }else
                        $activity->created_at = date('Y-m-d H:i:s',strtotime($activity->created_at));*/
                    ?>
                    <div class="list-group-item" style="padding: 0px;padding-bottom:4px">
                        <div class="row" style="padding: 7px 15px">
                            <div class="col-xs-12" style="display: table">
                                <div style="display:table-row">
                                    <div class="div-table-first-cell" data-id="{{$activity->id}}">
                                        <span class="tooltipster" title='{!! $activity->created_at->timezone($timezone)->format('Y-m-d H:i:s') !!}'>{!! \App\Library\Helpers::timetostr($activity->created_at->timezone($timezone)->format('Y-m-d H:i:s')) !!}</span>
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
                                        @if($index == count($site_activity) - 1)
                                            <div class="hide-last-border"></div>
                                            <div class="last-site-activity"></div>
                                        @elseif($index == 0)
                                            <div></div>
                                            <div class="first-site-activity"></div>
                                        @else
                                            <div></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="list-item-main child_{{$index}}"></div>
                    </div>
                @endforeach
                @if($site_activity->lastPage() > 1 && $site_activity->lastPage() != $site_activity->currentPage())
                    <div class="list-group-item text-right more-btn">
                        <a href="#"class="btn black-btn @if($site_activity_text == 'Global Activity Log') more_site_activity_btn
                    @else more_unit_site_activity_btn @endif"
                           data-url="{{$site_activity->url($site_activity->currentPage()+1) }}" @if($site_activity_text == 'Unit Activity Log')
                           data-unit_id="{{$unitIDHashID->encode($unit_activity_id)}}" @endif
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
@else
    <?php $i=(\Config::get('app.site_activity_page_limit')*$site_activity->currentPage() - \Config::get('app.site_activity_page_limit')) + 1;?>
    @if(count($site_activity) > 0)
        @foreach($site_activity as $index=>$activity)
            <div class="list-group-item" style="padding: 0px;padding-bottom:4px">
                <div class="row" style="padding: 7px 15px">
                    <div class="col-xs-12" style="display: table">
                        <div style="display:table-row">
                            <div class="div-table-first-cell">
                                <span class="tooltipster" title='{!! ($activity->created_at) !!}'>{!! \App\Library\Helpers::timetostr($activity->created_at) !!}</span>
                            </div>
                            <div class="div-table-second-cell">
                                <div class="circle activity-refresh">
                                    <i class="fa fa-refresh"></i>
                                </div>
                            </div>
                            <div class="div-table-third-cell">
                                {!! $activity->comment !!}

                            </div>
                            <div class="border-main child_{{$i}}">
                                <div></div>
                                @if($index == count($site_activity) - 1)
                                    <div class="last-site-activity"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="list-item-main child_{{$i}}"></div>
            </div>
            <?php $i++; ?>
        @endforeach
        @if($site_activity->lastPage() > 1 && $site_activity->lastPage() != $site_activity->currentPage())
            <div class="list-group-item text-right more-btn">
                <a href="#"class="btn black-btn @if($site_activity_text == 'Global Activity Log') more_site_activity_btn
                    @else more_unit_site_activity_btn @endif"
                   data-url="{{$site_activity->url($site_activity->currentPage()+1) }}" @if($site_activity_text == 'Unit Activity Log')
                   data-unit_id="{{$unitIDHashID->encode($unit_activity_id)}}" @else data-from_page="global" @endif
                   type="button">MORE ACTIVITY <span class="more_dots">...</span>
                </a>
            </div>
        @endif
    @else
        <div class="list-group-item">
            No activity found.
        </div>
    @endif
@endif
