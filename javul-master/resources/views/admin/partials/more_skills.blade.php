@if(count($jobSkillObj) > 0 )
    @foreach($jobSkillObj as $skill)
        <tr>
            <td>
                <a href="{!! url('job_skills/'.$jobSkillIDHashID->encode($skill->id)) !!}">
                    {{$skill->skill_name}}
                </a>
            </td>
            <td>
                @if(!empty($skill->parent_id))
                    <a href="{!! url('job_skills/'.$jobSkillIDHashID->encode($skill->parent_id )) !!}">
                        {{\App\JobSkill::getName($skill->parent_id)}}
                    </a>
                @else
                    -
                @endif
            </td>
        </tr>
    @endforeach
    <tr style="background-color: #fff !important;text-align: right">
        <td colspan="2">
            <a href="{!! url('job_skills/add')!!}"class="btn black-btn" id="add_job_skill_btn" type="button">
                <i class="fa fa-plus plus"></i> <span class="plus_text">ADD SKILL</span>
            </a>

            @if($jobSkillObj->lastPage() > 1 && $jobSkillObj->lastPage() != $jobSkillObj->currentPage())
                <a href="#" data-url="{{$jobSkillObj->url($jobSkillObj->currentPage()+1) }}"
                   class="btn more-black-btn more-skills" type="button">
                    MORE SKILLS <span class="more_dots">...</span>
                </a>
            @endif
        </td>
    </tr>
@endif