<p>A competition entry has been submitted from <?= URL::base() ?>.</p>

<h2 style="font-size: 11pt;">Details</h2>

<table style="font-size:10pt;vertical-align:top;">
	<tbody>
		<?php $ignore_fields = array('formbuilder_id', 'redirect', 'failpage', 'subject', 'event', 'trigger', 'form_type', 'email_template', 'terms'); ?>
		<?php foreach ($form as $field => $value): ?>
			<?php if ( ! in_array($field, $ignore_fields)): ?>
				<tr>
					<td><strong><?= ucfirst(str_replace('_', ' ', $field)) ?></strong></td>
					<td><?= is_string($value) ? nl2br($value) : $value ?></td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
	</tbody>
</table>

<p>This email was sent <?= date('F j,  Y,  g:i a') ?> from <?= URL::base() ?></p>