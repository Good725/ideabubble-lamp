$(document).ready(function()
{
    // Server-side datatable
    var $table = $('#list_contacts_table');

    $table.ib_serverSideTable(
        '/admin/contacts3/ajax_get_datatable',
        { aaSorting: [[ 12, 'desc']] },
        { responsive: true, row_data_ids: true }
    );

    // Search by individual columns
    $table.find('.search_init').on('change', function ()
    {
        $table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this) );
    });

    // temporary, until better permanent links are set up
    var query_string     = window.location.search;
    var query_array      = query_string.replace('?','').split('&');
    var query_parameters = [];

    for (var q = 0; q < query_array.length; q++)
    {
        var q_array = query_array[q].split('=');
        query_parameters[q_array[0]] = q_array[1];
    }

    var contact_id = query_parameters['contact'];
    if (typeof contact_id != 'undefined' && contact_id != '')
    {
        select_row(document.getElementById('list_contacts_table').querySelector('[data-id="'+contact_id+'"]'));
        if (query_parameters['add_new'] && query_parameters['add_new'] == 'yes') {
            load_family_for_contact(contact_id, null, function(){
                $("#add_new_family_member_link").click();
            });
        } else {
            load_family_for_contact(contact_id);
        }
    }

    //when loaded check of document tab needs to be shown
    //todo to make intelligent for family tab as well as contact doc tab
    var documents = query_parameters['documents'];
    if(documents=='true'){
        //reload doc tab
        $('[href="#family-member-documents-tab"]').click();
    }

    // Server-side datatable
    var $htable = $('#list_hosts_table');

    $htable.ib_serverSideTable(
        '/admin/contacts3/hosts_datatable',
        { aaSorting: [[ 11, 'desc']] },
        { responsive: true, row_data_ids: true }
    );

    // Search by individual columns
    $htable.find('.search_init').on('change', function ()
    {
        $htable.dataTable().fnFilter(this.value, $htable.find('tr .search_init').index(this));
    });

    var $generic_table = $('#list_contact_generic_type_table');

    $generic_table.ib_serverSideTable(
        '/admin/contacts3/generic_contact_type_datatable',
        {"aaSorting": [[0, 'desc']],
        "fnServerData" : function (sSource, aoData, fnCallback, oSettings) {
            aoData.push({
                "name": "contact_type",
                "value": window.location.pathname.split("/").pop()
            });
            oSettings.jqXHR = $.ajax({
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback
            });
        }}, {responsive: true, row_data_ids: true}
    );

    initialize_contact_type_table();

});

$(document).ready(function () {
    // Server-side datatable
    var $table = $('#list_departments_table');

    $table.ib_serverSideTable(
        '/admin/contacts3/ajax_get_department_datatable',
        {aaSorting: [[11, 'desc']]},
        {responsive: true, row_data_ids: true}
    );

    // Search by individual columns
    $table.find('.search_init').on('change', function () {
        $table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this));
    });
});

$('#list_contacts_table, #list_hosts_table, #list_organisations_table, #list_departments_table, ' +
    '#list_contact_generic_type_table').on('click', 'tbody tr', function(ev)
{
    ev.preventDefault();
    select_row(this);
    load_family_for_contact($(this).data('id'));
});

$('#list_families_table').on('click', 'tbody tr', function(ev)
{
    ev.preventDefault();
    select_row(this);
    load_family(this.getAttribute('data-id'));
});

$(document).on('click', '#list_family_members_table tbody tr', function(ev)
{
    ev.preventDefault();
    select_row(this);
    load_contact($(this).data('id'));
});
$(document).on('click', '#add_new_family_member_link', function(ev)
{
    ev.preventDefault();
    $('#list_family_members_table').find('tr.selected').removeClass('selected');
    load_contact(null);
});

$(document).on('click', '.contact_bookings_table tbody tr', function(ev)
{
    select_row(this);
    var booking_id   = this.getAttribute('data-booking_id');
    var schedule_id  = this.getAttribute('data-schedule_id');
    var category_id  = this.getAttribute('data-category_id');
    var location_id  = this.getAttribute('data-location_id');
    var pane         = $(this).parents('.tab-pane')[0];
    var form_wrapper = $('#family-member-booking-details-tab').find('.content-area');
    window.order_table_data = {};
    var outstanding = parseFloat($(this).data("outstanding"));

    // Timestamp to make the URL different and therefore not load the cached version.
    const timestamp = new Date().dateFormat('unixtime');

    $(form_wrapper).load('/admin/bookings/add_edit_booking/'+booking_id+'?ts='+timestamp+' #add_edit_booking_wrapper', function()
    {
        var booking_form_tab_content = document.getElementById('contact_booking_form_wrapper').getElementsByClassName('tab-content')[0];
        booking_form_tab_content.style.display = 'block';
        var labels = '';
        $('#edit_family_member_booking_heading').html('<div class="edit_heading-left"><h2>Edit Booking <strong>#' + booking_id + '</strong></h2><div class="flags">' +
            '</div>' +
            '</div>');
       $('#booking_select_contact_fieldset, .booking-send-email-wrapper, #booking-form-panel-contact, #booking-delegates-list-add').hide();


       booking_form_loaded();
       $('#select_location').val(location_id);
       $('#select_category').val(category_id);
       $('#search_courses_schedules').click();

        var extra_headings = $("#add-edit-booking-extra-headings > *");
        extra_headings.remove();
        if (extra_headings.length > 0) {
            $("#family-member-booking-details-tab .edit_heading .flags").append(extra_headings);
        }
        $('#edit_family_member_booking_heading')[0].scrollIntoView();
    });
});

