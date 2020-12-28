<?php
$has_daterangepicker = !empty($daterangepicker);
$has_status_filter   = !empty($status_filters);
?>

<?php if ($has_daterangepicker): ?>
    <link rel="stylesheet" href="<?= URL::get_engine_assets_base().'css/bootstrap.daterangepicker.min.css' ?>" />
<?php endif; ?>

<div class="alert_area"><?= isset($alert) ? $alert : IbHelpers::get_messages() ?></div>

<?php if (!empty($show_mine_only)): ?>
    <input type="hidden" name="show_mine_only" class="<?= $id_prefix ?>-table-filter" id="<?= $id_prefix ?>-show_mine_only" value="1" />
<?php endif; ?>

<?php if (!empty($action_button) || !empty($top_filter)): ?>
    <div class="form-row gutters">
        <?php if (!empty($top_filter)): ?>
            <div class="col-sm-4">
                <?= $top_filter ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($action_button)): ?>
            <div class="col-sm-4 right">
                <?= $action_button ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($has_daterangepicker || $has_status_filter): ?>
    <div class="form-row gutters vertically_center daterangepicker-main-wrapper">
        <?php if ($has_daterangepicker): ?>
            <div class="col-xs-12 <?= isset($filter_menu_options) ? 'col-sm-5' : ($has_status_filter ? 'col-sm-8' : '') ?>">
                <?php
                if (isset($views) && in_array('calendar', $views)) {
                    $daterange_start = date('Y-m-01'); // start of month
                    $daterange_end   = date('Y-m-t');  // end of month
                } else {
                    $daterange_start = isset($daterange_start) ? $daterange_start : date('Y-01-01');
                    $daterange_end   = isset($daterange_end)   ? $daterange_end   : date('Y-12-31');
                }

                echo Form::ib_daterangepicker(
                    'start_date',
                    'end_date',
                    $daterange_start,
                    $daterange_end,
                    ['class' => 'daterangepicker-main', 'id' => $id_prefix.'-daterangepicker']
                );
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($filter_menu_options)): ?>
            <div class="col-xs-12 col-sm-7">
                <?= Form::ib_filter_menu($filter_menu_options); ?>
            </div>
        <?php elseif ($has_status_filter): ?>
            <div class="col-xs-12 col-sm-4">
                <?= Form::ib_select('Status', 'statuses[]', $status_filters, '{all}', ['multiple' => 'multiple', 'class' => $id_prefix.'-table-filter'], ['multiselect_options' => ['includeSelectAllOption' => true, 'selectAllText' => __('ALL')]]); ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>