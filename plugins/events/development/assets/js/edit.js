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
        var buttonTarget = $(container).data("button-target");
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


		var searchBox = new google.maps.places.SearchBox(document.getElementById('edit-event-map-search'));
		// Bias the SearchBox results towards current map's viewport.
		map.addListener('bounds_changed', function() {
			searchBox.setBounds(map.getBounds());
		});

		var markers = [];
		// Listen for the event fired when the user selects a prediction and retrieve
		// more details for that place.
		searchBox.addListener('places_changed', function() {
			var places = searchBox.getPlaces();

			if (places.length == 0) {
				return;
			}

			// Clear out the old markers.
			markers.forEach(function(marker) {
				marker.setMap(null);
			});
			markers = [];

			// For each place, get the icon, name and location.
			var bounds = new google.maps.LatLngBounds();
			places.forEach(function(place) {
				var icon = {
					url: place.icon,
					size: new google.maps.Size(71, 71),
					origin: new google.maps.Point(0, 0),
					anchor: new google.maps.Point(17, 34),
					scaledSize: new google.maps.Size(25, 25)
				};

				// Create a marker for each place.
				markers.push(new google.maps.Marker({
					map: map,
					icon: icon,
					title: place.name,
					position: place.geometry.location
				}));

				if (place.geometry.viewport) {
					// Only geocodes have viewport.
					bounds.union(place.geometry.viewport);
				} else {
					bounds.extend(place.geometry.location);
				}
			});
			map.fitBounds(bounds);

			if (places[0])
			{
				$('#edit-event-venue-lat').val(places[0].geometry.location.lat());
				$('#edit-event-venue-lng').val(places[0].geometry.location.lng());
			}
		});

        google.maps.event.addListener(map, 'click', function(event) {
            map.setCenter(event.latLng);
            if (marker != null) {
                marker.setMap(null);
            }
            marker = new google.maps.Marker(
                {
                    position: new google.maps.LatLng(event.latLng.lat(), event.latLng.lng()),
                    map: map
                }
            );

            $(targetX).val(event.latLng.lat());
            $(targetY).val(event.latLng.lng());

            $('#edit-event-venue-lat').val(event.latLng.lat());
            $('#edit-event-venue-lng').val(event.latLng.lng());
        });

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

		$(searchBox).on('keydown', function() {
			google.maps.event.trigger(searchBox, 'places_changed');
		});

        if (button) {
            $(button).on("click", function(){
                var venue    = $("#edit-event-venue-name").val();
				var address1 = $("#edit-event-venue-address_1").val();
				var address2 = $("#edit-event-venue-address_2").val();
				var address3 = $("#edit-event-venue-city").val();
                var country  = $("#edit-event-venue-country option:selected").text();
                var county   = $("#edit-event-venue-county option:selected").text();
                var postcode = $("[name=postcode], [name=eircode]").val();
				var address  = (venue + " " + address1 + " " + address2 + " " + address3 + " " + county + " " + country).trim();

				// Update the hidden search bar field
				$('#edit-event-map-search').val(address).trigger('change');

				// Trigger a change
                try {
                    var input = document.getElementById('edit-event-map-search');
                    google.maps.event.trigger(input, 'focus');
                    google.maps.event.trigger(input, 'keydown', {keyCode: 13});
                } catch (e) {

                }

                geocoder.geocode(
                    {
                        'address': address
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


                /*
                 //for latlng to address
                 if (marker) {
                 console.log(marker.position);
                 $.get(
                 "http://maps.googleapis.com/maps/api/geocode/json?latlng=" + marker.position.lat() + "," + marker.position.lng(),
                 function(response){
                 if (response.status && response.status == "OK") {
                 for (var i = 0 ; i < response.results.length ; ++i) {
                 var code = null;
                 for (var j = 0 ; j < response.results[i].address_components.length ; ++j) {
                 var pindex = response.results[i].address_components[j].types.indexOf("postal_code");
                 if (pindex != -1) {
                 code = response.results[i].address_components[j].long_name;
                 }
                 }
                 if (code) {
                 $(buttonTarget).val(code);
                 } else {
                 $(buttonTarget).val(response.results[i].formatted_address);
                 }

                 break;
                 }
                 }
                 }
                 );
                 }*/


            });
        }

    });

	$('#edit-event-location-reset-button').on('click', function()
	{
		$('#edit-event-location-fields').find(':input:not([type="hidden"])').val('').trigger('change');
	});
}