/**
 * Load content for the timetable tab, when the tab is clicked
 */
// Family Member
$(document).on('click', '[href="#family-member-timetable-tab"]', function(ev)
{
    var contact_id = document.getElementById('contact_id').value;

    // change actions menu to load default menu options
    const $buttons = $('#header_buttons');

    if ($buttons.length > 0) {
        $buttons.empty();
    }
    start_date = $('#timetable_from_date').val();
    end_date = $('#timetable_to_date').val();
    var data = {contact_id:contact_id,after:start_date,before:end_date, print_filter: 1};
    $('#family-member-timetable-tab').find('.content-area').load('/admin/contacts3/ajax_get_booking_timetable/',data, function(result)
    {
        $('#family-member-timetable-tab').find('.content-area .datepicker').datepicker({format: 'dd/mm/yyyy'});
        $("#student_id").val($("#contact_id").val());
        // document.getElementById('timetable_view_area').innerHTML = result;
		show_booking_calendar();
        // document.getElementById('edit_family_member_heading').scrollIntoView();
    });
});

/**
 * Load content for the timetable tab, when the tab is clicked
 */
// Family Member
$(document).on('click', '[href="#family-member-attendance-tab"]', function(ev)
{
    var contact_id = document.getElementById('contact_id').value;

    // change actions menu to load default menu options
    $('#header_buttons').empty();

    var data = {contact_id:contact_id};
    $('#family-member-attendance-tab')
        .find('.content-area')
        .load(
            '/admin/contacts3/ajax_get_attendance/',
            data,
            function(result){
                $('#family-member-attendance-tab .popinit').popover({placement: 'top', trigger: 'hover'});
                var $table = $('#family-member-attendance-tab .attending_timeslots_table');
                $table.dataTable({"aaSorting": []});

                $table.find('>thead tr .search_init').on('change', function (){
                    $table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this) );
                });
            }
        );
});
// Family
$(document).on('click', '[href="#family-timetable-tab"]', function()
{
    // change actions menu to load default menu options
    const $buttons = $('#family_header_buttons');

    if ($buttons.length > 0) {
        $buttons.empty().load('/admin/contacts3/ajax_load_default_actions/?contact=false');
    }

    var family_id = document.getElementById('family_id').value;
    $('#family-timetable-tab').find('.content-area').load('/admin/contacts3/ajax_get_booking_timetable/?family_id='+family_id, function(result)
    {
        // document.getElementById('timetable_view_area').innerHTML = result;
		show_booking_calendar();
        document.getElementById('edit_family_heading').scrollIntoView();
    });
});

$(document).on('click', '#email_timetable', function(ev) {
    ev.preventDefault();

    var $form = $(this).parents('form');
    $.ajax({
        method: $form[0].method,
        url: $form[0].action,
        data: $form.serialize()+'&email_timetable=1'
    }).done(function(data) {
        $('#booking-calendar-alert_area').add_alert(data.message, (data.success ? 'success' : 'danger')+' popup_box');
    });
});

// Load content for the messages tab, when the tab is clicked
$(document).on('click', '[href="#family-member-messages-tab"]', function(ev)
{
    var contact_id = document.getElementById('contact_id').value;
    $('#header_buttons').empty();

    $('#family-member-messages-tab').find('.content-area').load('/admin/contacts3/ajax_get_messages?contact_id='+contact_id, function() {
        $(this).find('.dataTable').attr('id', 'contact_messages_table').dataTable({aaSorting: [[$("#contact_messages_table").find("th:contains(Last Activity)").index(), 'asc']]},)
            .attr('data-table', 'contacts').attr('data-header', 'family_member');
    });
});

// Load content for the notes tab, when the tab is clicked
$(document).on('click', '[href="#family-member-notes-tab"]', function(ev)
{
    var contact_id = document.getElementById('contact_id').value;
    // change actions menu to load default menu options
    const $buttons = $('#header_buttons');

    if ($buttons.length > 0) {
        $buttons.empty().load('/admin/contacts3/ajax_load_default_actions/?tab=notes&contact=false');
    }

    $('#family-member-notes-tab').find('.content-area').load('/admin/contacts3/ajax_get_notes/contacts?id='+contact_id, function()
    {
        $(this).find('.dataTable').attr('id', 'contact_notes_table').dataTable({"aaSorting": []})
            .attr('data-table', 'contacts').attr('data-header', 'family_member');
    });
});

