/* Namespace for general CMS data */
var cms_ns = {};
cms_ns.modified = false;
cms_ns.modified_inputs = [];

// Set the default number of results to display in datatables and the numbers to be selectable
$.fn.dataTable.defaults.iDisplayLength = parseInt(10);
$.fn.dataTable.defaults.aLengthMenu    = [[10], [10]];
$.fn.dataTable.defaults.sDom           = 'frtip';
$.fn.dataTable.defaults.fnDrawCallback = function() {
    $(this).if_first_datatable_move_filter();
};

// When the number is changed, save it as the user's preference
$(document).on('change', '.dataTables_length select', function()
{
	$.ajax({url: '/admin/profile/ajax_set_preference', data: {column: 'datatable_length', value: this.value}, method: 'post'});
});

/*
 * Notifying the user of unsaved changes
 */
window.onload = function()
{
    // When the user tries leaving the page, check for unsaved changes and notify them, if any.
    window.addEventListener('beforeunload', function(ev)
    {

        // If the link to leave the page has the class "skip-save-warning", proceed without giving the warning
        if (ev && ev.srcElement && $(ev.srcElement.activeElement).hasClass('skip-save-warning'))
        {
            return undefined;
        }

        // If no changes have been made, do nothing
        if ( ! cms_ns.modified)
        {
            return undefined;
        }

        // Set a custom message. This will not work in Firefox, which only uses its default message.
        var message = 'You have made changes to the following form fields. Are you sure you wish to leave this page without saving?';

        // Add list of changed fields to the message (max 5)
        for (var i = 0; i < cms_ns.modified_inputs.length && i < 5; i++)
        {
            message += "\n"+cms_ns.modified_inputs[i].name;
        }

        // Show the message
        (ev || window.event).returnValue = message; //Gecko + IE
        return message; //Gecko + Webkit, Safari, Chrome etc.
    });
};
// When a form field is changed, record that a change has been made
$(document).on('change', 'form :input', function(ev)
{
    // Check that the field has not been specifically excluded
    // Check that the change was made by a human.
    // Check that the field has a name. (Unnamed input is not usually sent to the server.)
    if ( ! $(this).hasClass('save-warning-exclude') && (ev.originalEvent || $(this).find('\+ .cke').length > 0) && this.name != '')
    {
        cms_ns.modified = true ;
        cms_ns.modified_inputs.push(this);
    }
});

// If the user clicks a save button or a delete confirmation, clear record of unsaved changes
$(document).on('click mouseup', '[type="submit"], .btn[id*="save"], .btn[name="save"], .btn:contains("Save"), .btn:contains("Complete"), .modal .btn:contains("Delete"), .btn-primary, .btn-success, .btn-info', function()
{
    cms_ns.modified = false;
    cms_ns.modified_inputs = [];
});

// If the user submits a form, clear record of unsaved changes
$('form').submit(function()
{
    cms_ns.modified = false;
    cms_ns.modified_inputs = [];
});

// Remove a specific field from the list of modified fields
cms_ns.clear_modified_input = function(input_name)
{
    if (cms_ns.modified_inputs[input_name])
    {
        delete cms_ns.modified_inputs[input_name];
        // If this was the only modified field, set modified to false
        if (cms_ns.modified_inputs.length > 0)
        {
            cms_ns.modified = false;
        }
    }
};

if( !window.disableScreenDiv ){
    window.disableScreenDiv = document.createElement( "div" );
    window.disableScreenDiv.style.display = "block";
    window.disableScreenDiv.style.position = "fixed";
    window.disableScreenDiv.style.top = "0px";
    window.disableScreenDiv.style.left = "0px";
    window.disableScreenDiv.style.right = "0px";
    window.disableScreenDiv.style.bottom = "0px";
    window.disableScreenDiv.style.textAlign = "center";
    window.disableScreenDiv.style.zIndex = 99999999;
    window.disableScreenDiv.innerHTML = '<div style="position:absolute;top:0;left:0;right:0;bottom:0;background-color:#000;opacity:0.2;filter:alpha(opacity=20);z-index:1;"></div>' +
        '<div class="ajax_loader_icon_inner" style="position:absolute;top:50%;left:50%;margin:-16px;z-index:2;"></div>';
    window.disableScreenDiv.autoHide = true;
    window.disableScreenDiv.hide = true;
    window.disableScreenDiv.style.visibility = 'hidden';
    document.body.appendChild(window.disableScreenDiv);
}

