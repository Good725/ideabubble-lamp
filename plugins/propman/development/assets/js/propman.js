/* Main list screens */
// Open the link, when anywhere in the table row is clicked...
// ... except for form elements or other links. (Clicking these have their own actions.)
$('.list-screen-table').on('click', 'tbody tr', function(ev)
{
    // If the clicked element is a link or form element or is inside one, do nothing
    if ( ! $(ev.target).is('a, label, button, :input') && ! $(ev.target).parents('a, label, button, :input')[0])
    {
        // Find the edit link
        var link = $(this).find('.edit-link').attr('href');

        // If the user uses the middle mouse button or Ctrl/Cmd key, open the link in a new tab.
        // Otherwise open it in the same tab
        if (ev.ctrlKey || ev.metaKey || ev.which == 2)
        {
            window.open(link, '_blank');
        }
        else
        {
            window.location.href = link;
        }
    }
});

/* Dragging sortability */
$('.sortable-tbody').sortable({cancel: 'a, button, :input, label'});

/* Multiselect */
$(document).ready(function()
{
    $('.rate-weeks-multipleselect').multiselect({includeSelectAllOption: true});

    $("#property-edit").validate({
        submitHandler: function (form)
        {
            serializePropertyCalendar();
            form.submit();
        }
    });
    $("#group-edit").validate({
        submitHandler: function (form)
        {
            form.submit();
        }
    });
    $("#ratecard-edit").validate({
        submitHandler: function (form)
        {
            form.submit();
        }
    });
    $("#buildingtype-edit").validate({
        submitHandler: function (form)
        {
            form.submit();
        }
    });
    $("#facilitygroup-edit").validate({
    submitHandler: function (form)
    {
        form.submit();
    }
    });
    $("#facilitygroup-edit").validate({
        submitHandler: function (form)
        {
            form.submit();
        }
    });
    $("#facilitytype-edit").validate({
        submitHandler: function (form)
        {
            form.submit();
        }
    });
    $("#period-edit").validate({
        submitHandler: function (form)
        {
            form.submit();
        }
    });
    $("#propertytype-edit").validate({
        submitHandler: function (form)
        {
            form.submit();
        }
    });
    $("#suitabilitygroup-edit").validate({
        submitHandler: function (form)
        {
            form.submit();
        }
    });
    $("#suitabilitytype-edit").validate({
        submitHandler: function (form)
        {
            form.submit();
        }
    });
    $(document).on('change','#edit-group-country_id',function()
    {
        $.ajax({
            type:'POST',
            data:{country_id:$('#edit-group-country_id').val()},
            url:'/admin/propman/ajax_get_counties',
            dataType:'json'
        })
            .done(function(output)
            {
                $('#edit-group-county_id').html(output);
            });
    });
    $(document).on('change','#edit-property-country',function()
    {
        $.ajax({
            type:'POST',
            data:{country_id:$('#edit-property-country').val()},
            url:'/admin/propman/ajax_get_counties',
            dataType:'json'
        })
            .done(function(output)
            {
                $('#edit-property-county').html(output);
            });
    });
});

/* Calendar */
function serializePropertyCalendar()
{
    var days = [];
    $("#edit-property-calendar .ib-calendar-day[data-date]").each(function(){
        var day = {};
        day.available = $(this).hasClass("date-selected") ? 0 : 1;
        day.date = $(this).data("date");
        days.push(day);
    });

    $("#edit-property-calendar [name=calendar]").val(JSON.stringify(days));
}

function unserializePropertyCalendar()
{
    var override_group_calendar = $("#override_group_calendar_yes").prop('checked');
    var calendar = [];
    if (override_group_calendar) {
        calendar = $("#edit-property-calendar [name=calendar]");
    } else {
        calendar = $("#edit-property-calendar [name=group_calendar]");
    }
    $("#edit-property-calendar .ib-calendar-day").removeClass("date-selected")
        .removeClass("date-selected-start")
        .removeClass("date-selected-end")
        .removeClass("date-selected-booked");
    if (calendar.length) {
        var days = [];
        try {
            days = JSON.parse(calendar.val());
        } catch(exc) {

        }
        var started = false;
        for (var i in days) {
            var day = days[i];
            if (day.available == 0) {
                var $date = $("#edit-property-calendar .ib-calendar-day[data-date='" + day.date + "']");
                $date.addClass("date-selected");
                if (!started) {
                    $date.addClass("date-selected-start");
                    started = true;
                }
            } else {
                if (started) {
                    $date.addClass("date-selected-end");
                    started = false;
                }
            }
        }
    }
    if (window.bookedDays) {
        for(var i in window.bookedDays) {
            $("#edit-property-calendar .ib-calendar-day[data-date='" + window.bookedDays[i] + "']").addClass("date-booked");
        }
    }
}

function serializeGroupCalendar()
{
    var days = [];
    $("#edit-group-calendar .ib-calendar-day[data-date]").each(function(){
        var day = {};
        day.available = $(this).hasClass("date-selected") ? 0 : 1;
        day.date = $(this).data("date");
        days.push(day);
    });

    $("#edit-group-calendar [name=calendar]").val(JSON.stringify(days));
}

function unserializeGroupCalendar()
{
    var calendar = $("#edit-group-calendar [name=calendar]");
    if (calendar.length) {
        var days = [];
        try {
            days = JSON.parse(calendar.val());
        } catch(exc) {

        }
        var started = false;
        for (var i in days) {
            var day = days[i];
            if (day.available == 0) {
                var $date = $("#edit-group-calendar .ib-calendar-day[data-date='" + day.date + "']");
                $date.addClass("date-selected");
                if (!started) {
                    $date.addClass("date-selected-start");
                    started = true;
                }
            } else {
                if (started) {
                    $date.addClass("date-selected-end");
                    started = false;
                }
            }
        }
    }
}

/* RateCard Calendar */
function serializeRatecardCalendar()
{
    var days = [];
    $("#edit-ratecard-calendar .ib-calendar-day[data-date]").each(function(){
        var day = {};
        day.available = $(this).hasClass("date-selected") ? 0 : 1;
        day.date = $(this).data("date");
        days.push(day);
    });

    $("#edit-ratecard-calendar [name=calendar]").val(JSON.stringify(days));
}
function unserializeRatecardCalendar()
{
    /*var calendar = window.ratecard_data.calendar;
    if (calendar.length) {
        var started = false;
        for (var i in calendar) {
            var day = calendar[i];
            var $date = $("#edit-ratecard-calendar .ib-calendar-day[data-date='" + day.date + "']");
            //$date.data("date-range", i);
            $date.addClass("date-selected");
        }
    }**/

}

