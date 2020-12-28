$(document).ready(function(){

    $(".save_btn").click(function(){
        $("#action").val($(this).data('action'));
        $("#category_edit_form").submit();
    });

    $("#project_publish_toggle > button").click(function(){
        $("#publish").val($(this).val());
    });
});