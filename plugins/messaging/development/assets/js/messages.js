$(document).ready(function()
{
	var $messaging_sidebar = $('#messaging-sidebar');

	refresh_message_counters();

    $messaging_sidebar
        .on(':ib-popup-open', function () {
            $('body').addClass('fixed-body');
            // If the popup has been triggered some place besides the button, ensure the navigation menu still shows
            $('.navigation-menu').addClass('navbar-fixed-top');
        })
        .on(':ib-popup-close', function () {
            $('body').removeClass('fixed-body');
            $('.navigation-menu').removeClass('navbar-fixed-top');
        });

	$('#user-tools-messaging-sidebar-expand').on('click', function(ev)
	{
		ev.preventDefault();
		var $sidebar = $('#messaging-sidebar');
		if ($sidebar.hasClass('hidden'))
		{
			$sidebar.removeClass('hidden').show().trigger(':ib-popup-open');
		}
		else
		{
			$sidebar.addClass('hidden').trigger(':ib-popup-close');
		}
	});

	$('.messaging-sidebar-open_list').on('click', function(ev)
	{
		ev.preventDefault();
		$('#add-attachment').hide();
		$('.messaging-sidebar-message').addClass('hidden');

		$('.messaging-sidebar-open_list--active').removeClass('messaging-sidebar-open_list--active');
		$(this).addClass('messaging-sidebar-open_list--active');

		// Reset the pagination
		$('#messaging-sidebar-pagination-number').val(1);

		get_sidebar_messages();
	});

	$(document).keyup(function(ev)
	{
		if (ev.keyCode == 27 && ! $messaging_sidebar.hasClass('hidden')) // "esc" key
		{
			$messaging_sidebar.addClass('hidden').trigger(':ib-popup-close');
		}
	});

	// Search filter
	$(document).on('click',  '#messaging-sidebar-search-btn', get_sidebar_messages);
	$(document).on('change', '#messaging-sidebar-search', function() {
        get_sidebar_messages(function() {
            $('#messaging-sidebar-search').focus();
        });
    });

	// User toggle
	$('.messaging-sidebar-select_user-option').on('change', function()
	{
		// Change the displayed name
		document.getElementById('messaging-sidebar-user-name').innerHTML =
			$(this).parents('li').find('.messaging-sidebar-select_user-name').html();


		if (document.querySelector('.messaging-sidebar-select_user-option:checked').value == 'all') {
			$messaging_sidebar.addClass('messaging-sidebar--global_view');
		}
		else {
			$messaging_sidebar.removeClass('messaging-sidebar--global_view');
		}

		// Reset the pagination
        var pagination_field = document.getElementById('messaging-sidebar-pagination-number');
        if (pagination_field) {
            pagination_field.value = 1;
        }

		// Get the messages
		get_sidebar_messages();
	});

	// Pagination
	$messaging_sidebar.on('click', '#messaging-sidebar-pagination-prev', function()
	{
		document.getElementById('messaging-sidebar-pagination-number').value--;
		get_sidebar_messages();
	});
	$messaging_sidebar.on('click', '#messaging-sidebar-pagination-next', function()
	{
		document.getElementById('messaging-sidebar-pagination-number').value++;
		get_sidebar_messages();
	});

	// Mail type filter
	$messaging_sidebar.on('change', '[name="messaging-sidebar-type_filter"]', function()
	{
		$('[name="messaging-sidebar-type_filter"]:checked').parents('label').addClass('active');

		get_sidebar_messages();
	});
});

function get_sidebar_messages(callback)
{
	var page_number     = document.getElementById('messaging-sidebar-pagination-number');
	var params          = JSON.parse($('.messaging-sidebar-open_list--active').attr('data-params'));

	params.search       = $('#messaging-sidebar-search').val();
	params.message_type = $('[name="messaging-sidebar-type_filter"]:checked').val();

	var args            = {
		user_id     : $('.messaging-sidebar-select_user-option:checked').val(),
		page_number : page_number ? page_number.value : 1
	};

	$.ajax({
		url:    '/admin/messaging/ajax_get_sidemenu_messages',
		method: 'GET',
		data:   { params: params, args: args }
	}).done(function(result)
		{
			$('#messaging-sidebar-messages').html(result).removeClass('hidden');
            var label = $('.messaging-sidebar-open_list--active').data('name').charAt(0).toUpperCase() + $('.messaging-sidebar-open_list--active').data('name').slice(1);
            $('.messaging-sidebar-messages-heading').text(label);
			refresh_message_counters();

			if (typeof callback == 'function') {
				callback();
			}
		});

}

