function openColorBox(smWidth, smHeight)
{ 
    smWidth = (smWidth > 0) ? smWidth : "840px";
    smHeight = (smHeight > 0) ? smHeight : "500px";
    $('.colorbox_popup').colorbox({
        //href: urls,
        width: smWidth,
        height: smHeight,
        iframe: true,        
        scrolling: true,
        title:''
    });
}

//fadeout flash message
$(".flash_message").delay(3000).slideUp(500);

 //SELECT ALL CHECKBOXES IN CHECKBOX LIST IN     USERS PERMISSION POP UP    
$('.select_all').change(function() {
    var checkboxes = $(this).closest('form').find(':checkbox');
    if($(this).is(':checked')) {
        checkboxes.prop('checked', true);
    } else {
        checkboxes.prop('checked', false);
    }
});
//Add label for required field identificaiton
/*if($("form .required").size() > 0){
   $("form").prepend('<p class="note">Fields with <span class="required">*</span> are required.</p>');
}*/

// Add required star in all form
//$(".required > label").append('<span class="required">*</span>');


/*$(document).ready(function(){
    if($.fn.datepicker){
        $.datepicker.setDefaults({maxDate: new Date()});
    }
});*/
