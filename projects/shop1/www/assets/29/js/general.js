$(document).ready(function()
{
	displayTotalDetails();

	$(document).foundation();

	/*------------------------------------*\
	 #Sliders
	 \*------------------------------------*/
	var swiper = null;
	if (document.getElementById('home-banner-swiper') && document.getElementById('home-banner-swiper').getAttribute('data-slides') > 1)
	{
		var $banner = $('#home-banner-swiper');

		var active_image = $banner.find('swiper-slide-active .banner-image').css('background-image');

		swiper = new Swiper('#home-banner-swiper', {
			autoplay: $banner.data('autoplay'),
			speed: $banner.data('speed'),
			effect: $banner.data('animation'),
			loop: true,
			pagination: '#home-banner-swiper .swiper-pagination',
			paginationClickable: true,
			preloadImages: true,
			nextButton: '#home-banner-swiper .swiper-button-next',
			prevButton: '#home-banner-swiper .swiper-button-prev'
		});
	}



	$("#more_events").on("click", function(ev){
		var upcoming_offset = parseInt($(this).attr('data-offset'));
		if (upcoming_offset)
		{
            $.ajax({
                url      : '/frontend/shop1/ajax_get_upcoming/upcoming_offset/',
                type     : 'post',
                data     : {upcoming_offset: upcoming_offset},
                dataType : 'json'
            }).done(function(result){
                if(result.length){
                    $.each(result, function( index, value ) {
                        $('.events_feed--upcoming').append(value);
                    });

					if(result.length < 16){
						$('#more_events').hide();
					}

                    $('#more_events').attr('data-offset', upcoming_offset + result.length);
                }else{
                    $('#more_events').hide();
                }
            });
		}

		return false;
	});

    $('.button--more_events').on('click', function()
    {
        var $button = $(this);
        var data = {
            type   : $button.data('type'),
            id     : $button.data('id'),
            offset : $button.data('offset')
        };

        $.ajax({
            url      : '/frontend/events/ajax_get_more_events/',
            type     : 'get',
            data     : data,
            dataType : 'json'
        }).done(function(result) {
            if (result.remaining <= 0) {
                $button.hide();
            }

            if (result.found && result.found > 0) {
                var offset = $button.data('offset') + result.found;
                $($button.data('feed')).append(result.html);
                $button.data('offset', offset).attr('data-offset', offset);

            }
        });

    });

	// Validate any formbuilder forms and any forms with the class "validate-on-submit"
	$('[action*="frontend/formprocessor"], .validate-on-submit').on('submit',function(ev)
	{
		if ( ! $(this).validationEngine('validate'))
		{
			return false;
		}
	});

	// Add a class to the header when the user scrolls down. This is to style the sticky header
	$(window).scroll(function() {
		var scroll = $(window).scrollTop();

		if (scroll >= 64) {
			$(".header").addClass("header--fixed");
		}
		else
		{
			$(".header").removeClass("header--fixed");
		}
	});


	/*------------------------------------*\
	 #Datepickers
	\*------------------------------------*/
	$('#home_banner-search-input').datetimepicker(
	{
		format:'d/m/Y',
		timepicker: false,
		closeOnDateSelect: true,
		minDate: 0
	});


	/*------------------------------------*\
	 #Header links
	\*------------------------------------*/
	$('#header-links-collapse').on('click', function()
	{
		var $header_links = $('#header-links');

		if ($header_links.hasClass('header-links--visible'))
		{
			$header_links.removeClass('header-links--visible');
		}
		else
		{
			$header_links.addClass('header-links--visible');
		}
	});


	/*------------------------------------*\
	 #Searchbar
	\*------------------------------------*/
	// Show / hide on button click
	$('#searchbar-button').on('click', function()
	{
		var $wrapper = $('#searchbar-wrapper');
		if ($wrapper.hasClass('shown'))
		{
			$wrapper.removeClass('shown');
		}
		else
		{
			$wrapper.addClass('shown');
			$wrapper.find('input')[0].focus();
		}
	});

    /*------------------------------------*\
     #Footer
     \*------------------------------------*/
    // Show thank you message and submit form
    $(document).on('submit','#subscription_form',function(e){
        $.ajax({
            type: "POST",
            url: '/frontend/formprocessor',
            data: $('#subscription_form').serialize(),
            success: function(data)
            {
                $('#subscription_form').find('.input-with-icon').css('visibility', 'hidden');
				$('#newsletter-button-text').html('Thank you for subscribing!');
            }
        });

        e.preventDefault();
    });

	// Display autocomplete
	var typing_timer;
	var $searchbar = $('.searchbar');
	$searchbar.on('keyup', function()
	{
		// start countdown after a key up
		clearTimeout(typing_timer);

		// searchbar_done_typing() will only get called if the user has stopped typing for half a second
		typing_timer = setTimeout(searchbar_done_typing, 500);
	});

	$searchbar.on('keydown', function()
	{
		// clear the countdown after every key down
		clearTimeout(typing_timer);
	});


	function searchbar_done_typing()
	{
		var $wrapper = $searchbar.parent();
		$wrapper.find('.searchbar-autocomplete').remove();
		if ($searchbar.val())
		{
			$.ajax({
				url      : '/frontend/events/ajax_search_autocomplete/?term='+$searchbar.val(),
				type     : 'post',
				data     : {term: $searchbar.val()},
				dataType : 'json',
				async    : false
			}).done(function(results)
				{
					if (results.length > 0)
					{
						var list = '<ul class="list-unstyled searchbar-autocomplete">\n';
						var previous_category = '';

						for (var i = 0; i < results.length; i++)
						{
							var item = results[i];
							list += '<li data-id="'+item.id+'"><a href="'+item.link+'" class="ac-item">'+item.label+'</a></li>\n';
						}
						list += '</ul>';
					}
					$wrapper.append(list);
				});
		}
	}

	// Dismiss the autocomplete when clicked away from
	$(document).click(function(event) {
		if ( ! $(event.target).closest('.searchbar-autocomplete').length) {
			$('.searchbar-autocomplete').remove();
		}
	});

	// When the user arrows down, navigate through autocomplete items
	$searchbar.on('keydown', function(ev)
	{
		if (ev.keyCode == 40) // down arrow
		{
			ev.preventDefault(); // prevent the screen scrolling down
			$('.searchbar-autocomplete a:first').focus();
		}
	});
	$(document).on('keydown', '.searchbar-autocomplete a', function(ev)
	{
		switch (ev.keyCode)
		{
			case 40: // down arrow
				ev.preventDefault();
				if ($(this).parents('li').next().length)
				{
					$(this).parents('li').next().find('a').focus();
				}
				else
				{
					// reached the bottom, go back to the searchbar
					$('.searchbar').focus();
				}
				break;

			case 38: // up arrow
				ev.preventDefault();
				if ($(this).parents('li').prev().length)
				{
					$(this).parents('li').prev().find('a').focus();
				}
				else
				{
					// reached the top, go back to the searchbar
					$('.searchbar').focus();
				}

				break;
		}
	});

    $(".button--adjust").on("click", function(){
        var qty = parseInt($(this).parent().find("input.qty").val());
        var min = parseInt($(this).parent().find("input.qty").data("min"));
        var max = parseInt($(this).parent().find("input.qty").data("max"));
        if ($(this).hasClass("minus")) {
            --qty;
        } else {
            ++qty;
        }
		if (min < 1) {
			min = 1;
		}
        if (qty < min) {
            return false;
        }
        if (qty > max) {
            return false;
        }
        $(this).parent().find("input.qty").val(qty);
        $(this).parent().find("span.qty").html(qty);

		update_checkout();

        return false;
    });


	var price_regex = /^[0-9]*\.?[0-9]{0,2}$/;

	$('.item_donation')
		.on('keydown', function() {
				if (price_regex.test(this.value)) {
					$(this).data('old_value', this.value)
				}
			})
		.on('keyup', function() {
				if ( ! price_regex.test(this.value)) {
					this.value = $(this).data('old_value');
				}
				else {
					var qty = $(this).parents('tr').find('input.qty').val();
					$(this).parents('tr').find('.item_total')
						.val(this.value * qty)
						.data('single-total', this.value)
					;
					update_checkout();
				}
			});

	function update_checkout()
	{
		var $item_total,
			$item_total_display,
			item_total_value,
			qty;
		$('#event-checkout-items').find('tr').each(function()
	    {
			$item_total         = $(this).find('.item_total');
			$item_total_display = $(this).find('.item_total-display');
			qty                 = $(this).find('input.qty').val();
			item_total_value    = parseFloat(qty * parseFloat($item_total.data("single-total"))).toFixed(2);

			$item_total.val(item_total_value);
			$item_total_display.html($item_total.data('currency') + item_total_value);
		});

		var data = $("form#checkout").serialize();
        disableScreenShow();
        $.post(
            '/checkout.html',
            data,
            function (response) {
                disableScreenHide();
                if (response.order) {
                    $("#subtotal-display").html(response.order.subtotal.toFixed(2));
                    $("#fees-display").html(response.order.commission.toFixed(2));
					$("#vat-display").html(response.order.vat.toFixed(2));
					$("#discount-display").html(response.order.discount.toFixed(2));
                    $("#total-display").html(response.order.total.toFixed(2));
					if (response.order.total == 0) {
						$(".payment-section.payment-cc").addClass("hidden");
					} else {
						$(".payment-section.payment-cc").removeClass("hidden");
					}
                }
            }
        );
	}

    function addTag(tag)
    {
        $("#search-filter-tags-list").append(
            '<li class="btn btn-default edit-event-tag">' +
            '<input type="hidden" name="tag[]" value="' + tag + '" />' +
            '<span>' + tag + '</span>' +
            '<a class="icon-times"  onclick="$(this).parent().remove()"></a>' +
            '</li>'
        );
    }

    $("#search-filter-tags").autocomplete({
        select: function(e, ui) {
            this.value = "";
            addTag(ui.item.label);
            return false;
        },

        source: function(data, callback){
            $.get("/frontend/events/autocomplete_tag_list",
                data,
                function(response){
                    callback(response);
                });
        }
    });
  
    
    
});

