<div class="panel panel-grey panel-default">
    <div class="panel-heading">
        <h4>ISSUES</h4>
    </div>
    <div class="panel-body list-group loading_content_hide">
        <div class="loading_dots objective_loading" style="position: absolute;top:0;left:43%;z-index: 9999;display:none;">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <table class="table table-striped issue-table">
            <thead>
            <tr>
                <th>Issue Name</th>
                <th>Status</th>
                <th>Created By</th>
                <th><a href="#" style="text-decoration:none;color:#333" class="sort_by" data-order_by="older">
                        Created Date <span class="fa fa-sort-desc" style="vertical-align:top"></span>
                    </a>
                </th>
            </tr>
            </thead>
            <tbody>
            @if(count($issuesObj) > 0)
                @foreach($issuesObj as $obj)
                    <tr>
                        <td>
                            <a href="{!! url('issues/'.$issueIDHashID->encode($obj->id).'/view') !!}"
                               title="edit">
                                {{$obj->title}}
                            </a>
                        </td>
                        <td>
                            <?php $status_class=''; $verified_by =''; $resolved_by ='';
                            if($obj->status=="unverified")
                                $status_class="text-danger";
                            elseif($obj->status=="verified"){
                                $status_class="text-info";
                                $verified_by = " (by ".App\Models\User::getUserName($obj->verified_by).')';
                            }
                            elseif($obj->status == "resolved"){
                                $status_class = "text-success";
                                $resolved_by = " (by ".App\Models\User::getUserName($obj->resolved_by).')';
                            }
                            ?>
                            <span class="{{$status_class}}">{{ucfirst($obj->status).$verified_by. $resolved_by}}</span>
                        </td>
                        <td>
                            <a href="{!! url('userprofiles/'.$userIDHashID->encode($obj->user_id).'/'.strtolower(str_replace(" ","_",App\Models\User::getUserName($obj->user_id)))) !!}">
                                {{App\Models\User::getUserName($obj->user_id)}}
                            </a>
                        </td>
                        <td>{{$obj->created_at}}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5">No record(s) found.</td>
                </tr>
            @endif

            <tr style="background-color: #fff;text-align: right;">
                <td colspan="5">
                    <a class="btn black-btn" id="add_objective_btn" href="{!! url('issues/'.$unitIDHashID->encode($unit_activity_id).'/add') !!}">
                        <i class="fa fa-plus plus"></i> <span class="plus_text">Add Issue</span>
                    </a>

                    @if($issuesObj->lastPage() > 1 && $issuesObj->lastPage() != $issuesObj->currentPage())
                        <a href="#" data-url="{{$issuesObj->url($issuesObj->currentPage()+1) }}" data-unit_id="{{$unitIDHashID->encode($unit_activity_id)}}" class="btn
                                    more-black-btn more-objectives" data-from_page="unit_view" type="button">
                            MORE ISSUES <span class="more_dots">...</span>
                        </a>
                    @endif
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
