/*
 *
 *   CUSTOM FUNCTIONS JSCRIPT - Responsive Admin Theme
 *   version 1.0
 *
 */

$('.decimal').keyup(function(){
    var val = $(this).val();
    if(isNaN(val)){
         val = val.replace(/[^0-9\.]/g,'');
         if(val.split('.').length>2) 
             val =val.replace(/\.+$/,"");
    }
    $(this).val(val); 
});

function toastr_msg(type, title, message, timeout){
    setTimeout(function() {
        toastr.options = {
        closeButton: true,
        progressBar: true,
        showMethod: 'slideDown',
        timeOut: timeout
    };
    switch (type)
    {
        case "success": 
        toastr.success(message, title);
        break;
        case "info": 
        toastr.info(message, title);
        break;
        case "warning": 
        toastr.warning(message, title);
        break;
        case "error": 
        toastr.error(message, title);
        break;
    }
    }, 1000);
}
