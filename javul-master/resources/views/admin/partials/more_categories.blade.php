@if(count($categoryObj) > 0 )
    @foreach($categoryObj as $category)
        <tr>
            <td>
                <a href="{!! url('category/'.$unitCategoryIDHashID->encode($category->id))!!}">
                    {{$category->name}}
                </a>
            </td>
            <td>
                @if($category->status == "pending")
                    <span class="text-danger">{{ucfirst($category->status)}}</span>
                @else
                    <span class="colorLightGreen">{{ucfirst($category->status)}}</span>
                @endif
            </td>
        </tr>
    @endforeach
    <tr style="background-color: #fff !important;text-align: right">
        <td colspan="2">
            <a href="{!! url('category/add')!!}"class="btn black-btn" id="add_job_skill_btn" type="button">
                <i class="fa fa-plus plus"></i> <span class="plus_text">ADD CATEGORY</span>
            </a>

            @if($categoryObj->lastPage() > 1 && $categoryObj->lastPage() != $categoryObj->currentPage())
                <a href="#" data-url="{{$categoryObj->url($jobSkillObj->currentPage()+1) }}"
                   class="btn more-black-btn more-category" type="button">
                    MORE CATEGORIES <span class="more_dots">...</span>
                </a>
            @endif
        </td>
    </tr>
@endif