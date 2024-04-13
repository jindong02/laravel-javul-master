@if(count($areaOfInterestObj) > 0 )
    @foreach($areaOfInterestObj as $area_of_interest)
        <tr>
            <td>
                <a href="{!! url('area_of_interest/'.$areaOfInterestIDHashID->encode($area_of_interest->id))!!}">
                    {{$area_of_interest->title}}
                </a>
            </td>
            <td>
                @if(!empty($area_of_interest->parent_id))
                    <a href="{!! url('area_of_interest/'.$areaOfInterestIDHashID->encode($area_of_interest->parent_id )) !!}">
                        {{\App\AreaOfInterest::getName($area_of_interest->parent_id)}}
                    </a>
                @else
                    -
                @endif
            </td>
        </tr>
    @endforeach
    <tr style="background-color: #fff !important;text-align: right">
        <td colspan="2">
            <a href="{!! url('area_of_interest/add')!!}"class="btn black-btn" id="add_job_skill_btn" type="button">
                <i class="fa fa-plus plus"></i> <span class="plus_text">ADD AREA OF INTEREST</span>
            </a>

            @if($areaOfInterestObj->lastPage() > 1 && $areaOfInterestObj->lastPage() != $areaOfInterestObj->currentPage())
                <a href="#" data-url="{{$areaOfInterestObj->url($areaOfInterestObj->currentPage()+1) }}"
                   class="btn more-black-btn more-area-of-interest" type="button">
                    MORE AREA OF INTEREST <span class="more_dots">...</span>
                </a>
            @endif
        </td>
    </tr>
@endif