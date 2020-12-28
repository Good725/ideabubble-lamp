$(document).ready(function() {
    jQuery.extend(jQuery.validator.messages, {
        required: "Required!"
    });

    $("#form_add_edit_type").validate();
    $("#publish_yes").click(function(ev){
        ev.preventDefault();
        $("#publish").val('1');
    });
    $("#publish_no").click(function(ev){
        ev.preventDefault();
        $("#publish").val('0');
    });
    $("#btn_delete").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data("id");
        $("#btn_delete_yes").data('id', id);
        $("#confirm_delete").modal();
    });
    $("#btn_delete_yes").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $.post('/admin/courses/remove_type', {id: id}, function (data) {
            if (data.redirect !== '' || data.redirect !== undefined) {
                window.location = data.redirect;
            } else {
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }
            $("#confirm_delete").modal('hide');

        }, "json");
    });

    $('.save_button').click(function(){
        $("#redirect").val(this.getAttribute("data-redirect"));
        $("#form_add_edit_type").submit();
    });
});