/* Table initialisation */
$(document).ready(function () {
    $(document).ajaxStart(function(){
        if( window.disableScreenDiv  && window.disableScreenDiv.hide ) {
            window.disableScreenDiv.style.visibility = "visible";
        }
    });
    $(document).ajaxStop(function(){
        if( window.disableScreenDiv  && window.disableScreenDiv.autoHide ){
            window.disableScreenDiv.style.visibility = "hidden";
        }
    });
    $(document).ajaxSend(function(e, x, o){
        console.log("ajax-start:" + o.url);
    });
    $(document).ajaxComplete(function(e, x, o){
        console.log("ajax-stop:" + o.url);
    });

    
    /* From: http://www.datatables.net/plug-ins/sorting */
    jQuery.extend( jQuery.fn.dataTableExt.oSort, {
        "num-html-pre": function ( a ) {
            var x = String(a).replace( /<[\s\S]*?>/g, "" );
            return parseFloat( x );
        },

        "num-html-asc": function ( a, b ) {
            return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },

        "num-html-desc": function ( a, b ) {
            return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
    } );

    // Initialise the datatables
    if($('#dataTable_in').length == 0 && $('#dataTable_out').length == 0){//Check for datatables in cashbook
        $('.dataTable').dataTable({
            "sPaginationType":"bootstrap",
            "bDestroy": true,
            "bRetrieve": true,
            "oLanguage":{
                "sLengthMenu":"_MENU_ records per page"
            },
            "aaSorting": []
        });

        // Move specified tools next to the searchbar
        $('[data-action_for_table]').each(function(index, element) {
            $(element).make_table_action($(element).data('action_for_table'));
        });
    }

    $('.dataTable').each(function()
    {
        var $table = $(this);

        // Open ".edit-link" when anywhere in the table row is clicked...
        // ... except for form elements or other links. (Clicking these have their own actions.)
        $table.on('click', 'tbody tr', function(ev)
        {
            if ($table.find('.edit-link').length) {
                // If the clicked element is a link or form element or is inside one, do nothing
                if ( ! $(ev.target).is('a, label, button, :input') && ! $(ev.target).parents('a, label, button, :input')[0]) {
                    // Find the edit link
                    var $edit_link = $(this).find('.edit-link');
                    var link = $edit_link.attr('href');

                    if (link) {
                        // If the user uses the middle mouse button or Ctrl/Cmd key, open the link in a new tab.
                        // Otherwise open it in the same tab
                        if (ev.ctrlKey || ev.metaKey || ev.which == 2) {
                            window.open(link, '_blank');
                        }
                        else {
                            window.location.href = link;
                        }
                    } else {
                        $edit_link.click();
                    }
                }
            }
        });

        $table.if_first_datatable_move_filter();
    });

    // Call the datepicker
    $('.datepicker').datepicker({
        format:'dd-mm-yyyy',
        autoclose: true,
        orientation:'bottom'
    });

    /* Daterange picker */
    cms_ns.initialize_daterangepickers = function()
    {
        try {
            var ranges = {};
            ranges['Yesterday']  = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
            ranges['Today']      = [moment(), moment()];
            ranges['Tomorrow']   = [moment().add(1, 'days'), moment().add(1, 'days')];

            ranges['Last Week']  = [moment().subtract(1, 'week').startOf('isoWeek'), moment().subtract(1, 'week').endOf('isoWeek')];
            ranges['This Week']  = [moment().startOf('isoWeek'), moment().endOf('isoWeek')];
            ranges['Next Week']  = [moment().add(1, 'week').startOf('isoWeek'), moment().add(1, 'week').endOf('isoWeek')];

            ranges['Last Month'] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];
            ranges['This Month'] = [moment().startOf('month'), moment().endOf('month')];
            ranges['Next Month'] = [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')];

            ranges['Last Year']  = [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')];
            ranges['This Year']  = [moment().startOf('year'), moment().endOf('year')];
            ranges['Next Year']  = [moment().add(1, 'year').startOf('year'), moment().add(1, 'year').endOf('year')];

            var $daterange = $('.form-daterangepicker-input:not(.initialized)');

            $daterange.daterangepicker({
                alwaysShowCalendars: true,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'DD/MMM/YYYY', // 'YYYY-MM-DD'
                    firstDay: 1
                },
                autoapply: false,
                autoUpdateInput: false,
                applyClass: 'btn btn-primary timeoff-daterange-apply',
                cancelClass: 'btn-cancel',
                linkedCalendars: false,
                ranges: ranges
            }, form_daterangepicker_callback);

            $daterange.data('daterangepicker').get_range_type = function() {
                switch (this.chosenLabel) {
                    case 'Yesterday':  return 'day';    break;
                    case 'Today':      return 'day';    break;
                    case 'Tomorrow':   return 'day';    break;
                    case 'Last Week':  return 'week';   break;
                    case 'This Week':  return 'week';   break;
                    case 'Next Week':  return 'week';   break;
                    case 'Last Month': return 'month';  break;
                    case 'This Month': return 'month';  break;
                    case 'Next Month': return 'month';  break;
                    case 'Last Year':  return 'year';   break;
                    case 'This Year':  return 'year';   break;
                    case 'Next Year':  return 'year';   break;
                    default:           return 'custom'; break;
                }
            };

            $daterange.addClass('initialized');

            $daterange.on('apply.daterangepicker', function(ev, arg = '') {
                const picker = $(ev.target).data('daterangepicker');

                if (arg != 'cancel') {
                    $(this).val(picker.startDate.format('DD/MMM/YYYY') + ' - ' + picker.endDate.format('DD/MMM/YYYY'));
                }
            });

            $daterange.on('cancel.daterangepicker', function(ev, picker) {
                var $wrapper = $daterange.parents('.form-daterangepicker');
                $wrapper.find('.form-daterangepicker-start_date').val('');
                $wrapper.find('.form-daterangepicker-end_date'  ).val('');

                $daterange.val('').trigger('apply.daterangepicker', 'cancel').trigger('change')
            });


            function form_daterangepicker_callback(start, end)
            {
                var $wrapper = this.element.parents('.form-daterangepicker');

                $wrapper.find($('.form-daterangepicker-start_date').val(start.format('YYYY-MM-DD')));
                $wrapper.find($('.form-daterangepicker-end_date'  ).val(  end.format('YYYY-MM-DD')));

                $wrapper.find('.form-daterangepicker-input').trigger('apply.daterangepicker').trigger('change');
            }

            $daterange.on('show.daterangepicker', function()
            {
                var $container = $daterange.data('daterangepicker').container;
                var $ranges    = $container.find('.ranges');

                $container.addClass('form-daterangepicker-container');
                if ($daterange.attr('id')) {
                    $container.attr('id', $daterange.attr('id')+'-popout');
                }

                // Add tabs to the range selector
                // A bit messy, but the daterangepicker does not natively support this
                if ($ranges.find('.nav-tabs').length == 0) {
                    var item_text;
                    var periods = 0;

                    $ranges.find('li').each(function() {
                        item_text = $(this).text().trim().toLowerCase();

                        $(this).addClass('form-daterangepicker-range');

                        if (item_text.indexOf('last') == 0 || item_text == 'yesterday') {
                            $(this).addClass('form-daterangepicker-range--last');
                        } else if (item_text.indexOf('next') == 0 || item_text == 'tomorrow') {
                            $(this).addClass('form-daterangepicker-range--next');
                        } else if (item_text.indexOf('period') == 0) {
                            if (periods == 0) {
                                $(this).before('<li>Period</li>');
                            }
                            $(this).addClass('form-daterangepicker-range--all form-dateranegpicker-range--period');
                            periods++;
                        } else if (item_text == 'custom range') {
                            $(this).addClass('form-daterangepicker-range--all');
                        } else {
                            $(this).addClass('form-daterangepicker-range--current');
                        }
                    });

                    var $tabs = $(
                        '<div class="nav nav-tabs form-daterangepicker-tabs">' +
                        '<button type="button" class="btn-link" data-tab="last">Last</button>' +
                        '<button type="button" class="btn-link" data-tab="current" class="active">Current</button>' +
                        '<button type="button" class="btn-link" data-tab="next">Next</button>' +
                        '</div>'
                    );

                    $tabs.prependTo($ranges);
                    $tabs.find('[data-tab]').on('click', function() {
                        $('.form-daterangepicker-range:not(.form-daterangepicker-range--all)').addClass('hidden');
                        $('.form-daterangepicker-range--'+$(this).data('tab')).removeClass('hidden');

                        $tabs.find('[data-tab]').removeClass('active');
                        $(this).addClass('active');
                    });

                    $container.find('.cancelBtn').removeClass('btn');

                    $tabs.find('[data-tab="current"]').trigger('click');
                }
            });

            $('.form-daterangepicker-prev, .form-daterangepicker-next').on('click', function () {
                var $wrapper         = $(this).parents('.form-daterangepicker');
                var $daterangepicker = $wrapper.find('.form-daterangepicker-input');
                var is_prev_btn      = $(this).hasClass('form-daterangepicker-prev');

                var date_from = $daterangepicker.data('daterangepicker').startDate;
                var date_to   = $daterangepicker.data('daterangepicker').endDate;
                var range     = $daterange.daterangepicker_get_range();

                // If the entire month is selected, go to the next entire month
                if (range.type == 'Month' && date_from.format('DD') === '01') {
                    is_prev_btn ? date_from.subtract(1, range.type).startOf('month') : date_from.add(1, range.type).startOf('month');
                    is_prev_btn ?   date_to.subtract(1, range.type).endOf('month') :   date_to.add(1, range.type).endOf('month');

                }
                // Move forward by a day, week, month or year
                else if (range.type != '') {
                    is_prev_btn ? date_from.subtract(1, range.type) : date_from.add(1, range.type);
                    is_prev_btn ?   date_to.subtract(1, range.type) :   date_to.add(1, range.type);
                }
                // Move forward by the number of days in the current range
                else {
                    is_prev_btn ? date_from.subtract(range.days_diff+1, 'days') : date_from.add(range.days_diff+1, 'days');
                    is_prev_btn ?   date_to.subtract(range.days_diff+1, 'days') :   date_to.add(range.days_diff+1, 'days');
                }

                $daterangepicker.data('daterangepicker').setStartDate(date_from);
                $daterangepicker.data('daterangepicker').setEndDate(date_to);

                $wrapper.find('.form-daterangepicker-start_date').val(date_from.format('YYYY-MM-DD'));
                $wrapper.find('.form-daterangepicker-end_date'  ).val(  date_to.format('YYYY-MM-DD'));

                $daterangepicker.trigger('apply.daterangepicker').trigger('change');
            });

        } catch (exception) {
            console.log(exception);
        }
    };

    try {
        cms_ns.initialize_daterangepickers();

        // return the the range type of the daterangepicker (day, week, month, year) and the number of days in the range
        $.fn.daterangepicker_get_range = function () {
            var date_from  = $(this).data('daterangepicker').startDate;
            var date_to    = $(this).data('daterangepicker').endDate;
            var days_diff  = Math.abs(date_from.diff(date_to, 'days'));
            var range_type = '';

            switch (days_diff) {
                case   0 :
                case   1 :
                    range_type = 'Day';
                    break;
                case   7 :
                    range_type = 'Week';
                    break;
                case 365 :
                case 366 :
                    range_type = 'Year';
                    break;
                default  :
                    if (date_from.clone().add(1, 'month').format('D M YYYY') == date_to.format('D M YYYY')) {
                        range_type = 'Month';
                    }
                    else if (date_from.clone().add(1, 'month').subtract(1, 'day').format('D M YYYY') == date_to.format('D M YYYY')) {
                        range_type = 'Month';
                    }
                    break;
            }

            return { 'type': range_type, 'days_diff': days_diff };
        };
    }
    catch (exception) {
        console.log(exception);
    }


    // Needed for input info popups
    $('.popinit').popover({ html : true });

    // init bootstrap-tooltip
    $('a[rel="tooltip"]').tooltip();

    // When a popover is hidden by .popover('hide'), unset its states
    // Otherwise it will be necessary to click the trigger twice to reopen it
    $('body').on('hidden.bs.popover', function (ev) {
        $(ev.target).data('bs.popover').inState = { click: false, hover: false, focus: false };
    });

    // handler tabs, page reloads & browser forward/back history.
    var History = window.History;
    if (!History.enabled) {
        return false;
    }
    $(window).bind('load statechange', function () {
        var State = History.getState();
        var hash = History.getHash();

        // Our default tab.
        if (!State.data || !State.data.tab) {
            if (hash) {
                State.data.tab = hash;
                window.location.hash = '';
            } else {
                State.data.tab = 'DEFAULT ACTIVE TAB';
            }
        }

        $('ul.nav-tabs > li > a[href="#' + State.data.tab + '"]').tab('show');
    });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (event) {

        // Set the selected tab to be the current state. But don't update the URL.
        var url = event.target.href.split("#")[0];
        var tab = event.target.href.split("#")[1];

        var State = History.getState();

        // Don't set the state if we haven't changed tabs.
        if (State.data.tab != tab) {
            History.pushState({'tab':tab}, null, url);
        }
    });

    /** Stop content moving around when the tab is changed **/
    $(document).on('show.bs.tab', '[data-toggle="tab"]', function(ev) {
        // If the tabs are inside a modal, their position will be unaffected by a change in modal height.
        // So the function can be left now, because there is nothing more to do.
        if ($(this).parents('.modal').length > 0) {
            return;
        }

        // Get the height of the current tab
        var $next_tab = $($(this).attr('href'));
        var $current_tab = $next_tab.parents('.tab-content').find(' > .tab-pane.active');

        // Add space to the bottom of the page to stop it moving if we switch to a smaller tab.
        // The height of the current tab is the most this will ever need to be.
        // Although in most cases, it will be too much. We will remove the extra space later.
        $('body').css('margin-bottom', $current_tab.height());
    });

    $(document).on('shown.bs.tab', '[data-toggle="tab"]', function(ev) {
        // Reduce the excess space added by the "show.bs.tab" to just enough to show the tab without scrolling

        // Get the distance between the bottom of the body (excluding margin) and the bottom of the viewport.
        var $body = $('body');
        var body_coordinates = $body[0].getBoundingClientRect();
        var new_margin = $(window).height() - body_coordinates.bottom;

        // If greater than 0, set it is a the margin. If less than 0, the body already reaches the bottom of the viewport.
        new_margin = new_margin <= 0 ? '' : new_margin;
        $body.css('margin-bottom', new_margin);
    });
        
        
        //stacktrace -> application logs
        $('.stacktrace').hide();
        
        $('.stacktrace_trigger').on({
            
            click: function() {

            $('.stacktrace').hide();
            
            var status = $($('#st_' + $(this).attr('data-id'))).attr('data-status');
            
            if(status == '1') {
                
                $('#st_' + $(this).attr('data-id')).hide();
                $($('#st_' + $(this).attr('data-id'))).attr('data-status', '0');
                
            } else {
                
               $('#st_' + $(this).attr('data-id')).show(); 
               $($('#st_' + $(this).attr('data-id'))).attr('data-status', '1');
                
            }
            
            return false; 
        }
            
        });

    $(".jira_description").hover(function(){
        $(this).children('span.description').show();
    },function(){
        $(this).children('span.description').hide();
    });

    $('body').on('click', function(ev)
    {
        // Hide dropouts when clicked away from
        if ( ! $(ev.target).closest('.dropout').length && ! $(ev.target).closest('.expand-dropout').length) {
            $('.expand-dropout.expanded').removeClass('expanded').find('\+ .dropout').hide();
        }

        // Hide widgets' actions dropdowns when clicked away from
        if ( ! $(ev.target).closest('.widget-actions-dropdown-toggle').length) {
            $('.widget-actions-dropdown-toggle').removeClass('active');
        }

        if ($(ev.target).closest('.dropdown.action-btn').length == 0) {
            $('.dropdown.action-btn [data-toggle="collapse"][aria-expanded="true"]').trigger('click');
        }
    });

    $('.expand-dropout').on('click', function(ev)
    {
        ev.preventDefault();
        if ($(this).hasClass('expanded'))
        {
            // Hide this dropout
            $(this).find('\+ ul').hide();
            $(this).removeClass('expanded');
        }
        else
        {
            // Hide currently expanded dropouts
            $('.expand-dropout.expanded').removeClass('expanded').find('\+ ul').hide();

            // show this dropout
            $(this).find('\+ ul').show();
            $(this).addClass('expanded');
        }
    });

    // Hide sidebars when the icon is clicked and save the preference as a cookie
    $('#sidebar-toggle, #sidebar-footer-toggle').on('click', function(ev)
    {
        ev.preventDefault();
        var column;
        var $body = $('body');

        if ($body.hasClass('sidebar-collapsed')) { // Expand
            $body.removeClass('sidebar-collapsed').removeClass('sidebar-preference-collapsed');
            column = '2_col';
        } else { // Collapse
            // Collapse the main sidebar
            $body.addClass('sidebar-collapsed').addClass('sidebar-preference-collapsed');
            $body.removeClass('sidebar_state--level1_collapsed');
            column = 'none';
        }

        // Don't cover the screen every time this is run
        var prev_xhr_hide = window.disableScreenDiv.hide;
        window.disableScreenDiv.hide = true;

        $.post('/admin/profile/save_user_column_preference', {column:column}, function() {
            // Restore the AJAX screen cover setting
            window.disableScreenDiv.hide = prev_xhr_hide;
        });

        // Save the state, as the user's preference in a cookie
        //$.ajax('/admin/settings/ajax_save_sidebar_state/'+expanded); // set cookie to remember the state

        $(window).trigger(':ib-sidebar-toggle');
    });

    $(document).on('click', '.widget-actions-dropdown-toggle', function(ev)
    {
        ev.preventDefault();
        if ($(this).hasClass('active'))
        {
            $(this).removeClass('active');
        }
        else
        {
            $(this).addClass('active');
        }
    });

    $(document).on('click', '.widget_minimize_button', function(ev)
    {
        ev.preventDefault();
        var widget = $(this).parents('.widget_container').eq(0);
        var widget_body = widget.find('.widget-body');
        if (widget_body.is(':visible'))
        {
            widget_body.fadeOut();
            if (this.innerHTML == 'Minimise') this.innerHTML = 'Expand';
        }
        else
        {
            widget_body.fadeIn();
            if (this.innerHTML == 'Expand') this.innerHTML = 'Minimise';
        }
    });

    $(document).on('click', '.widget_close_button', function()
    {
        $(this).parents('.widget_container').remove();
    });

});

