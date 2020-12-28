<style>
    .stock-item:first-child .stock-item-remove {display: none;}
</style>

<div id="stock-alert_area"></div>

<div class="form-horizontal">
    <?php if (empty($_GET['contact_id'])): ?>
        <div class="form-group">
            <div class="col-sm-4">
                <?php
                $options = html::optionsFromRows('id', 'title', $items, null);
                $attributes = ['multiple' => 'multiple', 'class' => 'stock-list-filter', 'id' => 'purchasing-filter-items'];
                $args = ['multiselect_options' => ['enableFiltering' => true, 'enableCaseInsensitiveFiltering' => true, 'includeSelectAllOption' => true, 'maxHeight' => 300]];
                echo Form::ib_select('Items', null, $options, null, $attributes, $args);
                ?>
            </div>

            <div class="col-sm-4">
                <?php
                $options = html::optionsFromRows('value', 'label', $locations, null);
                $attributes = ['multiple' => 'multiple', 'class' => 'stock-list-filter', 'id' => 'purchasing-filter-locations'];
                echo Form::ib_select('Locations', null, $options, null, $attributes, $args);
                ?>
            </div>

            <div class="col-sm-4">
                <button id="new-stock" class="btn btn-primary form-btn btn--full"><?=__('Add Stock')?></button>
            </div>
        </div>
    <?php else: ?>
        <div class="timeoff-header">
            <div class="timeoff-username">
                <img src="<?= URL::get_avatar($user['id']) ?>" alt="" width="40" height="40" style="margin-right: .5em;" />
                <strong><?= htmlentities($contact->get_full_name()) ?></strong>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <div class="col-sm-8">
            <?php
            echo Form::ib_daterangepicker(
                'start_date', 'end_date', // field names
                $start_date, $end_date, // default range
                ['id' => 'stock-period'] // attributes
            );
            ?>
        </div>
        <div class="col-sm-4">
            <div class="stock-status-wrapper">
                <?php
                $statuses = array(
                    'In Stock'    => __('In Stock'),
                    'Checked In'  => __('Checked In'),
                    'Checked Out' => __('Checked Out'),
                    'Lost'        => __('Lost'),
                );

                $attributes = array('multiple' => 'multiple', 'class' => 'stock-list-filter', 'id' => 'stock_status');
                $args = array('multiselect_options' => array('enableHTML' => true, 'selectAllText' => __('ALL')));
                echo Form::ib_select('Item status', 'status[]', $statuses, null, $attributes, $args);
                ?>
            </div>
        </div>
    </div>

    <div class="form-row">
        <?= View::factory('snippets/feature_reports')
            ->set('reports', $reports)
            ->set('date_range', date('j/M/Y', strtotime($start_date)).' - '.date('j/M/Y', strtotime($end_date)))
            ->set('attributes', ['class' => 'form-row', 'id' => 'inventory-stats'])
        ?>
    </div>
</div>

<div class="stock-table-wrapper" id="stock-table-wrapper" >
    <table id="stock-table" class="table">
        <thead>
            <tr>
                <th><?=__('Created')?></th>
                <th><?=__('Reporter')?></th>
                <th><?=__('Requested By')?></th>
                <th><?=__('Item')?></th>
                <th><?=__('Location')?></th>
                <th><?=__('Amount')?></th>
                <th><?=__('Available')?></th>
                <th><?=__('Status')?></th>
                <th><?=__('Updated')?></th>
                <th><?=__('Actions')?></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="hidden" id="stock-table-empty">
    <p>There are no records to display.</p>
</div>