$(document).on('click', '[href="#family-notes-tab"]', function(ev)
{
    // change actions menu to load default menu options
    const $buttons = $('#family_header_buttons');

    if ($buttons.length > 0) {
        $buttons.empty().load('/admin/contacts3/ajax_load_default_actions/?tab=notes&contact=false');
    }

    var family_id  = document.getElementById('family_id').value;
    $('#family-notes-tab').find('.content-area').load('/admin/contacts3/ajax_get_notes/family?id='+family_id, function()
    {
        $(this).find('.dataTable').attr('id', 'family_notes_table').dataTable({"aaSorting": []})
            .attr('data-table', 'family').attr('data-header', 'family');
            remove_popbox();
    });
});

$(document).on('click', '[href="#family-todos-tab"]', function(ev)
{
    // change actions menu to load default menu options
    const $buttons = $('#family_header_buttons');

    if ($buttons.length > 0) {
        $buttons.empty().load('/admin/contacts3/ajax_load_default_actions/?tab=todos&contact=false');
    }

    var family_id  = document.getElementById('family_id').value;
    $('#family-todos-tab').find('.content-area').load('/admin/contacts3/ajax_get_todos?family_id='+family_id+'&contact_id=0', function()
    {
        $(this).find('.dataTable').attr('id', 'family_notes_table').dataTable({"aaSorting": []})
            .attr('data-table', 'family').attr('data-header', 'family');
    });
});

$(document).on('click', '[href="#family-bookings-tab"]', function(ev)
{
    if($('#family-member-booking-details-tab').length)
    {
        document.getElementById('contact_booking_form_wrapper').getElementsByClassName('tab-content')[0].style.display = 'none';
    }
    // change actions menu to load default menu options
    const $buttons = $('#family_header_buttons');

    if ($buttons.length > 0) {
        $buttons.empty();
    }

    var family_id  = document.getElementById('family_id').value;
    $('#family-bookings-tab').find('.content-area').load('/admin/contacts3/ajax_get_bookings?family_id='+family_id+'&contact_id=0', function()
    {
        $(this).find('.dataTable').attr('id', 'family_bookings_table').dataTable({"aaSorting": []});
    });
});

// Load the booking form when the bookings tab is clicked
$(document).on('click', '[href="#family-member-bookings-tab"]', function(ev, args)
{
    args = args || {};
    ev.stopPropagation();
    if($('#family-member-booking-details-tab').length)
    {
        document.getElementById('contact_booking_form_wrapper').getElementsByClassName('tab-content')[0].style.display = 'none';
    }
    // change actions menu to load default menu options
    var contact_id = $('#contact_id').val() || '';
    const $buttons = $('#header_buttons');

    if ($buttons.length > 0) {
        $buttons.empty();
    }

    var booking_id = args.booking_id || null;

    open_contact_booking_tab(booking_id, { tab_open: true });
});

$(document).on('click', '[href="#family-member-activities-tab"]', function(ev)
{
    ev.stopPropagation();
    var contact_id = $('#contact_id').val() || '';
    const $buttons = $('#header_buttons');

    if ($buttons.length > 0) {
        $buttons.empty().load('/admin/contacts3/ajax_load_default_actions/?contact=true&contact_id=' + contact_id);
    }
    open_family_member_activity_table(contact_id);
});

function open_family_member_activity_table(contact_id, callback)
{
    var params = 'family_member-contact_id=' + contact_id;
    var tab = $('#family-member-activities-tab');
    var tableId = 'family_member_activities_table';

    tab.find('.content-area').load('/admin/contacts3/ajax_get_family_member_activities?' + params, function() {
        $(this).find('.dataTable').attr('id', tableId).dataTable({"aaSorting": []});
        if (callback) {
            callback();
        }
    });
}

