$(document).ready(setup_contact_list_table);
window.contact_editor = {extensions: []};

function setup_contacts_list_datatable()
{
    // Server-side datatable
    var $table = $('#list_contacts_table');
    var ajax_source = '/admin/contacts2/list_datatable';
    $table.ready(function() {
            var settings = {
                "aLengthMenu"     : [10, 25, 50, 100],
                "aaSorting"       : [[ 5, "desc" ]],
                "sPaginationType" : "bootstrap",
                "aoColumnDefs"    : [{
                    "aTargets": [1],
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol)
                    {
                        // Add data attribute, with the contact ID to each row
                        $(nTd).parent().attr({'data-id': oData[0]});
                    }
                }]
            };
        $table.ib_serverSideTable(ajax_source, settings);
    });

    // Search by individual columns
    $table.find('.search_init').on('change', function () {
        $table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this) );
    });

}

function setup_contact_list_table()
{
    setup_contacts_list_datatable();

    var item_selected;

    // Listener to toggle contact publish
    $(".publish").click(function() {
        // Save the object (to be used later)
        item_selected = $(this);

        // Remove all the alerts, preventing stacking
        $(".alert").remove();

        $.post('/admin/contacts2/ajax_toggle_publish/' + $(item_selected).data('id'))
            .done(function(r) {
                var msg;

                if (r == '1') {
                    img = $(item_selected).children();

                    // Update icon
                    if(img.hasClass('icon-ok'))
                    {
                        img.removeClass('icon-ok');
                        img.addClass('icon-remove');
                    } else {
                        img.removeClass('icon-remove');
                        img.addClass('icon-ok');
                    }

                    // Set the message
                    msg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><strong>Success: </strong>Contact successfully updated.</div>';
                } else {
                    msg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Warning: </strong>Unable to complete the requested operation.</div>';
                }

                // Show a notification
                $("#main").prepend(msg);
            })
            .fail(function() {
                $("#main").prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Warning: </strong>Cannot connect with the server.</div>');
            })
    });

    // Listener to show the confirmation window when the user wants to delete a contact
    $("#list_contacts_table tbody").on("click", ".delete, .flaticon-remove-button", function() {
        // Save the object (to be used later)
        item_selected = $(this).parents("tr");

        // Show the confirmation window
        $('#confirm_delete').modal();
        return false;
    });

    // Listener to delete a contact
    $("#btn_delete_yes").click(function() {
        // Hide the confirmation window
        $('#confirm_delete').modal('hide');

        // Remove all the alerts, preventing stacking
        $(".alert").remove();

        $.post('/admin/contacts2/ajax_delete/' + $(item_selected).data('id'))
            .done(function(r) {
                var msg;

                if (r == '1') {
                    // Remove the row // TODO: Use API function to remove the row (see http://www.datatables.net/)
                    item_selected.parents("tr").remove();

                    // Set the message
                    msg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><strong>Success: </strong>Contact successfully deleted.</div>';
                } else {
                    msg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Warning: </strong>Unable to complete the requested operation.</div>';
                }

                // Show a notification
                $("#main").prepend(msg);

                item_selected = null;
                setup_contacts_list_datatable();
            })
            .fail(function() {
                $("#main").prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Warning: </strong>Cannot connect with the server.</div>');
            })
    });

    $("#list_contacts_table tbody").on(
        "click",
        "tr",
        function(){
			$(this).parents('tbody').find('> .selected').removeClass('selected');
			$(this).addClass('selected');
            var id = $(this).data("id");
            var last_modification = $(this).find(">td:nth-child(6)").html();
            if (parseInt(id)) {
                load_contact(id, last_modification);
            }
        }
    );

    $("#contact-list-add").on(
        "click",
        function(e){
            load_contact();
            return false;
        }
    );
}

function load_contact(id, last_modification, extra_params, callback)
{
    var url = "/admin/contacts2/edit/";
    if (parseInt(id)) {
        url += id;
    }
    if (last_modification) {
        url += '?' + last_modification;
    }
    if (extra_params) {
        url += extra_params;
    }
    $.ajax(
        url,
        {
            method: 'GET',
            cache: last_modification ? true : false,
            success: function (response) {
                $("#contacts2-editor-container").html(response);
                setup_contact_editor($("#contacts2-editor-container"));
                for (var i = 0 ; i < window.contact_editor.extensions.length ; ++i ){
                    try {
                        window.contact_editor.extensions[i].setup($("#contacts2-editor-container"));
                    } catch (exc) {
                        console.log(exc);
                    }
                }
                if (callback) {
                    callback(response);
                }

				$('.contact_messages_table').dataTable();
				document.getElementById('contact-editor').scrollIntoView();
            },
            error: function() {

            }
        }
    );
}

