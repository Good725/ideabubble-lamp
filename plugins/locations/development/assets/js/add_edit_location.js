$(document).ready(function() {
	/*
    jQuery.extend(jQuery.validator.messages, {
        required: "Required!"
    });

    $("#form_add_edit_location").validate();
    */

	$("#form_add_edit_location").on('submit', function()
	{
		if ( ! $(this).validationEngine('validate'))
		{
			return false;
		}
	});

    $(".btn.clear").on("click", function(){
        $(this.form).find("input, select").val("");
    });
});
