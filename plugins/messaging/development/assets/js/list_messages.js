var messaging_ns = {};

function prepare_message_list() {
    var $table = $('#list_messages_table');

    // Server-side datatable
    $table.ready(function () {
        var ajax_source = '/admin/messaging/ajax_get_datatable';
        var settings = {
            "bAutoWidth": true,
            "sPaginationType": "bootstrap",
            "bSearchable": true,
            "fnServerParams": function (aoData) {
                aoData.push({"name": "use_columns", "value": document.getElementById('table_use_columns').value});
                aoData.push({"name": "parameters", "value": document.getElementById('table_parameters').value});
            },
            /*
             "aoColumnDefs": [
             { "aTargets": [0], "bSortable": false, "bSearchable": false }
             ],
             */
            "fnCreatedRow": function (row) {
                // Add ID data attribute to the table row
                row.setAttribute('data-id', $(row).find('[data-id]').data('id'));

                // Add a class to the table row to flag messages as read
                if ($(row).find('[data-read]').data('read') == 0) {
                    $(row).addClass('messaging_tr_unread');
                }
            }
        };
        var drawback_settings = {
            draw_callback: function () {
                $table.find('[data-toggle="tooltip"]').tooltip();
                $table.find('[data-toggle="popover"]').popover({trigger: 'focus'});
            }
        };
        $table.ib_serverSideTable(ajax_source, settings, drawback_settings);
    });

    // Search by individual columns
    $table.find('.search_init').on('change', function () {
        $table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this));
    });

    function send_outbox(button) {
        var message_id = $(button).data("message_id");
        $.post(
            '/admin/messaging/send_start',
            {outbox_send: 1, message_id: message_id},
            function (response) {
                window.location.reload();
            }
        );
        return false;
    }

    // When a table row is clicked, open the corresponding message
    $table.on('click', 'tbody tr :not(a):not(:input):not(label):not(td:first-child):not(td:last-child)', function (ev) {
        if ($(ev.target).hasClass("send_outbox")) {
            send_outbox(ev.target);
            return false;
        }
        // If the clicked element is a link or form element, don't open the message. Let the link/form element do its own thing.
        if (!$(ev.target).parents('a, label, :input')[0]) {
            var $tr = $(this).parents('tr');
            var $link = $tr.find('.message_details_link');
            var url = $link[0].href;

            // If the message is a draft
            if ($link.hasClass('view_draft')) {
                messaging_view_draft($tr.data('id'));
            }
            // User is attempting to open the link in a new tab
            else if (ev.ctrlKey || ev.shiftKey || ev.metaKey || (ev.button && ev.button == 1)) {
                var new_tab = window.open(url, '_blank');
                new_tab.focus();
            }
            // Open the link in the current tab
            else {
                location.href = url;
            }
        }
    });

    $table.on('click', '[data-toggle="popover"]', function (ev) {
        ev.preventDefault();
    });

    $('.send-email-to, .send-email-cc, .send-email-bcc').on("keypress", function (e) {
        if (e.keyCode == 13) { //enter=13
            var x_details = this.id.replace("send-email-", "");
            messaging_target_add(
                $(this).find('\+.contact-list-labels'),
                "email_recipient",
                {
                    value: this.value,
                    label: this.value,
                    category: "EMAIL",
                    ask_input: true
                },
                {x_details: x_details}
            );
            this.value = "";
            return false;
        }
    });

    $('.send-email-to').autocomplete({
        source: function (data, callback) {
            data.driver = 'email';
            $.get('/admin/messaging/to_autocomplete', data, function (response) {
                callback(response);
                $(".ui-helper-hidden-accessible").addClass("sr-only");
                $(".ui-autocomplete").css("max-height", "300px").css("overflow", "auto");
            });
        },
        select: function (event, ui) {
            event.preventDefault();
            messaging_target_add($(this).find('\+.contact-list-labels'), "email_recipient", ui.item, {x_details: "to"});
            this.value = '';
        }
    });

    $('.send-email-cc').autocomplete({
        source: function (data, callback) {
            data.driver = 'email';
            $.get('/admin/messaging/to_autocomplete', data, function (response) {
                callback(response);
                $(".ui-helper-hidden-accessible").addClass("sr-only");
                $(".ui-autocomplete").css("max-height", "300px").css("overflow", "auto");
            });
        },
        select: function (event, ui) {
            event.preventDefault();
            messaging_target_add($(this).find('\+.contact-list-labels'), "email_recipient", ui.item, {x_details: "cc"});
            this.value = '';
        }
    });

    $('.send-email-bcc').autocomplete({
        source: function (data, callback) {
            data.driver = 'email';
            $.get('/admin/messaging/to_autocomplete', data, function (response) {
                callback(response);
                $(".ui-helper-hidden-accessible").addClass("sr-only");
                $(".ui-autocomplete").css("max-height", "300px").css("overflow", "auto");
            });
        },
        select: function (event, ui) {
            event.preventDefault();
            messaging_target_add($(this).find('\+.contact-list-labels'), "email_recipient", ui.item, {x_details: "bcc"});
            this.value = '';
        }
    });

    $('.send-sms-to').autocomplete({
        source: function (data, callback) {
            data.driver = 'sms';
            $.get('/admin/messaging/to_autocomplete/', data, function (response) {
                callback(response);
                $(".ui-helper-hidden-accessible").addClass("sr-only");
                $(".ui-autocomplete").css("max-height", "300px").css("overflow", "auto");
            });
        },
        select: function (event, ui) {
            event.preventDefault();
            messaging_target_add($(this).find('\+.contact-list-labels'), "sms_recipient", ui.item, {});
            this.value = '';
        }
    });

    $('.send-dashboard-to').autocomplete({
        source: function (data, callback) {
            data.driver = 'dashboard';
            $.get('/admin/messaging/to_autocomplete', data, function (response) {
                callback(response);
                $(".ui-helper-hidden-accessible").addClass("sr-only");
                $(".ui-autocomplete").css("max-height", "300px").css("overflow", "auto");
            });
        },
        select: function (event, ui) {
            event.preventDefault();
            messaging_target_add($(this).find('\+.contact-list-labels'), "dashboard_recipient", ui.item, {});
            this.value = '';
        }
    });

    $('.send-sms-to').on("keypress", function (e) {
        if (e.keyCode == 13) { //enter=13
            messaging_target_add(
                $(this).find('\+.contact-list-labels'),
                "sms_recipient",
                {
                    value: this.value,
                    label: this.value,
                    category: "MOBILE",
                    ask_input: true
                },
                {}
            );
            this.value = "";
            return false;
        }
    });

    // When the "Save as Draft" modal appears, hide the "Send" modal behind the blackout
    $('#save-message-draft-modal')
        .on('show.bs.modal', function () {
            $('.send-message-modal').css('z-index', 1);
        })
        .on('hide.bs.modal', function () {
            $('.send-message-modal').css('z-index', '');
        });

    $('#save-message-draft-no').on('click', function () {
        $('.send-message-modal').modal('hide');
    });
    $('#save-message-draft-yes').on('click', function () {
        $('.send-message-modal:visible').find('[type="submit"][value="save"]').click();
    });

    $('.send-message-form').find('button[type=reset]').on('click', function (e) {
        e.stopPropagation();
        messaging_reset_send_dialog();
    });

    // Bulk mark messages as read/unread
    $('#mark-messages-as-read, #mark-messages-as-unread').on('click', function (ev) {
        ev.preventDefault();

        // Get the IDs of all selected messages
        var message_ids = [];
        $table.find('.checkbox-tick-label [type="checkbox"]:checked').each(function () {
            message_ids.push(this.getAttribute('data-id'));
        });

        // 1 = mark as read, 0 = mark as unread
        var read = (this.id != 'mark-messages-as-unread') ? 1 : 0;

        if (message_ids.length == 0) {
            // None selected -> show message
            $('#no-messages-selected-modal').modal();
        } else {
            // Flag messages as read/unread server side
            $.post('/admin/messaging/ajax_bulk_mark_as_read/', {ids: message_ids, read: read}).done(function () {
                $table.find('.search_init').trigger('change'); // force the table to refresh, after the update
            });
        }
    });

    // Bulk delete messages
    $('#delete-messages-modal-btn').on('click', function () {
        // Count the selected messages
        var number_selected = $table.find('.checkbox-tick-label [type="checkbox"]:checked').length;

        if (number_selected < 1) {
            // Alert the user if they have not selected any messages
            $('#no-messages-selected-modal').modal();
        } else {
            // Show the confirmation modal, which will mention the number of items selected
            $('#delete-message-modal-amount').html(number_selected);
            $('#delete-messages-modal').modal();
        }
    });

    // Confirm bulk delete
    $('#delete-messages-modal-confirm').on('click', function () {
        // Get the IDs of all selected messages
        var message_ids = [];
        $table.find('.checkbox-tick-label [type="checkbox"]:checked').each(function () {
            message_ids.push(this.getAttribute('data-id'));
        });

        // Flag messages as deleted, server-side
        $.post('/admin/messaging/ajax_bulk_delete/', {ids: message_ids}).done(function (results) {

            $table.find('.search_init').trigger('change'); // Force the table to refresh, after the update
            $('#list-messages-alert-area').html(results);  // Display success/failure messages
            $('#delete-messages-modal').modal('hide');     // Dismiss the modal
        });
    });

    // Toggle starred
    $table.on('change', '.toggle_starred', function () {
        var id = $(this).parents('tr').data('id');
        var starred = this.checked ? 1 : 0;
        $.ajax('/admin/messaging/ajax_toggle_starred/' + id + '?is_starred=' + starred).done(function () {
        });
    });


    $(".send-message-form").find(".modal-footer button[type=submit]").off("click");
    $(".send-message-form").find(".modal-footer button[type=submit]").on("click", function (e) {
        $(this).parents(".send-message-form").find("[name=operation]").val(this.value);
    });

    $(document).on('click', '.messaging-popout-send', function (ev) {
        ev.preventDefault();
        var $form = $(this).parents('.send-message-form');
        $form.find('[name="operation"]').val('send');
        $form.submit();
    });

    $(document).on('click', '.messaging-popout-save_as_draft', function () {
        var $form = $(this).parents('.send-message-form');
        $form.find('[name="operation"]').val('save');
        $form.submit();
    });

    $("#send-email-attachments-add-button").on("click", function () {
        var index = $("#send-email-attachments-list").children().length;
        $("#send-email-attachments-list").append(
            '<div>' +
            '<label>File Id:</label>' +
            '<input type="text" name="attachment[' + index + '][file_id]" value="" />' +
            '</div>'
        );
    });
}

