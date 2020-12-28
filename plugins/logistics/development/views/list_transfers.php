<div class="alert_area"><?= isset($alert) ? $alert : '' ?></div>

<div class="form-row">
    <?php
    $start_of_month = date('Y-m-01');
    $end_of_month = date('Y-m-t');
    echo Form::ib_daterangepicker('start_date', 'end_date', $start_of_month, $end_of_month, ['id' => 'transfers-date_range']);
    ?>
</div>

<div id="transfers-table-wrapper">
    <table class="table dataTable" id="transfers-table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Title</th>
                <th scope="col">Type</th>
                <th scope="col">Passenger</th>
                <th scope="col">Driver</th>
                <th scope="col">Pickup</th>
                <th scope="col">Drop off</th>
                <th scope="col">Scheduled</th>
                <th scope="col">Updated</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
    </table>
</div>

<div class="hidden" id="transfers-table-empty">
    <p>There are no records to display.</p>
</div>

<?php ob_start(); ?>
    <form action="#" method="post" class="form-horizontal" id="transfer-form">
        <input type="hidden" name="id" id="transfer-id" />
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label" for="transfer-title">Title*</label>
            </div>

            <div class="col-sm-12">
                <?php
                $attributes = ['class' => 'validate[required]', 'id' => 'transfer-title', 'placeholder' => 'Enter title of transfer'];
                echo Form::ib_input(null, 'title', '', $attributes);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label" for="transfer-type">Type*</label>
            </div>

            <div class="col-sm-12">
                <?php
                $options = ['' => '-- Please select --', 'Arrival' => 'Arrival', 'Departure' => 'Departure'];
                $attributes = ['class' => 'validate[required]', 'id' => 'transfer-type'];
                echo Form::ib_select(null, 'type', $options, '', $attributes);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label" for="transfer-driver">Driver</label>
            </div>

            <div class="col-sm-12">
                <input type="hidden" id="transfer-driver_id" name="driver_id" />
                <?php
                $attributes = ['placeholder' => 'Search all contacts', 'class' => 'transfer-contact_ac', 'id' => 'transfer-driver'];
                echo Form::ib_input(null, null, '', $attributes, ['icon' => '<span class="icon-search"></span>']);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label" for="transfer-pickup">Pickup location</label>
            </div>

            <div class="col-sm-12">
                <?php
                $location_options = html::optionsFromRows('id', 'title', $locations, null, ['label' => '', 'value' => '']);
                $attributes = ['class' => 'ib-combobox', 'id' => 'transfer-pickup', 'data-placeholder' => __('Search locations')];
                $args = ['arrow_position' => 'none', 'icon' => '<span class="icon-search"></span>'];
                echo Form::ib_select(null, 'pickup_id', $location_options, null, $attributes, $args);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-6">
                <label class="control-label" for="transfer-date-input">Date*</label>

                <?php
                $attributes = ['id' => 'transfer-date'];
                $display_attributes = [
                    // Since transfers can only be viewed for a given date range...
                    // a transfer with no date will not be viewable...
                    // so its field is mandatory.
                    'class' => 'validate[required]',
                    'id' => 'transfer-date-input',
                    'placeholder' => '__/___/____'
                ];
                $args = ['right_icon' => '<span class="icon-calendar"></span>'];
                echo Form::ib_datepicker(null, 'date', null, $attributes, $display_attributes, $args) ?>
            </div>

            <div class="col-sm-6">
                <label class="control-label" for="transfer-time">Time</label>

                <?php
                $attributes = ['class' => 'form-timepicker', 'id' => 'transfer-time', 'placeholder' => '00:00', 'autocomplete' => 'off'];
                $args = ['right_icon' => '<span class="icon-clock-o"></span>'];
                echo Form::ib_input(null, 'time', null, $attributes, $args);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label" for="transfer-dropoff">Dropoff location</label>
            </div>

            <div class="col-sm-12">
                <?php
                $attributes = ['class' => 'ib-combobox', 'id' => 'transfer-dropoff', 'data-placeholder' => __('Search locations')];
                $args = ['arrow_position' => 'none', 'icon' => '<span class="icon-search"></span>'];
                echo Form::ib_select(null, 'dropoff_id', $location_options, null, $attributes, $args);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label" for="transfer-passenger">Passenger*</label>
            </div>

            <div class="col-sm-12">
                <input type="hidden" name="passenger_id" id="transfer-passenger_id" />

                <?php
                $attributes = ['placeholder' => 'Search all contacts', 'class' => 'transfer-contact_ac validate[required]', 'id' => 'transfer-passenger'];
                echo Form::ib_input(null, 'transfer-passenger', '', $attributes, ['icon' => '<span class="icon-search"></span>']);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label" for="transfer-booking">Passenger booking</label>

                <?= Form::ib_select(null, 'booking_id', ['' => '-- Please select --'], null, ['id' => 'transfer-booking']); ?>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <div class="col-sm-12">
                <label class="control-label" for="transfer-note">Note</label>

                <?= Form::ib_textarea(null, 'note', '', ['id' => 'transfer-note']); ?>
            </div>
        </div>
    </form>
<?php $modal_body = ob_get_clean(); ?>

<?php ob_start(); ?>
    <div class="form-actions">
        <span class="transfer-modal-add_only" style="margin-right: 5em;">
            <?= Form::ib_checkbox('Add another', null, 1, false, ['id' => 'transfer-modal-add_another']) ?>
        </span>

        <button type="button" class="btn btn-primary" id="transfer-save">
            <span class="transfer-modal-add_only hidden">Submit transfer</span>
            <span class="transfer-modal-edit_only">Save</span>
        </button>
        <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
    </div>
<?php $modal_footer = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')
    ->set('id',    'transfer-modal')
    ->set('title',  '<span class="transfer-modal-add_only hidden">'.__('Add transfer').'</span>'.
        '<span class="transfer-modal-edit_only">'.__('Edit transfer').'</span>')
    ->set('body',   $modal_body)
    ->set('footer', $modal_footer)
;
?>