// Open the bookings tab and load the form of a specific booking
function open_contact_booking_tab(booking_id, args)
{
    args = args || {};
    window.order_table_data = {};
    if(Cookies.get("bookingActionButtonClicked") == 'booking_book_and_pay') {
        var tab = 'accounts';
        var send_backend_booking_emails = '1';
    } else {
        var tab = 'bookings';
        var send_backend_booking_emails = '0';
    }
    Cookies.remove("bookingActionButtonClicked");
    var $tab = $('[href="#family-member-'+tab+'-tab"]');

    var contact_id = $('#contact_id').val() || '';

    if (!contact_id) {
        contact_id = $('#family_member-contact_id').val() || '';
    }
    var new_booking_href = null;
    var autoscroll = args.autoscroll || false;
    if ($tab !== undefined && $tab.length > 0) {
       new_booking_href = ($tab[0].getAttribute('data-new_booking')) ? $tab[0].getAttribute('data-new_booking') : false;
        $tab[0].removeAttribute('data-new_booking');
    }

    if (new_booking_href || booking_id) {

        var tab_open = args.tab_open || false;

        // If the tab is not currently open, open it.
        if (tab == 'bookings' && !tab_open) {
            $tab.trigger('click', [{booking_id: booking_id}]);

            // Opening the tab will cause this function to run again. Exit now to stop the remaining code from running twice.
            return false;
        }
    }

    if (tab == 'accounts') {
        var data = {
            contact_id: $('#contact_id').val(),
            credit: 1,
            "send_backend_booking_emails": send_backend_booking_emails
        };
        data.booking_id = booking_id;
        make_payment_modal(data);
    }

    $('#family-member-bookings-tab').find('.content-area').load('/admin/contacts3/ajax_get_bookings?contact_id='+contact_id+'&family_id=no', function()
    {
        var $table = $(this).find('.contact_bookings_table');
        $table.attr('id', 'family_member_bookings_table').dataTable({"aaSorting": []});
        var autoscroll = autoscroll = (typeof args != 'undefined' && args.autoscroll) ? args.autoscroll : false;

        if (new_booking_href)
        {
            $('#family-member-bookings-tab .content-area').load(new_booking_href+' #add_edit_booking_wrapper', function()
            {
                this.innerHTML = '<div class="edit_heading"><h2>Add New Booking</h2></div>' + this.innerHTML;
                document.getElementById('booking_select_contact_fieldset').style.display = 'none';
                booking_form_loaded(true);

                if (autoscroll) {
                    $('#family-member-bookings-tab .content-area')[0].scrollIntoView();
                }
            });
        }
        else if (booking_id)
        {
            var $booking_tr = $table.find('tr[data-booking_id="'+booking_id+'"]');

            if ($booking_tr.length) {
                $booking_tr.click();
            }
        }
        else if (autoscroll) {
            document.getElementById('edit_family_member_heading').scrollIntoView();
        }

        window.disableScreenDiv.hide = false;
        window.disableScreenDiv.style.visibility = "hidden";
        $("#family_member_bookings_table .navapi-outstanding").each(function(){
            var span = this;
            $.get(
                "/admin/navapi/getbooking",
                {
                    id: $(this).parents("tr").data("booking_id")
                },
                function (response) {
                    if (response.remainingAmount) {
                        var ra = parseFloat(response.remainingAmount);
                        $(span).html('€' + ra.toFixed(2));
                    } else {
                        $(span).html('-');
                    }
                }
            )
        });
        window.disableScreenDiv.hide = true;

        $("#family_member_bookings_table tbody > tr").each(function(){
            var max_p_height = [];
            var $td = $(this).find("td");
            $td.each(function(){
                var i = 0;
                $(this).find("p").each(function(){
                    if (!max_p_height[i]) {
                        max_p_height[i] = 0;
                    }
                    max_p_height[i] = Math.max(max_p_height[i], $(this).height());
                    ++i;

                });
            });

            $td.each(function(){
                var i = 0;
                $(this).find("p").each(function(){
                    $(this).css("height", max_p_height[i] + "px");
                    ++i;
                });
            });
        });
    });
}

// Load the student applications is clicked
$(document).on('click', '[href="#family-member-applications-tab"]', function(ev)
{
    ev.stopPropagation();
    var contact_id = $('#contact_id').val();

    open_contact_application_tab();
});

function open_contact_application_tab(application_id)
{
    var contact_id       = $('#contact_id').val();
    const $buttons = $('#header_buttons');

    if ($buttons.length > 0) {
        $buttons.empty().load('/admin/contacts3/ajax_load_default_actions/?contact=true&contact_id=' + contact_id + '&tab=applications');
    }

    $('#family-member-applications-tab').find('.content-area').load('/admin/contacts3/ajax_get_applications?contact_id='+contact_id, function()
    {

    });
}

$(document).on('click','#todo_save',function(){
    var config = {};
    jQuery("#todo").serializeArray().map(function(item) {
        config[item.name] = item.value;
    });
    $.post('/admin/contacts3/create_todo',{form: config},function(){
        if($("#todo").find("input[name='family_id']").val() != '0')
        {
            $("#edit_family").find("div ul.nav li a[href='#family-todos-tab']").click();
            $("#cancel_button").click();
        }
        else
        {
            $("#edit_family_member_wrapper").find("div ul.nav li a[href='#family-member-todos-tab']").click();
            $("#cancel_button").click();
        }
    });
});

$(document).on('click', '[href="#family-member-todos-tab"]', function(ev)
{
    var contact_id = $("#add_edit_contact #contact_id").val();

    // change actions menu to load default menu options
    const $buttons = $('#header_buttons');

    if ($buttons.length > 0) {
        $buttons.empty().load('/admin/contacts3/ajax_load_default_actions/?tab=todos&contact=false');
    }

    $('#family-member-todos-tab').find('.content-area').load('/admin/contacts3/ajax_get_todos?family_id=0'+'&contact_id='+contact_id, function()
    {
        $(this).find('.dataTable').attr('id', 'family_notes_table').dataTable({"aaSorting": []})
            .attr('data-table', 'family').attr('data-header', 'family');
    });
});