messaging_ns.message_sending = false;
window.messaging_submit_handler = function (ev) {
    ev.preventDefault();
    ev.stopPropagation();
    var $form = $(this);
    var form_data = $form.serializeArray();
    var data = {};
    var operation = decodeURIComponent($form.serialize()).match(/operation=([^&]*)/)[1];
    var attachments = [];
    var attachment, src, $file_id;

    // Only continue if a message is not currently being sent
    if (!messaging_ns.message_sending) {
        var $form = $(this);

        // Put the CKEditor data into the original field before getting the form data
        $form.find('.ckeditor, .ckeditor-email').each(function () {
            CKEDITOR.instances[this.id].updateElement();
        });

        var form_data = $form.serializeArray();
        var data = {};
        var operation = decodeURIComponent($form.serialize()).match(/operation=([^&]*)/)[1];
        var attachments = [];
        var attachment, src, $file_id;

        for (var i = 0; i < form_data.length; i++) {
            if (form_data[i]['name'].indexOf("[]") != -1) {
                if (!data[form_data[i]['name']]) {
                    data[form_data[i]['name']] = [];
                }
                data[form_data[i]['name']].push(form_data[i]['value']);
            } else {
                data[form_data[i]['name']] = form_data[i]['value'];
            }
        }

        $('#messaging-sidebar-attachments-list').find('tbody tr').each(function () {
            attachment = {name: $(this).find('.messaging-sidebar-attachment-name').text()};
            $file_id = $(this).find('.messaging-sidebar-attachment-file_id');

            if ($file_id && $file_id.val()) {
                attachment.file_id = $file_id.val();
            } else {
                attachment.path = $(this).find('.messaging-sidebar-attachment-icon').attr('data-src').replace('/_thumbs_cms/', '/');
            }

            attachments.push(attachment);
        });

        if (attachments.length) {
            data.attachment = attachments;
        }

        if (operation == 'send') {
            if ($form.find(".contact-list-labels .label").length == 0) {
                alert("You have not added any recipients");
                $("input[name*='[to]']").focus();
                return false;
            }

            var $subject = $(this).find("input[name*='[subject]']");
            if ($subject.length && $subject.val() == "") {
                alert("Please enter a subject");
                $subject.focus();
                return false;
            }

            var $message = $(this).find("textarea[name*='[message]']");
            var $page_id = $(this).find("textarea[name*='[page_id]']");
            if ($message.length && $message.val() == "" && $page_id.val() == "") {
                alert("Please enter message");
                $message.focus();
                return false;
            }
        }

        messaging_ns.message_sending = true;
        $.post('/admin/messaging/ajax_send_message', data)
            .done(function (result) {
                messaging_ns.message_sending = false;
                var $table = $('#list_messages_table');
                // Force the table to refresh
                $table.find('.search_init').trigger('change');

                // Display message
                if (result.message != '') {
                    if ($('#list-messages-alert-area').length > 0) {
                        $('#list-messages-alert-area').add_alert(result.message, (result.error ? 'error' : 'success') + ' popup_box');
                        // If a message has been composed from selecting the contact, close the messaging popup menu
                        if ($('#messaging-sidebar').data('hide_popup_after_send') === true) {
                            $('[data-popup-close]').trigger('click');
                            $('#messaging-sidebar').data('hide_popup_after_send', false);
                        }
                        remove_popbox();
                    } else {
                        alert(result.message);
                    }
                }

                var $draft_prompt = $('#save-message-draft-modal');

                if (!result.id && (operation == 'save' || operation == 'save_and_exit')) {
                    $('#list-messages-alert-area').add_alert('Message not saved', 'warning popup_box');
                    remove_popbox();
                }

                if (operation == 'save' && !$draft_prompt.is(':visible') && result.id) {
                    // Keep modal open and set the ID field
                    $form.find('[name="message_id"]').val(result.id);
                } else {
                    // Dismiss the modal
                    $form.parents('.send-message-modal').modal('hide');
                    $draft_prompt.modal('hide');
                }

                $('#messaging-compose-search').hide();

                $('.messaging-sidebar-open_list[data-name="sent"]:visible').click();
            })
            .fail(function () {
                messaging_ns.message_sending = false;
                $('#list-messages-alert-area').add_alert('Internal error', 'danger popup_box');
                remove_popbox();
            });
    }

    return false;
};

