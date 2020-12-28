<div class="alert_area"><?= isset($alert) ? $alert : '' ?></div>

<div id="locations-table-wrapper">
    <table class="table dataTable" id="locations-table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Title</th>
                <th scope="col">City</th>
                <th scope="col">County</th>
                <th scope="col">Modified</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
    </table>
</div>

<div class="hidden" id="locations-table-empty">
    <p>There are no records to display.</p>
</div>

<?php ob_start(); ?>
    <form action="#" method="post" class="form-horizontal" id="location-modal-form">
        <input type="hidden" name="id" id="location-modal-id" />

        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label" for="location-modal-title">Title*</label>

                <?= Form::ib_input(null, 'title', null, ['class' => 'validate[required]', 'id' => 'location-modal-title']) ?>
            </div>
        </div>

        <?php
        echo View::factory('address_fields')
            ->set('id_prefix',     'location-modal-')
            ->set('town_field',     false)
            ->set('postcode_field', false)
            ->set('counties',       html::optionsFromRows('name', 'name', $counties, null, ['value' => '', 'label' => '']))
            ->set('cities',         html::optionsFromRows('id',   'name', $cities,   null, ['value' => '', 'label' => '-- Please select --']))
        ;
        ?>

        <div class="form-group mb-0">
            <div class="col-sm-12">
                <label class="control-label" for="location-modal-note">Note</label>

                <?= Form::ib_textarea(null, 'note', '', ['id' => 'location-modal-note']); ?>
            </div>
        </div>
    </form>
<?php $modal_body = ob_get_clean(); ?>

<?php ob_start(); ?>
    <span class="location-modal-add_only" style="margin-right: 5em;">
        <?= Form::ib_checkbox('Add another', null, 1, false, ['id' => 'location-modal-add_another']) ?>
    </span>

    <button type="button" class="btn btn-primary" id="location-modal-save">
        <span class="location-modal-add_only hidden">Submit location</span>
        <span class="location-modal-edit_only">Save</span>
    </button>
    <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
<?php $modal_footer = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')
    ->set('id',     'location-modal')
    ->set('title',
        '<span class="location-modal-add_only hidden">'.__('Add location').'</span>'.
        '<span class="location-modal-edit_only">'.__('Edit location').'</span>')
    ->set('body',   $modal_body)
    ->set('footer', $modal_footer)
;
?>

<?php ob_start(); ?>
    <button type="button" class="btn btn-danger" id="location-delete-btn"><?= __('Delete') ?></button>
    <button type="button" class="btn btn-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
<?php $modal_footer = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')
    ->set('id',     'location-delete-modal')
    ->set('title',  'Delete location')
    ->set('body',   __('Are you sure you wish to delete this location?'))
    ->set('footer', $modal_footer)
;
?>