// Display "add note" modal boxes
$(document).on('click', '.add_note_btn', function(ev)
{
    ev.preventDefault();
    var modal_box  = document.getElementById('add_note_modal');
    var note_for   = $(this).parents('.edit_heading, .edit_heading-nested').find('h2').html();
    var table      = this.getAttribute('data-table');
    var link_id    = '';
    switch (table)
    {
        case 'family'   : link_id = document.getElementById('family_id').value;  break;
        case 'contacts' : link_id = document.getElementById('contact_id').value; break;
    }

    modal_box.getElementsByClassName('note_to')[0].innerHTML = note_for;
    document.getElementById('add_note_link_id').value        = link_id;
    document.getElementById('add_note_table').value          = table;
    document.getElementById('add_note_note').value           = '';
    $(modal_box).modal();
});

// Display "edit note" modal boxes
$(document).on('click', '.educate_notes_table tbody tr', function()
{
    var id      = this.getAttribute('data-id');
    var heading = $('#edit_'+$(this).parents('table').data('header')+'_heading').find('h2').html();
    $('#edit_notes_modal_wrapper').load('/admin/contacts3/ajax_edit_note/'+id, function()
    {
        var modal = $('#edit_note_modal').modal();
        modal.find('.note_to').html(heading+': ');
    });
});

$(document).on('click', '.educate_todos_table tbody tr', function()
{
    var id      = this.getAttribute('data-id');
    // var heading = $('#edit_'+$(this).parents('table').data('header')+'_heading').find('h2').html();
    $('#family_notes_modal').load('/admin/contacts3/ajax_edit_todo/'+id, function()
    {
        $("#family_notes_modal").modal();
    });
});


// Save a note
$(document).on('click', '.note_save_btn', function(ev)
{
    ev.preventDefault();
    var modal = $(this).parents('.modal');
    var table = modal.find('.note_table_name').val();
    var tab;
    switch (table)
    {
        case 'family'   : tab = 'family-notes-tab';        break;
        case 'contacts' : tab = 'family-member-notes-tab'; break;
    }
    $.ajax({ url : '/admin/contacts3/ajax_save_note', data : modal.find('form').serialize(), type  : 'post', dataType: 'json' })
        .done(function(results)
        {
            $('#'+tab).find('.alert-area').prepend(results.alerts);
            $('[href="#'+tab+'"]').click();
            remove_popbox();
            //console.log(results);
        });

    modal.modal('hide');
});

// Delete a note - modal
$(document).on('click', '.note_delete_btn', function(ev)
{
    ev.preventDefault();
    $(this).parents('.modal').modal('hide');
    $('#confirm_delete_note_modal').modal();
});

// Delete a note - action
$(document).on('click', '#delete_note_button', function(ev)
{
    ev.preventDefault();
    var modal = $(this).parents('.modal');
    $.ajax({url: '/admin/contacts3/ajax_delete_note/'+this.getAttribute('data-id'), dataType: 'json'}).done(function(results)
    {
        var tab, table = $('#edit_note_modal').find('.note_table_name').val();
        switch (table)
        {
            case 'family'   : tab = 'family-notes-tab';        break;
            case 'contacts' : tab = 'family-member-notes-tab'; break;
        }

        modal.modal('hide');
        $('#'+tab).find('.alert-area').prepend(results.alerts);
        $('[href="#'+tab+'"]').click();
        remove_popbox();
    });
});

$(document).on('click', '.email_subscription', function(ev)
{
    var booking_id = $("#booking_id").val();

    $.post(
        '/admin/bookings/email_subscription',
        {
            booking_id: booking_id
        },
        function (response) {
            $("#email_subscription_info").modal();
        }
    )
});

$(document).on('click', '.email_accreditation', function(ev)
{
    var booking_id = $("#booking_id").val();
    var delegate_ids = [];

    $("input.delegate-id").each(function(){
        if (this.value != "" && this.value != null) {
            delegate_ids.push(this.value);
        }
    });

    if (delegate_ids.length > 0) {
        var sent_count = 0;
        for (var i = 0 ; i < delegate_ids.length ; ++i) {
            $.get(
                '/admin/bookings/send_accreditation_application_email/' + booking_id + '/' + delegate_ids[i],
                {
                    id: booking_id,
                    toggle: delegate_ids[i]
                },
                function (response) {
                    ++sent_count;
                    if (sent_count == delegate_ids.length) {
                        $("#email_accreditation_application_info").modal();
                    }
                }
            );
        }
    } else {
        $.get(
            '/admin/bookings/send_accreditation_application_email/' + booking_id,
            {
                id: booking_id
            },
            function (response) {
                $("#email_accreditation_application_info").modal();
            }
        );
    }
});

