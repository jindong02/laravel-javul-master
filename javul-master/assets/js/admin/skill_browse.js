$(function(){
    $(document).off("change","#skill_firstbox").on('change',"#skill_firstbox",function(event){
        var that = $(this);
        getNextBox(that,page);
        if(page=="task" || page=="account") {
            var text = $(this).find(':selected').text();
            text= text.replace('>','');
            $(".selected_text_task").html('<b>' + text + '</b>');
            browse_skill_box.find('.okay-btn').prop('disabled',false);
            //For my account screen
            if(page == 'account' && hasOpenJobSkill)
                selected_skill_id.pop();
            if(page == 'account' && !hasOpenJobSkill)
                hasOpenJobSkill = true;
            //end
            selected_skill_id.push($(this).val());
        }
        return false;
    });
    $(document).off("click","a.select_skill").on('click',"a.select_skill",function(event){
        $(this).parents('.new_box').find("a.hierarchy").removeClass('selected');
        var that = $(this).addClass('selected');
        if(page=="task" || page=="account"){
            var text = $(this).html();
            text= text.replace('&nbsp; &gt;','');
            $(".selected_text_task").html('<b>' + text + '</b>');
            browse_skill_box.find('.okay-btn').prop('disabled',false);
            selected_skill_id.pop();
            selected_skill_id.push($(this).attr('data-value'));
        }
        // $(this).parents('.hierarchy_parent').find(".buttons").find(".edit_skill").removeClass("disabled");
        getNextBox(that,page);
        return false;
    });

    function getNextBox(that){

        if(that.attr('id') == "skill_firstbox"){
            var id =that.val();
            var box_number = that.data('number');
            var type= that.find(':selected').attr('data-type');
        }
        else
        {
            var id =that.attr('data-value');
            var box_number = that.attr('data-number');
            var type= that.attr('data-type');
        }
        $.ajax({
            type:'get',
            url:siteURL+'/job_skills/get_next_level_skills',
            data:{id:id,type:type,page:page},
            dataType:'json',
            success:function(resp){
                if(resp.success){

                    if(Object.keys(resp.data).length > 0)
                    {
                        $(".add_edit_skills").remove();
                        var next_level=$(".all_levels").find('.hierarchy_parent').length;
                        if(next_level > box_number ){
                            for(var i=box_number;i<=next_level;i++){
                                if($(".all_levels").find('.hierarchy_parent').length != box_number)
                                    $(".all_levels").find('.hierarchy_parent:last').remove();
                            }
                        }
                        next_level=$(".all_levels").find('.hierarchy_parent').length;

                        var html = '<div class="hierarchy_parent"><div class="hierarchy new_box" data-number="'+
                            (next_level+1)+'">';
                        $.each(resp.data,function(index,val){
                            if(val.hasSubOption)
                                html+='<a  href="#" class="hierarchy select_skill" data-number="'+(next_level+1)+'" data-value="'+index+'" ' + 'data-type="'+val.type+'" >'+val.name+'&nbsp; ></a>';
                            else
                                html+='<a  href="#" class="hierarchy select_skill" data-number="'+(next_level+1)+'" data-value="'+index+'" ' + 'data-type="'+val.type+'" >'+val.name+'</a>';
                        });
                        if(page == "site_admin") {
                            html += '</div>' +
                                '<div class="buttons">' +
                                '<div style="display:block;">' +
                                '<a href="#" class="btn black-btn btn-xs add_skill" style="text-decoration: none; padding: 5px 10px;">' +
                                '<i class="fa fa-plus plus"></i> <span class="plus_text" style="left:-5px;">ADD</span>' +
                                '</a></div>' +
                                '<div style="display:block;margin-top:5px;"><a class="edit_skill btn black-btn ' +
                                'btn-xs" style="padding-top: 6px;padding-bottom: 6px">Edit</a></div>' +
                                '</div></div>';
                        }

                        $(".all_levels").append(html);
                        return false;
                    }
                    else{
                        $(".add_edit_skills").remove();
                        var next_level=$(".all_levels").find('.hierarchy_parent').length;
                        if(next_level > box_number ){
                            for(var i=box_number;i<=next_level;i++){
                                if($(".all_levels").find('.hierarchy_parent').length != box_number)
                                    $(".all_levels").find('.hierarchy_parent:last').remove();
                            }
                        }
                        if($(".all_levels").find(".hierarchy_parent").length > 1)
                            var selected_text = $(".all_levels").find(".hierarchy_parent:last").find("a.selected").html();
                        else
                            var selected_text = $(".all_levels").find(".hierarchy_parent").eq(0).find('select').find(':selected').text();
                        selected_text = selected_text.replace(">","");
                        selected_text = selected_text.replace("&nbsp; &gt;","");
                        if(page == "site_admin") {
                            var html = '<div class="add_edit_skills"><div style="border:1px solid #828790;border-style: dashed;background-color:#EEEEEE;text-align:center;">' +
                                '<img src="' + siteURL + '/assets/images/completed.png" style="width:20px;vertical-align:top;"><span>Selected:<br/>' + selected_text.replace(">", "") + '</span>' +
                                '</div><div style="margin-top:5px;margin-left:10px;">' +
                                '<a  class="add_skill btn black-btn btn-xs" data-pos="last" style="text-decoration: none; padding: 5px 10px;display: block;">' +
                                '<i class="fa fa-plus plus"></i> <span class="plus_text" style="left:-5px;">ADD</span></button>' +
                                '<a class="edit_skill btn black-btn btn-xs" data-pos="last" style="text-decoration: none; ' +
                                'padding-top: 6px;padding-bottom: 6px;display: block;margin-top:5px;">Edit</a>' +
                                '<a class="delete_skill btn black-btn btn-xs" style="text-decoration: none;' +
                                'padding-top: 7px;padding-bottom: 7px;display: block;margin-top:5px;">DELETE SKILL</a></div>' +
                                '</div>';
                        }

                        $(".all_levels").append(html);
                    }

                }

            }
        });
    }

    $(".browse-skills").on('click',function(){
        $.ajax({
            type:'get',
            url:siteURL+'/job_skills/browse_skills',
            data : { from : page },
            dataType:'json',
            success:function(resp){
                if(resp.success){
                    var class_name = 'btn-success okay-btn';
                    if(page == 'account')//hide "Set Skill" button on my account screen reference issue(#101 : Github)
                        class_name = 'btn-success okay-btn hide';
                    browse_skill_box = bootbox.dialog({
                        message: resp.html,
                        title: "Browse Skill",
                        buttons: {
                            success: {
                                label: "Set Skill",
                                className: class_name,
                                callback: function(e) {
                                    if($.trim(selected_skill_id) != ""){
                                        $("#task_skills").select2('val',selected_skill_id);
                                    }else {
                                        showToastMessage('PLEASE_SELECT_SKILL');
                                        return false;
                                    }
                                }
                            },
                            cancel: {
                                label: "Close",
                                className: "btn-danger cancel-btn",
                                callback: function(e) {
                                    return true;
                                }
                            }
                        }
                    });
                    browse_skill_box.on("shown.bs.modal", function (e) {
                        browse_skill_box.find('.okay-btn').prop('disabled',true);
                    });
                    browse_skill_box.on("hidden.bs.modal", function (e) {
                        if(page == "account"){
                            $.ajax({
                                type:'post',
                                url:siteURL+'/job_skills/update_user_skill',
                                data:{ selected_skill_id:selected_skill_id, update_skill:true, _token: window.report_concern_token },
                                dataType:'json',
                                success:function(resp){
                                    if(resp.success){
                                        if($.trim(selected_skill_id) != ""){
                                            $("#task_skills").select2('val',selected_skill_id);
                                        }
                                        browse_skill_box='';
                                    }
                                }
                            });
                            hasOpenJobSkill = false;
                        }else{
                            browse_skill_box='';
                        }
                    });

                    browse_skill_box.modal('show');
                }
            }
        });
        return false;
    });

    //to delete selected skills from my_account screen
    $(document).off("click",".delete-selected-skill-tag").on('click','.delete-selected-skill-tag',function(e){
        var skill_id = $.trim($(this).data('id'));
        var $this = $(this);
        if(skill_id !== ''){
            $.ajax({
                type:'post',
                url:siteURL+'/job_skills/update_user_skill',
                data:{ id:skill_id, delete_skill:true, _token: window.report_concern_token },
                dataType:'json',
                success:function(resp){
                    if(resp.success){
                        $this.parents('.badge').remove();
                        var ind = selected_skill_id.indexOf(skill_id);
                        selected_skill_id.splice(ind,1);
                        if($(".delete-selected-skill-tag").length < 1)
                            $(".selected_text_task").html('<b> None </b>');
                        $("#task_skills").select2('val',selected_skill_id);
                    }
                }
            });
        }
    });
});