<?php ob_start(); ?>
<div class="form-horizontal">
    <form id="add-stock-form" name="add-stock-form" method="post">
        <input type="hidden" name="id" value="" />
        <div class="form-row vertically_center">
            <span class="icon-plus-circle text-primary" style="font-size: 3em;margin-right: .25em;"></span>
            <h1><?= __('Add stock item') ?></h1>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label text-left" for="stock-purchasing_item"><?=__('Order')?></label>
            </div>

            <div class="col-sm-8">
                <input type="hidden" name="purchasing_item_id" id="stock-purchasing_item_id" value="" />

                <?php
                $attributes = [
                    'class' => 'product',
                    'id' => 'stock-purchasing_item',
                    'placeholder' => 'Order'
                ];
                echo Form::ib_input(null, 'purchasing_item', null, $attributes);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label text-left" for="stock-supplier"><?=__('Supplier')?></label>
            </div>

            <div class="col-sm-8">
                <input type="hidden" name="supplier_id" id="stock-supplier_id" value="" />

                <?php
                $attributes = [
                    'class' => 'product validate[required]',
                    'id' => 'stock-supplier',
                    'placeholder' => 'Supplier'
                ];
                echo Form::ib_input(null, 'stock-supplier', null, $attributes);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label text-left" for="stock-inventory"><?=__('Item')?></label>
            </div>

            <div class="col-sm-8">
                <input type="hidden" name="item_id" id="stock-item_id" value="" />

                <?php
                $attributes = [
                    'class' => 'product validate[required]',
                    'id' => 'stock-item',
                    'placeholder' => 'Item name'
                ];
                echo Form::ib_input(null, 'stock-item', null, $attributes);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label text-left" for="amount_type"><?=__('Measurement')?></label>
            </div>

            <div class="col-sm-4">
                <?php
                $attributes = ['id' => 'stock-amount_type', 'data-placeholder' => 'Measurement'];
                $options = html::optionsFromArray(array('Weight' => __('Weight'), 'Volume' => __('Volume'), 'Unit' => __('Unit')), null);
                echo Form::ib_select(null, 'amount_type', $options, '', $attributes);
                ?>
            </div>

            <div class="col-sm-4">
                <?php
                $attributes = [
                    'class' => 'po-request-product-amount validate[required]',
                    'id' => 'po-request-product-item--index-amount',
                    'placeholder' => 'Amount'
                ];
                $args = ['icon' => '<span title="Kilograms">kg</span>', 'icon_attributes' => ['class' => 'po-request-product-amount-icon']];
                echo Form::ib_input(null, 'amount', null, $attributes, $args);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label text-left" for="expiry_date"><?=__('Expiry Date')?></label>
            </div>

            <div class="col-sm-8">
                <?php
                $display_attributes = ['id' => 'expiry_date', 'placeholder' => __('Expiry Date')];
                $args = ['right_icon' => '<span class="icon-calendar"></span>'];
                echo Form::ib_datepicker(null, 'expiry_date', null, [], $display_attributes, $args);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label text-left" for="stock-location"><?=__('Location')?></label>
            </div>

            <div class="col-sm-8">
                <input type="hidden" name="location_id" id="stock-location_id" value="" />

                <?php
                $attributes = [
                    'class' => 'product validate[required]',
                    'id' => 'stock-location',
                    'placeholder' => 'Location'
                ];
                echo Form::ib_input(null, 'stock-location', null, $attributes);
                ?>
            </div>
        </div>

    </form>
</div>
<?php $modal_body = ob_get_clean(); ?>

<?php ob_start(); ?>
    <button type="button" class="btn btn-primary btn-lg save"><?= __('Save') ?></button>
    <button type="button" class="btn-cancel btn-lg" data-dismiss="modal"><?= __('Cancel') ?></button>
<?php $modal_footer = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')
    ->set('id',     'stock-modal')
    ->set('title',  __('Add Stock Item'))
    ->set('body',   $modal_body)
    ->set('footer', $modal_footer);
?>

<!-- -->

<?php ob_start(); ?>
<div class="form-horizontal">
    <form id="checkin-form" name="checkin-form" method="post">
        <input type="hidden" name="id" value="" />
        <div class="form-row vertically_center">
            <span class="icon-sign-in text-primary" style="font-size: 3em;margin-right: .25em;"></span>
            <h1><?= __('Check In') ?></h1>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label text-left" for="checkin-requestee"><?=__('Requestee')?></label>
            </div>

            <div class="col-sm-8">
                <input type="hidden" name="requestee_id" id="checkin-requestee_id" value="" />

                <?php
                $attributes = [
                    'class' => 'product validate[required]',
                    'id' => 'checkin-requestee',
                    'placeholder' => 'Requestee'
                ];
                echo Form::ib_input(null, 'checkin-requestee', null, $attributes);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label text-left" for="checkin-stock"><?=__('Item')?></label>
            </div>

            <div class="col-sm-8">
                <input type="hidden" name="stock_id" id="checkin-stock_id" value="" />
                <input type="hidden" name="checkout_id" />

                <?php
                $attributes = [
                    'class' => 'product validate[required]',
                    'id' => 'checkin-stock',
                    'placeholder' => 'Order',
                    'readonly' => 'readonly'
                ];
                echo Form::ib_input(null, 'checkin-stock', null, $attributes);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4">
                <label class="control-label text-left" for="amount_type"><?=__('Measurement')?></label>

                <?php
                $options = html::optionsFromArray(array('Weight' => __('Weight'), 'Volume' => __('Volume'), 'Unit' => __('Unit')), null);
                echo Form::ib_select(null, 'amount_type', $options, null, ['id' => 'checkin-amount_type']);
                ?>
            </div>

            <div class="col-sm-4">
                <div class="control-label text-left"><?=__('Available')?></div>
                <span id="checkin-available" class="po-request-product-amount-available" style="margin-top: .5em;">&nbsp;</span>
            </div>

            <div class="col-sm-4">
                <label class="control-label text-left amount_label" for="amount"><?=__('Amount')?></label>
                <br />
                <?php
                $attributes = [
                    'class' => 'po-request-product-amount',
                    'id' => 'po-request-product-item--index-amount',
                    'placeholder' => 'Amount'
                ];
                $args = [];
                echo Form::ib_input(null, 'amount', null, $attributes, $args);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label text-left" for="checkin-location_id"><?=__('Location')?></label>
            </div>

            <div class="col-sm-8">
                <input type="hidden" name="location_id" id="checkin-location_id" value="" />

                <?php
                $attributes = [
                    'class' => 'product validate[required]',
                    'id' => 'checkin-location',
                    'placeholder' => 'Location'
                ];
                echo Form::ib_input(null, 'checkin-location', null, $attributes);
                ?>
            </div>
        </div>

    </form>