$(document).on('click', '.add_contact_booking', function(ev)
{
    window.order_table_data = {};
    window.too_many_discount_ignore = false;
    ev.preventDefault();
    var link = this;

    acquireActivityLock(
        'bookings',
        'select-contact-' + $(this).data('contact-id'),
        function (lock) {

            if (!lock.locked) {
                $("#activity-lock-continue-yes").off("click");
                $("#activity-lock-continue-yes").on("click", function(){
                    if ($('.contact_role_id [data-name=guardian]:selected').length == 1) // Guardian Show warning modal
                    {
                        $('#guardian_add_booking_warning').modal();
                    }
                    else // Other contact role process with add a booking
                    {
                        var tab = document.querySelector('[href="#family-member-bookings-tab"]');
                        tab.setAttribute('data-new_booking', link.href);
                        $(tab).click();
                    }
                });
                $("#activity-lock-warning-modal").modal();
                if (lock.locked_by) {
                    $("#activity-lock-warning-modal .username").html(lock.locked_by);
                    $("#activity-lock-warning-modal .time").html(lock.time);
                }
            } else {
                if ($('.contact_role_id [data-name=guardian]:selected').length == 1) // Guardian Show warning modal
                {
                    $('#guardian_add_booking_warning').modal();
                }
                else // Other contact role process with add a booking
                {
                    var tab = document.querySelector('[href="#family-member-bookings-tab"]');
                    tab.setAttribute('data-new_booking', link.href);
                    $(tab).click();
                }
            }
        }
    );
});

$(document).on('click', '.contact_compose_message', function (ev) {
    var message_type = $(this).data('message-type');
    var contact_message_info =
        {
            "email": $(this).data('contact-email'), "db_id": $(this).data('id'),
            "value": $(this).data('id'), "label": $(this).data('contact-label'),
        };
    contact_message_info.category = (message_type === 'alert') ? 'CMS_USER' : 'CMS_CONTACT3'
    $('#messaging-sidebar').removeClass('hidden').show().trigger(':ib-popup-open');
    $('#messaging-sidebar').find(`a[rel="send-${message_type}"]`).trigger('click');
    // the alert and dashboard variables are interchangeably used when referencing sending an alert
    message_type = (message_type === 'alert') ? 'dashboard' : message_type;

    messaging_target_add($(`#send-${message_type}-to-contact-list`), `${message_type}_recipient`,
        contact_message_info, {'x_details': 'to'});
    // The the pop to hide after message has been sent
    $('#messaging-sidebar').data('hide_popup_after_send', true);
});

// Adding a new family member via the "Add Member" button
$(document).on('click', '#add_family_member_btn', function()
{
    var modal = $('#add_family_member_modal').modal();
    // Autocomplete
    $('#add_family_member_name').autocomplete({
        source :'/admin/contacts3/ajax_get_all_contacts_ui/?not_family_id='+$('#family_id').val(),
        open   : function () {
			//$(this).data("uiAutocomplete").menu.element.addClass('educate_ac');
		},
        select : function (event, ui){$('#add_family_member_id').val(ui.item.id);}
    });

    // If the field is blanked, remove the contact id
    $('#add_family_member_name', function() {
        (this.value == '') ? $('#add_family_member_id').val('') : null;
    });

    // Autofocus
    $(modal).on('shown', function() {
        $(this).find('input:first').focus();
    });
});

$(document).on('click', '#add_family_member_modal .btn-primary', function(ev)
{
    ev.preventDefault();
    $.ajax({
        url      : '/admin/contacts3/ajax_change_family/',
        data     : {contact_id: $('#add_family_member_id').val(), family_id: $('#family_id').val()},
        type     : 'post',
        dataType : 'json'
    }).done(function(results)
        {
            $('#add_family_member_modal').find('.alert-area').prepend(results.alerts);
            remove_popbox();
        });
});

$(document).on('click', '[href="#family-member-booking-activity-tab"]', function (ev)
{
    ev.preventDefault();
    var booking_id = $('#booking_id').val();
    open_family_member_booking_activity_table(booking_id);
});

$(document).on('click', '.contact-type-toggle', function () {
    var id = $(this).data('id') || '';
    $.ajax('/admin/contacts3/ajax_get_contact_type/' + id).done(function (data) {

        // Populate the form
        $('#contact-type-id').val(data.id);
        $('#contact-type-name').val(data.display_name);

        // Toggle visibility, depending on whether this is add or edit mode
        var existing_item = !!data.contact_type_id;
        $('.contact-type-add_only').toggleClass('hidden', existing_item);
        $('.contact-type-edit_only').toggleClass('hidden', !existing_item);
        $('#model-contact-type-id').val((existing_item) ? existing_item: '');
        // Open the modal
        $('#contact-type-modal').modal();
    });
});

$(document).on('click', '[href="#family-member-booking-delegates-tab"]', function (ev)
{
    ev.preventDefault();
    var booking_id = $('#booking_id').val();
    open_family_member_booking_delegates_table(booking_id);
});

