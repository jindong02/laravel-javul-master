<div class="panel panel-grey panel-default">
    <div class="panel-heading">
        <h4>UNIT INFORMATION</h4>
    </div>
    <div class="panel-body unit-info-panel list-group">
        <div class="list-group-item">
            <div class="row">
                <div class="col-xs-12">
                    <label class="control-label upper">UNIT NAME</label>
                    <label class="control-label colorLightGreen form-control label-value">
                        <a href="{!! url('units/'.$unitIDHashID->encode($unitObj->id).'/'.$unitObj->slug) !!}" class="colorLightGreen" >{{$unitObj->name}}</a>
                    </label>
                </div>
            </div>
        </div>
        <div class="list-group-item">
            <div class="row">
                <div class="col-xs-4 unit-info-main-div">
                    <label class="control-label upper">UNIT LINKS</label>
                </div>
                <div class="col-xs-8" style="padding-top: 7px;">
                    <div class="row unit_info_row_1">
                        <div class="col-xs-12">
                            <ul class="unit_info_link_1" style="">
                                <li><a href="{!! url('objectives/'.$unitIDHashID->encode($unitObj->id).'/lists') !!}" class="colorLightBlue upper">OBJECTIVES</a></li>
                                <li class="mrgrtlt5">|</li>
                                <li><a href="{!! url('tasks/'.$unitIDHashID->encode($unitObj->id).'/lists') !!}" class="colorLightBlue upper">TASKS</a></li>
                                <li class="mrgrtlt5">|</li>
                                <li><a href="{!! url('issues/'.$unitIDHashID->encode($unitObj->id).'/lists') !!}" class="colorLightBlue upper">ISSUES</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <ul class="unit_info_link_2">
                                <i class="fa fa-quote-right colorLightBlue"></i>
                                <li><a href="{!! url('forum') !!}/{!! $unitIDHashID->encode($unitObj->id) !!}" class="colorLightBlue upper">FORUM </a></li>
                                <i class="fa fa-comments colorLightBlue"></i>
                                <li><a href="#" class="colorLightBlue start-unit-chat upper" data-unit_id="{{$unitIDHashID->encode($unit_activity_id)}}">CHAT (<span id="chat-online">0</span>) </a></li>
                                <i class="fa fa-wikipedia-w colorLightBlue"></i>
                                <li><a href="{!! url('wiki/home') !!}/{!! $unitIDHashID->encode($unitObj->id).'/'.$unitObj->slug !!}" class="colorLightBlue upper">WIKI</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-group-item">
            <div class="row">
                <div class="col-xs-4 borderRT paddingTB7">
                    <label class="control-label upper">OTHER LINKS</label>
                </div>
                <div class="col-xs-8 paddingTB7">
                    <label class="control-label colorLightBlue">{!! $unitObj->other_menulink !!}</label>
                    <a class="pull-right" href="{!! url('wiki/menu') !!}/{!! $unitIDHashID->encode($unitObj->id).'/'.$unitObj->slug !!}"> <i class="fa fa-pencil"></i> </a>
                </div>
            </div>
        </div>
        <div class="list-group-item">
            <div class="row">
                <div class="col-xs-4 borderRT paddingTB7 unit_category_left_height">
                    <label class="control-label upper">UNIT CATEGORIES</label>
                </div>
                <div class="col-xs-8 paddingTB7">
                    <?php $category_names = \App\Models\UnitCategory::getName($unitObj->category_id);
                    $category_ids = explode(",",$unitObj->category_id);
                    $category_names  = explode(",",$category_names ); ?>
                        @if(count($category_ids) > 0 )
                            @foreach($category_ids as $index=>$category)
                                <a class="upper colorLightBlue" href="{!! url('units/category='.strtolower($category_names[$index]))
                                !!}">{{$category_names[$index]}}</a>
                                @if(count($category_ids) > 1 && $index != count($category_ids) -1)
                                    <span>&#44;</span>
                                @endif
                            @endforeach
                        @endif
                </div>
            </div>
        </div>
        <div class="list-group-item">
            <div class="row">
                <div class="col-xs-4 borderRT paddingTB7">
                    <label class="control-label upper">UNIT LOCATION</label>
                </div>
                <div class="col-xs-8 paddingTB7">
                    <label class="control-label upper">
                        @if($unitObj->country_id != "247")
                            {{\App\Models\City::getName($unitObj->city_id)}}
                        @else
                            GLOBAL
                        @endif
                    </label>
                </div>
            </div>
        </div>
        <div class="list-group-item">
            <div class="row">
                <div class="col-xs-4 borderRT paddingTB7">
                    <label class="control-label upper">UNIT CREDIBILITY</label>
                </div>
                <div class="col-xs-8 paddingTB7">
                    <label class="control-label upper">{{$unitObj->credibility}}</label>
                </div>
            </div>
        </div>
        <div class="list-group-item">
            <div class="row">
                <div class="col-xs-4 borderRT paddingTB7">
                    <label class="control-label upper" style="width: 100%;">
                        <span class="fund_icon">FUND</span>
                        <span class="text-right pull-right">
                            <a href={!! url('funds/donate/unit/'.$unitIDHashID->encode($unitObj->id)) !!}><div class="fund_paid"><i class="fa fa-plus"></i></div></a></span>
                    </label>
                </div>
                <div class="col-xs-8 paddingTB7" style="padding-bottom: 2px;padding-top:5px">
                    <div class="row">
                        <div class="col-xs-6">Available</div>
                        <div class="col-xs-6 text-right">{{number_format($availableFunds,2)}} $</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">Awarded</div>
                        <div class="col-xs-6 text-right">{{number_format($awardedFunds,2)}} $</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-group-item">
            <div class="row">
                <div class="col-xs-4 borderRT paddingTB7">
                    <label class="control-label upper" style="width: 100%;">
                        <span class="fund_icon">PARENT UNIT</span>
                    </label>
                </div>
                <div class="col-xs-8 paddingTB7">
                    <a href="{!! url('units/'.$unitIDHashID->encode($unitObj->parent_id).'/'.\App\Models\Unit::getSlug($unitObj->parent_id)) !!}">
                        {{\App\Models\Unit::getUnitName($unitObj->parent_id)}}
                    </a>
                </div>
            </div>
        </div>
        <div class="list-group-item">
            <div class="row">
                <div class="col-xs-4 borderRT paddingTB7">
                    <label class="control-label upper" style="width: 100%;">
                        <span class="fund_icon">RELATED UNITs</span>
                    </label>
                </div>
                <div class="col-xs-8 paddingTB7">
                    <?php $related_unit =\App\Models\RelatedUnit::getRelatedUnitName($unitObj->id); $i=0; ?>
                    @if(count($related_unit) > 0)
                        @foreach(\App\Models\RelatedUnit::getRelatedUnitName($unitObj->id) as $index=>$val)
                            <a href="{!! url('units/'.$unitIDHashID->encode($index).'/'.\App\Models\Unit::getSlug($index)) !!}">
                                {{$val}}
                            </a>
                            @if($i == 0 && count($related_unit) > 1)
                                ,
                            @elseif($i !=0 && $i != count($related_unit))
                                ,
                            @endif
                            <?php $i++; ?>
                        @endforeach
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    window.onload = function(){

        function chatOnline(){
            $.ajax({
                type:'post',
                url:siteURL  + '/chat/online',
                data:{_token:'{{csrf_token()}}',unit_id:{!! $unitObj->id !!}},
                dataType:'json',
                complete: function(xhr, textStatus) {
                    setTimeout(function(){ chatOnline() }, 5000);
                },
                success:function(resp,text,xhr){
                    $("#chat-online").html(resp.online);
                }
            })
        }
        chatOnline();
    }
</script>
