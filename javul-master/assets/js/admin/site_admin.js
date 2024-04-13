$(function(){
    $('.show_bid_details').on('click',function(){
        var id = $(this).attr('data-id');
        if($.trim(id) != ""){
            $.ajax({
                type:'get',
                url:siteURL+'/tasks/get_biding_details',
                data:{id:id},
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        bootbox.dialog({
                            message: resp.html
                        });
                    }
                }
            })
        }
        return false;
    });


    $(document).off("click",".add_skill").on("click",".add_skill",function(){
        var selected = $(this).parents('.hierarchy_parent').find(".new_box").find("a.selected").length;
        var total_divs ='';
        var pos = $(this).attr('data-pos');
        var parent_id='';
        var type = null;
        var box_number = '';
        if(typeof pos !== typeof undefined && pos !== false){

            if(pos == "first") {
                total_divs = 2;
                parent_id = 0;
                type = null;
                box_number = 1;
            }
            else {
                total_divs = $(".all_levels").find(".hierarchy_parent").length + 1;
                box_number = "last";
                if($(".all_levels").find(".hierarchy_parent").length == 1) {
                    parent_id = $(this).parents(".all_levels").find(".hierarchy_parent").eq(0).find('select').val();
                    type= $(this).parents(".all_levels").find(".hierarchy_parent").eq(0).find('select option:selected').attr('data-type');
                }
                else {
                    parent_id = $(this).parents(".all_levels").find(".hierarchy_parent:last").find('a.selected').attr('data-value');
                    type = $(this).parents(".all_levels").find(".hierarchy_parent:last").find('a.selected').attr('data-type');
                }
            }
        }else {
            total_divs = $(this).parents('.hierarchy_parent').find(".new_box").attr('data-number');
            var current_box_number = $(this).parents('.hierarchy_parent').find(".new_box").attr('data-number');
            box_number = current_box_number;
            var target_div = current_box_number-2;
            if(target_div  == 0) {
                parent_id = $(this).parents(".all_levels").find(".hierarchy_parent:first").find('select').val();
                type= $(this).parents(".all_levels").find(".hierarchy_parent:first").find('select option:selected').attr('data-type');
            }
            else {
                parent_id = $(this).parents(".all_levels").find(".hierarchy_parent").eq(target_div).find('a.selected').attr('data-value');
                type= $(this).parents(".all_levels").find(".hierarchy_parent").eq(target_div).find('a.selected').attr('data-type');
            }
        }

        if(total_divs > 0){
            var html = '';
            for(var i=0;i<total_divs - 1;i++){
                var text = '';
                if(i == 0) {
                    if(pos != "first")
                        html += "<span>" + $(".all_levels").find(".hierarchy_parent").eq(i).find('select').find(':selected').text() + "</span>";
                }
                else
                    html+="<span>&nbsp;"+$(".all_levels").find(".hierarchy_parent").eq(i).find('a.selected').html()+"</span>";
            }
        }
        category_text=  html;
        html+='<span><input name="skill_name" type="text" style="border:1px solid #ccc" placeholder="add new" ' +
            'class="new_skill_dynamic"></span>';
        var frm = '<form method="post" id="add_skill_form">'+
            '<div class="row">'+
            '<div class="col-sm-12"><div class="error_msg_skill"></div>'+
            '<label class="control-label">'+html+'</label>' +
            '<input type="hidden" name="parent_id" value="'+parent_id+'"/><input type="hidden" name="tbl_type" ' +
            'value="'+type+'"/><input type="hidden" name="path_text" value="'+category_text+'"/></div>'+
            '</div>'+
            '</form>';
        var that = $(this);
        var box = bootbox.dialog({
            title: "Add new skill",
            message: frm,
            buttons: {
                danger: {
                    label: "Cancel",
                    className: "btn-danger",
                    callback: function() {
                        bootbox.hideAll();
                    }
                },
                success: {
                    label: "Add Skill",
                    className: "btn-success",
                    callback: function(e) {
                        var frm =$("#add_skill_form");
                        $(".error_msg_skill").html('');
                        if($.trim(frm.find("[name='skill_name']").val()) != "") {

                            $.ajax({
                                type: 'get',
                                url: siteURL + '/job_skills/add',
                                data: $("#add_skill_form").serialize(),
                                dataType: 'json',
                                success: function (resp) {
                                    if (resp.success) {
                                        bootbox.hideAll();
                                        showToastMessage('JOB_SKILL_ADDED');

                                        if(box_number == "last"){
                                            var total_box = $(".all_levels").find(".hierarchy_parent").length;
                                            total_box++;

                                            $(".add_edit_skills").remove();
                                        }
                                        else{
                                            if(box_number == 1){
                                                $('#skill_firstbox').append($('<option>', {value:resp.skill_id, text:resp.skill_name+' >'}));
                                                $('#skill_firstbox option:last').attr('data-type','new');
                                                return false;

                                            }
                                            total_box = parseInt(box_number) + 1;
                                        }

                                        var html ='<div class="hierarchy_parent"><div class="hierarchy new_box" data-number="'+total_box+'">'+
                                            '<a href="" class="hierarchy select_skill" data-number="'+total_box+'" data-value="'+resp.skill_id+'" data-type="new">' +
                                            resp.skill_name+' &nbsp; &gt;</a>'+
                                            '</div>'+
                                            '<div class="buttons">'+
                                            '   <div style="display:block;">'+
                                            '   <a class="btn black-btn btn-xs add_skill" style="text-decoration: none; padding: 5px 10px;"><i class="fa fa-plus plus"></i> <span class="plus_text" style="left:-5px;">ADD</span></a>'+
                                            '   </div>'+
                                            '   <div style="display:block;margin-top:5px;">'+
                                            '   <a class="edit_skill btn black-btn btn-xs" style="padding-top: 6px;padding-bottom: 6px">Edit</a>'+
                                            '   </div>'+
                                            '   </div>'+
                                            '   </div>';

                                        if(pos != "last") {
                                            box_number = that.parents('.hierarchy_parent').find(".new_box").attr('data-number');
                                            that.parents('.hierarchy_parent').find(".new_box").append('<a href="" class="hierarchy select_skill"' +
                                                ' data-number="'+box_number+'" data-value="' + resp.skill_id + '" data-type="new">'+resp.skill_name+' &nbsp;&gt;</a> ');
                                                return false;
                                        }
                                        $(".all_levels").append(html);
                                    }
                                    else {
                                        var error = '';
                                        $.each(resp.errors, function (index, val) {
                                            error += '<span>' + val + '</span>';
                                        });
                                        toastr['error'](error, '');
                                        return false;
                                    }
                                }
                            });
                        }
                        else
                            $(".error_msg_skill").html('<span style="color:#a94442">Please enter skill name</span>');

                        return false;
                    }
                }
            }
        });

        $(".div-table-second-cell").css('z-index','100');
        $(".list-item-main").css('z-index','100');

        box.on("hidden.bs.modal", function (e) {
            $(".list-item-main").css('z-index','99999');
            $(".div-table-second-cell").css('z-index','99999');
        });

        box.modal('show');
        return false;
    });

    $(document).off("click",".delete_skill").on("click",".delete_skill",function(){

        var last_div = $(".all_levels").find(".hierarchy_parent").length - 1;
        var selected_id ='';
        var type='';
        if(last_div == 0) {
            var deleting_text = $(".all_levels").find(".hierarchy_parent:first").find('select').find(':selected').text();
            selected_id = $(".all_levels").find(".hierarchy_parent:first").find('select').val();
            type= $(this).parents(".all_levels").find(".hierarchy_parent:first").find('select option:selected').attr('data-type');
        }
        else {
            var deleting_text = $(".all_levels").find(".hierarchy_parent").eq(last_div).find('a.selected').html();
            selected_id = $(".all_levels").find(".hierarchy_parent").eq(last_div).find('a.selected').attr('data-value');;
            type= $(".all_levels").find(".hierarchy_parent").eq(last_div).find('a.selected').attr('data-type');
        }

        var total_divs = $(".all_levels").find(".hierarchy_parent").length + 1;
        var html = '<p>';
        var path_text = '';
        if(total_divs > 0){
            for(var i=0;i<total_divs - 1;i++){
                if(i == 0) {
                    html += "<span>" + $(".all_levels").find(".hierarchy_parent").eq(i).find('select').find(':selected').text() + "</span>";
                    path_text += $(".all_levels").find(".hierarchy_parent").eq(i).find('select').find(':selected').text();
                }
                else if(i == total_divs - 2) {
                    html += '<span class="highlight_delete">&nbsp;' + $(".all_levels").find(".hierarchy_parent").eq(i).find('a' +
                            '.selected').html().replace("&nbsp; &gt;","")
                        + '</span>';
                    path_text += $(".all_levels").find(".hierarchy_parent").eq(i).find('a' +
                        '.selected').html().replace("&nbsp; &gt;","");
                }
                else {
                    html += '<span>&nbsp;' + $(".all_levels").find(".hierarchy_parent").eq(i).find('a.selected').html() + '</span>';
                    path_text += $(".all_levels").find(".hierarchy_parent").eq(i).find('a.selected').html();
                }

            }
        }

        html+="</p>";
        var box = bootbox.dialog({
            title: "Delete skill",
            message: 'Are you sure you want to delete <span style="color:#a94442;">'+deleting_text.replace("&nbsp; &gt;","")
            +'</span>?'+html,
            buttons: {
                danger: {
                    label: "Cancel",
                    className: "btn-danger",
                    callback: function() {
                        bootbox.hideAll();
                    }
                },
                success: {
                    label: "Delete",
                    className: "btn-success",
                    callback: function(e) {
                        if($.trim(selected_id) != "" && $.trim(type) != "") {
                            $.ajax({
                                type: 'get',
                                url: siteURL + '/job_skills/delete',
                                data: {id: selected_id, type: type,path_text:path_text},
                                dataType: 'json',
                                success: function (resp) {
                                    if (resp.success) {
                                        bootbox.hideAll();
                                        showToastMessage('JOB_SKILL_DELETED');

                                        var total_div = $(".all_levels").find('.hierarchy_parent').length - 1;
                                        if(total_div == 0){
                                            $(".add_edit_skills").remove();
                                            $("#skill_firstbox option:selected").remove();
                                        }
                                        else{
                                            $(".add_edit_skills").remove();
                                            var total_skills = $(".all_levels").find('.hierarchy_parent').eq(total_div).find(".new_box").find('a').length;
                                            if(total_skills == 1){
                                                $(".all_levels").find('.hierarchy_parent').eq(total_div).remove();
                                                $(".all_levels").find('.hierarchy_parent').eq(total_div-1).find(".new_box").find('a.selected').trigger('click');
                                            }
                                            else {

                                                $(".all_levels").find('.hierarchy_parent').eq(total_div).find(".new_box").find('a.selected').remove();

                                            }
                                            if(total_div == 1)
                                                setTimeout(function(){
                                                    $(".all_levels").find('.hierarchy_parent').eq(0).find('select').trigger('change');
                                                },500);

                                        }
                                    }
                                    else {

                                        toastr['error'](resp.msg, '');
                                        bootbox.hideAll();
                                        return false;
                                    }
                                }
                            });
                            return false;
                        }
                        else {
                            $(".error_msg_skill").html('<span style="color:#a94442">Please select skill to delete</span>');
                        }
                    }
                }
            }
        });

        $(".div-table-second-cell").css('z-index','100');
        $(".list-item-main").css('z-index','100');

        box.on("hidden.bs.modal", function (e) {
            $(".list-item-main").css('z-index','99999');
            $(".div-table-second-cell").css('z-index','99999');
        });

        box.modal('show');
        return false;
    });

    $(document).off("click",".edit_skill").on("click",".edit_skill",function(){

        var skill_name = '';
        var selected = $(this).parents('.hierarchy_parent').find(".new_box").find("a.selected").length;
        var total_divs ='';
        var pos1 = $(this).attr('data-pos');

        var current_selected_value='';
        var type = null;


        if(typeof pos1 !== typeof undefined && pos1 !== false){
            if(pos1 == "first") {
                total_divs = 2;
                current_selected_value = $(this).parents(".all_levels").find(".hierarchy_parent:last").find('a.selected').attr('data-value');
                type= $(this).parents(".all_levels").find(".hierarchy_parent:last").find('a.selected').attr('data-type');
                box_number = 1;
            }
            else {
                total_divs = $(".all_levels").find(".hierarchy_parent").length + 1;
                if($(".all_levels").find(".hierarchy_parent").length == 1){
                    current_selected_value = $(this).parents(".all_levels").find(".hierarchy_parent").eq(0).find('select').val();
                    type = $(this).parents(".all_levels").find(".hierarchy_parent").eq(0).find('select').find(':selected').attr('data-type');
                    box_number = 0;
                }else {
                    current_selected_value = $(this).parents(".all_levels").find(".hierarchy_parent:last").find('a.selected').attr('data-value');
                    type = $(this).parents(".all_levels").find(".hierarchy_parent:last").find('a.selected').attr('data-type');
                    box_number = $(this).parents(".all_levels").find(".hierarchy_parent:last").find('a.selected').attr('data-number');
                }

            }
        }
        else {
            total_divs = $(this).parents('.hierarchy_parent').find(".new_box").attr('data-number');
            var current_box_number = $(this).parents('.hierarchy_parent').find(".new_box").attr('data-number');
            var target_div = current_box_number-2;
            if(target_div  == 0) {
                current_selected_value = $(this).parents(".all_levels").find(".hierarchy_parent:first").find('select').val();
                type= $(this).parents(".all_levels").find(".hierarchy_parent:first").find('select option:selected').attr('data-type');
                box_number = 1;
            }
            else {
                current_selected_value = $(this).parents(".all_levels").find(".hierarchy_parent").eq(target_div).find('a.selected').attr('data-value');
                type= $(this).parents(".all_levels").find(".hierarchy_parent").eq(target_div).find('a.selected').attr('data-type');
                box_number = $(this).parents(".all_levels").find(".hierarchy_parent").eq(target_div).find('a.selected').attr('data-number');
            }

        }

        if(total_divs > 0){
            var html = '';
            var text= '';
            for(var i=0;i<total_divs - 1;i++){
                if(i == 0) {
                    if(pos1 != "first") {
                        if(i == total_divs - 2)
                            text = $(".all_levels").find(".hierarchy_parent").eq(i).find('select').find(':selected').text();
                        else
                            html += "<span>" + $(".all_levels").find(".hierarchy_parent").eq(i).find('select').find(':selected').text() + "</span>";
                    }
                }
                else if(i == (total_divs - 2)) {
                    if(i == 0)
                        text = $(".all_levels").find(".hierarchy_parent").eq(i).find('select').find(':selected').text();
                    else
                        text = $(".all_levels").find(".hierarchy_parent").eq(i).find('a.selected').html();
                }else
                    html += "<span>" + $(".all_levels").find(".hierarchy_parent").eq(i).find('a.selected').html() + "</span>";
            }
            text = text.replace(">","");
            category_text=  html;
            html+='<span>&nbsp;[<input name="skill_name" type="text" style="border:1px solid #ccc" placeholder="[add new]" ' +
                'value="'+text.replace("&gt;","")+'" class="edit_skill_dynamic">]</span>';
        }


        var frm = '<form method="post" id="edit_skill_form">'+
            '<div class="row">'+
            '<div class="col-sm-12">'+
            '<label class="control-label">'+html+'</label>'+
            '<input type="hidden" name="selected_id" value="'+current_selected_value+'"/><input type="hidden" name="path_text" value="'+category_text+'"/><input type="hidden" name="tbl_type" value="'+type+'"/></div></div></form>';
        var that= $(this);
        var box = bootbox.dialog({
            title: "Update skill",
            message: frm,
            buttons: {
                danger: {
                    label: "Cancel",
                    className: "btn-danger",
                    callback: function() {
                        bootbox.hideAll();
                    }
                },
                success: {
                    label: "Edit Skill",
                    className: "btn-success",
                    callback: function(e) {
                        $.ajax({
                            type:'get',
                            url:siteURL+'/job_skills/edit',
                            data:$("#edit_skill_form").serialize(),
                            dataType:'json',
                            success:function(resp){
                                if(resp.success){
                                    bootbox.hideAll();
                                    showToastMessage('JOB_SKILL_UPDATED');
                                    if(box_number == 1 || $(".all_levels").find(".hierarchy_parent").length == 1){
                                        $('#skill_firstbox option:selected').text(resp.skill_name+' >');
                                        $('#skill_firstbox option:selected').attr('data-type',resp.type);
                                        $('#skill_firstbox option:selected').attr('value',resp.skill_id);
                                        if(box_number != 1 && $(".all_levels").find(".hierarchy_parent").length == 1){
                                            that.parents(".add_edit_skills").find("div:first").html('<span>Selected:<br/>'+resp.skill_name+'</span>');
                                        }
                                        return false;
                                    }
                                    else{
                                        $(".all_levels").find(".hierarchy_parent").eq(box_number-1).find(".new_box").find('a.selected').html(resp.skill_name+' >').attr('data-value',resp.skill_id);
                                        if(pos1 == "last"){
                                            that.parents(".add_edit_skills").find("div:first").html('<span>Selected:<br/>'+resp.skill_name+'</span>');
                                        }
                                    }

                                }
                                else{
                                    var error='';
                                    $.each(resp.errors,function(index,val){
                                        error+='<span>'+val+'</span>';
                                    });
                                    toastr['error'](error, '') ;
                                    return false;
                                }
                            }
                        })
                        return false;
                    }
                }
            }
        });

        $(".div-table-second-cell").css('z-index','100');
        $(".list-item-main").css('z-index','100');

        box.on("shown.bs.modal", function (e) {
            var val = $("#edit_skill_form").find('[name="skill_name"]').val();
            $("#edit_skill_form").find('[name="skill_name"]').css('width',((val.length)*8)+'px');
        });
        box.on("hidden.bs.modal", function (e) {
            $(".list-item-main").css('z-index','99999');
            $(".div-table-second-cell").css('z-index','99999');
        });

        box.modal('show');
        return false;
    });



    $(document).off("click",".mark-skill-approve").on("click",".mark-skill-approve",function(){
        var id = $(this).attr('data-id');
        var that = $(this);
        if($.trim(id) != ""){
            $.ajax({
                type:'get',
                url:siteURL+'/job_skills/approve_skill',
                data:{id:id},
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        showToastMessage('JOB_SKILL_APPROVED');
                        that.parents('tr').remove();
                    }
                    else
                        toastr['error'](resp.msg, '') ;

                }
            });
        }
        return false;
    });

    $(document).off("click",".discard-change").on("click",".discard-change",function(){
        var id = $(this).attr('data-id');
        var that = $(this);
        if($.trim(id) != ""){
            $.ajax({
                type:'get',
                url:siteURL+'/job_skills/discard_skill_changes',
                data:{id:id},
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        showToastMessage('JOB_SKILL_CHANGES_DISCARDED');
                        if(that.parents('tbody').find('tr').length > 1)
                            that.parents('tr').remove();
                        else
                        that.parents(".skill-approve-panel").remove();
                    }
                    else {
                        toastr['error'](resp.msg, '') ;
                    }
                }
            });
        }
        return false;
    });



    $(document).off("click",".mark-category-approve").on("click",".mark-category-approve",function(){
        var id = $(this).attr('data-id');
        var that = $(this);
        if($.trim(id) != ""){
            $.ajax({
                type:'get',
                url:siteURL+'/unit_category/approve_category',
                data:{id:id},
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        showToastMessage('UNIT_CATEGORY_APPROVED');
                        that.parents('tr').remove();
                    }
                    else
                        toastr['error'](resp.msg, '') ;

                }
            });
        }
        return false;
    });

    $(document).off("click",".discard-category-change").on("click",".discard-category-change",function(){
        var id = $(this).attr('data-id');
        var that = $(this);
        if($.trim(id) != ""){
            $.ajax({
                type:'get',
                url:siteURL+'/unit_category/discard_category_changes',
                data:{id:id},
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        showToastMessage('JOB_SKILL_CHANGES_DISCARDED');
                        if(that.parents('tbody').find('tr').length > 1)
                            that.parents('tr').remove();
                        else
                            that.parents(".skill-approve-panel").remove();
                    }
                    else {
                        toastr['error'](resp.msg, '') ;
                    }
                }
            });
        }
        return false;
    });


    $(document).off("click",".mark-area-of-interest-approve").on("click",".mark-area-of-interest-approve",function(){
        var id = $(this).attr('data-id');
        var that = $(this);
        if($.trim(id) != ""){
            $.ajax({
                type:'get',
                url:siteURL+'/area_of_interest/approve_area_of_interest',
                data:{id:id},
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        showToastMessage('AREA_OF_INTEREST_APPROVED');
                        that.parents('tr').remove();
                    }
                    else
                        toastr['error'](resp.msg, '') ;

                }
            });
        }
        return false;
    });

    $(document).off("click",".discard-area-of-interest-change").on("click",".discard-area-of-interest-change",function(){
        var id = $(this).attr('data-id');
        var that = $(this);
        if($.trim(id) != ""){
            $.ajax({
                type:'get',
                url:siteURL+'/area_of_interest/discard_area_of_interest_changes',
                data:{id:id},
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        showToastMessage('JOB_SKILL_CHANGES_DISCARDED');
                        if(that.parents('tbody').find('tr').length > 1)
                            that.parents('tr').remove();
                        else
                            that.parents(".skill-approve-panel").remove();
                    }
                    else {
                        toastr['error'](resp.msg, '') ;
                    }
                }
            });
        }
        return false;
    });


    // for featured unit
    function formatSkills (repo) {
        if (repo.loading) return repo.text;

        var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + repo.text + "</div></div></div></div>";

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


    var featured_unit = $("#featured_unit").select2({
        width: '100%',
        maximumSelectionLength: 1,

    }).on('select2:select',function(event){
        var id = $(event.currentTarget).find("option:selected").val();
        if($.trim(id) != ""){
            $.ajax({
                type:'get',
                url:siteURL+'/unit/set_featured_unit',
                data:{id:id,type:'set'},
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        showToastMessage('FEATURED_UNIT_SET_SUCCESSFULLY');
                    }
                    else {
                        toastr['error'](resp.msg, '') ;
                    }
                }
            });
        }
    }).on('select2:unselect',function(event){
        $.ajax({
            type:'get',
            url:siteURL+'/unit/set_featured_unit',
            data:{type:'delete'},
            dataType:'json',
            success:function(resp){
                if(resp.success){
                    showToastMessage('FEATURED_UNIT_REMOVED_SUCCESSFULLY');
                }
                else {
                    toastr['error'](resp.msg, '') ;
                }
            }
        });
    });

});