// AJAX save the data
$('#contact-type-save').on('click', function () {
    var $form = $('#contact-type-form');

    if ($form.validationEngine('validate')) {
        var data = $form.serialize();

        $.ajax({
            url: '/admin/contacts3/ajax_save_contact_type',
            method: 'post',
            data: data,
            dataType: 'json'
        }).done(function (data) {
            // After saving, display message
            for (var i = 0; i < data.messages.length; i++) {
                $('.alert_area').add_alert(
                    data.messages[i].message,
                    (data.messages[i].success ? 'success' : 'danger') + ' popup_box');
            }

            // If successful, refresh the table
            if (data.success) {
                initialize_contact_type_table();
                $('#contact-type-modal').modal('hide');
            }
        });
    }
});

$(document).on("click", ".publish", function (ev) {
    ev.preventDefault();
    var id = $(this).data('id');
    var state = $(this).data('publish');
    $.post('/admin/contacts3/ajax_save_contact_type', {id: id, publish: state}, function (data) {
        if (data.message === 'success') {
            if (state === 1) {
                $(".publish[data-id='" + id + "']").html('<i class="icon-ok"></i>');
                $(".publish[data-id='" + id + "']").data('publish', 0);
            } else {
                $(".publish[data-id='" + id + "']").html('<i class="icon-ban-circle"></i>');
                $(".publish[data-id='" + id + "']").data('publish', 1);
            }
        }
        for (var i = 0; i < data.messages.length; i++) {
            $('.alert_area').add_alert(
                data.messages[i].message,
                (data.messages[i].success ? 'success' : 'danger') + ' popup_box');
        }
    }, "json");
});

function initialize_contact_type_table() {
    var $contact_type_table = $('#list_types_table');

    $contact_type_table.ib_serverSideTable(
        '/admin/contacts3/types_datatable',
        {
            "aaSorting": [[0, 'asc']],
        }, {responsive: true, row_data_ids: true}
    );
}

$(document).on("click", ".delete", function (ev) {
    ev.preventDefault();
    var id = $(this).data('id');
    $.getJSON(
        "/admin/contacts3/find_contact", {
            contact_type_id: id,
        },
        function(data) {
            $("#contact-type-delete-amount").text(data.length);
        }
    );
    $("#btn_delete_yes").data('id', id);
    $("#confirm_delete").modal();
});

$("#btn_delete_yes").click(function (ev) {
    ev.preventDefault();
    var id = $(this).data('id');
    $.post('/admin/contacts3/ajax_delete_contact_type', {contact_type_id: id}, function (data) {
        if (data.message === 'success') {
            initialize_contact_type_table();
        }
        for (var i = 0; i < data.messages.length; i++) {
            $('.alert_area').add_alert(
                data.messages[i].message,
                (data.messages[i].success ? 'success' : 'danger') + ' popup_box');
        }
        $("#confirm_delete").modal('hide');

    }, "json");


});

function open_family_member_booking_activity_table(booking_id, callback)
{
    var params = 'booking_id=' + booking_id;
    var tab = $('#family-member-booking-activity-tab');
    var tableId = 'family_member_booking_activities_table';
    tab.find('.content-area').load('/admin/contacts3/ajax_get_family_member_booking_activities?' + params, function () {
        $(this).find('.dataTable').attr('id', tableId).dataTable({"aaSorting": []});
        if (callback) {
            callback();
        }
    });

}

// Highlight the row clicked on
function select_row(row)
{
    $(row).parents('tbody').find('tr').removeClass('selected');
    $(row).addClass('selected');
}

function open_family_member_booking_delegates_table(booking_id, callback)
{
    var params = 'booking_id=' + booking_id;
    var tab = $('#family-member-booking-delegates-tab');
    var tableId = 'family_member_booking_delegates_table';
    tab.find('.content-area').load('/admin/contacts3/ajax_get_family_member_booking_delegates?' + params, function () {
        $(this).find('.dataTable').attr('id', tableId).dataTable({"aaSorting": []});
        if (callback) {
            callback();
        }
    });

}

function load_family(id, alerts, callback)
{
    alerts = (typeof alerts === 'undefined') ? '' : alerts;
    $('#family_menu_wrapper').load('/admin/contacts3/ajax_display_family_details/'+id, function() {
        $('#edit_family_member_wrapper').hide();
        $('#list_family_members_table').dataTable({"aaSorting": []});
        $('#family-details-tab').find('.alert-area').html(alerts);
        $('body').ib_initialize_datepickers();
        remove_popbox();
        if (callback) {
            callback();
        }
    });
}

// Load the family details form beneath the list of contacts
function load_family_for_contact(contact_id, alerts, callback)
{
    alerts = (typeof alerts === 'undefined') ? '' : alerts;
    $('#family_menu_wrapper').load('/admin/contacts3/ajax_display_family_details', {
        contact_id: contact_id
    }, function() {
        load_contact(contact_id, {}, callback);
        $('#list_family_members_table').dataTable({"aaSorting": []});
        $('#family-details-tab').find('.alert-area').html(alerts);
        remove_popbox();
    });
    var form = $('#add_edit_family');
    form.find('.btn[data-content]').popover({placement:'top',trigger:'hover'});
    $('body').ib_initialize_datepickers();
}

