<div class="alert_area"><?= isset($alert) ? $alert : '' ?></div>

<div id="list_types_wrapper">
    <table id="list_types_table" class="table dataTable dataTable-collapse">
        <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
            <th scope="col">Publish</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
    </table>
</div>

<?php ob_start(); ?>
    <form action="#" method="post" class="form-horizontal" id="contact-type-form">
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label" for="contact-type-name">Name*</label>
            </div>
            <input type="hidden" id="model-contact-type-id" name="contact-type-id">
            <div class="col-sm-12">
                <?php
                $attributes = [
                    'class' => 'validate[required]',
                    'id' => 'contact-type-name',
                    'placeholder' => 'Contact type name'
                ];
                echo Form::ib_input(null, 'name', '', $attributes);
                ?>
            </div>
        </div>

    </form>
<?php $modal_body = ob_get_clean(); ?>

<?php ob_start(); ?>
    <div class="form-actions">
        <button type="button" class="btn btn-primary" id="contact-type-save">
            <span>Save</span>
        </button>
        <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
    </div>
<?php $modal_footer = ob_get_clean(); ?>


<?php
echo View::factory('snippets/modal')
    ->set('id', 'contact-type-modal')
    ->set('title', '<span class="contact-type-add_only hidden">' . __('Add contact type') . '</span>' .
        '<span class="contact-type-edit_only">' . __('Edit contact type') . '</span>')
    ->set('body', $modal_body)
    ->set('footer', $modal_footer);
?>


<?php ob_start(); ?>
    <p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected
        contact type. <span id="contact-type-delete-amount"></span> contacts will be moved to contact type Family.</p>
<?php $modal_body = ob_get_clean(); ?>

<?php ob_start(); ?>
    <a href="#" class="btn" data-dismiss="modal">Cancel</a>
    <a href="#" data-id="0" class="btn btn-danger" id="btn_delete_yes">Delete</a>
<?php $modal_footer = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')
    ->set('id', 'confirm_delete')
    ->set('title', ' <h3>Warning!</h3>')
    ->set('body', $modal_body)
    ->set('footer', $modal_footer);
?>