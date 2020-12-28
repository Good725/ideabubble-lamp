var student                 = {first_name:'',last_name:''};
var booking                 = {};
var current_booking_data    = {};
var current_booking_discounts = [];
var current_schedule        = '';
var calendar_data           = {};
var additional_booking_data = {};
var amount;
var contact_id              = '';
var booking_id              = '';
var booking_payment         = [];
var selected_locs   = 0 ; var locs    = [];

var bookingActionButtonClicked = '';

function check_schedule_capacity(schedule_id, timeslot_id, ok_callback, warn_callback, skip_duplicate)
{
    var original_args = arguments;
    skip_duplicate = skip_duplicate || false;
    if (isNaN(parseInt($('#contact_id').val())) && $("#students-wrapper tbody tr").length == 0) {
        $("#alert_no_contact").modal();
        return false;
    }
    var quantity = 1;
    if ($("#booking_qty_type").val() == "single") {
        quantity = 1;
    }
    if ($("#booking_qty_type").val() == "delegates") {
        quantity = $("#delegates-wrapper tbody tr").length;
    }
    if ($("#booking_qty_type").val() == "multiple") {
        quantity = $("#students-wrapper tbody tr").length;
    }
    $.post(
        '/admin/bookings/check_schedule_capacity',
        {
            schedule_id: schedule_id,
            timeslot_id: timeslot_id,
            contact_id: $('#contact_id').val(),
            quantity: quantity
        },
        function (response) {
            if (!skip_duplicate && response.duplicate) {
                // Keep note of the funciton and its parameters
                // So that they can be called agani, should the user click "continue anyway"
                cms_ns.duplicate_booking_params = original_args;
                cms_ns.duplicate_booking_params[4] = true; // skip duplicate argument
                cms_ns.duplicate_booking_function = check_schedule_capacity;

                $("#duplicate_schedule_booking_modal").modal();
            } else if (response.error) {
                alert(response.error);
            } else if (response.warn) {
                if (warn_callback) {
                    warn_callback(schedule_id, timeslot_id, response);
                }
            } else {
                if (ok_callback) {
                    ok_callback(schedule_id, timeslot_id);
                }
            }
        }
    )
}

function get_recurring_schedule(args, callback)
{
    var booking_type = args.booking_type || null;
    var schedule_id  = args.schedule_id  || null;
    var timeslot_id  = args.timeslot_id  || null;

    current_schedule   = schedule_id;
    var contact_name   = (document.getElementById('select_contact') != null) ? document.getElementById('select_contact').value : '';
    var contact_header = document.getElementById('edit_family_member_heading');
    var period_id;
    contact_name       = (contact_name == '' && contact_header != null) ? contact_header.getElementsByTagName('h2')[0].innerHTML : contact_name;

    if (typeof additional_booking_data.outside_of_school != 'undefined')
    {
        document.getElementById('studied_outside_of_school').checked = (additional_booking_data.outside_of_school.indexOf(current_schedule) > -1);
    }
    else
    {
        document.getElementById('studied_outside_of_school').checked = false;
    }

    document.getElementById('student_name_modal_display').innerHTML = '<table class="table table-striped">'+document.getElementById('ajax_contact_data').innerHTML+'</table>';

    var params = {
        schedule_id: schedule_id,
        booking_type: booking_type
    };
    if (booking_type == 'One Timeslot') {
        params.event_id = timeslot_id;
    } else {
        params.filter_from_event_id = timeslot_id;
    }
    $.post(
        '/admin/bookings/get_recurring_schedule',
        params,
        function (response) {
            if (callback) {
                response = callback(response.timeslots);
            }
            //$("#register_modal").modal("show");
            $("#modal_display_schedule .bulk_attend_update .datepicker").datepicker();
            var data = response;
            var value;
            if (typeof data.timeslots[1] !== 'undefined') {
                value = data.timeslots[1];
            } else if (typeof data.timeslots[0] !== 'undefined') {
                value = data.timeslots[0] ;
            } else {
                value = data;
            }

            var date = new Date();
            var html  = '';
            $("#period_booking_table").dataTable().fnDestroy();
            var tbody = document.getElementById('period_booking_table').getElementsByTagName("tbody");
            tbody.innerHTML = '';
            var title = "";
            $('#cost_per_class').val(value.fee);
            $('#schedule_detail_schedule_id').val(value.schedule_id);
            var period_attending=data.timeslots.length;
            title += ': Register Place for: '+value.course_title;
            $('#myModalLabel').html(title);

            $('#register_modal_course_details').html(
                '<div>' +
                '	<div class="row">' +
                '		<div class="col-sm-6">Schedule #' + value.schedule_id + ' ' + value.schedule_title + '</div>' +
                '		<div class="col-sm-6">Time: ' + value.time + '</div>' +
                '	</div>' +
                '	<div class="row">' +
                '		<div class="col-sm-6">Course name ' + value.course_title + ', starting ' + value.day + ' ' + value.date + '</div>' +
                '		<div class="col-sm-6">Number of Time period: ' + period_attending + '</div>' +
                '	</div>' +
                '</div>'
            );


            var array_of_week_days = [];
            var filled_timeslots = [];

            if (data.timeslots.length > 0) {
            $.each(data.timeslots, function(index, period)
            {
                var icon = 'icon-remove';
                var has_removed = false;
                var past = period.today > period.datetime_start ;
                if (past){
                    var include_past_timeslots = ($('[name="timeslots_range"]:checked').val() == 'past');
                    if (include_past_timeslots) {
                        past = false;
                    }
                }
                if (typeof current_booking_data[current_schedule] == 'undefined' || current_booking_data[period.schedule_id][parseInt(period.period_id)])
                {
                    icon = 'icon-ok';
                }
                if (current_booking_data[period.schedule_id] &&
                    current_booking_data[period.schedule_id][parseInt(period.period_id)] &&
                    current_booking_data[period.schedule_id][parseInt(period.period_id)].attending == 0
                )
                {
                    icon = 'icon-remove';
                    has_removed = true;
                }

                var $customize_timeslot = $("#booking-schedule-edit-modal .freq_b tr[data-timeslot_id=" + period.period_id + "]");
                if ($customize_timeslot.length > 0){
                    if ($customize_timeslot.find(".attend:checked").length > 0) {
                        if ($customize_timeslot.find(".attend:checked").val() == "1") {
                            icon = 'icon-ok';
                            has_removed = false;
                        } else {
                            icon = 'icon-remove';
                            has_removed = true;
                        }
                    }
                }

                var note = '';

                if (params.filter_from_event_id && parseInt(params.filter_from_event_id) > parseInt(period.period_id) && !past) {
                    note = 'Not Due';
                    icon = 'icon-remove';
                } else if (current_booking_data[period.schedule_id] && current_booking_data[period.schedule_id][parseInt(period.period_id)])
                {
                    note = current_booking_data[period.schedule_id][parseInt(period.period_id)].note;
                    if (note == null) note = '';
                }
                else
                {
                    if (past )
                    {
                        note = 'Booked after start date.';
                        icon = 'icon-remove';
                    }
                }

                if (!past) {
                    if ($("#booking-schedule-edit-modal .note[data-timeslot_id=" + period.period_id + "]").length > 0) {
                        if ($("#booking-schedule-edit-modal .freq_b tr[data-timeslot_id=" + period.period_id + "] .attend:checked").length > 0) {
                            note = $("#booking-schedule-edit-modal .note[data-timeslot_id=" + period.period_id + "]").val();
                        }
                    }
                }

                // Check
                if (period.available_slots != null)
                if (period.available_slots <= 0)
                {
                    icon = 'icon-ban-circle';
                    filled_timeslots.push(period);
                }
                period_id = period.schedule_id;
                var prepay = false;
                if (period.payment_type == 1)
                {
                    prepay = true;
                }

                html += '<tr data-schedule_id="'+period.schedule_id+'" data-period_id="'+period.period_id+'" data-datetime_start="' + period.datetime_start + '" data-fee_per="' + period.fee_per + '" data-fee="' + period.fee + '" data-prepay="'+prepay+'"';
                if (past || (params.filter_from_event_id && parseInt(params.filter_from_event_id) > parseInt(period.period_id)))
                {
                    html += ' class="hide"';
                    period_attending--;
                }else{
                    if (!has_removed && $.inArray(period.day, array_of_week_days) == -1) {
                        array_of_week_days.push(period.day);
                        html += 'data-schedule_day=' + period.day;
                    } else {
                        if (!has_removed) {
                            html += 'class="hide future-auto-select" data-schedule_day=' + period.day;
                        }else{
                            html += 'class="hide"';
                        }
                    }
                }
                html +='>' +
                    '<td>'+period.course_title+'</td>' +
                    '<td>'+period.schedule_title+'</td>' +
                    '<td>'+period.day+'</td>' +
                    '<td>'+period.date+'</td>' +
                    '<td>'+period.time+'</td>' +
                    '<td class="fee_per_timeslot">' + (period.fee_per == 'Timeslot' ? period.fee : '') + '</td>' +
                    '<td><i class="period_attend_icon '+icon+'" data-period_id="'+period.period_id+'"></i></td>' +
                    '<td><input type="text" class="form-control period_booking_note" value="'+note+'" /></td>' +
                    '</tr>';
            });
            } else {
                // No-timeslot (likely self-paced learning) booking
                html += '<tr ' +
                    '   data-schedule_id="'+data.schedule_id+'" ' +
                    '   data-fee_per="' + data.fee_per + '" ' +
                    '   data-fee="' + data.fee + '" ' +
                    '   data-prepay="'+(data.payment_type == 1)+'"' +
                    '>' +
                    '   <td>'+data.course_title+'</td>' +
                    '   <td>'+data.schedule_title+'</td>' +
                    '   <td></td>' + // day (n/a)
                    '   <td></td>' + // date  (n/a)
                    '   <td></td>' + // time (n/a)
                    '   <td class="fee_per_timeslot">' + (data.fee_per == 'Timeslot' ? data.fee : '') + '</td>' +
                    '   <td><span class="period_attend_icon  icon-ok"></span></td>' +
                    '   <td><input type="text" class="form-control period_booking_note" value="" /></td>' +
                    '</tr>';
            }

            $("#modal_display_schedule").attr("data-booking_type", params.booking_type).data("booking_type", params.booking_type);
            document.getElementById('period_booking_table').getElementsByTagName("tbody")[0].innerHTML = html;
            $("#period_booking_table .fee_per_timeslot").css("display", value.fee_per == 'Timeslot' ? '' : 'none');

            if ($("#period_booking_table").find(".icon-ban-circle").length > 0)
            {
                $("#schedule_full_message").html(
                    'Please note schedule ' +
                    filled_timeslots[0].schedule_title + ' ' + filled_timeslots[0].day + ' ' + filled_timeslots[0].date + ' ' + filled_timeslots[0].time +
                    ' is fully booked. Please allocate more seats under the schedule to allow more bookings to this schedule'
                );
                $("#alert_schedule_full").modal();
                $('#modal_display_schedule').hide();
                $("#add_places_to_booking").hide();
            }
            else
            {
                $('#modal_display_schedule').show();
                $("#add_places_to_booking").show();
                $('#period_attending').val(period_attending);
                $('#schedule_fee_per').val(value.fee_per);
                $('#schedule_fee_amount').val(value.fee);

                if (value.payment_type == 1)
                {
                    $('#schedule_payment_type').val(true);
                    title = 'Prepay';
                    $('#schedule_total_cost').val(value.fee);
                    get_attending_cost();
                }
                else
                {
                    $('#schedule_payment_type').val(false);
                    title = 'PAYG';
                    get_attending_cost();
                }

                /*
                 if (value.fee_per == 'Schedule') {
                 $('#cost_per_class_div').hide();
                 } else {
                 $('#cost_per_class_div').show();
                 }*/
                $('#cost_per_class_div').hide();

                // Show warning, if no timeslots were selected, unless it is self-paced learning.
                if (period_attending <= 0 && data.learning_mode != 'self_paced') {
                    $('#add_places_to_booking').hide();
                    $('#cancel_add_places_to_booking').html('All timeslots are in the past');
                } else {
                    $('#add_places_to_booking').show();
                    $('#cancel_add_places_to_booking').html('Cancel');
                    $("#add_places_to_booking").click();
                }

                set_distinct_day_times();
                $("#period_booking_table").dataTable({"bPaginate": false, "bInfo": false, "bFilter": false});
            }
        }
    );
}

$(document).on('click', '#schedule_duplicate_booking_continue', function() {
    // Re-run the function with the parameters specified before the modal was apened.
    cms_ns.duplicate_booking_function.apply(null, cms_ns.duplicate_booking_params);

    $('#schedule_duplicate_booking_modal').modal('hide');

    cms_ns.duplicate_booking_function = null;
    cms_ns.duplicate_booking_params = {};
});