$(document).on('submit', '.send-message-form', messaging_submit_handler);

var messages_contact_editor = {
    setup: function (container) {
        prepare_message_list();
        $('.send_email_contact').off('click').on('click', function () {

            var $popout = $('#messaging-sidebar');
            var container;

            $('a[href="#contact-extention-messages-tab"]').click();
            $('#send-email-to-contact-list').html('');

            // If the sidebar popout is enabled use it, otherwise use the modal
            if ($popout.length) {
                container = $popout.find('#send-email-to-contact-list');

                $('.message-wrapper').css('top', 0);
                $popout.removeClass('hidden').show().trigger(':ib-popup-open');
                $('.detail-btn[rel="send-email"]').click();
            } else {
                container = '#send-email-to-contact-list';
                $('#send-message-modal-email').modal();
            }

            messaging_target_add(
                container,
                'email_recipient',
                {
                    value: $('#id').val(),
                    label: $('[name=first_name]').val() + ' ' + $('[name=last_name]').val(),
                    category: 'CMS_CONTACT',
                    ask_input: false
                },
                {}
            );
        });
    },

    validate: function (container) {

    }
};

$(document).ready(function () {
    if (window.contact_editor) {
        window.contact_editor.extensions.push(messages_contact_editor);
    }

    prepare_message_list();
});

