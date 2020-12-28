<?=(isset($alert)) ? $alert : '';?>
<table class="table table-striped dataTable">
    <thead>
		<tr>
			<th scope="col">ID</th>
			<th scope="col">Form Name</th>
			<th scope="col">Target</th>
			<th scope="col">Last Modified</th>
			<th scope="col">Publish</th>
		</tr>
    </thead>
    <tbody>
		<?php foreach ($forms as $form): ?>
			<tr>
				<td><a href='<?php echo URL::Site('admin/formbuilder/add_edit_form/'.$form['id']); ?>'><?= $form['id']; ?></a></td>
				<td><a href='<?php echo URL::Site('admin/formbuilder/add_edit_form/'.$form['id']); ?>'><?= $form['form_name']; ?></a></td>
				<td><a href='<?php echo URL::Site('admin/formbuilder/add_edit_form/'.$form['id']); ?>'><?= $form['action']; ?></a></td>
				<td><a href='<?php echo URL::Site('admin/formbuilder/add_edit_form/'.$form['id']); ?>'><?= $form['date_modified']; ?></a></td>
				<td id="publish_<?= $form['id'] ?>" class="publish"><i class="icon-<?= ($form['publish'] == 1) ? 'ok' : 'remove' ?>"></i></td>
			</tr>
		<?php endforeach; ?>
    </tbody>
</table>

