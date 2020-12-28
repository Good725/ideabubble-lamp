<?php if (count($side_panels)): ?>
    <?php $overlay_exists = $media_model->is_filename_used('panel-overlay.png', 'content'); ?>

    <div class="column-panels columns medium-5 large-4">
        <?php foreach ($side_panels as $panel): ?>
            <?php
            $panel_text = IbHelpers::expand_short_tags($panel['text']);
            $has_form = (strpos($panel_text, '<form') > -1);
            ?>

            <div class="panel-item<?= $has_form ? ' has_form' : '' ?><?= $panel['image'] ? ' has_image' : '' ?>">
                <?php if ($panel['image']): ?>
                    <?php ob_start(); ?>
                        <img src="<?= $media_path.'panels/'.$panel['image'] ?>" />

                        <?php if ($overlay_exists): ?>
                            <div class="panel-item-overlay" style="background-image: url('<?= $media_path ?>content/panel-overlay.png');"></div>
                        <?php endif; ?>
                    <?php $panel_image_html = ob_get_clean(); ?>

                    <?php if ($panel['link_url']): ?>
                        <a href="<?= $panel['link_url'] ?>" class="panel-item-image"><?= $panel_image_html ?></a>
                    <?php else: ?>
                        <div class="panel-item-image"><?= $panel_image_html ?></div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="panel-item-text">
                    <?= $panel_text ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>