function refresh_message_counters()
{
	if (!document.querySelector('.messaging-sidebar-select_user-option:checked')){
		return;
	}
	var $counters = $('#messaging-sidebar').find('.counts');
	var args      = { user_id : $('.messaging-sidebar-select_user-option:checked').val() };
	var params    = {};
	var $folder;

	$counters.each(function()
	{
		$folder = $(this).parents('a');
		params[$folder.attr('data-name')] = JSON.parse($folder.attr('data-params'));
	});

	$.ajax({
		url      : '/admin/messaging/ajax_get_unread_counters',
		method   : 'GET',
		dataType : 'json',
		data     : { params: params, args: args }
	}).done(function(result)
	{
		if (result.counters)
		{
			var name;
			for (var i = 0; i < $counters.length; i++)
			{
				name = $($counters[i]).parents('a').attr('data-name');
				$counters[i].innerHTML = result.counters[name] ? result.counters[name] : '';
			}
		}
	});
}

$(function() {
	//----- OPEN
	$('[data-popup-open]').on('click', function(e)  {
		var targeted_popup = jQuery(this).attr('data-popup-open');
		$('[data-popup="' + targeted_popup + '"]').fadeIn(350);

		$(this).trigger(':ib-popup-open');

		e.preventDefault();
	});

	//----- CLOSE
	$('[data-popup-close]').on('click', function(e)  {
		var targeted_popup = jQuery(this).attr('data-popup-close');
		$('[data-popup="' + targeted_popup + '"]').fadeOut(350);

		$(this).trigger(':ib-popup-close');

		e.preventDefault();
	});
});
				
