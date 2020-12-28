$(document).on("ready", function(){
    function init_dalm()
    {
        var hostname = $("#ccsaas-host-form-hostname").val();
        //var url = 'http://' + hostname + '/api/ccsaas/init';
        var url = '/api/ccsaas/init_dalm_remote';
        var contact_id = parseInt($("#ccsaas-host-form-contact_id").val());
        var init_attempt = 0;
        var max_attempt = 3; //wait for apache load, db init; ignore first error. second call should be without error.
        var cms_skin = $("#ccsaas-host-form-cms_skin").val();

        function create_contact_on_remote()
        {
            $.post (
                '/api/ccsaas/create_contact_on_remote',
                {
                    contact_id: contact_id,
                    hostname: hostname,
                    cms_skin: cms_skin
                },
                function (data) {
                    location.href = '/admin/ccsaas';
                }
            )
        }

        function init_call()
        {
            if (init_attempt >= max_attempt) {
                return;
            }
            ++init_attempt;
            $.ajax(
                url,
                {
                    method: 'POST',
                    data: {hostname: hostname},
                    cache: false,
                    success: function (data, status, xhr) {
                        console.log(data);
                        if (!isNaN(contact_id)) {
                            create_contact_on_remote(contact_id);
                        } else {
                            location.href = '/admin/ccsaas';
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log(status);
                        console.log(error);
                        setTimeout(init_call, 1000);
                    }
                }
            );
        }

        window.disableScreenDiv.autoHide = false;
        setTimeout(init_call, 1000);
    }

    function submit_handler()
    {
        var data = $("#ccsaas-host-form").serializeArray();
        console.log(data);
        var create = isNaN(parseInt($("#ccsaas-host-form-id").val()));
        $("#ccsaas-host-form button").prop("disabled", true);

        $.post(
            create ? '/api/ccsaas/create_website' : '/api/ccsaas/update_website',
            data,
            function (response) {
                if (create) {
                    init_dalm();
                } else {
                    location.href = '/admin/ccsaas';
                }
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
            '/api/ccsaas/delete_website',
            data,
            function (response) {
                location.href = '/admin/ccsaas';
            }
        );

        return false;
    }

    function set_contact_autocomplete()
    {
        $("#ccsaas-contact").autocomplete({
            select: function(e, ui) {
                $('#ccsaas-contact').val(ui.item.label);
                $('#ccsaas-host-form-contact_id').val(ui.item.value);
                return false;
            },

            source: function(data, callback){
                $.get("/admin/contacts3/autocomplete_contacts?user_only=1",
                    data,
                    function(response){
                        callback(response);
                    }
                );
            }
        });
    }

    $("#ccsaas-host-form").on("submit", submit_handler);
    $("#ccsaas-host-form-delete-modal").on("submit", delete_handler);
    set_contact_autocomplete();
});
