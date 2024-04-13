@if(count($issues) > 0 )
    @foreach($issues as $issue)

        <tr>
            <td>{!! \App\Library\Helpers::timetostr($issue->created_at) !!}</td>
            <td>
                {{$issue->title}}
            </td>
            <td>
                <a href="{!! url('units/'.$unitIDHashID->encode($issue->unit_id).'/'.\App\Unit::getSlug($issue->slug)) !!}">
                    {{\App\Unit::getUnitName($issue->unit_id)}}
                </a>
            </td>
            <td>
                {{$issue->status}}
            </td>
        </tr>
    @endforeach
    <tr style="background-color: #fff;text-align: right;">
        <td colspan="4">
            <a href="{!! url('units/add')!!}"class="btn black-btn" id="add_unit_btn"
               type="button">
                <i class="fa fa-plus plus"></i> <span class="plus_text">ADD ISSUES</span>
            </a>
            @if($issues->lastPage() > 1 && $issues->lastPage() != $issues->currentPage())
                <a href="#" data-url="{{$issues->url($issues->currentPage()+1) }}" class="btn more-black-btn
                                        more-issues" id="add_unit_btn"
                   type="button">
                    MORE ISSUES <span class="more_dots">...</span>
                </a>
            @endif
        </td>
    </tr>
@endif