function getWeekOfYear(date)
{
    var target = new Date(date.valueOf()),
        dayNumber = (date.getUTCDay() + 6) % 7,
        firstThursday;

    target.setUTCDate(target.getUTCDate() - dayNumber + 3);
    firstThursday = target.valueOf();
    target.setUTCMonth(0, 1);

    if (target.getUTCDay() !== 4) {
        target.setUTCMonth(0, 1 + ((4 - target.getUTCDay()) + 7) % 7);
    }

    return Math.ceil((firstThursday - target) /  (7 * 24 * 3600 * 1000)) + 1;
}

function getDateFromString(date)
{
    var dt = date.split('-');

    if (dt[0].length == 4) { // 2016-12-31
        return new Date(dt[0] + "-" + dt[1] + "-" + dt[2]);
    } else { // 31-12-2016
        return new Date(dt[2] + "-" + dt[1] + "-" + dt[0]);
    }
}

function testDateRangeConflict(range1, range2)
{
    var starts1 = getDateFromString(range1.starts);
    var ends1 = getDateFromString(range1.ends)
    var starts2 = getDateFromString(range2.starts)
    var ends2 = getDateFromString(range2.ends)

    if (starts2 >= starts1 && starts2 < ends1) {
        return true;
    } else if (ends2 > starts1 && ends2 <= ends1) {
        return true;
    }
    return false;
}

function setDefaultArrival()
{
    var $tr = $(this).parents("tr");
    var minstay = this.value == "High" ? window.propman_minstay_high : window.propman_minstay_low;
    var arrival = this.value == "High" ? window.propman_arrival_high : window.propman_arrival_low;
    $tr.find(".ratecard_dt_min_stay").val(minstay);
    $tr.find(".ratecard_dt_arrival").val(arrival);
}

function dateRangeEdit(i)
{
    var dateRange = window.ratecard_data.dateRanges[i];

    $("#bulk-change-rates-modal [name=starts]").val(dateRange['starts']);
    $("#bulk-change-rates-modal [name=ends]").val(dateRange['ends']);
    $("#bulk-change-rates-modal [name=weekly_price]").val(dateRange['weekly_price']);
    $("#bulk-change-rates-modal [name=short_stay_price]").val(dateRange['short_stay_price']);
    $("#bulk-change-rates-modal [name=additional_nights_price]").val(dateRange['additional_nights_price']);
    $("#bulk-change-rates-modal [name=min_stay]").val(dateRange['min_stay']);
    $("#bulk-change-rates-modal [name=pricing]").val(dateRange['pricing']);
    $("#bulk-change-rates-modal [name=discount]").val(dateRange['discount']);
    $("#bulk-change-rates-modal [name=arrival]").val(dateRange['arrival']);
    $("#bulk-change-rates-update").data("edit", i);

    $("#bulk-change-rates-modal").modal();
}

function dateRangeRemove(i)
{
    window.ratecard_data.dateRanges.splice(i, 1);
    $(".ib-calendar-day").each(function(){
        if (this.range == i) {
            if ($(this).hasClass("date-selected-start") && $(this).hasClass("date-selected-end")) { // end of a range; start of another
                $(this).removeClass("date-selected-start");
            } else {
                $(this).removeClass("date-selected-start");
                $(this).removeClass("date-selected-end");
                $(this).removeClass("date-selected");
                delete this.range;
            }
        }
    });
    ratecardDatatableRebuild();
}

function ratecardDatatableRebuild()
{
    var table = $("#ratecard-date-ranges-table");
    var tbody = $("#ratecard-date-ranges-table tbody")[0];

    $("#ratecard-date-ranges-table").dataTable().fnClearTable();

    var $dates = $(".ib-calendar-day[data-date]");
    for (var i = 0 ; i < window.ratecard_data.dateRanges.length ; ++i) {
        var dr = window.ratecard_data.dateRanges[i];

        var defaultPricing = $("#edit-ratecard-pricing").val();
        var arrival = defaultPricing == 'Low' ? window.propman_arrival_low : window.propman_arrival_high;
        var row = [];
        row[0] = '<input type="checkbox" name="ratecard_range[' + i + '][is_deal]" value="1" ' + (dr.is_deal == 1 ? 'checked="checked"' : '') + ' />';
        row[1] = '<input type="text" name="ratecard_range[' + i + '][starts]" value="' + dr.starts + '" size="4" data-date-format="dd-mm-yyyy" />';
        row[2] = '<input type="text" name="ratecard_range[' + i + '][ends]" value="' + dr.ends + '" size="4" data-date-format="dd-mm-yyyy"  />';
        row[3] = '<input type="text" name="ratecard_range[' + i + '][weekly_price]" value="' + dr.weekly_price + '" size="4" />';
        row[4] = '<input type="text" name="ratecard_range[' + i + '][short_stay_price]" value="' + dr.short_stay_price + '" size="4" />';
        row[5] = '<input type="text" name="ratecard_range[' + i + '][additional_nights_price]" value="' + dr.additional_nights_price + '" size="4" />';
        row[6] = '<input type="text" name="ratecard_range[' + i + '][min_stay]" value="' + dr.min_stay + '" size="4" />';
        row[7] = '<select class="ratecard_dt_pricing" name="ratecard_range[' + i + '][pricing]">' +
            '<option value="Low"' + (dr.pricing == "Low" ? 'selected="selected"' : '') + '>Low</option>' +
            '<option value="High"' + (dr.pricing == "High" ? 'selected="selected"' : '') + '>High</option>' +
            '</select>';
        row[8] = '<input type="text" name="ratecard_range[' + i + '][discount]" value="' + dr.discount + '" size="4" />';
        row[9] = '<select class="ratecard_dt_arrival" name="ratecard_range[' + i + '][arrival]">' +
            '<option value="Any"' + (dr.arrival == "Any" ? 'selected="selected"' : '') + '>Any</option>' +
            '<option value="Monday"' + (dr.arrival == "Monday" ? 'selected="selected"' : '') + '>Monday</option>' +
            '<option value="Tuesday"' + (dr.arrival == "Tuesday" ? 'selected="selected"' : '') + '>Tuesday</option>' +
            '<option value="Wednesday"' + (dr.arrival == "Wednesday" ? 'selected="selected"' : '') + '>Wednesday</option>' +
            '<option value="Thursday"' + (dr.arrival == "Thursday" ? 'selected="selected"' : '') + '>Thursday</option>' +
            '<option value="Friday"' + (dr.arrival == "Friday" ? 'selected="selected"' : '') + '>Friday</option>' +
            '<option value="Saturday"' + (dr.arrival == "Saturday" ? 'selected="selected"' : '') + '>Saturday</option>' +
            '<option value="Sunday"' + (dr.arrival == "Sunday" ? 'selected="selected"' : '') + '>Sunday</option>' +
            '</select>';
        row[10] = row[10] = '<a class="edit-link" title="Edit" data-range="' + i + '" onclick="dateRangeEdit(' + i + ');"><span class="icon-pencil"></span>Edit</a>' +
            '<a class="list-delete-button" title="Remove" data-range="' + i + '" onclick="dateRangeRemove(' + i + ');"><span class="icon-times"></span>Remove</a>';

        $("#ratecard-date-ranges-table").dataTable().fnAddData(row);

        var dstarts = getDateFromString(dr.starts);
        var dends = getDateFromString(dr.ends);

        $dates.each(function(){
            if (this.range == i) {
                if (this.date < dstarts || this.date > dends) {
                    delete this.range;
                    $(this).removeClass("date-selected-start");
                    $(this).removeClass("date-selected");
                    $(this).removeClass("date-selected-end");
                }
            }
        });
        for (var date = dstarts ; date <= dends ; date = new Date(date.setDate(date.getDate() + 1))) {
            var $date = $("[data-date='" + date.getYMD() + "'");
            $date.addClass("date-selected");
            $date[0].range = i;
            $date.removeClass("date-selected-start");
            $date.removeClass("date-selected-end");
            if (date == dstarts) {
                $date.addClass("date-selected-start");
            }
            if (date.getYMD() == dends.getYMD()) {
                $date.addClass("date-selected-end");
            }
        }
    }

    $("#ratecard-date-ranges-table [name*=starts], #ratecard-date-ranges-table [name*=ends]").datepicker();
    $(".ratecard_dt_pricing").off("change");
    $(".ratecard_dt_pricing").on("change", setDefaultArrival);
}