document.addEventListener("DOMContentLoaded", function(event) {

    $(document).on("change", "#booking_qty_type", function(){
        $(".booking-select_contact, #delegates-wrapper, #lead-booker-wrapper, #students-wrapper").addClass("hidden");
        $("#booking-select_contact-heading .student").addClass("hidden");
        $("#booking-select_contact-heading .organization").addClass("hidden");
        if (this.value == "single") {
            $(".booking-select_contact").removeClass("hidden");
            $("#booking-select_contact-heading .student").removeClass("hidden");
        }
        if (this.value == "delegates") {
            $(".booking-select_contact, #delegates-wrapper, #lead-booker-wrapper").removeClass("hidden");
            $("#booking-select_contact-heading .organization").removeClass("hidden");
        }
        if (this.value == "multiple") {
            $("#students-wrapper").removeClass("hidden");
        }
        $("#booking_schedules_list_table").dataTable()._fnAjaxUpdate();
    });

    $(".action-buttons button").on("click", function(){
        bookingActionButtonClicked = this.id;
    });

    booking_form_loaded(true);
    $('.multiple_select').multiselect();

    $("#select_contact").autocomplete({
        source: function (request, response) {
            var data = {};
            if ($("#booking_qty_type").val() == "delegates") {
                data.type = "organisation";
            }
            data.term = request.term;
            $.ajax({
                url: "/admin/bookings/find_customer",
                data: data,
                success: function (data) {
                    response(data);
                },
                error: function () {
                    response([]);
                }
            });
        },
        select:function (event, ui)
        {
            var item = ui.item;
            if ($("#booking_qty_type").val() == "delegates")
            if ($("#bookings_require_primary_biller_organisation_booking").val() == 1 && item.primary_biller_id == null) {
                $("#select_contact").val("");
                $("#primary_biller_error_modal").modal();
                return false;
            }
            function display()
            {
                document.getElementById('contact_id').value = item.id;
                $("#ajax_contact_data .first_name").html(item.first_name);
                $("#ajax_contact_data .last_name").html(item.last_name);
                $("#ajax_contact_data .mobile").html(item.mobile);
                $("#ajax_contact_data .address1").html(item.address1);
                $("#ajax_contact_data .address2").html(item.address2);
                $("#ajax_contact_data .address3").html(item.address3);
                $("#ajax_contact_data .country").html(item.country);
                $("#ajax_contact_data .county").html(item.county);
                $("#ajax_contact_data .postcode").html(item.postcode);
                $("#ajax_contact_data .town").html(item.town);
                $("#ajax_contact_data .email").html(item.email);
                $('#ajax_contact_data').removeClass('hidden');
                $('#booking-select_contact-heading').addClass('hidden');

                student = item;
                document.getElementById('student_name_modal_display').innerHTML = '<table class="table table-striped">' + document.getElementById('ajax_contact_data').innerHTML + '</table>';
            }

            acquireActivityLock(
                'bookings',
                'select-contact-' + item.id,
                function (lock) {
                    if (!lock.locked) {
                        $("#activity-lock-continue-yes").off("click");
                        $("#activity-lock-continue-yes").on("click", function(){
                            display();
                        });
                        $("#activity-lock-warning-modal").modal();
                        if (lock.locked_by) {
                            $("#activity-lock-warning-modal .username").html(lock.locked_by);
                            $("#activity-lock-warning-modal .time").html(lock.time);
                        }
                    } else {
                        display();
                    }
                }
            );
            if($("#booking_qty_type").val() == "delegates") {
                $.post(
                    '/admin/contacts3/ajax_update_membership',
                    {
                        id: item.id,
                    },
                    function (response) {
                        console.log(response);
                    });
            }
        },
        messages: {
            noResults: '',
            results: function() {}
        },
        minLength: 1
    });

    window.$booking_delegate_tr = $("#delegates-wrapper tbody > tr.hidden.delegate-row-template");
    $booking_delegate_tr.remove();

    $(document).on("click", "#delegates-wrapper tbody .delete-delegate", function(){
        $(this).parents("tr").remove();
        get_order_table_html();
    });

    window.delegate_add_to_list = null;

    $(document).on("click", "#delegates-wrapper .delegate-add", function(){
        var $tr = $booking_delegate_tr.clone();
        $tr.find(".delegate-name").html(window.delegate_add_to_list.first_name + " " + window.delegate_add_to_list.last_name);
        $tr.find("input.delegate-id").val(window.delegate_add_to_list.id);
        $tr.removeClass("template");
        $tr.removeClass("hidden");

        $("#delegates-wrapper tbody").append($tr);
        $("#select_delegate").val("");
        window.delegate_add_to_list = null;
        get_order_table_html();
    });

    $("#select_lead_booker").autocomplete({
        source: function (request, response) {
            var data = {};
            data.linked_contact_id = $("#contact_id").val();
            data.term = request.term;
            $.ajax({
                url: "/admin/bookings/find_customer",
                data: data,
                success: function (data) {
                    response(data);
                },
                error: function () {
                    response([]);
                }
            });
        },
        open: function () {

        },
        select: function (event, ui) {
            $("#lead_booker_id").val(ui.item.id);
        }
    });

    $("#select_delegate").autocomplete({
        source: function (request, response) {
            var data = {};
            data.linked_contact_id = $("#contact_id").val();
            data.term = request.term;
            $.ajax({
                url: "/admin/bookings/find_customer",
                data: data,
                success: function (data) {
                    response(data);
                },
                error: function () {
                    response([]);
                }
            });
        },
        select:function (event, ui)
        {
            var item = ui.item;
            window.delegate_add_to_list = item;
        },
        messages: {
            noResults: '',
            results: function() {}
        },
        minLength: 1
    });

    window.$booking_student_tr = $("#students-wrapper tbody > tr.hidden.student-row-template");
    $booking_student_tr.remove();

    $(document).on("click", "#students-wrapper tbody .delete-student", function(){
        $(this).parents("tr").remove();
        get_order_table_html();
    });

    window.student_add_to_list = null;

    $(document).on("click", "#students-wrapper .student-add", function(){
        var $tr = $booking_student_tr.clone();
        $tr.find(".student-name").html(window.student_add_to_list.first_name + " " + window.student_add_to_list.last_name);
        $tr.find("input.student-id").val(window.student_add_to_list.id);
        $tr.removeClass("template");
        $tr.removeClass("hidden");

        $("#students-wrapper tbody").append($tr);
        $("#select_student").val("");
        window.student_add_to_list = null;

        get_order_table_html();
    });

    $(document).on('click', '#students-wrapper .student-add_by_tag', function() {
        const tag_id = $('#students-filter-tag').val();

        if (!tag_id) {
            $('#students-missing-tag-modal').modal();
            return false;
        }

        $.ajax({
            url: '/admin/contacts3/ajax_search?tag_id=' + tag_id
        }).done(function(data) {
            data.contacts.forEach(contact => {
                var $tr = $booking_student_tr.clone();
                $tr.find('.student-name').text(contact.full_name);
                $tr.find('.student-id').val(contact.id);
                $tr.removeClass('template').removeClass('hidden');
                $('#students-wrapper').find('tbody').append($tr);
            });

            $('#students-filter-tag').val('');
            $('#students-filter-tag-input').val('');
            get_order_table_html();
        });
    });

    $("#select_student").autocomplete({
        source:"/admin/bookings/find_customer",
        select:function (event, ui)
        {
            var item = ui.item;
            window.student_add_to_list = item;
        },
        messages: {
            noResults: '',
            results: function() {}
        },
        minLength: 1
    });

    $(document).on('keypress', '#select_schedule_name, #select_location-input, #select_category-input, #select_subject-input, #select_year-input',function(e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            $("#search_courses_schedules").click();
        }
    });

    $(document).on('click', '#booking_schedules_list .display_timetable', function(){
        var start = $('#from_date').val();
        try {
            start = $calendar.fullCalendar("getView").end.format("YYYY-MM-DD");
        } catch (exc) {

        }
        var end = $('#to_date').val();
        try {
            end = $calendar.fullCalendar("getView").end.format("YYYY-MM-DD");
        } catch (exc) {
            console.log(exc);

        }
        var params = {
            contact_id:document.getElementById('contact_id').value,
            booking_id:document.getElementById('booking_id').value,
            edit_booking_id: $("[name=edit_booking_id]").val(),
            datetime_start:start,
            datetime_end:end,
            //room:$("#select_room").val(),
            current_bookings: current_booking_data,
            publish: 1
        };

        params.schedule_id = [];
        var schedules_table = $("#booking_schedules_list_table")[0];
        if (schedules_table.selected_ids)
        for (var schedule_id in schedules_table.selected_ids) {
            if (schedules_table.selected_ids[schedule_id]) {
                params.schedule_id.push(schedule_id);
            }
        }


        if (params.schedule_id.length == 0) {
            alert("Please select at least one schedule");
            return;
        }
        params.schedule_name = "";
        params.subject = "";
        params.location = "";
        params.category = "";
        params.year = "";
        params.timeslots_range = $('[name="timeslots_range"]:checked').val();

        $.post('/admin/bookings/get_schedules_list', params, function(data)
        {
            document.getElementById('student_name_modal_display').innerHTML = '<table class="table table-striped">'+document.getElementById('ajax_contact_data').innerHTML+'</table>';
            document.getElementById('category_tab').innerHTML = data;
            $('.multiple_select').multiselect();
            show_booking_calendar();

            var calendar = new Ib_calendar({
                id: 'booking-calender',
                bookings_enabled: true,
                popover_mode: 'read'
            });
        });
    });

    $(document).on('click', '#search_courses_schedules',function()
    {
		var valid   = false;
        var $alerts = $('#timetable_selection_alert_area');
        var params = {
            contact_id:document.getElementById('contact_id').value,
            booking_id:document.getElementById('booking_id').value,
            edit_booking_id: $("[name=edit_booking_id]").val(),
            datetime_start:$('#from_date').val(),
            datetime_end:$('#to_date').val(),
            //room:$("#select_room").val(),
            current_bookings: current_booking_data,
            publish: 1
        };

        if ($('#select_schedule_name_id').val() != '')
        {
            valid = true;
            params.schedule_id = $('#select_schedule_name_id').val();
            params.schedule_name = "";
            params.subject = "";
            params.location = "";
            params.category = "";
            params.year = "";
        } else {
            var valid_search = true;

			var form_validation = $('#kes_booking_edit_form').validationEngine('validate', {
				'prettySelect' : true,
				'useSuffix'    : '-input'
			});

			if ( ! form_validation)
			{
				valid_search = false;
			}
			else
			{
				if ( ! $('#select_contact').val()) {
					$alerts.add_alert('You must select a contact.', 'warning', {autoscroll: false});
					valid_search = false;
				}

				if ( ! $('#select_location').val()) {
					$alerts.add_alert('You must select a location.', 'warning', {autoscroll: false});
					valid_search = false;
				}

				if ( ! $('#select_category').val()) {
					$alerts.add_alert('You must select a category.', 'warning', {autoscroll: false});
					valid_search = false;
				}
			}

            valid = valid_search;

            params.schedule_id = "";
            params.schedule_name = $('#select_schedule_name').val();
            params.subject = $('#select_subject').val();
            params.location = $('#select_location').val();
            params.category = $('#select_category').val();
            params.year = $('#select_year').val();
        }

        if (valid)
        {
            $.post('/admin/bookings/get_schedules_list', params, function(data)
            {
                document.getElementById('student_name_modal_display').innerHTML = '<table class="table table-striped">'+document.getElementById('ajax_contact_data').innerHTML+'</table>';
                document.getElementById('category_tab').innerHTML = data;
                $('.multiple_select').multiselect();
				show_booking_calendar();
            });
        }
    });

    $(document).on('click', '#add_places_to_booking', function()
    {
        var absences_explained = true;

        // Loop through each event in the schedule
        /*$('#period_booking_table').find('tbody tr').each(function()
        {
            // Check if absent
            if ($(this).find('.period_attend_icon').hasClass('icon-remove'))
            {
                // Check if note is empty
                if ($(this).find('.period_booking_note').val().trim() == '')
                {
                    absences_explained = false;
                    return false;
                }
            }
        });*/

        // Don't continue, if there are any unexplained absences
        if ( ! absences_explained && $("#booking-schedule-edit-modal [name='attend-all']").val() == "0" )
        {
            $('#unexplained_absence_modal').modal();
        }
        else
        {
            $('#register_modal').modal('hide');
            add_periods_to_booking();

            confirm_bookings();
            disable_registration_button();
            refresh_tables();
        }
    });

    $(document).on('click','.cancel_place',function()
    {
        var period_id    = $(this).parents('.schedule_container').data('period_id');
        current_schedule = $(this).parents('.schedule_container').data('schedule_id');

        if (current_booking_data[current_schedule])
        {
            delete current_booking_data[current_schedule][period_id];
        }
        show_register(period_id);
        unattend_period(period_id);
        refresh_tables();
        $('#schedule_id').val(current_schedule);
    });

    $(document).on("click", "#alternative-schedules-warning-continue", function (){
        get_recurring_schedule({
            booking_type: $(this.booking_link).data('booking_type'),
            schedule_id:  $(this.booking_link).closest('.schedule_container').data('schedule_id'),
            timeslot_id:  $(this.booking_link).data('event_id')
        });
    });

    $(document).on('click','.register_place',function()
    {
        var booking_link = this;
        current_schedule   = $(booking_link).closest('.schedule_container').data('schedule_id');

        if ($(this).parents(".calendar-popover").find(".customize.yes").prop("checked")) {
            $("#booking-schedule-edit-modal .btn.add").removeClass("hidden");
            $("#booking-schedule-edit-modal .btn.update").addClass("hidden");
            booking_schedule_edit_modal_display($(booking_link).data('schedule_id'), booking_link, true);
            return;
        }

        $("#register_modal").modal("hide");
        check_schedule_capacity(
            $(booking_link).data('schedule_id'),
            $(booking_link).data('event_id'),
            function (schedule_id, timeslot_id, response) {
                get_recurring_schedule({
                    booking_type: $(booking_link).data('booking_type'),
                    schedule_id:  $(booking_link).data('schedule_id'),
                    timeslot_id:  $(booking_link).data('event_id')
                });
            },
            function (schedule_id, timeslot_id, response) {
                capacity_check_failed(schedule_id, timeslot_id, response)
            }
        );
    });

    $(document).on('click', '.period_attend_icon', function()
    {
        var current_tr = $(this).closest('tr');
        // var note = current_tr.find('td:eq(7) > input').val();
        var period_id = this.getAttribute('data-period_id');
        current_schedule = current_tr.data('schedule_id');
        var schedule_day = current_tr.data('schedule_day');
        var period_attending = parseInt($('#period_attending').val());

        // select all hide tr with same day
        var all_trs = $("#period_booking_table tr.future-auto-select[data-schedule_day='"+schedule_day+"']");
        $.each(all_trs,function (index,value) {
            // $(value).find('td:eq(7) > input').val(note);
            var td = $(value).find('td:eq(6) > i');
            if (td.hasClass('icon-remove'))
            {
                td.addClass('icon-ok');
                td.removeClass('icon-remove');
                period_attending++;
            }
            else
            {
                td.addClass('icon-remove');
                td.removeClass('icon-ok');
                period_attending--;
            }
        });

        if ($(this).hasClass('icon-remove'))
        {
            $(this).addClass('icon-ok');
            $(this).removeClass('icon-remove');
            period_attending++;
        }
        else
        {
            $(this).addClass('icon-remove');
            $(this).removeClass('icon-ok');
            period_attending--;
        }
        $('#period_attending').val(period_attending);
        get_attending_cost();
        refresh_tables();
    });

    $(document).on('change', '.period_booking_note', function()
    {
        var current_tr = $(this).closest('tr');
        var note = current_tr.find('td:eq(7) > input').val();

        var schedule_day = current_tr.data('schedule_day');

        // select all hide tr with same day
        var all_trs = $("#period_booking_table tr.future-auto-select[data-schedule_day='"+schedule_day+"']");
        $.each(all_trs,function (index,value) {
            $(value).find('td:eq(7) > input').val(note);
        });
        refresh_tables();
    });

    $(document).on('click', '#confirmed_periods_table tbody td i', function()
    {
        var period_id = this.getAttribute('data-period_id');
        current_schedule = $(this).closest('tr').data('schedule_id');
        if ($(this).hasClass('icon-remove'))
        {
            $(this).addClass('icon-ok');
            $(this).removeClass('icon-remove');
            attend_period(period_id);
            show_cancel(period_id);
        }
        else
        {
            $(this).addClass('icon-remove');
            $(this).removeClass('icon-ok');
            // If this is a current booking, change its attending status
            unattend_period(period_id);
            show_register(period_id);
        }
        refresh_tables();
        $('#schedule_id').val(current_schedule);
    });

    $(document).on('change', '.confirmed_period_booking_note', function()
    {
        var $row        = $(this).parents('tr');
        var period_id   = $row[0].getAttribute('data-period_id');
        var schedule_id = $row[0].getAttribute('data-schedule_id');
        current_booking_data[schedule_id][period_id].note = this.value ? this.value : '';
    });

    $(document).on('click','.schedule_title',function(){
        var schedule_id = $(this).closest('.schedule_container').data('schedule_id');
        $.post('/admin/bookings/get_course_details',{schedule_id:schedule_id},function(data){
            data = $.parseJSON(data);
            document.getElementById('modal_course_name').innerHTML = data.title;
            document.getElementById('modal_course_summary').innerHTML = data.summary;
            document.getElementById('modal_course_description').innerHTML = data.description;
            document.getElementById('modal_course_details_detail').innerHTML = 'Schedule: #' + data.schedule
                    + '<br>Subject: '+ (data.subject === null ? 'Not Set in the course' : data.subject )
                    + '<br>Category: '+ (data.category === null ? 'Not Set in the course' : data.category );
            $("#modal_course_details").modal();
        });
    });

    $(document).on('click','#schedules_tbody i',function()
    {
        var schedule_id = $(this).closest('tr').data('schedule_id');
        delete current_booking_data[schedule_id];
        $('.schedule_container[data-schedule_id="'+schedule_id+'"] .cancel_place').removeClass('cancel_place').addClass('register_place');
        enable_registration_button(schedule_id);
        refresh_tables();
    });

    $(document).on('click','#offers_tbody .remove_cell',function()
    {
        var discount_id = $(this).closest('tr').data('offer_id');
        var $row = $('#offer_id_'+discount_id);
        if ($row.hasClass('ignored_discount'))
        {
            $row.removeClass('ignored_discount');
            $(this).find('.icon-ban-circle').attr('class', 'icon-ok');
        }
        else
        {
            $row.addClass('ignored_discount');
            $(this).find('.icon-ok').attr('class', 'icon-ban-circle');
        }
        get_order_table_html();
    });

    $(document).on('click', '#booking_book', function()
    {
        if (validate('#booking_book'))
        {
            if ($("#purchase_order_number").val() == "" && $("#purchase_order_number").data("mandatory") == 1) {
                $('#booking-modal-po_number-required').modal();
                return false;
            }

            var data = set_data(2, 'book');
            process_booking(data, this);
        }
    });

    $(document).on('click', '#booking-create_sales_quote', function()
    {
        if (validate('#booking_book'))
        {
            var data = set_data(6, 'book');
            process_booking(data, this);
        }
    });

    $(document).on('click', '#booking_save', function()
    {
        if (validate('#booking_save'))
        {
            var data = set_data(1, 'save');
            process_booking(data, this);
        }
    });

    $(document).on('click', '#booking_save_change', function()
    {
        if (validate('#booking_save_change'))
        {
            var data = set_data(2, 'update');
            process_booking(data, this);
        }
    });

    $(document).on('click', '#booking-confirm', function(ev)
    {
        if (!$('#purchase_order_number').val() && $("#purchase_order_number").data("mandatory") == 1) {
            $('#booking-modal-po_number-required').modal();
        } else {
            var data = set_data(2, 'update');
            process_booking(data, this);
        }
    });


    $(document).on('click','#booking_book_and_pay',function()
    {
        if(validate('#booking_book_and_pay'))
        {
            var data = set_data(2,'pay');
            process_booking(data, this);
            //make_payment();
        }
    });

    $(document).on('click','#booking_book_and_bill',function()
    {
        if(validate('#booking_book_and_bill'))
        {
            var data = set_data(2,'bill');
            data.type = 7;
            process_booking(data, this);
        }
    });

    $(document).on('click','#booking_cancel',function(){
        if ($('#contact_booking_form_wrapper').length > 0) { // booking via contact page
            $('#contact_booking_form_wrapper').hide();
        } else {
            //booking via /admin/bookings
            window.location.href = "/admin/bookings";
        }
    });

    $(document).on('click','#booking_cancel_booking',function(ev)
    {
        ev.stopPropagation();
        var multiple_transaction = $('#multiple_transaction').val();
        console.log(multiple_transaction);
        if (multiple_transaction == 1)
        {
            show_cancel_booking_modal({booking_id:$("#booking_id").val()});
        }
        else
        {
            $('#modal_multiple_transaction').modal();
        }
    });

    $(document).on('click','#booking_cancel_booking_multiple',function(ev)
    {
        ev.stopPropagation();
        var booking_id = $(this).data("booking_id");
        var data = {};
        data.booking_id = booking_id;
        data.schedule_id = [];
        $("#booking-schedules-list .select-schedule input").each (function(){
            if (this.checked) {
                data.schedule_id.push(this.value);
            }
        });
        $.ajax({
                url:'/admin/bookings/show_cancel_booking_multiple',
                data:data,
                datatype:'json',
                type:'POST'
            })
            .success(function(results){
                $('#cancel_booking_multiple_modal').remove();
                $('body').append(results);

                $("#credit_to_family_autocomplete").on("change", function(){
                    if (this.value == "") {
                        $('[name=credit_to_family_id]').val('');
                    }
                });
                $("#credit_to_family_autocomplete").autocomplete({
                    source: function(data, callback){
                        $('[name=credit_to_family_id]').val('');
                        $.get("/admin/contacts3/ajax_get_all_families_ui",
                            data,
                            function(response){
                                callback(response);
                            });
                    },
                    open: function () {
                        $('[name=credit_to_family_id]').val('');
                    },
                    select: function (event, ui) {
                        $('[name=credit_to_family_id]').val(ui.item.id);
                    }
                });

                $('#cancel_booking_multiple_modal').modal();
                $('#cancel_booking_multiple_modal').on('hidden.bs.modal', function () {
                    $('#cancel_booking_multiple_modal').remove();
                })

                $("#cancel_booking_multiple_modal .cancel").on("click", function(){

                });
                $("#cancel_booking_multiple_modal .save").on("click", function(){
                    if ($('#cancel_booking_multiple_modal_form').find('.confirm:checked').length == 0) {
                        alert("Please select at least one schedule or delegate to cancel");
                        return false;
                    }

                    var data = $("#cancel_booking_multiple_modal_form").serialize();
                    $.post(
                        '/admin/bookings/cancel_booking_multiple',
                        data,
                        function (response) {
                            $("#cancel_booking_multiple_modal").modal("hide");
                            //$('[href="#family-member-bookings-tab"]').click();
                            var contact_id = $("#family-member-details-tab [name=id]").val();
                            load_contact(
                                contact_id,
                                {},
                                function(){
                                    $("[href='#family-member-bookings-tab']").click();
                                }
                            );

                            response.alerts.forEach(alert => {
                                $('body').add_alert(alert.message, alert.type);
                            });

                        }
                    )

                });
            });
    });

    $(document).on('click','#booking_delete_booking',function(){
        $("#delete_booking_id").val($("#booking_id").val());
        $("#delete_booking_form").submit();
    });

    $(document).on('click',"#schedule_view_times b",function(){
        $("#calendar_modal").modal();
    });

    $(document).on('click','#schedule_view_times .timetable_selection_prev',function(){
        $.post('/admin/bookings/prev_week',{datetime_start:$('#from_date').val(),datetime_end:$('#to_date').val()},function(data){
            data = $.parseJSON(data);
            $("#from_date").datepicker("update", data.datetime_start);
            $("#to_date").datepicker("update", data.datetime_end);
            $("#search_courses_schedules").click();
        });
    });

    $(document).on('click','#schedule_view_times .timetable_selection_next',function(){
        $.post('/admin/bookings/next_week',{datetime_start:$('#from_date').val(),datetime_end:$('#to_date').val()},function(data){
            data = $.parseJSON(data);
            $("#from_date").datepicker("update", data.datetime_start);
            $("#to_date").datepicker("update", data.datetime_end);
            $("#search_courses_schedules").click();
        });
    });

    $(document).on('click','#week_view_selector a',function(ev)
    {
        ev.preventDefault();
        if($(this).data('value') == "custom")
        {
            document.getElementById('from_date').removeAttribute('readonly');
            document.getElementById('to_date').removeAttribute('readonly');
        }
        else
        {
            document.getElementById('from_date').setAttribute('readonly','');
            document.getElementById('to_date').setAttribute('readonly','');
            $.post('/admin/bookings/get_times',{type:$(this).data('value')},function(data){
                data = $.parseJSON(data);
                $("#from_date").datepicker("update", data.datetime_start);
                $("#to_date").datepicker("update", data.datetime_end);

            });
        }
    });

    $(document).on('click', '#timetable_navigation i.icon-arrow-left', function()
    {
        var data = {
            contact_id:document.getElementById('contact_id').value,
            before:document.getElementById('timetable_to_date').value,
            after:document.getElementById('timetable_from_date').value
        };
        $.post('/admin/contacts3/ajax_prev_month/',data, function(result)
        {
            result = $.parseJSON(result);
            data.after = result.after;
            data.before = result.before;
            $('#family-member-timetable-tab').find('.content-area').load('/admin/contacts3/ajax_get_booking_timetable/',data, function(result)
            {
                // document.getElementById('timetable_view_area').innerHTML = result;
				show_booking_calendar();
                document.getElementById('edit_family_member_heading').scrollIntoView();
            });
        }),'json';
    });
    $(document).on('click', '#timetable_navigation i.icon-arrow-right', function()
    {
        var data = {
            contact_id:document.getElementById('contact_id').value,
            before:document.getElementById('timetable_to_date').value,
            after:document.getElementById('timetable_from_date').value
        };
        $.post('/admin/contacts3/ajax_next_month/',data, function(result)
        {
            result = $.parseJSON(result);
            data.after = result.after;
            data.before = result.before;
            $('#family-member-timetable-tab').find('.content-area').load('/admin/contacts3/ajax_get_booking_timetable/',data, function(result)
            {
                // document.getElementById('timetable_view_area').innerHTML = result;
				show_booking_calendar();
                document.getElementById('edit_family_member_heading').scrollIntoView();
            });
        }),'json';
    });

    $(document).on('change','#timetable_from_date',function()
    {
        var from_date = $("#timetable_from_date").val(),
            to_date = $("#timetable_to_date").val();
        if (from_date > to_date)
        {
            to_date = from_date;
            var date = new Date(clean_date_string(to_date));
            date.setMonth(date.getMonth() + 1);
            $("#timetable_to_date").val(date.dateFormat("Y-m-d"));
        }
    });

    $(document).on('change','#timetable_to_date',function()
    {
        var from_date = $("#timetable_from_date").val(),
            to_date = $("#timetable_to_date").val();
        if (from_date > to_date)
        {
            from_date = to_date;
            var date = new Date(clean_date_string(from_date));
            date.setMonth(date.getMonth() - 1);
            $("#timetable_from_date").val(date.dateFormat("Y-m-d"));
        }
    });

    $(document).on('click', '.minimize_button', function()
    {
        var target = this.getAttribute('data-minimize');
        var $target = $('#'+target);
        ($target.is(':visible')) ? $target.fadeOut() : $target.fadeIn();
    });

    $(document).on('click','#process_unmatched_booking', function()
    {
        Cookies.set("bookingActionButtonClicked", bookingActionButtonClicked);
        this.disabled = true;
        var $alerts = $('#booking_form_alerts');
        var modal = $(this).parents('.modal');
        var data = $.parseJSON(document.getElementById('data').value);
        var balance_data = {contact_id:data.contact_id};
        $.ajax({
            type: 'POST',
            url: '/admin/bookings/ajax_process_booking',
            data:data ,
            dataType: 'json'
        })
            .done(function(results)
            {
                if (typeof(results.status) == "undefined" && results.length) {
                    window.location = '/admin/bookings';
                } else if (results.status == 'success') {
                    if (window.location.pathname === '/admin/bookings/add_edit_booking' ||  window.location.pathname === '/admin/bookings/add_edit_booking/') {
                        window.location = '/admin/bookings?booking='+results.booking_id;
                    } else {
                        display_balance(
                            balance_data,
                            function (){
                                redirect_booking(results.case, results.transaction_id, results.amount, results.message, results.booking_id);
                            }
                        );
                    }
                }
                else
                {
                    this.disabled = false;
                    redirect_booking(results.case,results.transaction_id,results.amount,results.message, results.booking_id);
                    //$alerts.add_alert(results.message);
                }
                modal.modal('hide');
                $('.modal-backdrop').remove();
            })
            .error(function()
            {
                this.disabled = false;
                $alerts.add_alert('The server did not respond. Please try again later.', 'danger popup_box');
                remove_popbox();
            });
    });

    $(document).on('click','#make_billed_booking_modal_btn',function()
    {
        this.disabled = true;
        var data = $.parseJSON(document.getElementById('bill_data').value),
            $modal = $(this).parents('.modal'),
            $alerts = $('.alert-area');
        var balance_data = {
            contact_id:data.contact_id
        };
        data["bill_payer"] = $.parseJSON(document.getElementById('modal_make_billed_booking_select_bill_payer').value);
        if (data.bill_payer == '' || data.bill_payer == null)
        {
            $alerts.add_alert('Please select a Bill Payer from the list');
        }
        else
        {
            $.ajax({
                type: 'POST',
                url: '/admin/bookings/ajax_process_booking',
                data: data,
                dataType: 'json'
            })
                .done(function(results)
                {
                    if (results.status == 'success')
                    {
                        //$alerts.add_alert(results.message, 'success');
                        $('#make_billed_booking_modal').modal('hide');
                        setTimeout(function()
                        {
                            $('.modal-backdrop').remove();
                            var table = ($alerts.parents('#family-accounts-tab').length) ? 'family' : 'member';
                            display_balance(balance_data);
                            loadTransactions(
                                table,
                                function(){
                                    //$('#make_billed_booking_modal').modal('hide');
                                    if ( window.location.pathname === '/admin/bookings/add_edit_booking' ||  window.location.pathname === '/admin/bookings/add_edit_booking/')
                                    {
                                        window.location = '/admin/bookings';
                                        make_booking_payment(results.booking_id);
                                    } else {
                                        redirect_booking(results.case, results.transaction_id, results.amount, results.message, results.booking_id);
                                    }
                                }
                            );
                        }, 1500);
                    }
                    else
                    {
                        this.disabled = false;
                        $alerts.add_alert(results.message);
                    }
                    $modal.modal();

                })
                .error(function()
                {
                    this.disabled = false;
                    $alerts.add_alert('The server did not respond. Please try again later.', 'danger popup_box');
                    remove_popbox();
                });
        }
    });

    function from_date_change_handler ()
    {
        var from_date = $("#from_date").val(),
            to_date = $("#to_date").val();

        // Dates need to be compared in american format
        var from = from_date.split("-").reverse().join("-"),
            to   = to_date.split("-").reverse().join("-");

        if (from > to)
        {
            $(document).off('change','#to_date', to_date_change_handler);
            $("#to_date").datepicker("update", from_date);
            $(document).on('change','#to_date', to_date_change_handler);
        }
    }
    $(document).on('change','#from_date', from_date_change_handler);

    function to_date_change_handler()
    {
        var from_date = $("#from_date").val(),
            to_date = $("#to_date").val();

        // Dates need to be compared in american format
        var from = from_date.split("-").reverse().join("-"),
            to   = to_date.split("-").reverse().join("-");

        if (from > to)
        {
            $(document).off('change','#from_date', from_date_change_handler);
            $("#from_date").datepicker("update", to_date);
            $(document).on('change','#from_date', from_date_change_handler);
        }
    }
    $(document).on('change','#to_date', to_date_change_handler);

    $(document).on('click','.attend_all_timeslots',function()
    {
        var div = $(this).parents('div'), table;
        if (div.hasClass('confirmed_periods'))
        {
            table = $(this).parents('section').find("table").attr('id');
            $('#'+table+' tr td i').each(function()
            {
                current_schedule = $(this).closest('tr').data('schedule_id');
                var period_id = this.getAttribute('data-period_id');
                if ($(this).closest('i').hasClass('icon-remove'))
                {
                    $(this).closest('i').removeClass('icon-remove').addClass('icon-ok');
                    attend_period(period_id);
                    show_cancel(period_id);
                }
            });
            if ($('#booking_id').val() != "")
            {
                refresh_tables();
            }
            $('#schedule_id').val(current_schedule);
        }
        else
        {
            table = $(this).parents('fieldset').find("table").attr('id');
            var period_attending = parseInt($('#period_attending').val());
            $('#'+table+' tr td i').each(function()
            {
                if ($(this).closest('i').hasClass('icon-remove'))
                {
                    period_attending++;
                    $(this).closest('i').removeClass('icon-remove').addClass('icon-ok');
                }
            });
            $('#period_attending').val(period_attending);
            get_attending_cost()
            refresh_tables();
        }
    });

    $(document).on('click','.attend_no_timeslots',function()
    {
        var div = $(this).parents('div'), table;
        if (div.hasClass('confirmed_periods'))
        {
            table = $(this).parents('section').find("table").attr('id');
            $('#'+table+' tr td i').each(function()
            {
                current_schedule = $(this).closest('tr').data('schedule_id');
                var period_id = this.getAttribute('data-period_id');
                if ($(this).closest('i').hasClass('icon-ok'))
                {
                    $(this).closest('i').removeClass('icon-ok').addClass('icon-remove');
                    unattend_period(period_id);
                    show_register(period_id);
                }
            });
            if ($('#booking_id').val() != "")
            {
                refresh_tables();
            }
            $('#schedule_id').val(current_schedule);
        }
        else
        {
            table = $(this).parents('fieldset').find("table").attr('id');
            var period_attending = parseInt($('#period_attending').val());
            $('#'+table+' tr td i').each(function()
            {
                if ($(this).closest('i').hasClass('icon-ok'))
                {
                    $(this).closest('i').removeClass('icon-ok').addClass('icon-remove');
                    period_attending--;
                }
            });
            $('#period_attending').val(period_attending);
            get_attending_cost();
            refresh_tables();
        }
    });

    function bulkUpdateStartDateChanged()
    {
        var date = $(this).datepicker('getDate');
        var $div = $(this).parents("div.bulk_attend_update");

        $div.find('.bulk_attend_update_date_from').off("change", bulkUpdateStartDateChanged);
        $div.find('.bulk_attend_update_date_to').off("change", bulkUpdateEndDateChanged);
        $div.find('.bulk_attend_update_date_to').datepicker("remove");
        $div.find('.bulk_attend_update_date_to').datepicker({
            autoclose: true,
            orientation: "auto bottom",
            startDate: date,
        });
        $div.find('.bulk_attend_update_date_to').on("change", bulkUpdateEndDateChanged);
    }

    function bulkUpdateEndDateChanged()
    {
        var date = $(this).datepicker('getDate');
        var $div = $(this).parents("div.bulk_attend_update");

        $div.find('.bulk_attend_update_date_from').off("change", bulkUpdateStartDateChanged);
        $div.find('.bulk_attend_update_date_to').off("change", bulkUpdateEndDateChanged);
        $div.find('.bulk_attend_update_date_from').datepicker("remove");
        $div.find('.bulk_attend_update_date_from').datepicker({
            autoclose: true,
            orientation: "auto bottom",
            endDate: date,
        });

        $div.find('.bulk_attend_update_date_from').on("change", bulkUpdateStartDateChanged);
        $div.find('.bulk_attend_update_date_to').on("change", bulkUpdateEndDateChanged);
    }


    $(document).on('shown.bs.modal', '#bulk-update-modal, #display-schedule-bulk-update-modal', function() {

        $('.bulk_attend_update_date_from').datepicker({
            autoclose: true,
            orientation: "auto bottom",
        });

        $('.bulk_attend_update_date_to').datepicker({
            autoclose: true,
            orientation: "auto bottom",
        });

        $('.bulk_attend_update_date_from').on("change", bulkUpdateStartDateChanged);
        $('.bulk_attend_update_date_to').on("change", bulkUpdateEndDateChanged);
    });

    $(document).on('click','.bulk_attend_update_set',function () {
        var $fields = $(this).parents("fieldset, #bulk-update-modal");
        var from = $fields.find(".bulk_attend_update_date_from").val();
        var fromd = from != "" ? $fields.find(".bulk_attend_update_date_from").datepicker( 'getDate' ) : false;
        var to = $fields.find(".bulk_attend_update_date_to").val();
        var tod = to != "" ? $fields.find(".bulk_attend_update_date_to").datepicker( 'getDate' ) : false;
        var note = $fields.find(".bulk_attend_update_note").val();
        var attending = $fields.find(".bulk_attend_update_attending_yes").parent().hasClass("active");
        var days = [];
        $fields.find(".bulk_attend_update_days option").each(function(){
            if (this.selected) {
                days.push(this.value);
            }
        });


        var table = $(this).parents(".confirmed_periods_table_wrapper").length > 0 ? "confirmed_periods_table" : "period_booking_table";
        var period_attending = 0;
        $("#" + table + " > tbody > tr").each(function (){
            var schedule_id = parseInt($(this).data("schedule_id"));
            var period_id = parseInt($(this).data("period_id"));
            var date = new Date(clean_date_string($(this).data("datetime_start")));
            date.setMilliseconds(0);

            var update = true;

            if (fromd && tod && (fromd > date || tod < date)) {
                update = false;
            }
            if (update && days && days.length >0) {
                update = false;
                for (var i = 0 ; i < days.length ; ++i) {
                    if (days[i] == "all" || days[i] == date.dateFormat("l H:i")) {
                        update = true;
                        break;
                    }
                }
            }
            if (update) {
                var icon = 'icon-remove';
                if (attending) {
                    icon = 'icon-ok';
                }
                $(this).find("input[type=text]").val(note);
                $(this).find(".period_attend_icon, i").removeClass("icon-ok");
                $(this).find(".period_attend_icon, i").removeClass("icon-remove");
                $(this).find(".period_attend_icon, i").addClass(icon);

                if (current_booking_data && current_booking_data[schedule_id] && current_booking_data[schedule_id][period_id]) {
                    current_booking_data[schedule_id][period_id].attending = attending ? 1 : 0;
                    current_booking_data[schedule_id][period_id].note = note;
                }
            }

            if ($(this).find(".period_attend_icon").hasClass("icon-ok")){
                ++period_attending;
            }
        });
        $('#period_attending').val(period_attending);
        refresh_tables();
        $("#bulk-update-modal, #display-schedule-bulk-update-modal").modal("hide");
    });

    $(document).on('click','#add_credit_balance',function()
    {
        if ($('#add_credit_balance').find('input[name="credit_transaction"]:checked').val() == 'yes')
        {
            $('#cancel_booking_refund_details').show();
        }
        else
        {
            $('#cancel_booking_refund_details').hide();
        }
        $("#add_credit_to_type").click();
    });

    $(document).on('click','#add_credit_to_type',function()
    {
        $(".family_credit, .contact_credit").css("display", "none");
        if ($('#add_credit_to_type').find('input[name="journal_type"]:checked').val() == 'family')
        {
            $(".family_credit").css("display", "");
        }
        else
        {
            $(".contact_credit").css("display", "");
        }
    });

    $('.table-bookings .view-surveys').on('click', function (ev) {
        ev.preventDefault();
        var booking_id = $(this).attr('data-id');
        select_row(this);
        open_survey_list(booking_id);
    });

    $('.table-bookings .add-surveys').on('click', function (ev) {
        ev.preventDefault();
        var booking_id = $(this).attr('data-id');
        var row = $(this).parents('tr').eq(0);
        select_row(row);
        open_survey(booking_id);
    });
});

