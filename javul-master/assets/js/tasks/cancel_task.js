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
                App.scrollTo(error2, -200);
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
                bootbox.dialog({
                    message: "Are you sure? you want to cancel the task.",
                    title: "Task Cancellation",
                    buttons: {
                        success: {
                            label: "Ok",
                            className: "btn-success",
                            callback: function() {
                                form.submit();
                            }
                        },
                        danger: {
                            label: 'Cancel',
                            className: "btn-danger",
                            callback: function() {

                            }
                        }
                    }
                });
                  // submit the form

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

    });

/*
    $("#cancel_task_final").on('click',function(){
        var tid = $(this).attr('data-tid');
        if($.trim(tid) != ""){
            $.ajax({
                type:'get',
                url:siteURL+'/tasks/mark_task_cancelled',
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
            })
        }
    })*/


});



