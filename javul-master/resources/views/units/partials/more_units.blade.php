@if(count($units) > 0 )
    @foreach($units as $unit)
        <?php $category_ids = $unit->category_id;
        $category_names = \App\Models\UnitCategory::getName($category_ids);
        $category_ids = explode(",",$category_ids);
        $category_names  = explode(",",$category_names );
        ?>
        <tr>
{{--            <td><a href="{!! url('units/'.$unitIDHashID->encode($unit->id).'/'.$unit->slug) !!}">{{$unit->name}}</a></td>--}}
            <td>
                @if(count($category_ids) > 0 )
                    @foreach($category_ids as $index=>$category)
{{--                        <a href="{!! url('units/category/'.$unitCategoryIDHashID->encode($category)) !!}">{{$category_names[$index]}}</a>--}}
                        @if(count($category_ids) > 1 && $index != count($category_ids) -1)
                            <span>&#44;</span>
                        @endif
                    @endforeach
                @endif
            </td>
            <td><div class="text_wraps" data-toggle="tooltip" data-placement="top"  title="{!!trim
                                            ($unit->description)!!}"><span
                            class="ellipsis_text">{!!trim($unit->description)!!}</span></div></td>
        </tr>
    @endforeach
    <tr style="background-color: #fff;text-align: right">
        <td colspan="4">
            <a href="{!! url('units/add')!!}"class="btn black-btn" id="add_unit_btn" type="button">
                <i class="fa fa-plus plus"></i> <span class="plus_text">{!! trans('messages.add_unit')!!}</span>
            </a>
            @if($units->lastPage() > 1 && $units->lastPage() != $units->currentPage())
                <a href="#" data-url="{{$units->url($units->currentPage()+1) }}" class="btn
                                    more-black-btn more-units" type="button">
                    MORE UNITS <span class="more_dots">...</span>
                </a>
            @endif
        </td>
    </tr>
@endif