function setupCalendarPeriods(params)
{
    var onSelectHandler = null;
    var unselectOnClick = false;

    if (params && params.unselectOnClick) {
        unselectOnClick = params.unselectOnClick;
    }

    if (params && params.onSelect) {
        onSelectHandler = params.onSelect;
    }

    $(".ib-calendar-period").each(function(){
        var calendar = this;
        var $dates = $(this).find(".ib-calendar-day");
        var start = null;
        var end = null;

        function selectRange(date1, date2, isTemp)
        {
            var first = null;
            var last = null;

            if (date1 < date2) {
                first = date1;
                last = date2;
            } else {
                first = date2;
                last = date1;
            }

            $dates.each(function(){
                if ($(this).hasClass("date-range-temp")) {
                    $(this).removeClass("date-selected");
                    $(this).removeClass("date-selected-start");
                    $(this).removeClass("date-selected-end");
                }
                if (this.date == first) {
                    $(this).addClass("date-selected-start");
                }
                if (this.date == last) {
                    $(this).addClass("date-selected-end");
                }
                if (this.date >= first && this.date <= last) {
                    $(this).addClass("date-selected");
                    if (isTemp) {
                        $(this).addClass("date-range-temp");
                    } else {
                        $(this).removeClass("date-range-temp");
                        $(this).addClass("clear-on-cancel");
                    }
                }
            });

            if (!isTemp && onSelectHandler) {
                onSelectHandler(first, last);
            }
        }

        calendar.selectRange = selectRange;

        $dates.each(function(){
            var ddate = $(this).data("date");
            if (ddate) {
                this.date = new Date(clean_date_string(ddate));
            }
        }).on("click", function(){
            if (unselectOnClick && $(this).hasClass("date-selected") && !$(this).hasClass("date-range-temp")) {
                var date = $(this).data("date");
                var current = new Date(clean_date_string(date));

                var previous = new Date();
                var next = new Date();
                previous.setTime(current.getTime() - 86400000);
                next.setTime(current.getTime() + 86400000);
                $(this).removeClass("date-selected");
                $(this).removeClass("date-selected-start");
                $(this).removeClass("date-selected-end");


                if ($("[data-date=" + previous.getYMD() + "]").hasClass("date-selected")){
                    $("[data-date=" + previous.getYMD() + "]").addClass("date-selected-end");
                }
                if ($("[data-date=" + next.getYMD() + "]").hasClass("date-selected")){
                    $("[data-date=" + next.getYMD() + "]").addClass("date-selected-start");
                }
            } else {
                if (start == null) {
                    if ($(this).hasClass("date-selected") && !$(this).hasClass("date-range-temp") && !$(this).hasClass("date-selected-end")) {
                        window.editDateRange = this.range;
                        var rstart = null;
                        var rend = null;
                        $dates.each(function () {
                            if (this.range == window.editDateRange) {
                                if (rstart == null) {
                                    rstart = this;
                                } else {
                                    rend = this;
                                }
                            }
                        });
                        onSelectHandler(rstart.date, rend.date);
                    } else {
                        start = this;
                        console.log("start:" + this.date.getYMD());
                    }
                } else if (end == null) {
                    end = this;
                    console.log("end:" + this.date.getYMD());
                    console.log("selected: " + start.date.getYMD() + ' <-> ' + end.date.getYMD());
                    calendar.selectRange(start.date, end.date, false);
                    ////

                    start = null;
                    end = null;
                }
            }
        }).on("mouseover touchstart", function(){
            if (start != null){
                if (this.date) {
                    console.log("select: " + this.date.getYMD() + ' <-> ' + start.date.getYMD());
                    calendar.selectRange(start.date, this.date, true);
                }
            }
        });
    });

}

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
}

function checkPropertyGroupNameAvailable()
{
    var name = $("#edit-property-group-name").val();
    var id = $("#group-edit [name=id]").val();
    $.post(
        '/admin/propman/check_property_group_name_available',
        {
            name: name,
            id: id
        },
        function (response) {
            if (!response.available) {
                alert("Please use a different name");
            }
        }
    );
}

