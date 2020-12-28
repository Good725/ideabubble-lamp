<p>Email sent from the <?= isset($form['business_name']) ? $form['business_name'].' ' : '' ?>website</p>

<h2>Personal details</h2>
<ul>
	<?php if (isset($form['name']) AND trim($form['name'])): ?>
		<li>Name: <?= $form['name'] ?></li>
	<?php endif; ?>

	<?php if (isset($form['telephone']) AND trim($form['telephone'])): ?>
		<li>Telephone: <?= $form['telephone'] ?></li>
	<?php elseif (isset($form['tel']) AND trim($form['tel'])): ?>
		<li>Telephone: <?= $form['tel'] ?></li>
	<?php endif; ?>

	<?php if (isset($form['email']) AND trim($form['email'])): ?>
		<li>Email: <?= $form['email'] ?></li>
	<?php endif; ?>

	<?php if (isset($form['dob']) AND trim($form['dob'])): ?>
		<li>Date of birth: <?= $form['dob'] ?></li>
	<?php endif; ?>

	<?php if (isset($form['gender']) AND trim($form['gender'])): ?>
		<li>Gender: <?= $form['gender'] ?></li>
	<?php endif; ?>

	<?php if (isset($form['country']) AND trim($form['country'])): ?>
		<li>Country: <?= $form['country'] ?></li>
	<?php endif; ?>

	<?php if (isset($form['from_where']) AND trim($form['from_where'])): ?>
		<li>From where did you here about us?: <?= $form['from_where'] ?></li>
	<?php endif; ?>

</ul>

<h2>Medical history</h2>
<ul>
	<?php if (isset($form['taking_medication']) AND ($form['taking_medication'] == 1 OR $form['taking_medication'] == 0)): ?>
		<li>Are you currently taking any medication?: <?= ($form['taking_medication'] == 1) ? 'Yes' : 'No' ?></li>
	<?php elseif (isset($form['takingM'])): ?>
		<li>Are you currently taking any medication?: <?= $form['takingM'] ?></li>
	<?php endif; ?>

	<?php if (isset($form['takingM2'])): ?>
		<li>If yes, please provide us with some more information: <?= $form['takingM2'] ?></li>
	<?php endif; ?>
</ul>

<h2>Hair condition:</h2>
<ul>
	<?php if (isset($form['hair_loss_age'])): ?>
		<li>Approximately at what age did your hair loss begin: <?= $form['hair_loss_age']?></li>
	<?php elseif (isset($form['hairlossA'])): ?>
		<li>Approximately at what age did your hair loss begin: <?= $form['hairlossA'] ?></li>
	<?php endif; ?>

	<?php if (isset($form['hair_loss_development'])): ?>
		<li>At what rate has your hair loss developed: <?= $form['hair_loss_development'] ?></li>
	<?php elseif (isset($form['hairlossB'])): ?>
		<li>At what rate has your hair loss developed: <?= $form['hairlossB'] ?></li>
	<?php endif; ?>

	<?php if (isset($form['had_restoration_before']) AND ($form['had_restoration_before'] == 1 OR $form['had_restoration_before'] == 0)): ?>
		<li>Have you had hair restoration before: <?= ($form['had_restoration_before'] == 1) ? 'Yes' : 'No' ?></li>
	<?php elseif (isset($form['hairlossC'])): ?>
		<li>Have you had hair restoration before: <?= $form['hairlossC'] ?></li>
	<?php endif; ?>

	<?php if (isset($form['achieve'])): ?>
		<li>What would you like to achieve? <?= $form['achieve'] ?></li>
	<?php endif; ?>

	<?php if (isset($form['desired_date'])): ?>
		<li>When would you like to have a procedure? <?= $form['desired_date'] ?></li>
	<?php elseif (isset($form['when'])): ?>
		<li>When would you like to have a procedure? <?= $form['when'] ?></li>
	<?php endif; ?>


	<?php if (isset($form['other_information'])): ?>
		<li>Please leave any extra information you think is important: <?= $form['other_information'] ?></li>
	<?php elseif (isset($form['info'])): ?>
		<li>Please leave any extra information you think is important: <?= $form['info'] ?></li>
	<?php endif; ?>

</ul>

<br />

<p>This email was sent <?= date('F d, Y h:i:s a', time()) ?> from <a href="<?= URL::site() ?>"><?= URL::site() ?></a></p>