$(document).on("ready", function()
{
    // for some reason ready event is happening twice.
    // this is to prevent running this code second time
    if (window.doc_ready_called) {
        return;
    } else {
        window.doc_ready_called = true;
    }

    $("[type=checkbox][name=has_payment_plan]").on("change", function(){
        if (this.value == 1) {
            if (this.checked) {
                $("#event-payment-plan table").removeClass("hidden");
            } else {
                $("#event-payment-plan table").addClass("hidden");
            }
        }
    });

    var $payment_plan_stage_template = $(".payment-plan-stage");
    $payment_plan_stage_template.remove();

    function add_payment_plan_stage(tickettype_index, data)
    {
        if (!tickettype_index) {
            tickettype_index = 0;
        }
        var $table = $('.ticket-template.details.row-' + tickettype_index).find('table');
        var $payment_plan_stage = $payment_plan_stage_template.clone();
        $payment_plan_stage.removeClass("hidden");
        var index = $table.find('tbody tr').length;
        $payment_plan_stage.find("input, select").each(function(){
            this.name = this.name.replace("ticket_paymentplan[ticket_index][index]", "ticket_paymentplan[" + tickettype_index + "][" + index + "]");
        });
        if (data) {
            $payment_plan_stage.find(".pp-id").val(data.id);
            $payment_plan_stage.find(".pp-tickettype_id").val(data.tickettype_id);
            $payment_plan_stage.find(".pp-title").val(data.title);
            $payment_plan_stage.find(".pp-type").val(data.payment_type);
            $payment_plan_stage.find(".pp-amount").val(data.payment_amount);
            $payment_plan_stage.find(".pp-due_date").val(data.due_date);
        }
        $payment_plan_stage.find('.pp-due_date').datetimepicker({
            defaultDate: new Date(),
            format:'Y-m-d',
            step: 15,
            closeOnDateSelect: true
        });

        $table.find('tbody').append($payment_plan_stage);

        $table.find('tbody tr:first-child .pp-due_date').prop('disabled', true).addClass('hidden');
    }

    $(document).on("click", ".paymentplan .add", function(){
        var tickettype_index = Math.floor($(this).parents(".ticket-template.details").index() / 2);
        add_payment_plan_stage(tickettype_index);
    });

    $(document).on("click", ".paymentplan .remove", function() {
        var $table = $(this).parents('table'); // .parents won't work after the row has been removed
        // Remove this row
        $(this).parents('.payment-plan-stage').remove();
        // Make sure there is no "due date" field in the first row
        $table.find('tbody tr:first-child .pp-due_date').remove();
    });

	// Ensure the quantity fields are formatted "number remaining / total" as the user changes the total figure
	$(document).on('change keyup', '.quantity-field :input', function(ev)
	{
		var total     = parseInt(this.value || 0);
		var sold      = [];
		var remaining = [];
        if (this.dates_quantity_remaining) {
            for (var i in this.dates_quantity_remaining) {
                sold.push(this.dates_quantity_remaining[i].sold);
                remaining.push(total - this.dates_quantity_remaining[i].sold);
                if (parseInt($("[name=one_ticket_for_all_dates]").val()) == 1) {
                    break;
                }
            }
            sold = sold.join(';');
            remaining = remaining.join(';');
        } else {
            sold = 0;
            remaining = total;
        }

		// The total cannot be less than the amount sold
		if (ev.type == 'change' && remaining < 0)
		{
			remaining  = 0;
			this.value = sold;
		}

		var mask = this.value == '' ? '' : remaining + ' / ';
		$(this).parents('.quantity-field').find('.quantity-field-mask').html(mask);
		// Indent by the length of the number remaining and a forward slash
		this.style.textIndent = (''+remaining).length + 2 + 'ex';
	});

    function addNewVideo(url)
    {
        if (url.trim() && url.indexOf('http://') == -1 && url.indexOf('https://') == -1) {
            url = 'https://' + url;
        }
        if (url === 'http://')
            url = 'https://';
        $("#event-videos").append(
			'<div class="form-group"><div class="col-sm-12">' +
            '<input type="text" id="video-event" class="form-control" name="videos[]" value="'+url+'" placeholder="http://" />' +
			'</div></div>'
        );
    }

	// Add new video by hitting "Enter" after typing the URL
    $("#edit-event-video-url-new").on("keypress", function (e){
        if (e.keyCode == 13) { //enter pressed
            addNewVideo(this.value);
            this.value = "";
            $("#event-view-new-hide-btn").click();
            return false;
        }
    });

	// Add new video by pressing the button after typing the URL
	$('#edit-event-add-video-btn').on('click', function()
	{
		var url = document.getElementById('edit-event-video-url-new').value;
		addNewVideo(url);
		$("#event-view-new-hide-btn").click();
	});

    function addTag(tag)
    {
		var tag_ar = tag.split(',');
            tag_ar.forEach( function(s) {
				if(s.trim()){ 
					$("#edit-event-tags-list").append(
					'<li class="btn btn-default edit-event-tag">' +
						'<input type="hidden" name="has_tag[]" value="' + s + '" />' +
						'<span>' + s + '</span>' +
						'<a class="icon-times"  onclick="$(this).parent().remove()"></a>' +
					'</li>'
					);
			    }
            }); 
    }
    $("#edit-event-add_tag").autocomplete({
        select: function(e, ui) {
            this.value = "";
            addTag(ui.item.label);
            return false;
        },

        source: function(data, callback){
            $.get("/admin/events/autocomplete_tag_list",
                data,
                function(response){
                    callback(response);
                });
        }
    });

    function organizer_primary_check_handler()
    {
        var check = this;
        if (check.checked) {
            $(".organiser_checkbox").each(function () {
                if (check != this) {
                    this.checked = false;
                }
            });
        }
    }
    $(".organiser_checkbox").on("change", organizer_primary_check_handler);
    var $organizerTemplate = $('#add-another-organizer-template').clone();
    $organizerTemplate.remove();// temove the template from dom, though keep it for cloning later on button click

    $('#add-another-organizer-btn').on('click', function()
	{
		// Clone the template
		var $clone = $organizerTemplate.clone();

        //uncheck others so that there will be always just one primary
        $clone.find(".organiser_checkbox").on("change", organizer_primary_check_handler);

		// IDs must be unique
		$clone.removeAttr('id');
		var index = $('.edit-event-additional-organizer').length + 1;
		$clone.find('[id]').each(function() {
			this.setAttribute('id', this.getAttribute('id').replace('-template', '-'+index));
		});
        $clone.find('.organizer-toggle').data("target", "#organizer-toggle-" + index);
        $clone.find('input').each(function() {
            this.setAttribute('name', this.getAttribute('name').replace('[-1]', '[' + index + ']'));
        });
		$clone.find('[data-target]').each(function() {
			this.setAttribute('data-target', this.getAttribute('data-target').replace('-template', '-'+index));
		});

		// Fields are disabled, while in the template. Enable them for the clone.
		$clone.find(':disabled, [disabled]').prop('disabled', false);

		// Add the clone to the DOM
		$('#edit-event-additional-organizers').append($clone);

		// Add the autocomplete to the name input
		$clone.find('[name$="[name]"]').event_organizer_autocomplete();

		// Make the clone visible after everything is set up
		$clone.removeClass('hidden');

        $clone.find("input[type=text]:nth-child(1)").focus();

        uploader_ready();// this is to initializer new image uploaders
    });

	// Populate organiser fields, after a name has been selected from the autocomplete
	(function($)
	{
		$.fn.event_organizer_autocomplete = function()
		{
			$(this).autocomplete({
				select: function(e, ui) {
					$(this).val(ui.item.label);
					var $section = $(this).parents('.edit-event-additional-organizer, .edit-event-organizer-section');
					$section.find('[name$="[contact_id]"]').val(ui.item.value);
					$section.find('[name$="[telephone]"]').val(ui.item.phone);
					$section.find('[name$="[mobile]"]').val(ui.item.mobile);
                    $section.find('[name$="[email]"]').val(ui.item.email);
					$section.find('[name$="[url]"]').val(ui.item.url);
					$section.find('[name$="[website]"]').val(ui.item.website);
					$section.find('[name$="[facebook_url]"]').val(ui.item.facebook);
					$section.find('[name$="[twitter_url]"]').val(ui.item.twitter);
                    $section.find('[name$="[snapchat_url]"]').val(ui.item.snapchat);
                    $section.find('[name$="[instagram_url]"]').val(ui.item.instagram);
                    if (ui.item.profile_media_id) {
                        $section.find('[name$="[profile_media_id]"]').val(ui.item.profile_media_id);
                        $section.find('.organizer_profile_image_saved').attr("src", ui.item.profile_media_url);
                        $section.find('.organizer_profile_image_saved').parents(".form-group").find("button, img").removeClass("hidden");
                    }
                    if (ui.item.banner_media_id) {
                        $section.find('[name$="[banner_media_id]"]').val(ui.item.banner_media_id);
                        $section.find('.organiser_banner_image_saved').attr("src", ui.item.banner_media_url);
                        $section.find('.organiser_banner_image_saved').parents(".form-group").find("button, img").removeClass("hidden");
                    }
					return false;
				},

				source: function(data, callback){
                    $(this).parents(".edit-event-organizer-section").find("[name*='[contact_id]']").val("");
					data.list = 'Event Organizer';
					$.get("/admin/events/autocomplete_organiser_list",
						data,
						function(response){
							callback(response);
						});
				}
			});
		};
	})(jQuery);

    $("#edit-event-organizer-contact-name").event_organizer_autocomplete();
    $("#edit-event-organizer-email").on("change", function(){
        var that = this;
        var $contact_id = $(that).parents(".edit-event-organizer-section").find("[name*='[contact_id]']");
        if ($contact_id.val() == "") {
            $.post(
                "/admin/contacts2/test_email",
                {email: that.value},
                function (response) {
                    if (response && response.length > 0) {
                        $contact_id.val(response[0].id);
                        $(that).parents(".edit-event-organizer-section").find("[name*='[name]']").val(response[0].first_name + " " + response[0].last_name);
                    }
                }
            );
        }
    });

    $("#new-contact-dialog .btn.add").on("click", function(){
        var element = $("#new-contact-dialog")[0].addtoelement;
        var index = $(element).index();
        var firstname = $("#add-new-contact-first_name").val();
        var lastname = $("#add-new-contact-last_name").val();
        var email = $("#add-new-contact-email").val();
        var phone = $("#add-new-contact-phone").val();
        var mobile = $("#add-new-contact-mobile").val();
        var url = $("#add-new-contact-url").val();
        var twitter = $("#add-new-contact-twitter").val();
        var facebook = $("#add-new-contact-facebook").val();
        var linkedin = $("#add-new-contact-linkedin").val();
        $(element).find("input[type=text]").val(firstname + " " + lastname);
        $(element).find(".new-contact").remove();
        $(element).append(
            '<input class="new-contact" type="hidden" name="has_organizer[firstname][' + index + ']" value="' + firstname + '" />' +
            '<input class="new-contact" type="hidden" name="has_organizer[lastname][' + index + ']" value="' + lastname + '" />' +
            '<input class="new-contact" type="hidden" name="has_organizer[email][' + index + ']" value="' + email + '" />' +
            '<input class="new-contact" type="hidden" name="has_organizer[phone][' + index + ']" value="' + phone + '" />' +
            '<input class="new-contact" type="hidden" name="has_organizer[mobile][' + index + ']" value="' + mobile + '" />' +
            '<input class="new-contact" type="hidden" name="has_organizer[url][' + index + ']" value="' + url + '" />' +
            '<input class="new-contact" type="hidden" name="has_organizer[twitter][' + index + ']" value="' + twitter + '" />' +
            '<input class="new-contact" type="hidden" name="has_organizer[facebook][' + index + ']" value="' + facebook + '" />' +
            '<input class="new-contact" type="hidden" name="has_organizer[linkedin][' + index + ']" value="' + linkedin + '" />'
        );

        $("#add-new-contact-first_name").val("");
        $("#add-new-contact-last_name").val("");
        $("#add-new-contact-email").val("");
        $("#add-new-contact-phone").val("");
        $("#add-new-contact-mobile").val("");
        $("#add-new-contact-url").val("");
        $("#add-new-contact-twitter").val("");
        $("#add-new-contact-facebook").val("");
        $("#add-new-contact-linkedin").val("");
    });

    var $ticketTemplate2 = $(".ticket-template");
    $ticketTemplate2.remove();
    var ticketRows = 0;
    function validateTotalCapacity()
    {
        var total = 0;
        $(".ticket-quantity").each(function(){
            var quantity = parseInt(this.value);
            if (quantity) {
                total += quantity;
            }
        });

        $("#edit-event-ticket-quantity").val(total);
    }
    function addTicketType2(type, data)
    {
        var $ticket = $ticketTemplate2.clone();
        var $basic = $($ticket[0]);
        var $details = $($ticket[1]);
        $ticket.row = ticketRows;
        $basic.addClass("row-" + ticketRows);
        $details.addClass("row-" + ticketRows);
        $basic.find(".edit-event-ticket-settings-icon").on("click", function(){
            if ($details.hasClass("hidden")) {
                $details.removeClass("hidden");
                $basic.addClass("greyed");
                $details.addClass("greyed");
            } else {
                $details.addClass("hidden");
                $basic.removeClass("greyed");
                $details.removeClass("greyed");
            }
        });

        $basic.find(".ticket-name").addClass("validate[required]");
        $basic.find(".ticket-name").on("change", function(){
            $details.find(".ticket-name").val(this.value);
        });

        $basic.find(".ticket-quantity").addClass("validate[required]");
        $basic.find(".ticket-quantity").on("change", function(){
            $details.find(".ticket-quantity").val(this.value).trigger('change');
        });

        $basic.find(".type").addClass("hidden");
        $basic.find(".type." + type).removeClass("hidden");
        $details.find(".ticket-type").val(type);

        if (type == 'Paid' || type == 'Donation') {
            $("#payment-details").removeClass("hidden");
        }
        if (type == 'Paid') {
            $details.find("input.price").prop("readOnly", false);
            $details.find("input.price").val("");
            $details.find(".total").show();
        } else {
            $details.find("input.price").prop("readOnly", true);
            $details.find("input.price").val(this.value);
            $details.find(".total").hide();
        }

        if (type == 'Free') {
            $details.find(".commission").hide();
        } else {
            $details.find(".commission").show();
            $details.find("[data-commission]").val($ticket.find("[data-commission]").data("commission"));
        }

        $basic.find(".ticket-quantity").on("change", validateTotalCapacity);

        function round(price)
        {
            // similar to php round($val, 2)
            price *= 100;
            price = Math.floor(price);
            price /= 100;
            return price;
        }

        function solveBasePriceFeesIncluded(total, commission, vatRate)
        {
            var divider = 1.0;
            var fixedValues = 0;

            fixedValues += commission['fixed_charge_amount'];
            fixedValues += commission['fixed_charge_amount'] * vatRate;

            if (commission['type'] == 'Fixed') {
                fixedValues += commission['amount'] + (commission['amount'] * vatRate);
            } else {
                divider += (commission['amount'] / 100.0) + ((commission['amount'] / 100.0) * vatRate);
            }

            var price = round((total - fixedValues) / divider);

            // handle rounding errors like 0.01 difference

            var isubtotal = price;
            var commission_total = commission['type'] == 'Fixed' ?
                commission['amount']
                :
                round((commission['amount'] / 100) * isubtotal);
            commission_total += commission['fixed_charge_amount'];
            isubtotal += commission_total;
            var vat = round(commission_total * vatRate);
            isubtotal += vat;

            var diff = round(total - isubtotal);
            if (diff == 0.01) {
                price += diff;
            }

            return price;
        }

        function displayTotalDetails()
        {
            $basic.find(".price-display-calculation").html('updating...');
            var $commission = $details.find(".ticket-commission");
            var type = $("#commission_type").val() != "" ? $("#commission_type").val() : window.commission.type;
            var fixedChargeAmount = $("#commission_fixed_amount").val() != "" ? parseFloat($("#commission_fixed_amount").val()) : parseFloat(window.commission.fixed_charge_amount);
            var commissionAmount = $("#commission_amount").val() != "" ? parseFloat($("#commission_amount").val()) : parseFloat(window.commission.amount);
            var price = parseFloat($basic.find(".ticket-price").val()) || 0;
            var vatRate = parseFloat(window.vatRate);
            var vat = 0;
            var calculatedCommission = 0;
            var currency = $("#edit_event-ticket-currency").val();
            var sym = window.currencies[currency].symbol;
    		currency = '<span class="currency-sym">' + sym + '</span>';
			var total;

            $.get(
                '/frontend/events/calculate_price_breakdown',
                {
                    'price': price,
                    'fee_fixed' : fixedChargeAmount + (type == 'Fixed' ? commissionAmount : 0),
                    'fee_percent': type == 'Fixed' ? 0 : commissionAmount,
                    'vat_rate' : vatRate,
                    'absorb_fee' : $details.find(".ticket-include_commission").val() == 1 ? 1 : 0
                },
                function (result) {
                    var base_price = result.base_price;

                    var calculatedCommission = result.fee;
                    var vat = result.vat;
                    var total = result.total;

                    $details.find(".ticket-total").val(total.toFixed(2));

                    var calcdetails = '';
                    $details.find("[name*=ticket_channel]").each(function(){
                        if (this.checked) {
                            calcdetails += this.value + ':';
                        }
                    });

                    var breakdown_string = (type == 'Fixed') ? currency + (commissionAmount.toFixed(2)) : commissionAmount + '%';
                    breakdown_string += (fixedChargeAmount ? ' + ' + currency + fixedChargeAmount : '');
                    breakdown_string += (vatRate) ? ' + VAT' : '';

                    var $breakdown = $('#ticket-price-breakdown-template').clone();
                    $breakdown.removeAttr('id');

                    $breakdown.find('.ticket-price-breakdown-base_price').html(currency+(price.toFixed(2)));
                    $breakdown.find('.ticket-price-breakdown-fee').html(currency+((calculatedCommission + vat).toFixed(2)));
                    $breakdown.find('.ticket-price-breakdown-fee_breakdown').html(breakdown_string);
                    $breakdown.find('.ticket-price-breakdown-final_price').html(currency+(total.toFixed(2)));

                    if ($details.find(".ticket-include_commission").val() == 1)
                    {
                        $breakdown.find('.ticket-price-breakdown-back_to_org').html(currency+base_price+' ');
                    }
                    else
                    {
                        $breakdown.find('.ticket-price-breakdown-back_to_org').html(currency+(base_price)+' ');
                    }

                    calcdetails += '<table>'+$breakdown.html()+'</table>';
                    $basic.find(".price-display-calculation")
                        .attr('data-content', calcdetails)
                        .data('content', calcdetails)
                        .html(currency+(total.toFixed(2)));

                    $basic.find(".price-display-calculation").popover({content: function() { return $(this).data('content') }});
                }
            );
        }
        $basic.find(".ticket-price").on("change", displayTotalDetails);
        $details.find(".ticket-include_commission").on("change", displayTotalDetails);

        $details.find(".datetime-picker").datetimepicker({
            defaultDate: new Date(),
            format:'Y-m-d H:i',
			step: 15,
            closeOnDateSelect: true
        });
        $details.find(".datetime-picker_no-auto-close").datetimepicker({
            defaultDate: new Date(),
            format:'Y-m-d H:i',
			step: 15
        });
        $details.find(".ticket-visible").on("change", function(){
            if ($details.find(".ticket-visible.yes").prop("checked")) {
                $details.find(".tthide").hide();
            } else {
                $details.find(".tthide").show();
            }
        });

        // Make the IDs unique
        var count = ticketRows;
        $basic.find('[id]').each(function() {
            $(this).attr('id', $(this).attr('id').replace('_0', '_'+count));
        });
        $basic.find('[for]').each(function() {
            $(this).attr('for', $(this).attr('for').replace('_0', '_'+count));
        });
        $basic.find('input,select,textarea').each(function(){
            if (this.name.indexOf('[]') != -1) {
                this.name = this.name.replace('[]', '[' + count + ']');
            }
        });
        $details.find('[id]').each(function() {
            $(this).attr('id', $(this).attr('id').replace('_0', '_'+count));
        });
        $details.find('[for]').each(function() {
            $(this).attr('for', $(this).attr('for').replace('_0', '_'+count));
        });
        $details.find('input,select,textarea').each(function(){
            if (this.name.indexOf('[]') != -1) {
                this.name = this.name.replace('[]', '[' + count + ']');
            }
        });

        // fill data if set
        if (data) {
            $basic.find(".ticket-archived").val(data.archived);
            $basic.find(".edit-event-ticket-delete-icon").addClass("hidden");
            $basic.find(".edit-event-ticket-archive-icon").addClass("hidden");
            $basic.find(".edit-event-ticket-unarchive-icon").addClass("hidden");
            if (parseInt(data.archived) > 0) {
                $basic.find(".edit-event-ticket-unarchive-icon").removeClass("hidden");
            } else if (parseInt(data.ordered) > 0) {
                $basic.find(".edit-event-ticket-archive-icon").removeClass("hidden");
            } else {
                $basic.find(".edit-event-ticket-delete-icon").removeClass("hidden");
            }
            $details.find(".ticket-type-id").val(data.id);
            $details.find(".ticket-type").val(data.type);
            $details.find(".ticket-type").change();
            $details.find(".ticket-name").val(data.name);
            $basic.find(".ticket-name").val(data.name);
            $details.find(".price").val(data.price);
            $basic.find(".ticket-price").val(data.price);
			$details.find(".ticket-description").val(data.description);
			$details.find(".ticket-show_description").prop('checked', (data.show_description == "1"));
			$basic.find(".ticket-quantity")[0].dates_quantity_remaining = data.dates_quantity_remaining;
            $basic.find(".ticket-quantity").val(data.quantity).trigger('change');
            $details.find(".ticket-sale_starts").val(data.sale_starts);
            $details.find(".ticket-sale_ends").val(data.sale_ends);
            if (data.visible == 1) {
                $details.find(".ticket-visible.yes").prop("checked", true);
                $details.find(".ticket-visible.yes").parent().addClass("active");
                $details.find(".ticket-visible.no").parent().removeClass("active");
            } else {
                $details.find(".ticket-visible.no").prop("checked", true);
                $details.find(".ticket-visible.yes").parent().removeClass("active");
                $details.find(".ticket-visible.no").parent().addClass("active");
            }
            $details.find(".ticket-hide_before").val(data.hide_before);
            $details.find(".ticket-hide_after").val(data.hide_after);
            $details.find(".ticket-max_per_order").val(data.max_per_order);
            $details.find(".ticket-min_per_order").val(data.min_per_order);

            if (data.sold > 0) {
                $details.find(".btn.remove").prop("disabled", true);
                $details.find(".ticket-ttsold").val(data.sold);
                $details.find(".ttsold").show();
            } else {
                $details.find(".ttsold").hide();
            }

            $details.find(".ticket-include_commission").val(data.include_commission);
            $details.find(".ticket-include_commission").change();

            $details.find(".ticket-sleep_capacity").val(data.sleep_capacity);
        } else {
            $basic.find(".edit-event-ticket-delete-icon").removeClass("hidden");
        }

        $details.find(".ticket-visible").change();
		$ticket.hide();
        $("#edit-event-tickets-list").append($ticket);
		$ticket.find('.ticket-price').trigger('change');
		$ticket.fadeIn();

		// Ensure the column headings are visible when there are rows
		$('#edit-event-ticket-table').find('thead').removeClass('hidden');

        if (data)
        if (data.paymentplan) {
            for (var p in data.paymentplan) {
                add_payment_plan_stage(ticketRows, data.paymentplan[p]);
            }
        }

        ++ticketRows;

		$('#edit-event-ticket-table').find('.ticket-quantity').trigger('change');
    }

    $("#commission_fixed_amount, #commission_amount, #commission_type").on("change", function(){
        $(".ticket-price").change();
    });

	$(document).on('click', '.hide-ticket-settings', function() {
		$(this).parents('.details').prev().find('.edit-event-ticket-settings-icon').trigger('click');
		document.getElementById('create-tickets-section').scrollIntoView();
	});

    $('#edit-event-ticket-buttons').find('button').on("click", function(){
		$('#create-tickets-error-area').html('');
        var type = $(this).data("type");
        addTicketType2(type);
    });

    // archive a ticket
    $('#edit-event-tickets-list').on('click', '.edit-event-ticket-archive-icon', function()
    {
        $(this).parents('.ticket-template').find(".ticket-archived").val(1);
        $(this).parents('.ticket-template').find(".edit-event-ticket-archive-icon").addClass("hidden");
        $(this).parents('.ticket-template').find(".edit-event-ticket-unarchive-icon").removeClass("hidden");
    });

    // unarchive a ticket
    $('#edit-event-tickets-list').on('click', '.edit-event-ticket-unarchive-icon', function()
    {
        $(this).parents('.ticket-template').find(".ticket-archived").val(0);
        $(this).parents('.ticket-template').find(".edit-event-ticket-archive-icon").removeClass("hidden");
        $(this).parents('.ticket-template').find(".edit-event-ticket-unarchive-icon").addClass("hidden");
    });

	// Delete a ticket
	$('#edit-event-tickets-list').on('click', '.edit-event-ticket-delete-icon', function()
	{
		// Get the row class of the ticket and pass it to the modal
		var row_class = $(this).parents('.ticket-template').attr('class').match(/row-\d+/);
		$('#edit-event-ticket-delete-modal-btn').attr('data-row_class', row_class);

		// Show the modal box
		$('#edit-event-ticket-delete-modal').modal();
	});

	$('#edit-event-ticket-delete-modal-btn').on('click', function()
	{
        var row_class = $(this).attr('data-row_class');

		// Remove all rows with that class
		$('.ticket-template.'+row_class).remove();

		// Hide the column headings if there are no rows
		if ($('#edit-event-tickets-list').find('tr').length == 0)
		{
			$('#edit-event-ticket-table').find('thead').addClass('hidden');
		}

		// Dismiss the modal
		$('#edit-event-ticket-delete-modal').modal('hide');

        var new_total = 0;
        $("#edit-event-tickets-list tr .ticket-quantity").each(function(){
            if (parseInt(this.value)) {
                new_total += parseInt(this.value);
            }
        });
        var old_total = parseInt($("#edit-event-ticket-quantity").val());
        if (old_total > new_total) {
            $("#edit-event-ticket-quantity").val(new_total);
        }
    });

    var discountTemplate = $(".discount-row-template").first();
    discountTemplate.remove();

    var $discount_starts = $('#edit-event-discount-starts');
    var $discount_ends   = $('#edit-event-discount-ends');

    $discount_starts.datetimepicker({
        defaultDate: new Date(),
        format: 'Y-m-d H:i',
        formatTime: 'H:i',
        formatDate: 'Y-m-d H:i',
        step: 15,
        onShow: function () {
            this.setOptions({
                maxDate: $discount_ends.val() ? $discount_ends.val() : false
            })
        }
    });

    $discount_ends.datetimepicker({
        format: 'Y-m-d H:i',
        formatTime: 'H:i',
        formatDate: 'Y-m-d H:i',
        step: 15,
        onShow: function () {
            this.setOptions({
                minDate: $discount_starts.val() ? $discount_starts.val() : false
            })
        }
    });

    $(".discount-edit-btn, .discount-add-btn").on("click", function(){
        var ticketTypes = $("#edit-event-tickets-list .basic .ticket-name");
        $("#edit-event-discount-ticket_type").find("option:not(:first-child)").remove();
		var html = '';
        for (var i = 0; i < ticketTypes.length ; ++i) {
			html += '<option value="' + i + '">' + ticketTypes[i].value + '</option>';
        }
		$("#edit-event-discount-ticket_type").append(html);
    });
    $("#edit-event-discount-save").on("click", function(){
        var data = {};
        data.id = $("#edit-event-discount-id").val();
        data.type = $("#edit-event-discount-type").val();
        data.amount = $("#edit-event-discount-amount").val();
        data.code = $("#edit-event-discount-code").val();
        data.ticket_type = $("#edit-event-discount-ticket_type").val();
        data.quantity = $("#edit-event-discount-quantity").val();
        data.starts = $("#edit-event-discount-starts").val();
        data.ends = $("#edit-event-discount-ends").val();

        $("#edit-event-discount-id").val("");
        $("#edit-event-discount-type").val("");
        $("#edit-event-discount-amount").val("");
        $("#edit-event-discount-code").val("");
        $("#edit-event-discount-ticket_type").val("");
        $("#edit-event-discount-quantity").val("");
        $("#edit-event-discount-starts").val("");
        $("#edit-event-discount-ends").val("");

        var rowIndex = $(this).data("row-index");
        $(this).data("row-index", "");

        addDiscount(data, rowIndex);
    });
    function addDiscount(data, rowIndex)
    {
        var count = $("#ticket-types > li").length;
        var $discount = null;
        if (data.id) {
            $discount = $("#discounts-list tr[data-discount-id=" + data.id + "]");
        } else if (rowIndex) {
            $discount = $("#discounts-list tbody tr:nth-child(" + rowIndex + ")");
        }

        if (!$discount) {
            $discount = discountTemplate.clone();
            $discount.find(".delete").on("click", function(){
                var index = $(this).parents("tr").index() + 1;
                $("#edit-event-discount-delete-modal-btn").data("row-index", index);
            });

            $discount.find(".edit").on("click", function(){
                if(!$("#edit-event-discounts-panel").hasClass('in')){
                    $("#edit-event-discounts-panel").addClass('in');
                    $("#edit-event-discounts-panel").removeAttr('style')
                }
				var amount_number = $discount.find("[name*=discount_amount]").val();
				var type          = $discount.find("[name*=discount_type]").val();
				var amount        = (type == 'Percentage' || type == 'Percent') ? amount_number+'%' : '&euro;'+amount_number;

                var index = $(this).parents("tr").index() + 1;
                $("#edit-event-discount-id").val($discount.find("[name*=discount_id]").val());
                $("#edit-event-discount-type").val(type).trigger('change');
                $("#edit-event-discount-amount").val(amount);
                $("#edit-event-discount-code").val( $discount.find("[name*=discount_code]").val());
                $("#edit-event-discount-ticket_type").val($discount.find("[name*=discount_ticket_type]").val());
                $("#edit-event-discount-quantity").val($discount.find("[name*=discount_quantity]").val());
                $("#edit-event-discount-starts").val($discount.find("[name*=discount_starts]").val());
                $("#edit-event-discount-ends").val($discount.find("[name*=discount_ends]").val());
                $("#edit-event-discount-save").data("row-index", index);
            });
        }

        var ticketTypes = $("#edit-event-tickets-list").find(".details .ticket-name");
        $discount.find("[name*=discount_ticket_type] option").remove();
        $("#edit-event-discount-ticket_type option").each(function(){
            $discount.find("[name*=discount_ticket_type]").append(
                '<option value="' + this.value + '"' + (this.selected ? 'selected="selected"' : '') + '>' + this.innerHTML + '</option>'
            );
        });


        // fill data if set
        if (data) {
            $discount.find("[name*=discount_ticket_type]").val(data.ticket_type);
            var typeTexts = [];
            if (data.ticket_type) {
                ticketTypes = $("#edit-event-tickets-list").find(".basic .ticket-name");
				var ticketTypeText;
                for (var i = 0 ; i < data.ticket_type.length ; ++i) {
					if (data.ticket_type[i] === '') {
						ticketTypeText = $('#edit-event-discount-ticket_type').find('[value=""]').html();
					}
					else {
						ticketTypeText = ticketTypes[data.ticket_type[i]].value;
					}
					typeTexts.push(ticketTypeText);
                }
            }
            $discount.find(".discount-ticket-types").html(typeTexts.join(', '));

			var amount = (data.type == 'Percentage' || data.type == 'Percent') ? data.amount+'%' : '&euro;'+data.amount;

            $discount.find("[name*=discount_id]").val(data.id);
            $discount.find("[name*=discount_type]").val(data.type);
            $discount.find("[name*=discount_amount]").val(data.amount);
            $discount.find("[name*=discount_code]").val(data.code);
            $discount.find("[name*=discount_quantity]").val(data.quantity);
            $discount.find("[name*=discount_starts]").val(data.starts);
            $discount.find("[name*=discount_ends]").val(data.ends);

            $discount.find(".discount-code span").html(data.code);
            $discount.find(".discount-type").html(data.type);
            $discount.find(".discount-quantity").html(data.quantity);
            $discount.find(".discount-used").html("");
            $discount.find(".discount-date").html(data.starts + " &ndash; " + data.ends);
            $discount.find(".discount-amount").html(amount);
        }



        // Add the discount table below TOTAL CAPACITY
        $(".discount-table").removeClass('hidden');
        $(".discount-table").find('tbody').append($discount);
    }
    $('#edit-event-discount-delete-modal').on('show.bs.modal', function (e) {
        var dataDiscountId = $(e.relatedTarget).closest(".discount-row-template").data('discount-id');
        $(this).data('discount-id', dataDiscountId);
    });
    $("#edit-event-discount-delete-modal-btn").on("click", function(){
        var discountid = $(this).closest('#edit-event-discount-delete-modal').data("discount-id");
        $.post(
            "/admin/events/ajax_delete_discount",
            {id: discountid},
            function (response) {
                $("#discounts-list").find("[data-discount-id=" + discountid + "]").remove();
            }
        );
    });
    $("#edit-lookup,#create-lookup").on("click", function(e){
        $('.error-area').html('');
        if($('#lookup-label').val() == ''){
            $('#lookup-label-error-area').html("Please type Label field.");
        }else if($('#lookup-value').val() == ''){
            $('#lookup-value-error-area').html("Please type Value field.");
        }else if($(this).attr('id') == 'create-lookup') {
            if($('#lookup-field').val() == ''){
                $('#lookup-field-error-area').html("Please type Label field.");
            }else{
                $('#form_add_lookup').submit()
            }
        }else{
            $('#form_edit_lookup').submit()
        }
    })
    $dateTemplate = $("#event-dates .template");
    $dateTemplate.remove();
    var dateCount = 0;
    function addDate(data)
    {
        var $date = $dateTemplate.clone();
        $date.find(".date-start").addClass("validate[required]");
        $date.find(".time-start").addClass("validate[required]");

        if (data) {
            $date.find('[name="date_id[]"]').val(data.id);
            $date.find(".date-start").val(data.start_date);
            $date.find(".time-start").val(data.start_time);
            $date.find(".date-end").val(data.end_date);
            $date.find(".time-end").val(data.end_time);
            $date.find(".on_sale").val(data.is_onsale);
        }

		$date.find('.date-start').datetimepicker(
		{
            timepicker:false,
            format: 'Y-m-d',
			minDate: 0,
			minTime: 0,
			step: 15,
			yearStart: new Date().getFullYear(),
			closeOnDateSelect: true,
			onShow:function() {
				var $end_date = $date.find('.date-end');
				this.setOptions({
					maxDate: $end_date.val() ? $end_date.val().replace(/-/g, '/') : false
				});
			}
		});

		$date.find('.date-end').datetimepicker(
		{
			timepicker:false,
            format: 'Y-m-d',
			minDate: 0,
			minTime: 0,
			step: 15,
			yearStart: new Date().getFullYear(),
			closeOnDateSelect: true,
			onShow:function() {
				var $start_date = $date.find('.date-start');
				this.setOptions({
					minDate: $start_date.val() ? $start_date.val().replace(/-/g, '/') : 0
				});
			}
		});

        $date.find(".time-start, .time-end").datetimepicker({
            datepicker:false,
            defaultDate: new Date(),
            format: 'H:i',
			minDate: 0,
			step: 15,
			closeOnDateSelect: true
        });

		$date.find(".count-down-time").datetimepicker({
            datepicker:false,
            format: 'H:i:s',
            minDate: 0,
			step: 1,
			closeOnDateSelect: true
        });

        if (dateCount == 0) {
            $("#event-dates").append($date);
        } else {
            if (data && !data.has_order) {
                $date.find(".remove").removeClass("hidden");
            }
            $("#event-multi-dates").append($date);
        }
        ++dateCount;
    }
    $("#event-edit-add-multi-date-btn").on("click", function(){
        addDate();
    });

	// Have at least one date picker available when "Schedule multiple events" is clicked
	$('#edit-event-multiple_events-panel').on('show.bs.collapse', function()
	{
		if ($('#event-multi-dates').find('> li').length == 0)
		{
			addDate();
		}
	});


    $("#edit-event-venue-name").on("change", function(){
        var option = new Option();
        option.value = "new";
        option.innerHTML = $("#edit-event-venue-name").val() + " (New)";
        option.className = "new";
        option.selected = true;
        $("#edit-event-venue option.new").remove();
        $("#edit-event-venue").append(option);
    });

    $("#edit-event-venue-name").autocomplete({
        select: function(e, ui) {
            this.value = ui.item.label;
            $("#edit-event-venue-id").val(ui.item.value);

            $("#edit-event-venue-address_1").val(ui.item.address_1);
            $("#edit-event-venue-address_2").val(ui.item.address_2);
            $("#edit-event-venue-city").val(ui.item.city);
            $("#edit-event-venue-country").val(ui.item.country_id);
            $("#edit-event-venue-country").change();
            $("#edit-event-venue-county").val(ui.item.county_id);
            $("#edit-event-venue-eircode").val(ui.item.eircode);
            $("#edit-event-venue-telephone").val(ui.item.telephone);
            $("#edit-event-venue-website").val(ui.item.website);
            $("#edit-event-venue-email").val(ui.item.email);
            $("#edit-event-venue-facebook_url").val(ui.item.facebook_url);
            $("#edit-event-venue-twitter_url").val(ui.item.twitter_url);
            if (ui.item.image_media_url) {
                $("#venue-image_media_id").val(ui.item.image_media_id);
                $("#venue_image_saved").attr("src", ui.item.image_media_url);
                $("#venue_image_saved").removeClass('hidden');
                $("#venue-image_media_remove").removeClass('hidden');
            }
            return false;
        },

        source: function(data, callback){
            $.get("/admin/events/autocomplete_venue_list",
                data,
                function(response){
                    callback(response);
                });
        }
    });

	$(".time").datetimepicker({
		datepicker:false,
		defaultDate: new Date(),
		format: 'H:i',
		step: 15,
		closeOnDateSelect: true
	});

	// Clear specified form fields when an element with the data-clear_fields attribute is clicked
	$('#event-edit').on('click', '[data-clear_fields]', function()
	{
		$(this.getAttribute('data-clear_fields')).val('');
	});


    var $customTimeTemplate = $("#custom-times .template");
    $customTimeTemplate.remove();
    function addCustomTime(title, time)
    {
        var $li = $customTimeTemplate.clone();
        $li.removeClass("template");
        $li.find(".title").val(title);
        $li.find(".time").val(time);
		$li.find(".time").datetimepicker({
			datepicker:false,
			defaultDate: new Date(),
			format: 'H:i',
			step: 15,
			closeOnDateSelect: true
		});
        $("#custom-times").append($li);
    }

    $("#event-edit-add-custom-time-btn").on("click", function(){
        addCustomTime("", "");
    });
    $("#edit-event-venue-country").on("change", function(){
        var countryId = this.value;
        $("#edit-event-venue-county option").remove();
        if (window.venue_countries["id_" + countryId])
        for (var i in window.venue_countries["id_" + countryId].counties) {
            $("#edit-event-venue-county").append(
                new Option(window.venue_countries["id_" + countryId].counties[i].name, window.venue_countries["id_" + countryId].counties[i].id)
            );
        }

		if (window.venue_countries["id_" + countryId].counties.length || $('#edit-event-venue-country').val() == '')
		{
			$("#edit-event-venue-county").show();
		}
		else
		{
			$("#edit-event-venue-county").hide();
		}

    });

    function updateEventUrl() {
        var name = $("#edit-event-web_address_url").val();
        if (name == "" || !name) {
            name = $("#edit-event-name").val();
        }
        $.post(
            "/admin/events/geturl",
            {
                category_id: $("#edit-event-category").val(),
                name: name,
                exclude_id: $("#edit-event-id").val()
            },
            function (response){
                $("#edit-event-web_address_url").val(response.url);
				$("#seo-edit-event-web_address_url").val(response.url);
            }
        );
    }

    $("#edit-event-name, #edit-event-category, #edit-event-web_address_url, #seo-edit-event-web_address_url").on("change", function()
	{
        $("#seo-edit-event-name").val($("#edit-event-name").val());
        updateEventUrl();
    });

    $("#seo-edit-event-name").on("change", function () {
        $("#edit-event-name").val($("#seo-edit-event-name").val());
        updateEventUrl();
    });

	$('#seo-edit-event-web_address_url').on('change', function() {
		$('#edit-event-web_address_url').val(this.value);
	});

    $("#edit-event-add_tag").on("keypress", function(e){
        if (e.keyCode == 13) { //enter
            addTag($("#edit-event-add_tag").val());
            $("#edit-event-add_tag").val("");
            return false;
        }
    });
    $("#tag-add-btn").on("click", function(){
        addTag($("#edit-event-add_tag").val());
        $("#edit-event-add_tag").val("");
    });

	$('#edit-event-discount-type').on('change', function()
	{
		var unit = '&euro;';
		if ($(this).find(':selected').attr('data-type') == 'percentage')
		{
			unit = '%';
		}

		$('#edit-event-discount-amount-unit').html(unit);
	}).trigger('change');


    $("#edit-event-add-faqs-panel button.add").on("click", function(){
        var html = '';
        $("#edit-event-add-faqs-panel input[type=checkbox]").each(function(){
            if (this.checked) {
                html += '<p><b>' + $(this).parent().find('span').html() + '</b></p>';
            }
        });
        CKEDITOR.instances['edit-event-description'].updateElement();
        CKEDITOR.instances['edit-event-description'].setData((CKEDITOR.instances['edit-event-description'].getData() + html));
        CKEDITOR.instances['edit-event-description'].updateElement();
    });
    function loadEventEditData()
    {
        if (window.eventEditData) {
            var data = window.eventEditData;
            if (data.ticket_types) {
                for (var i = 0 ; i < data.ticket_types.length ; ++i) {
                    addTicketType2(data.ticket_types[i].type, data.ticket_types[i]);
                }
            }

            if (data.other_times) {
                for (var i = 0 ; i < data.other_times['title'].length ; ++i) {
                    addCustomTime(data.other_times['title'][i], data.other_times['time'][i]);
                }
            }

            if (data.discounts) {
                for (var i = 0 ; i < data.discounts.length ; ++i) {
                    addDiscount(data.discounts[i]);
                }
            }

            if (data.tags) {
                for (var i = 0 ; i < data.tags.length ; ++i) {
                    addTag(data.tags[i].tag);
                }
            }

            if (data.dates && data.dates.length > 0) {
                for (var i = 0 ; i < data.dates.length ; ++i) {
                    addDate(data.dates[i]);
                }
            } else {
                addDate();
            }

            if (data.videos) {
                for (var i = 0 ; i < data.videos.length ; ++i) {
                    addNewVideo(data.videos[i]);
                }
            }
        } else {
            addNewVideo("");
            addCustomTime("", "")
            addDate();
        }
        validateTotalCapacity();
    }

    loadEventEditData();

    initMap(".map-container");

	/*
	// Enforce a character limit, which ignores HTML code
	// This could be moved to a method to make easier to apply to more elements
	CKEDITOR.instances['edit-event-ticket_note'].on('key', function(ev)
	{
		var event = ev.data.domEvent.$;

		// Let these keys behave as normally
		var ignore_keys = ['Backspace', 'Delete'];
		var is_arrow    = (event.code.substr(0, 5) == 'Arrow');
		if (event.ctrlKey || event.metaKey || ignore_keys.indexOf(event.code) > -1 || is_arrow)
		{
			console.log(event);
		}
		else
		{
			var input = this.getData(); // Text in the editor
			var max   = this.element.getAttribute('data-maxlength'); // Max length defined in the element's data attribute

			var length = $(input).text().length; // length excluding HTML elements

			// Only perform the truncation if the text is too long
			if (length > max)
			{
				var counter        = 0;
				var tag_open       = false;
				var entity_open    = false;
				var shortened_text = '';
				for (var i = 0; i < input.length; i++)
				{
					// Signal the opening on a tag or entity. e.g. '<br />' or entity '&amp;'
					switch (input[i])
					{
						case '<': tag_open    = true;  break;
						case '&': entity_open = true;  break;
					}

					// Don't count tags toward the character limit. Only count the entire entity as 1 character.
					if (tag_open || entity_open && input[i] != '&')
					{
						// Keep the entity or tag in the cut text
						shortened_text += input[i];
					}
					else
					{
						// Keep the text if the limit has not been reached
						if (counter < max) shortened_text += input[i];

						counter++;
					}

					// Signal the closing of the tag or entity.
					switch (input[i])
					{
						case '>': tag_open    = false; break;
						case ';': entity_open = false; break;
					}
				}

				var t = this;
				setTimeout(function() {
					if (input != shortened_text)
					{
						t.setData(shortened_text);
						t.focus();
					}
				}, 50);
			}
		}
	} );
	*/
	CKEDITOR.replace('edit-event-ticket_note' , {
		extraPlugins: 'notification,wordcount',
		wordcount: {
			showWordCount: true,
			showCharCount: false,
			showParagraphs: false,
			countSpacesAsChars: true,
			countHTML: false,
            maxWordCount: 25
		},
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


    $("#event-edit [name=action]").on("click", function(){

		$('.error-area').html('');

        if ($("[name=name]").val() == ""){
            //alert("Please enter event name");
            //return false;
        }
        if (this.value == 'save') {

        }

        if (this.value != 'make_offline' && this.value != 'save_draft' && this.value != 'save_stripe_connect' && this.value != 'save_stripe_disconnect')
		{
			$('#edit-event-organizer_contact_details-panel').collapse('show');
			$('.edit-event-organizer-section .panel-body.collapse').collapse('show');

            if ( ! $("#event-edit").validationEngine('validate')) {
                return false;
            }

            if ($("#event-dates > li").length == 0) {
                alert("Please add at least one date!");
                $("#event-dates").focus();
                return false;
            }

            //payment information needed
            if ($("#use_stripe_connect").val() == 1 && $('.use-stripe:contains("Connect Stripe Account")').length > 0) {
                $('#billing-error-area').html("Please add billing information.");
                $("#billing-error-area")[0].scrollIntoView();
                return false;
            }

            //at least one ticket type is needed
            if ($("#edit-event-tickets-list").find("\> tr").length == 0) {
                $('#create-tickets-error-area').html("Please add at least one ticket.");
                $("#create-tickets-section")[0].scrollIntoView();
                return false;
            }

            //event picture is needed
            if ($("[name=event_image_media_id]").val() == "") {
                $('#event-image-error-area').html("Please add a picture.");
                $("#event-image-error-area")[0].scrollIntoView();
                return false;
            }

            if($('.organiser_checkbox:checked').length > 1) {
                $("#organizers-primary-error-area").html($("#organizers-primary-error-area").data("error"));
                $("#organizers-primary-error-area").css("display", "");
                $("#organizers-primary-error-area")[0].scrollIntoView();
                return false;
            }

            var oemails = [];
            $(".organizer-email").each(function(){
                if (this.value && this.value != "") {
                    oemails.push(this.value);
                }
            });
            for (var i = 0 ; i < oemails.length ; ++i) {
                for (var j = 0 ; j < oemails.length ; ++j) {
                    if (i != j) {
                        if (oemails[i] == oemails[j]) {
                            $("#organizers-email-error-area").html($("#organizers-email-error-area").data("error"));
                            $("#organizers-email-error-area").css("display", "");
                            $("#organizers-email-error-area")[0].scrollIntoView();
                            return false;
                        }
                    }
                }
            }
            $("#organizers-email-error-area").css("display", "none");

            var orgids = [];
            $(".organizer-contact-id").each(function(){
                if (this.value && this.value != "") {
                    orgids.push(this.value);
                }
            });
            for (var i = 0 ; i < orgids.length ; ++i) {
                for (var j = 0 ; j < orgids.length ; ++j) {
                    if (i != j) {
                        if (orgids[i] == orgids[j]) {
                            $("#organizers-duplicate-error-area").html($("#organizers-duplicate-error-area").data("error"));
                            $("#organizers-duplicate-error-area").css("display", "");
                            $("#organizers-duplicate-error-area")[0].scrollIntoView();
                            return false;
                        }
                    }
                }
            }
            $("#organizers-duplicate-error-area").css("display", "none");
        }

        /*
         if ($("[name=name]").val() == ""){
         alert("Please enter event name");
         return false;
         }
         */
        return true;
    });

	update_weather_icon(document.getElementById('edit-event-forecast_icon').value);

	$('#edit-event-date-start, #edit-event-time-start').on('change', function(ev)
	{
		ev.preventDefault();
		var start_date = document.getElementById('edit-event-date-start').value.trim();
		var start_time = document.getElementById('edit-event-time-start').value.trim();
		var latitude   = document.getElementById('edit-event-venue-lat' ).value.trim();
		var longitude  = document.getElementById('edit-event-venue-lng' ).value.trim();
		var api_key    = document.getElementById('forecast_api_key'     ).value.trim();

		if (start_date && start_time && latitude && longitude && api_key)
		{
			start_date = start_date.replace(/-/g, '/');
			var current_time      = new Date();
			var event_time        = new Date(clean_date_string(start_date+' '+start_time));
			var current_timestamp = current_time.getTime() / 1000;
			var event_timestamp   = event_time.getTime()   / 1000;
			var difference        = event_timestamp - current_timestamp;

			var weeks             = difference / 60 / 60 / 24 / 7;

			var url = 'https://api.forecast.io/forecast/'+api_key+'/'+latitude+','+longitude;

			// Only get the forecast if the date is in the next two weeks
			if (difference >= 0 && weeks <= 2)
			{
				$.ajax({url: url, type: 'post', dataType: 'jsonp', crossDomain: true}).done(function(results)
				{
					var forecast = '';

					var minutes = Math.round(difference / 60);
					var hours   = Math.round(difference / 60 / 60);
					var days    = Math.round(difference / 60 / 60 / 24);

					// If the date is in the next hour, get the minutely forecast
					if (minutes <= 61 && results.minutely && results.minutely.data[minutes])
					{
						forecast = results.minutely;
					}
					// If the date is in the next two days, get the forecast for the specific hour
					else if (hours <= 49 && results.hourly && results.hourly.data[hours])
					{
						forecast = results.hourly.data[hours];
					}
					// If the date is in the next week, get the forecast for the specific day
					else if (days <= 8 && results.daily && results.daily.data[days])
					{
						forecast = results.daily.data[days];
					}
					// Shouldn't come to this.
					else
					{
						forecast = results.currently;
					}

					update_weather_icon(forecast.icon);
					document.getElementById('edit-event-weather-forecast-summary').innerHTML = forecast.summary;

					document.getElementById('edit-event-forecast_icon'   ).value = forecast.icon;
					document.getElementById('edit-event-forecast_summary').value = forecast.summary;
					document.getElementById('edit-event-forecast_json'   ).value = JSON.stringify(results);
				});
			}
			else
			{
				var forecast_summary = 'Cannot get forecast for the selected date';
				document.getElementById('edit-event-weather-forecast-summary').innerHTML = forecast_summary;

				document.getElementById('edit-event-forecast_icon'   ).value = '';
				document.getElementById('edit-event-forecast_summary').value = forecast_summary;
				document.getElementById('edit-event-forecast_json'   ).value = '{}';
			}

		}
	});

	$('#edit-event-preview-button').on('click', function(ev)
	{
        var $form = $('#event-edit');
        // change form target to _blank to open a new window
        $form.attr("target", "_blank");
        setTimeout(function(){
            $form.attr("target", ""); // reset form target to use self window when another button is clicked
        }, 1000);
	});

	$('.popover-init').popover({content: function() { return $(this).data('content') }});

	$('#edit-event-tweet-prompt').on('show.bs.modal', function (ev)
	{
		// Check if the form is valid before showing the Tweet prompt
		if ( ! document.getElementById('event-edit').checkValidity())
		{
			// If invalid, don't show the modal...
			ev.preventDefault();
			// ... and click the submit button to force the validation errors to show
			document.getElementById('edit-event-publish-btn').click();
		}
	});

    //avoid to submit on enter key
    $('#event-edit input[type=text], #event-edit select').keypress(function(e){
        if (e.keyCode == 13 || e.key == "Enter") {
            e.preventDefault();
            return false;
        }
    });

    $("#show-email-attendees-modal").on("click", function(){
        var $form = $("#email_attendees_form");
        $form.find("[name='order_id[]']").remove();

        $("#edit-event-attendees [name='order_id[]']").each(function(){
            if (this.checked) {
                $form.append('<input type="hidden" name="order_id[]" value="' + this.value + '" />');
            }
        });
    });

    $("#select-all-attendees").on("click", function(){
        $("#edit-event-attendees [name='order_id[]']").prop("checked", true);
    });

    var uploaded_image_to_delete = null;
    $(document).on('click', '.saved-image-remove', function(){
        uploaded_image_to_delete = this;
        $("#edit-event-image-delete-modal").modal();
    });

    $("#edit-event-image-delete-modal-btn").on("click", function(){
        if (uploaded_image_to_delete) {
            $(uploaded_image_to_delete).hide();
            $(uploaded_image_to_delete).parent().find("img").attr("src", "");
            $(uploaded_image_to_delete).parent().find("input").val("");
        }
    });


    $("#event_image_saved").on("click", function(){
        var img = this;
        existing_image_editor(
            img.src,
            "Event banners",
            function (response) {
                $("[name=event_image_media_id]").val(response.media_id);
                img.src = "/" + response.file;
                $(img).removeClass('hidden');
                $(img).parents(".saved-image").find(".saved-image-remove").removeClass('hidden');
                $('#edit_image_modal').modal('hide');
            },
			'locked'
        );
    });


    $("#venue_image_saved").on("click", function(){
        var img = this;
        existing_image_editor(
            img.src,
            "Venue banners",
            function (response) {
                $("#venue-image_media_id").val(response.media_id);
                img.src = "/" + response.file;
                $(img).removeClass('hidden');
                $(img).parents(".saved-image").find(".saved-image-remove").removeClass('hidden');
                $('#edit_image_modal').modal('hide');
            },
			'locked'
        );
    });

    $(".organizer_profile_image_saved").on("click", function(){
        var img = this;
        existing_image_editor(
            img.src,
            "Organizer profiles",
            function (response) {
                $(img).parents(".saved-image").find("input[type=hidden]").val(response.media_id);
                img.src = "/" + response.file;
                $(img).removeClass('hidden');
                $(img).parents(".saved-image").find(".saved-image-remove").removeClass('hidden');
                $('#edit_image_modal').modal('hide');
            },
			'locked'
        );
    });

    $(".organiser_banner_image_saved").on("click", function(){
        var img = this;
        existing_image_editor(
            img.src,
            "Organizer banners",
            function (response) {
                $(img).parents(".saved-image").find("input[type=hidden]").val(response.media_id);
                img.src = "/" + response.file;
                $(img).removeClass('hidden');
                $(img).parents(".saved-image").find(".saved-image-remove").removeClass('hidden');
                $('#edit_image_modal').modal('hide');
            },
			'locked'
        );
    })

    $("#edit_event-ticket-currency").on("click", function(){
        var currency = this.value;
        var sym = window.currencies[currency].symbol;
        $(".currency-sym").html(sym);
        $ticketTemplate2.find(".currency-sym").html(sym);
    });
});

function update_weather_icon(icon)
{
	icon = icon.toUpperCase().replace(/-/g, '_');
	var skycons = new Skycons({'color': 'black'});
	skycons.add(document.getElementById('edit-event-weather-icon'), Skycons[icon]);
}

window.event_image_uploaded = function(filename, path, data, upload_wrapper){
    if (data.media_id) {
        $("[name=event_image_media_id]").val(data.media_id);
        $("[name=event_image_media_id]").parents(".saved-image").find('img').prop('src', window.location.protocol + '//' + window.location.host + "" + path);
        $("[name=event_image_media_id]").parents(".saved-image").find('img').removeClass('hidden');
        $("[name=event_image_media_id]").parents(".saved-image").find(".saved-image-remove").removeClass('hidden');

        upload_wrapper.find(".file_previews").focus();
        try {
            upload_wrapper.find(".file_previews")[0].scrollIntoView();
        } catch (exc) {

        }

        existing_image_editor(
            window.location.protocol + '//' + window.location.host + "" + path,
            "Event banners",
            function (response) {
                $("[name=event_image_media_id]").val(response.media_id);
                $('#edit_image_modal').modal('hide');
            },
			'locked'
        );
    }
};

window.venue_image_uploaded = function(filename, path, data, upload_wrapper){
    if (data.media_id) {
        $("#venue-image_media_id").val(data.media_id);
        $("#venue-image_media_id").parents(".saved-image").find('img').prop('src', window.location.protocol + '//' + window.location.host + "" + path);
        $("#venue-image_media_id").parents(".saved-image").find('img').removeClass('hidden');
        $("#venue-image_media_id").parents(".saved-image").find(".saved-image-remove").removeClass('hidden');

        upload_wrapper.find(".file_previews").focus();
        try {
            upload_wrapper.find(".file_previews")[0].scrollIntoView();
        } catch (exc) {

        }

        existing_image_editor(
            window.location.protocol + '//' + window.location.host + "" + path,
            "Venue banners",
            function (response) {
                $("#venue-image_media_id").val(response.media_id);
                $('#edit_image_modal').modal('hide');
            },
			'locked'
        );
    }
};

window.organizerp_image_uploaded = function(filename, path, data, upload_wrapper){
    var index = upload_wrapper.attr('id').replace('multiple_upload_wrapper_organiser_' , ''); //get the number at the end of id e.g. multiple_upload_wrapper_organiser_0
    if (data.media_id) {
        $("[name='organizers[" + index + "][profile_media_id]']").val(data.media_id);
        $("[name='organizers[" + index + "][profile_media_id]']").parents(".saved-image").find('img').prop('src', window.location.protocol + '//' + window.location.host + "" + path);
        $("[name='organizers[" + index + "][profile_media_id]']").parents(".saved-image").find('img').removeClass('hidden');
        $("[name='organizers[" + index + "][profile_media_id]']").parents(".saved-image").find(".saved-image-remove").removeClass('hidden');

        upload_wrapper.find(".file_previews").focus();
        try {
            upload_wrapper.find(".file_previews")[0].scrollIntoView();
        } catch (exc) {

        }

        existing_image_editor(
            window.location.protocol + '//' + window.location.host + "" + path,
            "Organizer profiles",
            function (response) {
                $("[name='organizers[" + index + "][profile_media_id]']").val(response.media_id);
                $('#edit_image_modal').modal('hide');
            },
			'locked'
        );
    }
};

window.organizer_image_uploaded = function(filename, path, data, upload_wrapper){
    var index = upload_wrapper.attr('id').replace('multiple_upload_wrapper_banner_file_id_' , ''); //get the number at the end of id e.g. multiple_upload_wrapper_organiser_0
    if (data.media_id) {
        $("[name='organizers[" + index + "][banner_media_id]']").val(data.media_id);
        $("[name='organizers[" + index + "][banner_media_id]']").parents(".saved-image").find('img').prop('src', window.location.protocol + '//' + window.location.host + "" + path);
        $("[name='organizers[" + index + "][banner_media_id]']").parents(".saved-image").find('img').removeClass('hidden');
        $("[name='organizers[" + index + "][banner_media_id]']").parents(".saved-image").find(".saved-image-remove").removeClass('hidden');

        upload_wrapper.find(".file_previews").focus();
        try {
            upload_wrapper.find(".file_previews")[0].scrollIntoView();
        } catch (exc) {

        }

        existing_image_editor(
            window.location.protocol + '//' + window.location.host + "" + path,
            "Organizer banners",
            function (response) {
                $("[name='organizers[" + index + "][banner_media_id]']").val(response.media_id);
                $('#edit_image_modal').modal('hide');
            },
			'locked'
        );
    }
};
