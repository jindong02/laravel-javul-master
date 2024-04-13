var FormValidation = function () {

    // validation using icons
    var handleValidation = function() {

        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation

        var form2 = $('#form_sample_2');
        var error2 = $('.alert-danger', form2);
        var success2 = $('.alert-success', form2);

        jQuery.validator.addMethod("checkGreaterOrNot", function(value, element) {
            var start_datetime = $("#estimated_completion_time_start").val();
            var end_datetime = $("#estimated_completion_time_end").val();
            if($.trim(start_datetime) != "" &&  $.trim(end_datetime) != ""){
                var start = start_datetime.split(" ");
                var start_date = start[0].split("/");
                var start_time = start[1];

                var end = end_datetime.split(" ");
                var end_date = end[0].split("/");
                var end_time = end[1];

                return moment(start_date[0]+'-'+start_date[1]+'-'+start_date[2]+' '+start_time).isBefore(end_date[0]+'-'+end_date[1]+'-'+end_date[2]+' '+end_time);
            }
            else{
                jQuery.extend(jQuery.validator.messages, {
                    checkGreaterOrNot:'Please enter start datetime and end datetime.'
                });
                return false;
            }
        }, "end datetime must be greater than start datetime.");

        jQuery.validator.addMethod("checkTaskDescription", function(value, element) {
            // var description = $.trim($("#description").summernote().val());
            if($("#description").summernote('isEmpty')){
                $("#desc-error").html('<span class="help-block"><strong>Description cannot be an empty.</strong></span>');
                $("#description").parent('.col-sm-12').addClass('has-error');
                $("#cke_description").hide();
                return false;
            }else{
                $("#desc-error").html('');
                $("#description").parent('.col-sm-12').removeClass('has-error');
                return true;
            }
        }, "Description cannot be an empty.");

        form2.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",  // validate all fields including form hidden input
            rules: {
                unit: {
                    required: true
                },
                objective: {
                    required: true
                },
                task_name: {
                    required: true
                },
                'task_skills[]': {
                    required: true
                },
                estimated_completion_time_start: {
                    required: true
                },
                estimated_completion_time_end: {
                    required: true,
                    checkGreaterOrNot : true
                },
                description:{
                    checkTaskDescription: true
                },
                city: {
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
                if(field_name == "unit" || field_name == "objective" || field_name == "task_skills[]")
                    $(element).parents('.input-icon').find(".select2").find(".select2-selection").css("border-color","#a94442");

                if(field_name == "estimated_completion_time_end" || field_name == "estimated_completion_time_start")
                    var icon = $(element).parent('.input-group').children('i');
                else if(field_name == "unit" || field_name == "objective" || field_name == "task_skills[]"  )
                    var icon = $(element).parents('.input-icon').children('i');
                else
                    var icon = $(element).parent('.input-icon').children('i');
                icon.removeClass('fa-check').addClass("fa-warning");
                icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
            },

            highlight: function (element) { // hightlight error inputs
                $(element).closest('.col-sm-4').removeClass("has-success").addClass('has-error'); // set error class to the control group
                if($(element).attr('name') == "task_skills[]"){
                    $(element).closest('.col-sm-8').removeClass("has-success").addClass('has-error'); // set error class to the control group
                }
            },

            unhighlight: function (element) { // revert the change done by hightlight

            },

            success: function (label, element) {
                var field_name =$(element).attr('name');
                if(field_name == "estimated_completion_time_end" || field_name == "estimated_completion_time_start")
                    var icon = $(element).parent('.input-group').children('i');
                else if(field_name == "unit" || field_name == "objective" || field_name == "task_skills[]"  )
                    var icon = $(element).parents('.input-icon').children('i');
                else
                    var icon = $(element).parent('.input-icon').children('i');

                if(field_name == "unit" || field_name == "objective" || field_name == "task_skills[]")
                    $(element).parents('.input-icon').find(".select2").find(".select2-selection").css("border-color","#3c763d");

                $(element).closest('.col-sm-4').removeClass('has-error').addClass('has-success'); // set success class to the control group
                $(element).closest('.col-sm-8').removeClass('has-error').addClass('has-success');
                icon.removeClass("fa-warning").addClass("fa-check");
            },

            submitHandler: function (form) {
                success2.show();
                error2.hide();

                // for each task action insert into task_actions table.
                /*var code = $("#action_items").code();
                var text = code.replace(/<p>/gi, " ");
                var data= text.split("</li>");

                for(var i=0;i<data.length;i++){
                    if(i==0)
                        $('.action_items_class').eq(i).val(data[i]);
                    else{
                        $(".all_action_items").append('<input type="hidden" name="action_items_array[]" id="action_items_array" class="action_items_class" value="'+data[i]+'"/>')
                    }
                }*/

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
        if(editTask)
        {
            $('#datetimepicker1').datetimepicker({
                format: 'YYYY/MM/DD HH:mm'
            });
            $('#datetimepicker2').datetimepicker({
                format: 'YYYY/MM/DD HH:mm'
            });

            $("#datetimepicker1").on("dp.change", function(e) {
                addEditedFieldName('datetimepicker1');
            });

            $("#datetimepicker2").on("dp.change", function(e) {
                addEditedFieldName('datetimepicker2');
            });
        }
        else{
            $('#datetimepicker1').datetimepicker({
                format: 'YYYY/MM/DD HH:mm',
                minDate:moment()
            });
            $('#datetimepicker2').datetimepicker({
                format: 'YYYY/MM/DD HH:mm',
                minDate:moment()
            });
        }

        //check user can change task status or not
        if($("#task_status").length > 0 && $("#task_status").val() != "editable"){
            $("#unit").attr("disabled","disabled");
            $("#objective").attr("disabled","disabled");
            $("#task_name").attr("disabled","disabled");
            $("#task_skills").attr("disabled","disabled");
            $("#estimated_completion_time_start").attr("disabled","disabled");
            $("#estimated_completion_time_end").attr("disabled","disabled");
            $("#compensation").attr("disabled","disabled");
            $("#taskSummary").attr("disabled","disabled");
            $("#action_items").attr("disabled","disabled");
            $(".summernote").attr("disabled","disabled");
            $("#create_objective").removeAttr('disabled').addClass('black-btn').css('background-color','none');
        }

        $("#taskSummary").ckeditor();
        var taskSummary = $('#taskSummary');
        taskSummary.ckeditor({ 
        extraPlugins: 'charcount', 
        maxLength: 1000, 
        toolbar: 'TinyBare', 
        toolbar_TinyBare: [
             ['Bold','Italic','Underline'],
             ['Undo','Redo'],['Cut','Copy','Paste'],
             ['NumberedList','BulletedList','Table'],['CharCount']
        ] 
        }).ckeditor().editor.on('key', function(obj) {
            if (obj.data.keyCode === 8 || obj.data.keyCode === 46 || obj.data.keyCode === 37 || obj.data.keyCode === 38 || obj.data.keyCode === 39 || obj.data.keyCode === 40 || obj.data.keyCode === 27 || obj.data.keyCode === 16 ) {
                return true;
            }
            if (taskSummary.ckeditor().editor.document.getBody().getText().length >= 1000) {
                showToastMessage('TASK_SUMMARY_CAN_ACCEPT_MAXIMUM_1000_CHARACTER');
                return false;
            }else { return true; }
        });

        //disabled submit button
        $("#form_sample_2").submit(function (e) {
            //disable the submit button
            if($("#form_sample_2").valid()){
                $("#create_objective").attr("disabled", true);
                return true;
            }
        });


        if(typeof editTask !== typeof undefined && editTask) {

            $("#action_items").ckeditor();
            $('.summernote').ckeditor();

            CKEDITOR.on('instanceReady', function(){
                $.each( CKEDITOR.instances, function(instance) {
                    CKEDITOR.instances[instance].on("change", function(e) {
                        for ( instance in CKEDITOR.instances )
                            CKEDITOR.instances[instance].updateElement();
                    });
                });
            });
        }else{

            $("#action_items").ckeditor();
            $('.summernote').ckeditor();

            CKEDITOR.on('instanceReady', function(){
                $.each( CKEDITOR.instances, function(instance) {
                    CKEDITOR.instances[instance].on("change", function(e) {
                        for ( instance in CKEDITOR.instances )
                            CKEDITOR.instances[instance].updateElement();
                    });
                });
            });
        }

        $("#unit").select2({
            allowClear:true,
            placeholder:"Select Unit"
        });

        $("#objective").select2({
            allowClear: true,
            placeholder: "Select Objective"
        });

        if(from_unit) {
            $("#objective").select2('enable',false);
        }

        function formatSkills (repo) {
            if (repo.loading) return repo.text;

            var markup = "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__title'>" + repo.name + "</div></div></div></div>";

            /*if (repo.description) {
                markup += "<div class='select2-result-repository__description'>" + repo.description + "</div>";
            }

            markup += "<div class='select2-result-repository__statistics'>" +
                "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> " + repo.forks_count + " Forks</div>" +
                "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> " + repo.stargazers_count + " Stars</div>" +
                "<div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> " + repo.watchers_count + " Watchers</div>" +
                "</div>" +
                "</div></div>";*/

            return markup;
        }

        function formatSkillsSelection (repo) {
            return repo.text;
        }


         var skillSelect2 = $("#task_skills").select2({
            allowClear: true,
            width: '100%',
            displayValue:'skill_name',
            ajax: {
                url: siteURL+"/job_skills/get_skills",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            //more: (params.page * 1) < data.total_counts
                            more:false
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatSkills, // omitted for brevity, see the source of this page
            templateSelection: formatSkillsSelection // omitted for brevity, see the source of this page
        });

        skillSelect2.on("select2:unselect",function(e){
            var id = e.params.data.id;
            var index = selected_skill_id.indexOf(id);
            if (index > -1) {
                selected_skill_id.splice(index, 1);
            }
            return false;
        });

        $("#unit").on('change',function(){
            var unit_val = $(this).val();
            var token = $('[name="_token"]').val();
            if($.trim(unit_val) == "")
            {
                $("#objective").html('<option value="">Select</option>');
                return false;
            }
            else
            {
                $(".objective_loader.location_loader").show();
                $("#objective").prop('disabled',true);
                $.ajax({
                    type:'POST',
                    url:siteURL+'/tasks/get_objective',
                    dataType:'json',
                    data:{unit_id:unit_val,_token:token },
                    success:function(resp){
                        $(".objective_loader.location_loader").hide();
                        $("#objective").prop('disabled',false);
                        if(resp.success){
                            var html='<option value="">Select</option>';
                            $.each(resp.objectives,function(index,val){
                                html+='<option value="'+index+'">'+val+'</option>'
                            });
                            $("#objective").html(html).select2({allowClear:true,placeholder:"Select Objective"});
                        }
                    }
                })
            }
            return false
        });

        $("#input-id").fileinput({'showUpload':false, 'previewFileType':'any'});

        $(".editFileInput").fileinput({'showUpload':false, 'previewFileType':'any'});

    });

    $(document).off('click','.addMoreDocument').on('click',".addMoreDocument",function(){
        cloneTR();
        return false;
    });

    $(document).on("click","table.documents tbody .remove-row", function(){
        var index_tr = $(".documents").find("tbody").find("tr").index($(this));
        var id = $(this).attr('data-id');
        var task_id = $(this).attr('data-task_id');
        var fromEdit = $(this).attr('data-from_edit');
        $that = $(this);
        if($.trim(id) != "" && $.trim(task_id) != ""){
            addEditedFieldName("remove_doc");

            $.ajax({
                type:'get',
                url:siteURL+'/tasks/remove_task_document',
                data:{id:id,task_id:task_id,fromEdit:fromEdit },
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        showToastMessage('DOCUMENT_DELETED');
                        if ($("table.documents tbody tr").length > 1)
                            $that.parents('tr:eq(0)').remove();
                        if ($("table.documents tbody tr").length < 10)
                            // cloneTR(true);

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



    // when user click on submit for approval.
    $(".submit_for_approval").click(function(){
       var tid = $(this).attr('data-task_id');
        if($.trim(tid) != ""){
            $.ajax({
                type:'get',
                url:siteURL+'/tasks/submit_for_approval',
                data:{task_id:tid},
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        showToastMessage('REQUEST_SUBMITTED');
                        if($.trim(resp.status) != "" && resp.status == "awaiting_approval")
                            window.location.reload(true);
                        //more than 1 editors edit the task
						if(resp.status == "")
							window.location.reload(true);
                    }
                    else
                        showToastMessage('SOMETHING_GOES_WRONG');
                }
            })
        }
        return false;
    });

    // if edit task then only bind keyup event to all field to get field name which are getting change
    if(editTask){
        $("select").on('change',function(){
            var field_name = $(this).attr('id');
            addEditedFieldName(field_name);
        });

        $("input[type='text']").on("input",function(){
            var field_name = $(this).attr('name');
            addEditedFieldName(field_name);
        });

        $(".summernote,#action_items").on("summernote.change", function (e) {   // callback as jquery custom event
            var field_name = $(this).attr('name');
            addEditedFieldName(field_name);
        });

        $("input[name='documents[]'").on('change',function(){
            addEditedFieldName('add_document');
        });
    }

});
function cloneTR(addnew = false){
    var last = $("table.documents tbody tr:last").clone();
    var UploadDocumentTR = '<td style="width:90%;">'+
        '<div class="fileinput fileinput-new input-group" data-provides="fileinput"><div class="form-control" data-trigger="fileinput">'+
        '<i class="glyphicon glyphicon-file fileinput-exists"></i>'+
        '<span class="fileinput-filename"></span></div><span class="input-group-addon btn btn-default btn-file" style="line-height: 1;border-radius:0;">'+
        '<span class="fileinput-new">Select file</span><span class="fileinput-exists">Change</span><input type="file" name="documents[]">'+
        '</span><a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput" style="line-height: 1;border-radius:0;">Remove</a>'+
        '</div></td>'+
        '<td><span><a href="#" class="remove-row text-danger hide" ><i class="fa fa-remove"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;'+
        '<a href="#" class="addMoreDocument"><i class="fa fa-plus plus"></i></a></span></td>';
    //after document delete
    if(addnew){
        $("table.documents tbody tr:last").after("<tr>" + UploadDocumentTR + "</tr>");
        $("table.documents tbody tr:last").find(".fileinput").find("a.input-group-addon").trigger('click');
        $("table.documents tbody tr:last").find('.fileinput').fileinput();
        // reset all values
        $("table.documents tbody tr:last :input:not(:checked)").val("").removeAttr('selected');
        if($("table.documents tbody tr").length == 10)
            $("table.documents tbody tr:last").find(".addMoreDocument").addClass("hide");
        return false;
    }else if($("table.documents tbody tr").length < 10){
        last.find(".remove-row").attr('data-id','').removeClass('hide');
        $("table.documents tbody tr:last").find(".addMoreDocument").addClass("hide");
        $("table.documents tbody tr:last").after("<tr>" + last.html() + "</tr>");
        $("table.documents tbody tr:last").find(".fileinput").find("a.input-group-addon").trigger('click');
        $("table.documents tbody tr:last").find('.fileinput').fileinput();
        // reset all values
        $("table.documents tbody tr:last :input:not(:checked)").val("").removeAttr('selected');
        if($("table.documents tbody tr").length == 10)
            $("table.documents tbody tr:last").find(".addMoreDocument").addClass("hide");
        return false;
    }
}

function addEditedFieldName(field_name){
    var cnt = $(".changed_items").length;
    if($(".changed_items[value='"+field_name+"']").length)
        $(".changed_items[value='"+field_name+"']").val(field_name);
    else
        $('<input type="hidden" class="changed_items" name="changed_items[]" id="'+(cnt+1)+'" value="'+field_name+'"/>').appendTo("#form_sample_2");
}