$.fn.ib_serverSideTable = function(url, custom_dt_options, args) {
    try {
        args = args || {};
        args.responsive = (typeof args.responsive == 'undefined') ? true : args.responsive;
        custom_dt_options = custom_dt_options || {};
        var $table = $(this);
        if ($table.length == 0) {
            return;
        }

        if (args.responsive) {
            $table.addClass('dataTable-collapse');
        }

        var dt_options = {
            "bDestroy"     : false,
            "bAutoWidth"   : false,
            "oLanguage"    : {"sInfoFiltered": ''},
            // disableScreenDiv JS already adds a blackout+spinner for all AJAX requests.
            // Turning off the DataTables one to avoid duplicate spinners.
            "bProcessing"  : false,
            "aLengthMenu"  : [10],
            "bServerSide"  : true,
            "sAjaxSource"  : url,
            "aoColumnDefs" : [{
                "aTargets" : [1],
                "fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                    if (args.row_data_ids) {
                        // Add data attribute, with the ID to each row
                        $(nTd).parent().attr({'data-id': oData[0]});
                    }
                }
            }],
            "fnDrawCallback": function() {
                if (args.responsive || $table.hasClass('dataTable-collapse')) {
                    // Add data attribute containing the column name to each table cell.
                    // This is necessary for displaying the label next to each cell on mobile
                    var $headings = $table.find('thead:first th');
                    var column_number, $heading, heading_text;

                    $table.find('tbody > tr > td').each(function () {
                        column_number = $(this).index();
                        $heading = $($headings[column_number]);

                        // If the table heading contains a filter, just get the label, rather than everything
                        heading_text = $heading.find('.datatable-multiselect-label').length
                            ? $heading.find('.datatable-multiselect-label').text()
                            : $heading.text();

                        $(this).data('label', heading_text).attr('data-label', heading_text);
                    });
                }

                if ($(this).find('.edit-link').length) {
                    $(this).addClass('dataTable-clickable-rows');
                }

                // Additional callback functions
                if (args.draw_callback && typeof args.draw_callback == 'function') {
                    args.draw_callback();
                }
            }
        };
        if (args.ajax_callback) {
            dt_options.fnServerData = function(sSource, aoData, fnCallback)
            {
                $.ajax({
                    'dataType': 'json',
                    'type': custom_dt_options.sServerMethod || 'GET',
                    'url': sSource,
                    'data': aoData,
                    'success': [fnCallback,args.ajax_callback]
                });
            };
        }

        // Merge default and custom DataTable options
        Object.assign(dt_options, custom_dt_options);
        $table.dataTable().fnDestroy(); // Destroy the autoloaded table.
        $table.dataTable(dt_options);

        $table.if_first_datatable_move_filter();

    } catch (exc) {
        console.log(exc);
    }
};


// If a table is the first datatable in the content area, move its search bar next to the plugin tools
$.fn.if_first_datatable_move_filter = function() {
    var $table = $(this);

    // If this is the first datatable in the content area, move its search bar next to the plugin tools
    var $first_content_datatable = $('#page-container').find('.dataTable:first');

    var is_first_datatable = $first_content_datatable.length && $table.length && $first_content_datatable[0] == $table[0];
    var filter_fixed = $table.data('fixed_filter');

    if (is_first_datatable && !filter_fixed) {
        var $filter = $table.parents('.dataTables_wrapper').find('.dataTables_filter');
        var $plugin_tools = $('#plugin_tools');

        if ($filter.length && $plugin_tools.length) {
            // If there is an existing filter in tools menu, remove it.
            $plugin_tools.find('.dataTables_filter').remove();

            // Move the table's filter
            $filter.find('input').addClass('form-input');
            $filter.addClass('left').detach().prependTo('#plugin_tools');

            // The code should prevent duplicates filters appearing. This is just in case.
            $table.parents('.dataTables_wrapper').addClass('dataTables_wrapper-hidden_filter');
        }
    }
};

$.fn.make_table_action = function(table) {
    const $filter = $(table).parents('.dataTables_wrapper').find('.dataTables_filter');
    $(this).addClass('right ml-2').detach().appendTo($filter.find('label'));
    $filter.addClass('usermenu-wrapper');
    $filter.find('label').addClass('d-flex align-items-center');
    $filter.find('input').addClass('form-input w-auto ml-1');
};

// Reload the page on cancel
$(".cancel").click(function () {
    window.location.href = window.location.href;
});

function toggle_tab_field_details_click(field){
    //Set Policies tab clicked
    if(field == 'mapTab'){
        $('#'+field).click();
        //any other Fields are using the following format: tab_field_details
    }else $('#tab_'+field+'_details').click();
}

