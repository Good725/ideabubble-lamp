$(document).ready(function () {
    var $table = $('#list_dashboards_table');

    // Server-side datatable
    $table.ready(function () {
        var ajax_source = '/admin/dashboards/ajax_get_datatable';
        var settings = {
            "bAutoWidth": true,
            "bSearchable": true,
            "sPaginationType": "bootstrap",
            "aoColumnDefs": [{
                "aTargets": [1],
                "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    // Add data attribute, with the contact ID to each row
                    $(nTd).parent().attr({'data-id': oData[0]});
                }
            }]
        };
        var drawback_settings = {
            "fnDrawCallback": function () {
                $table.find('[data-toggle="tooltip"]').tooltip();
            }
        };
        $table.ib_serverSideTable(ajax_source, settings, drawback_settings);

    });

    // Search by individual columns
    $table.find('.search_init').on('change', function () {
        $table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this));
    });

    // When a table row is clicked, open the corresponding dashboard
    $table.on('click', 'tbody tr :not(a):not(:input):not(label)', function (ev) {
        // If the clicked element is a link or form element, don't open the dashboard. Let the link/form element do its own thing.
        if (!$(ev.target).parents('a, label, :input')[0]) {
            var $link = $(this).parents('tr').find('.dashboard_link');
            if ($link.hasClass('dashboard_link_disabled')) {
                ev.preventDefault();
                $('#dashboard-permission-error-modal').modal();
            } else {
                location.href = $link[0].href;
            }
        }
    });

    // Toggle favourite
    var toggling_favorite = false;
    $table.on('change', '.toggle_favorite', function () {
        // Stop event firing more than once
        if (toggling_favorite) {
            toggling_favorite = false;
            return '';
        }
        var checkbox = this;
        var id = $(this).parents('tr').data('id');
        var favorite = this.checked ? 1 : 0;
        var status = (favorite) ? 'favourited' : 'unfavourited';
        $.ajax('/admin/dashboards/ajax_toggle_favorite/' + id + '?is_favorite=' + favorite)
            .done(function (saved) {
                if (saved == 1) {
                    set_message('Dashboard #' + id + ' successfully ' + status + '.', 'success');
                } else {
                    set_message('Failed to ' + status + ' dashboard #' + id + '.', 'danger');
                    checkbox.checked = (!checkbox.checked); // undo checkbox toggle
                }
            })
            .fail(function () {
                set_message('Failed to ' + status + ' dashboard #' + id + '.', 'danger');
                checkbox.checked = (!checkbox.checked); // undo checkbox toggle
            });
        toggling_favorite = true;
    });

    // Deleting a dashboard
    // When the delete icon is clicked, open the modal window
    $table.on('click', '.list_dashboards_delete', function (ev) {
        ev.preventDefault();
        document.getElementById('delete_dashboard_button').setAttribute('data-id', this.getAttribute('data-id'));
        $('#delete_dashboard_modal').modal();
    });

    // When the delete button in the modal box is clicked
    $('#delete_dashboard_button').on('click', function () {
        var id = this.getAttribute('data-id');
        // AJAX call to delete the record
        $.ajax('/admin/dashboards/ajax_delete_dashboard/' + id)
            .done(function (result) {
                if (result == 1) {
                    // display success message and refresh the table
                    set_message('Dashboard #' + id + ' successfully deleted.', 'success');
                    $table.find('.search_init').trigger('change'); // force a refresh
                } else {
                    // display failure message
                    set_message('Failed to delete dashboard #' + id + '.', 'danger');
                }

                // dismiss the modal box
                $('#delete_dashboard_modal').modal('hide')
            });
    });


    function set_message(message, type) {
        document.getElementById('dashboard_list_alerts').innerHTML += '<div class="alert alert-' + type + '">' + message + '<a href="#" class="close" data-dismiss="alert">&times;</a></div>';
    }

});
