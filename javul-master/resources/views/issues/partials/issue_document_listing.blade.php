<tr>
    <td>
        <span>
            <a href="{!! url($document->file_path) !!}" target="_blank">
                {{$document->file_name}}
            </a>
        </span>
    </td>
    <td>
        @if($issueObj->status != "resolved")
        <a href="#" class="remove-row text-danger" data-issue_id="{{$issueIDHashID->encode($issueObj->id)}}"
            @if($fromEdit == "yes") data-from_edit="yes" data-id='{{$document->id}}' @else data-id="{{$issueDocumentIDHashID->encode($document->id)}}"
        @endif>
            <i class="fa fa-remove"></i>
        </a>
        <?php $addMoreUnitClass = ""; ?>
        @if(count($issueDocumentsObj) > 1)
        <?php $addMoreUnitClass = "hide";?>
        @endif
        @if(count($issueDocumentsObj) == $i)
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