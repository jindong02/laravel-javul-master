$(function(){
    // allowed only digits
    $(".onlyDigits").keypress(function (e) {
        //if the letter is not digit then don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });
    
    /**
     * below function are used for validate bitcoin value with 4 decimal poin
     * bitcoin minimum transfer amount is 0.0001
     */
    $(document).off('keypress','.onlyDigitWithDecimal').on('keypress','.onlyDigitWithDecimal',function(event) {
        var $this = $(this);
        if ((event.which != 46 || $this.val().indexOf('.') != -1) &&
           ((event.which < 48 || event.which > 57) &&
           (event.which != 0 && event.which != 4))) {
            event.preventDefault();
        }
      
        var text = $(this).val();
        if ((event.which == 46) && (text.indexOf('.') == -1)) {
         setTimeout(function() {
          if ($this.val().substring($this.val().indexOf('.')).length > 4) {
           $this.val($this.val().substring(0, $this.val().indexOf('.') + 4));
           
          }     
         }, 1);
        }
      
        if ((text.indexOf('.') != -1) &&
         (text.substring(text.indexOf('.')).length > 4) &&
         (event.which != 0 && event.which != 4) &&
         ($(this)[0].selectionStart >= text.length - 4)) {
          event.preventDefault();
        }      
    });

    //show loaded on ajax calls.
    $loading = $('#loadingDiv').hide();
    if(login){
        check_assigned_task();
        $(document).off('click','.offer').on('click','.offer',function(){
            var tid=$(this).attr('data-task_id');
            if($(this).hasClass('btn-success'))
                accept_reject_offer(tid,'/tasks/accept_offer');
            else if($(this).hasClass('re_assigned'))
                accept_reject_offer(tid,'/tasks/accept_offer');
            else if($(this).hasClass('btn-danger'))
                accept_reject_offer(tid,'/tasks/reject_offer');
        })
    }

    $(document).off("click",".more_unit_site_activity_btn").on('click','.more_unit_site_activity_btn',function(e){
        e.preventDefault();
        $(".site_activity_loading").show();
        var page = $(this).data('url').split('page=')[1];
        var unit_id = $(this).data('unit_id');
        $(this).parents(".loading_content_hide").css('opacity','0.5');
        getUnitActivity(page,unit_id);
        return false;
    });

    $(document).off("click",".more_site_activity_btn").on('click','.more_site_activity_btn',function(e){
        e.preventDefault();
        $(".site_activity_loading").show();
        var from_page = $(this).data('from_page');
        var page = $(this).data('url').split('page=')[1];
        $(this).parents(".loading_content_hide").css('opacity','0.5');
        if($.trim(from_page) != "" && (from_page == "global" || from_page == "user")) {
            if(from_page == "global")
                from_page = "global_activity";

            getGlobalSiteActivity(page, from_page);
        }
        else
            getSiteActivity(page);
        return false;
    });

    //load more units
    $(document).off("click",".more-units").on('click','.more-units',function(e){
        e.preventDefault();
        $(".unit_loading").show();
        var page = $(this).data('url').split('page=')[1];
        $(this).parents(".loading_content_hide").css('opacity','0.5');
        getUnits(page);
        return false;
    });

    //load more units
    $(document).off("click",".more-issues").on('click','.more-issues',function(e){
        e.preventDefault();
        $(".unit_loading").show();
        var page = $(this).data('url').split('page=')[1];
        $(this).parents(".loading_content_hide").css('opacity','0.5');
        getIssues(page);
        return false;
    });

    //load more units
    $(document).off("click",".more-objectives").on('click','.more-objectives',function(e){
        e.preventDefault();
        $(".objective_loading").show();
        var page = $(this).data('url').split('page=')[1];
        var from_page = $(this).data('from_page');
        var unit_id = $(this).data('unit_id');
        if($.trim(unit_id) == "")
            unit_id = 'none';
        if($.trim(from_page) == "" || from_page != "unit_view")
            from_page = "main_page";
        $(this).parents(".loading_content_hide").css('opacity','0.5');
        getObjectives(page,unit_id,from_page);
        return false;
    });

    //load more units
    $(document).off("click",".more-tasks").on('click','.more-tasks',function(e){
        e.preventDefault();
        $(".task_loading").show();
        var page = $(this).data('url').split('page=')[1];
        var from_page = $(this).data('from_page');
        var unit_id = $(this).data('unit_id');
        var objective_id =$(this).data('objective_id');
        if($.trim(unit_id) == "")
            unit_id = 'none';
        if($.trim(objective_id) == "")
            objective_id = 'none';
        if($.trim(from_page) == "" || from_page != "unit_view")
            from_page = "main_page";
        $(this).parents(".loading_content_hide").css('opacity','0.5');
        getTasks(page,unit_id,objective_id,from_page);
        return false;
    });

    $(document).off("click",".more-skills").on('click','.more-skills',function(e){
        e.preventDefault();
        $(".skill_loading").show();
        var page = $(this).data('url').split('page=')[1];
        $(this).parents(".loading_content_hide").css('opacity','0.5');
        $.ajax({
            type:'get',
            url:siteURL+'/skills/get_skill_paginate',
            data:{page:page},
            success:function(resp){
                $(".skill-table tbody tr:last-child").remove();
                $(".skill-table tbody").append(resp.html);
                $(".skill_loading").hide();
                $(".loading_content_hide").css('opacity','1');
            }
        });
        return false;
    });
    $(document).off("click",".more-area-of-interest").on('click','.more-area-of-interest',function(e){
        e.preventDefault();
        $(".area_of_interest_loading").show();
        var page = $(this).data('url').split('page=')[1];
        $(this).parents(".loading_content_hide").css('opacity','0.5');
        $.ajax({
            type:'get',
            url:siteURL+'/area_of_interest/get_area_of_interest_paginate',
            data:{page:page},
            success:function(resp){
                $(".area_of_interest-table tbody tr:last-child").remove();
                $(".area_of_interest-table tbody").append(resp.html);
                $(".area_of_interest_loading").hide();
                $(".loading_content_hide").css('opacity','1');
            }
        });
        return false;
    });
    $(document).off("click",".more-category").on('click','.more-category',function(e){
        e.preventDefault();
        $(".category_loading").show();
        var page = $(this).data('url').split('page=')[1];
        $(this).parents(".loading_content_hide").css('opacity','0.5');
        $.ajax({
            type:'get',
            url:siteURL+'/category/get_category_paginate',
            data:{page:page},
            success:function(resp){
                $(".category-table tbody tr:last-child").remove();
                $(".category-table tbody").append(resp.html);
                $(".category_loading").hide();
                $(".loading_content_hide").css('opacity','1');
            }
        });
        return false;
    });

    //Global Search Form
    $('#form-global-search').submit(function() {
        $(this).find("[name='search_term']").val($.trim($(this).find("[name='search_term']").val()));
    });



    //add item to watchlist
    var flag = false;
    $(".add_to_my_watchlist").on('click',function(){
        var type=$(this).data('type');
        var id = $(this).data('id');
        var url_redirect = $(this).data('redirect');

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-top-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        if(!flag && !$('#toast-container').length) {
            flag = true;
            if ($.trim(type) != "" && $.trim(id) != '') {
                $.ajax({
                    type: 'get',
                    url: siteURL + '/add_to_watchlist',
                    data: {type: type, id: id, sessionUrl: url_redirect},
                    dataType: 'json',
                    success: function (resp) {
                        flag = false;
                        if (!resp.success) {
                            if(resp.msg == 'Please login to continue.'){
                                window.location.href = siteURL + '/login';
                            }else{
                                toastr['error'](resp.msg, '');
                            }
                        } else {
                            toastr['success'](resp.msg, '');
                        }
                    },
                    error: function (error) {
                    }
                })
            }
        }
    });

    var widget;
    var onloadCallbackRecaptcha = function() {
        var myinto = setInterval(function(){
            var elementExists = document.getElementById("gcaptcha");
            if ( $('#gcaptcha').length ) {
                widget = grecaptcha.render(document.getElementById('gcaptcha'), {
                    'sitekey' : '6LfDyawUAAAAAIeiKY1kIW8hs0e52tAcmV3Gk0id',
                    'callback' : correctCaptcha
                });
                $("button[data-bb-handler='success']").attr("disabled", true);
                var captchaResponse = grecaptcha.getResponse();
                clearInterval(myinto);
            }
        }, 1000);
    };

    var correctCaptcha = function(response) {
        $("button[data-bb-handler='success']").attr("disabled", false);
    };

    $(".report").click(function(){
        var captchaResponse = "";
        var visited_url = window.location.href;
        $(".div-table-second-cell").css('z-index','100');
        $(".list-item-main").css('z-index','100');

        var html = '<form class="form-horizontal" role="form" method="post" action="javascript:grecaptcha.reset(widget);">'+
            '<div class="form-group">'+
            '<label for="visited url" class="col-sm-2 control-label paddpop">Visited URL</label>'+
            '<div class="col-sm-10">'+
            '<input type="text" class="form-control" disabled  id="url" name="url" value="'+visited_url+'">'+
            '</div>'+
            '</div>'+
            '<div class="form-group">'+
            '<label for="message" class="col-sm-2 control-label">Message</label>'+
            '<div class="col-sm-10">'+
            '<textarea class="form-control" id="message" rows="4" name="message" placeholder="Write your message here.">'+
            '</textarea><span class="text-danger errors" id="message_error"></span>'+
            '</div>'+
            '</div>';


        if($.trim(login) == ""){
            html+= '<div class="form-group">'+
                '<div class="col-sm-10">'+
                ' <div id="gcaptcha" ></div>'+
                '</div>'+
                '</div>'+
                '</form>';
            onloadCallbackRecaptcha();
            var captcha_intval = setInterval(function(){
                captchaResponse = grecaptcha.getResponse();
            }, 1000);
        }else{
             captchaResponse = "";
        }

        var bootbox_dialog=bootbox.dialog({
            title: 'Report a Concern<br><p class="text">Your message will be sent to the Javul.org administrator.',
            message:html,
            buttons: {
                success: {
                    label: "Submit",
                    className: 'btn-success',
                    callback: function () {
                        clearInterval(captcha_intval);
                        $(".errors").html('');
                        var captcha_value = captchaResponse;
                        var visit_url = $('#url').val();
                        var message = $('#message').val();
                        $.ajax({
                            type: 'post',
                            url: siteURL + '/reportconc',
                            dataType: 'json',
                            data: {
                                _token: report_concern_token,
                                visit_url: visit_url,
                                message: message,
                                captcha_value: captcha_value
                            },
                            success: function (response) {
                                if (!response.success) {
                                    console.log(response);
                                    $.each(response.errors, function (index, value) {
                                        $("[id='" + index + "_error']").html(value);
                                    });

                                    if(response.errors.message = 'The message field is required.' && response.auth_check.message == 0){
                                        grecaptcha.reset();
                                    }

                                } else {
                                    showToastMessage('THANK_YOU_YOUR_MESSAGE_WAS_SENT_TO_JAVUL');
                                    captcha_code = response.captcha_value;
                                    bootbox.hideAll();
                                }
                            },
                            error: function ($err) {
                                console.log($err);
                            }
                        });
                        return false;
                    }
                }
                },
                cancel:{
                    label:'Cancel',
                    className:'btn-danger'
                }
        });

        bootbox_dialog.on("hidden.bs.modal", function (e) {
            $.ajax({
                type:'get',
                url:siteURL+'/close_report',
                dataType:'json',
                success:function(response){
                    captcha_code=response.captcha_value;
                }
            });
        });

        bootbox_dialog.modal('show');
    });
});

function check_assigned_task(){
    $.ajax({
        type:'get',
        url:siteURL+'/tasks/check_assigned_task',
        dataType:'json',
        success:function(resp){
            if(resp.success){
                if($(".confirmation_box_"+resp.task_id).length == 0){
                    $(".content > .container").prepend('<div class="row"><div class="col-sm-12">'+resp.html+'</div></div>');
                }
                /*bootbox.dialog({
                    message: resp.html,
                    title: resp.title,
                    buttons: {
                        success: {
                            label: resp.ok,
                            className: "btn-success",
                            callback: function() {
                                accept_reject_offer(resp.task_id,'/tasks/accept_offer');
                            }
                        },
                        danger: {
                            label: resp.cancel,
                            className: "btn-danger",
                            callback: function() {
                                accept_reject_offer(resp.task_id,'/tasks/reject_offer');
                            }
                        }
                    }
                });*/
            }else
            {
                setTimeout(function(){
                    check_assigned_task();
                },15000)
            }
        }
    })
}

function accept_reject_offer(task_id,url){
    $.ajax({
        type:'get',
        url:siteURL+url,
        data:{task_id:task_id},
        dataType:'json',
        success:function(resp){
            $(".close").trigger('click');
            setTimeout(function(){
                check_assigned_task();
            },15000)
        }
    });
}

function getUnitActivity(page,unit_id){


    $.ajax({
        type:'get',
        url:siteURL+'/get_unit_site_activity_paginate',
        data:{page:page,unit_id:unit_id},
        success:function(resp){
            $(".site_activity_list .panel-body").find(".more-btn").remove();
            $(".site_activity_list .panel-body").append(resp.html);
            $(".site_activity_loading").hide();
            $(".loading_content_hide").css('opacity','1');
        }
    });
}

function getSiteActivity(page){
    $.ajax({
        type:'get',
        url:siteURL+'/get_site_activity_paginate',
        data:{page:page},
        success:function(resp){
            $(".site_activity_list .panel-body").find(".more-btn").remove();
            $(".site_activity_list .panel-body").find('.last-site-activity').remove();

            $(".site_activity_list .panel-body").append(resp.html);
            $(".site_activity_loading").hide();
            $(".loading_content_hide").css('opacity','1');

            $(".site_activity_list .panel-body").find('span.tooltipster').each(function(){
                if(!$(this).hasClass('tooltipstered')){
                    $(this).tooltipster({
                        position: 'right'
                    });
                }
            });

        }
    });
}

function getGlobalSiteActivity(page,from_page){
    $.ajax({
        type:'get',
        url:siteURL+'/get_site_activity_paginate',
        data:{page:page,from_page:from_page},
        success:function(resp){
            $(".site_activity_list .panel-body").find(".more-btn").remove();
            $(".site_activity_list .panel-body").find('.last-site-activity').remove();
            $(".site_activity_list .panel-body").append(resp.html);
            $(".site_activity_loading").hide();
            $(".loading_content_hide").css('opacity','1');

            $(".site_activity_list .panel-body").find('span.tooltipster').each(function(){
                if(!$(this).hasClass('tooltipstered')){
                    $(this).tooltipster({
                        position: 'right'
                    });
                }
            });
        }
    });
}

function getUnits(page){
    $.ajax({
        type:'get',
        url:siteURL+'/units/get_units_paginate',
        data:{page:page},
        success:function(resp){
            $(".unit-table tbody tr:last-child").remove();
            $(".unit-table tbody").append(resp.html);
            $(".unit_loading").hide();
            $(".loading_content_hide").css('opacity','1');
        }
    });
}

function getIssues(page){
    $.ajax({
        type:'get',
        url:siteURL+'/issues/get_issues_paginate',
        data:{page:page},
        success:function(resp){
            $(".unit-table tbody tr:last-child").remove();
            $(".unit-table tbody").append(resp.html);
            $(".unit_loading").hide();
            $(".loading_content_hide").css('opacity','1');
        }
    });
}

function getObjectives(page,unit_id,from_page){
    $.ajax({
        type:'get',
        url:siteURL+'/objectives/get_objectives_paginate',
        data:{page:page,from_page:from_page,unit_id:unit_id},
        success:function(resp){
            $(".objective-table tbody tr:last-child").remove();
            $(".objective-table tbody").append(resp.html);
            $(".objective_loading").hide();
            $(".loading_content_hide").css('opacity','1');
        }
    });
}

function getTasks(page,unit_id,objective_id,from_page){
    $.ajax({
        type:'get',
        url:siteURL+'/tasks/get_tasks_paginate',
        data:{page:page,from_page:from_page,unit_id:unit_id,objective_id:objective_id},
        success:function(resp){
            $(".tasks-table tbody tr:last-child").remove();
            $(".tasks-table tbody").append(resp.html);
            $(".task_loading").hide();
            $(".loading_content_hide").css('opacity','1');
        }
    });
}