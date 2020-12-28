$(document).ready(function(){
    CKEDITOR.replace(
        'item_content',
        {
            // CUSTOM Toolbar
            // More Info @ http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Toolbar
            /*toolbar :
             [
             ['Source', '-', 'NewPage'],
             ['Font', 'FontSize'],
             ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat'],
             ['TextColor','BGColor'],
             ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
             ['NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
             ['Image', 'Table'],
             ['Link', 'Unlink', 'Anchor'],
             ['Maximize', 'ShowBlocks']
             ],*/
            width  : '100%',
            height : '300px'
        }
    );

    $("#btn_delete").click(function(){
        $('#confirm_delete').modal();
    })
    $("#btn_delete_yes").click(function(){
		$('#editor_action').val('delete');
		$("#editor_redirect").val("/admin/testimonials");
		$("#form_testimonials_story_add_edit").submit();
    })

    $("#btn_save").click(function(){
        if (validate_form()) {
            $("#editor_redirect").val("/admin/testimonials/add_edit_item");
		    $("#form_testimonials_story_add_edit").submit();
        }
    })
	$("#btn_save_exit").click(function(){
        if (validate_form()) {
		    $("#editor_redirect").val("/admin/testimonials");
		    $("#form_testimonials_story_add_edit").submit();
        }
	})


    $(document).scroll(function(e){
        actionBarScroller();
    });

    $(document).resize(function(){
        actionBarScroller();
    });

    actionBarScroller();
});


function actionBarScroller() {
    var viewportHeight = window.innerHeight ? window.innerHeight : $(window).height();
    var fromTop = $(window).scrollTop();
    var howFar = viewportHeight + fromTop;
    var pos = $('.floating-nav-marker').position().top;
    stuff = 'AP:'+pos+' PB:'+howFar;
    if (pos > howFar) {
        $('#ActionMenu').addClass("floatingMenu");
        $('#ActionMenu').removeClass("fixedMenu");
    } else {
        $('#ActionMenu').addClass("fixedMenu");
        $('#ActionMenu').removeClass("floatingMenu");
    }
}


function imageChange(img_selector_id, preview_id) {
	var selected_element = $('#'+img_selector_id+' :selected');
	var selected_image = selected_element.val();
	var preview_element = $('#' + (preview_id || 'imagePreview'));
	if(selected_image != 0){
		var image_thumb = selected_element.data('thumb');
        preview_element.html('<img src="'+image_thumb+'" alt="'+selected_image+'"/>');
	}
	else if(selected_image == 0) preview_element.html('');
}//end of function

function validate_form() {
    var ok = true;

    if ( ($("#item_title").val()).length <= 0 || $("#item_category_id").val() == '0' ) {
        $('#validation_failed').modal();

        $('#btn_review').click(function() {
            $('#validation_failed').modal('hide');
        })

        ok = false;
    }

    return ok;
}