function load_documents(){
    $('#documents_list_ajax').html('<div class="ajax_loader_icon_inner"></div>');
    $.ajax({ url:'/admin/contacts/load_documents_ajax/'+contact_id , global: false}).done(function (data)
    {
        $('#documents_list_ajax').html(data);
    });
}
$(document).ready(function(){
    //General AJAX Errors handler
    $(document).bind("ajaxError", function(event, request, options){
        //If some error happens, check if the error is a logout, else send a message
        if (request.getResponseHeader('login_header') === '1') {
            window.location = '/';
        }
        else {
            //This error mainly happens when the connexion is lost. This error still happening if the ajax get any error and the request fail
            //SetTimeout avoid the call of this function every time the user click a link while a Ajax call is running
            if(request.statusText != 'abort'){
                //setTimeout("alert('There is a problem with your connection.Please refresh you browser to reconnect.')", 8000);
            }
        }
        }).bind("ajaxSuccess", function(event,request,settings) {
            if (request.getResponseHeader('login_header') === '1') {
                window.location = '/';
            }
        }).bind("ajaxStart", function(){
            //start_ajax_loader();
        }).bind("ajaxStop", function(){
            //end_ajax_loader();
        });


    // Apply Bootstrap multiselect JS to IB form multiselects, if the multiselect plugin has been loaded
    window.ib_initialize_multiselects = function() {
        $('.form-select--multiple:not(.initialized), .form-select[data-multiselect_options]:not(.initialized)').each(function () {
            var args = $(this).data('multiselect_options') || {};

            args.buttonText = function(options, $select) {
                var $input = $(options.context).parents('.form-select--multiple').find('.form-input');
                $input.addClass('form-input--active');
                var ms_args = $select.parents('.form-select').data('multiselect_options');

                if (options.length === 0) {
                    $input.removeClass('form-input--active');
                    return args.defaultText || '';
                }
                else if (options.length > ((ms_args && ms_args.numberDisplayed) || 3)) {
                    return options.length + ' selected';
                }
                else {
                    var selected = [];
                    options.each(function() {
                        selected.push([$(this).text(), $(this).data('order')]);
                    });

                    selected.sort(function(a, b) {
                        return a[1] - b[1];
                    });

                    var text = '';
                    for (var i = 0; i < selected.length; i++) {
                        text += selected[i][0] + ', ';
                    }

                    return text.substr(0, text.length -2);
                }
            };

            args.onInitialized = function (select, container) {
                var total = select.find('option').length;
                container.find('.multiselect-filter').after(
                    '<li class="multiselect-item multiselect-counters">' +
                    '   <span class="multiselect-counters-unfiltered">' +
                    '       <span class="multiselect-counters-total">' + total + '</span> records found.' +
                    '   </span>' +
                    '   <span class="multiselect-counters-filtered hidden">' +
                    '       Showing <span class="multiselect-counters-shown">' + total + '</span> of <span class="multiselect-counters-total">' + total + '</span> records.' +
                    '   </span>' +
                    '</li>'
                );
            };

            args.onFiltering = function(filter) {
                var $container  = this.$container;
                var visible_lis = $('li:not(.divider):not(.disabled):not(.multiselect-group):not(.multiselect-all):not(.multiselect-filter):not(.multiselect-collapisble-hidden):not(.multiselect-collapsible-hidden):not(.multiselect-counters)', $container).filter(':visible');

                $container.find('.multiselect-counters-shown').html(visible_lis.length);

                if (filter.value.trim()) {
                    $container.find('.multiselect-counters-unfiltered').addClass('hidden');
                    $container.find('.multiselect-counters-filtered').removeClass('hidden');
                } else {
                    $container.find('.multiselect-counters-filtered').addClass('hidden');
                    $container.find('.multiselect-counters-unfiltered').removeClass('hidden');
                }
            };

            $(this).addClass('.initialized').find('select').multiselect(args);
        });
    };

    try {
        ib_initialize_multiselects();
    } catch (exc) {
        console.log("no multiselect");
        console.log(exc);
    }

    // Apply CodeMirror JS if CodeMirror has been loaded
    try {
        // Apply the colour pickers
        var variable_cm;
        $('.form-input-colorpicker').each(function()
        {
            variable_cm = new CodeMirror.fromTextArea(this, {
                colorpicker: { mode: 'edit' }
            });
            variable_cm.setSize('100%', '2.75em');
        });

        // Apply to larger text editors
        var editor, rows, line_height;
        $('.code_editor').each(function()
        {
            editor = new CodeMirror.fromTextArea(this, {
                colorpicker   : { mode: 'edit' },
                extraKeys     : {"Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }},
                foldGutter    : true,
                gutters       : ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                lineNumbers   : true,
                matchBrackets : true,
                mode          : $(this).data('mode'),
                readOnly      : (this.disabled || this.readOnly)
            });

            // Set the height of the editor to match the number of lines in the textarea
            rows        = $(this).attr('rows') || 15;
            line_height = parseInt($(this).parent().css('lineHeight'));
            editor.setSize('100%', line_height * rows + 8); // add 8 for the padding

            $(editor.getWrapperElement()).resizable({
                resize: function() {
                   editor.setSize('100%', $(this).height());
                }
            });

        });

    } catch (exc) {
        console.log('Code Mirror has not been loaded.');
    }

    /** Other colour picker **/
    function rgb2hex(rgb){
        rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
        return (rgb && rgb.length === 4) ? "#" +
        ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
    }

    /* Colour picker */
    var $custom_color_link = $('.custom_color_link');

    // Dismiss palette when clicked away from
    $(window).on('click', function(ev)
    {
        ev.stopPropagation();
        $('.color_palette').hide();
    });
    // Show palette when colour box is clicked
    $('.select_color_preview, .select_color_text, .custom_color_link').on('click', function(ev)
    {
        ev.stopPropagation();
        $(this).parents('.color_picker_wrapper, .controls, .form-group').find('.color_palette').show();
    });

    // Set colour, when a colour from the palette is clicked
    $('.color_palette').on('click touchup', 'tbody td[style]:not([colspan])', function()
    {
        var color  = ($(this).hasClass('transparent_option')) ? 'transparent' : $(this).css('background-color');
        var $section = $(this).parents('.color_picker_wrapper');
        $section.find('.select_color_preview').css('background-color', color);
        $(this).parents('.form-group-colorpicker').find('input').val(rgb2hex(color));
        $section.find('.color_palette').hide();
    });

    // make spectrum appear when "custom" is clicked
    $custom_color_link.on('click touchup', function(ev)
    {
        ev.preventDefault();
        $(this).find('input').spectrum({
            change: function (color) {
                var $section = $(this).parents('.form-group-colorpicker');
                $section.find('.color_picker_input').val(color.toHexString());
                $section.find('.select_color_preview').css('background-color', color.toHexString());
                $('.color_picker_input').val(color.toHexString());
                $section.find('.color_palette').hide();
            }
        });
        $(this).find('.sp-replacer').click();
        $('.sp-container').appendTo($(this).parents('.color_picker_wrapper').find('.custom_color_link'));
    });

    // put value from spectrum into empty custom colour cell
    $custom_color_link.find('input').on('change', function()
    {
        var $custom_palette = $(this).parents('.controls').find('.custom_palette');
        // Create a new row, if necessary
        if ($custom_palette.find('td:not([style])').length == 0)
        {
            $custom_palette.append('<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
        }

        var color = $(this).find('.sp-preview-inner').css('background-color');
        $custom_palette.find('td:not([style])').first().css('background-color', color);
    });


    $('.color_picker_input').on('change', function() {
        $(this).parents('.form-group').find('.select_color_preview').css('background-color', this.value);
    });
    /** Colour picker - end **/

    // Items invisible when CodeMirror is run are not rendered correctly. Refresh the CodeMirror instance when they become visible
    $('.collapse').on('shown.bs.collapse', function() {
        this.querySelectorAll('.CodeMirror').forEach(function(element) {
            element.CodeMirror.refresh();
        });
    });
    $('.nav-tabs a').on('shown.bs.tab', function(event){
       $(event.target.getAttribute('href')).find('.CodeMirror').each(function() {
            this.CodeMirror.refresh();
        });
    });



    $('.ckeditor-simple').each( function()
    {
        CKEDITOR.replace(this.id , {
            toolbar :
                [
                    [
                        'Format', '-',
                        'Bold', 'Italic', 'Underline', 'Strike', 'TextColor', 'RemoveFormat', '-',
                        'NumberedList', 'BulletedList', '-',
                        'Outdent', 'Indent', '-',
                        'JustifyLeft', 'JustifyCenter', 'JustifyRight', '-',
                        'Image', 'Link', 'Unlink', 'Table', 'SpecialChar', '-',
                        'Undo', 'Redo'
                    ]
                ],
            height : '100px'
        });
    });


    // Convert a specified textarea to a CKEditor for emails.
    $.fn.ckeditor_email = function() {
        CKEDITOR.replace(this.attr('id') , {
            toolbar :
                [
                    [
                        'Format', '-',
                        'Bold', 'Italic', 'Underline', 'TextColor', 'RemoveFormat', '-',
                        'NumberedList', 'BulletedList', 'Outdent', 'Indent',
                        'JustifyLeft', 'JustifyCenter', 'JustifyRight'
                    ],
                    [
                        'Link', 'Unlink'
                    ]
                ],
            height : '150px'
        });

        return this;
    };

    $('.ckeditor-email').each(function() {
        $(this).ckeditor_email();
    });

    // Add autocomplete to form fields that specify the options in a data attribute
    $('.form-autocomplete[data-autocomplete-options]').autocomplete({
        source: function(request, response) {
            var options = $(this.element).data('autocomplete-options');

            var results = $.ui.autocomplete.filter(options, request.term);
            response(results.slice(0, 10));
        },
        messages: {
            minLength: 1
        },
        minChars: 0
    }).focus(function(){
        $(this).autocomplete('search', $(this).val());
    });

});
/**
 *
 * Add functionality for abort all ajax queries, prevent the ajax error when clicking a link or sending a form
 * Please use the function $.xhrPool.abortAll() before uploading files to prevent the display of errors.
 */
$.xhrPool = [];
$.xhrPool.abortAll = function() {
    $(this).each(function(idx, jqXHR) {
        jqXHR.abort();
    });
    $.xhrPool.length = 0
};

$.ajaxSetup({
    beforeSend: function(jqXHR)
    {
        if ( ! $.xhrPool) $.xhrPool = [];

        $.xhrPool.push(jqXHR);
    },
    complete: function(jqXHR)
    {
        if ( ! $.xhrPool) $.xhrPool = [];

        var index = $.xhrPool.indexOf(jqXHR);
        if (index > -1) {
            $.xhrPool.splice(index, 1);
        }
    }
});


var is_ajax_loading = 0; //Sets the number of currents AJAX calls
var ajax_loading_time = 0; //Set the duration of the ajax calls
var interval; //Handler for the SetInterval call.

/**
 * DON'T USE THIS FUNCTION IF THE FUNCTION IS CALLED IN THE GLOBAL AJAX EVENTS (PREVENT REDUNDANT CALLS)
 *
 * Call this function for a  Ajax loading in large loads.
 * This function show a loading animation and prevents the user to click elsewhere
 *
 * You can call the function as many times as you want even if the previous call has not ended
 * Don't forget to call the end_ajax_loader() at the end of each call.
 */
function start_ajax_loader(recursive_call) {
    var wait = 300; //Time to wait until the animation is show, if the animation is less than this variable the ajax animation will be skipped
    var timeout = 18000; //Time to wait for the ajax request before display the alert msg.
    var msg_alert = 'The server connection has timed out. Please refresh you browser to reconnect.';
    //if is set the parameter is because is a nested calla (seTimeout);
    if (recursive_call) { //If this function is called recursively
        var now = new Date;
        if(now.getTime() - ajax_loading_time > wait){ //If is long than 'wait' then show the animation
            //General load screen
            if($('#ajax_loader').size() == 0) {
                var html = '<div id="ajax_loader" style="display:none;"><div id="ajax_loader_background"><div id="ajax_loader_icon"></div></div></div>';
                $('body').append(html);
                $('#ajax_loader').fadeIn();
            }
            if (is_ajax_loading == 0) {
                clearTimeout(interval);
            }
            else{
                if(now.getTime() - ajax_loading_time > timeout){
                    //alert(msg_alert);
                }
                else{
                    interval = setTimeout('start_ajax_loader("true")',wait);
                }
            }
        }
    }
    else{
        //If is not loading another Ajax request, then, set time out
        if(!is_ajax_loading > 0){
            is_ajax_loading++;
            var now = new Date;
            ajax_loading_time = now.getTime();
            interval = setTimeout('start_ajax_loader("true")',wait);
        }
        else{
            is_ajax_loading++;
        }
    }
}
function end_ajax_loader(){
    is_ajax_loading--;
    if(is_ajax_loading == 0){
        clearTimeout(interval);
        $('#ajax_loader').remove();
    }
}


/**
 * Function used to set up an ERROR MESSAGE similar to the PHP Error messages generated by the system.
 *
 *
 * @param error_msg_area_id <i>OPTIONAL</i> The ID of the HTML <code>DIV</code>, or other element which is dedicated to hold the error message.
 *                             If not specified, will be defaulted to: <code>error_msg_area</code>
 * @param error_msg The message to be displayed in the specified ERROR AREA.
 */
function show_error_msg(error_msg, error_msg_area_id){

    //check if the error_msg_area_id was set, and if not, default it to: error_msg_area
    if(typeof error_msg_area_id == 'undefined' || error_msg_area_id == null) error_msg_area_id = "error_msg_area";

    var formatted_err_msg = '';
    //check if the error message was already formatted with the corresponding ALERT-styels or not
    if(error_msg.indexOf('<div class=') == -1){
        formatted_err_msg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Warning: </strong>'+
                                error_msg+
                            '</div>';
    }else formatted_err_msg = error_msg;

    if($('#'+error_msg_area_id).html().length !== null && $('#'+error_msg_area_id).html().length > 0){
        $('#'+error_msg_area_id).html(
                $('#'+error_msg_area_id).html() +
                formatted_err_msg
        );
    }else $('#'+error_msg_area_id).html(formatted_err_msg);

    // show the Msg
    if ($('#'+error_msg_area_id).html().length > 0 && $('#'+error_msg_area_id).css('display') == 'none') $('#'+error_msg_area_id).show();
}//end of function


/**
 * Function used to set up an ALERT MESSAGE similar to the PHP Alert messages generated by the system.
 *
 * @param msg_to_show The message to be displayed in the specified ERROR AREA.
 * @param msg_area_id <i>OPTIONAL</i> The ID of the HTML <code>DIV</code>, or other element which is dedicated to hold the error message.
 *                       If not specified, will be defaulted to: <code>error_msg_area</code>
 */
function show_msg(msg_to_show, msg_area_id, alert_type){
    //check if the msg_area_id was set, and if not, default it to: error_msg_area
    if(typeof msg_area_id == 'undefined' || msg_area_id == null) msg_area_id = "error_msg_area";

    var formatted_msg = '';
    //check if the message was already formatted with the corresponding ALERT-styles or not
    if(msg_to_show.indexOf('<div class=') == -1){
        var alert_class = 'alert-'+alert_type;
        formatted_msg = '<div class="alert '+ alert_class +' popup_box"><a class="close" data-dismiss="alert">×</a>' + msg_to_show + '</div>';

    }else formatted_msg = msg_to_show;

    if ($('#'+msg_area_id).html() !== null && $('#'+msg_area_id).html().length > 0) {
        $('#'+msg_area_id).append(formatted_msg);
    }else {
        $('#'+msg_area_id).html(formatted_msg);
    }

    // show the Msg
    if ($('#'+msg_area_id).html().length > 0 && $('#'+msg_area_id).css('display') == 'none') $('#'+msg_area_id).show();
}//end of function


/**
 * Function used to hide ERROR MESSAGES, which were generated in a view.
 *
 * @param alerts_area_id <i>OPTIONAL</i> The ID of the HTML <code>DIV</code>, or other element which is dedicated to hold the error message(s).
 *                          If specified, the HTML content of the specified ERROR_MESSAGES AREA will be set to EMPTY_STRING.
 *                          Otherwise, the displayed ERORR MESSAGE(S) will be just hidden.
 */
function remove_alerts(alerts_area_id){

    if(typeof alerts_area_id != 'undefined' || alerts_area_id != null){
        //hide the generated error messages
        $('.alert').hide('slow');
        //empty the contents of the Alert-Area
        $('#'+alerts_area_id).html('');
    //just hide the alert messages
    }else $('.alert').hide('slow');
}//end of function

/**
 * Generate popup window
 *
 * @param  String action open or close OR  object { action, width, height, position }
 * @param width (optional) div with, leave empty for default with
 * @param height (optional) div height, leave empty for default height
 */
function popup(action, width, height, classname){
	classname = (typeof classname !== 'undefined') ?  classname : null;
	/* Set variables, get the values directly or from the object */
    cnf = {};
    if(jQuery.isPlainObject(action)){
		options = action;
		if(options.action == 'open'){
            cnf.action = 'open';
        }
        if(typeof(options.class) != "undefined"){
           cnf.class = options.class;
        }
        if(typeof(options.width) != "undefined"){
           cnf.width = options.width;
        }
        if(typeof(options.height) != "undefined"){
            cnf.height = options.height;
        }
        if(typeof(options.position) != "undefined" && options.position == 'fixed'){
            cnf.position = 'fixed';
        }
        else{
            cnf.position = 'absolute';
        }
    }
    else{
		cnf.action = action;
        cnf.width = width;
        cnf.height = height;
        cnf.position = 'absolute';
        cnf.class = classname;
    }
    if(cnf.action == 'open'){
        $('#add_new_js').off();
        $('#add_new_js').remove(); //Prevent duplicate calls
        var html = '' +
            '<div id="add_new_js">' +
                '<div id="new_background_js"></div>' +
                '<div id="new_js"></div>' +
            '</div>';
        $('body').append(html);

		var $new_js = $('#add_new_js').find('#new_js');

        //Set width
        if(parseInt(cnf.width) > 0 ){
            $new_js.width(parseInt(cnf.width));
            $new_js.css({'width': parseInt(cnf.width), 'margin-left': function(){
                var ml = parseInt(cnf.width) / 2;
                return parseInt(-ml);
            }});
        }

        //Set height
        if(parseInt(cnf.height) > 0 ){
            $new_js.height(parseInt(cnf.height));
            $new_js.css({ 'height': parseInt(cnf.height)});
        }

        //Set position
        $new_js.css({ 'position': cnf.position });

		//Set class
        $new_js.addClass(cnf.class);

        $('#add_new_js').fadeIn();

        //Attach the event in a namespace for easy unattach
        $(document).on('keyup.namespace_popup', function(e){
            // If press scape, close the window
            if (e.keyCode == 27) { popup('close'); }
        });

    }
    else{
        $('#add_new_js').fadeOut(function(){
            //Un-attach the event attached to this namespace
            $(document).off('.namespace_popup');
            $('#add_new_js').remove();
        });
    }
}


function update_page_view(view_url, selected_item_id){

    var reload_view = '';

    if(view_url.indexOf('?id=') != -1){
        reload_view = view_url.substr(0, (view_url.indexOf('?id=') + 4))+selected_item_id;
    }else{
        reload_view = view_url+'?id='+selected_item_id;
    }

    //Reload the page to the updated view and set the specified Item to selected
    window.location = reload_view;

}//end of function

//
// CORS
//

var CORS = CORS || {};

/**
 * Make CORS request. More info at http://enable-cors.org/
 *
 * @param request Request to be made.
 * @param cb_function Function to be called when request is completed.
 */
CORS.makeCORSRequest = function(request, cb_function) {
    var xhr;

    // Save callback function
    CORS.cb_function = cb_function;

    // Create CORS request
    xhr = CORS.createCORSRequest('GET', request);

    if (!xhr) {
        throw new Error('CORS not supported');
    }

    // Response handlers
    xhr.onload  = function() {
        CORS.cb_function(true, this.responseText);
    };

    xhr.onerror = function() {
        CORS.cb_function(false, this.statusText);
    };

    xhr.send();
};

/**
 * Make CORS batch request. More info at http://enable-cors.org/
 *
 * @param request An array of requests to be made.
 * @param cb_function Function to be called when all requests are completed.
 */
CORS.makeCORSBatchRequest = function(request, cb_function) {
    var i;

    // Save callback function
    CORS.cb_function = cb_function;

    // Number of request to be made
    CORS.requests = request.length;

    // Array to save the response texts
    CORS.responses = Array(request.length);

    // Make CORS request
    for (i = 0; i < request.length; i++) {
        var xhr = CORS.createCORSRequest('GET', request[i]);

        // To know where to store the response text for this request
        xhr.array_id = i;

        // Response handlers
        xhr.onload  = function() {
            CORS.responses[this.array_id] = this.responseText;

            if (--CORS.requests == 0)
                CORS.cb_function(true, CORS.responses);
        };

        xhr.onerror = function() {
            if (--CORS.requests == 0)
                CORS.cb_function(false, this.statusText);
        };

        xhr.send();
    }
};

/**
 * Create CORS request. More info at http://enable-cors.org/
 *
 * @param method Method to be used (i.e. GET).
 * @param url URL of the resource.
 */
CORS.createCORSRequest = function(method, url) {
    var xhr = new XMLHttpRequest();

    if ("withCredentials" in xhr) {

        // Check if the XMLHttpRequest object has a "withCredentials" property.
        // "withCredentials" only exists on XMLHTTPRequest2 objects.
        xhr.open(method, url, true);

    } else if (typeof XDomainRequest != "undefined") {

        // Otherwise, check if XDomainRequest.
        // XDomainRequest only exists in IE, and is IE's way of making CORS requests.
        xhr = new XDomainRequest();
        xhr.open(method, url);

    } else {

        // Otherwise, CORS is not supported by the browser.
        xhr = null;

    }

    return xhr;
};

//
// Image Browser
//

var ImageBrowser = ImageBrowser || {};

/**
 * Launch the image browser.
 *
 * @param cb_function Function to be called when user picks an image.
 */
ImageBrowser.run = function(cb_function) {

    // Save the callback function
    this.cb_function = cb_function;

    // Load the data (and continue with the image browser creation)
    ImageBrowser.loadData();
};

/**
 * Load the data from the server and create the data structure. Then call function createBrowser.
 */
ImageBrowser.loadData = function() {
    var requests = Array();

    requests[0] = '/admin/media/ajax_get_location_list';
    requests[1] = '/admin/media/ajax_get_image_list';
    requests[2] = '/admin/media/ajax_get_docs_list';
    requests[3] = '/admin/media/ajax_check_shared_media';
    CORS.makeCORSBatchRequest(requests,
        function(success, response) {
            var i, j, location_list, items_list, data, location_title, location_data;
            if (success) {
                // - GET the Number of Available Media Locations Creates JS objects from the JSON responses
                location_list = jQuery.parseJSON(response[0]);

                // Creates an associative array to hold the objects. Each location (category) contains a set of
                // objects belonging to that location.
                data = Array();
                location_data = Array();

                for (i = 0; i < location_list.length; i++) {
                    //initialize the Media Folder (Location) to be used for
                    location_title = location_list[i]['location'];
                    location_data = Array();

                    // Get the Items to be LISTED in this Location List
                    if(location_title == 'docs'){
                        // DOCUMENTS are to be listed -=> get the result from Documents Request
                        items_list    = jQuery.parseJSON(response[2]);
                    }else{
                        // IMAGES are to be LISTED -=> get the result from Images Request
                        items_list    = jQuery.parseJSON(response[1]);
                    }

                    // Add items to their correspondent List
                    for (j = 0; j < items_list.length; j++) {
                        if(items_list[j].location == location_title){
                            location_data.push(items_list[j]);
                        }
                    }

                    // Add this Location List to data
                    data[location_title] = location_data;
                }

                // Save data
                ImageBrowser.data = data;

                // Create image browser
                ImageBrowser.createBrowser(response[3]);
            } else {
                alert('There was a problem. Please, try again later. Server said: ' + response);
            }
        }
    );
};

/**
 * Create the image browser.
 */
ImageBrowser.createBrowser = function(response) {
    var i, html;
    // Show a 800x600 window
    ImageBrowser.showWindow(800, 600);

    // Create a drop-down list
    html = '' +
        '<input type="text" id="gallery-search-box"><a id="gallery-search-button">Search</a><label class="ib_label" for="ib_select_folder">Location:</label>' +
        '<select id="ib_select_folder">';

    for (key in ImageBrowser.data) {
        var selected = '';
        if (key == 'content') {
            selected = ' selected="selected"';
        }
        html += '<option value="' + key + '"' + selected + '>' + key + '</option>';
    }

    html += '</select>';

    $('#ib_wnd_top').append(html);

    $('#ib_select_folder').change(
        function() {
            ImageBrowser.showFolder(this.value,response);
        }
    );
    
    $('#gallery-search-button').click(
        function() {
			var img_name=$('#gallery-search-box').val();
			var select_folder;
		    select_folder = $('#ib_select_folder').val();
            ImageBrowser.searchImage(img_name, select_folder);
        }
    );
    
    if ($('#layout_id').find(':selected').html() == 'campaign')
    {
        $('#ib_select_folder').val('campaign').trigger('change');
    }

    // Set the focus on the drop-down list
    $('#ib_select_folder').focus();
    // Show the current selected folder
    ImageBrowser.showFolder($('#ib_select_folder').val(),response);
};

$('#layout_id').on('change', function()
{
    if ($(this).find(':selected').html() == 'campaign')
    {
        $('#ib_select_folder').val('campaign');
    }
});

ImageBrowser.searchImage = function(image_name,folder) {
	
	div_folder_id = 'ib_folder' + folder;
    $('#'+div_folder_id+' .ib_tn .ib_tn_div').each(function(){
		var alt=$(this).children('img').attr('alt');
		var name = image_name.toLowerCase().toString();
		alt = alt.toLowerCase().toString();
		if(alt.match(name)){
			$(this).parent('.ib_tn').removeClass('hide-img-wrapper');
		}else {
			$(this).parent('.ib_tn').addClass('hide-img-wrapper');
		}
    });
};

ImageBrowser.showFolder = function(folder,response) {
	$("#gallery-search-box").val('');
    var i, div_folder_id, folder_items, html, filename, dimensions, item_src, thumb_src, item_html;
    // Hide the actual folder
    if (ImageBrowser.actual_folder)
        $('#' + ImageBrowser.actual_folder).addClass('ib_div_folder_hidden');

    // Generate folder id
    div_folder_id = 'ib_folder' + folder;
    $('#'+div_folder_id+' .ib_tn ').removeClass('hide-img-wrapper');
    if (document.getElementById(div_folder_id) == null) {
	
        html = '<div id="' + div_folder_id + '" class="ib_div_folder_hidden">';

        // Get all the folder_items of the selected folder
        folder_items = ImageBrowser.data[folder];

        for (i = 0 ; i < ImageBrowser.data[folder].length; i++) {

            // Attributes
            filename   = folder_items[i]['filename'  ];
            dimensions = folder_items[i]['dimensions'];
            // Source
            item_src = response + '/media/' + ((folder == 'docs')? folder : 'photos/' + folder)  + '/' + filename;
            thumb_src  = response + '/media/' +  ((folder == 'docs')? folder : 'photos/' + folder + '/_thumbs_cms') + '/' + filename;

            // Item HTML
            if(folder == 'docs'){
                item_html = '<div class="ib_tn ib_hide_tn" onClick="ImageBrowser.pickImage(\'' + item_src + '\')">' +
                        '<div class="ib_tn_div">' +
                            '<span class="icon-file">&nbsp;</span>' +
                            '<span>' +filename + '</span>' +
                        '</div>' +
                    '</div>';

            // List Images
            }else{
				
                item_html = '<div class="ib_tn ib_hide_tn" onClick="ImageBrowser.pickImage(\'' + item_src + '\')">' +
                        '<div class="ib_tn_div">' +
                            '<img class="ib_tn_img" src="' + thumb_src + '" alt="' + filename + '"/>' +
                        '</div>' +
                        '<span>' + filename + '<br/>' + dimensions + '</span>' +
                    '</div>';
            }

            // HTML
            html += '' + item_html;

        }

        html += '</div>';
        // Append the HTML
        $('#ib_wnd_container').append(html);
        $(".ib_hide_tn").each(function(){
            $(this).removeClass("ib_hide_tn");
        });
    }

    // Show the actual folder
    $('#' + div_folder_id).removeClass('ib_div_folder_hidden');

    //Show Items in Documents Folder
    if(folder == 'docs') $('#' + div_folder_id).children().removeClass('ib_hide_tn');

    ImageBrowser.actual_folder = div_folder_id;
};

/**
 * Show the image browser window centered.
 */
ImageBrowser.showWindow = function() {
    var html;

    html = '' +
        '<div id="ib_background"></div>' +
        '<div id="ib_window">' +
            '<div id="ib_wnd_top"></div>' +
            '<div id="ib_wnd_container"></div>' +
        '</div>';

    $('body').append(html);

    $('#ib_background').hide();
    $('#ib_window'    ).hide();

    // Center window
    $('#ib_window').css("top" , Math.max(0, (($(window).height() - $('#ib_window').outerHeight()) / 2) + $(window).scrollTop ()) + "px");
    $('#ib_window').css("left", Math.max(0, (($(window).width () - $('#ib_window').outerWidth ()) / 2) + $(window).scrollLeft()) + "px");

    // If the user press ESC, close the window
    $(document).keyup(
        function(e) {
            if (e.keyCode == 27) {
                ImageBrowser.hideWindow();
            }
        }
    );

    $('#ib_background').fadeIn(400);
    $('#ib_window'    ).fadeIn(400);
};

/**
 * Hide the image browser window.
 */
ImageBrowser.hideWindow = function() {
    $('#ib_window'    ).fadeOut(200, function() { $('#ib_window'    ).remove(); });
    $('#ib_background').fadeOut(200, function() { $('#ib_background').remove(); });
};


ImageBrowser.pickImage = function(url) {
    // Hide the window
    ImageBrowser.hideWindow();

    // Call callback function with the image URL
    ImageBrowser.cb_function(url);
};



/*
 * Links Browser
 */
//Initialize LinksBrowser
var LinksBrowser = LinksBrowser || {};

/**
 * Launch the links browser.
 *
 * @param cb_function Function to be called when user picks an link (Document or Internal Page).
 */
LinksBrowser.run = function(cb_function) {

    // Save the callback function
    this.cb_function = cb_function;

    // Load the data (and continue with the link browser creation)
    LinksBrowser.loadData();
};

/**
 * Load the data from the server and create the data structure. Then call function createBrowser.
 */
LinksBrowser.loadData = function() {
    var requests = Array();
    var browser_locations = Array();

    // Links Browser Locations
    browser_locations[0] = {'location' : 'WebsiteCMS Pages'};
    browser_locations[1] = {'location' : 'Documents'};

    requests[0] = '/admin/pageshelper/ajax_get_pages_list';
    requests[1] = '/admin/media/ajax_get_docs_list';
    requests[2] = '/admin/media/ajax_check_shared_media';

    CORS.makeCORSBatchRequest(requests,
        function(success, response) {
            var i, j, items_list, data, location_title, location_data;
            if (success) {
                // Creates an associative array to hold the objects. Each location (category) contains a set of
                // objects belonging to that location.
                data = Array();
                location_data = Array();

                for (i = 0; i < browser_locations.length; i++) {
                    //initialize the Links Browser Folder (Location) to be used for
                    location_title = browser_locations[i]['location'];
                    location_data = Array();

                    // Get the Items to be LISTED in this Location List
                    switch(location_title){
                        case 'WebsiteCMS Pages':
                            // WebsiteCMS Pages are to be LISTED -=> get the result from Images Request
                            items_list    = jQuery.parseJSON(response[0]);
                            break;

                        case 'Documents':
                            // DOCUMENTS are to be listed -=> get the result from Documents Request
                            items_list    = jQuery.parseJSON(response[1]);
                            break;
                        // Default items_list to Empty Array
                        default:
                            items_list = Array();
                            break;
                    }

                    // Add items to their correspondent List
                    for (j = 0; j < items_list.length; j++) {
                        location_data.push(items_list[j]);
                    }

                    // Add this Location List to data
                    data[location_title.replace(' ', '_').toLowerCase()] = location_data;
                }

                // Save data
                LinksBrowser.data = data;

                // Create link browser
                if(response[2] == 'undefined')
                {
                    response[2] == '';
                }
                LinksBrowser.createBrowser(response[2]);
            } else {
                alert('There was a problem. Please, try again later. Server said: ' + response);
            }
        }
    );
};

/**
 * Create the links browser.
 */
LinksBrowser.createBrowser = function(response) {
    var i, html;

    // Show a 800x600 window
    LinksBrowser.showWindow(800, 600);

    // Create a drop-down list
    html = '' +
        '<label class="ib_label" for="ib_select_folder">Location:</label>' +
        '<select id="ib_select_folder">';

    for (key in LinksBrowser.data) {
        html += '<option value="' + key.replace(' ', '_').toLowerCase() + '">' + key + '</option>';
    }

    html += '</select>';

    $('#ib_wnd_top').append(html);

    $('#ib_select_folder').change(
        function() {
            LinksBrowser.showFolder(this.value,response);
        }
    );
  
    

    // Set the focus on the drop-down list
    $('#ib_select_folder').focus();
    if(typeof response === 'undefined'){
        response = "";
    }
    // Show the current selected folder
    LinksBrowser.showFolder($('#ib_select_folder').val(),response);
};

LinksBrowser.showFolder = function(folder,response) {

    var i, div_folder_id, folder_items, html, filename, dimensions, item_src, thumb_src, item_html;

    // Hide the actual folder
    if (LinksBrowser.actual_folder)
        $('#' + LinksBrowser.actual_folder).addClass('ib_div_folder_hidden');

    // Generate folder id - should be in the FORM: an_example_of_field_id
    div_folder_id = 'ib_folder' + folder.replace(' ', '_').toLowerCase();

    if (document.getElementById(div_folder_id) == null) {
        html = '<div id="' + div_folder_id + '" class="ib_div_folder_hidden">';

        // Get all the folder_items of the selected folder
        folder_items = LinksBrowser.data[folder];

        for (i = 0 ; i < LinksBrowser.data[folder].length; i++) {

            // Set Item Links etc.
            if(folder == 'documents'){
                // Attributes
                filename   = folder_items[i]['filename'  ];
                dimensions = folder_items[i]['dimensions'];

                // Source
                item_src = response + '/media/docs/' + filename;
                thumb_src  = item_src;

            }else{
                // Attributes for Pages
                filename   = folder_items[i]['name_tag'];
                dimensions = '';

                // Source
                item_src  = '/' + filename;
                thumb_src = item_src;
            }

            // Item HTML
            if(folder == 'documents'){
                item_html = '<div class="ib_list ib_hide_tn" onClick="LinksBrowser.pickItemToLink(\'' + item_src + '\')">' +
                        '<div class="ib_list_div">' +
                            '<span class="icon-file">&nbsp;</span>' +
                            (i + 1) +
                            '. <span>' +filename + '</span>' +
                        '</div>' +
                    '</div>';

            // List Pages links
            }else{
                item_html = '<div class="ib_list ib_hide_tn" onClick="LinksBrowser.pickItemToLink(\'' + item_src + '\')">' +
                        '<div class="ib_list_div">' +
                            (i + 1) +
                            '. <span>' +filename + '</span>' +
                        '</div>' +
                    '</div>';
            }

            // HTML
            html += '' + item_html;

        }

        html += '</div>';

        // Append the HTML
        $('#ib_wnd_container').append(html);
    }

    // Show the actual folder
    $('#' + div_folder_id).removeClass('ib_div_folder_hidden');

    //Show Items in Current Folder
    $('#' + div_folder_id).children().removeClass('ib_hide_tn');

    LinksBrowser.actual_folder = div_folder_id;
};

/**
 * Show the link browser window centered.
 */
LinksBrowser.showWindow = function() {
    var html;

    html = '' +
        '<div id="ib_background"></div>' +
        '<div id="ib_window">' +
            '<div id="ib_wnd_top"></div>' +
            '<div id="ib_wnd_container"></div>' +
        '</div>';

    $('body').append(html);

    $('#ib_background').hide();
    $('#ib_window'    ).hide();

    // Center window
    $('#ib_window').css("top" , Math.max(0, (($(window).height() - $('#ib_window').outerHeight()) / 2) + $(window).scrollTop ()) + "px");
    $('#ib_window').css("left", Math.max(0, (($(window).width () - $('#ib_window').outerWidth ()) / 2) + $(window).scrollLeft()) + "px");

    // If the user press ESC, close the window
    $(document).keyup(
        function(e) {
            if (e.keyCode == 27) {
                LinksBrowser.hideWindow();
            }
        }
    );

    $('#ib_background').fadeIn(400);
    $('#ib_window'    ).fadeIn(400);
};

/**
 * Hide the link browser window.
 */
LinksBrowser.hideWindow = function() {
    $('#ib_window'    ).fadeOut(200, function() { $('#ib_window'    ).remove(); });
    $('#ib_background').fadeOut(200, function() { $('#ib_background').remove(); });
};


LinksBrowser.pickItemToLink = function(url) {
    // Hide the window
    LinksBrowser.hideWindow();

    // Call callback function with the link URL
    LinksBrowser.cb_function(url);
};


// Hide the pop-up bubbles from the jQuery Validation
function removeBubbles() {
    $('.formError').each(function(i,e){document.body.removeChild(e);});
}

if (typeof validationEngine == 'function') {
    $('.validate-on-submit').on('submit', function() {
        if (!$(this).validationEngine('validate')) {
            return false;
        }
    });
}

$.widget( "custom.catcomplete", $.ui.autocomplete,
{
    _renderMenu: function( ul, items )
    {
        ul.addClass('ideabubble_site_search');
        var that = this,
            currentCategory = "";
        $.each( items, function( index, item )
        {
            if ( typeof item.category != 'undefined')
            {
                if ( item.category != currentCategory )
                {
                    ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                    currentCategory = item.category;
                }
                if(item.label == undefined){
                    item.label = 'NULL';
                }
                that._renderItem( ul, item );
            }
        });
    },
    _renderItem:function(ul, item){
        var search_value = $('#search2:visible, #search2-mobile:visible').val().toLowerCase();
        return $( "<li></li>" )
            .data( "item.autocomplete", item )
            //.hover(function(){$('#search2').val($(this).children().html())})
            .append("<a href='"+item.link+"'>" + item.label.toLowerCase().replace(search_value, '<strong>'+search_value
                +'</strong>') + "</a>" )
            .appendTo( ul );
    }
});

$(function()
{
    var xhr = null;
    var searchValue = $('#search2:visible, #search2-mobile:visible').val();
    var url =($('#custom_search').val() == 1) ? '/admin/search/ajax_getcontacts/'+searchValue : '/admin/searchbar/ajax_getresults/'+searchValue;
    $(document).ready(function(){
        searchValue = '';
    });
    $("#search2, #search2-mobile").catcomplete({
        source: function (request, response) {
            if (!xhr) {
                xhr = $.ajax({
                    url: url,
                    timeout: 20000,
                    data: request,
                    dataType: "json",
                    delay:200,
                    success: function (data) {
                        xhr = null;
                        response(data);
                    },
                    error: function () {
                        $(this).removeClass("searchboxwait");
                        $('#resultscount').css({'display':'block'});
                        response([]);
                    }
                });
            }
        },
        minLength: 0,
        search: function(event, ui) {
            $(this).addClass("searchboxwait");
            $('#resultscount').hide();

        },
        open: function(event, ui) {
            $(this).removeClass("searchboxwait");
            $('#resultscount').show();
        },
        response: function(event, ui){
            if(ui.content.length == 0){
                $('#resultscount').html('No Results.');
                $(this).removeClass("searchboxwait");
            }
            else
            {
                if(ui.content[0]['label'] == 'No Results.'){
                    $('#resultscount').html('No Results.');
                    $(this).removeClass("searchboxwait");
                }
                else{
                    $('#resultscount').html(ui.content[ui.content.length-1]['value']+' results');
                }
            }
        }
    });
});

function ib_script_load(src, allow_duplicate)
{
    window.ib_script_loaded_list = window.ib_script_loaded_list || [];
    var scripts = document.getElementsByTagName("script");
    if(!allow_duplicate){
        for(var i in scripts){
            if(scripts[i].src == src){
                return;
            }
        }
        if(window.ib_script_loaded_list.indexOf(src) != -1){
            return;
        }
    }
    window.ib_script_loaded_list.push(src);
    var elem = document.createElement("script");
    elem.src = src;
    document.body.appendChild(elem);
}

function copy_options_html(sel)
{
    var opts = '';
    for(var i = 0 ; i < sel.options.length ; ++i){
        opts += '<option value="' + sel.options[i].value + '"' + (sel.selectedIndex == i ? 'selected="selected"' : '') + '>' + sel.options[i].text + '</option>';
    }
    return opts;
}

window.luhnTest = function(input){
    var value = input.val();
    // accept only digits, dashes or spaces
    if (/[^0-9-\s]+/.test(value)) {
        return "Invalid Credit/Debit Card Number";
    }

    // The Luhn Algorithm. It's so pretty.
    var nCheck = 0, nDigit = 0, bEven = false;
    value = value.replace(/\D/g, "");

    for (var n = value.length - 1; n >= 0; n--) {
        var cDigit = value.charAt(n),
            nDigit = parseInt(cDigit, 10);

        if (bEven) {
            if ((nDigit *= 2) > 9) nDigit -= 9;
        }

        nCheck += nDigit;
        bEven = !bEven;
    }

    return (nCheck % 10) == 0 ? undefined : "Invalid Credit/Debit Card Number";
};

function setupUserActivityTrackHandler()
{
    if (window.ibcms && window.ibcms.user && window.ibcms.user.auto_logout_minutes){
        var auto_logout_minutes = parseInt(window.ibcms.user.auto_logout_minutes);
        if (auto_logout_minutes > 0) {
            console.log("will logout after " + auto_logout_minutes + " minutes of inactivity");
            var lastActivity = new Date().getTime();
            var checkInterval = null;
            function userActivityTrackHandler()
            {
                var dt = new Date();
                lastActivity = dt.getTime();
            }

            function checkActivity()
            {
                var dt = new Date();
                var diff = (dt.getTime() - lastActivity) / 1000;
                if (window.location.href.indexOf(".test") > 0 || window.location.href.indexOf(".dev") > 0) {
                    console.log(diff + " seconds inactive");
                }
                diff = diff / 60;
                if (diff >= auto_logout_minutes) {
                    clearInterval(checkInterval);
                    window.location.href = '/admin/login/logout?auto=yes';
                }
            }

            $(document).on("keypress", userActivityTrackHandler);
            $(document).on("mousemove", userActivityTrackHandler);
            checkInterval = setInterval(checkActivity, 1000);
        }
    }
}

$(document).ready(setupUserActivityTrackHandler);

function acquireActivityLock(plugin, activity, callback)
{
    $.post(
        '/admin/profile/acquire_activity_lock',
        {
            plugin: plugin,
            activity: activity
        },
        function (response) {
            if (callback) {
                callback(response);
            } else {
                if (!response.locked) {
                    if (response.locked_by) {
                        alert(response.locked_by + " is already working on it since: " + response.locked);
                    } else {
                        alert("Lock failed");
                    }
                }
            }
        }
    )
}

function releaseActivityLock(plugin, activity, callback)
{
    $.post(
        '/admin/profile/release_activity_lock',
        {
            plugin: plugin,
            activity: activity
        },
        function (response) {
            if (callback) {
                callback(response);
            } else {
            }
        }
    )
}

$('.tweet-item-btn').on('click', function(ev)
{
	ev.preventDefault();
	var width  = 650;
	var height = 300;
	var left   = screen.width/2-325;
	var url    = this.href;

	window.open(url, 'newwindow', 'width='+width+', height='+height+', left='+left);
	window.focus();
});


function display_note_editor(type, reference_id, id, $container, table)
{
    if (!$container) {
        $container = $(document);
    }
    var $editor = $container.find("#edit_note_modal");
    $editor.find("[name=note_id]").val(id);
    $editor.find("[name=reference_id]").val(reference_id);
    $editor.find("[name=type]").val(type);
    $editor.find("[name=note]").val("");

    $editor.modal("show");

    $editor.find(".btn.save").off("click");
    $editor.find(".btn.save").on("click", function(){
        $.post(
            "/admin/notes2/save",
            {
                reference_id: $editor.find("[name=reference_id]").val(),
                type: $editor.find("[name=type]").val(),
                note: $editor.find("[name=note]").val()
            },
            function (response) {
                $editor.modal("hide");
                fill_notes_list(type, reference_id, table);
            }
        );
    });
}

function fill_notes_list(type, reference_id, table)
{
    $.get(
        "/admin/notes2/search",
        {
            type: type,
            reference_id: reference_id
        },
        function (response) {
            var tbody = "";
            for (var i = 0 ; i < response.length ; ++i) {
                var row = response[i];
                tbody +=
                    '<tr>' +
                        '<td>' + row['id'] + '</td>' +
                        '<td>' + row['reference_id'] + '</td>' +
                        '<td>' + row['note'] + '</td>' +
                        '<td>' + row['creator'] + '</td>' +
                        '<td>' + row['created'] + '</td>' +
                    '</tr>';
            }
            $(table).find("tbody").html(tbody);
        }
    )
}

function rebuild_multiselect(selector, new_items)
{
    selector.html('');
    $.each(new_items, function (id, name) {
        selector.append($('<option>', {
            value: id,
            text : name,
        }));
    });
    selector.multiselect('rebuild');
}
// Ensure the sidebar stays fixed as the user scrolls, resizes the window or toggles the sidebar state
$(document).ready(function()
{
	// Currently only works in certain theme, where the section is visible.
	// We might add a setting to selectively switch it on/off on any theme
	if ( ! $('#sidebar-footer').is(':visible'))
	{
		return false;
	}

	var page_container = document.getElementById('page-container');
	var $header        = $('.navigation-menu');
	var $window        = $(window);
	var $sidebar       = $('#sidebar-menu-wrapper');
	var $main_content  = $('.main-content-wrapper');
	var $footer        = $('.footer-wrapper');

	var content_start, header_height, dist, sidebar_height;

	$window.on('scroll resize :ib-sidebar-toggle', function()
	{
		content_start = page_container.getBoundingClientRect().top;
		header_height = $header.height;

		// If the user has scrolled past the beginning of the content area, position the sidebar at the top of the screen
		// If the user has not scrolled further than the header, position the sidebar at the bottom of the header
		dist = (content_start <= 0) ? 0 : content_start;
		dist = (dist <= header_height) ? header_height : dist;

		// Sidebar height is viewport height, minus gap above the sidebar
		sidebar_height = $(window).height() - dist;

		$('body').addClass('sidebar-fixed');

		$('#sidebar-menu-wrapper').css('position', 'fixed').css('top', dist).css('height', sidebar_height);

		// Move the content over to make room for the sidebar
		$main_content.css('margin-left', $sidebar.width());
		$footer.css('margin-left', $sidebar.width());
	}).trigger('resize');
});

/*
 * Add multiselects to applicable columns in a datatable
 */
$.fn.add_column_multiselects = function()
{
    var table_settings   = $(this).dataTable().fnSettings();
    var $columns         = $(this).find('thead:first tr:first th');
    var $template        = $('#datatable-multiselect-template').clone();

    $template.removeAttr('id').removeClass('hidden');

    var heading, $dropdown, options, options_src, placeholder_text;
    // Loop through each column to add the individual multiselects
    for (var i = 0; i < $columns.length; i++)
    {
        // This only has an effect on columns that specify dropdown options in a data attribute
        options     = $($columns[i]).data('options') || [];
        options_src = $($columns[i]).data('options_src') || '';

        if (options.length || options_src.length)
        {
            // Clone the dropdown template. Make changes to the clone and add that to the header
            $dropdown = $template.clone();
            heading   = $($columns[i]).text();

            $dropdown.find('.datatable-multiselect-label').text(heading);

            // Add options to the cloned multiselect
            datatable_multiselect_add_options($dropdown, options, i);

            table_settings.aoColumns[i].bSortable = false;

            // If there are more than 10 items or this uses a serverside query, show the searchbar
            if (options.length >= 10 || options_src) {
                $dropdown.find('.datatable-multiselect-search-wrapper').removeClass('hidden');
                placeholder_text = options_src ? 'Search records' : 'Search '+options.length+' records';
                $dropdown.find('.datatable-multiselect-search').attr('placeholder', placeholder_text);
            }

            $dropdown.data('column_number', i).attr('data-column_number', i);

            $($columns[i]).html($dropdown);
        }
    }
};

// Populate the dropdown in a datatable multiselect column filter
function datatable_multiselect_add_options(dropdown, options, column_number)
{
    var $dropdown     = $(dropdown);
    var options_html  = '';

    // Column number needs to be specified, if the dropdown has not yet been added to the DOM.
    // If the dropdown is in the DOM, its column number can be determined
    var $th = $dropdown.parents('th');
    column_number = column_number || $th.parent().children().index($th);

    // Find the options that are currently checked. They are to remain in the DOM (i.e. won't be removed if the list is being repopulated)
    // (This should only make a difference for AJAX lists.)
    var currently_checked = [];
    $dropdown.find('.datatable-multiselect-checkbox:checked').each(function() {
        $(this).attr('checked', 'checked'); // Set the "checked" attribute (rather than the property), so it is maintained, when the HTML is stringified
        options_html += '<li>'+$(this).parents('li').html()+'</li>';
        currently_checked.push($(this).val());
    });

    // Add each option
    var table_id      = $dropdown.parents('.dataTable').attr('id');
    var $template     = $dropdown.find('.datatable-multiselect-li');
    var $option, option_text, option_value;
    for (var i = 0; i < options.length; i++)
    {
        option_text  = options[i].title ? options[i].title : options[i];
        option_value = options[i].id ? options[i].id : option_text;

        // Add the option, if it is not already among the existing ones
        if (currently_checked.indexOf(option_value) == -1) {
            $option = $template.clone();
            $option.find('.datatable-multiselect-li-label').text(option_text);
            $option.find('[type="checkbox"]')
                .attr('name', table_id+'_filter_'+column_number+'[]')
                .val(option_value);

            // If there are more than 10 results, hide the results
            options_html += '<li'+(options.length > 10 ? ' class="hidden"' : '')+'>' + $option.html() + '</li>';
        }
    }

    $dropdown.find('.datatable-multiselect-options').html(options_html);
}

// Stop dropdown from dismissing when clicking inside the dropdown
$(document).on('click', '.dropdown[data-autodismiss="false"] .dropdown-menu', function() {
    $(this).parents('.dropdown').addClass('open').find('[data-toggle]').attr('aria-expanded', 'true');
});

$(document).on('change keyup', '.datatable-multiselect-search', function(ev) {
    var search = this;
    if (this.onchange_timer) {
        clearTimeout(this.onchange_timer);
    }
    // this timer is to prevent making search request on each key press. just make one request after key presses stop
    this.onchange_timer = setTimeout(
        function(){




            var $dropdown = $(search).parents('.dropdown-menu');
            var term      = search.value.toLowerCase();
            var ajax_url  = $(search).parents('th').data('options_src') || false;
            var is_a_match;
            var unchecked_matches = 0;
            var number_of_matches = 0;

            if (ajax_url) {
                // Get results from the server, add them to the DOM
                $.ajax({
                    url: ajax_url,
                    data: {term: term}
                }).done(
                    function(results) {
                        results = JSON.parse(results);
                        datatable_multiselect_add_options($dropdown, results);
                    });

            }
            else {
                // Loop through options that are in the DOM. Toggle their visibility, based on the searched term
                $dropdown.find('li').each(function() {
                    is_a_match = $(this).find('.datatable-multiselect-li-label').text().toLowerCase().indexOf(term) > -1;

                    number_of_matches += is_a_match ? 1 : 0;

                    // If the result is already checked, show it
                    if ($(this).find(':checked').length) {

                    }
                    // If the result matches the searched term, show it. But only allow up to ten to be shown
                    else if (is_a_match && unchecked_matches < 10) {
                        $(this).removeClass('hidden');
                        unchecked_matches += 1;
                    }
                    else {
                        $(this).addClass('hidden');
                    }
                });
            }

            /* Text to show the number of results found. */
            var $found_text = $dropdown.find('.datatable-multiselect-found');

            // Only show if something has been typed.
            term ? $found_text.removeClass('hidden') : $found_text.addClass('hidden');

            // Update number in the string. Different text displayed depending whether a plural is to be used or not.
            $found_text.find('.datatable-multiselect-found-number').html(number_of_matches);
            if (number_of_matches == 1) {
                $found_text.find('.pural_text'   ).addClass('hidden');
                $found_text.find('.singular_text').removeClass('hidden');
            } else {
                $found_text.find('.singular_text').addClass('hidden');
                $found_text.find('.plural_text'  ).removeClass('hidden');
            }
        },
        250
    )
});

$(document).on('click', '.datatable-multiselect-select_all', function() {
    var $checkboxes = $(this).parents('.dropdown-menu').find('[type="checkbox"]');
    $checkboxes.prop('checked', true);
    $($checkboxes[0]).trigger('change'); // trigger change only once
});

$(document).on('click', '.datatable-multiselect-clear_all', function() {
    var $checkboxes = $(this).parents('.dropdown-menu').find('[type="checkbox"]');
    $checkboxes.prop('checked', false);
    $($checkboxes[0]).trigger('change'); // trigger change only once
});

// When a checkbox in a dropdown is changed, update the datatable filters and refresh the table
$(document).on('change', '.datatable-multiselect-checkbox', function() {
    var column_number  = $(this).parents('.datatable-multiselect').data('column_number');
    var checkbox_group = $(this).attr('name');
    var datatable      = $(this).parents('.dataTable').dataTable();
    var values         = '';

    $('[name="'+checkbox_group+'"]:checked').each(function() {
        values += '|' + this.value;
    });

    values = values.replace(new RegExp('^[|]+'), '');

    datatable.fnFilter(values, column_number);
});




/* Dropdown submenus */
$(document).on('click', '.dropdown-submenu-toggle > button', function(ev) {
    var $submenu = $(this).next('ul');

    // Remove temporary class
    $submenu.removeClass('dropdown-menu--move_left');

    $submenu.toggle();

    if ($submenu[0]) {
        var coordinates = $submenu[0].getBoundingClientRect();
        // If the submenu goes offscreen, move it to the other side
        if (coordinates.right  > document.body.offsetWidth) {
            $submenu.addClass('dropdown-menu--move_left');
        }
    }

    ev.stopPropagation();
    ev.preventDefault();
});

$(window).on('resize', function() {
    $('.dropdown-submenu-toggle .dropdown-menu:visible').each(function()
    {
        $(this).removeClass('dropdown-menu--move_left');
        var coordinates = $(this)[0].getBoundingClientRect();

        // If the submenu goes offscreen, move it to the other side
        if (coordinates.right > document.body.offsetWidth) {
            $(this).addClass('dropdown-menu--move_left');
        }
    });
});




/*------------------------------------*\
 #Search filters
\*------------------------------------*/
$(document).on('change', '.search-filter-dropdown .search-filter-checkbox', function()
{
    var $dropdown = $(this).parents('.search-filter-dropdown');
    var $filters  = $(this).parents('search-filters');
    var $clear    = $filters.find('.search-filters-clear');
    var $amount   = $dropdown.find('.search-filter-amount');
    var amount    = $dropdown.find('.search-filter-checkbox:checked').length;

    if (amount == 0) {
        $dropdown.removeClass('filter-active');
        $amount.html('');
    } else {
        $amount.html(amount);
        $dropdown.addClass('filter-active');
    }

    // Show the "clear filters" option, if at least one filter has been selected
    if ($filters.find('.search-filter-checkbox:checked').length) {
        $clear.addClass('visible');
    } else {
        $clear.removeClass('visible');
    }
});

// Clear all filters in a given group
$(document).on('click', '.search-filter-select_none', function()
{
    var $dropdown  = $(this).parents('.search-filter-dropdown');

    $dropdown.find('.search-filter-checkbox').prop('checked', false);
    $dropdown.find('.search-filter-checkbox:first').trigger('change');
});

// Select all filters in a given group
$(document).on('click', '.search-filter-select_all', function()
{
    var $dropdown  = $(this).parents('.search-filter-dropdown');

    $dropdown.find('.search-filter-checkbox:visible').prop('checked', true);
    $dropdown.find('.search-filter-checkbox:visible:first').trigger('change');
});

// Clear *all* filters
$(document).on('click', '.search-filters-clear', function()
{
    var $filters  = $(this).parents('.search-filters');

    $filters.find('.search-filter-checkbox:visible').prop('checked', false);
    $filters.find('.search-filter-dropdown:visible').removeClass('filter-active');
    $filters.find('.search-filter-amount').html('');
    $(this).removeClass('visible');

    $filters.find('.search-filter-checkbox:first').trigger('change');
});


// Reset all form fields in a given section to their original values
// Similar to the JavaScript .reset(), but works on elements other than just "form"
$.fn.form_reset = function()
{
    $(this).find(':input').each(function()
    {
        var selected;
        var is_multiselect = Boolean($(this).attr('multiselect')) || Boolean($(this).attr('multiple'));

        // Reset most forms of input to the value defined in the DOM
        this.value = $(this).attr('value') || '';

        // Reset checkboxes and radio buttons
        $(this).prop('checked', $(this).attr('checked'));

        // Reset select lists
        $(this).find('option').each(function() {
            selected = false;

            // If there is a single select, the first option is selected by default
            if (!is_multiselect && $(this).is(':first-child')) {
                this.selected = true;
            }

            // If any of the options are have the selected attribute, select them
            if (this.defaultSelected) {
                this.selected = selected = true;
            }
        });
    });
};

/**
 * Get the text to describe a date range
 * Examples:
 ** '2020':                  1 January 2020 to 31 December 2020
 ** 'Mar/2020':              1 March 2020 to 31 March 2020
 ** '16/Mar/2020':           16 March 2020 to 16 March 2020
 ** '18/Mar – 30 June 2020': 18 March 2020 to 30 June 2020
 *
 * @param start_date
 * @param end_date
 *
 * @returns {string}
 */
function get_date_range_text(start_date, end_date)
{
    if (!start_date && !end_date) {
        return '';
    }

    start_date = new moment(start_date);
    end_date = new moment(end_date);

    const same_year      = (start_date.format('YYYY') === end_date.format('YYYY'));
    const same_month     = (same_year && start_date.format('MM') === end_date.format('MM'));
    const same_day       = start_date.format('YYYY-MM-DD') === end_date.format('YYYY-MM-DD');
    const is_year_range  = (same_year && start_date.format('DD-MM') === '01-01' && end_date.format('DD-MM') === '31-12');
    const is_month_range = (same_month && start_date.format('DD') === '01' && end_date.format('DD') === end_date.endOf('month').format('DD'));
    let range_text = '';

    if (is_year_range) {
        range_text = start_date.format('YYYY');
    }
    else if (is_month_range) {
        range_text = start_date.format('MMM/YYYY');
    }
    else if (same_day) {
        range_text = start_date.format('D/MMM/YYYY');
    }
    else if (same_year) {
        range_text = start_date.format('D/MMM') + ' – ' + end_date.format('D/MMM/YYYY');
    }
    else {
        range_text = start_date.format('D/MMM/YYYY') + ' – ' + end_date.format('D/MMM/YYYY');
    }

    return range_text;
}

$(document).on('change', '.publish-toggle', function() {
    var input   = this;
    var id      = $(this).data('id');
    var publish = this.checked ? 1 : 0;
    var url     = $(this).data('url');
    var message = '';

    if (url) {
        $.ajax({url: url, data: {id: id, publish: publish}}).done(function(data)
        {
            if (data.success) {
                message = '<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> '+data.message+'</div>';
                $(input).parent().find('.publish-toggle-sort').html(publish);
            } else {
                message = '<div class="alert alert-danger popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Error: </strong> ' + data.message + '</div>';

                // Revert to the old status, since the update failed
                input.checked = (!input.checked);
            }

            $('body').append(message);
        });
    }
});