var FormValidation = function () {

    // validation using icons
    var handleValidation = function() {

        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation

        var form2 = $('#form_sample_2');
        var error2 = $('.alert-danger', form2);
        var success2 = $('.alert-success', form2);

        form2.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",  // validate all fields including form hidden input
            rules: {
                title: {
                    required: true
                },
                unit_id: {
                    required: true
                },
                "description": {
                    required: true
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit
                success2.hide();
                error2.show();
                App.scrollTo(error2, -200);
            },

            errorPlacement: function (error, element) { // render error placement for each input type
                var field_name = $(element).attr('name');
                if(field_name == "description")
                    $(element).parent('.col-sm-12').find(".cke_editor_description").attr("style","border-color:#a94442 !important;");

                var icon = $(element).parent('.input-icon').children('i');
                icon.removeClass('fa-check').addClass("fa-warning");
                icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
            },

            highlight: function (element) { // hightlight error inputs
                var field_name = $(element).attr('name');
                if(field_name == "description")
                    $(element).closest('.col-sm-12').removeClass("has-success").addClass('has-error'); // set error class to the control group
                else 
                    $(element).closest('.col-sm-4').removeClass("has-success").addClass('has-error'); // set error class to the control group
                    
                if(field_name  == "unit_id")
                    $(element).parents('.input-icon').find(".select2").find(".select2-selection").css("border-color","#a94442");

            },

            unhighlight: function (element) { // revert the change done by hightlight

            },

            success: function (label, element) {
                var field_name = $(element).attr('name');
                var icon = $(element).parent('.input-icon').children('i');

                if(field_name == "description")
                    $(element).closest('.col-sm-12').removeClass('has-error').addClass('has-success'); // set success class to the control group
                else
                    $(element).closest('.col-sm-4').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");

                if(field_name == "description")
                    $(element).parent('.col-sm-12').find(".cke_editor_description").attr("style","border-color:#3c763d !important;");
                if(field_name == "unit_id")
                    $(element).parents('.input-icon').find(".select2").find(".select2-selection").css("border-color","#3c763d");

            },

            submitHandler: function (form) {
                success2.show();
                error2.hide();
                form.submit(); // submit the form

            }
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
        }
    };

}();

$(document).ready(function() {
    FormValidation.init();

    if($("#unit_id").length > 0){
        $("#unit_id").select2({
            allowClear:true,
            placeholder:"Select Unit"
        }).on('select2:select',function(event){
            var id = $(event.currentTarget).find("option:selected").val();
            if($.trim(id) != ""){
                $.ajax({
                    type:'post',
                    url:siteURL+'/tasks/get_objective',
                    data:{unit_id:id,_token:csrf_token},
                    dataType:'json',
                    success:function(resp){
                        if(resp.success){
                            var html='<option value="">Select</option>';
                            $.each(resp.objectives,function(index,val){
                                html+='<option value="'+index+'">'+val+'</option>'
                            });
                            $("#objective_id").html(html).select2({allowClear:true,placeholder:"Select Objective"});
                        }
                        else {
                            showToastMessage('OBJECTIVE_NOT_FOUND');
                        }
                    }
                });
            }
        });
    }
    $("#objective_id").select2({
        allowClear:true,
        placeholder:"Select Objective"
    });

    $("#task_id").select2({
        allowClear: true,
        placeholder: "Select Task"
    });

    $('.summernote,.summernote_resolution').ckeditor(function(textarea) {
        if(issue_status != "resolved") {
            CKEDITOR.instances['resolution'].setReadOnly(true);
        }
    });

    CKEDITOR.on('instanceReady', function(){
        $.each( CKEDITOR.instances, function(instance) {
            CKEDITOR.instances[instance].on("change", function(e) {
                for ( instance in CKEDITOR.instances )
                    CKEDITOR.instances[instance].updateElement();
            });
        });
    });

    $("[name='status']").on('change',function(){
        if($(this).val() == "resolved"){

            CKEDITOR.instances['resolution'].setReadOnly(false);
        }
        else
            CKEDITOR.instances['resolution'].setReadOnly(true);
    })
    /**/


    $(document).off('click','.addMoreDocument').on('click',".addMoreDocument",function(){
        cloneTR();
        return false;
    });

    $("#objective_id").on('change',function(){
        var obj_val = $(this).val();
        var token = $('[name="_token"]').val();
        if($.trim(obj_val) == "")
        {
            $("#objective_id").html('<option value="">Select</option>');
            return false;
        }
        else
        {
            $(".task_loader.location_loader").show();
            $("#objective_id").prop('disabled',true);
            $.ajax({
                type:'POST',
                url:siteURL+'/tasks/get_tasks',
                dataType:'json',
                data:{obj_id:obj_val,_token:token },
                success:function(resp){
                    $(".task_loader.location_loader").hide();
                    $("#objective_id").prop('disabled',false);
                    if(resp.success){
                        var html='<option value="">Select</option>';
                        $.each(resp.tasks,function(index,val){
                            html+='<option value="'+index+'">'+val+'</option>'
                        });
                        $("#task_id").html(html).select2({allowClear:true,placeholder:"Select Task"});
                    }
                }
            })
        }
        return false
    });

    $(document).on("click","table.documents tbody .remove-row", function(){
        var index_tr = $(".documents").find("tbody").find("tr").index($(this));
        var id = $(this).attr('data-id');
        var issue_id = $(this).attr('data-issue_id');
        var fromEdit = $(this).attr('data-from_edit');
        $that = $(this);
        if($.trim(id) != "" && $.trim(issue_id) != ""){
            $.ajax({
                type:'get',
                url:siteURL+'/issues/remove_issue_document',
                data:{id:id,issue_id:issue_id,fromEdit:fromEdit },
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        showToastMessage('DOCUMENT_DELETED');
                        if ($("table.documents tbody tr").length > 1)
                            $that.parents('tr:eq(0)').remove();

                        $(".documents").find("tbody").find("tr").eq(index_tr).find(".addMoreDocument").removeClass("hide");
                    }
                    else
                        showToastMessage('SOMETHING_GOES_WRONG');
                }
            })
        }
        else{

            if ($("table.documents tbody tr").length > 1)
                $(this).parents('tr:eq(0)').remove();

            var addedDocLength = $(".fileinput-new:not(:hidden)").length;
            if(addedDocLength == 0)
                $(".changed_items[value='"+field_name+"']").remove();

            $(".documents").find("tbody").find("tr").eq(index_tr).find(".addMoreDocument").removeClass("hide");
        }

        return false;
    });
});

function cloneTR(){
    var last = $("table.documents tbody tr:last").clone();
    last.find(".remove-row").attr('data-id','').removeClass('hide');
    $("table.documents tbody tr:last").find(".addMoreDocument").addClass("hide");
    $("table.documents tbody tr:last").after("<tr>" + last.html() + "</tr>");
    console.log($("table.documents tbody tr:last").html());
    $("table.documents tbody tr:last").find("[name='documents[]']").find("a.input-group-addon").trigger('click');
    $("table.documents tbody tr:last").find("[name='documents[]']").fileinput();
    // reset all values
    $("table.documents tbody tr:last :input:not(:checked)").val("").removeAttr('selected');
    return false;
}



