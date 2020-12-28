function initialize_map(map_summary_id, search_box_id, coordinates_id, coordinates_lt, coordinates_lg)
{
    var $coordinates_field = $('#'+coordinates_id);
    // Set the map options
    var myOptions = {
        center:new google.maps.LatLng(coordinates_lt, coordinates_lg),
        zoom: $coordinates_field.val() ? 20 : 12, // Zoom in closer if the coordinates have already been specified
        mapTypeId:google.maps.MapTypeId.HYBRID,
        panControl:true,
        zoomControl:true,
        mapTypeControl:true,
        streetViewControl:false,
        overviewMapControl:false

    };

    //Create a new map object
    var map_summary = new google.maps.Map(document.getElementById(map_summary_id), myOptions);

    // Add in the marker at the project location
    var marker = new google.maps.Marker({
        position:new google.maps.LatLng(coordinates_lt, coordinates_lg),
        map:map_summary
    });


    // A function to create the marker and set up the event window function
    function createMarker(latlng)
    {
        var marker = new google.maps.Marker({
            position:latlng,
            map:map_summary,
            zIndex:Math.round(latlng.lat() * -100000) << 5
        });

        // Update the location text input box
        $('#'+coordinates_id).val(Math.round(latlng.lat() * 1000000) / 1000000 + ',' + Math.round(latlng.lng() * 1000000) / 1000000).trigger('change');

        google.maps.event.trigger(marker, 'click');
        return marker;
    }

    google.maps.event.addListener(map_summary, 'click', function (event)
    {
        //call function to create marker
        if (marker)
        {
            marker.setMap(null);
            marker = null;
        }
        marker = createMarker(event.latLng);
    });

    var markers = [];

    // Create the search box and link it to the UI element.
    var input = /** @type {HTMLInputElement} */(
        document.getElementById(search_box_id));
    map_summary.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    var searchBox = new google.maps.places.SearchBox(
        /** @type {HTMLInputElement} */(input));

    // Listen for the event fired when the user selects an item from the
    // pick list. Retrieve the matching places for that item.
    google.maps.event.addListener(searchBox, 'places_changed', function() {
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }
        for (var i = 0, marker; marker = markers[i]; i++) {
            marker.setMap(null);
        }

        // For each place, get the icon, place name, and location.
        markers = [];
        var bounds = new google.maps.LatLngBounds();
        for (var i = 0, place; place = places[i]; i++) {
            var image = {
                url: place.icon,
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(25, 25)
            };

            // Create a marker for each place.
            var marker = new google.maps.Marker({
                map: map_summary,
                icon: image,
                title: place.name,
                position: place.geometry.location
            });

            markers.push(marker);

            bounds.extend(place.geometry.location);
        }

        map_summary.fitBounds(bounds);

        // Put the bounds in the coordinates field
        $coordinates_field.val(bounds.pa.g+','+bounds.ka.g);
    });

    // Bias the SearchBox results towards places that are within the bounds of the
    // current map's viewport.
    google.maps.event.addListener(map_summary, 'bounds_changed', function() {
        var bounds = map_summary.getBounds();
        searchBox.setBounds(bounds);
    });
}
