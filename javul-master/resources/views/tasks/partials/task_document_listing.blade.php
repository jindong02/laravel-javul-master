<tr>
    <td>
        <span>
            <a href="{!! url($document->file_path) !!}" target="_blank">
                {{$document->file_name}}
            </a>
        </span>
    </td>
    <td>
        @if($taskObj->status == "editable")
        <a href="#" class="remove-row text-danger" data-task_id="{{$taskIDHashID->encode($taskObj->id)}}"
            @if($fromEdit == "yes") data-from_edit="yes" data-id='{{$document->id}}' @else data-id="{{$taskDocumentIDHashID->encode($document->id)}}"
        @endif>
            <i class="fa fa-remove"></i>
        </a>
        <?php $addMoreUnitClass = ""; ?>
        @if(count($taskDocumentsObj) > 1)
        <?php $addMoreUnitClass = "hide";?>
        @endif
        @if(count($taskDocumentsObj) == $i)
        <?php $addMoreUnitClass = "";?>
        @endif
        <span class="{{$addMoreUnitClass}}">
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="#" class="addMoreUnit">
                <i class="fa fa-plus plus"></i>
            </a>
        </span>
        @endif
    </td>
</tr>