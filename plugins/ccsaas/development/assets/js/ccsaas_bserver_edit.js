$(document).on("ready", function(){
    function submit_handler()
    {
        var data = $("#ccsaas-host-form").serializeArray();
        console.log(data);
        $("#ccsaas-host-form button").prop("disabled", true);

        $.post(
            '/api/ccsaas/edit_bserver',
            data,
            function (response) {
                location.href = '/admin/ccsaas/bservers'
            }
        );
        return false;
    }

    function delete_handler()
    {
        var data = $("#ccsaas-host-form").serializeArray();
        console.log(data);

        $("#ccsaas-host-form button").prop("disabled", true);

        $.post(
            '/api/ccsaas/delete_bserver',
            data,
            function (response) {
                location.href = '/admin/ccsaas/bservers';
            }
        );

        return false;
    }

    $("#ccsaas-host-form").on("submit", submit_handler);
    $("#ccsaas-host-form-delete-modal").on("submit", delete_handler);
});
