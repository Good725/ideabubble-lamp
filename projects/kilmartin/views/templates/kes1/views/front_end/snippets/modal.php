<div
    class="sectionOverlay<?= isset($class) ? ' '.$class : '' ?>"
    id="<?= $id ?>"
    style="display: none;"
    role="dialog"
    aria-hidden="true"
    <?php if (!empty($id) && !empty($body)): ?>
        aria-labelledby="<?= $id ?>-title"
    <?php endif; ?>
    >
    <div class="overlayer"></div>

    <div class="screenTable">
        <div class="screenCell">
            <div class="sectioninner zoomIn small-width"<?= $width ? ' style="max-width: '.$width.';"' : '' ?>>
                <?php if (!empty($title)): ?>
                    <div class="popup-header">
                        <div class="popup-title" id="<?= $id ?>-title"><?= $title ?></div>

                        <button type="button" class="button--plain basic_close" data-close aria-label="<?= __('Close') ?>">
                            <span class="flaticon-remove" aria-hidden="true"></span>
                        </button>
                    </div>
                <?php else: ?>
                    <button type="button" class="button--plain basic_close" data-close aria-label="<?= __('Close') ?>">
                        <span class="flaticon-remove" aria-hidden="true"></span>
                    </button>
                <?php endif; ?>

                <?php if (!empty($body)): ?>
                    <div class="popup-content page-content<?= isset($body_class) ? ' '.$body_class : '' ?>">
                        <?= $body ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($footer)): ?>
                    <div class="popup-footer">
                        <?= $footer ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
