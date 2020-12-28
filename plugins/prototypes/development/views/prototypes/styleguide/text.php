<?php foreach ($contexts as $key => $label): ?>
    <?php if ($key != 'default'): ?>
        <p class="text-<?= $key ?>"><?= $label ?> text</p>
    <?php endif; ?>
<?php endforeach; ?>

<p><strong>bold</strong> <em>italic</em> <u>underline</u></p>