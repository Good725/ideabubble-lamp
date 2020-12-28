<style>
    .po-request-product-item:first-child .po-request-product-item-remove {display: none;}
</style>

<div id="inventory-alert_area"></div>

<div class="inventory-table-wrapper" id="inventory-table-wrapper" >
    <table id="inventory-table" class="table">
        <thead>
            <tr>
                <th><?=__('ID')?></th>
                <th><?=__('Title')?></th>
                <th><?=__('Category')?></th>
                <th><?=__('Measurement')?></th>
                <th><?=__('Usage')?></th>
                <th><?=__('VAT')?></th>
                <th><?=__('Added')?></th>
                <th><?=__('Updated')?></th>
                <th><?=__('Publish')?></th>
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
    <form id="inventory-edit-form" name="intentory-edit-form" method="post">
        <input type="hidden" name="id" value="" />
        <div class="form-row vertically_center">
            <span class="icon-plus-circle text-primary" style="font-size: 3em;margin-right: .25em;"></span>
            <h1><?= __('Add Item') ?></h1>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label" for="inventory-title"><?=__('Title')?></label>
                <?= Form::ib_input(null, 'title', '', ['placeholder' => 'Title', 'id' => 'inventory-title']) ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label" for="inventory-category"><?= __('Category') ?></label>
                <?php
                $attributes = ['class' => 'ib-combobox', 'id' => 'inventory-category', 'data-placeholder' => 'Category'];
                $options = html::optionsFromRows('id', 'category', $categories, null, ['value' => '', 'label' => '']);
                echo Form::ib_select(null, 'category_id', $options, '', $attributes);
                ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <div class="control-label text-left"><?=__('Use')?></div>

                <div class="btn-group" data-toggle="buttons" id="inventory-use">
                    <label class="btn btn-default active">
                        <input type="radio" checked="checked" value="Single" name="use" id="inventory-use-single"><?=__('Single')?>
                    </label>
                    <label class="btn btn-default">
                        <input type="radio" value="Multi" name="use" id="inventory-use-multi"><?=__('Multi')?>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label" for="inventory-amount_type"><?= __('Measurement') ?></label>
            </div>

            <div class="col-sm-4">
                <?php
                $attributes = ['id' => 'inventory-amount_type', 'data-placeholder' => 'Measurement'];
                $options = html::optionsFromArray(array('Weight' => __('Weight'), 'Volume' => __('Volume'), 'Unit' => __('Unit')), null);
                echo Form::ib_select(null, 'amount_type', $options, '', $attributes);
                ?>
            </div>

            <div class="col-sm-2">
                <?= Form::ib_input(null, null, null, ['id' => 'inventory-amount_type_detail']); ?>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <div class="col-sm-12">
                <div class="control-label text-left"><?=__('VAT')?></div>
                <div class="col-sm-2">
                <?php
                echo Form::ib_checkbox_switch('', 'vat_rate_enable', 1, false);
                ?>
                </div>

                <div class="vat_rate hidden col-sm-4">
                <?php
                $attributes = [
                    'placeholder' => __('VAT Rate'),
                ];
                $args = ['icon' => '<span title="Percent">%</span>'];
                echo Form::ib_input(null, 'vat_rate', null, $attributes, $args);
                ?>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $modal_body = ob_get_clean(); ?>

<?php ob_start(); ?>
    <?php if (Auth::instance()->has_access('inventory_edit')) { ?>
    <button type="button" class="btn btn-primary btn-lg save"><?= __('Save') ?></button>
    <?php } ?>
    <button type="button" class="btn-cancel btn-lg" data-dismiss="modal"><?= __('Cancel') ?></button>
<?php $modal_footer = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')
    ->set('id',     'inventory-item-modal')
    ->set('title',  '<span class="inventory-item-modal-add_only">'.__('Add Item').'</span>'.
                    '<span class="inventory-item-modal-edit_only hidden">'.__('View Item').'</span>')
    ->set('body',   $modal_body)
    ->set('footer', $modal_footer);
?>
