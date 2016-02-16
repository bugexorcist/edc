jQuery(function() {
    jQuery('#set_current_as_old').change(function(){
        if(jQuery(this).attr('checked')){
            jQuery('#old_domain').val(document.domain);
            if(jQuery('#new_domain').val()==document.domain) {
                jQuery('#new_domain').val('');
            }
            jQuery('#set_current_as_new').removeAttr('checked');
        } else {
            if(jQuery('#old_domain').val()==document.domain) {
                jQuery('#old_domain').val('');
            }
        }
    });
    jQuery('#set_current_as_new').change(function(){
        if(jQuery(this).attr('checked')){
            jQuery('#new_domain').val(document.domain);
            if(jQuery('#old_domain').val()==document.domain) {
                jQuery('#old_domain').val('');
            }
            jQuery('#set_current_as_old').removeAttr('checked');
        } else {
            if(jQuery('#new_domain').val()==document.domain) {
                jQuery('#new_domain').val('');
            }
        }
    });
});