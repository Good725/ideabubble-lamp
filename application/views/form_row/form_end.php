<?= '</form>' ?>

<?php if ($form->has_delete_button()) : ?>
    <?php ob_start(); ?>
        <form action="<?= $form->delete_url ?>" method="get">
            <?php if (!empty($form->data_object->id)): ?>
                <input type="hidden" name="id" value="<?= $form->data_object->id ?>" />
            <?php endif; ?>

            <p>Are you sure you want to delete this <?= $form->subject ? $form->subject : 'item' ?>?</p>

            <div class="form-action-group text-center">
                <button type="submit" class="btn btn-danger"><?= htmlspecialchars(__('Delete')) ?></button>
                <button type="button" class="btn btn-cancel" data-dismiss="modal"><?= htmlspecialchars(__('Cancel')) ?></button>
            </div>
        </form>
    <?php $modal_body = ob_get_clean(); ?>

    <?= View::factory('snippets/modal')
        ->set('id',    $form->id.'-delete-modal')
        ->set('title', htmlspecialchars(__('Delete')))
        ->set('body',  $modal_body);
    ?>
<?php endif; ?>