<?php
$bookings_flder_url = URL::get_engine_plugin_assets_base('bookings');
$shared_folder_url  = URL::get_engine_assets_base();
$view = '';
$student_wrapper = '';
?>

<?php if (!Auth::instance()->has_access('contacts3_limited_view')): ?>
    <?php if (isset($contacts[0]) && $contacts[0]['family_id'] !== null): ?>
        <div class="form-group">
            <?= View::factory('frontend/snippets/family_members')
                ->set('family',     $contacts[0]['family_id'])
                ->set('attributes', array('class' => 'contacts-select_contact'))
                ->set('contact_role', $contact_role); ?>
        </div>
    <?php elseif(isset($contacts[0])): ?>
        <input  type="hidden"
                name="family_members[]"
                value="<?= $contacts[0]['id'] ?>"
                class="family-member-checkbox"
                data-contact_id="<?= $contacts[0]['id'] ?>"
                data-is_primary="1"
                checked="checked"
        />
    <?php endif;
    else: ?>
    <?php $student_wrapper = 'student_wrapper'; ?>
    <div class="page-title db-bt-rule">
        <div class="title-left">
            <?php $contact = $contacts[0]; ?>

            <h1><?= trim($contact['first_name'].' '.$contact['last_name']) ?>’s Timetable</h1>
        </div>
    </div>
<?php endif; ?>

