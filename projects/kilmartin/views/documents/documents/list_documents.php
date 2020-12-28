<table class="table table-striped dataTable">
	<thead>
		<tr>
			<th scope="col">Doc ID</th>
			<?php if ($show_contact_id_column): ?>
				<th scope="col">Contact ID</th>
			<?php endif; ?>
			<th scope="col">Name</th>
<!--			<th scope="col">Created</th>-->
			<th scope="col">Updated</th>
			<th scope="col">Size (KB)</th>
			<th scope="col">Type</th>
			<th scope="col">Download</th>
		</tr>
	</thead>
	<tbody>
		<?php if (isset($doc_array['document'])): ?>
			<?php foreach ($doc_array['document'] as $id => $doc): ?>
				<tr>
					<td><?= $doc['id'] ?></td>
					<?php if ($show_contact_id_column): ?>
						<td><?= $doc['contact_id'] ?></td>
					<?php endif; ?>
					<td><?= $doc['name'] ?></td>
<!--					<td>--><?//= DATE::ymdh_to_dmyh($doc['created']) ?><!--</td>-->
					<td><?= DATE::ymdh_to_dmyh($doc['last modified']) ?></td>
					<td><?= $doc['size'] ?></td>
					<td><?= pathinfo($doc['filename'], $pathinfo_extension) ?></td>
					<td title="Location: <?= $doc['path'] ?>"><?= $doc['download'] ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	<tbody>
</table>