function capacity_check_failed(schedule_id, timeslot_id, response)
{
    if (response && response.suggestions.length > 0) {
        $("#alternative-schedules-warning-continue").removeClass("hidden");
        if (response.full) {
            $("#alternative-schedules-warning-continue").addClass("hidden");
        }
        $("#alternative-schedules-warning-continue")[0].booking_link = booking_link;
        var $tbody = $("#alternative-schedules-warning-modal #alternative-schedules tbody");
        $tbody.html("");
        var trs = "";
        for (var i = 0; i < response.suggestions.length; ++i) {
            var suggestion = response.suggestions[i];

            if (suggestion.id == schedule_id) { // skip same schedule
                continue;
            }

            trs += '<tr>';
            trs += '<td>' + suggestion.id + '</td>';
            trs += '<td>' + suggestion.building + ' / ' + suggestion.room + '</td>';
            trs += '<td>' + suggestion.trainer + '</td>';
            trs += '<td class="alternative-schedule-name">' + suggestion.name + '</td>';
            trs += '<td>' + suggestion.max_capacity + '</td>';
            trs += '<td>' + suggestion.booked + '</td>';
            trs += '<td><a class="select-alternative-schedule" data-alternative-schedule-id="' + suggestion.id + '">select</a></td>';
            trs += '</tr>';
        }
        $tbody.html(trs);
        $tbody.find("a.select-alternative-schedule").on("click", function () {
            var $tr = $(this).parents("tr");
            $("#select_schedule_name").val($tr.find(".alternative-schedule-name").html());
            $("#select_schedule_name_id").val($(this).data("alternative-schedule-id"));
            $("#search_courses_schedules").click();
            $("#alternative-schedules-warning-modal").modal("hide");
        });
        $("#alternative-schedules-warning-modal").modal("show");
    } else {
        $("#alert_schedule_full").modal();
    }
}

