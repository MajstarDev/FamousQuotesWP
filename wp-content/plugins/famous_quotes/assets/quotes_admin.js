jQuery( document ).ready(function() {
   jQuery('#quote_form').on('submit', function(e){
	var field = ["quote_author","quote_text"];
	for (var i = 0; i < field.length; i++) {
		var f = jQuery('[name=' + field[i] + ']');
		if (f.val().trim() == '') {
			jQuery(f).css('border', '2px solid red');
			jQuery('.fmq-form-error').text('All fields are required');
			jQuery(f).focus();
			return false;
		}
		else {
			jQuery(f).css('border', 'none');
		}
    }
	return true;
   });

});

