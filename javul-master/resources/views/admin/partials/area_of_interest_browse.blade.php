<div class="all_levels_area_of_interest @if($from == "account") account_listing_skills @endif">
    @if(count($firstBox_areaOfInterest) > 0)
        <div class="hierarchy_parent">
            <select name="title" id="area_of_interest_firstbox" class="first_level hierarchy" size="5" data-number="1">
                @foreach($firstBox_areaOfInterest as $area_of_interest_id=>$area_of_interest)
                    <option value="{{$area_of_interest_id}}" data-type="{{$area_of_interest['type']}}">{{$area_of_interest['name']}}&nbsp;></option>
                @endforeach
            </select>
            @if($from == "site_admin")
                <div style="margin-left:10px;margin-top:5px;">
                    <a class="btn black-btn btn-xs add_category" data-pos="first" id="add_category_btn" style="padding:5px 10px 5px;
                                            text-decoration:none;">
                        <i class="fa fa-plus plus"></i> <span class="plus_text" style="left:-5px;">ADD</span>
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
@if($from != "site_admin")
    <div class="row">
        <div class="col-xs-12">
            <div class="text-center"><h5>You have selected:<span class="selected_text_area">None</span></h5></div>
        </div>
    </div>
@endif