$(document).on("ready", function()
{

    $("#list-buildingtypes-table tbody tr .list-publish-button").on("click", function ()
    {
        var button = this;
        var id = $(button).parents("tr").data("id");
        var published = $(button).find(".icon-ok").length > 0 ? 0 : 1;
        $.post("/admin/propman/publish_buildingtype",
            {id: id, published: published},
            function (response)
            {
                if (published)
                {
                    $(button).find(".icon-times").removeClass("icon-times").addClass("icon-ok");
                    $("#list-buildingtypes-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Building Type #:'+id+' Published.</div>');
                    remove_popbox();
                } else {
                    $(button).find(".icon-ok").removeClass("icon-ok").addClass("icon-times");
                    $("#list-buildingtypes-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Building Type #:'+id+' Archived.</div>');
                    remove_popbox();
                }
            }
        );
    });

    $("#list-buildingtypes-table").on("click", ".list-delete-button", function(){
        var id = $(this).parents("tr").data("id");
        $("#delete-building-type [name=id]").val(id);
    });

    $("#list-propertytypes-table").on("click", ".list-publish-button", function(){
        var button = this;
        var id = $(button).parents("tr").data("id");
        var published = $(button).find(".icon-ok").length > 0 ? 0 : 1;
        $.post("/admin/propman/publish_propertytype",
                {id: id, published: published},
                function(response) {
                    if (published) {
                        $(button).find(".icon-times").removeClass("icon-times").addClass("icon-ok");
                        $("#list-propertytypes-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Property Type #:'+id+' Published.</div>');
                        remove_popbox();
                    } else {
                        $(button).find(".icon-ok").removeClass("icon-ok").addClass("icon-times");
                        $("#list-propertytypes-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Property Type #:'+id+' Archived.</div>');
                        remove_popbox();
                    }
                }
        );
    });

    $("#list-propertytypes-table").on("click", ".list-delete-button", function(){
        var id = $(this).parents("tr").data("id");
        $("#delete-property-type [name=id]").val(id);
    });

    $("#list-facilitygroups-table").on("click", ".list-publish-button", function(){
        var button = this;
        var id = $(button).parents("tr").data("id");
        var published = $(button).find(".icon-ok").length > 0 ? 0 : 1;
        $.post("/admin/propman/publish_facilitygroup",
                {id: id, published: published},
                function(response) {
                    if (published) {
                        $(button).find(".icon-times").removeClass("icon-times").addClass("icon-ok");
                        $("#list-facilitygroups-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Facility Group #:'+id+' Published.</div>');
                        remove_popbox();
                    } else {
                        $(button).find(".icon-ok").removeClass("icon-ok").addClass("icon-times");
                        $("#list-facilitygroups-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Facility Group #:'+id+' Archived.</div>');
                        remove_popbox();
                    }
                }
        );
    });

    $("#list-facilitygroups-table").on("click", ".list-delete-button", function(){
        var id = $(this).parents("tr").data("id");
        $("#delete-facilitygroup-modal [name=id]").val(id);
    });

    $("#delete-facilitygroup-modal").on("click", "#delete-facilitygroup-button", function ()
    {
        var id = $('#delete-facilitygroup-modal [name=id]').val();
        console.log(id);
        var $button = $("#list-facilitygroups-table tbody tr .list-delete-button[data-id=" + id + "]");

        $.ajax({
            type: 'POST',
            url: "/admin/propman/ajax_delete_facilitygroup",
            dataType: "json",
            data: {id: id}
        })
            .success(function (response)
            {
                if (response.status == 'success')
                {
                    $button.parents("tr").remove();
                    $("#list-facilitygroups-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> The Facility Group #:' + id + ' Deleted.</div>');
                    remove_popbox();
                }
                else if (response.status == 'in_use')
                {
                    $("#delete-facilitygroup-modal").modal('hide');
                    $('#delete-used-facilitygroup-modal [name=id]').val(id);
                    $('#delete-used-facilitygroup-modal').modal();
                }
            })
            .error(function ()
            {
            });
    });

    $('#delete-used-facilitygroup-modal #delete-used-facilitygroup-button').on('click', function ()
    {
        var id = $('#delete-facilitygroup-modal [name=id]').val();
        var $button = $("#list-facilitygroups-table tbody tr .list-delete-button[data-id=" + id + "]");

        $.post(
            "/admin/propman/delete_used_facilitygroup",
            {id: id},
            function (response)
            {
                $button.parents("tr").remove();
            }
        );
    });

    $("#list-facilitytypes-table tbody tr .list-publish-button").on("click", function ()
    {
        var button = this;
        var id = $(button).parents("tr").data("id");
        var published = $(button).find(".icon-ok").length > 0 ? 0 : 1;
        $.post("/admin/propman/publish_facilitytype",
            {id: id, published: published},
            function(response) {
                if (published) {
                    $(button).find(".icon-times").removeClass("icon-times").addClass("icon-ok");
                    $("#list-facilitytypes-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Facility #:'+id+' Published.</div>');
                    remove_popbox();
                } else {
                    $(button).find(".icon-ok").removeClass("icon-ok").addClass("icon-times");
                    $("#list-facilitytypes-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Facility #:'+id+' Archived.</div>');
                    remove_popbox();
                }
            }
        );
    });

    $("#list-facilitytypes-table").on("click", ".list-delete-button", function(){
        var id = $(this).parents("tr").data("id");
        $("#delete-facility-type [name=id]").val(id);
    });

    $("#list-suitabilitygroups-table").on("click", ".list-publish-button", function(){
        var button = this;
        var id = $(button).parents("tr").data("id");
        var published = $(button).find(".icon-ok").length > 0 ? 0 : 1;
        $.post("/admin/propman/publish_suitabilitygroup",
                {id: id, published: published},
                function(response) {
                    if (published) {
                        $(button).find(".icon-times").removeClass("icon-times").addClass("icon-ok");
                        $("#list-suitabilitygroups-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> The Suitability Group #:'+id+' Published.</div>');
                        remove_popbox();
                    } else {
                        $(button).find(".icon-ok").removeClass("icon-ok").addClass("icon-times");
                        $("#list-suitabilitygroups-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> The Suitability Group #:'+id+' Archived.</div>');
                        remove_popbox();
                    }
                }
        );
    });

    $("#list-suitabilitygroups-table").on("click", ".list-delete-button", function(){
        var id = $(this).parents("tr").data("id");
        $("#delete-suitability-group [name=id]").val(id);
    });

    $("#delete-suitabilitygroup-modal #delete-suitabilitygroup-button").on("click", function(){
        var id = $('#delete-suitabilitygroup-modal [name=id]').val();
        var $button = $("#list-suitabilitygroups-table tbody tr .list-delete-button[data-id="+id+"]");

        $.ajax({
            type: 'POST',
            url: "/admin/propman/ajax_delete_suitabilitygroup",
            dataType: "json",
            data: {id: id}
        })
            .success(function(response) {
                if (response.status == 'success') {
                    $button.parents("tr").remove();
                    $("#list-suitabilitygroups-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> The Suitability Group #:'+id+' Deleted.</div>');
                    remove_popbox();
                }
                else if (response.status == 'in_use') {
                    $("#delete-suitabilitygroup-modal").modal('hide');
                    $('#delete-used-suitabilitygroup-modal [name=id]').val(id);
                    $('#delete-used-suitabilitygroup-modal').modal();
                }
            })
            .error(function(){});
    });

    $('#delete-used-suitabilitygroup-modal #delete-used-suitabilitygroup-button').on('click', function() {
        var id = $('#delete-suitabilitygroup-modal [name=id]').val();
        var $button = $("#list-suitabilitygroups-table tbody tr .list-delete-button[data-id="+id+"]");

        $.post(
            "/admin/propman/delete_used_suitabilitygroup",
            {id: id},
            function(response) {
                $button.parents("tr").remove();
                $('#delete-used-suitabilitygroup-modal').modal('hide');
            }
        );
    });

    $("#list-suitabilitytypes-table").on("click", ".list-publish-button", function(){
        var button = this;
        var id = $(button).parents("tr").data("id");
        var published = $(button).find(".icon-ok").length > 0 ? 0 : 1;
        $.post("/admin/propman/publish_suitabilitytype",
                {id: id, published: published},
                function(response) {
                    if (published) {
                        $(button).find(".icon-times").removeClass("icon-times").addClass("icon-ok");
                        $("#list-suitabilitytypes-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Property Type #:'+id+' Published.</div>');
                        remove_popbox();
                    } else {
                        $(button).find(".icon-ok").removeClass("icon-ok").addClass("icon-times");
                        $("#list-suitabilitytypes-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Property Type #:'+id+' Archived.</div>');
                        remove_popbox();
                    }
                }
        );
    });

    $("#list-suitabilitytypes-table").on("click", ".list-delete-button", function(){
        var id = $(this).parents("tr").data("id");
        $("#delete-suitability-type [name=id]").val(id);
    });

    $("#list-periods-table").on("click", ".list-publish-button", function(){
        var button = this;
        var id = $(button).parents("tr").data("id");
        var published = $(button).find(".icon-ok").length > 0 ? 0 : 1;
        $.post("/admin/propman/publish_period",
            {id: id, published: published},
            function(response) {
                if (published) {
                    $(button).find(".icon-times").removeClass("icon-times").addClass("icon-ok");
                    $("#list-periods-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Period #:' + id + ' Published.</div>');
                    remove_popbox();
                } else {
                    $(button).find(".icon-ok").removeClass("icon-ok").addClass("icon-times");
                    $("#list-periods-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Period #:' + id + ' Archived.</div>');
                    remove_popbox();
                }
            });
        });

    $("#list-periods-table").on("click", ".list-delete-button", function(){
        var id = $(this).parents("tr").data("id");
        $("#delete-period [name=id]").val(id);
    });

    $("#list-property-groups-table").on("click", ".list-publish-button", function(){
        var button = this;
        var id = $(button).parents("tr").data("id");
        var published = $(button).find(".icon-ok").length > 0 ? 0 : 1;
        $.post("/admin/propman/publish_group",
            {id: id, published: published},
            function(response) {
                if (published) {
                    $(button).find(".icon-times").removeClass("icon-times").addClass("icon-ok");
                    $("#list-property-groups-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Property Group #:'+id+' Published.</div>');
                    remove_popbox();
                } else {
                    $(button).find(".icon-ok").removeClass("icon-ok").addClass("icon-times");
                    $("#list-property-groups-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Property Group #:'+id+' Archived.</div>');
                    remove_popbox();
                }
            }
        );
    });

    $("#list-property-groups-table").on("click", '.list-delete-button', function(){
        var id = $(this).parents("tr").data("id");
        $("#delete-property-group [name=id]").val(id);
    });

    function toggleDateSelector(e)
    {
        if ($(this).hasClass("date-selected")) {
            $(this).removeClass("date-selected");
        } else {
            $(this).addClass("date-selected");
        }
        serializePropertyCalendar();
        return false;
    }

	/*
    //$(".ib-calendar-day[data-date]").on("click", toggleDateSelector);
    $(".ib-calendar-day[data-date]").on("mousedown", toggleDateSelector);

    $(".ib-calendar-day[data-date]").on("mouseover", function(e){
        if (e.buttons) {
            $(this).addClass("date-selected");
            serializePropertyCalendar();
        }
        return false;
    });
    */

	/*
	 * Selecting date ranges
	 *
	 */
    if ($("#edit-ratecard-tab-calendar").length > 0) {
        setupCalendarPeriods({
            onSelect: function(date1, date2) {
                $("#bulk-change-rates-update").data("edit", window.editDateRange);
                $("#bulk-change-rates-modal [name=starts]").datepicker('setDate', date1);
                $("#bulk-change-rates-modal [name=ends]").datepicker('setDate', date2);
                $("#bulk-change-rates-modal").modal();
            }
        });
    } else {
        setupCalendarPeriods({unselectOnClick: true});
    }

    $("#edit-property-period, #edit-ratecard-period_id").on("change", function(){
        $(".ib-calendar-period").css("display", "none");
        $("#ib-calendar-period-" + this.value).css("display", "");
    });

    if ($("#edit-property-calendar").length > 0) {
        unserializePropertyCalendar();
    }

    if ($("#edit-group-calendar").length > 0) {
        unserializeGroupCalendar();
    }

    if ($("#ratecard-date-ranges-table").length > 0){
        unserializeRatecardCalendar();
        ratecardDatatableRebuild();
    }

    $("#edit-ratecard-period_id").on("change", function(){
        if (this.selectedIndex >= 0) {
            var $period = $(this.options[this.selectedIndex]);
            $("form[name='ratecard-edit'] [name=starts]").val($period.data("starts"));
            $("form[name='ratecard-edit'] [name=ends]").val($period.data("ends"));
        }
    });

    $("#list-ratecards-table").on("click", ".list-publish-button", function(){
        var button = this;
        var id = $(button).parents("tr").data("id");
        var published = $(button).find(".icon-ok").length > 0 ? 0 : 1;
        $.post("/admin/propman/publish_ratecard",
            {id: id, published: published},
            function(response) {
                if (published) {
                    $(button).find(".icon-times").removeClass("icon-times").addClass("icon-ok");
                    $("#list-ratecards-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Rate Card #:'+id+' Published.</div>');
                    remove_popbox();
                } else {
                    $(button).find(".icon-ok").removeClass("icon-ok").addClass("icon-times");
                    $("#list-ratecards-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Rate Card #:'+id+' Archived.</div>');
                    remove_popbox();
                }
            }
        );
    });

    $("#list-ratecards-table").on("click", ".list-delete-button", function(){
        var id = $(this).parents("tr").data("id");
        $("#delete-ratecard [name=id]").val(id);
    });

    $("#delete-ratecard-modal #delete-ratecard-button").on("click", function(){
        var id = $('#delete-ratecard-modal [name=id]').val();
        var $button = $("#list-ratecards-table tbody tr .list-delete-button[data-id="+id+"]");

        $.ajax({
            type: 'POST',
            url: "/admin/propman/ajax_delete_ratecard",
            dataType: "json",
            data: {id: id}
        })
            .success(function(response) {
                if (response.status == 'success') {
                    $button.parents("tr").remove();
                    $("#list-ratecards-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Rate Card #:'+id+' Deleted.</div>');
                    remove_popbox();
                }
                else if (response.status == 'in_use') {
                    $("#delete-ratecard-modal").modal('hide');
                    $('#delete-used-ratecard-modal [name=id]').val(id);
                    $('#delete-used-ratecard-modal').modal();
                }
            })
            .error(function(){});
    });

    $('#delete-used-ratecard-modal #delete-used-ratecard-button').on('click', function() {
        var id = $('#delete-ratecard-modal [name=id]').val();
        var $button = $("#list-ratecards-table tbody tr .list-delete-button[data-id="+id+"]");

        $.post(
            "/admin/propman/delete_used_ratecard",
            {id: id},
            function(response) {
                $button.parents("tr").remove();
                $("#list-ratecards-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Suitability group #:'+id+' Deleted. And associations removed.</div>');
                $('#delete-used-ratecard-modal').modal('hide');
                remove_popbox();
            }
        );
    });

    /*****
    $("#bulk-change-rates-update").on("click", function(){
        var weeklyPrice = $("#bulk-change-rates-weekly_price").val();
        var midweekPrice = $("#bulk-change-rates-midweek_price").val();
        var weekendPrice = $("#bulk-change-rates-weekend_price").val();
        var pricing = $("#bulk-change-rates-pricing").val();
        var discount = $("#bulk-change-rates-discount_price").val();

        $("#bulk-change-rates-weeks option:selected").each(function(){
            var date = this.value;
            $("#ratecard-weeks-table [name*='[" + date + "][weekly_price]']").val(weeklyPrice);
            $("#ratecard-weeks-table [name*='[" + date + "][midweek_price]']").val(midweekPrice);
            $("#ratecard-weeks-table [name*='[" + date + "][weekend_price]']").val(weekendPrice);
            $("#ratecard-weeks-table [name*='[" + date + "][pricing]']").val(pricing);
            $("#ratecard-weeks-table [name*='[" + date + "][discount]']").val(discount);
        })
    });
     */

    $("#bulk-change-rates-modal-button").on("click", function(){
        $("#bulk-change-rates-update").data("edit", "");
    });
    $("#bulk-change-rates-update").on("click", function(){
        var edit = parseInt($(this).data("edit"));
        var is_deal = $("#bulk-change-rates-is_deal").prop('checked') ? 1 : 0;
        var starts = $("#bulk-change-rates-starts").val();
        var ends = $("#bulk-change-rates-ends").val();
        var weeklyPrice = $("#bulk-change-rates-weekly_price").val();
        var shortStayPrice = $("#bulk-change-rates-short_stay_price").val();
        var minStay = $("#bulk-change-rates-min_stay").val();
        var additionalNightsPrice = $("#bulk-change-rates-additional_nights_price").val();
        var pricing = $("#bulk-change-rates-pricing").val();
        var discount = $("#bulk-change-rates-discount_price").val();
        var arrival = $("#bulk-change-rates-arrival").val();

        var conflict = false;
        var update = -1;
        for (var i = 0 ; i < window.ratecard_data.dateRanges.length ; ++i) {
            if (window.ratecard_data.dateRanges[i].starts == starts && window.ratecard_data.dateRanges[i].ends == ends) {
                update = i;
                break;
            }
            conflict = testDateRangeConflict(
                {starts: window.ratecard_data.dateRanges[i].starts, ends: window.ratecard_data.dateRanges[i].ends},
                {starts: starts, ends: ends}
            );
            if (conflict) {
                break;
            }
        }
        if (!isNaN(edit)) {
            update = edit;
            conflict = false;
        } else {
            edit = window.ratecard_data.dateRanges.length;
        }

        if (conflict) {
            $("#date-range-conflict-modal p").html(
                "There is a date range conflict: " +
                window.ratecard_data.dateRanges[i].starts + " - " + window.ratecard_data.dateRanges[i].ends
            );
            $("#date-range-conflict-modal").modal();
        } else {
            if (update >= 0) {
                window.ratecard_data.dateRanges[i] = {
                    is_deal: is_deal,
                    starts: starts,
                    ends: ends,
                    weekly_price: weeklyPrice,
                    short_stay_price: shortStayPrice,
                    additional_nights_price: additionalNightsPrice,
                    min_stay: minStay,
                    pricing: pricing,
                    discount: discount,
                    arrival: arrival
                };
            } else {
                window.ratecard_data.dateRanges.push(
                    {
                        is_deal: is_deal,
                        starts: starts,
                        ends: ends,
                        weekly_price: weeklyPrice,
                        short_stay_price: shortStayPrice,
                        additional_nights_price: additionalNightsPrice,
                        min_stay: minStay,
                        pricing: pricing,
                        discount: discount,
                        arrival: arrival
                    }
                );
            }
            ratecardDatatableRebuild();
        }

        $(".clear-on-cancel").removeClass("clear-on-cancel");
    });

    $("#bulk-change-rates-cancel").on("click", function(){
        $(".clear-on-cancel")
            .removeClass("date-selected date-selected-end date-selected-start clear-on-cancel");
    });

    $("#list-properties-table").on("click", ".list-publish-button", function(){
        var button = this;
        var id = $(button).parents("tr").data("id");
        var published = $(button).find(".icon-ok").length > 0 ? 0 : 1;
        $.post("/admin/propman/publish_property",
            {id: id, published: published},
            function(response) {
                if (published) {
                    $(button).find(".icon-times").removeClass("icon-times").addClass("icon-ok");
                    $("#list-properties-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Property #:'+id+' Published.</div>');
                    remove_popbox();
                } else {
                    $(button).find(".icon-ok").removeClass("icon-ok").addClass("icon-times");
                    $("#list-properties-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Property #:'+id+' Archived.</div>');
                    remove_popbox();
                }
            }
        );
    });

    $("#list-properties-table").on("click", ".list-delete-button", function(){
        var id = $(this).parents("tr").data("id");
        $("#delete-property [name=id]").val(id);
    });

    $("#link-sharedmedia-modal [data-media-id]").on("click", function(){
        if ($(this).hasClass("selected")) {
            $(this).removeClass("selected").css("border-style", "none");
        } else {
            $(this).addClass("selected").css("border-style", "solid");
        }
    });

    if ($("#edit-property-tab-photos").length > 0) {
        try {
			var md = new MediaDialog();
			md.onselect = function (media) {
				add_image_to_table(media.mediaId, media.thumbUrl, media.filename);
				md.hide();
			};

			/** Adding an existing image **/
			// Open image select modal when the button is clicked
			$('#edit-property-tab-photos').find('.add_existing_image_button').add_existing_image_button('properties', false);

			// When an image is clicked, add it to the table and dismiss the modal
			$(document).on('click', '.svg_thumb, .browse-images-noeditor .image_thumb', function()
			{
				var id       = this.getAttribute('data-id');
				var filepath = this.getElementsByTagName('img')[0].src;
				var filename = this.getElementsByClassName('filename')[0].innerHTML;
				add_image_to_table(id, filepath, filename);
				$('#browse_files_modal').modal('hide');
			});

			$("#edit-property-tab-photos .multi_upload_button").on("click", function () {
            	md.display("upload");
        	});
        } catch (exc) {

        }
    }

	// Add an image to photos list
	function add_image_to_table(id, filepath, filename)
	{
		var table = $("#edit-property-images-table");
		table.find("tbody").append(
			'<tr>' +
				'<td><span class="icon-bars"></span></td>' +
				'<td><img src="' + filepath + '" style="max-width: 50px; max-height: 50px;" /></td>' +
				'<td>' + filename + '</td>' +
				'<td>' +
					'<input type="hidden" name="shared_media_id[]" value="' + id + '" />' +
					'<button type="button" class="btn-link btn-remove" title="Remove">' +
						'<span class="icon-times"></span>' +
					'</button>' +
				'</td>' +
			'</tr>'
		);
	}

	// Remove an image from the photos list
	$("#edit-property-images-table").on('click', '.btn-remove', function()
	{
		$(this).parents("tr").remove();
	});

    $("#related-property-add-button").on("click", function(){
        var selectedProperty = $("#edit-property-select_related_property option:selected");
        var table = $("#edit-property-related-table");

        table.find("tbody").append(
            '<tr data-id="' + selectedProperty.val() + '">' +
                '<td title="Drag to reorder">' +
                    '<span class="icon-bars"></span>' +
                '</td>' +
                '<td></td>' +
                '<td>' + selectedProperty.data("id") + '</td>' +
                '<td>' + selectedProperty.data("refcode") + '</td>' +
                '<td>' + selectedProperty.data("name") + '</td>' +
                '<td>' +
                    '<input type="hidden" name="linked_property_id[]" value="' + selectedProperty.data("id") + '" />' +
                    '<button type="button" class="btn-remove" title="Remove">' +
                        '<span class="icon-times"></span>' +
                    '</button>' +
                '</td>' +
            '</tr>'
        );

        table.find(".btn-remove").off("click");
        table.find(".btn-remove").on("click", function(){
            $(this).parents("tr").remove();
        });
    });

    $("#edit-property-related-table .btn-remove").on("click", function(){
        $(this).parents("tr").remove();
    });

    $("#edit-property-details-tab-facilities li [name*=has_surcharge]").on("change", function(){
        if (this.value == "free") {
            $(this).parent().removeClass("yes");
            $(this).parent().find("input").prop("disabled", true);
        } else {
            $(this).parent().addClass("yes");
            $(this).parent().find("input").prop("disabled", false);
        }
    });

    $("#edit-group-tab-address #edit-group-country_id").on("change", function(){
        var counties = window.counties[this.value] ? window.counties[this.value] : [];
        $("#edit-group-tab-address #edit-group-county_id option").remove();
        var select = $("#edit-group-tab-address #edit-group-county_id")[0];
        for (var i in counties) {
            var option = new Option(counties[i], i);
            select.options[select.options.length] = option;
        }
    });

    $("#edit-property-tab-location #edit-property-country").on("change", function(){
        var counties = window.counties[this.value] ? window.counties[this.value] : [];
        $("#edit-property-tab-location #edit-property-county option").remove();
        var select = $("#edit-property-tab-location #edit-property-county")[0];
        for (var i in counties) {
            var option = new Option(counties[i], i);
            select.options[select.options.length] = option;
        }
    });

    $("form#property-edit button[name=action]").on("click", function(){
        serializePropertyCalendar();
    });

    $("form#group-edit button[name=action]").on("click", function(){
        serializeGroupCalendar();
    });

    $("#edit-group-properties-table").on("click", ".btn-remove", function(){
        var id = $(this).data("id");
        var button = this;
        $("#delete-property-modal [name=id]").val(id);
        $("#delete-property-modal .ref_code").html($(this).data("refcode"));
    });

    $("#delete-property-modal #delete-property-button").on("click", function(){
        var id = $("#delete-property-modal [name=id]").val();
        var $button = $("#edit-group-properties-table tbody tr .btn-remove[data-id=" + id + "]");

        $.post(
            "/admin/propman/delete_property",
            {id: id},
            function(response) {
                $button.parents("tr").remove();
                $("#list-properties-table_wrapper").prepend('<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Property #:'+id+' Deleted.</div>');
                remove_popbox();
            }
        );
    });

    if ($("#edit-group-tab-address").length > 0) {
        initMap("#edit-group-tab-address .map-container");
    }


    // this is a workaround. google map does not initialize correctly if the div is not visible. so initialize it
    // when the tab is activated
    if ($("#edit-property-tab-location").length > 0) {
        var propertyMapInit = true;
        $("a[href='#edit-property-tab-location']").on("click", function(){
            if (propertyMapInit) {
                propertyMapInit = false;
                setTimeout(function(){
                    initMap("#edit-property-tab-location .map-container");
                }, 1000);
            }
        });
    }

    $("#edit-property-tab-location [name=use_group_address]").on("change", function(){
        if ($("#edit-property-tab-location #use_group_address_yes").prop("checked")) {
            $.get(
                "/admin/propman/get_group",
                {id: $("#edit-property-details-tab-information #edit-property-group").val()},
                function (response) {
                    if (response) {
                        $("[name=address1]").val(response.address1);
                        $("[name=address2]").val(response.address2);
                        $("[name=country_id]").val(response.countryId);
                        $("#edit-property-tab-location #edit-property-country").change();
                        $("[name=county_id]").val(response.countyId);
                        $("[name=city]").val(response.city);
                        $("[name=eircode]").val(response.postcode);
                        $("[name=latitude]").val(response.latitude);
                        $("[name=longitude]").val(response.longitude);
                        $("[name=latitude]").change();
                    }
                }
            );
        }
    });

    if ($("#edit-group-tab-address").length > 0)
    {
        initMap("#edit-group-tab-address .map-container");
    }

    $("#link-property-rate-card-button").on("click", function(){
        var propertyId = $("[name='property-edit'] [name=id]").val();
        var propertyTypeId = $("[name='property-edit'] [name=property_type_id]").val();
        var ratecardId = $("#link-property-rate-card").val();
        if ($("#link-property-rate-card [value=" + ratecardId + "]").data("property-type-id") != propertyTypeId) {
            alert("Property Type does not match");
            return false;
        }

        var $tbody = $("#property-property-prices-table tbody");
        var $option = $("#link-property-rate-card option[value='" + ratecardId + "']");
        $tbody.append(
            '<tr>' +
            '<td><a href="/admin/propman/edit_rate_card/' + $option.val()+ '">' + $option.val()+ '</a></td>' +
            '<td><a href="/admin/propman/edit_rate_card/' + $option.data('id')+ '">' + $option.data('name')+ '</a></td>' +
            '<td>' + $option.data('weekly_price')+ '</td>' +
            '<td>' + $option.data('short_stay_price')+ '</td>' +
            '<td>' + $option.data('additional_nights_price')+ '</td>' +
            '<td>' + $option.data('min_stay')+ '</td>' +
            '<td>' + $option.data('pricing')+ '</td>' +
            '<td>' + $option.data('discount')+ '</td>' +
            '<td>' + $option.data('arrival')+ '</td>' +
            '<td>' +
                '<input type="hidden" name="has_ratecard_id[]" value="' + $option.val() + '" />' +
                '<button type="button" class="btn-link list-delete-button" title="Delete" onclick="$(this).parents(\'tr\').remove()"><span class="icon-times"></span></button>' +
            '</td>' +
            '</tr>'
        );
    });

    $("[name='property-edit'] [name=property_type_id]").on("change", function(){
        $("#link-property-rate-card option").css("display", "none");
        $("#link-property-rate-card option[data-property-type-id=" + this.value + "]").css("display", "");
    });

    $("#link-ratecard-group-button").on("click", function(){
        var ratecardId = $("[name='ratecard-edit'] [name=id]").val();
        var groupId = $("#link-ratecard-group").val();

        var $tbody = $("#ratecard-groups-table tbody");
        var $option = $("#link-ratecard-group option[value='" + groupId + "']");
        $tbody.append(
            '<tr>' +
            '<td><a href="/admin/propman/edit_group/' + $option.val()+ '">' + $option.val()+ '</a></td>' +
            '<td><a href="/admin/propman/edit_group/' + $option.data('id')+ '">' + $option.data('name')+ '</a></td>' +
            '<td>' +
            '<input type="hidden" name="has_group_id[]" value="' + $option.val() + '" />' +
            '<button type="button" class="btn-link list-delete-button" title="Delete" onclick="$(this).parents(\'tr\').remove()"><span class="icon-times"></span></button>' +
            '</td>' +
            '</tr>'
        );
    });

    /*
    $("#link-property-rate-card-button").on("click", function(){
        var propertyId = $("[name='property-edit'] [name=id]").val();
        var ratecardId = $("#link-property-rate-card").val();

        $.post(
            '/admin/propman/link_property_ratecard',
            {ratecard_id: ratecardId, property_id: propertyId},
            function (response) {
                if (!response) {
                    alert('Unable to link rate card');
                } else {
                    var $tbody = $("#property-property-prices-table tbody");
                    var $option = $("#link-property-rate-card option[value='" + ratecardId + "']");
                    $tbody.append(
                        '<tr>' +
                            '<td><a href="/admin/propman/edit_rate_card/' + $option.val()+ '">' + $option.val()+ '</a></td>' +
                            '<td><a href="/admin/propman/edit_rate_card/' + $option.data('id')+ '">' + $option.data('name')+ '</a></td>' +
                            '<td>' + $option.data('weekly_price')+ '</td>' +
                            '<td>' + $option.data('midweek_price')+ '</td>' +
                            '<td>' + $option.data('weekend_price')+ '</td>' +
                            '<td>' + $option.data('pricing')+ '</td>' +
                            '<td>' + $option.data('discount')+ '</td>' +
                            '<td>' +
                                '<input type="hidden" name="has_ratecard[]" value="' + $option.data('id') + '" />' +
                                '<button type="button" class="btn-link list-delete-button" title="Delete" onclick="$(this).parents(\'tr\').remove()"><span class="icon-times"></span></button>' +
                            '</td>' +
                        '</tr>'
                    );
                }
            }
        );
    });

    $("#property-property-prices-table tr .list-delete-button").on("click", function(){
        var ratecardId = $(this).data("ratecard-id");
        $("#delete-property-ratecard [name=ratecard_id]").val(ratecardId);
    });

    $("#delete-has-ratecard-button").on("click", function(){
        var propertyId = $("#delete-property-ratecard [name=property_id]").val();
        var ratecardId = $("#delete-property-ratecard [name=ratecard_id]").val();

        $.post(
            "/admin/propman/unlink_property_ratecard",
            {ratecard_id: ratecardId, property_id: propertyId},
            function (response) {
                $("tr[data-ratecard-id=" + ratecardId + "]").remove();
            }
        );
    });
    */

    $("#edit-suitabilitygroup-types-table").on("click", ".btn-remove", function(){
        var $tr = $(this).parents("tr");
        var name = $tr.find("[name='suitabilityType[]']").val();
        $("#delete-suitability-type-modal [name=name]").val(name);
        $("#delete-suitability-type-modal .name").html(name);
    });

    $("#delete-suitability-type-button").on("click", function(){
        var name = $("#delete-suitability-type-modal [name=name]").val();
        $("#edit-suitabilitygroup-types-table [value='" + name + "']").parents("tr").remove();
    });

    $("#edit-facility-types-table").on("click", ".btn-remove", function(){
        var $tr = $(this).parents("tr");
        var name = $tr.find("[name='facilityType[]']").val();
        $("#delete-facility-type-modal [name=name]").val(name);
        $("#delete-facility-type-modal .name").html(name);
    });

    $("#delete-facility-type-button").on("click", function(){
        var name = $("#delete-facility-type-modal [name=name]").val();
        $("#edit-facility-types-table [value='" + name + "']").parents("tr").remove();
    });

    $(".ratecard_dt_pricing").on("change", setDefaultArrival);

    $("#edit-property-group-name").on("change", checkPropertyGroupNameAvailable);

    $("[name=override_group_calendar]").on("change", function(){
        unserializePropertyCalendar();
    });

});

// Get a date in the format DD-MM-YYYY
Date.prototype.getDMY = function()
{
    var month = '' + (this.getMonth() + 1),
        day = '' + this.getDate(),
        year = this.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [day, month, year].join('-');
};

// Get a date in the format YYYY-MM-DD
Date.prototype.getYMD = function()
{
	var month = '' + (this.getMonth() + 1),
		day = '' + this.getDate(),
		year = this.getFullYear();

	if (month.length < 2) month = '0' + month;
	if (day.length < 2) day = '0' + day;

	return [year, month, day].join('-');
};

// Prevent form submissions when enter is clicked on an inline edit field
$(document).on('keypress', '.inline-edit-field:not(textarea)', function(ev) {
	if (ev.keyCode == 13)
	{
		ev.preventDefault();
		$(this).trigger('change'); // Treat clicking enter as changing the field
	}
});

// When the new item field is filled out, add the item to the table
$('.inline-edit-field-new').on('change', function()
{
	if (this.value != '')
	{
		var $table = $(this).parents('table');
		// There should be a template of the row available to clone
		var $template = $table.find('.inline-edit-template').clone();

		// Append the template to the tbody
		$table.find('tbody').append($template);
		// Change the text in the template to match the typed value
		$template.find('.inline-edit-field').val(this.value);
        $template.find('.inline-edit-field').attr("value", this.value);
		// Remove the template class from the clone, to make it visible
		$template.removeClass('inline-edit-template');

		this.value = '';
	}
});
