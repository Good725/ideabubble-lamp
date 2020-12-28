<?php if (count($form) > 0): ?>
	<h3>Form data</h3>
	<table style="text-align: left;font-size: 12px;">
		<thead>
			<tr>
				<th scope="col">Input</th>
				<th scope="col">Value</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($form as $key => $value): ?>
				<?php if (is_string($value) AND strpos($key, 'recaptcha_') === FALSE): ?>
					<tr>
						<th scope="row"><?= ucfirst(str_replace('_', ' ', str_replace('contact_form_', '', $key))) ?></th>
						<td><?= nl2br($value) ?></td>
					</tr>
				<?php endif; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
