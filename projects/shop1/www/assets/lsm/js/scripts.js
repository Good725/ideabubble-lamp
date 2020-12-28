if( !window.disableScreenDiv ){
	window.disableScreenDiv = document.createElement( "div" );
	window.disableScreenDiv.style.display = "block";
	window.disableScreenDiv.style.position = "fixed";
	window.disableScreenDiv.style.top = "0px";
	window.disableScreenDiv.style.left = "0px";
	window.disableScreenDiv.style.right = "0px";
	window.disableScreenDiv.style.bottom = "0px";
	window.disableScreenDiv.style.textAlign = "center";
	window.disableScreenDiv.style.visibility = "hidden";
	window.disableScreenDiv.style.zIndex = 9999;
	window.disableScreenDiv.innerHTML = '<div style="position:absolute;top:0;left:0;right:0;bottom:0;background-color:#ffffff;opacity:0.2;filter:alpha(opacity=20);z-index:1;"></div>' +
			'<div class="ajax_loader_icon_inner" style="position:absolute;top:50%;left:50%;z-index:2;width: 32px;height: 32px;margin: 0 auto;background-image: url(\'/engine/shared/img/ajax-loader.gif\')"></div>';
	window.disableScreenDiv.autoHide = true;
	document.body.appendChild(window.disableScreenDiv);
}

