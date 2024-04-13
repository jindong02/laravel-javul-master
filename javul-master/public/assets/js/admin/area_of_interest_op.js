$(document).off("click",".add_area_of_interest").on("click",".add_area_of_interest",function(){
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
                total_divs = $(".all_levels_area_of_interest").find(".hierarchy_parent").length + 1;
                box_number = "last";
                if($(".all_levels_area_of_interest").find(".hierarchy_parent").length == 1) {
                    parent_id = $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent").eq(0).find('select').val();
                    type= $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent").eq(0).find('select option:selected').attr('data-type');
                }
                else {
                    parent_id = $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent:last").find('a.selected').attr('data-value');
                    type = $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent:last").find('a.selected').attr('data-type');
                }
            }
        }else {
            total_divs = $(this).parents('.hierarchy_parent').find(".new_box").attr('data-number');
            var current_box_number = $(this).parents('.hierarchy_parent').find(".new_box").attr('data-number');
            box_number = current_box_number;
            var target_div = current_box_number-2;
            if(target_div  == 0) {
                parent_id = $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent:first").find('select').val();
                type= $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent:first").find('select option:selected').attr('data-type');
            }
            else {
                parent_id = $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent").eq(target_div).find('a.selected').attr('data-value');
                type= $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent").eq(target_div).find('a.selected').attr('data-type');
            }
        }

        if(total_divs > 0){
            var html = '';
            for(var i=0;i<total_divs - 1;i++){
                var text = '';
                if(i == 0) {
                    if(pos != "first")
                        html += "<span>" + $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(i).find('select').find(':selected').text() + "</span>";
                }
                else
                    html+="<span>&nbsp;"+$(".all_levels_area_of_interest").find(".hierarchy_parent").eq(i).find('a.selected').html()+"</span>";
            }
        }
        category_text=  html;
        html+='<span><input name="title" type="text" style="border:1px solid #ccc" placeholder="add new" ' +
            'class="new_title_dynamic"></span>';
        var frm = '<form method="post" id="add_area_of_interest_form">'+
            '<div class="row">'+
            '<div class="col-sm-12"><div class="error_msg_area_of_interest"></div>'+
            '<label class="control-label">'+html+'</label>' +
            '<input type="hidden" name="parent_id" value="'+parent_id+'"/><input type="hidden" name="tbl_type" ' +
            'value="'+type+'"/><input type="hidden" name="path_text" value="'+category_text+'"/></div>'+
            '</div>'+
            '</form>';
        var that = $(this);
        var box = bootbox.dialog({
            title: "Add new area of interest",
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
                    label: "Add Area of Interest",
                    className: "btn-success",
                    callback: function(e) {
                        var frm =$("#add_area_of_interest_form");
                        $(".error_msg_area_of_interest").html('');
                        if($.trim(frm.find("[name='title']").val()) != "") {

                            $.ajax({
                                type: 'get',
                                url: siteURL + '/area_of_interest/add',
                                data: $("#add_area_of_interest_form").serialize(),
                                dataType: 'json',
                                success: function (resp) {
                                    if (resp.success) {
                                        bootbox.hideAll();
                                        showToastMessage('AREA_OF_INTEREST_ADDED');

                                        if(box_number == "last"){
                                            var total_box = $(".all_levels_area_of_interest").find(".hierarchy_parent").length;
                                            total_box++;

                                            $(".add_edit_area_of_interest").remove();
                                        }
                                        else{
                                            if(box_number == 1){
                                                $('#area_of_interest_firstbox').append($('<option>', {value:resp.area_of_interest_id, text:resp.title+' >'}));
                                                $('#area_of_interest_firstbox option:last').attr('data-type','new');
                                                return false;

                                            }
                                            total_box = parseInt(box_number) + 1;
                                        }

                                        var html ='<div class="hierarchy_parent"><div class="hierarchy new_box" data-number="'+total_box+'">'+
                                            '<a href="" class="hierarchy select_area_of_interest" data-number="'+total_box+'" data-value="'+resp.area_of_interest_id+'" data-type="new">' +
                                            resp.title+' &nbsp; &gt;</a>'+
                                            '</div>'+
                                            '<div class="buttons">'+
                                            '   <div style="display:block;">'+
                                            '   <a class="btn black-btn btn-xs add_category" style="text-decoration: none; padding: 5px 10px;"><i class="fa fa-plus plus"></i> <span class="plus_text" style="left:-5px;">ADD</span></a>'+
                                            '   </div>'+
                                            '   <div style="display:block;margin-top:5px;">'+
                                            '   <a class="edit_category btn black-btn btn-xs" style="padding-top: 6px;padding-bottom: 6px">Edit</a>'+
                                            '   </div>'+
                                            '   </div>'+
                                            '   </div>';

                                        if(pos != "last") {
                                            box_number = that.parents('.hierarchy_parent').find(".new_box").attr('data-number');
                                            that.parents('.hierarchy_parent').find(".new_box").append('<a href="" class="hierarchy select_area_of_interest"' +
                                                ' data-number="'+box_number+'" data-value="' + resp.area_of_interest_id + '" data-type="new">'+resp.title+' &nbsp;&gt;</a> ');
                                                return false;
                                        }
                                        $(".all_levels_area_of_interest").append(html);
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
                            $(".error_msg_area_of_interest").html('<span style="color:#a94442">Please enter area of interest</span>');

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

    $(document).off("click",".delete_area_of_interest").on("click",".delete_area_of_interest",function(){

        var last_div = $(".all_levels_area_of_interest").find(".hierarchy_parent").length - 1;
        var selected_id ='';
        var type='';
        if(last_div == 0) {
            var deleting_text = $(".all_levels_area_of_interest").find(".hierarchy_parent:first").find('select').find(':selected').text();
            selected_id = $(".all_levels_area_of_interest").find(".hierarchy_parent:first").find('select').val();
            type= $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent:first").find('select option:selected').attr('data-type');
        }
        else {
            var deleting_text = $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(last_div).find('a.selected').html();
            selected_id = $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(last_div).find('a.selected').attr('data-value');;
            type= $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(last_div).find('a.selected').attr('data-type');
        }

        var total_divs = $(".all_levels_area_of_interest").find(".hierarchy_parent").length + 1;
        var html = '<p>';
        var path_text = '';
        if(total_divs > 0){
            for(var i=0;i<total_divs - 1;i++){
                if(i == 0) {
                    html += "<span>" + $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(i).find('select').find(':selected').text() + "</span>";
                    path_text += $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(i).find('select').find(':selected').text();
                }
                else if(i == total_divs - 2) {
                    html += '<span class="highlight_delete">&nbsp;' + $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(i).find('a' +
                            '.selected').html().replace("&nbsp; &gt;","")
                        + '</span>';
                    path_text += $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(i).find('a' +
                        '.selected').html().replace("&nbsp; &gt;","");
                }
                else {
                    html += '<span>&nbsp;' + $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(i).find('a.selected').html() + '</span>';
                    path_text += $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(i).find('a.selected').html();
                }

            }
        }

        html+="</p>";
        var box = bootbox.dialog({
            title: "Delete area of interest",
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
                                url: siteURL + '/area_of_interest/delete',
                                data: {id: selected_id, type: type,path_text:path_text},
                                dataType: 'json',
                                success: function (resp) {
                                    if (resp.success) {
                                        bootbox.hideAll();
                                        showToastMessage('AREA_OF_INTEREST_DELETED');

                                        var total_div = $(".all_levels_area_of_interest").find('.hierarchy_parent').length - 1;
                                        if(total_div == 0){
                                            $(".add_edit_area_of_interest").remove();
                                            $("#area_of_interest_firstbox option:selected").remove();
                                        }
                                        else{
                                            $(".add_edit_area_of_interest").remove();
                                            var total_categorys = $(".all_levels_area_of_interest").find('.hierarchy_parent').eq(total_div).find(".new_box").find('a').length;
                                            if(total_categorys == 1){
                                                $(".all_levels_area_of_interest").find('.hierarchy_parent').eq(total_div).remove();
                                                $(".all_levels_area_of_interest").find('.hierarchy_parent').eq(total_div-1).find(".new_box").find('a.selected').trigger('click');
                                            }
                                            else {

                                                $(".all_levels_area_of_interest").find('.hierarchy_parent').eq(total_div).find(".new_box").find('a.selected').remove();

                                            }
                                            if(total_div == 1)
                                                setTimeout(function(){
                                                    $(".all_levels_area_of_interest").find('.hierarchy_parent').eq(0).find('select').trigger('change');
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
                            $(".error_msg_category").html('<span style="color:#a94442">Please select area of interest to delete</span>');
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

    $(document).off("click",".edit_area_of_interest").on("click",".edit_area_of_interest",function(){

        var category_name = '';
        var selected = $(this).parents('.hierarchy_parent').find(".new_box").find("a.selected").length;
        var total_divs ='';
        var pos1 = $(this).attr('data-pos');

        var current_selected_value='';
        var type = null;


        if(typeof pos1 !== typeof undefined && pos1 !== false){
            if(pos1 == "first") {
                total_divs = 2;
                current_selected_value = $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent:last").find('a.selected').attr('data-value');
                type= $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent:last").find('a.selected').attr('data-type');
                box_number = 1;
            }
            else {
                total_divs = $(".all_levels_area_of_interest").find(".hierarchy_parent").length + 1;
                if($(".all_levels_area_of_interest").find(".hierarchy_parent").length == 1){
                    current_selected_value = $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent").eq(0).find('select').val();
                    type = $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent").eq(0).find('select').find(':selected').attr('data-type');
                    box_number = 0;
                }else {
                    current_selected_value = $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent:last").find('a.selected').attr('data-value');
                    type = $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent:last").find('a.selected').attr('data-type');
                    box_number = $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent:last").find('a.selected').attr('data-number');
                }

            }
        }
        else {
            total_divs = $(this).parents('.hierarchy_parent').find(".new_box").attr('data-number');
            var current_box_number = $(this).parents('.hierarchy_parent').find(".new_box").attr('data-number');
            var target_div = current_box_number-2;
            if(target_div  == 0) {
                current_selected_value = $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent:first").find('select').val();
                type= $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent:first").find('select option:selected').attr('data-type');
                box_number = 1;
            }
            else {
                current_selected_value = $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent").eq(target_div).find('a.selected').attr('data-value');
                type= $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent").eq(target_div).find('a.selected').attr('data-type');
                box_number = $(this).parents(".all_levels_area_of_interest").find(".hierarchy_parent").eq(target_div).find('a.selected').attr('data-number');
            }

        }

        if(total_divs > 0){
            var html = '';
            var text= '';
            for(var i=0;i<total_divs - 1;i++){
                if(i == 0) {
                    if(pos1 != "first") {
                        if(i == total_divs - 2)
                            text = $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(i).find('select').find(':selected').text();
                        else
                            html += "<span>" + $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(i).find('select').find(':selected').text() + "</span>";
                    }
                }
                else if(i == (total_divs - 2)) {
                    if(i == 0)
                        text = $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(i).find('select').find(':selected').text();
                    else
                        text = $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(i).find('a.selected').html();
                }else
                    html += "<span>" + $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(i).find('a.selected').html() + "</span>";
            }
            text = text.replace(">","");
            category_text=  html;
            html+='<span>&nbsp;[<input name="title" type="text" style="border:1px solid #ccc" placeholder="[add new]" ' +
                'value="'+text.replace("&gt;","")+'" class="edit_title_dynamic">]</span>';
        }


        var frm = '<form method="post" id="edit_area_of_interest_form">'+
            '<div class="row">'+
            '<div class="col-sm-12">'+
            '<label class="control-label">'+html+'</label>'+
            '<input type="hidden" name="selected_id" value="'+current_selected_value+'"/><input type="hidden" name="path_text" value="'+category_text+'"/><input type="hidden" name="tbl_type" value="'+type+'"/></div></div></form>';
        var that= $(this);
        var box = bootbox.dialog({
            title: "Update category",
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
                    label: "Edit Area of Interest",
                    className: "btn-success",
                    callback: function(e) {
                        $.ajax({
                            type:'get',
                            url:siteURL+'/area_of_interest/edit',
                            data:$("#edit_area_of_interest_form").serialize(),
                            dataType:'json',
                            success:function(resp){
                                if(resp.success){
                                    bootbox.hideAll();
                                    showToastMessage('AREA_OF_INTEREST_UPDATED');
                                    if(box_number == 1 || $(".all_levels_area_of_interest").find(".hierarchy_parent").length == 1){
                                        $('#area_of_interest_firstbox option:selected').text(resp.title+' >');
                                        $('#area_of_interest_firstbox option:selected').attr('data-type',resp.type);
                                        $('#area_of_interest_firstbox option:selected').attr('value',resp.area_of_interest_id);
                                        if(box_number != 1 && $(".all_levels_area_of_interest").find(".hierarchy_parent").length == 1){
                                            that.parents(".add_edit_area_of_interest").find("div:first").html('<span>Selected:<br/>'+resp.title+'</span>');
                                        }
                                        return false;
                                    }
                                    else{
                                        $(".all_levels_area_of_interest").find(".hierarchy_parent").eq(box_number-1).find(".new_box").find('a.selected').html(resp.title+' >').attr('data-value',resp.area_of_interest_id);
                                        if(pos1 == "last"){
                                            that.parents(".add_edit_area_of_interest").find("div:first").html('<span>Selected:<br/>'+resp.title+'</span>');
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
            var val = $("#edit_area_of_interest_form").find('[name="title"]').val();
            $("#edit_area_of_interest_form").find('[name="title"]').css('width',((val.length)*8)+'px');
        });
        box.on("hidden.bs.modal", function (e) {
            $(".list-item-main").css('z-index','99999');
            $(".div-table-second-cell").css('z-index','99999');
        });

        box.modal('show');
        return false;
    });