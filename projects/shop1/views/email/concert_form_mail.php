<p>A concert application has been made</p>

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

<?php if (isset($form['fulltitleofpiece']) AND trim($form['fulltitleofpiece']) != ''): ?>
	<p><b>Title of piece</b>: <?= $form['fulltitleofpiece'] ?></p>
<?php endif; ?>

<?php if (isset($form['composer_surname']) AND trim($form['composer_surname']) != ''): ?>
	<p><b>Composer surname</b> <?= $form['composer_surname'] ?></p>
<?php endif; ?>

<?php if (isset($form['composer_firstname']) AND trim($form['composer_firstname']) != ''): ?>
	<p><b>Composer first name</b>  <?= $form['composer_firstname'] ?></p>
<?php endif; ?>

<?php if (isset($form['performing_thursday'])): ?>
	<p><b>Performing Thursday</b> <?= $form['performing_thursday'] ? 'Yes' : 'No' ?></p>
<?php endif; ?>

<p>This email was sent <?= date('F j, Y H:i:s') ?> from: <?= $_SERVER['HTTP_HOST'] ?></p>
