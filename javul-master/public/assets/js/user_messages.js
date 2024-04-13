let toast_messages = user_messages;

function getToastMessage(key){
    if(key !== ''){
        return toast_messages[key];
    }
}

function showToastMessage(msg_key){
    toastr.options = {
        "closeButton": false,//To show close button
        "timeOut": 3000,//set time limit for message
    };
    let user_msg = toast_messages[$.trim(msg_key)];
    if(!user_msg['auto-dismiss']){
        toastr.options.closeButton = true;
        toastr.options.timeOut = 0;
    }
    toastr[user_msg['type']](user_msg['text'], ''/*Title*/);

    // switch(type){
    //     case 'success':
    //         toastr['success'](toast_messages[msg_key]['text'], '');
    //         break;
    //     case 'info':
    //         toastr['info'](toast_messages[msg_key]['text'], '');
    //         break;
    //     case 'warning':
    //         toastr['warning'](toast_messages[msg_key]['text'], '');
    //         break;
    //     case 'error':
    //         toastr['error'](toast_messages[msg_key]['text'], '');
    //         break;
    //     default:
    //         break;
    // }    
}