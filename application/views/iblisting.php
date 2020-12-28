<?php
$type_plural = isset($type_plural) ? $type_plural : $type.'s';
$views = isset($views) ? $views : ['overview']; // ['calendar', 'overview', 'details']
$has_daterangepicker = !empty($daterangepicker);
$has_status_filter   = !empty($status_filters);
?>


<div id="<?= $id_prefix?>-wrapper">
    <?php include 'snippets/filters_row.php'; ?>

    <?php if (in_array('calendar', $views)): ?>
        <?php $searchbar_on_top = isset($searchbar_on_top) ? $searchbar_on_top : false; ?>
        <div class="iblisting-view ibcalendar-wrapper" data-view="calendar" id="<?= $id_prefix ?>-calendar-wrapper">
            <?php
            include 'ibcalendar.php';
            ?>
        </div>
    <?php endif; ?>

    <?php if (in_array('overview', $views)): ?>
        <div class="iblisting-view<?= in_array('calendar', $views) ? ' hidden' : '' ?>" data-view="overview" id="<?= $id_prefix ?>-overview-wrapper">
            <?php if (!empty($reports)): ?>
                <div class="form-row gutters">
                    <div class="col-sm-12" id="<?= $id_prefix ?>-reports-wrapper">
                        <?php
                        $range_text = isset($daterange_start) && !$daterange_start &&
                            isset($daterange_end) && !$daterange_end ? 'All time' : date('Y');
                        echo View::factory('snippets/feature_reports')->set('reports', $reports)->set('date_range', $range_text);
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($columns)): ?>
                <div id="<?= $id_prefix ?>-table-section">
                    <div
                        <?= isset($checkbox_table) ? ' class="table-outer-checkboxes"' : '' ?>
                        id="<?= $id_prefix ?>-table-wrapper"
                    >
                        <table
                            class="table dataTable"
                            id="<?= $id_prefix ?>-table"
                            <?= isset($searchbar_on_top) && !$searchbar_on_top ? ' data-fixed_filter="1"' : '' ?>
                        >
                            <thead>
                            <tr>
                                <?php foreach ($columns as $column): ?>
                                    <th scope="col"><?= htmlspecialchars($column) ?></th>
                                <?php endforeach; ?>
                            </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="hidden" id="<?= $id_prefix ?>-table-empty">
                        <p><?= htmlspecialchars(__('There are no records to display.')) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php ob_start(); ?>
            <p><?= __('Are you sure you want to delete this $1?', ['$1' => $type]) ?></p>

            <div class="form-action-group text-center">
                <button type="button" class="btn btn-danger" id="<?= $id_prefix ?>-table-delete"><?= __('Delete') ?></button>
                <button type="button" class="btn btn-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
            </div>
        <?php $modal_body = ob_get_clean(); ?>


        <?php
        echo View::factory('snippets/modal')
            ->set('id',     $id_prefix.'-table-delete-modal')
            ->set('title',  'Delete '.$type)
            ->set('body',   $modal_body);
        ?>

        <?php
        if (in_array('calendar', $views)) {
            echo View::factory('admin/snippets/schedule_timeslot_conflicts');

            echo View::factory('snippets/modal')->set([
                'id'     => 'timetable-slot-remove-modal',
                'title'  => 'Confirm deletion',
                'body'   => 'Are you sure you wish to remove this timeslot?',
                'footer' => '
                    <button type="button" class="btn btn-danger" id="timetable-slot-remove-confirm" data-action="remove">Remove</button>
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>'
            ]);
        }
        ?>
    <?php endif; ?>
</div>

<?php if ($has_daterangepicker): ?>
    <script src="<?= URL::get_engine_assets_base().'js/bootstrap.daterangepicker.min.js' ?>"></script>
<?php endif; ?>

