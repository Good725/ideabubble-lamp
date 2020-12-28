$(document).ready(function() {
    jQuery.extend(jQuery.validator.messages, {
        required: "Required!"
    });

    $("#form_add_zone").validate();


    // $("#btn_delete").click(function (ev) {
    //     ev.preventDefault();
    //     var id = $(this).data("id");
    //     $("#btn_delete_yes").data('id', id);
    //     $("#confirm_delete").modal();
    // });
    // $("#btn_delete_yes").click(function (ev) {
    //     ev.preventDefault();
    //     var id = $(this).data('id');
    //     $.post('/admin/courses/remove_year', {id: id}, function (data) {
    //         if (data.redirect !== '' || data.redirect !== undefined) {
    //             window.location = data.redirect;
    //         } else {
    //             var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
    //             $("#main").prepend(smg);
    //         }
    //         $("#confirm_delete").modal('hide');
    //
    //     }, "json");
    // });

    $('.save_button').click(function(){
        $("#redirect").val(this.getAttribute("data-redirect"));
        $("#form_add_zone").submit();
    });

    $('.cancel_button').click(function(){
        window.location = "/admin/courses/zones";
    });
});