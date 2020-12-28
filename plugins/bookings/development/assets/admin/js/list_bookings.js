$(document).ready(function()
{
    // Server-side datatable
    var $table = $('#list_bookings_table');
    $table.ready(function()
    {
            var settings = {
            "aoColumnDefs": [
                {
                    "aTargets": [1],
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol)
                    {
                        // Add data attribute, with the contact ID to each row
                        $(nTd).parent().attr({'data-id': oData[0]});
                    }
                },
                {"bSearchable": false, "aTargets": [9]}, {
                    "bSortable": false,
                    "aTargets": [$('#list_bookings_table').find('thead').first().find('th:contains(Outstanding)').index()]
                }
            ],
                "aaSorting": [[$('#list_bookings_table').find('thead').first().find('th:contains(Updated)').index(), 'desc']]
            };
            settings.fnDrawCallback = function()
                {
                    // temporary, until better permanent links are set up
                    // load the booking from the query string
                    var query_string     = window.location.search;
                    var query_array      = query_string.replace('?','').split('&');
                    var query_parameters = [];

                    for (var q = 0; q < query_array.length; q++)
                    {
                        var q_array = query_array[q].split('=');
                        query_parameters[q_array[0]] = q_array[1];
                    }

                    var booking_id = query_parameters['booking'];
                    if (booking_id != undefined && booking_id != '')
                    {
                        select_row(document.getElementById('list_bookings_table').querySelector('[data-id="'+booking_id+'"]'));
                        open_booking(booking_id);
                    }

                    var $headings = $table.find('thead:first th');
                    var column_number;

                    var $headings = $table.find('thead:first th');
                    var column_number;

                    $table.find("tbody > tr").each(function(){
                        var max_p_height = [];
                        var $td = $(this).find("td");
                        $td.each(function(){
                            var i = 0;
                            column_number = $(this).index();
                            $(this).data('label', $($headings[column_number]).text()).attr('data-label', $($headings[column_number]).text());
                            $(this).find("p").each(function(){
                                if (!max_p_height[i]) {
                                    max_p_height[i] = 0;
                                }
                                max_p_height[i] = Math.max(max_p_height[i], $(this).height());
                                ++i;
                            });
                        });

                        $td.each(function(){
                            var i = 0;
                            $(this).find("p").each(function(){
                                $(this).css("height", max_p_height[i] + "px");
                                ++i;
                            });
                        });
                    });
                };
            $table.ib_serverSideTable(
                '/admin/bookings/ajax_get_datatable',
                settings
            );

    });

    // Search by individual columns
    $table.find('.search_init').on('change', function ()
    {
        $table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this) );
    });
});

$('#list_bookings_table').on('click', 'tr[data-id]', function(ev)
{
	ev.preventDefault();
	select_row(this);
    var booking_id = $(this).closest('tr').data('id');
	open_booking(booking_id);
});

// Below the list of bookings, open the family form -> contact form -> booking form of the selected booking
function open_booking(booking_id)
{
    // Load the family form
    $('#family_menu_wrapper').load('/admin/bookings/ajax_display_family_details/', {
        booking_id: booking_id
    }, function()
    {
        // Select the contact
		var contact_id = $('#list_bookings_table').find('tr.selected .view_link').data('contact_id');
        load_contact(
            contact_id,
            { autoscroll: false },
            function() {
                // Select the booking
                open_contact_booking_tab(booking_id, { autoscroll: true });
            }
        );
        $('#list_family_members_table').dataTable({"aaSorting": []});
    });
}

function select_row(row)
{
    $(row).parents('tbody').find('tr').removeClass('selected');
    row.className += ' selected';
}