<?php
// Predefined options
// "actions" dropdown for a list item or table row
if (isset($type) && $type == 'actions') {
    $btn_type      = isset($btn_type)      ? $btn_type      : 'outline-primary';
    $sr_title      = isset($sr_title)      ? $sr_title      : __('Actions');
    $title         = isset($title)         ? $title         : ['text' => '<span class="icon-ellipsis-h"></span>', 'html' => true];
    $options_align = isset($options_align) ? $options_align : 'right';
}
// "actions" dropdown that is not linked to an individual list item
if (isset($type) && $type == 'main_actions') {
    $btn_type      = isset($btn_type)      ? $btn_type      : 'primary';
    $title         = isset($title)         ? $title         : ['text' => htmlentities(__('Actions')).'&nbsp;<span class="icon-caret-down"></span>', 'html' => true];
    $options_align = isset($options_align) ? $options_align : 'right';
}

$fullwidth = !empty($fullwidth);
$title = isset($title) ? $title : '';
if (is_array($title) && isset($title['text'])) {
    $title = (empty($title['html'])) ? htmlentities($title['text']) : $title['text'];
}
if (isset($options)) {
    foreach ($options as &$option) {
        $option['attributes'] = isset($option['attributes']) ? $option['attributes'] : [];
        $option['type'] = isset($option['type']) ? $option['type'] : 'link';

        if ($option['type'] == 'button') {
            $option['attributes']['type']  = isset($option['attributes']['type']) ? $option['attributes']['type'] : 'button';
        }
        elseif ($option['type'] == 'link' && !isset($option['attributes']['href'])) {
            $option['attributes']['href'] = isset($option['link']) ? $option['link'] : '#';
        }

        $option['title'] = isset($option['title']) ? $option['title'] : '';
        if (is_array($option['title']) && isset($option['title']['text'])) {
            $option['title'] = (empty($option['title']['html'])) ? htmlentities($option['title']['text']) : $option['title']['text'];
        }

        if (empty($option['icon_html'])) {
            $option['icon_html'] = isset($option['icon']) ? '<span class="icon-'.trim($option['icon']).'"></span>' : '';
        }

        unset($option);
    }
} else {
    $options = [];
}

?>

<div
    class="btn-group btn-group-<?= $fullwidth ? ' btn--full' : ''?> <?= empty($group_classes) ? '' : $group_classes ?>"
    <?= !empty($id) ? ' id="'.$id.'"' : '' ?>
    <?= !empty($group_attributes) ? html::attributes($group_attributes) : '' ?>
>
    <button
        type="button"
        class="btn <?= isset($btn_type) ? 'btn-'.$btn_type : 'btn-default' ?><?= $fullwidth ? ' btn--full' : '' ?> dropdown-toggle"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
    >
        <?php if (isset($sr_title)): ?>
            <span class="sr-only"><?= htmlentities($sr_title) ?></span>
        <?php endif; ?>

        <?= $title ?>
    </button>

    <ul class="dropdown-menu<?= $fullwidth ? ' btn--full' : '' ?><?= isset($options_align) ? ' pull-'.$options_align : '' ?>" style="margin-top: 0;">
        <?php foreach ($options as $option): ?>
            <li>
                <?php if ($option['type'] == 'button'): ?>
                    <button<?= html::attributes($option['attributes']) ?>><?= trim($option['icon_html'].' '.$option['title']) ?>
                    </button>
                <?php else: ?>
                    <a<?= html::attributes($option['attributes']) ?>><?= trim($option['icon_html'].' '.$option['title']) ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>