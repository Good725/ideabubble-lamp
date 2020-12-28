<style>
    .po-request-product-item:first-child .po-request-product-item-remove {display: none;}
</style>

<div id="purchasing-alert_area"></div>

<div class="form-horizontal">
    <div class="form-group">
        <div class="col-sm-8">
            <?php
            echo Form::ib_daterangepicker(
                'start_date', 'end_date', // field names
                date('Y-m-d', strtotime('this week')), date('Y-m-d', strtotime('next week')), // default range
                ['id' => 'purchasing-period'] // attributes
            );
            ?>
        </div>
        <div class="col-sm-4">
            <div class="purchasing-status-wrapper">
                <?php
                $statuses = array(
                    'Pending'   => __('Pending'),
                    'Approved'     => __('Approved'),
                    'Declined'      => __('Declined'),
                    'Purchased'      => __('Purchased'),
                );

                $attributes = array('multiple' => 'multiple', 'id' => 'purchasing_status');
                $args = array('multiselect_options' => array('enableHTML' => true, 'selectAllText' => __('ALL')));
                echo Form::ib_select('PO status', 'status[]', $statuses, null, $attributes, $args);
                ?>
            </div>
        </div>
    </div>
</div>

<div class="purchasing-overview-table-wrapper table hidden" id="purchasing-overview-table-wrapper" >
    <thead></thead>
    <tbody></tbody>
    <tfoot></tfoot>
</div>

<div class="purchasing-purchases-table-wrapper hidden" id="purchasing-purchases-table-wrapper">
    <table id="purchasing-purchases-table" class="table" data-fixed_filter="true">
        <thead>
            <tr>
                <th><?=__('Created')?></th>
                <th><?=__('Department')?></th>
                <th><?=__('PO No')?></th>
                <th><?=__('Reporter')?></th>
                <th><?=__('Supplier')?></th>
                <th><?=__('Total ex VAT')?></th>
                <th><?=__('Status')?></th>
                <th><?= __('Date required') ?></th>
                <th><?=__('Updated')?></th>
                <th><?=__('Actions')?></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>


<div class="hidden" id="purchasing-purchases-table-empty">
    <p>There are no records to display.</p>
</div>