function attend_period(period_id)
{
    period_id = parseInt(period_id);
    if (current_booking_data[current_schedule] && current_booking_data[current_schedule][period_id])
    {
        current_booking_data[current_schedule][period_id].attending = 1;
    }
}

function unattend_period(period_id)
{
    period_id = parseInt(period_id);

    if (current_booking_data[current_schedule] && current_booking_data[current_schedule][period_id])
    {
        current_booking_data[current_schedule][period_id].attending = 0;
    }
}

function remove_schedule_from_booking(schedule_id)
{
    if (typeof current_booking_data[schedule_id] != "undefined")
    {
        delete current_booking_data[schedule_id];
    }
}

function set_distinct_day_times()
{
    var sections = ["#modal_display_schedule", "#time_slots_section_content"];
    for (var i = 0 ; i < sections.length ; ++i) {
        var day_times = {};
        var $section = $(sections[i]);
        $section.find("tr[data-datetime_start]").each (function(){
            if ($(this).hasClass("hide")) {
                return;
            }
            try {
                var dt = new Date(clean_date_string($(this).data("datetime_start")));
                day_times[dt.dateFormat("l H:i")] = {
                    day: dt.dateFormat("l"),
                    time: dt.dateFormat("H:i")
                };
            } catch (exc) {
                console.log(exc);
            }
        });
        $section.find(".bulk_attend_update_days option").remove();
        $section.find(".bulk_attend_update_days").append('<option value="all">All</option>');
        for (var dt in day_times) {
            $section.find(".bulk_attend_update_days").append(
                '<option value="' + day_times[dt].day + " " + day_times[dt].time + '"' +
                ' data-day="' + day_times[dt].day + '"' +
                ' data-time="' + day_times[dt].time + '">' +
                dt +
                '</option>'
            );
        }

        $section.find(".bulk_attend_update_days").multiselect('rebuild');
    }
}

function get_period_table_html(callback)
{
    $.post(
        '/admin/bookings/get_period_table_html',
        {
            periods: JSON.stringify(current_booking_data),
            booking_type: $("#modal_display_schedule").data("booking_type")
        },
        function (data) {
            data = $.parseJSON(data);
            $("#confirmed_periods_table").dataTable().fnDestroy();
            document.getElementById('confirmed_periods_table').getElementsByTagName('tbody')[0].innerHTML = data.html;
            $("#confirmed_periods_table").dataTable({"bPaginate": false, "bInfo": false, "bFilter": false});
            if (window.order_table_data == null) {
                window.order_table_data = {};
            }
            set_distinct_day_times();
            if (callback) {
                callback();
            }
        }
    );
}

/**
 * Order table for the schedule
 */
window.order_table_data = null;
function get_order_table_data(callback)
{
    var discounts = {};
    var row = null;
    for (var i in order_table_data) {
        row = order_table_data[i];
        discounts[row.id] = [];
        for (var j in row.discounts) {
            discounts[row.id].push(
                {
                    id: row.discounts[j].id,
                    custom: row.discounts[j].custom,
                    amount: row.discounts[j].custom == 1 ? row.discounts[j].amount : null,
                    ignore: row.discounts[j].ignore == 1 ? 1 : 0,
                    code: row.discounts[j].code,
                    memo: row.discounts[j].memo
                }
            );
        }
    }

    delegate_ids = [];
    $("#delegates-wrapper .delegate-id").each(function(){
        if(this.value != "") {
            delegate_ids.push(this.value);
        }
    });

    var booking_id = $("#booking_id").val();
    $("#booking-schedules-list td.select-schedule").addClass("hidden");
    $.post(
        '/admin/bookings/get_order_table_html',
        {
            booking           : current_booking_data,
            client_id         : $('#contact_id').val(),
            discounts         : discounts,
            booking_id        : booking_id,
            delegate_ids      : delegate_ids
        },
        function (data) {
            order_table_data = data;
            var total = 0;
            var discounts = 0;
            var subtotal = 0;
            var due_now = 0;
            var has_sessions_selected = false;

            var tbody = order_table_data.length ? '' : '<tr><td colspan="13">No orders to display</td></tr>';

            $("#booking-schedules-list tbody").html(tbody);
            var display_delegate_input = booking_id ? false : true;
            for (var i = 0 ; i < order_table_data.length ; ++i) {
                var classes = 0;
                var attending = 0;
                var row = order_table_data[i];

                try {
                    if (row.id > 0) {
                        if (row.details) {
                            if (row.details.is_group_booking == 1) {
                                display_delegate_input = true;
                            }
                        }
                        for (var ai in current_booking_data[row['id']]) {
                            ++classes;
                            if (current_booking_data[row['id']][ai].attending == 1) {
                                ++attending;
                            }
                        }
                        if (row.how_did_you_hear && $('#how_did_you_hear')) {
                            $('#how_did_you_hear').val(row.how_did_you_hear);
                        }
                        if (row.special_requirements && $('#special_requirements')) {
                            $('#special_requirements').text(row.special_requirements);
                        }
                        var due_sum = '';
                        if (row.outstanding) {
                            if (row.discount >= row.fee) {
                                due_sum = '0.00';
                            } else {
                                due_sum = row.outstanding;
                            }
                        } else {
                            due_sum = '-';
                        }

                        var tr = '<tr ' +
                                'data-schedule_id="' + row['id'] + '" ' +
                                'data-pre_pay="' + (row['prepay'] ? 'true' : 'false') + '" ' +
                                'data-amount="' + row['fee'] + '"' +
                                'data-allow_sales_quote="' + row['details']['allow_sales_quote'] + '"' +
                            '>';
                        tr += '<td data-label="" class="hidden select-schedule">' + (booking_id > 0 ? '<input type="checkbox" class="schedule_id" value="' + row['id'] + '" />' : '') + '</td>';
                        tr += '<td data-label="Category">' + row['details']['category'] + '</td>';
                        tr += '<td data-label="Schedule"><a href="/admin/courses/edit_schedule/?id=' + row['id'] + '" target="_blank">' + row['name'] + '</a></td>';
                        tr += '<td data-label="Type">' + (row['prepay'] ? 'Prepay' : 'PAYG') + '</td>';
                        tr += '<td data-label="Classes" class="classes">' + classes + '</td>';
                        tr += '<td data-label="Attedance" class="attended">' + attending + '</td>';
                        tr += '<td data-label="Created" class="created">' + row['created_date'] + '</td>';
                        tr += '<td data-label="Start date" class="starts">' + row['details']['start_date'] + '</td>';
                        tr += '<td data-label="Quantity" class="quantity">' + (row['number_of_delegates'] > 0 ? row['number_of_delegates'] : 1) + '</td>';
                        tr += '<td data-label="Fee" class="fee">' + row['fee'] + '</td>';
                        tr += '<td data-label="Discount" class="discount">' + row['discount'] + '</td>';
                        tr += '<td data-label="Next payment"> ' + (row['next_payment'] ? row['next_payment']['date'] : '') + ' </td>';
                        tr += '<td data-label="Due"> ' + due_sum + ' </td>';
                        tr += '<td data-label="Actions">';
                        if (row["can_add_discount"]) {
                            tr += '<button type="button" class="btn btn-default booking-discount-modal-display" data-toggle="modal" data-target="#booking-discount-modal">' + (row['discounts'].length > 0 ? 'View' : 'Add') + ' Discount</button>';
                        }
                        tr += '<button type="button" class="btn btn-default booking-schedule-edit-modal-display" data-toggle="modal" data-target="#booking-schedule-edit-modal">edit</button>' +
                            (booking_id ? '' : '<button type="button" class="btn btn-default booking-schedule-remove-modal-display" data-toggle="modal" data-target="#booking-schedule-remove-modal">remove</button>') +
                            '</td>';
                        tr += '</tr>';
                        $("#booking-schedules-list tbody").append(tr);
                        if (booking_id > 0) {
                            $("#booking-schedules-list .select-schedule").removeClass("hidden");
                        }

                        if (row['prepay']) {
                            var quantity = parseFloat(row['number_of_delegates']);
                            if (isNaN(quantity) || quantity == 0) {
                                quantity = 1;
                            }

                            if (row.details.charge_per_delegate == '0') {
                                quantity = 1;
                            }

                            subtotal += parseFloat(row['fee'] * quantity);
                            discounts += parseFloat(row['discount']);
                            total += parseFloat(row['fee'] * quantity) - parseFloat(row['discount']);
                            if (booking_id > 0) {
                                due_now += row['outstanding'];
                            }
                            if (due_now < 0) {
                                due_now = 0;
                            }
                        }

                        has_sessions_selected = true;

                    } else {
                        //if (row['prepay']) {
                            $("#booking-schedules-list .bdiscount").html(row['discount']);
                            discounts += parseFloat(row['discount']);
                            total -= parseFloat(row['discount']);
                            if (isNaN(parseInt(booking_id))) {
                                due_now = total;
                            }
                            if (due_now < 0) {
                                due_now = 0;
                            }
                        //}
                    }
                } catch (exc) {
                    console.log(exc);
                }
            }
            //$("#delegates-wrapper").addClass("hidden");
            if (display_delegate_input) {
                //$("#delegates-wrapper").removeClass("hidden");
            }

            var allow_sales_quote = $('#booking-schedules-list').find('[data-allow_sales_quote="1"]').length > 0;
            $('#booking-create_sales_quote').toggleClass('hidden', !allow_sales_quote);

            // User can only use the action buttons, if a session has been selected
            $('#booking-form-no_session_selected').toggleClass('hidden', has_sessions_selected);
            $('#booking-form-actions').find('.btn').prop('disabled', !has_sessions_selected);

            $(".payment_details_column .subtotal").html(subtotal.toFixed(2));
            $(".payment_details_column .discounts").html("-" + discounts.toFixed(2));
            $(".payment_details_column .total").html(due_now.toFixed(2));

            $("#booking-schedules-list .booking-discount-modal-display").off("click", booking_discount_modal_display_clicked);
            $("#booking-schedules-list .booking-discount-modal-display").on("click", booking_discount_modal_display_clicked);
            if (callback) {
                callback(data);
            }
        }
    );
}
function get_order_table_html()
{
    get_order_table_data();
            /*$("#booking-schedules-list tbody").html(data);
            if(document.getElementById('booking_id') && document.getElementById('booking_id').value > 0)
            {
                $('#schedules_tbody i').remove();
            }*/
            //get_offers_and_discounts();
}

function booking_discount_modal_display_clicked()
{
    var schedule_id = $(this).parents("tr").data('schedule_id');
    booking_discount_modal_fill(schedule_id);

}

function booking_discount_modal_fill(schedule_id)
{
    var $modal = $("#booking-discount-modal");
    $modal.data('schedule_id', schedule_id);
    $modal.find(".memo").val("");
    if (schedule_id == "") {
        schedule_id = null;// booking level discount
    }
    var row = null;
    for (var i in order_table_data) {
        if (order_table_data[i].id == schedule_id) {
            row = order_table_data[i];
            break;
        }
    }
    if (schedule_id == null) {
        $modal.find(".modal-title span").html("All schedules");
        $modal.find(".schedule-title").html("All schedules");
        $modal.find(".fee").html(row['fee']);
    } else {
        $modal.find(".modal-title span").html(row['details']['category'] + ', ' + row['name'] + ', ' + row['details']['start_date']);
        $modal.find(".schedule-title").html(row['details']['schedule']);
        $modal.find(".fee").html(row['fee']);
    }

    var $table = $modal.find(".discounts-table");
    var $tbody = $table.find("tbody");

    $tbody.find(">tr.offer").remove();
    $tbody.find(".add-custom .custom").val("");
    $tbody.find(".add-custom .negative").val("");
    $tbody.find(".add-custom .balance").html("");

    var balance = row.fee;
    var discounts = 0;
    var price = row.fee;
    for (var i in row.discounts) {
        if (row.discounts[i].custom != 1) {
            if (row.discounts[i].ignore != 1) {
                balance -= row.discounts[i].amount;
            }
            discount_add_row($tbody, row.discounts[i], balance);
        }
    }
    for (var i in row.discounts) {
        if (row.discounts[i].custom == 1) {
            if (row.discounts[i].ignore != 1) {
                balance -= row.discounts[i].amount;
            }
            $tbody.find(".add-custom .custom").val(row.discounts[i].amount);
            $tbody.find(".add-custom .negative").val("-" + row.discounts[i].amount);
            $tbody.find(".add-custom .balance").html('&euro;' + balance);
            $modal.find(".memo").val(row.discounts[i].memo);
        }
    }

    discounts = price - balance;

    $modal.find('.subtotal').html(row['fee']);
    $modal.find("#apply-discount-price").val(price.toFixed(2));
    $modal.find("#apply-discount-discounts").val(discounts.toFixed(2));
    $modal.find("#apply-discount-subtotal").val(balance.toFixed(2));
    $modal.find('tr.offer .toggle').on('click', function(){
        var tr = $(this).parents('tr');
        if (tr.hasClass('ignored_discount')) {
            tr.removeClass('ignored_discount');
        } else {
            tr.addClass('ignored_discount');
        }
    });

    $modal.find('.add-coupon input.coupon').autocomplete(
        {
            source: function (request, response) {
                $.getJSON(
                    "/admin/bookings/coupon_autocomplete", {
                        term: $modal.find('.add-coupon input.coupon').val()
                    },
                    response
                );
            },
            select:function (event, ui)
            {
                var item = ui.item;
                $("tr.add-coupon").data("discount_id", item.id);
            },
            minLength: 1
        }
    );
}

function discount_add_row($tbody, discount, balance)
{
    var tr = '<tr class="offer ' + (discount.code ? 'coupon' : '') + (discount.custom == 1 ? 'custom' : '') + (discount.ignore == 1 ? ' ignored_discount' : '') + '" data-discount_id="' + discount.id + '" data-code="' + discount.code +'">';
    tr += '<td>' + discount.title + '</td>';
    if (discount.code) {
        tr += '<td><button type="button" class="btn btn-default remove">Remove</button></td>';
    } else {
        tr += '<td><button type="button" class="btn btn-default toggle">Remove</button></td>';
    }
    tr += '<td data-discount_id="' + discount.id + '">' +
        '<label class="input-group">' +
        '<span class="sr-only">Discount amount</span>' +
        '<span class="input-group-addon">&euro;</span>' +
        '<input type="text" readonly="readonly" class="form-control" value="-' + discount.amount + '" style="width: 100px;" />' +
        '</label>' +
        '</td>';
    tr += '<td>&euro;' + (balance > 0 ? balance.toFixed(2) : '') + '</td>';
    tr += '</tr>';
    $(tr).insertBefore($tbody.find("tr.add-coupon"));
}