$(document).ready(function(){
	var $sidebar = $('#messaging-sidebar');
	var $message_list = $('#messaging-sidebar-messages');

    /* for filter ======*/
	$sidebar.on('click', '.pullBtn', function()
	{
        $(this).toggleClass('open');
        $(this).siblings('.toggle-box').slideToggle();
    });

    /* check single email ======*/
    $message_list.on('change', '.input-field:checkbox', function()
	{
        if ($(this).is(":checked"))
		{
            $(this).parents('.medialist > li').addClass("selected");
        }
		else
		{
            $(this).parents('.medialist > li').removeClass("selected");
        }

		if ($message_list.find('.input-field:checked').length)
		{
			$('.messaging-sidebar-actions').addClass("display");
		}
		else
		{
			$('.messaging-sidebar-actions').removeClass("display");
		}
    });
   

    var height = $(".navigation-menu,.navbar-inner").outerHeight();
    $(".message-wrapper").css("top", height);
	/*
    if($('.message-wrapper').attr('display','block')){
        $("body").addClass("fixed-body");
    } else{
        $("body").removeClass("fixed-body");
    }
    $('.popup-close').click(function () {
        $("body").removeClass("fixed-body");
    });
    */
    
    

    /* check single email ==========*/
	$message_list.on('change', '.checked-all:checkbox', function()
	{
		var checked = $(this).is(":checked");
        $('.input-field').each(function()
		{
			$(this).prop('checked', checked).trigger('change');
		});
    });

    /* read email ==========*/
	$message_list.on('click', '.medialist > li', function(ev)
	{
		// If anywhere on the message other than a link or form field was clicked
		if ( ! $(ev.target).is('a, label, button, :input, .sidebar-no_messages') && $(ev.target).parents('a, label, button, :input, .sidebar-no_messages').length == 0)
		{
			$(this).removeClass('unread');

			$('#messaging-sidebar-attachments-view').hide();
			$('.messaging-sidebar-message').each(function(){$(this).addClass('hidden')});

			$.ajax('/admin/messaging/ajax_get_message/'+this.getAttribute('data-id')).done(function(result) {
				result = JSON.parse(result);
				$('#messaging-sidebar-message--view'   ).html(result.message_html   ).removeClass('hidden');

				if (result.attachment_html) {
					$('#messaging-sidebar-attachments-view').html(result.attachment_html).show();
				}
				else {
					$('#messaging-sidebar-attachments-view').html(result.attachment_html).hide();
				}
				refresh_message_counters();
			});
		}
    });
  
    /* close email ==========*/
	$sidebar.on('click', '.basic_close', function()
	{
		if ($(this).parents('.messaging-sidebar-message').length) {
			$('.mail-wrap').addClass("hidden");
			$('.messaging-sidebar-column.last').find('.content-box').css('display', 'none');
		} else if ($(this).parents('#messaging-sidebar-messages').length) {
            $(this).parents('#messaging-sidebar-messages').addClass("hidden");
            $('.messaging-sidebar-open_list--active').removeClass('messaging-sidebar-open_list--active');
        }
        $(this).parents('.content-box').css('display', 'none');
        $(".detail-btn").removeClass("selected");
        $('.toggle-box').css('display','none');
        return false;
    });

	$('.close-btn').click(function()
	{
		$('#messaging-sidebar-sms').addClass('hidden');
        $(this).parents('.content-box').css('display', 'none');
        $(".detail-btn").removeClass("selected");
        $('.toggle-box').css('display','none');
        return false;
    });

	$sidebar.on('click', '#messaging-sidebar-send-email', function()
	{
		var $form = $(this).parents('form');

	});

    $(document).on("change", "#message_template_select", function(){
        $.post(
            "/admin/messaging/notification_template_details",
            {id: this.value},
            function (response) {
                $("#messaging-sidebar-email-message").val(response.message);
                CKEDITOR.instances["messaging-sidebar-email-message"].setData(response.message);
                $("[name='email[subject]']").val(response.subject);
            }
        )
    });

    /* Open the send email/SMS/alert form */
    $sidebar.on('click', '.detail-btn', function()
    {
        // Clear the attachments and reset the form
        $('#messaging-sidebar-attachments-list').hide().find('tbody').html('');
        var $form = $('.send-message-form').trigger('reset');
        $form.find('.ckeditor, .ckeditor-email').each(function() {
            CKEDITOR.instances[this.id].setData(this.defaultValue);
        });
        $('.messaging-sidebar-message').addClass('hidden');
        $('.messaging-sidebar-column.last').find('.content-box').hide();


        var activeTab = $(this).attr("rel");
        $("#" + activeTab).css('display', '').removeClass('hidden');
        $(".detail-btn").removeClass("selected");
        $(this).addClass("selected");

        $(this).parents('.content-box').show();
    });

    // When the message form is reset, remove any recipients that have been added
    $(document).on('reset', '.send-message-form', function() {
        $('.contact-list-labels').find('[value="new"]').parents('.label').remove();
    });

    /* for tabs========*/  
    $( "#tabs-1" ).tabs();
    $( "#tabs-2" ).tabs();
    $( "#tabs-3" ).tabs();
    

     /*----popup---*/
    $('.screenCell .popup_close').click(function(){
        $(".sectioninner").removeClass("bounceInDown");
        $('.sectionOverlay').css('display','none');
        return false;
    });
 
      /* popup and slider========*/   
    $('.popup-btn').click(function(){
        $(".sectioninner").addClass("bounceInDown");
        $('#popup').css('display','block');
        $('#attachment-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            fade: false,
            asNavFor: '#attachment-slider-nav',
            autoplay: true,
            autoplaySpeed: 5000
        });
        $('#attachment-slider-nav').slick({
            slidesToShow: 7,
            slidesToScroll: 1,
            asNavFor: '#attachment-slider',
            dots: true,
            centerMode: false,
            focusOnSelect: true
        });
    });
    /*add files ======*/
    $('.add-files-wrap').hide();

	$('#messaging-sidebar, #automation-edit').on('click', '.add-btn', function()
	{
        var $activeTab = $('#'+$(this).attr("rel"));
        if ($activeTab.is(':visible')) {
			$activeTab.fadeOut();
			$(this).removeClass('selected')
		}
		else {
			$activeTab.fadeIn();
			$(this).addClass("selected");
		}
    });

    /*custom tabs ======*/
    $(".tabs-pills li:first a").addClass("selected");
    $(".tabs-pills > li > a").click(function ()
	{
		var $pills = $(this).parents('.tabs-pills');
		var $pane = $($pills.data('pane'));

		$pane.find('.tabs-pills-content').hide();
        var $active_tab = $($(this).attr('rel'));
        $active_tab.fadeIn();

        $pills.find('> li > a').removeClass('selected');
        $(this).addClass('selected');
	});

	// $(document).on('submit', '.send-message-form', messaging_submit_handler);

    $('#messaging-contact-course-location-finder').on('change', function()
    {
        $.get('/admin/messaging/ajax_course_contact_finder_autocomplete', {"location_ids": $(this).val()}, function (result, status)
        {
            rebuild_multiselect($('#messaging-contact-course-category-finder'), result.categories);
            rebuild_multiselect($('#messaging-contact-course-schedule-finder'), result.schedules);
        });
    });

    $('#messaging-contact-course-category-finder').on('change', function()
    {
        $.get('/admin/messaging/ajax_course_contact_finder_autocomplete',
            {"location_ids": $('#messaging-contact-course-location-finder').val(), "course_category_ids": $(this).val()}, function (result, status)
        {
            rebuild_multiselect($('#messaging-contact-course-schedule-finder'), result.schedules);
        });
    });

    $('.message-compose-search-btn').on('click', function()
    {
        var $search_section = $('#messaging-compose-search');
        var type = $(this).data('type');
        $('#messaging-compose-search').data('type', type);
        var recipient_type = $(this).data('recipient_type');

        if(type === 'sms') {
            $('.messaging-compose-search-contacts-table-notification-label').text('Number');
        } else {
            $('.messaging-compose-search-contacts-table-notification-label').text('Email');
        }
        $('#messaging-compose-search-recipient_type').html(recipient_type);
        $search_section.data("list_id", $(this).parents(".message-compose-value").find(".contact-list-labels").attr("id"));

        // If the section was dismissed, it should be reset before it is opened again
        if (!$search_section.is(':visible'))
        {
            $search_section.form_reset(); // Reset each form field to its original value
            $search_section.find('.messaging-condition-panel-remove').trigger('click'); // Hide panels
            $('#messaging-compose-search-contacts-list').addClass('hidden');
            $('#messaging-find_contacts-button').addClass('hidden');

            $search_section.show();
        }
    });

    // When a condition is selected, show the "Add" button.
    $('#messaging-compose-search-condition').on('change', function() {
        var $btn = $('#messaging-compose-search-condition-add');

        this.value ? $btn.removeClass('hidden') : $btn.addClass('hidden');
    });

    // When the "Add" button is clicked, show the section corresponding the to the condition
    $('#messaging-compose-search-condition-add').on('click', function() {
        var condition = $('#messaging-compose-search-condition').val();
        var $panel    = $('#messaging-condition-panel--'+condition);

        $panel.find('.panel-body').collapse('show');
        $panel.removeClass('hidden');

        // Show the "find a contacts" button when there's at least one condition
        $('#messaging-find_contacts-button').removeClass('hidden');
    });

    // When a condition's remove icon is clicked, hide the panel and unset its contained form fields
    $('.messaging-condition-panel-remove').on('click', function() {
        var $panel = $(this).parents('.panel');

        $panel.addClass('hidden');
        $panel.find('select[multiple] :selected').prop('selected', false);
        $panel.find('select[multiple]').multiselect('refresh');

        // Remove the "find a contacts" button when there are no conditions
        if ($('.messaging-condition-panel:not(.hidden)').length == 0) {
            $('#messaging-find_contacts-button').addClass('hidden');
        }
    });

    // When the list of contacts is opened...
    $('#messaging-compose-search-contacts-list').on('show.bs.collapse', function() {
        // Collapse the condition sections
        $('.messaging-condition-panel-body:visible').each(function() {
            $(this).parents('.messaging-condition-panel').find('.messaging-condition-panel-toggle').click();
        });
    });

    $('#messaging-compose-search-contacts-table').find('[data-toggle="tooltip"]').tooltip();

    // When the select all contacts button, is clicked, check all checkboxes in the table
    $('#messaging-compose-search-select_contact-all').on('click', function() {
        var filters = {};
        $(".messaging-condition-panel:not(.hidden) select").each(function(){
            var val = $(this).val();
            if (val) {
                filters[this.name] = val;
            }
        });
        filters.type = $('.send-message-form:visible [name="driver"]').val();
        if($('#messaging-compose-search-contacts-table_filter').find('input').first().val()){
            filters.global_search = '1';
            filters.sSearch = $('#messaging-compose-search-contacts-table_filter').find('input').first().val();
        }

        $.get("/admin/messaging/ajax_get_all_filter_contacts", filters, function(contacts, status) {
            for(var i = 0; i < contacts.length; i++) {
                selected_contacts[contacts[i].id] = {};
                selected_contacts[contacts[i].id].sms = contacts[i].mobile;
                selected_contacts[contacts[i].id].label = `${contacts[i].first_name} ${contacts[i].last_name}`;
                selected_contacts[contacts[i].id].email = contacts[i].email;
                selected_contacts[contacts[i].id].category = contacts[i].rtype;
                selected_contacts[contacts[i].id].template_data_id = contacts[i].template_field || '';
                selected_contacts[contacts[i].id].template_helper_function = contacts[i].template_helper_function || '';
                selected_contacts[contacts[i].id].checked = true;
            }
            $('.messaging-compose-search-select_contact').data('selected_contacts', selected_contacts);
            $('.messaging-compose-search-select_contact').prop('checked', true);
            update_finder_counter();
        });
    });

    // Similarly, uncheck all when the clear button is clicked
    $('#messaging-compose-search-select_contact-clear').on('click', function() {
        $('.messaging-compose-search-select_contact').data('selected_contacts', {});
        $('.messaging-compose-search-select_contact').prop('checked', false);
        update_finder_counter();
    });

    // When a contact checkbox is checked...
    $(document).on('change', '.messaging-compose-search-select_contact', function() {
        var selected_contacts =  $('.messaging-compose-search-select_contact').data('selected_contacts') || {};
        if(this.checked) {
            var selected_contact = {};
            selected_contact = {};
            selected_contact["sms"] = $(this).data("sms");
            selected_contact["label"] = $(this).data("label");
            selected_contact["email"] = $(this).data("email");
            selected_contact["category"] = $(this).data("category");
            selected_contact["checked"] = this.checked;
            selected_contact["template_data_id"] = $(this).data("template_data_id");
            selected_contact["template_helper_function"] = $(this).data("template_helper_function");
            selected_contact["primary_contact_id"] = $(this).data("primary_contact_id");
            selected_contact["primary_contact_label"] = $(this).data("primary_contact_label");
            selected_contacts[this.value] = selected_contact;
        } else {
            delete selected_contacts[this.value];
        }
        $('.messaging-compose-search-select_contact').data('selected_contacts', selected_contacts);
        update_finder_counter();

    });

    // Update the counter text when singular checkbox changes or user selects all
    function update_finder_counter() {
        var $report = $('#messaging-compose-search-contacts-counter_report');
        var number_selected = Object.keys($('.messaging-compose-search-select_contact').data('selected_contacts')).length || $('.messaging-compose-search-select_contact:checked').length;
        $('#messaging-compose-search-contacts-counter').html(number_selected);

        // Different text displays, if the message contains a singular or a plural.
        // No message displays, if none are selected.
        if (number_selected == 0) {
            $report.addClass('hidden').find('.singular_text, .plural_text').addClass('hidden');
        }
        else if (number_selected == 1) {
            $report.find('.plural_text').addClass('hidden');
            $report.find('.singular_text').removeClass('hidden');
            $report.removeClass('hidden');
        }
        else {
            $report.find('.singular_text').addClass('hidden');
            $report.find('.plural_text').removeClass('hidden');
            $report.removeClass('hidden');
        }
    }
    /* Drag and drop to add an image */
    var drop_area = document.querySelector('#messaging-sidebar .drag_and_drop_area');
    drop_area.ondragover = function (ev) {ev.preventDefault(); $(this).addClass('hover'); return false; };
    drop_area.ondragend  = function () { $(this).removeClass('hover'); return false; };
    drop_area.ondrop     = function (ev)
    {
        ev.preventDefault();
        $(this).removeClass('hover');
        var files = ev.dataTransfer.files || ev.target_files;
        add_message_attachment(files);
    };

    // Add an image
    $('#messaging-sidebar-add_photo').on('change', function()
    {
        add_message_attachment(this.files);

        // Reset the file input field
        var $input  = $(this);
        $input.replaceWith($input.val('').clone(true));
    });

    function add_message_attachment(files)
    {
        var formats   = ['image/jpeg', 'image/png', 'image/gif','image/svg+xml', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        var form_data;

        for (var i = 0; i < files.length; i++)
        {
            if (files[i])
            {
                if (formats.indexOf(files[i].type) == -1) {
                    $(".file-detail .alert-area").add_alert("Unrecognized file type", 'error');
                } else if (files[i].size > (window.ibcms.max_attachment_size_mb * 1024 * 1024)) {
                    $(".file-detail .alert-area").add_alert("The file size exceeds maximum limit", 'error');
                } else {
                    form_data = new FormData();
                    form_data.append('0', files[i]);

                    $.ajax({
                        url: '/admin/media/ajax_upload',
                        type: 'POST',
                        data: form_data,
                        cache: false,
                        dataType: 'json',
                        async: true,
                        processData: false,
                        contentType: false,
                        fail: function () {

                        },
                        success: function (data, textStatus, jqXHR) {
                            add_message_attachment_html({file_name: data.fullpaths[0], file_url: data.fullpaths[0]});
                        }
                    });
                }
            }
        }
    }

	// Remove an attachment
	$sidebar.on('click', '.messaging-sidebar-attachment-remove', function()
	{
		$(this).parents('tr').remove();
		var $table = $('#messaging-sidebar-attachments-list');
		if ($table.find('tbody tr').length == 0)
		{
			$table.hide();
		}
	});


    var selected_contacts = {};

    $("#messaging-find_contacts-button").on("click", function(){
        var filters = {};
        var type = $('#messaging-compose-search').data('type');

        $(".messaging-condition-panel:not(.hidden) select").each(function(){
            var val = $(this).val();
            if (val) {
                filters[this.name] = val;
            }
        });

        var $table = $("#messaging-compose-search-contacts-table");
        $("#messaging-compose-search-contacts-list").removeClass("hidden");
        $table.find("tbody").html("");
        
        selected_contacts = {};
        var ajaxSource = '/admin/messaging/ajax_filter_contacts?type=' + type + '&' + $.param(filters);
        var settings = {
            "aLengthMenu"     : [10, 25, 50, 100, 250],
            "bDestroy"        : true,
            "sDom"            : 'lfrtip',
            "sPaginationType" : 'bootstrap',
            "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
                oSettings.jqXHR = $.ajax({
                    "dataType": 'json',
                    "type": "GET",
                    "url": sSource,
                    "data": aoData,
                    "success": function (a, b, c) {
                        var selected_contacts = $('.messaging-compose-search-select_contact').data('selected_contacts') || {};
                        fnCallback(a, b, c);

                        $table.find(".messaging-compose-search-select_contact").each(function(){
                            if (selected_contacts[this.value]) {
                                this.checked = selected_contacts[this.value].checked;
                            }
                        });
                    }
                });
            }

        };
        $table.ib_serverSideTable(ajaxSource, settings);
        return;

        $.post(
            '/admin/messaging/ajax_filter_contacts',
            {
                filters: filters,
                type: type
            },
            function (contacts) {
                found_contacts = contacts;
                $("#messaging-compose-search-contacts-table").dataTable().fnDestroy();
                var $table = $("#messaging-compose-search-contacts-table tbody");
                $table.html("");

                var $template = $('#messaging-compose-search-contacts-template');
                var $clone, $checkbox, contact, contact_name, allow_email, allow_sms, allow_phone, p;

                for (var i in contacts) {
                    contact = contacts[i];
                    allow_email = false;
                    allow_sms = false;
                    allow_phone = false;
                    for (p in contact.preferences) {
                        if (contact.preferences[p].stub == 'email' && contact.preferences[p].value == 1) {
                            allow_email = true;
                        }
                        if (contact.preferences[p].stub == 'text_messaging' && contact.preferences[p].value == 1) {
                            allow_sms = true;
                        }
                        if (contact.preferences[p].stub == 'phone_call' && contact.preferences[p].value == 1) {
                            allow_phone = true;
                        }
                    }

                    $clone = $template.clone();
                    contact_name = (contact.first_name + ' ' + contact.last_name).trim();

                    $clone.removeAttr('id');

                    if (contact.preferences[0].value) $clone.find('.icon-phone'   ).removeClass('invisible');
                    if (contact.preferences[1].value) $clone.find('.icon-envelope').removeClass('invisible');
                    if (contact.preferences[2].value) $clone.find('.icon-mobile'  ).removeClass('invisible');

                    $clone.find('.messaging-search-contact-name').text(contact_name);
                    $clone.find('.messaging-search-contact-role').text(contact.role);
                    $clone.find('.messaging-search-contact-primary').text(contact.primary_contacts);

                    $checkbox = $clone.find('.messaging-compose-search-select_contact');

                    if ((type == 'sms' && allow_sms) || (type == 'email' && allow_email)) {
                        $checkbox
                            .attr('data-category', contact.rtype)
                            .attr('data-label',    contact_name)
                            .attr('data-email',    contact.email)
                            .attr('data-sms',      contact.mobile)
                            .attr('value',         contact.id)
                            .val(contact.id)
                        ;

                        $checkbox.data('category', contact.rtype);
                        $checkbox.data('label',    contact_name);
                        $checkbox.data('email',    contact.email);
                        $checkbox.data('sms',      contact.mobile);

                        $clone.find('.messaging-search-contact-na').remove();
                    }
                    else {
                        $checkbox.parents('.form-checkbox').remove();
                        $checkbox.remove();
                    }

                    $table.append($clone);
                }
                $("#messaging-compose-search-contacts-table").dataTable({
                    "bLengthChange": false,
                    "iDisplayLength": 10
                });

                $('#messaging-compose-search-contacts-table').find('[data-toggle="tooltip"]').tooltip();

                $("#messaging-compose-search-contacts-list").removeClass("hidden");
            }
        );
    });

    $("#messaging-compose-search-contacts-list .btn-primary").on("click", function(){
        var div = "#messaging-sidebar #" + $('#messaging-compose-search').data("list_id");
        x_details = "";
        if ($(div).parents(".message-compose-field").attr("id") == "cc") {
            x_details = "cc";
        }
        if ($(div).parents(".message-compose-field").attr("id") == "bcc") {
            x_details = "bcc";
        }

        var add_primary_contact = $(this).hasClass('add_primary');
        for (var i in $('.messaging-compose-search-select_contact').data('selected_contacts')) {
            var contact = $('.messaging-compose-search-select_contact').data('selected_contacts')[i];
            var target = {};
            target.value = i;
            target.label = contact.label;
            target.category = contact.category;
            target.email = contact.email;
            target.sms = contact.sms;
            target.final_target = $("#send-sms").hasClass('hidden') ? contact.email : contact.sms;
            target.final_target_type = $("#send-sms").hasClass('hidden') ? "EMAIL" : "PHONE";
            target.template_data_id = contact.template_data_id;
            target.template_helper_function = contact.template_helper_function;
            if (x_details == "" && target.final_target_type == "EMAIL") {
                x_details = "to";
            }
            messaging_target_add(div, (target.final_target_type == "EMAIL" ? "email_recipient" : "sms_recipient"), target, {"x_details": x_details});
            if (add_primary_contact) {

                var pc_target = {};
                pc_target.value = contact.primary_contact_id;
                pc_target.label = contact.primary_contact_label;
                pc_target.category = contact.category;
                if (pc_target.value != target.value && pc_target.value != "" && pc_target.value != null) {
                    messaging_target_add(div, (target.final_target_type == "EMAIL" ? "email_recipient" : "sms_recipient"), pc_target, {"x_details": x_details});
                }
            }
        }
    });
}); /*end*/

