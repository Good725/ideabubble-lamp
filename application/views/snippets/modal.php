<div class="modal" tabindex="-1" role="dialog"<?= !empty($id) ? 'id="'.$id.'"' : '' ?>>
    <div class="modal-dialog<?= !empty($size) ? ' modal-'.$size : '' ?>" role="document">
        <div class="modal-content">
            <?php if (!empty($title)): ?>
                <div class="modal-header">
                    <?php if (!isset($close_button) || !$close_button): ?>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    <?php endif; ?>

                        <h5 class="modal-title"><?= $title ?></h5>
                </div>
            <?php endif; ?>

            <?php if (!empty($body)): ?>
                <div class="modal-body">
                    <?= $body ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($footer)): ?>
                <div class="modal-footer text-center">
                    <?= $footer ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>