<div id="tab1" class="tabs_content1 calender_custom">
    <div class="db-bt-rule db-time-table ">
        <div id="booking-fullcalendar-wrapper">
            <div class="booking-timetables">
                <div class="filter-wrap">
                    <div class="left time-print-icon">

                        <div class="dropdown">
                            <button type="button" class="btn-link btn-lg text-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="icon-clock-o" aria-hidden="true"></span>
                            </button>

                            <div class="dropdown-menu show-next-class">
                                <div class="class-detail template hidden">
                                    <p class="text-primary"><span class="contact"></span></p>
                                    <p><span class="title"></span><span class="text-success time"><span></p>
                                    <div><span class="building"></span><span class="room-no"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="booking_item_filter">
                        <div class="dropdown timetable-status-filters" data-autodismiss="false">
                            <button type="button" class="btn btn-default btn-lg" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Filter
                                <span class="icon-angle-down" aria-hidden="true"></span>
                            </button>

                            <ul class="dropdown-menu pull-right">
                                <?php
                                $dropdown_options = array(
                                    'attending' => array('label' => __('Attending'), 'value' => '1',       'icon' => '<span class="icon-check circled_icon" aria-hidden="true"></span>'),
                                    'absent'    => array('label' => __('Absent'),    'value' => '',        'icon' => '<span class="icon-exclamation circled_icon" aria-hidden="true"></span>'),
                                    'late'      => array('label' => __('Late'),      'value' => 'Late',    'icon' => '&#9888'),
                                    'present'   => array('label' => __('Present'),   'value' => 'Present', 'icon' => '<span class="icon-check" aria-hidden="true"></span>')
                                );
                                ?>
                                <?php foreach ($dropdown_options as $key => $option): ?>
                                    <li class="background-status-<?= $key ?>">
                                        <a data-target="#">
                                            <?= Form::ib_checkbox($option['icon'].' '.$option['label'], $key, $option['value'], false, array('id' => 'booking_item_'.$key)) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="booking-fullcalendar" id="booking-fullcalendar"></div>

                <div class="hidden-sm hidden-md hidden-lg booking-fullcalendar-events-mobile" id="booking-fullcalendar-events-mobile">
                    <h3 id="booking-fullcalendar-events-mobile-day"></h3>

                    <table id="booking-fullcalendar-events-mobile-list">
                        <tbody></tbody>

                        <tfoot class="hidden">
                            <tr id="booking-fullcalender-event-template-mobile">
                                <td>
                                    <strong class="calendar-event-start_time"></strong><br />
                                    <span class="calendar-event-end_time"></span>
                                </td>

                                <td>
                                    <h4 class="calendar-event-title"></h4>
                                    <div class="calendar-event-description"></div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="hidden calendar-popover-wrapper template">
                <div class="calendar-popover">
                    <div class="calendar-popover-category"></div>
                    <div class="calendar-popover-trainer"></div>
                    <div class="calendar-popover-location"></div>
                    <div><span class="calendar-popover-start_time"></span> &ndash; <span class="calendar-popover-end_time"></span></div>

                    <hr />

                    <div>
                        <button
                            type="button"
                            class="btn btn-register register_place hidden">Register
                        </button>
                        <p class="calendar-popover-is_attending hidden">
                            <span class="icon-check is_attending_icon hidden"></span>
                            <span class="icon-exclamation-circle hidden"></span>
                            <span class="text">Attending</span>
                        </p>

                        <?php if (Auth::instance()->has_access('contacts3_limited_family_access')): ?>
                            <div class="form-actions text-left">
                                <div class="btn-group dropdown add_note_wrapper <?= $student_wrapper ?>">
                                    <button type="button" class="btn btn-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Add Note <span class="caret"></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li class="one"><a data-target="#">This One</a></li>
                                        <li class="until"><a data-target="#">Until</a></li>
                                        <li class="weekly"><a data-target="#">Weekly</a></li>
                                    </ul>
                                </div>

                                <button type="button" class="btn-cancel add_note_cancel">Cancel</button>
                            </div>
                        <?php endif; ?>

                        <div class="action_notes">
                            <ul>
                                <li class="view"><a class="view" style="cursor: pointer;">View Note</a></li>
                                <?php if (Auth::instance()->has_access('contacts3_limited_family_access')) { ?>
                                <li class="modify"><a class="modify" style="cursor: pointer;">Modify Note</a></li>
                                <li><a class="delete" style="cursor: pointer;">Delete Note</a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hidden calendar-popover-mytime-wrapper template">
                <div class="calendar-popover">
                    <div><span class="calendar-popover-start_date"></span> &ndash; <span class="calendar-popover-end_date"></span></div>
                    <div><span class="calendar-popover-start_time"></span> &ndash; <span class="calendar-popover-end_time"></span></div>
                    <div class="calendar-popover-days"></div>
                    <div class="action_mytime">
                        <ul>
                            <li><a class="update" style="cursor: pointer;">Update</a></li>
                            <li><a class="delete" style="cursor: pointer;">Delete</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .background-status-attending { background-color: #95c810; color:#fff; }
    .background-status-absent    { background-color: #e61f59; color:#fff; }
    .background-status-late      { background-color: #f09022; color:#fff; }
    .background-status-present   { background-color: #67ab49; color:#fff; }

    .timetable-status-filters .dropdown-menu {
        font-size: 16px;
        padding: 0;
        width: 200px;
    }

    .timetable-status-filters .dropdown-menu > li + li {
        margin-top: 1px;
    }

    .timetable-status-filters .dropdown-menu > li > a {
        color: inherit;
        padding: .5em;
    }
    .timetable-status-filters .dropdown-menu > li > a:hover {
        background: none;
        opacity: .8;
    }

    .timetable-status-filters .form-checkbox {
        margin-bottom: 0;
    }

    [class*="icon-"].circled_icon {
        border-radius: 50%;
        display: inline-block;
        border: 1px solid;
        font-size: .66667em;
        width: 1.5em;
        height: 1.5em;
        text-align: center;
    }

    .booking-fullcalendar .fc-toolbar {
        background: none;
        box-shadow: none;
        padding: 0 0 25px;
    }

    .booking-fullcalendar .fc-toolbar .fc-left {
        padding: 10px;
        width: 100%;
    }

    .booking-fullcalendar .fc-toolbar .fc-left > div {
        width: 100%;
    }

    .booking-fullcalendar .fc-prev-button { float: left;  }
    .booking-fullcalendar .fc-next-button { float: right; }

    .booking-fullcalendar .fc-right .fc-button.fc-state-active,
    .booking-fullcalendar .fc-right .fc-button:hover{
        z-index: 1;
    }

    .booking-timetables {
        clear: both;
        position: relative;
    }

    #confirm_bulk_update:disabled{
        background-color: gray;
    }

    #booking_item_filter {
        text-align: center;
    }

    @media screen and (min-width: 768px) {

        .booking-fullcalendar .fc-right .fc-button {
            background: #f3f3f3;
            border: 1px solid #a2a2a2;
        }

        .booking-fullcalendar .fc-toolbar .fc-left {
            background: #f3f3f3;
            border-radius: 4px;
            box-shadow: 0 3px 0 #e0e0e0;
            margin-bottom: 20px;
        }

        .filter-wrap {
            float: left;
            position: absolute;
            top: 85px;
            width: 50%;
        }

        #booking_item_filter {
            float: left;
        }
    }
</style>


<script type="text/javascript">
    $(document).on('click', '.calendar-popover-close, .popup_close, .add_note_cancel', function(){
        $('.popover ').css('display', 'none');
        return false;
    });
    $(document).on('click', '.add_note_wrapper a', function(){
        $(this).siblings('ul').slideToggle("slow", "linear");
    });

    var defaultView = 'month';
    var calendar_data = null;
    var defaultDate = null;
    var has_parent_permission = <?=Auth::instance()->has_access('contacts3_limited_family_access') ? 'true' : 'false'?>;
    var is_teacher = <?=json_encode($is_teacher)?>;

    $(document).on('click', '#booking_item_filter .showBtn', function () {
        $(this).siblings('.toggleContent').slideToggle();
    });

    function print_list()
    {
        if ($('#booking-fullcalendar').fullCalendar('getView').name != 'listMonth') {
            defaultView = 'listMonth';
            show_timetable_booking_calendar(calendar_data);
        }
        window.print();
    }

    function search_calendar_data(booking_item_id)
    {
        for (var i in calendar_data) {
            if (calendar_data[i].booking_item_id == booking_item_id) {
                return calendar_data[i];
            }
        }

        return false;
    }

    function search_calendar_data_mytime(mytime_id)
    {
        for (var i in calendar_data) {
            if (calendar_data[i].mytime_id == mytime_id) {
                return calendar_data[i];
            }
        }

        return false;
    }

    function search_timetables_data()
    {
        var contact_id = $(this).data("contact_id");

        var attending = null;
        if ($("#booking_item_attending").prop("checked")) {
            attending = 1;
        }

        var timeslot_status = [];
        $("[name*=timeslot_status]").each(function(){
            if (this.checked) {
                timeslot_status.push(this.value);
            }
        });

        get_timetables_data(
            [contact_id],
            null,
            null,
            attending,
            timeslot_status,
            function (data) {
                show_timetable_booking_calendar(data);
            }
        );
    }

    $('.contacts-select_contact').on('change', '.family-member-checkbox', function()
    {
        setTimeout(
            function() {
                var $calendar       = $('#booking-fullcalendar');
                var contact_ids     = [];
                var attending       = $('#booking_item_attending').prop('checked') ? 1 : null;
                var timeslot_status = [];

                defaultView = $calendar.fullCalendar('getView').name;
                defaultDate = $calendar.fullCalendar('getDate');
                $(".family-member-checkbox").each(function(){
                    if (this.checked) {
                        contact_ids.push(this.value);
                    }
                });

                $('[name*=timeslot_status]:checked').each(function() {
                    timeslot_status.push(this.value);
                });

                get_timetables_data(
                    contact_ids,
                    null,
                    null,
                    attending,
                    timeslot_status,
                    function (data) {
                        show_timetable_booking_calendar(data);
                    }
                );
            },
            50
        )
    });

    var last_timetables_params = {include_mytime: 1};
    function refresh_timetables(callback)
    {
        defaultView = $('#booking-fullcalendar').fullCalendar('getView').name;
        get_timetables_data(
            last_timetables_params.contact_ids,
            last_timetables_params.after,
            last_timetables_params.before,
            last_timetables_params.attending,
            last_timetables_params.timeslot_status,
            callback ? callback : function (data) {
                show_timetable_booking_calendar(data);
            }
        );
    }

    $(document).on("click", ".filter-wrap .wrist-watch", function(){
        defaultView = $('#booking-fullcalendar').fullCalendar('getView').name;
        defaultDate = $('#booking-fullcalendar').fullCalendar('getDate');
        if ($(this).data("display-mytime") == "1") {
            $(this).data("display-mytime", "0");
            last_timetables_params.include_mytime = 0;
        } else {
            $(this).data("display-mytime", "1");
            last_timetables_params.include_mytime = 1;
        }

        refresh_timetables();
    });

    var get_timetables_data_progress = false;
    function get_timetables_data(contact_ids, after, before, attending, timeslot_status, callback)
    {
        if (get_timetables_data_progress) {
            return;
        }
        get_timetables_data_progress = true;
        last_timetables_params = {
            contact_ids: contact_ids,
            after: after,
            before: before,
            attending: attending,
            timeslot_status: timeslot_status,
            include_mytime: (contact_ids.length <= 1 || (last_timetables_params && last_timetables_params.include_mytime)) ? 1 : 0
        };

        $.post(
            '/frontend/contacts3/get_timetables_data',
            last_timetables_params,
            function (response) {
                calendar_data = response.data;
                if (callback) {
                    callback(response.data);
                }
                get_timetables_data_progress = false;
            }
        )
    }

    var $shownextclasstemplate = $(".show-next-class .template");
    $shownextclasstemplate.removeClass(".template").removeClass("hidden");
    $shownextclasstemplate.remove();
    var nextclassset_names = {};
    function show_timetable_booking_calendar(data)
    {
        var $calendar = $('#booking-fullcalendar');
        try {
            $calendar.fullCalendar('destroy');
        } catch (exc) {

        }

        $(".show-next-class .class-detail").remove();
        nextclassset_names = {};

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
            views: {
                'week': {
                    columnFormat: 'ddd D/M'
                },
                'month': {
                    eventLimit: 5
                }
            },
            defaultView: defaultView,
            events: data,

            eventRender: function(event, element, view) {
                if (event.mytime_id) { // my time
                    element.data('mytime_id', event.mytime_id);
                    element.popover({
                        title: '<a>' + event.description + '</a> <button type="button" class="btn-link calendar-popover-close right">&times;</button>',
                        placement: (view.type == 'listMonth' ? 'bottom' : 'right'),
                        container: '#booking-fullcalendar .fc-view',
                        selector: ':not(button):not(button *)', // buttons within the event have their own actions
                        html: true,
                        content: function () {
                            var $clone = $('.calendar-popover-mytime-wrapper.template').clone();
                            $clone.removeClass('template');
                            $clone.removeClass('hidden');
                            $clone.find('.calendar-popover').attr('data-mytime_id', event.mytime_id);
                            $clone.find('.calendar-popover-start_time').html(event.start_time);
                            $clone.find('.calendar-popover-end_time').html(event.end_time);
                            $clone.find('.calendar-popover-start_date').html(event.start_date);
                            $clone.find('.calendar-popover-end_date').html(event.end_date);
                            if (event.days && event.days.length) {
                                $clone.find('.calendar-popover-days').html(event.days.join(', '));
                            }

                            return $clone.html();
                        },
                        trigger: 'click focus'
                    });
                    element.find('.fc-content').append('<a class="wrist-watch"><i class="watch-icon"></i></a>');
                } else { // booking item
                    var ndate = new Date(clean_date_string(event.start));
                    var edate = new Date(clean_date_string(event.end));
                    var  stime = (ndate.getUTCHours() > 12 ? ndate.getUTCHours() - 12 : ndate.getUTCHours()) + ":" + (ndate.getUTCMinutes() < 10 ? '0' : '') + ndate.getUTCMinutes() + (ndate.getUTCHours() > 12 ? 'pm' : 'am');
                    var  etime = (edate.getUTCHours() > 12 ? edate.getUTCHours() - 12 : edate.getUTCHours()) + ":" + (edate.getUTCMinutes() < 10 ? '0' : '') + edate.getUTCMinutes() + (edate.getUTCHours() > 12 ? 'pm' : 'am');
                    if (!nextclassset_names[event.first_name + event.schedule_id]) {
                        var $nextclass = $shownextclasstemplate.clone();
                        nextclassset_names[event.first_name + event.schedule_id] = true;
                        $nextclass.find(".contact").html(event.first_name + "' next class");
                        $nextclass.find(".building").html(event.location);
                        $nextclass.find(".room-no").html(event.room);
                        $nextclass.find(".title").html(event.title + ", " + ndate.toDateString() + " " + stime);
                        $nextclass.find(".time").html("");
                        $(".show-next-class").append($nextclass);
                    }
                    element.data('booking_item_id', event.booking_item_id);
                    element.popover({
                        title: '<a>' + event.title + '</a> <button type="button" class="btn-link calendar-popover-close right">&times;</button>',
                        placement: (view.type == 'listMonth' ? 'bottom' : 'right'),
                        container: '#booking-fullcalendar .fc-view',
                        html: true,
                        content: function () {
                            var $clone = $('.calendar-popover-wrapper.template').clone();
                            $clone.removeClass('template');
                            $clone.removeClass('hidden');
                            $clone.find('.calendar-popover').attr('data-booking_item_id', event.booking_item_id);
                            $clone.find('.calendar-popover').attr('data-schedule_id', event.schedule_id).data('schedule_id', event.schedule_id);
                            $clone.find('.calendar-popover').attr('data-booking_type', event.booking_type).data('booking_type', event.booking_type);
                            $clone.find('.calendar-popover').attr('data-event_id', event.period_id).data('event_id', event.period_id);
                            $clone.find('.calendar-popover-category').html(event.category).attr('title', event.category);
                            $clone.find('.calendar-popover-location').html(event.location);
                            $clone.find('.calendar-popover-trainer').html(event.trainer);
                            $clone.find('.calendar-popover-start_time').html(stime);
                            $clone.find('.calendar-popover-end_time').html(etime);
                            $clone.find('.calendar-popover-registered').html(event.attending);

                            if (event.booked) {
                                $clone.find('.calendar-popover-is_attending').removeClass('hidden');

                                if (event.attending == 1) {
                                    $clone.find('.calendar-popover-is_attending .text').html('attending');
                                    $clone.find('.calendar-popover-is_attending .icon-check').removeClass('hidden');
                                } else {
                                    $clone.find('.calendar-popover-is_attending').addClass('no');
                                    $clone.find('.calendar-popover-is_attending .text').html('not attending');
                                    $clone.find('.calendar-popover-is_attending .icon-exclamation-circle').removeClass('hidden');
                                }

                            }
                            else {
                                $clone.find('.register_place').removeClass('hidden');
                            }

                            if (event.note == null) {
                                $clone.find('.action_notes').addClass('hidden');
                            }
                            return $clone.html();
                        },
                        trigger: 'click focus'
                    });
                    if (event.attending == 1) {
                        element.find('.fc-content').prepend('<div class="icon-class-name orange"><span class="icon_box"><i class="iconcheck" aria-hidden="true"></i></span><span class="event_name">' + event.first_name + '</span></div>');
                    } else {
                        //element.find('.fc-content').prepend('<div class="icon-class-name blue"><span class="icon_box"><span class="icon">&#9888</span></span><span class="event_name">' + event.first_name + '</span></div>');
                        element.find('.fc-content').prepend('<div class="icon-class-name orange"><span class="icon_box"><i class="text-danger circled_icon icon-exclamation" aria-hidden="true"></i></span><span class="event_name">' + event.first_name + '</span></div>');
                    }
                }

                if (view.type == 'listMonth') {
                    element.find('.fc-list-item-marker.fc-widget-content').prepend('<span class="contact_name"><i> ' + event.first_name + ' </i></span>');
                    if (event.booking_item_id) {
                        var icon = 'confirmed';
                        if (event.attending == 1) {
                            if (event.timeslot_status !== null) {
                                if (event.timeslot_status === "") {
                                    element.find('.fc-list-item-time.fc-widget-content').prepend('<i class="text-danger circled_icon icon-exclamation" aria-hidden="true"></i>');
                                } else if (event.timeslot_status.indexOf("Late") != -1) {
                                    element.find('.fc-list-item-time.fc-widget-content').prepend('<i class="red icon-check" aria-hidden="true"></i>');
                                } else if (event.timeslot_status.indexOf("Present") != -1) {
                                    element.find('.fc-list-item-time.fc-widget-content').prepend('<i class="icon-check blue" aria-hidden="true"></i>');
                                }
                            } else {
                                element.find('.fc-list-item-time.fc-widget-content').prepend('<i class="icon-check orange" aria-hidden="true"></i>');
                            }
                            //element.find('.fc-list-item-time.fc-widget-content').prepend('<span class="icon-' + icon + ' atten-' + icon + '"><a>&nbsp;</a></span>');
                        } else {
                            icon = 'notconfirmed';
                            element.find('.fc-list-item-time.fc-widget-content').prepend('<i class="text-danger circled_icon icon-exclamation" aria-hidden="true"></i>');
                        }

                        if (event.note) {
                            element.find('.fc-list-item-title').append(
                                '<button type="button" class="fc-list-item-note btn-link" data-note="'+event.note+'">' +
                                    '<span class="icon-book"></span>' +
                                '</button>'
                            );
                        }

                    }
                }
            },
            eventAfterRender: function(event, element) {
                // Add a class to days with events (needed for styling)
                var index = element.parent().index();
                element.closest('.fc-week').find('.fc-content-skeleton td').eq(index).addClass('fc-day-with-events')
            },
            dayClick: function(date, jsEvent, view)
            {
                /* When a day is clicked, show events for that day under the calendar (visible in mobile) */

                // Update the heading to reflect the selected day
                $('#booking-fullcalendar-events-mobile-day').text(date.format('dddd MMMM D, YYYY'));

                // Get events whose start time is on the selected day
                var events = $('#booking-fullcalendar').fullCalendar('clientEvents',  function(event){
                    return event.start.format('YYYY-MM-DD') === date.format('YYYY-MM-DD');
                });

                // Clone the table row template, fill in its data for each event and put the HTML after the calendar
                var $clone  = $('#booking-fullcalender-event-template-mobile').clone();
                var html    = '';
                var description;

                for (var i = 0; i < events.length; i++) {
                    description = '';
                    if (events[i].location) description += events[i].location;
                    if (events[i].trainer)  description += ' with ' + events[i].trainer;

                    $clone.find('.calendar-event-start_time').text(events[i].start.format('h:mm a'));
                    $clone.find('.calendar-event-end_time').text(events[i].end.format('h:mm a'));
                    $clone.find('.calendar-event-title').text(events[i].title);
                    $clone.find('.calendar-event-description').text(description);

                    html += '<tr>' + $clone.html() + '</tr>';
                }
                $clone.remove();

                $('#booking-fullcalendar-events-mobile-list').find('tbody').html(html);
            }
        });

        if (defaultDate) {
            $calendar.fullCalendar('gotoDate', defaultDate);
        }
    }

    $(document).ready(function(){

        $(".time-print-icon .print").on("click", print_list);
        $('#add_weekly_notes_popup [name="filter_scope"], #add_note_until [type="checkbox"]').on('change', function()
        {
            var checkbox = this;

            $(this).parents('#add_weekly_notes_popup').find('[type="checkbox"]').each(function(){
                if (checkbox != this) {
                    this.checked = false;
                }
            });
        });

        $('.timetable-notes-datepicker').datetimepicker({
            timepicker:false,
            format: 'd/m/Y',
            yearStart: new Date().getFullYear(),
            closeOnDateSelect: true
        });
        $(".timepicker").datetimepicker({
            datepicker:false,
            format: 'H:i',
            yearStart: new Date().getFullYear(),
            closeOnDateSelect: true
        });

        var contact_ids = [];
        $('#profile-select_contact .btn, .family-member-checkbox').each(function() {
            contact_ids.push($(this).data("contact_id"));
        });

        get_timetables_data(
            contact_ids,
            null,
            null,
            null,
            null,
            function (data) {
                show_timetable_booking_calendar(data);
            }
        );

        $(document).on('click', '.action_notes .view.view', function(){
            var popover = $(this).parents('.calendar-popover');
            var booking_item_id = parseInt(popover.data('booking_item_id'));
            var data = search_calendar_data(booking_item_id);
            if (data.note != null) {
                $("#view_note_popup .note-txt").html(data.note);
                $('#view_note_popup').css('display','block');
            }
        });

        $(document).on('click', '.fc-list-item-note', function()
        {
            var $popup = $('#view_note_popup');
            $popup.find('.note-txt').html($(this).attr('data-note'));
            $popup.css('display','block');
        });

        $(document).on('click', '#view_note_popup .popup_close', function()
        {
            $('#view_note_popup').css('display','none').find('.note-txt').html('');
        });

        $(document).on('click', '.action_notes .modify.modify', function(){
            var $popover = $(this).parents('.calendar-popover');
            var booking_item_id = parseInt($popover.data('booking_item_id'));
            var data = search_calendar_data(booking_item_id);

            if (data.note != null) {
                var $popup = $('#add_note_popup');
                var is_attending = $popover.find('.calendar-popover-is_attending').hasClass('no') ? 0 : 1;

                $popup.find('textarea')
                    .data('booking_item_id', booking_item_id)
                    .data('action', 'update')
                    .val(data.note);

                $popup.find('.is_attending').val(is_attending);

                $popup.css('display','block');
            }
        });

        $(document).on('click', '.action_notes .delete', function(){
            var popover = $(this).parents('.calendar-popover');
            var booking_item_id = parseInt(popover.data('booking_item_id'));
            var data = search_calendar_data(booking_item_id);
            if (data.note != null) {
                if (confirm("Are you sure you want to delete this note:" + data.note)) {
                    delete_note(
                        [booking_item_id],
                        function(){
                            $('.popover ').remove();
                            refresh_timetables();
                        }
                    );
                }
            }
        });

        $(document).on('click', '.add_note_wrapper li.one', function(){
            var popover = $(this).parents('.calendar-popover');
            var booking_item_id = parseInt(popover.data('booking_item_id'));
            $("#add_note_popup textarea").data('booking_item_id', booking_item_id);
            $("#add_note_popup textarea").data('action', 'add');
            $('#add_note_popup').modal('show');
        });

        $(document).on('click', '#add_note_popup-submit', function(){
            var textarea = $(this).parents('#add_note_popup').find('textarea');
            var booking_item_id = parseInt(textarea.data('booking_item_id'));
            var action = textarea.data('action');
            var note = textarea.val();

            var attending = $("#add_note_popup .is_attending").val();

            save_note(
                [booking_item_id],
                note,
                attending,
                action,
                function(){
                    $("#add_note_popup").modal('hide');
                    $('.popover ').remove();
                    refresh_timetables();
                }
            );
        });

        function save_note(booking_item_ids, note, attending, action, callback)
        {
            $.post(
                '/frontend/contacts3/timetables_save_note',
                {
                    booking_item_ids: booking_item_ids,
                    note: note,
                    attending: attending,
                    action: action
                },
                function (response) {
                    if (callback) {
                        callback(response);
                    }
                }
            )
        }

        function delete_note(booking_item_ids, callback)
        {
            $.post(
                '/frontend/contacts3/timetables_delete_note',
                {
                    booking_item_ids: booking_item_ids,
                },
                function (response) {
                    if (callback) {
                        callback(response);
                    }
                }
            )
        }

        $(document).on('click', '.add_note_wrapper li.one', function(){
            var popover = $(this).parents('.calendar-popover');
            var booking_item_id = parseInt(popover.data('booking_item_id'));
            $("#add_note_popup textarea").data('booking_item_id', booking_item_id);
            $("#add_note_popup textarea").data('action', 'add');
            return false;
        });

        $(document).on('click', '.add_note_wrapper li.until', function(){
            var popover = $(this).parents('.calendar-popover');
            var booking_item_id = parseInt(popover.data('booking_item_id'));

            var form = document.forms.add_note_until_form;

            var data = search_calendar_data(booking_item_id);
            var dt = new Date(clean_date_string(data.start));

            $('#add_note_until [name=datetime_from]').val(data.start);

            $('#add_note_until [name=date_to]').datetimepicker('destroy');
            $('#add_note_until [name=date_to]').datetimepicker({
                timepicker:false,
                format: 'd/m/Y',
                yearStart: new Date().getFullYear(),
                closeOnDateSelect: true,
                minDate: dt
            });

            $('#add_note_until .start-date').html(dt.getDate() + '/' + (dt.getMonth() + 1) + '/' + dt.getFullYear());
            $('#add_note_until .start-time').html(dt.getHours() + ':' + dt.getMinutes());

            form.timeslot_id.value = data.period_id;
            form.schedule_id.value = data.schedule_id;
            form.booking_item_id.value = data.booking_item_id;
            form.datetime_from.value = data.start;

            $('#add_note_until').modal('show');
            return false;
        });

        $(document).on('click', '#add_note_until [type=button].continue', function (){
            var form = this.form;
            var search_params = {};

            var booking_item = search_calendar_data(form.booking_item_id.value);

            var date_to = form.date_to.value.split('/');
            date_to = date_to[2] + '-' + date_to[1] + '-' + date_to[0];
            search_params.after = form.datetime_from.value;
            search_params.before = date_to + ' ' + form.time_to.value + ':00';

            if ($("#add_note_until #filter_scope_family").prop('checked')) {
                search_params.contact_ids = last_timetables_params.contact_ids;
            } else if ($("#add_note_until #filter_scope_contact").prop('checked')) {
                search_params.contact_ids = [booking_item.contact_id];
            } else if ($("#add_note_until #filter_scope_class").prop('checked')) {
                search_params.contact_ids = [booking_item.contact_id];
                search_params.schedule_id = form.schedule_id.value;
            } else {
                search_params.contact_ids = last_timetables_params.contact_ids;
            }

            if ($("#booking_item_attending").prop("checked")) {
                search_params.attending = 1;
            }

            search_params.timeslot_status = [];
            $("[name*=timeslot_status]").each(function(){
                if (this.checked) {
                    search_params.timeslot_status.push(this.value);
                }
            });

            $.post(
                '/frontend/contacts3/get_timetables_data',
                search_params,
                function (response) {
                    var $ul = $(".until_confirm .slider-wrapper ul");
                    $ul.html("");
                    if (response.data.length > 0) {
                        for (var i in response.data) {
                            var timeslot = response.data[i];
                            $ul.append(
                                    '<li data-booking_item_id="' + timeslot.booking_item_id + '">' +
                                    '<a>' +
                                    '<span>' + timeslot.start + '</span>' +
                                    '<span class="sub-name">' + timeslot.schedule + '</span>' +
                                    '<span class="circled_icon icon-exclamation" aria-hidden="true"></span>' +
                                    '</a>' +
                                    '</li>'
                            );
                        }
                        $('#add_note_until .until_confirm').removeClass('hidden');
                    } else {
                        $('#add_note_until .until_confirm').addClass('hidden');
                        alert("No data found");
                    }

                    sliderwrapper_set_size($ul);
                }
            );
        });

        $(document).on('click', '#add_note_until [type=button].confirm', function (){
            var booking_item_ids = [];
            $(".until_confirm .slider-wrapper li").each (function(){
                booking_item_ids.push($(this).data('booking_item_id'));
            });
            var note = $("#add_note_until #add_until_note").val();
            var attending = $("#add_note_until [name=attending]").val();

            save_note(
                booking_item_ids,
                note,
                attending,
                'update',
                function (response) {
                    close_add_note_until();
                    $('.popover ').remove();
                    refresh_timetables();
                }
            );
        });

        $(document).on('click', '#add_note_until .basic_close, #add_note_until .cancel', function(){
            close_add_note_until();
            return false;
        });


        function close_add_note_until()
        {
            $('#add_note_until').css('display', 'none');
        }

        function close_add_note_weekly()
        {
            $('#add_weekly_notes_popup').modal('hide');
        }

        $(document).on('click', '.add_note_wrapper li.weekly', function(){
            var popover = $(this).parents('.calendar-popover');
            var booking_item_id = parseInt(popover.data('booking_item_id'));

            var form = document.forms.add_weekly_note_form;

            var data = search_calendar_data(booking_item_id);
            var dt = new Date(clean_date_string(data.start));

            $('#add_weekly_notes_popup [name=date_to]').datetimepicker('destroy');
            $('#add_weekly_notes_popup [name=date_to]').datetimepicker({
                timepicker:false,
                format: 'd/m/Y',
                yearStart: new Date().getFullYear(),
                closeOnDateSelect: true,
                minDate: dt
            });

            $('#add_weekly_notes_popup [name=datetime_from]').val(data.start);

            $('#add_weekly_notes_popup .start-date').html(dt.getDate() + '/' + (dt.getMonth() + 1) + '/' + dt.getFullYear());
            $('#add_weekly_notes_popup .start-time').html(dt.getHours() + ':' + dt.getMinutes());

            form.timeslot_id.value = data.period_id;
            form.schedule_id.value = data.schedule_id;
            form.booking_item_id.value = data.booking_item_id;
            form.datetime_from.value = data.start;

            $(".sectioninner").addClass("zoomIn");
            $('#add_weekly_notes_popup .weekly_confirm').addClass('hidden');
            $('#add_weekly_notes_popup').modal();
            return false;
        });

        $(document).on('click', '#add_weekly_notes_popup .basic_close, #add_weekly_notes_popup .cancel', function(){
            close_add_note_weekly();
        });

        $(document).on('click', '#add_weekly_notes_popup [type=button].continue', function (){
            var form = this.form;
            var search_params = {};

            var booking_item = search_calendar_data(form.booking_item_id.value);

            var date_to = form.date_to.value.split('/');
            date_to = date_to[2] + '-' + date_to[1] + '-' + date_to[0];
            search_params.after = form.datetime_from.value;
            search_params.before = date_to + ' ' + form.time_to.value + ':00';

            if ($("#add_weekly_notes_popup #filter_scope_family_w").prop('checked')) {
                search_params.contact_ids = last_timetables_params.contact_ids;
            } else if ($("#add_weekly_notes_popup #filter_scope_contact_w").prop('checked')) {
                search_params.contact_ids = [booking_item.contact_id];
            } else if ($("#add_weekly_notes_popup #filter_scope_class_w").prop('checked')) {
                search_params.contact_ids = [booking_item.contact_id];
                search_params.schedule_id = form.schedule_id.value;
            } else {
                search_params.contact_ids = last_timetables_params.contact_ids;
            }

            search_params.weekdays = [];
            $('#add_weekly_notes_popup').find('[name="add-note_days[]"]').each(function() {
                if (this.checked) {
                    search_params.weekdays.push(this.value);
                }
            });

            if ($("#booking_item_attending").prop("checked")) {
                search_params.attending = 1;
            }

            search_params.timeslot_status = [];
            $("[name*=timeslot_status]").each(function(){
                if (this.checked) {
                    search_params.timeslot_status.push(this.value);
                }
            });

            $.post(
                '/frontend/contacts3/get_timetables_data',
                search_params,
                function (response) {
                    var $ul = $(".weekly_confirm .slider-wrapper ul");
                    $ul.html("");
                    if (response.data.length > 0) {
                        for (var i in response.data) {
                            var timeslot = response.data[i];

                            var attendance = '';
                            if(timeslot.attending == 1){
                                attendance = '<i class="circled_icon text-success icon-check" aria-hidden="true"></i>';
                            }else{
                                attendance = '<i class="circled_icon icon-exclamation" aria-hidden="true"></i>';
                            }

                            $ul.append(
                                    '<li data-booking_item_id="' + timeslot.booking_item_id + '">' +
                                    '<a>' +
                                    '<span>' + timeslot.start + '</span>' +
                                    '<span class="sub-name">' + timeslot.schedule + '</span>' +
                                    attendance +
                                    '</a>' +
                                    '</li>'
                            );
                        }
                        $('#add_weekly_notes_popup .weekly_confirm').removeClass('hidden');
                    } else {
                        $('#add_weekly_notes_popup .weekly_confirm').addClass('hidden');
                        alert("No data found");
                    }

                    sliderwrapper_set_size($ul);
                }
            );
        });

        $(document).on('click', '#add_weekly_notes_popup [type=button].confirm', function (){
            var booking_item_ids = [];
            $(".weekly_confirm .slider-wrapper li").each (function(){
                booking_item_ids.push($(this).data('booking_item_id'));
            });
            var note = $("#add_weekly_notes_popup #add_weekly_note").val();
            var attending = $("#add_weekly_notes_popup [name=attending]").val();

            save_note(
                booking_item_ids,
                note,
                attending,
                'update',
                function (response) {
                    close_add_note_weekly();
                    $('.popover ').remove();
                    refresh_timetables();
                }
            );
        });

        $("#booking_item_filter input[type=checkbox]").on("change", function(){
            var attending = null;
            if ($("#booking_item_attending").prop("checked")) {
                last_timetables_params.attending = 1;
            }

            last_timetables_params.timeslot_status = [];
            $("[name*=timeslot_status]").each(function(){
                if (this.checked) {
                    last_timetables_params.timeslot_status.push(this.value);
                }
            });

            refresh_timetables();
        });

        $(document).on("change", "[name=availability]", function(){
            if (this.value == "YES") {
                $(".for_availability").removeClass("hidden");
                $(".not_for_availability").addClass("hidden");
            } else {
                $(".for_availability").addClass("hidden");
                $(".not_for_availability").removeClass("hidden");
            }
        });

        $(document).on("click", ".fc-day-top", function(e){
            return false;
            /* disabled for now
            var day = '';
            // empty slots are just one big cell. no day info exist. just guess from clicked position
            var days = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];
            var date = $(this).data("date");
            var time = "00:00";

            var datetime = date + " " + time;
            var dt = new Date(datetime);

            $("#add_mytime_popup [name=subjects] option").prop("selected", false);
            if (is_teacher) {
                $("#add_mytime_popup .teacher_only").removeClass("hidden");
            } else {
                $("#add_mytime_popup .teacher_only").addClass("hidden");
            }
            $("#add_mytime_popup [name=mytime_id]").val("new");
            $("#add_mytime_popup [name=start_date]").val(formatdate(dt));
            $("#add_mytime_popup [name=end_date]").val(formatdate(dt));

            $('#add_mytime_popup').find('.timetable-add_time_days').prop('checked', false);

            $('#add_mytime_popup .confirm_conflicts').addClass('hidden');
            $("#add_mytime_popup").modal();
            */
        });

        $(document).on("click", ".fc-widget-content", function(e){
            if (this.children.length == 0 && $('#profile-select_contact .btn.active').length <= 1) { // empty slot
                var day = '';
                // empty slots are just one big cell. no day info exist. just guess from clicked position
                var day = Math.floor(e.offsetX / ($(this).width() / 7));
                var days = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];
                var date = $($(".fc-day-grid table tbody > tr > td")[day + 1]).data("date");
                var time = $(this).parent().data("time");

                var datetime = date + " " + time;
                var dt = new Date(clean_date_string(datetime));

                $("#add_mytime_popup [name=subjects] option").prop("selected", false);
                if (is_teacher) {
                    $("#add_mytime_popup .teacher_only").removeClass("hidden");
                } else {
                    $("#add_mytime_popup .teacher_only").addClass("hidden");
                }
                $("#add_mytime_popup [name=mytime_id]").val("new");
                $("#add_mytime_popup [name=start_date]").val(formatdate(dt));
                $("#add_mytime_popup [name=end_date]").val(formatdate(dt));
                $("#add_mytime_popup [name=start_time]").val(formattime(dt));
                $("#add_mytime_popup [name=end_time]").val(formattime(dt));

                $('#add_mytime_popup').find('.timetable-add_time_days').prop('checked', false);

                $('#add_mytime_popup .confirm_conflicts').addClass('hidden');
                $("#add_mytime_popup").modal('hide');
            }
        });

        $(document).on("click", "#add_mytime_popup fieldset.mytime_type", function(){
            $("#add_mytime_popup fieldset.mytime_type").removeClass("selected");
            $("#add_mytime_popup fieldset.mytime_type div.mytime_type").addClass("hidden");
            $(this).find("div.mytime_type").removeClass("hidden");
            $(this).addClass("selected");
        });

        $(document).on('click', '#add_mytime_popup .basic_close, #add_mytime_popup .cancel', function(){
            close_add_mytime();
        });

        function close_add_mytime()
        {
            $("#add_mytime_popup").modal('hide');
        }

        $(document).on("click", "#add_mytime_popup [type=button].confirm", function(){
            var warn_on_conflict = $(this).hasClass("conflict") ? false : true;
            save_mytime(warn_on_conflict);
        });
        function save_mytime(warn_on_conflict)
        {
            var fieldset = $("#add_mytime_popup fieldset.mytime_type.selected");
            if (fieldset.length == 0) {
                return false;
            }


            var availability = $("#add_mytime_popup [name=availability]:checked").val();
            var subjects = $('#add_mytime_popup').find('[name="subjects"]').val();;
            var mytime_id = $("#add_mytime_popup [name=mytime_id]").val();
            var description = $("#add_mytime_popup [name=description]").val();
            var color = $("#add_mytime_popup [name=color]").val();
            var start_date = fieldset.find("[name=start_date]").val();
            var start_time = fieldset.find("[name=start_time]").val();
            var end_date = fieldset.find("[name=end_date]").val();
            var end_time = fieldset.find("[name=end_time]").val();
            var days = [];
            var contact_id = $('#profile-select_contact').find('.btn.active').data("contact_id");

            $('#add_mytime_popup').find('.timetable-add_time_days').prop('checked', false);

            fieldset.find('.timetable-add_time_days:checked').each(function() {
                var daynames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                days.push(daynames[this.value]);
            });

            last_timetables_params.include_mytime = 1;
            $.post(
                "/frontend/contacts3/mytime_save",
                {
                    id: mytime_id,
                    availability: availability,
                    subjects: subjects,
                    color: color,
                    description: description,
                    start_date: start_date,
                    start_time: start_time,
                    end_date: end_date,
                    end_time: end_time,
                    days: days,
                    warn_on_conflict: warn_on_conflict ? 1 : 0,
                    contact_id: contact_id
                },
                function (response) {
                    if (response.warn) {

                        var $ul = $(".confirm_conflicts .slider-wrapper ul");
                        $ul.html("");
                        if (response.conflicts.length > 0) {
                            for (var i in response.conflicts) {
                                var conflict = response.conflicts[i];
                                $ul.append(
                                        '<li data-' + (conflict.mytime_id ?  'mytime_id' :  'booking_item_id') + '-="' + (conflict.mytime_id ?  conflict.mytime_id :  conflict.booking_item_id) + '">' +
                                        '<a>' +
                                        '<span>' + conflict.start + '</span>' +
                                        '<span class="sub-name">' + (conflict.schedule ? conflict.schedule : conflict.title) + '</span>' +
                                        '<i class="' + (conflict.mytime_id ? 'red' : 'orange') + '-circle icon-exclamation" aria-hidden="true"></i>' +
                                        '</a>' +
                                        '</li>'
                                );
                            }
                            $('#add_mytime_popup .confirm_conflicts').removeClass('hidden');
                        }
                        sliderwrapper_set_size($ul);
                    } else if(response.error) {

                    } else {
                        close_add_mytime();
                        refresh_timetables();
                    }
                }
            );
        }

        function sliderwrapper_set_size($ul)
        {
            $ul.each(function(){
                var width = 0;
                var $ul = $(this);
                $ul.find(">li").each(function(){
                    width += $(this).width();
                });

                $ul.css("width", width + "px");
                $ul.css("position", "absolute");
                $ul.css("left", "0px");
            });
        }

        $(".slider_action > a").on("click", function(){
            var n = $(this).hasClass("next_arrow") ? -1 : 1;
            var $ul = $(this).parents(".slider-wrapper").find("ul");
            var step =  Math.round($ul.width() / $ul.find(">li").length);
            var left = parseInt($ul.css("left"));
            left += step * n;
            if (n == 1 && left > 0) {
                left = 0;
            }
            if (n == -1 && left < -1 * ($ul.width() - $(this).parents(".slider-wrapper").width())) {
                left = -1 * ($ul.width() - $(this).parents(".slider-wrapper").width());
            }
            $ul.css("left", left + "px");
        });

        $(document).on('click', '.action_mytime .delete', function(){
            var popover = $(this).parents('.calendar-popover');
            var mytime_id = parseInt(popover.data('mytime_id'));

            if (confirm("Are you sure you want to delete this time")) {
                delete_mytime(
                    [mytime_id],
                    function(){
                        $('.popover').remove();
                        refresh_timetables();
                    }
                );
            }
        });

        $(document).on('click', '.action_mytime .update', function(){
            var popover = $(this).parents('.calendar-popover');
            var data = search_calendar_data_mytime(popover.data("mytime_id"));

            if (is_teacher) {
                $("#add_mytime_popup .teacher_only").removeClass("hidden");
            } else {
                $("#add_mytime_popup .teacher_only").addClass("hidden");
            }

            $("#add_mytime_popup [name=subjects]").prop("selected", false);
            for (var i in data.subjects) {
                $("#add_mytime_popup [name=subjects][value=" + data.subjects[i].subject_id + "]").prop("selected", true);
            }
            $("#add_mytime_popup #availability_yes").prop('checked', data.availability == 'YES');
            $("#add_mytime_popup #availability_none").prop('checked', data.availability != 'YES');
            if (data.availability == 'YES') {
                $(".for_availability").removeClass("hidden");
                $(".not_for_availability").addClass("hidden");
            } else {
                $(".for_availability").addClass("hidden");
                $(".not_for_availability").removeClass("hidden");
            }

            $("#add_mytime_popup [name=description]").val(data.description);
            $("#add_mytime_popup [name=start_date]").val(formatdate(new Date(clean_date_string(data.start_date))));
            $("#add_mytime_popup [name=end_date]").val(formatdate(new Date(clean_date_string(data.end_date))));
            $("#add_mytime_popup [name=start_time]").val(formattime(new Date(clean_date_string(data.start_date + " " + data.start_time))));
            $("#add_mytime_popup [name=end_time]").val(formattime(new Date(clean_date_string(data.end_date + " " + data.end_time))));
            $("#add_mytime_popup [name=mytime_id]").val(data.mytime_id);


            if (data.days && data.days.length) {
                $("fieldset.mytime_type.weekly .mytime_type").removeClass("hidden");
            } else if (data.start_date != data.end_date) {
                $("fieldset.mytime_type.daily .mytime_type").removeClass("hidden");
            } else {
                $("fieldset.mytime_type.one .mytime_type").removeClass("hidden");
            }
            $('#add_mytime_popup').find('.timetable-add_time_days').prop('checked', false);

            $(".confirm_conflicts").addClass("hidden");
            $("#add_mytime_popup").modal();
        });

        function delete_mytime(mytime_ids, callback)
        {
            $.post(
                "/frontend/contacts3/delete_mytime",
                {
                    ids: mytime_ids
                },
                function (response) {
                    if (callback) {
                        callback();
                    }
                }
            )
        }
    });

    function formatdate(dt)
    {
        return dt.getDate() + "/" + (dt.getMonth() + 1) + "/" + dt.getFullYear();
    }

    function formattime(dt)
    {
        return dt.getHours() + ":" + dt.getMinutes();
    }
</script>

<?php
if (Auth::instance()->has_access('contacts3_limited_family_access')) {
    include_once __DIR__.'/snippets/add_note_popup_calender.php';
    include_once __DIR__.'/snippets/add_note_until.php';
    include_once __DIR__.'/snippets/add_weekly_notes.php';
}
include_once __DIR__.'/snippets/view_note_popup_calender.php';
require_once __DIR__.'/snippets/add_mytime.php';
?>