// Load the family member details form beneath list of family members
function load_contact(contact_id, args, callback)
{
    args = (typeof args == 'object') ? args : {};

    args.autoscroll = (typeof args.autoscroll == 'undefined') ? true : args.autoscroll;

    $('#edit_family_member_wrapper').show();
    $('#primary_contact_div').hide();
    
    $('#contact_menu_wrapper').load('/admin/contacts3/ajax_display_contact_details', {contact_id: contact_id, family_id: $('#family_id').val()}, function()
    {
        contact_form_loaded();
        get_family_autocomplete();
        var member_heading     = document.getElementById('edit_family_member_heading');
        var name_heading       = member_heading.getElementsByTagName('h2')[0];
        var flags              = member_heading.getElementsByClassName('flags')[0];
        var span_id            = $('#edit_family_member_wrapper .span_client_id');
        var contact_name = $("#list_family_members_table").find('tr[data-id="' + contact_id + '"] td:nth-child(2) a').html();
        contact_name = (contact_name === undefined) ? $("tbody").first().find("tr.selected").find("td[data-label='Name']").first().text() : contact_name;

        if (contact_id)
        {
            flags.innerHTML        = '';
            span_id.html('');
            // Editing a contact
            //name_heading.innerHTML = document.getElementById('list_family_members_table').querySelector('tr[data-id="'+contact_id+'"] td:nth-child(2) a').innerHTML;
           name_heading.innerHTML = contact_name;
            if (document.getElementById('contact_is_primary') && document.getElementById('contact_is_primary').querySelector(':checked')) {
                if (document.getElementById('contact_is_primary').querySelector(':checked').value == 1) {
                    flags.innerHTML += '<span class="label location-flag">Primary Contact' + '</span>';
                }
            }

            if (document.getElementById('contact_year_id') && document.getElementById('contact_year_id').options[document.getElementById('contact_year_id').selectedIndex].value != "")
            {
                flags.innerHTML += '<span class="label location-flag">Year: '+$("#contact_year_id").find("option:selected").text()+'</span>';
            }

            $(flags).append('<span class="label location-flag">Type: ' + ($('#contact_type option:selected').text()) + '</span>');
            messaging_target_remove($('#send-sms-to-contact-list').find('a.remove-to'));
            var role_flag = [];
            $(".contact_role_id option").each(function(){
                if (this.selected) {
                    role_flag.push(this.innerHTML);
                }
            });
            if (role_flag.length > 0) {
                $(flags).append('<span class="label location-flag">' + role_flag.join(', ') +'</span>');
            }

            if (window.accountsiq_id) {
                $(flags).append('<span class="label location-flag">AccountsIQ: ' + window.accountsiq_id +'</span>');
            }

            var location_flags = '';
            if (window.booked_locations) {
                for (var i = 0; i < window.booked_locations.length; i++) {
                    location_flags += '<span class="label location-flag" data-id="'+window.booked_locations[i].id+'" title="This contact has bookings in '+window.booked_locations[i].name+'">'+window.booked_locations[i].name+'</span>';
                }

            }
            flags.innerHTML += location_flags;

            if (window.contact_phone_number) {
                flags.innerHTML += '<span class="label location-flag">Phone: ' + window.contact_phone_number + '</span>';
            }

            span_id.html('Contact: #'+contact_id);
        }
        else
        {
            // Adding a new contact
            name_heading.innerHTML = 'Add New Contact';
			if (document.getElementById('family_id'))
			{
				document.getElementById('contact_family').value = document.getElementById('family_name').value;
				document.getElementById('contact_family_id').value = document.getElementById('family_id').value;
			}
            $('#contact_family_id').val($('#family_id').val());
            $('#contact_last_name').val($('#family_name').val());
            flags.innerHTML        = '';

            $('#contact_is_primary_no').click();
            $('.contact_role_id option[value="2"]').show();
            if ($('#contact_family').val() === '' || $('#contact_family_id').val() === '')
            {
                $('#contact_is_primary_yes').click();
                $('.contact_role_id option[value="2"]').hide();
            }
            // The adding of a new contact must from an organisation if family id is null
            if($('#family_id').val() === undefined) {
                $("#linked_organisation_id").val($("tbody").first().find("tr.selected").data('id'));
                $("#linked_organisation").val(contact_name);
            }

            //else
            //{
            //    $('#contact_is_primary_no').click();
            //    $('#contact_is_primary').val('0');
            //}
        }

        // found in error MOC . : $.post('/admin/bookings/get_additional_details');

        $('[href="#family-member-details-tab"]').click();

        if (args.autoscroll) {
            document.getElementById('edit_family_member_heading').scrollIntoView();
        }

        // Code to be run after this function
        if (callback && typeof(callback) === 'function')
        {
            callback();
        }
        $('body').ib_initialize_datepickers();
    });
}
