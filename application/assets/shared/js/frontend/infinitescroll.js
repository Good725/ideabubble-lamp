(function($)
{
	$.fn.infinite_scroll = function(params)
	{
		var $feed = this;


		// check if the feed exists
		if ($feed[0])
		{
			var footer_selector    = params.footer        ? params.footer        : '#footer';
			var feed_item_selector = params.feed_item     ? params.feed_item     : '';
			var ajax_url           = params.ajax_url      ? params.ajax_url      : '';
			var custom_params      = params.custom_params ? params.custom_params : {};

			// This is to stop the code trying to fetch another row, before it is finished fetching the current row
			var currently_fetching = false;

			// Add some items after the initial page load, if there is room
			fetch_infinite_scroll_items();

			// Add items, as the user scrolls
			document.addEventListener('scroll', function()
			{
				fetch_infinite_scroll_items();
			});
		}

        var previous_results = null;

		function fetch_infinite_scroll_items()
		{
			// If the footer or bottom of the feed is in view, load more content
			var scrolling_distance     = document.body.scrollHeight - window.innerHeight - window.scrollY;
			var footer_in_view         = (scrolling_distance <= $(footer_selector).height());
			var bottom_of_feed_in_view = (window.innerHeight + window.scrollY > $feed.offset().top + $feed.height());

			if ( ! currently_fetching && (footer_in_view || bottom_of_feed_in_view))
			{
				currently_fetching   = true;
				var currently_loaded = $(feed_item_selector);

				// find the number of news items per row
				var per_row = 0, same_row = true;
				for (var i = 0; i < currently_loaded.length && same_row; i++)
				{
					// If this is the first item or in the same vertical position as the previous item, it is on the same row, so increase the counter
					same_row = (i == 0 || $(currently_loaded[i]).position().top == $(currently_loaded[i]).prev().position().top);
					if (same_row) per_row++;
				}

				// If the row is incomplete, load enough to fill the row, otherwise load an entire row
				var amount_to_load = per_row - (currently_loaded.length % per_row);

				// console.log('Currently loaded '+currently_loaded.length, 'fetching up to '+amount_to_load+' more...');

				var data = {offset: currently_loaded.length, amount: amount_to_load};

				// Merge custom parameters into the object to be sent to the AJAX function
				for (var attrname in custom_params) { data[attrname] = custom_params[attrname]; }

                $.ajax({url: ajax_url, data: data}).done(function(results) {
                    if (results && results != previous_results) {
                        $(results).hide().appendTo($feed).fadeIn(1000, function() {
                            currently_fetching = false;
                            previous_results = results;
                            setTimeout(fetch_infinite_scroll_items, 2000); // If there's room for another row, recurse
                        });
                    }
                });
			}
		}

	};
})(jQuery);
