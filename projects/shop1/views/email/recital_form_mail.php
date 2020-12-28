<p>A tea-time recital application has been made.</p>

<?php if (isset($form['date']) AND trim($form['date']) != ''): ?>
    <p><b>Date</b>: <?= $form['date'] ?></p>
<?php endif; ?>

<?php if (isset($form['name']) AND trim($form['name']) != ''): ?>
    <p><b>Name</b>: <?= $form['name'] ?></p>
<?php endif; ?>

<?php if (isset($form['grade']) AND trim($form['grade']) != ''): ?>
    <p><b>Grade</b>: <?= $form['grade'] ?></p>
<?php endif; ?>

<?php if (isset($form['instrument']) AND trim($form['instrument']) != ''): ?>
    <p><b>Instrument</b>: <?= $form['instrument'] ?></p>
<?php endif; ?>

<?php if (isset($form['teacher']) AND trim($form['teacher']) != ''): ?>
    <p><b>Teacher</b>: <?= $form['teacher'] ?></p>
<?php endif; ?>

<?php if (isset($form['fpd']) AND trim($form['fpd']) != ''): ?>
    <p><b>Full Program details</b><br /> <?= nl2br($form['fpd']) ?><br /></p>
<?php endif; ?>

<p>This email was sent <?= date('F j, Y H:i:s') ?> from: <?= $_SERVER['HTTP_HOST'] ?></p>