</div>
<?php $modal_body = ob_get_clean(); ?>

<?php ob_start(); ?>
<button type="button" class="btn btn-primary btn-lg save"><?= __('Save') ?></button>
<button type="button" class="btn-cancel btn-lg" data-dismiss="modal"><?= __('Cancel') ?></button>
<?php $modal_footer = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')
    ->set('id',     'checkin-modal')
    ->set('title',  __('Check In'))
    ->set('body',   $modal_body)
    ->set('footer', $modal_footer);
?>

<!-- -->

<?php ob_start(); ?>
<div class="form-horizontal">
    <form id="checkout-form" name="checkout-form" method="post">
        <input type="hidden" name="id" value="" />
        <div class="form-row vertically_center">
            <span class="icon-sign-out-circle text-primary" style="font-size: 3em;margin-right: .25em;"></span>
            <h1><?= __('Check Out') ?></h1>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label text-left" for="checkout-requestee"><?=__('Requestee')?></label>
            </div>

            <div class="col-sm-8">
                <input type="hidden" name="requestee_id" id="checkout-requestee_id" value="" />

                <?php
                $attributes = [
                    'class' => 'product validate[required]',
                    'id' => 'checkout-requestee',
                    'placeholder' => 'Requestee'
                ];
                echo Form::ib_input(null, 'checkout-requestee', null, $attributes);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label text-left" for="checkout-stock"><?=__('Item')?></label>
            </div>

            <div class="col-sm-8">
                <input type="hidden" name="stock_id" id="checkout-stock_id" value="" />

                <?php
                $attributes = [
                    'class' => 'product validate[required]',
                    'id' => 'checkout-stock',
                    'placeholder' => 'Order'
                ];
                echo Form::ib_input(null, 'stock', null, $attributes);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4">
                <label class="control-label text-left" for="amount_type"><?=__('Measurement')?></label>

                <?php
                $options = html::optionsFromArray(array('Weight' => __('Weight'), 'Volume' => __('Volume'), 'Unit' => __('Unit')), null);
                echo Form::ib_select(null, 'amount_type', $options, null, ['id' => 'checkout-amount_type']);
                ?>
            </div>

            <div class="col-sm-4">
                <div class="control-label text-left"><?=__('Available')?></div>
                <div id="checkout-available" class="po-request-product-amount-available" style="margin-top: .5em;">&nbsp;</div>
            </div>

            <div class="col-sm-4">
                <label class="control-label text-left amount_label" for="amount"><?=__('Amount')?></label>
                <?php
                $attributes = [
                    'class' => 'po-request-product-amount',
                    'id' => 'po-request-product-item--index-amount',
                    'placeholder' => 'Amount'
                ];
                $args = [];
                echo Form::ib_input(null, 'amount', null, $attributes, $args);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label text-left" for="checkout-location_id"><?=__('Location')?></label>
            </div>

            <div class="col-sm-8">
                <input type="hidden" name="location_id" id="checkout-location_id" value="" />

                <?php
                $attributes = [
                    'class' => 'product validate[required]',
                    'id' => 'checkout-location',
                    'placeholder' => 'Location'
                ];
                echo Form::ib_input(null, 'checkout-location', null, $attributes);
                ?>
            </div>
        </div>

    </form>
</div>
<?php $modal_body = ob_get_clean(); ?>

<?php ob_start(); ?>
<button type="button" class="btn btn-primary btn-lg save"><?= __('Save') ?></button>
<button type="button" class="btn-cancel btn-lg" data-dismiss="modal"><?= __('Cancel') ?></button>
<?php $modal_footer = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')
    ->set('id',     'checkout-modal')
    ->set('title',  __('Check Out'))
    ->set('body',   $modal_body)
    ->set('footer', $modal_footer);
?>