function setup_contact_editor($container)
{
    $container.find(".add_note.contact").on("click", function(){
        var contact_id = $container.find("#form_add_edit_contact [name=id]").val();
        display_note_editor(
            "Contact",
            contact_id,
            "",
            $container,
            "#contact-notes-tab .table.notes"
        );
    });

    var save_exit = false;
    //$container.find(".modal").modal('hide');
    // Listener for the submission of the form
    $container.find("#form_add_edit_contact").submit(function(e) {
        if (validate_form(this)){
            var data = $(this).serialize();

            $.ajax(
                "/admin/contacts2/save/",
                {
                    data: data,
                    method: 'POST',
                    cache: false,
                    success: function (response) {
						$("#contacts2-editor-container").html(response);
                        if (save_exit) {
                            window.disableScreenDiv.style.visibility = "visible";
                            window.location = '/admin/contacts2';
                        } else {
                            setup_contact_editor($("#contacts2-editor-container"));
                            var $table = $('#list_contacts_table');
                            if ($table.length > 0) {
                                setup_contacts_list_datatable();

                            }
                        }
                    },
                    error: function() {

                    }
                }
            );
        }
        return false;
    });

    $container.find("#form_add_edit_contact [data-action=save_exit]").on("click", function(e) {
        save_exit = true;
    });

    $container.find("#contact_related_auto").autocomplete({
        select: function(e, ui) {
            $('#contact_related_auto').val(ui.item.label);
            $('#contact_related_auto').data("contact-id", ui.item.value);
            return false;
        },

        source: function(data, callback){
            $.get("/admin/contacts2/autocomplete_list",
                data,
                function(response){
                    callback(response);
                });
        }
    });

    $container.find("#contact_relation_add").on("click", function(){
        var contact_id = $('#contact_related_auto').data("contact-id");
        var relation_id = $("#contact_relation_id").val();
        if (relation_id > 0 && contact_id > 0) {
            $("#contact_has_relations").append(
                '<li>' +
                '<input type="hidden" name="has_relation[contact_2_id][]" value="' + contact_id  + '">' +
                '<input type="hidden" name="has_relation[relation_id][]" value="' + relation_id  + '">' +
                '<a href="/admin/contacts2/edit/' + contact_id + '">' + $('#contact_related_auto').val() + '</a> &nbsp; ' +
                '<span onclick="$(this).parent().remove()">remove</span>' +
                '</li>'
            );
            $("#contact_relation_id").val("");
            $('#contact_related_auto').data("contact-id", "");
            $('#contact_related_auto').val("");
        }
    });

    $container.find("#contact-permission-user").autocomplete({
        select: function(e, ui) {
            $('#contact-permission-user').val(ui.item.label);
            $('#contact-permission-user').data("user-id", ui.item.value);
            return false;
        },

        source: function(data, callback){
            $.get("/admin/contacts2/autocomplete_permission_list",
                data,
                function(response){
                    callback(response);
                });
        }
    });

    $container.find("#contact-permission-add").on("click", function(){
        var user_id = $("#contact-permission-user").data("user-id");
        var email = $("#contact-permission-user").val();
        $("#contact-permitted-users tbody").append(

            '<tr>' +
            '<td>' + user_id + '</td>' +
            '<td>' + email + '</td>' +
            '<td>' +
            '<input type="hidden" name="has_permission_user_id[]" value="' + user_id + '"/>' +
            '<button class="btn" type="button" onclick="$(this).parent().parent().remove();">remove</button>' +
            '</td>' +
            '</tr>'
        );
        $("#contact-permission-user").data("user-id", "");
        $("#contact-permission-user").val("");
    });

    $container.find(".contact-link").on(
        "click",
        function(){
            var id = $(this).data("id");
            if (parseInt(id)) {
                load_contact(id);
                return false;
            }
        }
    );

    $container.find(".datepicker.date").datetimepicker({timepicker: false, format: 'Y-m-d',});

    $container.find("[name=first_name], [name=last_name]").on('keypress', function(ev) {
        var input = this;

        if (input.capTimeout) {
            clearTimeout(input.capTimeout);
        }
        input.capTimeout = setTimeout(function(){
            if ($(this).parents("div").find(".enforce_ucfirst_toggle").prop("checked")) {
                input.value = input.value.toLowerCase();
                // Capitalise the first letter in each word
                input.value = input.value.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1);});
                // Capitalise the letter after "Mc" or "O'". e.g. "McMahon" and "O'Mahony"
                input.value = input.value.replace(/(Mc|O')([a-z])/g, function(txt, $1, $2){return $1+$2.toUpperCase();});
            }
        }, 500);
    });

    $container.find('.enforce_ucfirst_toggle', function() {
        if (this.checked) {
            $(this).parents("div").find("[name=first_name], [name=last_name]").trigger('keypress');
        }
    });

    $container.find("[name=first_name], [name=last_name]").on("blur", function(){
        var input = this;
        if ($(this).parents("div").find(".enforce_ucfirst_toggle").prop("checked")) {
            input.value = input.value.toLowerCase();
            // Capitalise the first letter in each word
            input.value = input.value.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1);});
            // Capitalise the letter after "Mc" or "O'". e.g. "McMahon" and "O'Mahony"
            input.value = input.value.replace(/(Mc|O')([a-z])/g, function(txt, $1, $2){return $1+$2.toUpperCase();});
        }
    });

    $container.on("click", ".comm-remove", function(){
        $(this).parents(".comm").remove();
    });

    $container.find(".communications button.add").on("click", function(){
        var $comm = $(this).parents(".communications");
        var $field = $comm.find(".comm-template").clone();
        var index = $comm.find(".comm-list .comm").length;

        $field.find("label").html($comm.find(".comm-add .comm_new_type option:selected").text());
        $field.find(".comm_type_index").val($comm.find(".comm-add .comm_new_type option:selected").val());
        $field.find(".comm_index").val($comm.find(".comm-add .comm_new_value").val());

        $field.find("label").attr("for", "comm_" + index);
        $field.find(".comm_type_index").attr("name", "comm[" + index + "][type_id]");
        $field.find(".comm_index").attr("name", "comm[" + index + "][value]");

        $field.removeClass("comm-template").removeClass("hidden");
        $comm.find(".comm-list").append($field);

        $comm.find(".comm-add .comm_new_value").val("");
        $comm.find(".comm-add .comm_new_type").val("");
    });

    $container.find('.upload_document').on('click', function(ev){
        $("#upload_document_modal").modal("show");
    });

    $container.find('#upload_document_modal_btn').on('click', function (ev){
        ev.preventDefault();

        if (document.getElementById('upload_document_modal_file').value) {
            var data = new FormData();
            var file = document.getElementById('upload_document_modal_file').files[0];
            data.append('file', file);
            data.append('contact_id', $container.find("[name=form_add_edit_contact] [name=id]").val());

            $.ajax(
                {
                    url         :'/admin/documents/ajax_upload_document',
                    type        : 'POST',
                    data        : data,
                    contentType : false,
                    processData : false
                }
            ).done(function (){
                    // Reload the tab with the new document and success message
                    load_documents($container.find("[name=form_add_edit_contact] [name=id]").val());
                    $('#upload_document_modal').modal('hide');

            }).fail(function (){
                    // Reload the tab with a failure notice
                    $('[href="#contact-documents-tab"]').click();
                    $('#upload_document_modal').modal('hide');
            });
        } else {
            $('#upload_document_modal').find('.alert-area').add_alert('Please choose a file', 'error');
        }
    });

    function load_documents(contact_id)
    {
        //var contact_id = $container.find("[name=form_add_edit_contact] [name=id]").val();
        $.get(
            "/admin/contacts2/get_documents",
            {
                contact_id: contact_id
            },
            function response(docs_table) {
                $('#contact-documents-tab').find('.content-area').html(docs_table);
            }
        );
    }


	$(document).on('click', '[href="#contact-attendance-tab"]', function(ev)
	{
		var contact_id = document.getElementById('contact-editor-contact_id').value;

		var data = {contact_id:contact_id};
		$('#contact-attendance-tab')
			.find('.content-area')
			.load(
			'/admin/contacts2/ajax_get_attendance/',
			data,
			function(result){
				$('#contact-attendance-tab .popinit').popover({placement: 'top', trigger: 'hover'});
				var $table = $('#contact-attendance-tab .attending_timeslots_table');
				$table.dataTable();

				$table.find('>thead tr .search_init').on('change', function (){
					$table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this) );
				});
			}
		);
	});
}

/**
 * Validate the form.
 * @return {Boolean}
 */
function validate_form(form) {
    // TODO: Validate fields (see model validation function).

    return true;
}