$(document).ready(function()
{
	$(document).on('click', '.button.wishlist_add', function(){
		var button = this;
		var contact_id = $(this).data('contact_id');
		var schedule_id = $(this).data('schedule_id');

		$.post(
				'/frontend/contacts3/wishlist_add',
				{
					contact_id: contact_id,
					schedule_id: schedule_id
				},
				function (response) {
					$(button).addClass("hidden");
					$(".button.wishlist_remove").each(function(){
						if ($(this).data("schedule_id") == schedule_id) {
							$(this).removeClass("hidden");
						}
					})
				}
		);
	});

	$(document).on('click', '.button.wishlist_remove', function(){
		var button = this;
		var contact_id = $(this).data('contact_id');
		var schedule_id = $(this).data('schedule_id');

		$.post(
				'/frontend/contacts3/wishlist_remove',
				{
					contact_id: contact_id,
					schedule_id: schedule_id
				},
				function (response) {
					$(button).addClass("hidden");
					$(".button.wishlist_add").each(function(){
						if ($(this).data("schedule_id") == schedule_id) {
							$(this).removeClass("hidden");
						}
					})
				}
		);
	});

	$(document).ajaxStart(function(){
		window.disableScreenDiv.style.visibility = "visible";
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

	/*------------------------------------*\
	 #Sliders
	\*------------------------------------*/
	new Swiper('#home-banner-swiper',{
		autoplay: 5000,
		loop: true,
		pagination: '#home-banner-swiper .swiper-pagination',
		paginationClickable: true,
		nextButton: '#home-banner-swiper .swiper-button-next',
		prevButton: '#home-banner-swiper .swiper-button-prev'
	});

	new Swiper('#news-slider', {
		autoplay: 5000,
		loop: true,
		pagination: '#news-slider .swiper-pagination',
		paginationClickable: true,
		nextButton: '#news-slider .swiper-button-next',
		prevButton: '#news-slider .swiper-button-prev'
	});

	var carousel_slider = new Swiper('#courses-carousel', {
		slidesPerView: calculate_slides_per_view(),
		paginationClickable: true,
		nextButton: '#courses-carousel-next',
		prevButton: '#courses-carousel-prev',
		spaceBetween: 31
	});

	$(window).resize(function()
	{
		carousel_slider.params.slidesPerView = calculate_slides_per_view();
	});

	function calculate_slides_per_view()
	{
		var width = window.innerWidth;
		var count = 4;
		if      (width <  768) count = 1;
		else if (width <  990) count = 2;
		else if (width < 1240) count = 3;

		return count;
	}




	/*------------------------------------*\
	 #Validation
	\*------------------------------------*/
	$('.validate-on-submit').on('submit',function()
	{
		if ( ! $(this).validationEngine('validate'))
		{
			return false;
		}
	});

    $('.datepicker').datetimepicker(
    {
        format:'d/m/Y',
        timepicker: false,
        closeOnDateSelect: true
    });



	/*------------------------------------*\
	 #Menus
	\*------------------------------------*/
	$('.header-menu-expand').on('click', function(ev)
	{
		ev.preventDefault();
		var $menu  = $(this).find('\+ .header-menu');
		var visible = $menu.is(':visible');

		// If any other menus are expanded, hide them
		$('.header-menu').hide();

		// Show the menu if it was hidden before the expand button was clicked
		if ( ! visible) $menu.show();
	});

	// Dismiss when clicked away from
	$(document).on('click', function(ev) {
		if ( ! $(ev.target).closest('.header-menu, .header-menu-expand').length) {
			$('.header-menu').hide();
		}
	});

	$('.level2 .submenu-expand').on('click', function()
	{
		var $li = $(this).parents('li');
		$li.hasClass('expanded') ? $li.removeClass('expanded') : $li.addClass('expanded');
	});




	/*------------------------------------*\
	 #Calendar
	\*------------------------------------*/
	$(document).ready(function() {
		if ($("#sidebar-calendar").length > 0) {
			$("#sidebar-calendar").eventCalendar({
				eventsjson: '/frontend/courses/get_calendar_event_feed',
				jsonDateFormat: 'human',
				cacheJson: false,
				dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thur', 'Fri', 'Sat'],
				startWeekOnMonday: false
			});
		}
	});




	/*------------------------------------*\
	 #Banner search
	\*------------------------------------*/
	// Display the dropout menu, when its input box is focused
	$('[data-drilldown]').focus(function()
	{
		$('.search-drilldown.active').removeClass('active');
		$(this.getAttribute('data-drilldown')).addClass('active');
	});

	// Function to check if a list is empty.
	// A list can be empty due to a selection not being made or due to no results being found.
	// A different message is displayed for each.
	$.fn.notify_if_empty = function()
	{
		if (this.children(':visible').length == 0) {

			// Check if this column is waiting on a filter (filter column exists and does not have a selection.)
			var $filtered_by = $(this.data('filtered_by'));
			var awaiting_filter = ($filtered_by.length && ! $filtered_by.find('.active').length);

			if (awaiting_filter)
			{
				this.find('~ .search-drilldown-no_results').addClass('hidden');
				this.find('~ .search-drilldown-awaiting_selection').removeClass('hidden');
			}
			else
			{
				this.find('~ .search-drilldown-awaiting_selection').addClass('hidden');
				this.find('~ .search-drilldown-no_results').removeClass('hidden');
			}
		}
		else
		{
			this.find('~ .search-drilldown-awaiting_selection').addClass('hidden');
			this.find('~ .search-drilldown-no_results').addClass('hidden');
		}

		return this;
	};

	// Filter results, as the user types
	$('[data-type_search]').on('keyup', function()
	{
		var term = this.value;
		var lc_term = term.toLowerCase();
		var re = new RegExp("^" + term, "gi") ;
		var $list = $(this.getAttribute('data-type_search'));

		$list.find('li').each(function()
		{
			var item = this.getElementsByTagName('a')[0];

			// remove tags (remove bolding from previous search terms)
			item.innerHTML = item.innerHTML.replace(/(<([^>]+)>)/ig, '');

			if (item.innerHTML.toLowerCase().indexOf(lc_term) == -1)
			{
				// Hide irrelevant items
				this.style.display = 'none';
			}
			else
			{
				// Show relevant items and bold the keyword
				this.style.display = '';
				item.innerHTML = item.innerHTML.replace(re, function(str) {return '<b>'+str+'</b>'});
			}
		});

		$list.notify_if_empty();
	});

	// When a location is selected, put it in the search bar and dismiss the menu
	$('#location-drilldown-location-list').on('click', 'a', function(ev)
	{
		ev.preventDefault();
		// Put the selected value, minus HTML tags in the search bar
		$('#banner-search-location_id')[0].value = this.getAttribute('data-id');
		$('#banner-search-location')[0].value = this.innerHTML.replace(/(<([^>]+)>)/ig, '');

		// Dismiss the current dropout and move on to the next one
		$('#banner-search-subject').focus();
	});

	// When a subject is selected, show the relevant class types (categories)
	$('#subject-drilldown-subject-list').on('click', 'a', function(ev)
	{
		ev.preventDefault();
		var $selected = $(this);
		var subject_id;

		if ($selected.hasClass('active'))
		{
			// Dismiss, if clicked when already active
			$selected.removeClass('active');
			subject_id = '';
		}
		else
		{
			$('#subject-drilldown-subject-list').find('.active').removeClass('active');
			$selected.addClass('active');
			subject_id = this.getAttribute('data-id');
		}

		$.ajax('/frontend/courses/ajax_get_categories_from_subject/'+subject_id).done(function(data)
		{
			var category_ids = subject_id ? JSON.parse(data) : [];
			var $list = $('#subject-drilldown-category-list');

			var category_id;

			// Remove previous selection / results
			$('#subject-drilldown-course-list').html('');
			$list.find('.active').removeClass('active');

			// Hide class types that don't match the search results
			$list.find('a').each(function()
			{
				category_id = parseInt($(this).attr('data-id'));
				this.parentElement.style.display = (category_ids.indexOf(category_id) != -1) ? '' : 'none';
			});

			// For smaller screens, position the class type list, relative to the selected subject
			if (window.innerWidth < 768)
			{
				var selected_position = $selected.parents('li').position();
				$list.parents('.search-drilldown-column').css('top', selected_position.top + $selected.parents('li').height());

				if (subject_id)
					$list.parents('.search-drilldown-column').show();
				else
					$list.parents('.search-drilldown-column').hide(); // Dismiss, if clicked when already active
			}

			$list.removeClass('hidden').notify_if_empty();
		});
	});

	// When a class type (category) is selected, show the relevant courses
	$('#subject-drilldown-category-list').on('click', 'a', function(ev)
	{
		ev.preventDefault();
		var $selected = $(this);
		var subject_id = $('#subject-drilldown-subject-list').find('.active').attr('data-id');
		var category_id;

		if ($selected.hasClass('active'))
		{
			// Dismiss, if clicked when already active
			$selected.removeClass('active');
			category_id = '';
		}
		else
		{
			$('#subject-drilldown-category-list').find('.active').removeClass('active');
			$selected.addClass('active');
			category_id = $(this).attr('data-id');
		}

		$.ajax({
			url: '/frontend/courses/ajax_get_courses',
			data: { subject_id: subject_id, category_id: category_id }
		}).done(function(data)
			{
				data = category_id ? JSON.parse(data) : [];
				var $list = $('#subject-drilldown-course-list');
				var html = '';

				for (var i = 0; i < data.length; i++)
				{
					html += '<li><a href="#" data-id="'+data[i].id+'">'+data[i].title+'</a></li>';
				}

				// For smaller screens, position the class type list, relative to the selected subject
				if (window.innerWidth < 768)
				{
					var selected_position = $selected.parents('li').position();
					$list.parents('.search-drilldown-column').css('top',
						selected_position.top +
						$selected.parents('li').height() +
						$('.search-drilldown-column--category').position().top
					);

					if (category_id)
						$list.parents('.search-drilldown-column').show();
					else
						$list.parents('.search-drilldown-column').hide(); // Dismiss, if clicked when already active
				}

				$list.html(html);
				$list.notify_if_empty();
			});
	});

	// When a course is selected, put it in the search bar and dismiss the menu
	$('#subject-drilldown-course-list').on('click', 'a', function(ev)
	{
		ev.preventDefault();

		$('#subject-drilldown-course-list').find('.active').removeClass('active');
		$(this).addClass('active');

		$('#banner-search-subject_id')[0].value = this.getAttribute('data-id');
		$('#banner-search-subject')[0].value = this.innerHTML;

		$('#subject-drilldown').removeClass('active');
	});

	// Dismiss via the close icon
	$('.search-drilldown-close').on('click', function()
	{
		$(this).parents('.search-drilldown').removeClass('active');
	});

	// Dismiss when clicked away from
	$(document).on('click', function(ev) {
		if ( ! $(ev.target).closest('.search-drilldown, [data-drilldown]').length) {
			$('.search-drilldown').removeClass('active');
		}
	});




	/*------------------------------------*\
	 #Search results
	\*------------------------------------*/
	var $course_results = $('#course-results');

	/* Toggle visibility of sidebar filters */
	$('.sidebar-section-collapse').on('click', function(ev)
	{
		ev.preventDefault();
		$(this).parents('.sidebar-section').find('.sidebar-section-content').slideToggle(0);
	});

	/* Toggle between list and grid mode */
	$course_results.on('change', '[name="course_list_display"]', function()
	{
		if (this.value == 'grid')
		{
			$('.course-list').addClass('course-list--grid').removeClass('course-list--list');
		}
		else
		{
			$('.course-list').addClass('course-list--list').removeClass('course-list--grid');
		}
	});

	// Only grid mode exists on small screens
	$(window).resize(function()
	{
		if (window.innerWidth < 1024)
		{
			$('#course-list-display_grid').prop('checked', true).trigger('change');
		}
	});

	/* Display fee when a schedule is selected */
	$course_results.on('change', '.course-widget-schedule', function()
	{
		var $price        = $(this).parents('.course-widget').find('.course-widget-price');
		var $price_amount = $price.find('.course-widget-price-amount');
		var fee           = $(this).find(':selected').attr('data-fee');

		if (fee == 0)
		{
			$price.hide();
			$price_amount.html('');
		}
		else
		{
			$price_amount.html(fee);
			$price.show();
		}
	});

	/* Search filters */
	var $search_criteria = $('#course_filter_criteria');

	// When a checkbox is changed, add/remove its entry in the search criteria
	$('.sidebar-filter-options').find('[type="checkbox"]').on('change', function(ev, arg)
	{
		arg = (typeof arg !== 'undefined') ? arg : null;

		// Add or remove the item from "search criteria" section
		if (this.checked)
		{
			var $li = $('<li class="search-criteria-li">'+$('#course_filter_criteria_template').html()+'</li>');
			$li
				.data('id', this.getAttribute('data-id'))
				.attr('data-id', this.getAttribute('data-id'));
			$li.find('.search-criteria-category').html(this.getAttribute('data-category'));
			$li.find('.search-criteria-value').html(this.getAttribute('data-value'));

			$li.insertBefore('#search-criteria-reset-li')
		}
		else
		{
			$('.search-criteria-li[data-id="'+this.getAttribute('data-id')+'"]').remove();
		}

		// Use "no_update" to only update the search criteria section and not perform
		// the AJAX call to update the results. (Only used on the initial page load.)
		if (arg != 'no_update')
		{
			update_search_results();
		}
	}).trigger('change', ['no_update']);

	// When a criteria item remove icon is clicked, remove the item and un-check its checkbox
	$search_criteria.on('click', '.search-criteria-remove', function()
	{
		var $li = $(this).parents('li');
		// Uncheck the corresponding checkbox and trigger its change action
		var id = $li.data('id');
		$('input[data-id="'+id+'"]').prop('checked', false).trigger('change');

		// Course results don't have a checkbox in the sidebar, so this is handled separately
		if ($li.find('.filter-course_ids').length)
		{
			$li.remove();
			update_search_results();
		}
	});

	// Clear all search criteria
	$('.search-criteria-reset').on('click', function()
	{
		$('.search-criteria-li').remove();
		$('.sidebar-filter-options').find('[type="checkbox"]').prop('checked', false);
		update_search_results();
	});

	// Keyword search
	$('#course-filter-keyword').on('change', update_search_results);

	// Navigate through pagination results
	$course_results.on('click', '.pagination a:not(.disabled)', function(ev)
	{
		ev.preventDefault();
		$course_results.find('.pagination .current').removeClass('current');
		$(this).addClass('current');
		update_search_results();
		$course_results[0].scrollIntoView();
	});

	function update_search_results()
	{
		var $sidebar     = $('#sidebar');
		var sort         = $('[name="course_list_sort"]:checked').val();
		var display      = $('[name="course_list_display"]:checked').val();
		var keywords     = $('#course-filter-keyword').val();
		var page         = $('#search_results-pagination').find('.current').attr('data-page');
		var location_ids = [];
		var year_ids     = [];
		var category_ids = [];
		var level_ids    = [];
		var course_ids   = [];

		page = page ? page : 1;

		$sidebar.find('[name="location_ids[]"]:checked').each(function(){location_ids.push(this.value);});
		$sidebar.find('[name="year_ids[]"]:checked'    ).each(function(){    year_ids.push(this.value);});
		$sidebar.find('[name="category_ids[]"]:checked').each(function(){category_ids.push(this.value);});
		$sidebar.find('[name="level_ids[]"]:checked'   ).each(function(){   level_ids.push(this.value);});
		$('.filter-course_ids'                         ).each(function(){  course_ids.push(this.value);});

		$.ajax(
		{
			url     : '/frontend/courses/ajax_filter_results',
			data    : {
				'location_ids' : location_ids,
				'year_ids'     : year_ids,
				'category_ids' : category_ids,
				'level_ids'    : level_ids,
				'course_ids'   : course_ids,
				'keywords'     : keywords,
				'sort'         : sort,
				'display'      : display,
				'page'         : page,
				'reminder'     : 0,
				'timeslots'    : 1
			},
			type     : 'post',
			dataType : 'json'
		}).done(function(result)
			{
				$('#course-results').html(result);
			});
	}




	/*------------------------------------*\
	 #Course details
	\*------------------------------------*/
	$("#schedule_selector").on("change", function()
	{
		var id = $(this).val();
		var event_id = (this.selectedIndex != -1) ? $(this.options[this.selectedIndex]).data('event_id') : '';
		var $price_wrapper = $('.price_wrapper');

		$("#enquire-course, #book-course").prop("disabled", true);
		if (id.length > 0)
		{
			$.post('/frontend/courses/get_schedule_price_by_id', {sid: id, event_id: event_id}, function (data)
			{
				$price_wrapper.find('.price').html(data.price);
				$price_wrapper.css('visibility', 'visible');
				$("#enquire-course, #book-course").prop("disabled", false);
			});
		}
		else
		{
			$price_wrapper.css('visibility', 'hidden');
			$('#trainer_name').hide().html('');
			$price_wrapper.find('.price').html('');
		}
	}).trigger('change');




	/*------------------------------------*\
	 #Booking/enquiry form
	\*------------------------------------*/
	$('#booking_form-student_date_of_birth').datetimepicker({ format: 'd/m/Y', timepicker: false });

	$('.booking-use_guardian-toggle').on('change', function()
	{
		var $target = $($(this).data('target'));
		var $target_sync_with = $($target.data('sync_with'));
		if (this.checked)
		{
			// Show the fields and disable synchronisation with the guardian section
			$target.removeClass('hidden');
			$target.data('sync', 'off').attr('data-sync', 'off');
			$target_sync_with.data('sync', 'off').attr('data-sync', 'off');
		}
		else
		{
			// Hide the fields, enable synchronisation with the guardian section and perform a sync
			$target.addClass('hidden');
			$target.data('sync', 'on').attr('data-sync', 'on');
			$target_sync_with.data('sync', 'on').attr('data-sync', 'on');
			$target_sync_with.find('[data-field]').each(function(){$(this).trigger('change');});
		}
	});

	// Synchronise fields in two linked sections
	$('[data-sync="on"]').find('[data-field]').on('change', function()
	{
		// The two sections to sync
		var $section = $(this).parents('[data-sync="on"]');
		var $sync    = $($section.data('sync_with')+'[data-sync="on"]');

		// When a field is changed, ensure the field with the same data-field in the other section is also changed
		$sync.find('[data-field="'+this.getAttribute('data-field')+'"]').val(this.value);
		$sync.find('[data-field="'+this.getAttribute('data-field')+'"][type="checkbox"]').prop('checked', this.checked);
	});

	$("#booking_form-guardian_relationship_to_student").on("change", function(){
		if(this.value=='other'){
			$('#booking_form-guardian_relationship_to_student_other').prop('disabled', false);
			$(".form-group.guardian_relationship_to_student_other").css("visibility", "visible");
		} else {
			$('#booking_form-guardian_relationship_to_student_other').prop('disabled', true);
			$(".form-group.guardian_relationship_to_student_other").css("visibility", "hidden");
		}

	});
});


/* ticket 2467 js */
$(function(){
    $('#db-student').change(function(){  if(this.checked){  $('#student-info').show() }else {  $('#student-info').hide();}  });
    $('#db-gu-student').on('change', function(){  if(this.checked){  $('#student-info2').show() }else {  $('#student-info2').hide();}  });
}); 

$(window).scroll(function () {
    var body_height = $(".dashboard-wrapper").position();
    var scroll_position = $(window).scrollTop();
	if (body_height) {
		if (body_height.top < scroll_position)
			$("body").addClass("fixed_strip");
		else
			$("body").removeClass("fixed_strip");
	}
});

$('.db-toggle-menu').click(function () {
		 $('body').addClass('flxed-nav');
		$('.db-sidebar').toggleClass('open');
		return false;
});
$('.avatar .header-avatar').click(function () {
	$(this).toggleClass('open');
	$(this).siblings('.subMenu-link').slideToggle();
	return false;
});
 $('.history-dropdown').click(function () {
	$(this).toggleClass('current-tab');
	$(this).next(".transactions-history").slideToggle()
		//$(this).siblings('.transactions-history').slideToggle();
	return false;
});
$('.action-btn > a').click(function () {
	$(this).toggleClass('open');
	//$('.action-btn ul').slideUp();
	$(this).siblings('.action-btn ul').slideToggle(500);
	return false;
});

function load_family_members_block(contact_id) {
	if($('#family_block').length){
		$.ajax({
			url: '/frontend/contacts3/ajax_get_family_members/',
		}).done(function(data) {
            $('#family_block').html(data);

			if(contact_id){
				$('#family_block a[data-contact_id="'+ contact_id +'"]').parent().addClass('db-current-tab');
			}
		});
	}
}
