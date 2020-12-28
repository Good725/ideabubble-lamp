(function ($) {
    $(document).ready(function() {

        function initMap(mapid)
        {
            $(mapid).each(function(){
                var container = this;
                var map = null;
                var initX = parseFloat($(container).data("init-x")) || 53.32693558541906;
                var initY = parseFloat($(container).data("init-y")) || -6.416015625;
                var initZ = parseInt($(container).data("init-z"));
                var targetX = $(container).data("target-x");
                var targetY = $(container).data("target-y");
                var button = $(container).data("button");
                var find_button = $('#edit-location-find_location')[0];
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

                var searchBox = new google.maps.places.SearchBox(document.getElementById('edit-location-map_search'));
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
                        $('#edit-lat' ).val(places[0].geometry.location.lat());
                        $('#edit-lng').val(places[0].geometry.location.lng());
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

                    $('#edit-lat').val(event.latLng.lat());
                    $('#edit-lng').val(event.latLng.lng());
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
                    });
                }

                $(find_button).off('click').on('click', function() {
                    var address = get_address();

                    // Trigger a change
                    try {
                        var input = document.getElementById('location-modal-map_search');
                        google.maps.event.trigger(input, 'focus');
                        google.maps.event.trigger(input, 'keydown', {keyCode: 13});
                    } catch (e) {
                        console.log(e);
                    }

                    geocoder.geocode(
                        { 'address': address },
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

                                if (args.coordinates_field) {
                                    $(args.coordinates_field).val(results[0].geometry.location.lat()+','+results[0].geometry.location.lng());
                                }
                                trackXY = true;
                            } else {
                                alert('Geocode was not successful for the following reason: ' + status);
                            }
                        }
                    );

                });

            });

            $('#location-reset-button').on('click', function() {
                $('#edit-lat, #edit-lng').val('').trigger('change');
            });
        }

        initMap(".map-container");
        jQuery.extend(jQuery.validator.messages, {
            required: "Required!",
            numeric: "Invalid value!"
        });

        /**
         * show add rows section
         */
        var $added_rows_container = $('#added_rows_container');
        var $location_parent_select = $("#parent_id");
        var $add_rows_section = $("#add_rows_section");
        var parent = $location_parent_select.val();
        if(parent !== ""){
            $add_rows_section.removeClass("hide");
        }else{
            if(!$add_rows_section.hasClass("hide")){
                $add_rows_section.addClass("hide");
            }
        }

        $("#form_add_edit_location").validate();

        $("#county_id").change(function(){
            var id = parseInt($(this).val());
            if (id > 0) {
                $.post('/admin/courses/ajax_get_cities_for_county', {id: id}, function (data) {
                    $("#city_id").html(data);
                    if ($("#city_id").data("default")) {
                        $("#city_id").val($("#city_id").data("default"));
                    }
                }, "text");
            } else {
                $("#city_id").html('<option value="" selected="selected">Please select county first</option>');
            }
        });

        $("#btn_delete").click(function (ev) {
            ev.preventDefault();
            var id = $(this).data("id");
            $("#btn_delete_yes").data('id', id);
            $("#confirm_delete").modal();
        });
        $("#btn_delete_yes").click(function (ev) {
            ev.preventDefault();
            var id = $(this).data('id');
            $.post('/admin/courses/remove_location', {id: id}, function (data) {
                if (data.redirect !== '' || data.redirect !== undefined) {
                    window.location = data.redirect;
                } else {
                    var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                    $("#main").prepend(smg);
                }
                $("#confirm_delete").modal('hide');

            }, "json");
        });

        $location_parent_select.on('change',function(){
            var current_value = $(this).val();
            if (this.selectedIndex > 0) {
                var $option = $('option:selected', this);
                $("#address1").prop('disabled', true);
                $("#address2").prop('disabled', true);
                $("#address3").prop('disabled', true);
                $("#county_id").prop('disabled', true);
                $("#city_id").prop('disabled', true);
                $("#edit-lat").prop('disabled', true);
                $("#edit-lng").prop('disabled', true);

                $("#address1").val($option.data('address1'));
                $("#address2").val($option.data('address2'));
                $("#address3").val($option.data('address3'));
                $("#county_id").val($option.data('county_id'));
                $("#city_id").val($option.data('city_id'));
                $("#city_id").html($('option:selected', this).data('city'));
                var id = parseInt($("#county_id").val());
                if (id > 0) {
                    $.post('/admin/courses/ajax_get_cities_for_county', {id: id}, function (data) {
                        $("#city_id").html(data);
                    }, "text");
                } else {
                    $("#city_id").html('<option value="" selected="selected">Please select county first</option>');
                }

                $(".map-container").data("init-x", $option.data("lat"));
                $(".map-container").data("init-y", $option.data("lng"));
                initMap(".map-container");
            } else {
                $("#address1").prop('disabled', false);
                $("#address2").prop('disabled', false);
                $("#address3").prop('disabled', false);
                $("#county_id").prop('disabled', false);
                $("#city_id").prop('disabled', false);
                $("#edit-lat").prop('disabled', false);
                $("#edit-lng").prop('disabled', false);

                $("#address1").val($("#address1").prop("defaultValue"));
                $("#address2").val($("#address2").prop("defaultValue"));
                $("#address3").val($("#address3").prop("defaultValue"));
                $("#county_id").val($("#county_id").data("default"));
                $("#city_id").val($("#city_id").prop("defaultValue"));
                $("#edit-lat").val($("#edit-lat").prop("defaultValue"));
                $("#edit-lng").val($("#edit-lng").prop("defaultValue"));
                $("#county_id").change();
            }

            // show add rows section
                if(current_value === "") {
                    parent = "";
                    if(!$add_rows_section.hasClass("hide")){
                        $add_rows_section.addClass("hide");
                    }
                }else if(current_value !== parent) {
                    parent = current_value;
                    $add_rows_section.removeClass("hide");
                }
        });

        $("#add_city").click(function(ev){
            ev.preventDefault();
            var city = $.trim($("#new_city").val());
            if (city.length < 1)
            {
                alert("Please enter proper city name!");
                return false;
            }
            var county = parseInt($("#county_id").val());
            if (county > 0)
            {
                $.post("/admin/courses/ajax_add_city", {name: city, county_id: county}, function(data){
                    $.post('/admin/courses/ajax_get_cities_for_county', {id: county}, function (data) {
                        $("#city_id").html(data);
                    }, "text");
                });
                return false;
            }
            else
            {
                alert('Please selecy county first!');
                return false;
            }
        });

        $("#add_type").click(function(ev){
            ev.preventDefault();
            var type = $.trim($("#new_type").val());
            if (type.length < 1)
            {
                alert("Please enter proper type name!");
                return false;
            }
            $.post("/admin/courses/ajax_add_type", {type: type}, function(data){
                $.post('/admin/courses/ajax_get_location_types', function (data) {
                    $("#location_type_id").html(data);
                }, "text");
            });
            return false;
        });

        $('.save_button.location').click(function(){
            $("#redirect").val($(this).data('redirect'));
            var added_rows_names = [];
            var added_rows_seats = [];
            // collect added rows data
            $added_rows_container.find('.form-group').each(function() {
                var $this =  $(this);
                added_rows_names.push($this.find('input.row-name').val());
                added_rows_seats.push($this.find('input.row-seats').val());
            });

            if ($added_rows_container.find("#added_rows_names").length > 0) {
                $added_rows_container.find("#added_rows_names").remove();
            }
            if ($added_rows_container.find("#added_rows_seats").length > 0) {
                $added_rows_container.find("#added_rows_seats").remove();
            }
            if (added_rows_names.length > 0) {
                $added_rows_container.append('<input type="hidden" id="added_rows_names" name="added_rows_names" value="' + added_rows_names + '">');
                $added_rows_container.append('<input type="hidden" id="added_rows_seats" name="added_rows_seats" value="'+ added_rows_seats +'">');
            }

            added_rows_names = [];
            added_rows_seats = [];

            if ($("#form_add_edit_location [name=modal]").val() == "1") {
                var data = $("#form_add_edit_location").serialize();
                $.post(
                    "/admin/courses/save_location",
                    data,
                    function (response) {
                        if (response.id) {
                            $("#location_id").append('<option value="' + response.id + '" selected="selected">' + $("#form_add_edit_location [name=name]").val() + '</option>');
                        }
                        $("#form_add_edit_location").parents(".modal").modal("hide");
                    }
                );
            } else {
                $("#form_add_edit_location").submit();
            }
        });

        /**
         * add rows to sub location
         */

        // add new rows
        $('#add_row_to_the_sub_location').on('click',function (ev) {
            ev.preventDefault();
            var new_row_val = $('#new_row').val();
            var seats_for_new_row_val = $('#seats_for_new_row').val();
            if ( new_row_val.length >=1 && parseInt( seats_for_new_row_val ) > 0 ){
            var str = '';
            str += '<div class="form-group">';
            str += '<div class="col-sm-4"><input readonly type="text" class="form-control row-name" value="'+new_row_val+'" /></div>';
            str += '<div class="col-sm-2"><input readonly type="number" min="0" class="form-control row-seats" value="'+seats_for_new_row_val+'" /></div>';
            str += '<div class="col-sm-2"><button class="btn btn-primary change-row">Change</button></div>';
            str += '<div class="col-sm-2"><button class="btn btn-primary remove-row">Remove</button></div>';
            str += '</div>';
                $added_rows_container.append(str);
            }else{
                alert("Name and Number of Seats (positive number) are required");
            }
        });

        // remove rows
        $added_rows_container.on('click','.remove-row',function (ev) {
            ev.preventDefault();
            $(this).closest('.form-group').remove();
        });

        // edit rows
        $added_rows_container.on('click','.change-row',function (ev) {
            ev.preventDefault();
            $(this).closest('.form-group').find('input').removeAttr("readonly");
        });


        $location_parent_select.change();
    });

    function get_address()
    {
        var address1 = document.getElementById('address1').value;
        var address2 = document.getElementById('address2').value;
        var address3 = document.getElementById('address3').value;
        var county   = $('#county_id').find(':not([value=""]):selected').html();
        county  = (county  == null) ? '' : county;

        var address = (address1 + ' ' + address2 + ' ' + address3 + ' ' + ' ' + county).trim();


        var $search = $('#edit-location-map_search');
        $search.val(address);

        google.maps.event.trigger($search[0], 'focus', {});
        google.maps.event.trigger($search[0], 'keydown', { keyCode: 13 });

        return address;
    }

    $(document).on('click', '#edit-location-find_location', function()
    {
        var address = get_address();
    });
})(jQuery);