function apply_discounts(nohide)
{
    var $modal = $("#booking-discount-modal");
    var schedule_id = $modal.data('schedule_id');
    if (schedule_id == "") {
        schedule_id = null; // booking level discount
    }
    var row = null;
    for (var i in order_table_data) {
        if (order_table_data[i].id == schedule_id) {
            row = order_table_data[i];
            break;
        }
    }
    if (row) {
        row.discounts = [];
        var $table = $modal.find(".discounts-table");
        var $tbody = $table.find("tbody");
        $tbody.find(">tr.offer").each(function(){
            var tr = $(this);
            var discount = {};
            discount.id = tr.data("discount_id");
            discount.custom = tr.data("custom");
            if (discount.custom == 1) {
                discount.id = "custom";
            }
            discount.code = tr.data("code");
            discount.ignore = tr.hasClass("ignored_discount") ? 1 : 0;
            discount.amount = discount.custom == 1 ? tr.find(".custom").val() : null;
            discount.memo = "";
            row.discounts.push(discount);
        });

        $tbody.find(">tr.add-custom").each(function(){
            var tr = $(this);
            var discount = {};
            discount.id = "custom";
            discount.custom = 1;
            discount.code = "";
            discount.ignore = tr.hasClass("ignored_discount") ? 1 : 0;
            discount.amount = parseFloat(tr.find(".custom").val());
            discount.memo = $("#booking-discount-modal .memo").val();
            if (discount.amount > 0) {
                row.discounts.push(discount);
            }
        });
    }

    get_order_table_data(function(data){
        booking_discount_modal_fill(schedule_id);
        if (!nohide) {
            $modal.modal('hide');
        }
    });
}

$(document).on("click", "#booking-discount-modal .apply", function (){apply_discounts();});
$(document).on("click", "#booking-discount-modal button.toggle", function (){apply_discounts(true);});

$(document).on("click", "#booking-discount-modal .add-coupon button", function(){
    var $modal = $("#booking-discount-modal");
    var schedule_id = $modal.data('schedule_id');

    var discount = {};
    discount.custom = 0;
    discount.code = $(this).parents("tr").find('.coupon').val()
    discount.id = $(this).parents("tr").data("discount_id");
    discount.amount = "";
    discount.title = discount.code;
    $.post(
        '/admin/bookings/validate_coupon',
        {
            discount: discount,
            schedule_id: schedule_id
        },
        function (response) {
            if (response.success) {
                discount_add_row($("#booking-discount-modal .discounts-table tbody"), discount, "")
            } else {
                alert("invalid coupon code");
            }
            $(this).parents("tr").find('.coupon').val("");
            apply_discounts(true);
        }
    )
});

$(document).on("click", "#booking-discount-modal .add-custom button", function(){
    var $modal = $("#booking-discount-modal");
    var schedule_id = $modal.data('schedule_id');

    var discount = {};
    discount.custom = 1;
    discount.code = ""
    discount.id = "custom";
    discount.amount = $(this).parents("tr").find('.custom').val();
    discount.title = "Custom";
    var max_discount = parseFloat($("#booking-discount-modal .fee").html());
    //discount_add_row($("#booking-discount-modal .discounts-table tbody"), discount, "")
    /*var row = null;
     for (var i in order_table_data) {
     if (order_table_data[i].schedule_id == schedule_id) {
     row = order_table_data[i];
     row.discounts.push({code: $(this).parents("tr").find('.coupon').val()});
     break;
     }
     }
     */

	if ( ! discount.amount.match(/^\d+(\.\d+)?$/)) {
        alert('Discount must be a numeric value.');
    } else if (max_discount < parseFloat(discount.amount)){
        alert('Discount can not be more than fee');
	} else {
		$(this).parents("tr").find('.negative').val("-" + discount.amount);
        apply_discounts(true);
	}

});

$(document).on("click", "#booking-discount-modal .offer button.remove", function(){
    $(this).parents("tr").remove();
    var $modal = $("#booking-discount-modal");
    var schedule_id = $modal.data('schedule_id');
});

function get_offers_and_discounts()
{
    return;
    var booking_id = $('#booking_id').val();
    $.post('/admin/bookings/get_offers_and_discounts', {booking_id:booking_id} , function(data)
    {
        var html = '';
        var results = JSON.parse(data);
        if (results.cart_offers != undefined)
        {
            var ignored = '';
            $.each(results.cart_offers, function()
            {
                ignored = (results.ignored_discounts && results.ignored_discounts.indexOf(this.id) >= 0);
                html += '<tr id="offer_id_'+this.id+'"'+' class="discounts '+(ignored ? 'ignored_discount"' : '')+'" data-offer_id="'+this.id+'">' +
                    '<td>'+this.title+'</td>' +
                    '<td>&minus;&nbsp;&euro;<span class="offer_amount">'+this.amount+'</span></td>' +
                    '<td class="remove_cell"><i class="icon-'+(ignored ? 'ban-circle' : 'ok')+'"></i></td>' +
                    '</tr>';
            });
        }
        document.getElementById('offers_tbody').innerHTML = html;
        calculate_total();
    });
}

function refresh_tables()
{
    get_period_table_html(
        function(){
            get_order_table_html();
            $(".confirmed_periods_table_wrapper .bulk_attend_update .datepicker").datepicker();
        }
    );
}

function confirm_bookings()
{
    return true;
}

function calculate_total()
{
    var net_total = 0;
    var prepay = 0;
    var payg = 0;
    var discounts = 0;
    var total_amount;
    var confirm,is_prepay,is_payg;

    $("#schedules_tbody").find("tr").each(function(){
        if($(this).data('pre_pay') == true)
        {
            prepay = prepay + parseFloat($(this).data('amount'));
            is_prepay = true;
        }
        else
        {
            payg = payg + parseFloat($(this).data('amount'));
            is_payg = true ;
        }
    });
    confirm = is_payg && ! is_prepay ;

    $("#offers_tbody").find(".offer_amount").each(function()
    {
        if ( ! $(this).parents('tr').hasClass('ignored_discount'))
        {
            discounts += parseFloat(this.innerHTML);
        }
    });

    if(document.getElementById('custom_discount').value != '')
    {
        discounts+= parseFloat(document.getElementById('custom_discount').value);
    }
    net_total = prepay + payg ;
    total_amount = prepay + payg - discounts;

    prepay = Math.round(prepay * 100)/100;
    payg = Math.round(payg * 100)/100;
    total_amount = Math.round(total_amount * 100)/100;

    $("#net_total").html(net_total);
    $("#payg_total").html(payg);
    $("#payg_total").val(payg);
    $("#prepay_total").html(prepay);
    $("#prepay_total").val(prepay);
    $("#total_amount").html(total_amount);
    $("#amount").val(total_amount);
    $('#discount_total').val(discounts);
    //document.getElementById('booking_amount').value = parseFloat(document.getElementById('total_amount').value);

    // Remove book and pay foy PAYG course
    if (confirm)
    {
        $('#booking_book_and_pay').hide();
        $('#booking_book').html('Confirm');
    }
    else
    {
        $('#booking_book_and_pay').show();
        $('#booking_book').html('Book');
    }
}

function disable_registration_button()
{
    var $period_table = $("#period_table");
    $("#period_booking_table").find("tbody td .icon-ok").closest('tr').each(function()
    {
        $period_table.find("[data-period_id='"+this.getAttribute('data-period_id')+"'] button")
            .removeClass('register_place')
            .addClass('cancel_place')
            .removeClass('btn-success')
            .addClass('btn-danger')
            .text('Cancel')
            .data('schedule',current_schedule)
            .attr('data-show',false)
            .attr('data-toggle','');
    });
}

function enable_registration_button(schedule_id)
{
    $("#period_table").find("tbody tr td div[data-schedule_id='"+schedule_id+"'] button")
        .addClass('register_place')
        .removeClass('cancel_place')
        .addClass('btn-success')
        .removeClass('btn-danger')
        .text('Register')
        .data('schedule',schedule_id)
        .attr('data-show',true)
        .attr('data-toggle','modal');
}

/* Custom form validation function add class="validate[funcCall[validate_schedule_location_category]]" */
function validate_schedule_location_category(field, rules, i, options) {
	var schedule = $('#select_schedule_name_id').val();
	var location = $('#select_location').val();
	var category = $('#select_category').val();

	if ( ! schedule && ! (location && category)) {
		rules.push('required');
		return 'You must select a schedule or both a location and category';
	}
}

// Code to be ran after the booking form has been loaded dynamically
function booking_form_loaded(new_booking)
{
    window.$booking_delegate_tr = $("#delegates-wrapper tbody > tr.hidden.delegate-row-template");
    //window.$booking_delegate_tr.remove();

    $("#select_delegate").autocomplete({
        source:"/admin/bookings/find_customer",
        select:function (event, ui)
        {
            var item = ui.item;
            window.delegate_add_to_list = item;
        },
        messages: {
            noResults: '',
            results: function() {}
        },
        minLength: 1
    });

    /* Add a delegate to an existing booking */
    // Set up the autocomplete
    $('#booking-delegates-list-new-input').autocomplete({
        source:"/admin/bookings/find_customer",
        select:function (event, ui)
        {
            $('#booking-delegates-list-new-id')
                .val(ui.item.id)
                .data('name', ui.item.first_name+' '+ui.item.last_name)
                .data('organisation_name', ui.item.organisation_contact_name);
        },
        messages: {
            noResults: '',
            results: function() {}
        },
        minLength: 1
    });

    // Add to the table when the "add" button is clicked
    $('#booking-delegates-list-new-btn').on('click', function() {
        const $input = $('#booking-delegates-list-new-input');
        const $selected = $('#booking-delegates-list-new-id');
        const selected_id = $selected.val();

        // Do nothing if the button is clicked while the input is empty.
        if ($input.val() == '' || selected_id == '') {
            return false;
        }

        // Get a list of existing delegate IDs.
        let existing_ids = [];
        $('.delegates-wrapper .booking-delegates-list tbody .delegate-id').each(function(i, element) {
            if (element.value) {
                existing_ids.push(element.value);
            }
        });

        if (existing_ids.indexOf(selected_id) > 0) {
            // If the delegate has already been added to the booking, show a notice and don't continue.
            $('#delegate-duplicate-modal').modal();
            $input.val('');
            $selected.val('');
            return false;
        }

        // Clone the template, populate it with data for the new delegate.
        const $clone = $('#booking-delegates-list-template').clone();
        $clone.removeAttr('id');
        $clone.attr('data-id', selected_id).data('id', selected_id);
        $clone.find('.delegate-id').val(selected_id);
        $clone.find('.delegate-name').text($selected.data('name'));
        $clone.find('.delegate-organisation_name').text($selected.data('organisation_name'));

        // Add the cloned template to the list.
        $(this).parents('.delegates-wrapper')
            .find('#booking-delegates-list tbody')
            .append($clone);

        // Clear the typeselect.
        $input.val('');
        $selected.val('');
    });

    /* Remove a delegate from an existing booking */
    // Ensure that the ID of the delegate is remembered when the modal is opened
    $('#delegate-remove-modal').on('show.bs.modal', function(ev) {
        const id = $(ev.relatedTarget).parents('tr').data('id')
        $('#delegate-remove-btn-confirm').data('id', id);
    });

    // Remove from the table, when the user confirms the removal
    $('#delegate-remove-btn-confirm').on('click', function() {
        const id = $(this).data('id');
        $(this).parents('.delegates-wrapper')
            .find('#booking-delegates-list tr[data-id="'+id+'"]')
            .remove();

        $('#delegate-remove-modal').modal('hide');
    });

    var table = $('#booking_schedules_list_table');
    if (table.length > 0 && new_booking) {
        table.dataTable().fnDestroy();
        initTable('#booking_schedules_list_table');

        $('#booking_schedules_list_table').add_column_multiselects();

        // Search by individual columns
        table.find('.search_init').on('change', function () {
            table.dataTable().fnFilter(this.value, table.find('tr .search_init').index(this));
        });
    }

    // Change the checkbox or click the button when anywhere on the row is clicked
    table.on('click', 'tbody tr', function(ev) {
        var $element = $(ev.target);

        // Unless the user clicks a link or form field (including the checkbox/button itself to avoid double changing)
        if (!$element.is('a, label, button, :input') && !$element.parents('a, label, button, :input')[0]) {

            // If this is in single mode, click the button
            if ($(this).parents('table').hasClass('mode--single')) {
                $(this).find('.booking-schedules-select-single').click();
            }
            // If the user is in multiple mode, change the checkbox
            else {
                var $checkbox = $(this).find('[type="checkbox"][name="schedule_id[]"]');
                $checkbox[0].checked = !$checkbox[0].checked;
                $checkbox.trigger('change');
            }
        }
    });

    function initTable(id) {
        $(id).on("change", "tbody input[type=checkbox][name='schedule_id[]']", function(){
            var table = $(this).parents("table")[0];
            if (!table.selected_ids) {
                table.selected_ids = {};
            }
            table.selected_ids[this.value] = this.checked;
        });

        $(id).on('click', 'tbody .booking-schedules-select-single', function() {
            var table = $(this).parents("table")[0];
            var schedule_id = $(this).data('id');

            table.selected_ids = {};
            table.selected_ids[schedule_id] = true;

            check_schedule_capacity(
                schedule_id,
                null,
                function() {
                    get_recurring_schedule({schedule_id: schedule_id});

                    $('.panel-heading[data-target="#booking-form-section-payment"]')[0].scrollIntoView();
                },
                function(schedule_id, timeslot_id, response) {
                    capacity_check_failed(schedule_id, timeslot_id, response);
                }
            );
        });

        var filter_arr = ["search_id", "search_course", "search_name", "search_category", "search_fee_amount", "search_repeat_name", "search_start_date", "search_location", "", "search_trainer", "", "search_date_modified"];
        var ajax_source = "/admin/bookings/search_schedules_datatable";
        var settings = {
            "aaSorting": [[ 11, "desc"]],
            "aLengthMenu"     : [10],
            "aoColumns": [
                // Update the aaSorting value to the "last_modified" column number, when adding/removing/reordering columns
                {"mDataProp": "id", "bSearchable": true, "bSortable": true},
                {"mDataProp": "location", "bSearchable": true, "bSortable": true},
                {"mDataProp": "subject", "bSearchable": true, "bSortable": true},
                {"mDataProp": "course", "bSearchable": true, "bSortable": true},
                {"mDataProp": "schedule", "bSearchable": true, "bSortable": true},
                {"mDataProp": "category", "bSearchable": true, "bSortable": true},
                {"mDataProp": "year", "bSearchable": true, "bSortable": true},
                {"mDataProp": "level", "bSearchable": true, "bSortable": true},
                {"mDataProp": "trainer", "bSearchable": true, "bSortable": true},
                {"mDataProp": "day", "bSearchable": true, "bSortable": true},
                {"mDataProp": "datetime_start", "bSearchable": false, "bSortable": false},
                {"mDataProp": "fee", "bSearchable": false, "bSortable": true},
                {"mDataProp": "payment_type", "bSearchable": false, "bSortable": true},
                {"mDataProp": "number_of_bookings", "bSearchable": false, "bSortable": true},
                {"mDataProp": "timeslots_counts", "bSearchable": false, "bSortable": false},
                {"mDataProp": "action", "bSearchable": false, "bSortable": false}
            ],
            "bStateSave" : false,
            "fnInitComplete": function(oSettings, json) {
                var cols = oSettings.aoPreSearchCols;
                for (var i = 0; i < cols.length; i++) {
                    var value = cols[i].sSearch;
                    if (value.length > 0) {
                        $("#"+filter_arr[i]).val(value);
                    }
                }
                // The class hidden is enabled when user enters the page
                var $schedule_range_dropdown = $('.schedule-range-dropdown').removeClass('hidden');
                var $select_type = $('#booking-form-select_type-wrapper').removeClass('hidden');

                $(id + '_filter').append($schedule_range_dropdown).append($select_type);

                $('[name="timeslots_range"]').on('change', function(){
                    $(id).dataTable()._fnAjaxUpdate();
                    // Show the selected item inside the dropdown toggle.
                    $('#booking-time_range-toggle').html($('[name="timeslots_range"]:checked + span').html());
                });
            },
            "oLanguage"      : { "sInfoFiltered": "" },
            "bAutoWidth"     : false,
            "bDestroy": true,
            "sPaginationType" : "bootstrap",
            "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
                if ($('[name="timeslots_range"]:checked').length) {
                    aoData.push({name: "timeslots_range", value:$('[name="timeslots_range"]:checked').val()});
                }
                if ($("#booking_qty_type").val() == "delegates") {
                    aoData.push({name: "is_group_booking", value: 1});
                }
                aoData.push({name: "exclude_cancelled", value:1});
                oSettings.jqXHR = $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aoData,
                    "success": function (a, b, c) {
                        fnCallback(a, b, c);

                        var table = $(id)[0];
                        if (!table.selected_ids) {
                            table.selected_ids = {};
                        }
                        $(id).find("tbody input[type=checkbox][name='schedule_id[]']").each(function(){
                            this.checked = table.selected_ids[this.value] ? true : false;
                        });
                    }
                });
            }
        };
        return $(id).ib_serverSideTable(ajax_source, settings);
    }

    // Toggle visibility of content depending on whether it is in single or multiple schedule booking mode
    $(document).on('change', '.booking-form-select_type', function() {
        var type = $('.booking-form-select_type:checked').val();
        $('#booking_schedules_list_table')
            .toggleClass('mode--single',   (type == 'single'))
            .toggleClass('mode--multiple', (type == 'multiple'))
    });

    $('#bulk-update2-starts').datetimepicker({
        format: ibcms.date_format.replace("dd", "d").replace("mm", "m").replace("yyyy", "Y"),
        timepicker: false,
        onShow:function( ct ){
            this.setOptions({

            })
        },
        onSelectDate:function(dp){
            $('#bulk-update2-starts').data("date", dp.getFullYear() + "-" + (dp.getMonth() + 1) + "-" + dp.getDate());
        }
    });

    $('#bulk-update2-ends').datetimepicker({
        format: ibcms.date_format.replace("dd", "d").replace("mm", "m").replace("yyyy", "Y"),
        timepicker: false,
        onShow:function( ct ){
            this.setOptions({

            })
        },
        onSelectDate:function(dp){
            $('#bulk-update2-ends').data("date", dp.getFullYear() + "-" + (dp.getMonth() + 1) + "-" + dp.getDate());
        }
    });

    $('.select_course_dropdown').combobox();
    $('#select_subject').combobox();

    $('.multiple_select3').each(function() {
        $(this).next('div.btn-group').attr('id', 'multiselect_' + this.id).removeClass('validate[required]');
    });
    var form = $('#kes_booking_edit_form');
    form.validationEngine('attach', { prettySelect : true, usePrefix: 'multiselect_' });
    form.find('.btn[data-content]').popover({placement:'top',trigger:'hover'});

    contact_id = $('#contact_id');
    try
    {
        var periods_table = $('#confirmed_periods_table');
        periods_table.dataTable().fnDestroy();
        periods_table.dataTable({"bPaginate": false, "bInfo": false, "bFilter": false});
    }
    catch(error)
    {
        console.log(error.message);
    }

    if(document.getElementById('booking_id') && document.getElementById('booking_id').value > 0)
    {
        current_booking_data = $.parseJSON(document.getElementById('booking_items').value);
        refresh_tables();
    }
    else
    {
        current_booking_data = {};
    }

    if(document.getElementById('additional_booking_data') && document.getElementById('additional_booking_data').value.length > 0)
    {
        additional_booking_data = $.parseJSON(document.getElementById('additional_booking_data').value);
    }
    else
    {
        additional_booking_data = {};
    }

    var booking_status = 0 ;
    if ($('#booking_status').length)
    {
        booking_status = document.getElementById('booking_status').value ;
    }
    if($('#booking_id').length)
    {
        booking_id = $('#booking_id').val();
    }
    if (booking_status > 1 && $("[name=edit_booking_id]").val() == "")
    {
        $('#timetable_filters_fieldset, #booking-form-panel-timetable').hide();
    }

    $("#select_schedule_name").on("keypress", function(e){
        if (e.keyCode == 13) {

        } else {
            $('#select_schedule_name_id').val("");
        }
    });

    $("#select_schedule_name").autocomplete(
    {
        source:function(request, response)
        {
            $.getJSON(
                "/admin/bookings/find_schedule", {
                    location:$('#select_location').val(),
                    category:$('#select_category').val(),
                    year:$('#select_year').val(),
                    term:document.getElementById('select_schedule_name').value
                },
                response
            );
        },
        minLength: 1,
        select: function(event, ui)
        {
            $('#select_schedule_name_id').val(ui.item.id).trigger('change');
            $('#select_location').val("").trigger('change');
            $('#select_category').val("").trigger('change');
            $('#select_year').val("").trigger('change');

            var start_date = new Date(clean_date_string(ui.item.start_date));
            var now = new Date();
            if (now > start_date) {
                start_date = now;
            }
            $('#from_date').val(start_date.dateFormat("d-m-Y"));
            start_date.setDate(start_date.getDate() + 28);
            $('#to_date').val(start_date.dateFormat("d-m-Y"));
            //$('#select_location'        ).val(ui.item.location_id).trigger('change');
			//$('#select_category'        ).val(ui.item.category_id).trigger('change');
			//$('#select_year'            ).val(ui.item.year_id    ).trigger('change');
            // $('.select_course_dropdown').multiselect('rebuild');
        },
        messages:
        {
            noResults: '',
            results: function() {}
        }
    });

    setTimeout(function()
    {
        $('#select_location').trigger('change');
    }, 500);

    $("#select_course_name").on('change', function()
    {
        if (this.value.trim() == '')
        {
            document.getElementById('select_course_name_id').value = '';
        }
    });

    $('#custom_discount, #coupon_discount_input').on('keyup', function()
    {
        if (this.etimeout) {
            clearTimeout(this.etimeout);
        }
        this.etimeout = setTimeout(refresh_tables, 500);
    });

    $('#custom_discount, #coupon_discount_input').on('blur', function()
    {
        if (this.etimeout) {
            clearTimeout(this.etimeout);
        }
        refresh_tables();
    });

    $("#from_date").datepicker({format: 'dd-mm-yyyy',orientation:'bottom'});
    $("#to_date").datepicker({format:'dd-mm-yyyy',orientation:'bottom',minDate:7});

    $('#kes_booking_edit_form').find('.btn-group [data-content]').popover({placement:'right',trigger:'hover'});

    $(".linked-contact-booking-autocomplete").autocomplete(
        {
            source: function (request, response) {
                $.getJSON(
                    "/admin/contacts3/find_contact", {
                        term: $(this.element).val(),
                        contact_type: ($(this.element).attr('contact-type') != null) ? $(this.element).attr('contact-type') : false
                    },
                    response
                );
            },
            select: function (event, ui) {
                $(this).siblings('.linked-contact-id').val(ui.item.id);
            }
        });
}

