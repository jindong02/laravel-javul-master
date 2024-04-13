var FormValidation = function () {

    // validation using icons
    var handleValidation = function() {

        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation

        var form2 = $('#form_sample_2');
        var error2 = $('.alert-danger', form2);
        var success2 = $('.alert-success', form2);

        $.validator.addMethod('notEmpty', function (value, element, param) {
            //Your Validation Here
            if(tp)
                return true;
            var code = $('#comment').code(),
                filteredContent = $(code).text().replace(/\s+/g, '');

            if(filteredContent.length == 0) {
                $("#comment").parents('.col-sm-12').removeClass("has-success").addClass('has-error');
                $("#comment").parents('.col-sm-12').find(".note-editor").css('border','1px solid #a94442')
                return false;
            }
            else{
                $("#comment").parents('.col-sm-12').removeClass("has-error").addClass('has-success');
                $("#comment").parents('.col-sm-12').find(".note-editor").css('border','1px solid #3c763d')
                return true;
            }

        }, 'This field is required.');



        $.validator.addMethod('calculatePercentage', function (value, element, param) {
            //Your Validation Here
            if(!tp)
                return true;
            var val=0;
            var flag = true;
            $(".amount_percentage").each(function(){
               if($.trim($(this).val()) == ""){
                   $(this).parents('.row').removeClass("has-success").addClass('has-error');
                   flag=false;
               }else{
                   $(this).parents('.row').removeClass("has-error").addClass('has-success');
                   val+=parseInt($(this).val());
               }
            });
            if(val < 100 || val > 100){
                $(".amount_percentage").each(function(){
                    $(this).parents('.row').removeClass("has-success").addClass('has-error');
                });
                if($(".error-not-100").length == 0){
                    $(".reward-assignment-body").append('<span class="has-error error-not-100"><span class="control-label">Please split 100% among all users.</span></span>')
                }
                flag= false;
            }
            else{
                if($(".error-not-100").length > 0)
                    $(".reward-assignment-body").find('.error-not-100').remove();
            }
            return flag;

        }, 'This field is required.');

        $.validator.addClassRules('amount_percentage', {
            calculatePercentage: true/*,
             other rules */
        });


        form2.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",  // validate all fields including form hidden input
            rules: {
                comment: {
                    notEmpty:true
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit
                success2.hide();
                error2.show();
                //App.scrollTo(error2, -200);
            },

            errorPlacement: function (error, element) { // render error placement for each input type
                var field_name = $(element).attr('name');
                if(field_name == "unit" || field_name == "objective" || field_name == "task_skills")
                    $(element).parents('.input-icon').find(".select2").find(".select2-selection").css("border-color","#a94442");

                if(field_name == "estimated_completion_time_end" || field_name == "estimated_completion_time_start")
                    var icon = $(element).parent('.input-group').children('i');
                else if(field_name == "unit" || field_name == "objective" || field_name == "task_skills"  )
                    var icon = $(element).parents('.input-icon').children('i');
                else
                    var icon = $(element).parent('.input-icon').children('i');
                icon.removeClass('fa-check').addClass("fa-warning");
                icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.col-sm-4').removeClass("has-success").addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by hightlight

            },

            success: function (label, element) {
                var field_name =$(element).attr('name');
                if(field_name == "estimated_completion_time_end" || field_name == "estimated_completion_time_start")
                    var icon = $(element).parent('.input-group').children('i');
                else if(field_name == "unit" || field_name == "objective" || field_name == "task_skills"  )
                    var icon = $(element).parents('.input-icon').children('i');
                else
                    var icon = $(element).parent('.input-icon').children('i');

                if(field_name == "unit" || field_name == "objective" || field_name == "task_skills")
                    $(element).parents('.input-icon').find(".select2").find(".select2-selection").css("border-color","#3c763d");

                $(element).closest('.col-sm-4').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");
            },

            submitHandler: function (form) {
                success2.show();
                error2.hide();
                form.submit();  // submit the form

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
    $(function(){
        $("#comment").ckeditor();

        CKEDITOR.on('instanceReady', function(){
            $.each( CKEDITOR.instances, function(instance) {
                CKEDITOR.instances[instance].on("change", function(e) {
                    for ( instance in CKEDITOR.instances )
                        CKEDITOR.instances[instance].updateElement();
                });
            });
        });

        $("#input-id").fileinput({'showUpload':false, 'previewFileType':'any'});

        $(".editFileInput").fileinput({'showUpload':false, 'previewFileType':'any'});

    });

    $(document).off('click','.addMoreDocument').on('click',".addMoreDocument",function(){
        cloneTR();
        return false;
    });

    $(document).on("click","table.complete_task_attachment tbody .remove-row", function(){
        var index_tr = $(".complete_task_attachment").find("tbody").find("tr").index($(this));
        var id = $(this).attr('data-id');
        var task_id = $(this).attr('data-task_id');
        var fromEdit = $(this).attr('data-from_edit');
        $that = $(this);
        if($.trim(id) != "" && $.trim(task_id) != ""){
            $.ajax({
                type:'get',
                url:siteURL+'/tasks/remove_task_document',
                data:{id:id,task_id:task_id,fromEdit:fromEdit },
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        showToastMessage('DOCUMENT_DELETED');
                        if ($("table.complete_task_attachment tbody tr").length > 1)
                            $that.parents('tr:eq(0)').remove();

                        $(".complete_task_attachment").find("tbody").find("tr").eq(index_tr).find(".addMoreDocument").removeClass("hide");
                    }
                    else
                        showToastMessage('SOMETHING_GOES_WRONG');
                }
            })
        }
        else{

            if ($("table.complete_task_attachment tbody tr").length > 1)
                $(this).parents('tr:eq(0)').remove();

            var addedDocLength = $(".fileinput-new:not(:hidden)").length;
            if(addedDocLength == 0)
                $(".changed_items[value='"+field_name+"']").remove();

            $(".complete_task_attachment").find("tbody").find("tr").eq(index_tr).find(".addMoreDocument").removeClass("hide");
        }

        return false;
    });


    $("#reassign_task_btn").on('click',function(){
        $(".reward-panel").hide();
        $(".comment_block").show();
        $(".complete_assign_btn").hide();
        return false;
    });

    $("#ok_reassign").on('click',function(){
        var code = CKEDITOR.instances['comment'].getData(),
            filteredContent = $(code).text().replace(/\s+/g, '');

        if(filteredContent.length == 0) {
            $("#comment").parents('.col-sm-12').removeClass("has-success").addClass('has-error');
            $("#comment").parents('.col-sm-12').find(".note-editor").css('border','1px solid #a94442')
            return false;
        }
        else{
            var task_id = $(this).attr('data-tid');
            $("#form_sample_2").attr('action',siteURL+'/tasks/re_assign/'+task_id);
            document.getElementById("form_sample_2").submit();
        }
    });

    $(".cancel_btn").on('click',function(){
        $(".comment_block").hide();
        $(".reward-panel").hide();
        $(".complete_assign_btn").show();
        return false;
    });

    $("#mark_as_complete").on('click',function(){
        $(".comment_block").hide();
        $(".reward-panel").show();
        $(".complete_assign_btn").hide();
        return false;
    });

    $("#ok_complete").on('click',function(){

        var tid = $(this).attr('data-tid');
        if($.trim(tid) != ""){
            var task_id = $(this).attr('data-tid');
            $("#form_sample_2").attr('action',siteURL+'/tasks/mark_task_complete/'+task_id);
            $("#form_sample_2").submit();
            /*$.ajax({
                type:'get',
                url:siteURL+'/tasks/mark_task_complete',
                data:{tid:tid},
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        toastr['success']('Task completed successfully.', '');
                        window.location.reload(true);
                    }
                    else
                        toastr['error']('Something goes wrong. please try again later.', '');
                }
            })*/
        }
        return false;
    });
    /*$('.amount_percentage').on('keyup', function() {

        var val = this.value,
            $allInputs = $('.amount_percentage');
        if(val > 100) {
            $allInputs.val(100/$allInputs.length);
        }
        else {
            var length
            $('.amount_percentage').not(this).val( (100-val)/ ($allInputs.length-1)  ) ;
        }
    });*/


});
function cloneTR(){
    var last = $("table.complete_task_attachment tbody tr:last").clone();
    last.find(".remove-row").attr('data-id','').removeClass('hide');
    $("table.complete_task_attachment tbody tr:last").find(".addMoreDocument").addClass("hide");
    $("table.complete_task_attachment tbody tr:last").after("<tr>" + last.html() + "</tr>");
    $("table.complete_task_attachment tbody tr:last").find(".fileinput").find("a.input-group-addon").trigger('click');
    $("table.complete_task_attachment tbody tr:last").find('.fileinput').fileinput();
    // reset all values
    $("table.complete_task_attachment tbody tr:last :input:not(:checked)").val("").removeAttr('selected');
    return false;
}