/*------------------------------------*\
 #Map
\*------------------------------------*/
function initMap(mapid)
{
	$(mapid).each(function(){
		var container = this;
		var map = null;
		var initX = parseFloat($(container).data("init-x"));
		var initY = parseFloat($(container).data("init-y"));
		var initZ = parseInt($(container).data("init-z"));
		var targetX = $(container).data("target-x");
		var targetY = $(container).data("target-y");
		var button = $(container).data("button");
		var showmarker = false;

		if (initX && initY) {
			showmarker = true;
		}
		if (!initX) {
			initX = 53.32693558541906;
		}
		if (!initY) {
			initY = -6.416015625;
		}
		if (!initZ) {
			initZ = 10;
		}

		var options = {
			center:new google.maps.LatLng(initX, initY),
			zoom:initZ,
			mapTypeId:google.maps.MapTypeId.ROADMAP,
			panControl:true,
			zoomControl:true,
			mapTypeControl:true,
			streetViewControl:false,
			overviewMapControl:true

		};

		map = new google.maps.Map(container, options);
		var geocoder = new google.maps.Geocoder();

		var marker = null;
		if (showmarker) {
			marker = new google.maps.Marker(
				{
					position: new google.maps.LatLng(initX, initY),
					map: map
				}
			);
		}

		var trackXY = true;
		$(targetX + "," + targetY).on("change", function(){
			if (trackXY) {
				if (marker != null) {
					marker.setMap(null);
				}
				var lat = parseFloat($(targetX).val());
				var lng = parseFloat($(targetY).val());
				map.setCenter(new google.maps.LatLng(lat, lng));
				marker = new google.maps.Marker(
					{
						position: new google.maps.LatLng(lat, lng),
						map: map
					}
				);
			}
		});
		if (button) {
			$(button).on("click", function(){
				var address1 = $("[name=address1]").val();
				var address2 = $("[name=address2]").val();
				var country = $("[name=country_id] option:selected").text();
				var county = $("[name=county_id] option:selected").text();
				var city = $("[name=city]").val();
				var postcode = $("[name=postcode], [name=eircode]").val();

				geocoder.geocode(
					{
						//'address': address1 + " " + address2 + " " + county + " " + city + " " + postcode + " " + country
						'address': address1 + " " + address2 + " " + county + " " + city + " " + country
					},
					function(results, status) {
						if (status === google.maps.GeocoderStatus.OK) {
							if (marker != null) {
								marker.setMap(null);
							}
							map.setCenter(results[0].geometry.location);
							marker = new google.maps.Marker({
								map: map,
								position: results[0].geometry.location
							});
							trackXY = false;
							$(targetX).val(results[0].geometry.location.lat());
							$(targetY).val(results[0].geometry.location.lng());
							trackXY = true;
						} else {
							alert('Geocode was not successful for the following reason: ' + status);
						}
					}
				);


			});
		}

	});
}

