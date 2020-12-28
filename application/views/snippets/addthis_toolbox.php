<div class="addthis_toolbox">
    <div class="custom_images">
        <?= file_get_contents($code_path.'share.svg') ?>

        <?php $options = ['linkedin' => 'Share on LinkedIn', 'facebook' => 'Share on Facebook', 'twitter' => 'Share on Twitter', 'email' => 'Email', 'more' => 'More...']; ?>

        <?php foreach ($options as $option => $text): ?>
            <a class="addthis_button_<?= $option ?>" title="<?= htmlspecialchars($text) ?>">
                <?= file_get_contents($code_path.$option.'.svg') ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js#username=<?= $addthis_id ?>"></script>