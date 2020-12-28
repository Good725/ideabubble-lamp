<?php if (trim($email) || trim($website)): ?>
    <div class="row widget-contact_details<?= (!empty($vertical)) ? ' widget-contact_details--vertical' : '' ?>">
        <?php if (trim($email)): ?>
            <span class="widget-contact_details-item">
                <button
                    class="button--plain"
                    data-toggle="modal"
                    data-target="#modal--contact_<?= $type ?>"
                    <?= isset($item_id) ? 'data-item_id="'.$item_id.'"' : '' ?>
                    <?= isset($title)   ? 'data-subtitle="'.$title.'"'     : '' ?>
                >
                    <span class="flaticon-envelope"></span>
                    <?= $contact_text ?>
                </button>
            </span>
        <?php endif; ?>

        <?php if (trim($website)): ?>
            <span class="widget-contact_details-item">
                <a target="_blank" href="<?= (!preg_match('#^(http|https)://#i', $website) ? 'http://' : '') . trim($website) ?>">
                    <span class="flaticon-domain"></span>
                    <?= __('Website') ?>
                </a>
            </span>
        <?php endif; ?>
    </div>
<?php endif; ?>