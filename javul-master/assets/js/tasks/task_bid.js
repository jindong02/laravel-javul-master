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
                amount: {
                    required: true
                },
                "comment": {
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
                var icon = $(element).parent('.input-icon').children('i');
                $(element).closest('.col-sm-4').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");

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

$(function () {

    FormValidation.init();

    $('.toggle').bootstrapToggle({
        width: '100%'
    });

    $('.summernote').ckeditor();

    CKEDITOR.on('instanceReady', function(){
        $.each( CKEDITOR.instances, function(instance) {
            CKEDITOR.instances[instance].on("change", function(e) {
                for ( instance in CKEDITOR.instances )
                    CKEDITOR.instances[instance].updateElement();
            });
        });
    });


    $('a[data-toggle="collapse"]').on('click',function(){

        var objectID=$(this).attr('href');
        $(".collapse").collapse('hide');
        if($(objectID).hasClass('in'))
        {
            $(objectID).collapse('hide');
        }
        else{
            $(objectID).collapse('show');
        }
    });


    $('#expandAll').on('click',function(){

        $('a[data-toggle="collapse"]').each(function(){
            var objectID=$(this).attr('href');
            if($(objectID).hasClass('in')===false)
            {
                $(objectID).collapse('show');
            }
        });
    });

    $('#collapseAll').on('click',function(){

        $('a[data-toggle="collapse"]').each(function(){
            var objectID=$(this).attr('href');
            $(objectID).collapse('hide');
        });
    });

});