<?php if (in_array('overview', $views)): ?>
<?php
// This JavaScript needs to be put in a JS file and made object-oriented like timetable_view.js
?>
<script>
    $(document).ready(function() {
        // Toggle between calendar, details and overview modes
        $('.iblisting-mode-toggle').on('change', function() {
            var selected = $('.iblisting-mode-toggle:checked').val();
            $('.iblisting-view').addClass('hidden');
            $('.iblisting-view[data-view="'+selected+'"]').removeClass('hidden');
        });

        // Make the date range a filter
        $('#<?= $id_prefix ?>-daterangepicker-start_date').addClass('<?= $id_prefix ?>-table-filter');
        $('#<?= $id_prefix ?>-daterangepicker-end_date').addClass('<?= $id_prefix ?>-table-filter');

        var $table = $('#<?= $id_prefix ?>-table');

        var custom_table_settings = {};
        var $custom_table_filters  = $('.<?= $id_prefix ?>-table-filter');

        if ($custom_table_filters.length) {
            custom_table_settings.fnServerParams = function (aoData) {
                var value, i;

                // Below is very duplicative
                // It should be possible to use `Object.assign(aoData, get_filters())` to get the filter data instead.
                // However that results in an infinite loading icon the initial table draw.
                $('.<?= $id_prefix ?>-table-filter, .form-filter-selected-field').each(function (index, element) {
                    value = $(element).val();

                    if (value) {
                        if (Array.isArray(value) || element.name.indexOf('[') > -1) {
                            value = Array.isArray(value) ? value : [value];

                            for (i = 0; i < value.length; i++) {
                                aoData.push({name: 'filters['+(element.name.replace('[]', ''))+'][]', value: value[i] });
                            }
                        } else {
                            aoData.push({name: 'filters['+element.name+']', value: value });
                        }
                    }
                });
            };
        }

        function get_filters() {
            var value, i, filters = [];
            $('.<?= $id_prefix ?>-table-filter, .form-filter-selected-field').each(function (index, element) {
                value = $(element).val();

                if (value) {
                    if (Array.isArray(value) || element.name.indexOf('[') > -1) {
                        value = Array.isArray(value) ? value : [value];

                        for (i = 0; i < value.length; i++) {
                            filters.push({
                                name: 'filters[' + (element.name.replace('[]', '')) + '][]',
                                value: value[i]
                            });
                        }
                    } else {
                        filters.push({name: 'filters[' + element.name + ']', value: value});
                    }
                }
            });

            return filters;
        }

        <?php if (isset($default_order)): ?>
        // Set the default order of the table
        custom_table_settings.aaSorting = [
            [ <?= array_search($default_order, $columns) ?>, "<?= isset($default_dir) ? $default_dir : 'desc' ?>" ]
        ];
        <?php endif; ?>


        $table.ib_serverSideTable(
            '/admin/<?= $plugin ?>/ajax_get_item_datatable/<?= $type?>',
            custom_table_settings,
            {
                responsive: true,
                draw_callback: function() {
                    // Hide the DataTable if there are no results.
                    var has_records = ($table.dataTable().fnGetData().length > 0);
                    $('#<?= $id_prefix ?>-table-wrapper table, #<?= $id_prefix ?>-table-wrapper table ~ *').toggleClass('hidden', !has_records);
                    $('#<?= $id_prefix ?>-table-empty').toggleClass('hidden', has_records);
                }
            }
        );

        // Redraw the table when a filter is changed
        $('.<?= $id_prefix ?>-table-filter').on('change', function() {
            refresh_report_counters();
            refresh_details_tab();
            <?php if (!empty($columns)): ?>
            $table.dataTable().fnDraw();
            <?php endif; ?>
        });

        $('.form-filter').on(':ib-form-filter-change', function() {
            refresh_report_counters();
            refresh_details_tab();
            <?php if (!empty($columns)): ?>
            $table.dataTable().fnDraw();
            <?php endif; ?>
        });

        $('#<?= $id_prefix ?>-daterangepicker').on('apply.daterangepicker', function() {
            refresh_report_counters();
            refresh_details_tab();
            <?php if (!empty($columns)): ?>
                $table.dataTable().fnDraw();
            <?php endif; ?>
        });

        function refresh_report_counters()
        {
            var filters = get_filters();
            $.ajax({
                url: '/admin/<?= $plugin ?>/ajax_refresh_reports/<?= $type ?>',
                data: filters,
                dataType: 'json'
            }).done(function(result) {
                console.log(result);
                if (result.success) {
                    var $wrapper = $('#<?= $id_prefix ?>-reports-wrapper');
                    for (var i = 0; i < result.reports.length; i++) {
                        $wrapper.find('.timeoff-report:nth-child('+(parseInt(i)+1)+') .timeoff-report-amount').text(result.reports[i].amount);
                        $wrapper.find('.timeoff-report:nth-child('+(parseInt(i)+1)+') .timeoff-report-title').text(result.reports[i].text);
                    }
                }

                const start_date = $('#<?= $id_prefix?>-daterangepicker-start_date').val();
                const end_date   = $('#<?= $id_prefix?>-daterangepicker-end_date').val();
                const range_text = get_date_range_text(start_date, end_date);

                $wrapper.find('.timeoff-report-period').text(range_text || 'All time');
            });
        }

        function refresh_details_tab()
        {
            const $details = $('#<?= $id_prefix ?>-details');

            if ($details.length) {
                const filters = get_filters();

                $.ajax({
                    url: '/admin/<?= $plugin ?>/ajax_refresh_details/<?= $type ?>',
                    data: filters,
                    dataType: 'json'
                }).done(function(result) {
                    console.log(result);
                    $details.html(result);
                }).fail(function(result) {
                    $details.html(result.responseText);
                });
            }
        }

        $table.on('change', '.<?= $id_prefix ?>-table-publish', function() {
            var checkbox = this;
            var published = this.checked ? 1 : 0;
            var id = $(this).data('id');

            $.ajax('/admin/<?= $plugin ?>/ajax_toggle_publish_state/<?= $type ?>/'+id+'?published='+published).done(function(result) {
                $('.page-wrapper').add_alert(result.message, result.success ? 'success popup_box' : 'danger popup_box');

                // Failed => revert state
                if (!result.success) {
                    checkbox.checked = !published;
                }
            });
        });

        $('#<?= $id_prefix ?>-table-delete-modal').on('shown.bs.modal', function(ev) {
            var id = $(ev.relatedTarget).data('id');
            $('#<?= $id_prefix ?>-table-delete').data('id', id);
        });

        $('#<?= $id_prefix ?>-table-delete').on('click', function() {
            var id = $(this).data('id');
            $.ajax('/admin/<?= $plugin ?>/ajax_delete_item/<?= $type ?>/'+id).done(function(result) {
                $('.page-wrapper').add_alert(result.message, result.success ? 'success popup_box' : 'danger popup_box');

                // Refresh the data
                refresh_report_counters();
                refresh_details_tab();
                $table.dataTable().fnDraw();

                $('#<?= $id_prefix ?>-table-delete-modal').modal('hide');
            });
        });
    });
</script>
<?php endif; ?>

<?= isset($below) ? $below : '' ?>