function round2(price)
{
	price *= 100;
	price = Math.floor(price);
	price /= 100;
	return price;
}

function displayTotalDetails()
{
	/*
	$('.checkout_form').each(function()
	{
		var $commission=$(this).find('.ticket-commission');
		var type = $commission.data("commission-type");
		var fixedChargeAmount = parseFloat($commission.data("fixed-charge-amount"));
		var commissionAmount = parseFloat($commission.data("commission-amount"));
		var vatRate = parseFloat($commission.data("vat-rate"));
		var vat = 0;
		var calculatedCommission = 0;
		$(this).find('.final_price').each(function(){
			if($(this).data('available') > 0){

				if ($(this).data('donation') == 1) {
					$(this).next('.final_price_value').html('&nbsp;');
				}
				else {
					var price = parseFloat($(this).data("price")) || 0;
					if ($(this).data("include-commission") == 1 || $(this).data('free') == 1 || $(this).data('donation') == 1) {
						total = price;
					} else {
						if (type == 'Fixed') {
							calculatedCommission = commissionAmount;
						} else {
							calculatedCommission = round2(price * (commissionAmount / 100));
						}
						var total = 0;
						vat = round2((calculatedCommission + fixedChargeAmount) * vatRate); 
						total = price + fixedChargeAmount + calculatedCommission + vat;
					}
					total=total.toFixed(2);
					var currency = $(this).next('.final_price_value').data("currency");
					var sym = window.currencies[currency].symbol;
					$(this).next('.final_price_value').html(sym + total);
				}
			}
		});
	});
	*/
}
