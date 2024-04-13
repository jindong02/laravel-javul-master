$(function(){
    $('#tabs').tab();

    $('[data-numeric]').payment('restrictNumeric');
    /*$('.cc-number').payment('formatCardNumber');
    $('.cc-cvc').payment('formatCardCVC');*/
    $.fn.toggleInputError = function(erred) {
        this.parents('.col-sm-4').toggleClass('has-error', erred);
        return this;
    };

    $("#submit_donate").on('click',function(e){
        e.preventDefault();
        $(".remove-alert ").remove();
        var $form = $('#donate_amount_form');
        if($("#paymentMethod").val() == "Zcash"){
            zcashPayment($form);
            return false;
        }
        //$($form).attr('action', siteURL+'/funds/donate-amount');
        $($form).attr('action', siteURL+'/funds/donate-amount');
        $("#donate_amount").toggleInputError(!$.payment.validateAmount($('#donate_amount').val()));
        if($('.has-error').length == 0){
            $("#donate_amount").attr('readonly','readonly');
            $(this).prop('disabled',true);
            $form.get(0).submit();
        }
    })

    /**
     * Zcash Receive Donation
     */
    function zcashPayment($form){
        $(".div-table-second-cell").css('z-index','100');
        $(".list-item-main").css('z-index','100');
        $("#loadingDiv").show();

        $.ajax({
            type:'post',
            data:$form.serialize(),
            url:siteURL+'/funds/donate-amount',
            success:function(resp){
                $(".list-item-main").css('z-index','99999');
                $(".div-table-second-cell").css('z-index','99999');
                $("#loadingDiv").hide();
                //$("#qrCode").attr('src',resp.qrcode);
                if(resp.success){
                    bootbox.alert({
                        title : 'To pay send Zcash to the address below',
                        message: '<div><div>To pay send Zcash to the address below</div><div class="form-control"><label id="zcash_address_info" data-address="'+resp.address+'" data-fundID="'+resp.fundID+'" data-user_transaction_id="'+resp.user_transaction_id+'">'+resp.address+'</label></div><div style="text-align:center;padding-top:15px;"><img src="'+resp.qrcode+'" /></div></div>',
                        className: 'bb-alternate-modal',
                        callback: function(){ 
                            window.location.reload(true);
                        }
                    });
                    check_zcash_payment();
                }else{
                    console.log("Error Response  -> ",resp);
                    toastr['error'](resp['message'], '');
                }
            }
        });
    }

    /**
     * Checking for received donation to above Zcash address
     * If payment made in above address then 
     * Checking payment notification recived or not
     * Zcash
     */
    function check_zcash_payment(){
        $.ajax({
            type:'get',
            data:{'zcash_address':$("#zcash_address_info").attr('data-address'),'fundID':$("#zcash_address_info").attr('data-fundID'),'user_transaction_id':$("#zcash_address_info").attr('data-user_transaction_id')},
            url:siteURL+'/zcash/check_zcash_payment',
            dataType:'json',
            success:function(resp){
                if(resp.success){
                    if(resp.success_url){
                        window.location.href = resp.success_url;
                    }
                }else{
                    setTimeout(function(){
                        check_zcash_payment();
                    },1000)
                }
            }
        })
    }

    /*$('.new_cc_submit').on('click',function(e) {
        e.preventDefault();
        $(".remove-alert ").remove();
        var $form = $('#new-credit-card-form');
        var cardType = $.payment.cardType($('.cc-number').val());
        $('.cc-number').toggleInputError(!$.payment.validateCardNumber($('.cc-number').val()));
        //$('.cc-exp').toggleInputError(!$.payment.validateCardExpiry($('.cc-exp').payment('cardExpiryVal')));
        $('[name="exp_month"]').toggleInputError(!$.payment.validateCardExpiry($("[name='exp_month']").val(),
            $("[name='exp_year']").val()));
        $('.cc-cvc').toggleInputError(!$.payment.validateCardCVC($('.cc-cvc').val(), cardType));
        $("#cc-amount").toggleInputError(!$.payment.validateAmount($('#cc-amount').val()));
        $("#cc-card-type").toggleInputError(!$.payment.validateCardType($('#cc-card-type').val()));
        $('.cc-brand').text(cardType);
        $('.validation').removeClass('text-danger text-success');
        if($('.has-error').length == 0){
            $that = $(this);
            $that.prop('disabled', true);
            $(".new_cc_btn_text").html('<span class="saving">Submitting<span>.</span><span>.</span><span>.</span></span>');
            $.ajax({
                type:'post',
                url:siteURL+'/funds/donate-amount',
                data:$form.serialize(),
                dataType:'json',
                success:function(resp){
                    if(!resp.success){
                        var html = '';
                        $.each(resp.errors,function(index,val){
                            html+="<span>"+val+"</span>";
                        })
                        var errorHTML = '<div class="remove-alert alert alert-danger">'+
                                '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+html
                            '</div>';
                        $form.prepend(errorHTML);
                        $that.prop('disabled', false);
                        $(".new_cc_btn_text").html('Submit Payment');

                    }
                    else
                    {
                        $form.find("input,select").val('');
                        var errorHTML = '<div class="remove-alert alert alert-success">'+
                            '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+
                            '<strong>Success!!!</strong> Amount donated successfully.'+
                        '</div>';
                        $form.prepend(errorHTML);
                        $that.prop('disabled', false);
                        $(".new_cc_btn_text").html('Submit Payment');
                    }
                }
            });
            return false;
            //$form.get(0).submit();
        }
    });


    $('.reuse-card').on('click',function(e) {
        $(".remove-alert ").remove();
        var $form = $('#reused-credit-card-form');
        e.preventDefault();
        var selectCard = $("[name='credit_cards']").val();
        var flag = true;
        if(selectCard == ""){
            flag=false
            $("[name='credit_cards']").css('border','1px solid #a94442');
        }
        else
            $("[name='credit_cards']").css('border','1px solid #ccc');

        var amount = $("#amount_reused_card").val();
        if($.trim(amount) == "" || parseInt(amount) <= 0){
            flag=false
            $("[id='amount_reused_card']").css('border','1px solid #a94442');
        }
        else
            $("[id='amount_reused_card']").css('border','1px solid #ccc');

        if(flag){
            $(".old_cc_btn_text").html('<span class="saving">Submitting<span>.</span><span>.</span><span>.</span></span>');
            $(this).prop('disabled', true);
            $that = $(this);
            //$form.get(0).submit();
            $.ajax({
                type:'post',
                url:siteURL+'/funds/donate-amount',
                data:$form.serialize(),
                dataType:'json',
                success:function(resp){
                    if(!resp.success){
                        var html = '';
                        $.each(resp.errors,function(index,val){
                            html+="<span>"+val+"</span>";
                        })
                        var errorHTML = '<div class="remove-alert alert alert-danger">'+
                            '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+html
                        '</div>';
                        $form.prepend(errorHTML);
                        $that.prop('disabled', false);
                        $(".old_cc_btn_text").html('Submit Payment');

                    }
                    else
                    {
                        $form.find("input,select").val('');
                        var errorHTML = '<div class="remove-alert alert alert-success">'+
                            '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+
                            '<strong>Success!!!</strong> Amount donated successfully.'+
                            '</div>';
                        $form.prepend(errorHTML);
                        $that.prop('disabled', false);
                        $(".old_cc_btn_text").html('Submit Payment');
                        window.location.reload(true);
                    }
                }
            });
            return false;
        }
    });


    $("#cc-number").on('keyup',function(){
        var cardType = $.payment.cardType($(this).val());

        if($.trim(cardType) != "" && cardType != 'null'){
            $(".card_image").html('<img src="'+url+'/'+cardType+'.png" height="30px;">');
            $("#cc-card-type").val(cardType);
        }
        else{
            $(".card_image").html('');
            $("#cc-card-type").val('');
        }

    })*/

    $("[name='amount_from_available_bal']").on('keyup keydown',function(e){
        var val = $(this).val();
        if(val < avlblamt)
            $(".availableLabel").html(avlblamt-val);
        else{
            $(".availableLabel").html(0);
            $(this).val(avlblamt);
        }
    })

    $("[name='credit_available_bal']").on('click',function(){
        var val = $(this).val();
        $(".donationDiv").hide();
        $("."+val).show();
    });

    $("#pay_now").on('click',function(){
        var amount = $("[name='amount_from_available_bal']").val();
        if($.trim(amount) == "" || (amount != avlblamt && amount > avlblamt )){
            $("[name='amount_from_available_bal']").parent('div').addClass('has-error');
            return false;
        }
    });

    //change card number on selected card
    $("[name='credit_cards']").on('change',function(){
        var val =$(this).val();
        if(val == "")
            $("[name='card_number']").val('');
        else{
            var img = '';
            var type= $(this).find(':selected').data('type');
            var last4 = $(this).find(':selected').data('last4');
            if(type =="amex")
                img = 'amex.png';
            if(type == "discover")
                img = 'discover.png';
            if(type == "MasterCard")
                img = 'mastercard.png';
            if(type == "visa")
                img =  'visa.png';
            if(type == "maestro")
                img = 'maestro.png';


            if($.trim(img) != '')
                $(".reused_card_image").html('<img src="'+siteURL+'/assets/images/'+img+'" style="height:40px;"/>');
            else
                $(".reused_card_image").html('');

            $("[name='card_number']").val('XXXX XXXX XXXX '+last4);
        }
        return false;
    });
})