function messaging_view_draft(draft_id) {
    $.get('/admin/messaging/ajax_message_data?id=' + draft_id + '&rand=' + Math.random(), function (data) {
        messaging_reset_send_dialog();
        // The modal's form fields get reset every time it opens, so the fields are set after it opens to avoid that
        // The negative z-index to ensure the modal stays hidden until its fields have been populated
        var $modal = $("#send-message-modal-" + data.driver.driver);
        $modal.css('z-index', -1).modal();

        var $form = $("#send-" + data.driver.driver + "-form");
        $form.find("[name=message_id]").val(draft_id);
        $form.find("[name*='[subject]']").val(data.subject);
        $form.find("[name*='[message]']").val(data.message);
        $form.find("[name*='[from]']").val(data.sender);
        var driver = data.driver.driver;
        for (var i in data.targets) {
            var t = data.targets[i];
            var item = {};
            item.value = t.target;
            item.category = t.target_type;
            item.label = t.target_d;
            item.db_id = t.id;
            var x_params = {};
            if (t.sms) {
                item.sms = t.sms;
                x_params.label_more = "sms";
            }
            if (t.email) {
                item.email = t.email;
                x_params.label_more = "email";
            }
            if (t.x_details) {
                x_params.x_details = t.x_details;
            }

            var container = "";
            if (driver == "dashboard") {
                container = "#send-dashboard-to-contact-list";
            } else if (driver == "sms") {
                container = "#send-sms-to-contact-list";
            } else {
                container = "#send-email-" + t.x_details + "-contact-list";
                if (t.x_details == "cc") {
                    $("#send-email-cc-wrapper")[0].style.display = 'block';
                }
                if (t.x_details == "bcc") {
                    $("#send-email-bcc-wrapper")[0].style.display = 'block';
                }
            }
            messaging_target_add(container, driver + "_recipient", item, x_params);
        }
        $modal.css('z-index', '');
    });
}


function messaging_reset_send_dialog() {
    $('.contact-list-labels .label.label-primary').remove();
    $("#send-email-cc-wrapper")[0].style.display = 'none';
    $("#send-email-bcc-wrapper")[0].style.display = 'none';
    $(".send-message-modal input[type=text], textarea").val('');
    $(".send-message-modal select").prop('selectedIndex', -1);
}

$(".send-message-modal").on("show.bs.modal", function () {
    messaging_reset_send_dialog();
});
