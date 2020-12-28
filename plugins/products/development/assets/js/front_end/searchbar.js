$(document).ready(function()
{
	var typing_timer;
	var $searchbar = $('.product_searchbar');

	$searchbar.on('keyup', function()
	{
		// start countdown after a key up
		clearTimeout(typing_timer);
		// searchbar_done_typing() will only get called if the user has stopped typing for a second
		typing_timer = setTimeout(searchbar_done_typing, 1000);
	});
	$searchbar.on('keydown', function()
	{
		// clear the countdown after every key down
		clearTimeout(typing_timer);
	});

	function searchbar_done_typing()
	{
		var $wrapper = $searchbar.parent();
		$wrapper.find('.search-autocomplete').remove();
        $.ajax({
            url      : '/frontend/products/ajax_search_autocomplete/?term='+$searchbar.val(),
            type     : 'post',
            dataType : 'json',
            async    : false
        }).done(function(results)
            {
                if (results.length > 0)
                {
                    var list = '<ul class="search-autocomplete">\n';
                    var previous_category = '';

                    for (var i = 0; i < results.length; i++)
                    {
                        var item = results[i];
                        if (item.category != previous_category)
                        {

                            list += '<li class="ac-category"><span class="ac-item">'+item.category+'</span></li>\n ';

                            previous_category = item.category;
                        }
                        if(item.preview_status=="1"){
                            list += '<li data-id="'+item.id+'"><span><img width="30px" height="30px"src="'+item.file_name+'" id="prodImage_0"></span><a href="'+item.link+'" class="ac-item">'+item.label+'</a></li>\n';
					    }else{
						    list += '<li data-id="'+item.id+'"><a href="'+item.link+'" class="ac-item">'+item.label+'</a></li>\n';

					    }
                    }
                    list += '</ul>';
                }
                $wrapper.append(list);
            });
	}

    // Remove the autocomplete when it is clicked away from
    $('#product_searchbar_wrapper').on('click', function(ev) {
        ev.stopPropagation();
    });
    $(document).on('click', function () {
        $('#product_searchbar_wrapper').find('.search-autocomplete').remove();
    });

    // Move from searchbar to first ac result on down arrow
    $searchbar.on('keydown', function(ev)
    {
        if (ev.keyCode == 40) // down arrow
        {
            ev.preventDefault();
            var item = $($(this).find('\+ ul li a')[0]).focus();
        }
    });

    // Events for particular key presses on autocomplete items
    $(document).on('keydown', '.ac-item:focus', function(ev)
    {
        switch (ev.keyCode)
        {
            case 13: // enter key - open (click) the link
                $(this).click();
                break;

            case 40: // down arrow - move down through the autocomplete items
                ev.preventDefault();
                var next = $(this).parent().next();
                if (next.hasClass('ac-category')) next = next.next();
                if (next.find('a.ac-item').length != 0)
                {
                    next.find('a.ac-item').focus();
                }
                break;

            case 38: // up arrow - move up through the autocomplete items
                ev.preventDefault();
                var prev = $(this).parent().prev();
                if (prev.hasClass('ac-category')) prev = prev.prev();
                if (prev.find('a.ac-item').length != 0)
                {
                    prev.find('a.ac-item').focus();
                }
                else // focus the searchbar if pressed on the top item
                {
                    $(this).parents('.product_searchbar_wrapper').find('.product_searchbar').focus();
                }
                break;

            default:
                // if another key is pressed, refocus the searchbar and type it in there
                $(this).parents('.product_searchbar_wrapper').find('.product_searchbar').focus();
                break;
        }
    });

});
