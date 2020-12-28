<?php
if (isset($buttons)) {
    foreach ($buttons as &$button) {
        $button['attributes'] = isset($button['attributes']) ? $button['attributes'] : [];
        $button['type'] = isset($button['type']) ? $button['type'] : 'link';
        $class = 'btn';
        $class .= isset($button['context']) ? ' btn-'.$button['context'] : ' btn-default';
        $button['attributes']['class']  = $class.(isset($button['attributes']['class']) ? ' '.$button['attributes']['class'] : '');

        if ($button['type'] == 'button') {
            $button['attributes']['type']  = isset($button['attributes']['type']) ? $button['attributes']['type'] : 'button';
        }
        elseif ($button['type'] == 'link' && !isset($button['attributes']['href'])) {
            $button['attributes']['href'] = isset($button['link']) ? $button['link'] : '#';
        }

        $button['text'] = isset($button['text']) ? $button['text'] : '';
        if (is_array($button['text']) && isset($button['text']['text'])) {
            $button['text'] = (empty($button['text']['html'])) ? htmlentities($button['text']['text']) : $button['text']['text'];
        }

        if (empty($button['icon_html'])) {
            $button['icon_html'] = isset($button['icon']) ? '<span class="icon-'.trim($button['icon']).'"></span>' : '';
        }

        unset($button);
    }
} else {
    $buttons = [];
}

?>


<div class="form-action-group text-center">
    <button type="submit" class="btn btn-primary"><?= htmlspecialchars(__('Save')) ?></button>
    
    <?php foreach ($buttons as $button): ?>
        <?php if ($button['type'] == 'button'): ?>
            <button<?= html::attributes($button['attributes']) ?>><?= trim($button['icon_html'].' '.$button['text']) ?>
            </button>
        <?php else: ?>
            <a<?= html::attributes($button['attributes']) ?>><?= trim($button['icon_html'].' '.$button['text']) ?></a>
        <?php endif; ?>
    <?php endforeach; ?>
    
    <button type="button" class="btn btn-cancel" data-dismiss="modal"><?= htmlspecialchars(__('Cancel')) ?></button>
</div>