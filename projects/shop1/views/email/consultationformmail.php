<p>A booking consulatation has been made.</p>
<table class="padded-table">
	<tbody>
		<?php if (isset($form['name']) AND trim($form['name'])): ?>
			<tr>
				<th scope="row">Name</th>
				<td><?= $form['name'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($form['address']) AND trim($form['address'])): ?>
			<tr>
				<th scope="row">Address</th>
				<td><?= nl2br($form['address']) ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($form['email']) AND trim($form['email'])): ?>
			<tr>
				<th scope="row">Email</th>
				<td><?= $form['email'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($form['phone']) AND trim($form['phone'])): ?>
			<tr>
				<th scope="row">Phone</th>
				<td><?= $form['phone'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($form['message']) AND trim($form['message'])): ?>
			<tr>
				<th scope="row">Message</th>
				<td><?= nl2br($form['message']) ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($form['service']) AND trim($form['service'])): ?>
			<tr>
				<th scope="row">Service</th>
				<td><?= $form['service'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($form['requirements']) AND trim($form['requirements'])): ?>
			<tr>
				<th scope="row">Requirements</th>
				<td><?= $form['requirements'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($form['service_type']) AND trim($form['service_type'])): ?>
			<tr>
				<th scope="row">Service Type</th>
				<td><?= $form['service_type'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($form['industry_category']) AND trim($form['industry_category'])): ?>
			<tr>
				<th scope="row">Industry Category</th>
				<td><?= $form['industry_category'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($form['role']) AND trim($form['role'])): ?>
			<tr>
				<th scope="row">Role</th>
				<td><?= $form['role'] ?></td>
			</tr>
		<?php endif; ?>

	</tbody>
</table>

<p>This email was sent <?= date('F j,  Y,  g:i a') ?> from: <?= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '' ?></p>

<style>
	.padded-table {font-size:1em;}
	.padded-table th, .padded-table td {padding:.1em;vertical-align:top;}
	.padded-table th {text-align:left;font-weight:normal;}
	.padded-table th:after{content:':';}
</style>
