{{--@if((empty($unitObjForLeftBar) || \App\Models\Task::isUnitAdminOfTask($taskObj->id)) && (!empty($taskDocumentsObj) && count($taskDocumentsObj) < 10))--}}
<tr>
    <td style="width:90%;" colspan="2">
        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
            <div class="form-control" data-trigger="fileinput">
                <i class="glyphicon glyphicon-file fileinput-exists"></i>
                <span class="fileinput-filename"></span>
            </div>
            <span class="input-group-addon btn btn-default btn-file" style="line-height: 1;border-radius:0;">
                <span class="fileinput-new">Select file</span>
                <span class="fileinput-exists">Change</span>
                <input type="file" name="documents[]">
            </span>
            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput" style="line-height: 1;border-radius:0;">Remove</a>
        </div>
        <input type="file" name="documents[]" class="file_input" multiple>
    </td>
   <td>
        <span>
            <a href="#" class="remove-row text-danger hide" >
                <i class="fa fa-remove"></i>
            </a>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="#" class="addMoreDocument">
                <i class="fa fa-plus plus"></i>
            </a>
        </span>
    </td>
</tr>
{{--@endif--}}