window.too_many_discount_ignore = false;
var validatelastbutton = null;
function validate(clicked_button)
{
    validatelastbutton = clicked_button;
    var valid = false;
    if ($("#booking_qty_type").val() == "multiple" && $("#students-wrapper tbody tr").length > 0) {
        valid = true;
    } else {
        if (document.getElementById('booking_contact_id')) {
            valid = !(document.getElementById('booking_contact_id').value == '');
        }
        if (!valid && document.getElementById('select_contact')) {
            valid = !($('#contact_id').val() == '');
            if (!valid) {
                $('#alert_no_contact').modal();
            }
        }
    }
    if (count_discounts_used() > 1 && window.too_many_discount_ignore == false) {
        valid = false;
        $('#alert_too_many_discount').modal();
    }
    return valid;
}

$(document).on("click", "#alert_too_many_discount .btn.continue", function(){
    window.too_many_discount_ignore = true;
    $(validatelastbutton).click();
});

function count_discounts_used()
{
    var used_discounts = 0 ;
    $("#offers_tbody").find(".offer_amount").each(function()
    {
        if ( ! $(this).parents('tr').hasClass('ignored_discount'))
        {
            used_discounts ++;
        }
    });
    if ( $('#custom_discount').val() != '' && $('#custom_discount').val() != 0)
    {
        used_discounts ++;
    }
    return used_discounts;
}

function add_periods_to_booking()
{
    // get the periods selected from the list.
    var periods = $('#period_booking_table').find('tbody tr');
    // current_booking_data = {};
    var schedule_id,
        period_id,
        attending,
        note;

    var days = {};

    $.each(periods, function(index, row)
    {
        schedule_id = row.getAttribute('data-schedule_id');
        period_id   = row.getAttribute('data-period_id');
        prepay      = row.getAttribute('data-prepay');
        fee         = row.getAttribute('data-fee');
        attending   = $(row.getElementsByClassName('period_attend_icon')[0]).hasClass('icon-remove') ? 0 : 1;
        note        = row.getElementsByClassName('period_booking_note')[0].value;

        if (typeof current_booking_data[schedule_id] == 'undefined')
        {
            current_booking_data[schedule_id] = {};
        }

        if (typeof current_booking_data[schedule_id][period_id] == 'undefined')
        {
            current_booking_data[schedule_id][period_id] = {};
        }

        current_booking_data[schedule_id][period_id].attending = attending;
        current_booking_data[schedule_id][period_id].note      = note;
        current_booking_data[schedule_id][period_id].prepay    = prepay;
        current_booking_data[schedule_id][period_id].fee       = fee;

       $('#schedule_id').val($(row).data('schedule_id'));
    });

    if (document.getElementById('studied_outside_of_school').checked)
    {
        if (typeof additional_booking_data['outside_of_school'] == 'undefined')
        {
            additional_booking_data['outside_of_school'] = [];
            additional_booking_data['outside_of_school'].push(current_schedule);
        }
        else if (additional_booking_data['outside_of_school'].indexOf(current_schedule) == -1)
        {
            additional_booking_data['outside_of_school'].push(current_schedule);
        }
    }
    else if (typeof additional_booking_data['outside_of_school'] != 'undefined')
    {
        if (additional_booking_data['outside_of_school'].indexOf(current_schedule) > -1)
        {
            var index = additional_booking_data['outside_of_school'].indexOf(current_schedule);
            additional_booking_data['outside_of_school'].splice(index,1);
        }
    }
}

/**
 * Process the booking and show alerts or modal box if study and course year do not match
 * @param data
 */
function process_booking(data, btn)
{
    var original_args = arguments;
    // Skip warning on duplicate booking
    data.skip_duplicate = data.skip_duplicate || false;

    if (btn) {
        btn.disabled = true;
    }
    Cookies.set("bookingActionButtonClicked", bookingActionButtonClicked);
    var absences_explained = true;
    var period_data;

    // Loop through each event in the bookings
    /*for (var schedule_id in current_booking_data)
    {
        for (var period_id in current_booking_data[schedule_id])
        {
            period_data = current_booking_data[schedule_id][period_id];
            // Check if there is an absence, without a note
            if (period_data.attending == 0 && ( ! period_data.note || period_data.note.trim() == ''))
            {
                absences_explained = false;
                break;
            }
        }
    }*/

    // Don't continue, if there are any unexplained absences
    if ( ! absences_explained)
    {
        $('#unexplained_absence_modal').modal();
        return false;
    }

    if (parseFloat($("#prepay_total").val()) < 0)
    {
        $('#alert_minus_total').modal();
        return false;
    }

    $.ajax(
        {
            type: "POST",
            url: "/admin/bookings/check_duplicate",
            data: data,
            dataType: "json"
        }
    ).done(function(duplicate_response){
        if (!data.skip_duplicate && duplicate_response.duplicate == true) {
            if (btn) {
                btn.disabled = false;
            }

            // Store the function and its arguments,
            // so that it can be called again, should the user click "Continue anyway"
            cms_ns.duplicate_booking_params = original_args;
            cms_ns.duplicate_booking_params[0].skip_duplicate = true; // skip duplicate warning this time
            cms_ns.duplicate_booking_function = process_booking;

            $("#duplicate_schedule_booking_modal").modal();
            return;
        }
        var $alerts = $('#booking_form_alerts');
        var balance_data = {
            contact_id:data.contact_id
        };
        $.ajax({
                type: 'POST',
                url: '/admin/bookings/ajax_check_year',
                data: data,
                dataType: 'json'
            })
            .done(function(results)
            {
                if (typeof(results.status) == "undefined" && results.length) {
                    window.location = '/admin/bookings';
                } else if (results.status == 'success') {
                    if ( window.location.pathname === '/admin/bookings/add_edit_booking' ||  window.location.pathname === '/admin/bookings/add_edit_booking/') {
                        window.location = '/admin/bookings?booking='+results.booking_id;
                    } else {
                        display_balance(
                            balance_data,
                            function () {
                                redirect_booking(results.case, results.transaction_id, results.amount, results.message, results.booking_id);
                            }
                        );
                    }
                }
                else if (results.status == 'unmatched')
                {
                    if (btn) {
                        btn.disabled = false;
                    }
                    var modal = $('#course_level_warning');
                    $('#modal_schedule_summary').html(results.message);
                    modal.modal();
                    document.getElementById('data').value = JSON.stringify(results.data);
                }
                else if (results.status == 'bill')
                {
                    if (btn) {
                        btn.disabled = false;
                    }
                    make_bill_booking_modal(results);
                }
                else
                {
                    if (btn) {
                        btn.disabled = false;
                    }
                    $alerts.add_alert(results.message);
                }
            })
            .error(function()
            {
                $alerts.add_alert('The server did not respond. Please try again later.', 'danger popup_box');
                remove_popbox();
            });
    });
}

function make_bill_booking_modal(results)
{
    var $modal_box  = $('#make_billed_booking_modal'),
        $alerts = $('.alert-area');
    if ( ! data.matched )
    {
        $('#booking_warning').show();
        $('#modal_bill_unmatched_schedule_summary').html(results.unmatched_message);
    }
    $('#modal_bill_matched_schedule_summary').html(results.message);
    $('#modal_make_billed_booking_contact_name').val(results.student);
    $('#modal_make_billed_booking_schedule_name').val(results.schedule_name);
    $('#modal_make_billed_booking_course_name').val(results.course_title);
    $('#modal_make_billed_booking_total').val($('#amount').val());
    document.getElementById('bill_data').value = JSON.stringify(results.data);
    $modal_box.modal();
}

/**
 * Set the data to be used to process a booking
 * @param status
 * @param redirect
 * @returns {{booking: {}, additional_booking_data: *, amount: *, booking_items: *, booking_status: *, redirect: (Node.value|*), type: *, contact_id: (Node.value|*), year_study: (Node.value|*), schedule_id: (Node.value|*)}}
 */
function set_data(status,redirect)
{
    document.getElementById('booking_items').value           = JSON.stringify(current_booking_data);
    document.getElementById('additional_booking_data').value = JSON.stringify(additional_booking_data);
    document.getElementById('redirect').value                = redirect;
    var type;
    if ($("#prepay_total").is(':empty') || $("#prepay_total") == 0 || $('#payg_total') != 0)
    {
        // PAYG Booking type
        type = 2;
    }
    else
    {
        // Prepay booking type
        type = 1;
    }

    var discounts = {};
    for (var i in order_table_data) {
        var row = order_table_data[i];
        discounts[row.id] = [];
        for (var j in row.discounts) {
            discounts[row.id].push(
                {
                    id: row.discounts[j].id,
                    ignore: row.discounts[j].ignore == 1 ? 1 : 0,
                    code: row.discounts[j].code,
                    amount: row.discounts[j].amount
                }
            );
        }
    }

    booking_payment = [];
    $('#booking-schedules-list > tbody > tr').each(function()
    {
        booking_payment.push({
            schedule_id:    this.getAttribute('data-schedule_id'),
            prepay:         this.getAttribute('data-pre_pay'),
            schedule_cost:  this.getAttribute('data-amount')
        });
    });

    var cancel_booking_schedule = [];

    $("#transfer_booking table tbody > tr").each(function(){
        var $tr = $(this);
        if ($tr.find(".confirm").prop("checked")) {
            var csb = {};
            csb.booking_id = $tr.find(".booking_id").val();
            csb.schedule_id = $tr.find(".schedule_id").val();
            csb.credit = $tr.find(".credit").val();
            csb.confirm = 1;
            cancel_booking_schedule.push(csb);
        }
    });

    var delegate_ids = [];
    if ($("#booking_qty_type").val() == "delegates") {
        $("#delegates-wrapper .delegate-id").each(function () {
            if (this.value != "") {
                delegate_ids.push(this.value);
            }
        });
    }

    var student_ids = [];
    if ($("#booking_qty_type").val() == "multiple") {
        $("#students-wrapper .student-id").each(function () {
            if (this.value != "") {
                student_ids.push(this.value);
            }
        });
    }

    return {
        additional_booking_data: JSON.stringify(additional_booking_data),
        amount:                  parseFloat($(".payment_details_column .total").html()),
        prepay:                  null,
        payg:                    null,
        discount:                parseFloat($(".payment_details_column .subtotal").html()) - parseFloat($(".payment_details_column .total").html()),
        schedules:               booking_payment,
        host_family_contact_id:  ($('#contact-host-id').length == 1) ? $('#contact-host-id').val() : null,
        coordinator_contact_id:  ($('#contact-coordinator-id').length == 1) ? $('#contact-coordinator-id').val() : null,
        agent_contact_id:        ($('#contact-agent-id').length == 1) ? $('#contact-agent-id').val() : null,
        booking_items:           JSON.stringify(current_booking_data),
        booking_status:          status,
        redirect:                document.getElementById('redirect').value,

        type:                    type,
        contact_id:              ($("#booking_qty_type").val() == "delegates" && $("#lead_booker_id").val() !== "") ? $("#lead_booker_id").val() : document.getElementById('contact_id').value,
        year_study:              document.getElementById('contact_study_year_id').value,
        booking_type:            $('#booking_qty_type').val(),
        send_backend_booking_emails:     ($('#booking-send-email').is(":checked")) ? '1' : '0',

        schedule_id:             document.getElementById('schedule_id').value,
        booking_id:              document.getElementById('booking_id').value,
        discounts:               discounts,

        custom_discount:         (document.getElementById('custom_discount'))       ? document.getElementById('custom_discount').value         : 0,
        discount_memo:           (document.getElementById('booking_discount_memo')) ? document.getElementById('booking_discount_memo').value   : '',
        cancel_booking_schedule: cancel_booking_schedule,
        delegate_ids:            delegate_ids,
        student_ids:             student_ids,
        invoice_details:         $('#purchase_order_number').val(),
        special_requirements:    $('#special_requirements') ? $('#special_requirements').val() : ''
    };
}

$(document).on("change", "#transfer_booking select[name=transfer_booking]", function(){
    var credit = "";
    if (this.selectedIndex > 0) {
        credit = $(this.options[this.selectedIndex]).data("default-transfer-credit");
    }
    $("[name=transfer_credit]").val(credit);
});

/**
 * @param redirect
 */