function add_message_attachment_html(args)
{
    var file_name = args.file_name || '';
    var file_id   = args.file_id   || '';
    var file_url  = args.file_url  || location.protocol + '//' + location.host + '/media/docs/' +file_name;

    var extension = file_name.split('.').pop().toLowerCase();
    var $clone    = $('#messaging-sidebar-attachments-list-template').find('tr').clone();

    if (['jpg', 'jpeg', 'png', 'gif', 'svg'].indexOf(extension) > -1) {
        file_url  = args.file_url || location.protocol + '//' + location.host + '/media/photos/content/_thumbs_cms/' +file_name;
        $clone.find('.attachment-icon--image').removeClass('hidden').attr('src', file_url);
    }
    else if (['doc', 'docx'].indexOf(extension) > -1) {
        $clone.find('.attachment-icon--document').removeClass('hidden');
    }
    else if (extension == 'pdf') {
        $clone.find('.attachment-icon--pdf').removeClass('hidden');
    }
    else {
        $clone.find('.attachment-icon--default').removeClass('hidden');
    }
    $clone.find('.attachment-icon.hidden').remove();

    if (typeof file_id != 'undefined') {
        $clone.append('<input type="hidden" class="messaging-sidebar-attachment-file_id" value="'+file_id+'" />');
    }

    var fname = file_name.split("/");
    fname = fname[fname.length - 1];
    $clone.find('.messaging-sidebar-attachment-icon').data('src', file_url).attr('data-src', file_url);
    $clone.find('.messaging-sidebar-attachment-name').text(fname);

    $('#messaging-sidebar-attachments-list').show().find('tbody').append($clone);
}