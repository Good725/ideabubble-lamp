$(document).ready(function()
{
    // Don't want anything open by default on small screens

    var accordion = $('#course_filter_accordion');
    var overlay = $('#course_filter_overlay');
    var dialogue = $('#course_filter_dialogue');
    var panels = accordion.find('> dd').hide();

    if($(window).width() > 949)
    {
        accordion.find('> dd:first-of-type').show().addClass('active');
    }


    accordion.find('> dt > a').click(function()
    {
        var target = $(this).parent().next();
        var active = target.hasClass('active');

        panels.removeClass('active').slideUp();
        if ( ! active)
        {
            target.addClass('active').slideDown();
        }

        return false;
    });

    accordion.find('input[type=checkbox]').change(function()
    {
        var checked = this.checked;
        var source_id = $(this).attr('id');
        var source_data = source_id.split('_');
        var category = source_data[1];
        var value = source_data[2];
        var name = $('label[for='+source_id+']').html();

        dialogue.find('.yes').attr('onclick', 'update_criteria(\''+category+'\', '+value+', \''+name+'\', '+checked+', 0)');
        dialogue.find('.cancel').attr('onclick', 'cancel_update_criteria(\''+source_id+'\', '+checked+', 0)');

        if ($('#cancel_filter_reminder:checked').length == 0)
        {
            overlay.show();
            dialogue.show();
        }
        else
        {
            dialogue.find('.yes').click();
        }
    });

    $('#filter_keywords').change(function()
    {
        if ($('#sorted').length == 0) {
            window.location = '/course-list.html?title='+$(this).val();
        }
        else {
            update_criteria('', '', '', '', 0);
        }
    });

    $('#sort-asc').click(function()
    {
        $('#sort-desc').removeClass('current');
        $('#sort-asc').addClass('current');
        update_criteria('', '', '', '', 0);
    });

    $('#sort-desc').click(function()
    {
        $('#sort-asc').removeClass('current');
        $('#sort-desc').addClass('current');
        update_criteria('', '', '', '', 0);
    });

    overlay.click(function(e)
    {
        if (!$(e.target).parents("#course_filter_overlay").length == 1 || $(e.target).attr('type') == 'button')
        {
            overlay.hide();
        }
    });

    $('#course_filter_reset').click(function()
    {
        var criteria = $('#course_filter_criteria');
        criteria.hide();
        criteria.find('> ul').html('');
        accordion.find('li > input[type=checkbox]').attr('checked', false);

        update_criteria('', '', '', '', '');
    });

    $('#sorted').on('click', '.course-book, .course-enquire', function(ev)
    {
        ev.preventDefault();

        var course_id = $(this).data('id');
        var event_id = $(this).parents('.contentBlock').find('.start_date :selected').data('event_id');
        var schedule_id = $(this).data("schedule");
        var valid = $('#select_schedule' + course_id).validationEngine('validate');
        if (valid) {
            var start_date_id = $('#start_date_' + course_id).val();
            var location_id = $('#location_' + course_id).val();
            var title = $(this).data("title");

            var href = "/booking-form/"+title+".html/?id="+schedule_id+'&eid='+event_id;
            window.location = "http://"+window.location.host+href;
        } else {
            setTimeout('removeBubbles()', 5000);
        }
        return false;
    });
});

function filter_offset(offset)
{
    var current = $('.filter_pagination .current');
    var current_page = current.data('page');
    var first = $('.filter_pagination > ul > li:first-child > a').data('page');
    var last  = $('.filter_pagination > ul > li:last-child > a').data('page');

    if (offset == 'prev' && current_page == first || offset == 'next' && current_page == last)
    {
        // do nothing
    }
    else
    {
        if (offset == 'prev' && current_page != first)
        {
            offset = (current_page - 2)*10;
        }
        else if (offset == 'next' && current_page != last)
        {
            offset = (current_page)*10;
        }

        update_criteria('', '', '', '', offset);
    }
}

function update_criteria(category, value, name, checked, offset)
{
    var criteria = $('#course_filter_criteria');
    if (checked)
    {
        var list_item = '<li>' +
            '<span class="remove" data-category="' + category + '" data-id="'+value+'" onclick="remove_criteria(this)">x</span> ' +
            '<span class="category">'+category+'</span>: ' + name +
            '</li>';

        criteria.find('> ul').append(list_item);
        criteria.show();
    }
    else if (category != '')
    {
        criteria.find('[data-category='+category+'][data-id='+value+']').parents('li').remove();
        if (criteria.find('> ul li').size() == 0)
        {
            criteria.hide();
        }
    }

    var keywords   = $('#filter_keywords').val();
    var sort       = $('#sorter').find('.current').data('sort');
    var locations  = $('[data-category=location]');
    var years      = $('[data-category=year]');
    var categories = $('[data-category=category]');
    var levels     = $('[data-category=level]');
    var location_ids = [], year_ids = [], category_ids = [], level_ids = [];

    $.each(locations, function(i, item){
        location_ids.push(item.getAttribute('data-id'));
    });
    $.each(years, function(i, item){
        year_ids.push(item.getAttribute('data-id'));
    });
    $.each(categories, function(i, item){
        category_ids.push(item.getAttribute('data-id'));
    });
    $.each(levels, function(i, item){
        level_ids.push(item.getAttribute('data-id'));
    });

    var reminder = 1;
    if ($('#cancel_filter_reminder:checked').length == 1)
    {
        reminder = 0;
    }

    if ($('#sorted').length == 0)
    {
        window.location = '/course-list.html?'+category+'='+value;

        $('#filter+'+category+'_'+value).check();
        criteria.find('> ul').append(list_item);
        criteria.show();
    }
    else
    {
        $.ajax(
        {
            url     : '/frontend/courses/ajax_filter_results',
            data    : {
                'location_ids' : location_ids,
                'year_ids'     : year_ids,
                'category_ids' : category_ids,
                'level_ids'    : level_ids,
                'keywords'     : keywords,
                'sort'         : sort,
                'offset'       : offset,
                'reminder'     : reminder
            },
            type     : 'post',
            dataType : 'json',
            async    : false
        }).done(function(result)
        {
            $('#sorted').html(result);
        });
    }

}

function cancel_update_criteria(box_id, checked)
{
    $('#'+box_id).prop('checked', !checked);
}

function remove_criteria(criteria)
{
    var category = $(criteria).data('category');
    var value    = $(criteria).data('id');
    $('#filter_'+category+'_'+value).click();
}