function redirect_booking(redirect,transaction_id,amount,message,booking_id)
{
    var $accounts_tab   = $('[href="#family-member-accounts-tab"]');
    var $bookings_tab   = $('[href="#family-member-bookings-tab"]');
    var $timetable_tab  = $('[href="#family-member-timetable-tab"]');
    var $alerts = $('.alert-area');
    document.getElementById('kes_booking_edit_form').reset();
    $('#calendar_data').val('');
    $('#booking_items').val('');
    $('#schedule_id').val('');
    /*
     Use the switch to load proper tab fot the action selected
     */
    switch (redirect)
    {
        case 'pay':
            $accounts_tab.attr("loaddata", "no");
            $accounts_tab.click();
            ajax_load_member_accounts_actions(function(){
                $accounts_tab.attr("loaddata", null);

                if ($accounts_tab[0]) $accounts_tab[0].scrollIntoView();
                var $tr = null;
                if (transaction_id) {
                    $tr = $('tr.transaction-row[data-transaction_id=' + transaction_id + ']');
                } else if (booking_id){
                    $tr = $('tr.transaction-row[data-booking_id=' + booking_id + ']');
                }
                if ($tr.length > 0) {
                    $tr.addClass('selected');
                    var transaction_balance = $tr.data('transaction_balance');
                    var data = {
                        contact_id: $('#contact_id').val(),
                        credit: 1
                    };
                    data.booking_id = booking_id;
                    make_payment_modal(data);
                }
            });
            $alerts.add_alert(message, 'success');
            break;
        case 'book':
            $bookings_tab.click();
            //ajax_load_member_accounts_actions(function(){
                //if ($accounts_tab[0]) $accounts_tab[0].scrollIntoView();
                //$('tr.transaction-row').find("[data-transaction_id='"+transaction_id+"']").addClass('selected');
                //var transaction_balance = $('tr.transaction-row').find("[data-transaction_id='"+transaction_id+"']").data('transaction_balance');
                //load_payment(transaction_id,transaction_balance);
            //});
            $alerts.add_alert(message, 'success');
            break;
        case 'save':
            $bookings_tab.click();
            if ($bookings_tab[0]) $bookings_tab[0].scrollIntoView();
            $alerts.add_alert(message, 'success');
            break;
        case 'bill':
            $timetable_tab.click();
            if ($timetable_tab[0]) $timetable_tab[0].scrollIntoView();
            //$bookings_tab.click();
            //if ($bookings_tab[0]) $bookings_tab[0].scrollIntoView();
            $alerts.add_alert(message, 'success');
            break;
        case 'update':
            $bookings_tab.click();
            if ($bookings_tab[0]) $bookings_tab[0].scrollIntoView();
            //$bookings_tab.click();
            //if ($bookings_tab[0]) $bookings_tab[0].scrollIntoView();
            $alerts.add_alert(message, 'success');
            break;
        default :
            $bookings_tab.click();
            if ($bookings_tab[0]) $bookings_tab[0].scrollIntoView();
            $alerts.add_alert(message, 'success');
            break;
    }
}

function get_timetable()
{
    var data = {
        contact_id:document.getElementById('contact_id').value,
        before:document.getElementById('timetable_from_date').value,
        after:document.getElementById('timetable_to_date').value
    };
    $('#family-member-timetable-tab').find('.content-area').load('/admin/contacts3/ajax_get_booking_timetable/',data, function(result)
    {
        // document.getElementById('timetable_view_area').innerHTML = result;

		show_booking_calendar();

    });
}

// Add an alert to a message area.
// e.g. $('#page_notification_area').add_alert('Page successfully saved', 'success');
(function($)
{
    $.fn.add_alert = function(message, type, args)
    {
        var $alert = $('<div class="alert'+((type) ? ' alert-'+type : '')+' popup_box">' +
                '<a href="#" class="close" data-dismiss="alert">&times;</a> ' + message +
            '</div>');
        $(this).append($alert);

        var autoscroll = (args && args.autoscroll) ? args.autoscroll : true;

        if (autoscroll && typeof(this[0]) != 'undefined') {
            this[0].scrollIntoView();
        }

        // Dismiss the alert after 10 seconds
        setTimeout(function()
        {
            $alert.fadeOut();
        }, 10000);
    };
})(jQuery);

function show_register(period_id)
{
    $("#period_table").find("tbody tr td div[data-period_id='"+period_id+"'] button")
        .addClass('register_place')
        .removeClass('cancel_place')
        .addClass('btn-success')
        .removeClass('btn-danger')
        .text('Register')
        .removeAttr('data-show')
        .attr('data-toggle','modal');
}

function show_cancel(period_id)
{
    $("#period_table").find("tbody tr td div[data-period_id='"+period_id+"'] button")
        .addClass('cancel_place')
        .removeClass('register_place')
        .addClass('btn-danger')
        .removeClass('btn-success')
        .text('Cancel')
        .attr('data-show',true)
        .removeAttr('data-toggle');
}

function get_attending_cost()
{
    var type = $('#schedule_payment_type').val();
    var fee_per = $('#schedule_fee_per').val();
    var class_fee = $('#cost_per_class').val();
    var period_attending = $('#period_attending').val();
    if (fee_per == 'Schedule') {
        $('#schedule_total_cost').val($('#schedule_fee_amount').val());
    } else {
        var total_cost = 0;
        $("#period_booking_table tbody tr").each(function(){
            if ($(this).find(".period_attend_icon").hasClass('icon-ok')) {
                total_cost += parseFloat($(this).data('fee'));
            }
        });
        $('#schedule_total_cost').val(total_cost.toFixed(2));
    }
}

function empty_booking_calendar()
{
    $('#booking-fullcalendar').fullCalendar('destroy');
}

function show_booking_calendar()
{
	var $calendar = $('#booking-fullcalendar');
	$calendar.fullCalendar({
		header: {
			left: 'prev,title,next',
			right: 'agendaWeek,month,listMonth'
		},
		buttonText: {
			week: 'Week',
			month: 'Month',
			listMonth: 'List'
		},
		weekNumbers: true,
		weekNumberTitle: '',
		firstDay: 1,
        listDayFormat : 'ddd,DD/MMM/YYYY',
		views: {
			'week': {
				columnFormat: 'ddd D/M'
			},
			'month': {
				eventLimit: 5
			}
		},
		events: $calendar.data('events'),

		eventRender: function(event, element, view) {

			element.popover({
				title: '<a href="/admin/courses/edit_schedule?id='+event.schedule_id+'">'+event.title+'</a>',
				placement: (view.type == 'listMonth' ? 'bottom' : 'right'),
				container: '#booking-fullcalendar .fc-view',
				html: true,
				content: function() {
                    $("div.popover").remove();
					var $clone = $('#calendar-popover-template').clone();

					$clone.find('.btn-register').attr('data-schedule_id', event.schedule_id).data('schedule_id', event.schedule_id);
                    $clone.find('.btn-register').attr('data-booking_type', event.booking_type).data('booking_type', event.booking_type);
                    $clone.find('.btn-register').attr('data-event_id', event.period_id).data('event_id', event.period_id);
					$clone.find('.calendar-popover-category'  ).html(event.category).attr('title', event.category);
					$clone.find('.calendar-popover-location'  ).html(event.location);
                    $clone.find('.calendar-popover-room'      ).html(event.room_no);
					$clone.find('.calendar-popover-trainer'   ).html(event.trainer);
					$clone.find('.calendar-popover-start_time').html(event.start.format('HH:mm'));
                    if (event.end) {
                        $clone.find('.calendar-popover-end_time').html(event.end.format('HH:mm'));
                    }
					$clone.find('.calendar-popover-registered').html(event.attending);

                    if (event.booked) {
						$clone.find('.calendar-popover-is_attending').removeClass('hidden');
					}
					else {
                        $clone.find('.register_place').removeClass('hidden');
                        if (event.booking_type == 'Whole Schedule') {
                            $clone.find('.register_place-amount').html(event.timeslots_count > 1 ? event.timeslots_count + ' sessions' : ' 1 session');
                        } else if (event.booking_type == 'Subscription') {
                            $clone.find('.register_place-amount').html('Subscribe');
                        } else {
                            $clone.find('.customize_register').addClass("hidden");
                            $clone.find('.register_place-amount').html(' 1 session');
                        }
					}

                    if (event.category == 'Supervised Study' || event.attend_all_default == 'NO') {
                        $clone.find('.customize.yes').attr("checked", "checked");
                        $clone.find('.register_place-amount').addClass('hidden');
                    } else {
                        $clone.find('.customize.no').attr("checked", "checked");
                    }

                    return $clone.html();
				},
				trigger: 'click focus'
			});

            // Add "booked" flag to items in list view
            var booked = event.booked.toString();
            element.data('booked', booked).attr('data-booked', booked);

            if (event.booked) {
                var $booked_flag = $('#booking-fullcalendar-templates').find('.booking-fullcalendar-flag--booked').clone();
                element.find('.fc-list-item-title').append($booked_flag);
            }
		},
        eventAfterRender: function(event, element) {
            var index = element.parent().index();
            element.closest('.fc-week').find('.fc-content-skeleton td').eq(index).addClass('fc-day-with-events')
        }
	});
}

// Number of places is not a fixed amount, when "custom" is selected.
// Hide the number, when "custom" is selected
$(document).on('change', '[name="customize_register_place"]', function() {
    var $popover = $(this).parents('.calendar-popover');
    // 0 means 'custom'
    if ($popover.find('[name="customize_register_place"]:checked').val() == 0) {
        $popover.find('.register_place-amount').addClass('hidden');
    }
    else {
        $popover.find('.register_place-amount').removeClass('hidden');
    }
});

// Dismiss calendar popover when clicked away from
$(document).on('click', function (ev)
{
    var $calendar = $('#booking-fullcalendar');
	$calendar.find('.fc-event, .fc-list-item, .fc-more-popover').each(function ()
	{
		if ( ! $(this).is(ev.target) && ! $(this).find(ev.target).length && $('.popover').has(ev.target).length === 0) {
			$(this).popover('hide');
		}
	});

    if ($calendar.find(ev.target).length == 0) {
        $calendar.find('.popover').popover('hide');
    }
    if ($(ev.target).hasClass("fc-day") || $(ev.target).hasClass("fc-week-number") || $(ev.target).hasClass("fc-day-top")) {
        $calendar.find('.popover').popover('hide');
    }
});

var booking_schedule_edit_modal_display_booking_link = null;
var bulk_update_schedule = null;
function booking_schedule_edit_modal_display(schedule_id, booking_link, customize)
{
    var $edit_modal = $('#booking-schedule-edit-modal');
    var $tbody = $edit_modal.find('table.frequency > tbody');
    $tbody.html("");

    booking_schedule_edit_modal_display_booking_link = booking_link;
    $.post(
        '/admin/bookings/get_schedule_details',
        {
            schedule_id: schedule_id,
            timeslot_ids: ''
        },
        function (schedule) {
            bulk_update_schedule = schedule;
            $edit_modal.find('.modal-title .category').html(schedule.category);
            $edit_modal.find('.modal-title .schedule').html(schedule.course + " " + schedule.schedule);
            $edit_modal.find('.modal-title .location').html(schedule.location || schedule.room);

            $edit_modal.find('.modal-body .schedule-title').html(
                schedule.location + " - " + schedule.trainer + " - " + schedule.fee_amount + " - " + schedule.room
            );

            $tbody.data("schedule_id", schedule_id);


            var timeslots = [];
            var frequencies = {};
            $edit_modal.find(".days-filter > label").addClass("hidden");

            var i, dt, freq;
            for (i = 0 ; i < schedule.timeslots.length ; ++i) {
                dt = new Date(clean_date_string(schedule.timeslots[i]['datetime_start']));
                freq = dt.dateFormat("D H:i");
                if (!frequencies[freq]) {
                    frequencies[freq] = [];
                    $edit_modal.find(".days-filter > label[data-day=" + dt.getDay() + "]").removeClass("hidden");
                }
                frequencies[freq].push(schedule.timeslots[i]);
            }

            var default_attend_yes = schedule.category == 'Supervised Study' || schedule.attend_all_default == 'NO' ? false : true;
            var default_note = default_attend_yes ? '' : 'Not Due';

            for (freq in frequencies) {
                var d = new Date(clean_date_string(frequencies[freq][0]['datetime_start']));
                var tr_inner = '';
                var at_least_one_yes = false;

                for (i = 0 ; i < frequencies[freq].length ; ++i) {
                    var timeslot = frequencies[freq][i];
                    var $boooking_tr = $("#confirmed_periods_table tr[data-period_id=" + timeslot.id + "]");
                    var attend = $boooking_tr.length ? ($boooking_tr.find('.icon-ok').length ? true : false) : default_attend_yes;
                    var note = $boooking_tr.length ? $boooking_tr.find('input').val() : default_note;
                    if (attend) {
                        at_least_one_yes = true;
                    }

                    dt = new Date(clean_date_string(timeslot['datetime_start']));
                    tr_inner += "<tr data-timeslot_id='" + timeslot.id + "'>" +
                        "<td>" + dt.dateFormat("d M") + "</td>" +
                        "<td>" + schedule.trainer + "</td>" +
                        //"<td><input type='checkbox' data-timeslot_id='" + timeslot['id'] + "' class='attend' value='1' " + (checked ? "checked='checked'" : "" ) + " /> </td>" +
                        '<td>' +
                        '<div class="btn-group btn-group-slide" data-toggle="buttons">' +
                        '<label class="btn btn-plain ' + (attend ? 'active' : '')+ '"><input type="radio" name="bulk_attend_update_attending[' + timeslot.id + ']" value="1" class="attend" ' + (attend ? 'checked="checked"' : '')+ ' />Yes</label>' +
                        '<label class="btn btn-plain ' + (!attend ? 'active' : '')+ '"><input type="radio" name="bulk_attend_update_attending[' + timeslot.id + ']" value="0" class="attend" ' + (!attend ? 'checked="checked"' : '')+ ' />No</label>' +
                        '</div>' +
                        '</td>' +
                        "<td><input type='text' data-timeslot_id='" + timeslot['id'] + "' class='note' value='" + note + "' /> </td>" +
                        "</tr>";
                }
                var tr = "<tr class='freq_a' data-day='" + d.getDay() + "' data-freq='" + freq + "'>" +
                    "<td><button type=\"button\" class='btn-link expand'><span class=\"icon-plus-square-o\"></span></button>" + freq + "</td>" +
                    "<td>" + frequencies[freq].length + "</td>" +
                    "<td><button type=\"button\" class=\"btn-link bulk_update\">update</button></td>" +
                        // "<td><input type=\"checkbox\" name=\"attend[" + schedule_id + "][" + freq + "]\" value=\"1\" class='attend' /></td>" +
                    '<td>' +
                    '<div class="btn-group btn-group-slide" data-toggle="buttons">' +
                    '<label class="btn btn-plain ' + (at_least_one_yes ? 'active' : '') + ' "><input type="radio" ' + (at_least_one_yes ? 'checked="checked"' : '') + ' name="attend[' + schedule_id + '][' + freq + ']" value="1" class="attend" />Yes</label>' +
                    '<label class="btn btn-plain ' + (!at_least_one_yes ? 'active' : '') + ' "><input type="radio" ' + (!at_least_one_yes ? 'checked="checked"' : '') + ' name="attend[' + schedule_id + '][' + freq + ']" value="0" class="attend" />No</label>' +
                    '</div>' +
                    '</td>' +
                    "<td><input type=\"text\" name=\"note[" + schedule_id + "][" + freq + "]\" class='note' /></td>" +
                    "</tr>" +
                    "<tr class='freq_b hidden'><td colspan=\"5\">" +
                    "<table class='table'>" +
                    tr_inner;

                tr += "</table>";
                tr += "</td></tr>";

                $tbody.append(tr);
            }

            if (customize) {
                $edit_modal.find("[name=customize][value=1]").prop("checked", true);
                $(".attendance-custom").removeClass("hidden");
            }
            $edit_modal.modal();
        }
    );
}

function booking_schedule_remove_modal_display(schedule_id)
{
    $.post(
        '/admin/bookings/get_schedule_details',
        {
            schedule_id: schedule_id,
            timeslot_ids: ''
        },
        function (schedule) {
            $("#booking-schedule-remove-modal .modal-title .category").html(schedule.category);
            $("#booking-schedule-remove-modal .modal-title .schedule").html(schedule.course + " " + schedule.schedule);
            $("#booking-schedule-remove-modal .modal-title .location").html(schedule.location);

            $("#booking-schedule-remove-modal .modal-body .schedule-title").html(
                schedule.location + " - " + schedule.trainer + " - " + schedule.fee_amount + " - " + schedule.room
            );

            $("#booking-schedule-remove-modal .btn.danger").data("schedule_id", schedule_id);
            $("#booking-schedule-remove-modal").modal();
        }
    );
}

$(document).on("click", ".booking-schedule-edit-modal-display", function(){
    var schedule_id = $(this).parents("tr").data("schedule_id");
    $("#booking-schedule-edit-modal .btn.add").addClass("hidden");
    $("#booking-schedule-edit-modal .btn.update").removeClass("hidden");
    booking_schedule_edit_modal_display(schedule_id);

});

$(document).on("click", ".booking-schedule-remove-modal-display", function(){
    var schedule_id = $(this).parents("tr").data("schedule_id");
    booking_schedule_remove_modal_display(schedule_id);

});

$(document).on("click", "#booking-schedule-remove-modal .btn.danger", function(){
    var schedule_id = $(this).data("schedule_id");
    delete current_booking_data[schedule_id];
    $("#confirmed_periods_table tbody > tr").each (function(){
        if ($(this).data("schedule_id") == schedule_id) {
            $(this).remove();
        }
    });
    $("#booking-schedules-list tbody > tr").each(function(){
        if ($(this).data("schedule_id") == schedule_id) {
            $(this).remove();
        }
    });
    get_order_table_html();
});

