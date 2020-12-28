(function() {
    var $;

    // This entire file should be rewritten to remove jQuery dependency
    if (typeof jQuery == 'undefined') {
        // jQuery not set up: dynamically add it, before running the rest of the file
        var script    = document.createElement('script');
        script.src    = 'https://code.jquery.com/jquery-2.2.4.min.js';
        script.type   = 'text/javascript';
        script.onload = function() {
            $ = window.jQuery;

            setup_finder();
        };
        document.getElementsByTagName('head')[0].appendChild(script);
    }
    else {
        $ = window.jQuery;
        // jQuery already set up: run the rest of the file immediately
        setup_finder();
    }

    function setup_finder()
    {
        var finder_script = document.querySelector('script[src*="/finder_menu.js"]');
        var base_url = finder_script ? finder_script.src.match(/^https?\:\/\/(?:www\.)?([^\/?#]+)(?:[\/?#]|$)/i)[0] : '/';

        // fill search form from search history
        $('.search_history').on('click',function (e) {
            if( ! $(e.target).hasClass("remove_search_history") )
            {
                var $this = $(this);
                $('#banner-search-location').val($this.data('location_name'));
                $('#banner-search-location-id').val($this.data('location_id'));

                $('#banner-search-subject').val($this.data('subject_name'));
                $('#banner-search-subject-id').val($this.data('subject_id'));
                $('#banner-search-year-id').val($this.data('year-id'));
                $('#banner-search-category-id').val($this.data('category-id'));

                // trigger form submit
                $('#banner-search-form').submit();
            }

        });

        // hide current search history and remove it from cookies
        $('.remove_search_history').on('click',function () {
            var parent_span =  $(this).parent('.search_history');
            var location_id = parent_span.data('location_id');
            var course_id = parent_span.data('course_id');
            parent_span.hide();
            var new_cookie_array = JSON.parse(Cookies.get('last_search_parameters'));
            new_cookie_array = $.grep(new_cookie_array, function(value) {
                return (value.location != location_id || value.course != course_id);
            });
            Cookies.set('last_search_parameters', JSON.stringify(new_cookie_array));
        });


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
            var re = new RegExp(term, 'gi') ;
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

            var finder_mode    = $('#banner-search-form').data('finder_mode');
            var $selected      = $(this);
            var $location_list = $('#location-drilldown-location-list');
            var location_ids   = [] , location_html = [];
            var is_fulltime    = $("[name=is_fulltime]:checked").val();

            $location_list.find('li a').removeClass('active');
            $selected.addClass('active');


            // Reset adjustments performed in pervious selections
            $selected.parents('ul').find('li').css('margin-top', '');

            // Put the selected value, minus HTML tags in the search bar
            if (this.getAttribute('data-id') === 'all') {
                $('#location-drilldown-location-list').find('li a.location').each(function() {
                    location_ids.push(this.getAttribute('data-id'));
                    location_html.push(this.innerText);
                });
                $('#banner-search-location-id')[0].value = location_ids;
                $('#banner-search-location')[0].value = location_html;
            }
            else {
                location_ids.push(this.getAttribute('data-id'));
                $('#banner-search-location-id')[0].value = this.getAttribute('data-id');
                $('#banner-search-location')[0].value = this.innerText;
            }

            if (finder_mode == 'training_company') {
                $.ajax({
                    url: base_url+'frontend/courses/ajax_get_courses',
                    data: { location_ids: location_ids, is_fulltime: is_fulltime }
                }).done(function(response)
                {
                    response = JSON.parse(response);
                    var $ul = $('#location-drilldown-course-list');
                    $ul.html('');
                    var course;
                    for (var i in response) {
                        course = response[i];
                        $ul.append('<li><a href="#" data-id="'+course.id+'">'+course.title+'</a></li>');
                    }

                    $ul.notify_if_empty();

                    // For smaller screens, position the list of courses, relative to the selected location
                    if (window.innerWidth < 768)
                    {
                        var selected_position = $selected.parents('li').position();

                        $ul.parents('.search-drilldown-column').css('top', selected_position.top + $selected.parents('li').height());

                        if (location_ids.length > 0) {
                            $selected.parents('li').find('\+ li').css('margin-top', $ul.parents('.search-drilldown-column').height()); // move the next location to below the course list
                            $ul.parents('.search-drilldown-column').show();
                        } else {
                            $ul.parents('.search-drilldown-column').hide(); // Dismiss, if clicked when already active
                        }
                    }
                });
            }
            else {
                $.post(
                    base_url+'frontend/courses/ajax_get_years',
                    { location_ids: location_ids, is_fulltime: is_fulltime },
                    function(response)
                    {
                        var $ul = $("#subject-drilldown-year-list");
                        $ul.html('');
                        var year;
                        for (var i in response) {
                            year = response[i];
                            $ul.append('<li><a class="" data-id="' + year.id + '">' + year.year + '</a>');
                        }
                    }
                );

                // Dismiss the current dropout and move on to the next one
                $('#banner-search-subject').focus();
            }
        });

        // When a year is selected, show the relevant course types (categories)
        $('#subject-drilldown-year-list').on('click', 'a', function(ev)
        {
            ev.preventDefault();
            var $selected = $(this);
            var year_id;

            if ($selected.hasClass('active'))
            {
                // Dismiss, if clicked when already active
                $selected.removeClass('active');
                year_id = '';
            }
            else
            {
                $('#subject-drilldown-year-list').find('.active').removeClass('active');
                $selected.addClass('active');
                year_id = this.getAttribute('data-id');
            }

            // add year_id to hidden input
            $('#banner-search-year-id').val(year_id);

            var location_ids = $("#banner-search-location-id").val().split(',');
            var is_fulltime    = $("[name=is_fulltime]:checked").val();
            $.post(
                base_url+'frontend/courses/ajax_get_categories_from_year/'+year_id,
                {location_ids: location_ids, is_fulltime: is_fulltime}
            ).done(function(data)
                {
                    var category_ids = year_id ? JSON.parse(data) : [];
                    var $list = $('#subject-drilldown-category-list');

                    var category_id;

                    // Remove previous selection / results
                    $('#subject-drilldown-subject-list').html('');
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

                        if (year_id)
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
            var year_id = $('#subject-drilldown-year-list').find('.active').attr('data-id');
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

            // add category_id to hidden input
            $('#banner-search-category-id').val(category_id);

            var location_ids = $("#banner-search-location-id").val().split(',');
            var is_fulltime    = $("[name=is_fulltime]:checked").val();

            $.ajax({
                url: base_url+'frontend/courses/ajax_get_subjects',
                data: { year_id: year_id, category_id: category_id, location_ids: location_ids, is_fulltime: is_fulltime }
            }).done(function(data)
            {
                data = category_id ? JSON.parse(data) : [];
                var $list = $('#subject-drilldown-subject-list');
                var html = '';


                for (var i = 0; i < data.length; i++)
                {
                    html += '<li><a href="#" data-id="'+data[i].id+'">'+data[i].name+'</a></li>';
                }

                // For smaller screens, position the class type list, relative to the selected subject
                if (window.innerWidth < 768)
                {
                    var selected_position = $selected.parents('li').position();
                    var $column = $list.parents('.search-drilldown-column');
                    $column.css('top',
                        selected_position.top +
                        $selected.parents('li').height() +
                        $column.prev().position().top
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

        // When a subject is selected, put it in the search bar and dismiss the menu
        $('#subject-drilldown-subject-list').on('click', 'a', function(ev)
        {
            ev.preventDefault();

            $('#subject-drilldown-subject-list').find('.active').removeClass('active');
            $(this).addClass('active');

            //$('#banner-search-subject-id')[0].value = this.getAttribute('data-id');
            $('#banner-search-subject-id').val(this.getAttribute('data-id'));
            $('#banner-search-subject')[0].value = this.innerHTML;

            $('#subject-drilldown').removeClass('active');
        });

        // When a course is selected, put it in the search bar and dismiss the menu
        $('#location-drilldown-course-list').on('click', 'a', function(ev)
        {
            ev.preventDefault();

            $('#location-drilldown-course-list').find('.active').removeClass('active');
            $(this).addClass('active');

            $('#banner-search-course-id').val(this.getAttribute('data-id'));
            $('#banner-search-course').val(this.innerText);

            $('#location-drilldown').removeClass('active');

            $('#search-button').focus();
        });

        // When an event is selected...
        $('#event-drilldown-event-list').on('click', 'a', function(ev)
        {
            ev.preventDefault();
            var $selected = $(this);
            var $counties = $('#event-drilldown-county-list');
            var event_id  = this.getAttribute('data-id');

            $('#event-drilldown-event-list').find('.active').removeClass('active');
            $(this).addClass('active');

            $('#banner-search-event-id').val(event_id);
            $('#banner-search-event').val(this.innerText);


            $counties.find('a.disabled').removeClass('disabled');

            $.ajax({
                url    :base_url+'frontend/events/ajax_get_event_counties',
                data   : {id: event_id},
                method : 'get'
            }).done(function(result) {
                var counties   = Object.values(JSON.parse(result));
                var county_ids = [];
                var $list      = $('#event-drilldown-county-list');

                for (var i = 0; i < counties.length; i++) {
                    county_ids.push(counties[i].id);
                }

                // Disable all counties where the event does not take place
                $list.find('a').each(function() {
                    if (county_ids.indexOf(this.getAttribute('data-id')) == -1) {
                        $(this).addClass('disabled');
                    }
                });

                // For smaller screens, position the list, relative to the selected event
                if (window.innerWidth < 768)
                {
                    var selected_position = $selected.parents('li').position();
                    $list.parents('.search-drilldown-column').css('top',
                        selected_position.top +
                        $selected.parents('li').height() +
                        $('.search-drilldown-column:first').position().top
                    );

                    if (event_id)
                        $list.parents('.search-drilldown-column').show();
                    else
                        $list.parents('.search-drilldown-column').hide(); // Dismiss, if clicked when already active
                }

            });

            $('#banner-search-county').focus();
        });

        // When a county is selected...
        $('#event-drilldown-county-list').on('click', 'a', function(ev)
        {
            ev.preventDefault();

            $('#event-drilldown-county-list').find('.active').removeClass('active');
            $(this).addClass('active');

            $('#banner-search-county-id').val(this.getAttribute('data-id'));
            $('#banner-search-county').val(this.innerText);

            $('#event-drilldown').removeClass('active');

            $('#search-button').focus();
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
    }
})();