<?php ob_start(); ?>
<div class="form-horizontal">
    <form clas="validate-on-submit" id="purchasing-request-form" name="purchasing-request-form" method="post">
        <input type="hidden" name="id" value="" />
        <div class="form-row vertically_center">
            <span class="icon-plus-circle text-primary" style="font-size: 3em;margin-right: .25em;"></span>
            <h1><?= __('Request a PO') ?></h1>

            <span class="label label-success hidden" id="purchasing-request-status-label" style="margin-left: .5em;"></span>
        </div>

        <div class="form-group">
            <div class="col-sm-6">
                <label class="control-label" for="po-request-department">Account reference</label>
                <?php
                $attributes = ['class' => 'ib-combobox validate[required]', 'id' => 'po-request-department', 'data-placeholder' => 'Department name'];
                $options = html::optionsFromRows('id', 'full_name', $departments, null, ['' => '']);
                echo Form::ib_select(null, 'department_id', $options, $selected, $attributes);
                ?>
            </div>
            
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label text-left" for="po-request-supplier">Supplier</label>
            </div>

            <div class="col-sm-6">
                <?php
                $attributes = ['class' => 'ib-combobox validate[required]', 'id' => 'po-request-supplier', 'data-placeholder' => 'Supplier name'];
                $options = html::optionsFromRows('id', 'full_name', $suppliers, null, ['' => '']);
                echo Form::ib_select(null, 'supplier_id', $options, $selected, $attributes);
                ?>
            </div>
        </div>

        <div id="po-request-products-wrapper"></div>

        <?php
        $amount_types = [
            ['name' => 'Weight', 'label' => 'Weight', 'unit' => 'kg',  'unit_name' => __('Kilograms')],
            ['name' => 'Volume', 'label' => 'Volume', 'unit' => 'lt',  'unit_name' => __('Litres')],
            ['name' => 'Unit',   'label' => 'Unit',   'unit' => 'Qty', 'unit_name' => __('Quantity')]
        ];
        $amount_type_options = '';
        foreach ($amount_types as $amount_type) {
            $amount_type_options .= '<option
                    value="'.$amount_type['name'].'"
                    data-unit="'.htmlspecialchars($amount_type['unit']).'"
                    data-unit_name="'.htmlspecialchars($amount_type['unit_name']).'"
                    data-price_per_text="'.htmlspecialchars(__('Price per X', ['X' => $amount_type['unit'] == 'Qty' ? 'unit' : $amount_type['unit']])).'"'.
                ($amount_type == 'Weight' ? ' selected="selected"' : '').'
                >'.$amount_type['label'].'</option>';
        }
        ?>

        <div class="form-group po-request-product-item hidden" id="po-request-product-item-template">
            <div class="col-sm-4">
                <input type="hidden" name="product[index][id]" class="product-id" value="" />
                <input type="hidden" name="product[index][inventory_item_id]" class="inventory_item_id" value="" />
                <label class="control-label text-left" for="po-request-product-item--index-product"><?=__('Item')?></label>

                <?php
                $attributes = [
                    'class' => 'product validate[required]',
                    'id' => 'po-request-product-item--index-product',
                    'placeholder' => 'Item name'
                ];
                echo Form::ib_input(null, null, null, $attributes);
                ?>
            </div>

            <div class="col-sm-2">
                <label class="control-label hidden--mobile">&nbsp;</label><?php // Empty space to keep vertically inline with other items in the row ?>
                <?php
                $attributes = ['class' => 'po-request-product-item-amount_type'];
                echo Form::ib_select(null, 'product[index][amount_type]', $amount_type_options, 'Unit', $attributes);
                ?>
            </div>

            <div class="col-sm-2">
                <label class="control-label text-left po-request-product-price-label" for="po-request-product-item--index-amount_price"><?= __('Price per X', ['X' => 'unit']) ?></label>
                <?php
                $attributes = [
                    'class' => 'po-request-product-price validate[required]',
                    'id' => 'po-request-product-item--index-amount_price',
                    'placeholder' => 'Price'
                ];
                $args = ['icon' => '€'];
                echo Form::ib_input(null, 'product[index][amount_price]', null, $attributes, $args);
                ?>
            </div>

            <div class="col-sm-2">
                <label class="control-label text-left" for="po-request-product-item--index-amount"><?=__('Amount') ?></label>
                <?php
                $attributes = [
                    'class' => 'po-request-product-amount validate[required]',
                    'id' => 'po-request-product-item--index-amount',
                    'placeholder' => 'Amount'
                ];
                $args = ['icon' => '<span title="Kilograms">kg</span>', 'icon_attributes' => ['class' => 'po-request-product-amount-icon']];
                echo Form::ib_input(null, 'product[index][amount]', null, $attributes, $args);
                ?>
            </div>

            <div class="col-sm-2">
                <label class="control-label text-left" for="po-request-product-item--index-line_total"><?=__('Line total') ?></label>
                <?php
                $attributes = [
                    'class' => 'po-request-product-line_total',
                    'id' => 'po-request-product-item--index-line_total',
                    'placeholder' => 'Total',
                    'readonly' => 'readonly'
                ];
                echo Form::ib_input(null, 'product[index][total]', null, $attributes, ['icon' => '€']);
                ?>
            </div>

            <div class="col-sm-12">
                <button type="button" class="btn-link text-primary po-request-product-item-remove" style="padding-left: 0;"><?= __('Remove item') ?></button>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <button type="button" class="btn btn-primary btn-lg" id="po-request-product-add"><?= __('Add item') ?></button>
            </div>
        </div>

        <div class="form-group">

            <div class="col-sm-4">
                <label class="control-label" for="po-request-product-total" ><?= __('Total price') ?> <strong>ex.VAT</strong></label>
                <?php
                $attributes = ['id' => 'po-request-product-total', 'placeholder' => 'Price ex. VAT', 'readonly' =>' readonly'];
                echo Form::ib_input(null, 'total', '', $attributes, ['icon' => '€']);
                ?>
            </div>

            <div class="col-sm-4">
                <label class="control-label" for="po-request-product-total_with_vat"><?= __('Total price') ?> <strong>inc.VAT</strong></label>
                <?php
                $attributes = ['id' => 'po-request-product-total_with_vat', 'placeholder' => 'Price inc. VAT', 'readonly' => 'readonly'];
                echo Form::ib_input(null, 'total_vat', '', $attributes, ['icon' => '€'] );
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4">
                <label class="control-label" for="po-request-product-date_required"><?= __('Date required') ?></label>

                <?php
                $display_attributes = ['id' => 'po-request-product-date_required', 'placeholder' => __('Date required')];
                $args = ['right_icon' => '<span class="icon-calendar"></span>'];
                echo Form::ib_datepicker(null, 'date_required', null, [], $display_attributes, $args);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4">
                <label class="control-label" for="po-request-product-reviewer"><?= __('Reviewer') ?></label>
                <?php
                $attributes = ['class' => 'ib-combobox', 'id' => 'po-request-product-reviewer', 'data-placeholder' => 'Reviewer name'];
                $options = html::optionsFromRows('id', 'name', $approvers, null, ['' => '']);
                echo Form::ib_select(null, 'reviewer_id', $options, $selected, $attributes);
                ?>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <div class="col-sm-4">
                <label class="control-label" for="comment"><?= __('Comment') ?></label>
                <?php
                $attributes = [
                    'id' => 'po-request-po_comment',
                    'data-placeholder' => 'Comment'
                ];
                echo Form::ib_textarea(__('Comment'), 'comment', '', $attributes);
                ?>
            </div>
        </div>
    </form>
</div>
<?php $modal_body = ob_get_clean(); ?>

<?php ob_start(); ?>
    <button type="button" class="btn btn-primary btn-lg save"><?= __('Save') ?></button>
    <button type="button" class="btn btn-default btn-lg approve purchasing-purchases-change_status"
            data-action="approve"><?= __('Approve') ?></button>
    <button type="button" class="btn btn-default btn-lg decline purchasing-purchases-change_status"
            data-action="decline"><?= __('Decline') ?></button>
    <button type="button" class="btn btn-primary btn-lg request"><?= __('Request PO') ?></button>
    <button type="button" class="btn-cancel btn-lg" data-dismiss="modal"><?= __('Cancel') ?></button>
<?php $modal_footer = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')
    ->set('id',     'purchasing-request-modal')
    ->set('size',   'lg')
    ->set('title',  __('Request a purchase order'))
    ->set('body',   $modal_body)
    ->set('footer', $modal_footer);
?>