$(document).on("click", "#booking-schedule-edit-modal [name=customize]", function(){
    if (this.value == "1") {
        $("#booking-schedule-edit-modal .attendance-custom").removeClass("hidden");
    } else {
        $("#booking-schedule-edit-modal .attendance-custom").addClass("hidden");
    }
});

$(document).on("change", "#booking-schedule-edit-modal [name=attend-all]", function(){
    var $inputs = $("#booking-schedule-edit-modal input.attend");
    $inputs.prop("checked", false);
    $inputs.parent().removeClass("active");
    if ($(this).val() == "1") {
        $("#booking-schedule-edit-modal input.attend[value=1]").prop("checked", true);
        $("#booking-schedule-edit-modal input.attend[value=1]").parent().addClass("active");
    }

    if ($(this).val() == "0") {
        $("#booking-schedule-edit-modal input.attend[value=0]").prop("checked", true);
        $("#booking-schedule-edit-modal input.attend[value=0]").parent().addClass("active");
    }
});

$(document).on("click", "#booking-schedule-edit-modal table.frequency .expand", function() {
    var $section = $(this).parents('tr').next();

    if ($section.hasClass('hidden')) {
        $(this).html('<span class="icon-minus-square-o"></span>');
        $section.removeClass('hidden');
    }
    else {
        $(this).html('<span class="icon-plus-square-o"></span>');
        $section.addClass('hidden');
    }
});

$(document).on("change", "#booking-schedule-edit-modal .freq_a .attend", function(){
    var $tr = $(this).parents("tr");
    $tr.next().find(".attend").prop("checked", false);
    $tr.next().find(".attend").parent().removeClass("active");
    var $radio = $tr.next().find(".attend[value=" + this.value + "]");
    $radio.prop("checked", true);
    $radio.parent().addClass("active");
});

$(document).on("change", "#booking-schedule-edit-modal .freq_a .note", function(){
    var $tr = $(this).parents("tr");
    $tr.next().find(".note").val(this.value);
});

$(document).on("click", "#booking-schedule-edit-modal .btn.add", function(){
    var warn_note_not_attend = false;

    if ($("#booking-schedule-edit-modal [name='attend-all']").val() != "0" && $("#booking-schedule-edit-modal .modal-title .category").html() != "Supervised Study"){
        $(".freq_b tr").each (function(){
            if ($(this).find(".attend:checked").val() == "0" && $(this).find(".note").val() == "") {
                warn_note_not_attend = true;
            }
        });
    }

    if (warn_note_not_attend) {
        $('#unexplained_absence_modal').modal();
        return false;
    }

    var args = {
        booking_type: $(booking_schedule_edit_modal_display_booking_link).data('booking_type'),
        schedule_id:  $(booking_schedule_edit_modal_display_booking_link).data('schedule_id'),
        timeslot_id:  $(booking_schedule_edit_modal_display_booking_link).data('event_id')
    };

    get_recurring_schedule(args, function(response) {
        return response;
    });

    $("#booking-schedule-edit-modal").modal('hide');
});

$(document).on("click", "#booking-schedule-edit-modal .btn.update", function(){
    var warn_note_not_attend = false;

    if ($("#booking-schedule-edit-modal [name='attend-all']").val() != "0" && $("#booking-schedule-edit-modal .modal-title .category").html() != "Supervised Study"){
        $(".freq_b tr").each (function(){
            if ($(this).find(".attend:checked").val() == "0" && $(this).find(".note").val() == "") {
                warn_note_not_attend = true;
            }
        });
    }

    if (warn_note_not_attend) {
        $('#unexplained_absence_modal').modal();
        return false;
    }

    $("#booking-schedule-edit-modal").modal('hide');

    var schedule_id = $("#booking-schedule-edit-modal table.frequency > tbody").data("schedule_id");
    $("#booking-schedule-edit-modal .freq_b table tr").each (function(){
        if ($(this).find(".attend:checked").length == 0) {
            return;
        }
        var timeslot_id = $(this).data("timeslot_id");
        var checked = $(this).find(".attend:checked").val() == "1";
        var note = $(this).find(".note").val();

        var $booking_tr = $("#confirmed_periods_table tr[data-period_id=" + timeslot_id + "]");
        $booking_tr.find(".confirmed_period_booking_note").val(note);
        var $icon = $booking_tr.find(".icon-ok, .icon-remove");
        $icon.removeClass("icon-ok");
        $icon.removeClass("icon-remove");
        if (checked) {
            $icon.addClass("icon-ok");
        } else {
            $icon.addClass("icon-remove");
        }

        current_booking_data[schedule_id][timeslot_id].attending = checked ? 1 : 0;
        current_booking_data[schedule_id][timeslot_id].note = note;
    });
    get_order_table_html();
});

$(document).on('change', '.booking-register-day', function() {
    var display = (this.checked);
    var day = this.value;

    $('#booking-schedule-edit-modal').find('.freq_a').each(function() {
        if ($(this).data("day") == day) {
            if (display) {
                $(this).removeClass("hidden");
            } else {
                $(this).addClass("hidden");
                $(this).next().addClass("hidden");
            }
        }
    });
});

$(document).on("click", "#booking-schedule-edit-modal .bulk_update", function(){
    var $modal = $("#bulk-update-modal2");
    var schedule = bulk_update_schedule;
    var selected_freq = $(this).parents("tr").data("freq");

    $(".bulk_attend2_update_frequency").html("");
    var frequencies = {};
    $('.bulk_attend2_update_frequency').multiselect('destroy');
    for (var i = 0 ; i < schedule.timeslots.length ; ++i) {
        var dt = new Date(clean_date_string(schedule.timeslots[i]['datetime_start']));
        var freq = dt.dateFormat("D H:i");
        if (!frequencies[freq]) {
            frequencies[freq] = [];
            $(".bulk_attend2_update_frequency").append("<option value='" + freq + "'" + (selected_freq == freq ? " selected='selected'" : '') + ">" + freq + "</option>");
        }
        frequencies[freq].push(schedule.timeslots[i]);
    }
    $('.bulk_attend2_update_frequency').multiselect();
    //$('.bulk_attend_update2_date_from, .bulk_attend2_update_date_to').val('');
    $modal.modal();
});

$(document).on('change', "#bulk-update2-starts, #bulk-update2-ends, .bulk_attend2_update_frequency", function(){
    var $modal = $("#bulk-update-modal2");
    var date_start = $("#bulk-update2-starts").data("date");
    var date_end = $("#bulk-update2-ends").data("date");

    var $table = $modal.find(".table.timeslots > tbody");
    $table.html("");

    if (date_start) {
        date_start = new Date(clean_date_string(date_start));
    }
    if (date_end) {
        date_end = new Date(clean_date_string(date_end));
    }

    var schedule = bulk_update_schedule;
    $(".bulk_attend2_update_frequency option:selected").each(function(){
        var selected_freq = this.value;

        for (var i = 0 ; i < schedule.timeslots.length ; ++i) {
            var dt = new Date(clean_date_string(schedule.timeslots[i]['datetime_start']));
            var freq = dt.dateFormat("D H:i");
            if (freq == selected_freq) {
                if (date_start && date_start.getTime() > dt.getTime()) {
                    continue;
                }
                if (date_end && date_end.getTime() < dt.getTime()) {
                    continue;
                }
                var tr = "<tr data-timeslot_id='" + schedule.timeslots[i].id + "'>";
                tr += "<td>" + dt.dateFormat("d M") + "</td>";
                tr += '<td>' +
                    '<div class="btn-group btn-group-slide" data-toggle="buttons">' +
                    '<label class="btn btn-plain"><input type="radio" name="bulk_attend2_update_attending" value="1" class="bulk_attend2_update_attending_yes"/>Yes</label>' +
                    '<label class="btn btn-plain"><input type="radio" name="bulk_attend2_update_attending" value="0" class="bulk_attend2_update_attending_no"/>No</label>' +
                    '</div>' +
                    '</td>';
                tr += '<td>' +
                    '<input type="text" name="bulk_attend2_update_note" class="bulk_attend2_update_note" value="" />' +
                    '</td>';
                tr += "</tr>";
                $table.append(tr);
            }
        }
    });
});

$(document).on("click", ".bulk_attend2_update_set", function(){

});

$(document).on("change", "[name=bulk_attend2_update_attending_all]", function(){
    if ($("[name=bulk_attend2_update_attending_all]:checked").val() == "1") {
        $(".timeslots .bulk_attend2_update_attending_yes").prop("checked", true);
        $(".timeslots .bulk_attend2_update_attending_yes").parent().addClass("active");
        $(".timeslots .bulk_attend2_update_attending_no").parent().removeClass("active");
    } else {
        $(".timeslots .bulk_attend2_update_attending_no").prop("checked", true);
        $(".timeslots .bulk_attend2_update_attending_yes").parent().removeClass("active");
        $(".timeslots .bulk_attend2_update_attending_no").parent().addClass("active");
    }
});

$(document).on("change", ".bulk_attend_update2_note_all", function(){
    $(".bulk_attend2_update_note").val(this.value);
})

$(document).on("click", ".bulk_attend2_update_set", function(){
    $("#bulk-update-modal2 .table.timeslots > tbody > tr").each (function(){
        var timeslot_id = $(this).data("timeslot_id");
        var $tr = $("#booking-schedule-edit-modal .frequency.table .freq_b tr[data-timeslot_id=" + timeslot_id + "]");
        $tr.find(".note").val($(this).find(".bulk_attend2_update_note").val());
        $tr.find("[type=radio]").parent().removeClass("active");
        if ($(this).find(".btn-plain.active input").val() == "1") {
            $tr.find("[type=radio][value=1]").prop("checked", true);
            $tr.find("[type=radio][value=1]").parent().addClass("active");
        } else {
            $tr.find("[type=radio][value=0]").prop("checked", true);
            $tr.find("[type=radio][value=0]").parent().addClass("active");
        }
    });
});

$(document).on("click", "#booking_schedules_list .clear", function(){
    empty_booking_calendar();
});

function load_application_details(application_id, display_ok, type)
{
    $.post(
        "/admin/bookings/ajax_load_application_details",
        {
            application_id: application_id,
            type: type,
            contact_id: $("#contact_id").val()
        },
        function (response) {
            // Highlight the table row
            var $table = $('.contact_applications_table');
            $table.find('tr.selected').removeClass('selected');
            $table.find('tr[data-booking_id="'+booking_id+'"]').addClass('selected');

            $("#fulltime-course-application-wrapper").html(response);
            if (display_ok) {
                $("#course_application_saved_modal").modal();
            }
        }
    )
}

$(document).on("click", ".contact_applications_table .edit", function(){
    var application_id = $(this).data("application_id");

    load_application_details(application_id);
});

$(document).on("click", ".new_fulltime_application", function(){
    load_application_details("new", false, "fulltime");
});

$(document).on("click", ".new_application", function(){
    load_application_details("new", false);
});

$(document).on("change", "[name=create_ftcourse_transaction]", function(){
    if ($("[name=create_ftcourse_transaction]:checked").val() == 'Yes'){
        $(".new_ftcourse_transaction_details").removeClass("hidden");
    } else {
        $(".new_ftcourse_transaction_details").addClass("hidden");
    }
});

$(document).on("change", "[name=create_transaction]", function(){
    if ($("[name=create_transaction]:checked").val() == 'Yes'){
        $(".new_transaction_details").removeClass("hidden");
    } else {
        $(".new_transaction_details").addClass("hidden");
    }
});

$(document).on("change", "#application_linked_schedules [name=has_course_id]", function(){
    $.post(
        '/admin/courses/find_schedule?course_id=' + this.value + '&ignore_fee=1&all_time=1',
        {

        },
        function (response) {
            var $select = $("#application_linked_schedules [name=has_schedule_id]");
            $select.html('<option value="">-- Select schedule --</option>');
            for (var i in response) {
                $select.append('<option value="' + response[i].id + '" data-fee_amount="' + response[i].fee_amount + '">' + response[i].label + '</option>');
            }
        }
    )
});

$(document).on("change", "#application_linked_schedules [name=has_schedule_id]", function(){
    if ($("#application_linked_schedules #schedule_period_select").length > 0) {
        var $tbody = $("#application_linked_schedules #schedule_period_select tbody");
        $tbody.html("");

        if (this.selectedIndex > 0) {
            $.post(
                '/admin/courses/find_schedule_periods',
                {
                    schedule_id: this.value
                },
                function (response) {
                    for (var i in response) {
                        $tbody.append(
                            "<tr>" +
                            "<td>" + response[i].period + " - " + response[i].period_end + "</td>" +
                            "<td>" + response[i].trainer + "</td>" +
                            "<td>" + response[i].max_capacity + "</td>" +
                            "<td>" + response[i].booking_count + "</td>" +
                            "<td><input type=\"checkbox\" name=\"application[has_period][]\" value=\"" + response[i].period + "," + response[i].trainer_id + "\" /></td>" +
                            "</tr>"
                        );
                    }
                }
            );
        }
    }
});

$(document).on("change", "[name=fulltime_course_id]", function(){
    $("[name=ftcourse_transaction_amount]").val($("[name=fulltime_course_id] option:selected").data("fulltime_price"));
});

$(document).on('click', '.contact_applications_table tbody tr', function(ev) {
    // If the user clicks anywhere in the row, that isn't a link or form element, load the application details
    if (!$(ev.target).is('a, label, button, :input') && !$(ev.target).parents('a, label, button, :input')[0]) {
        var application_id = $(this).data('application_id');
        load_application_details(application_id);
    }
});

$(document).on("click", "#fulltime-course-application-edit button.update", function(){
    var data = $("#fulltime-course-application-edit").serialize();
    var application_id = $("#fulltime-course-application-edit [name=application_id]").val();
    $.post(
        "/admin/bookings/update_application",
        data,
        function (response) {
            if (parseInt($("[name=interview_transfer_to_course_id]").val()) > 0) {
                open_contact_application_tab();
            } else {
                if (booking_id == "new") {
                    load_application_details(response.success, true);
                } else {
                    load_application_details(application_id, true);
                }

            }
        }
    );
    return false;
});
$(document).on("click", "#fulltime-course-application-edit button.move", function(){
    var data = $("#fulltime-course-application-edit").serialize();
    data = data + '&move=1';
    var application_id = $("#fulltime-course-application-edit [name=application_id]").val();
    $.post(
        "/admin/bookings/update_application",
        data,
        function (response) {
            if (parseInt($("[name=interview_transfer_to_course_id]").val()) > 0) {
                open_contact_application_tab();
            } else {
                if (booking_id == "new") {
                    load_application_details(response.success, true);
                } else {
                    load_application_details(application_id, true);
                }

            }
        }
    );
    return false;
});
// When the "cancel application" confirmation modal is opened, ensure the booking ID is passed into it.
$(document).on('show.bs.modal', '#application-cancel-confirm-modal',function(ev) {
    const booking_id = $(ev.relatedTarget).data('booking_id');

    $('#application-cancel-modal-confirm').data('booking_id', booking_id).attr('data-booking_id', booking_id);
});

// Cancel an application, after clicking the "confirm" button in the modal.
$(document).on('click', '#application-cancel-modal-confirm', function() {
    if (!booking_id) {
        booking_id =  $(this).data('booking_id');
    }
    $.post(
        "/admin/bookings/update_application",
        { booking_id: booking_id, update: 'cancel' },
        function (response) {
            open_contact_application_tab();
        }
    );

});

$(document).on("change", "[name=interview_transfer_to_course_id]", function(){
    $.post(
        "/admin/courses/get_timeslots",
        {course_id: this.value},
        function (response) {
            var options = '<option value=""></option>';
            for (var i in response) {
                options += '<option value="' + response[i].id + '" data-schedule_id="' + response[i].schedule_id + '">' +
                    response[i].schedule + ' - ' + response[i].datetime_start + ' (' + response[i].booking_count + ' / ' + response[i].max_capacity + ')' +
                    '</option>';
            }
            $("[name=interview_transfer_to_timeslot_id]").html(options);
            $(".send_email.hidden").removeClass("hidden");
        }
    );
});

$(document).on("change", "[name=interview_transfer_to_timeslot_id]", function(){
    $("[name=interview_transfer_to_schedule_id]").val($(this.options[this.selectedIndex]).data("schedule_id"));
    $(".send_email.hidden").removeClass("hidden");
});

$(document).on("change", "[name=interview_transfer_to_schedule_id]", function(){
    $(".send_email.hidden").removeClass("hidden");
});

$(document).on("change", "[name=interview_transfer_interview_status]", function(){
    $(".send_email.hidden").removeClass("hidden");
});

$(document).on("change", "[name=interview_status]", function(){
    $(".send_email.hidden").removeClass("hidden");
});

function select_row(row) {
    $(row).parents('tbody').find('tr').removeClass('selected');
    row.className += ' selected';
}

function open_survey_list(booking_id) {
    $.ajax({
        method: "post",
        data : {'booking_id' : booking_id},
        url: "/admin/surveys/ajax_display_survey_related_to_booking",
        async: false,
    }).done(function (data) {
        $('#survey_booking_wrapper').html(data);
    });
}
function open_survey(booking_id) {
    $.ajax({
        method: "post",
        url: "/admin/surveys/ajax_display_survey_details/",
        async: false,
        data: {booking_id: booking_id, start_new: true}
    }).done(function (data) {
        $('#survey_booking_wrapper').html(data);
        $.getScript('/engine/plugins/surveys/js/survey.js', function(){
            survey_initialisation();
        });
    });
}