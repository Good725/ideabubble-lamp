<div class="row-fluid header list_notes_alert">
    <?= (isset($alert)) ? $alert : '' ?>
    <?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
</div>
<?php if ( ! empty($todos)): ?>
    <table class="table dataTable educate_todos_table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Reporter</th>
                <th scope="col">Assignee</th>
                <th scope="col">Title</th>
                <th scope="col">Status</th>
                <th scope="col">Priority</th>
                <th scope="col">Type</th>
                <th scope="col">Due Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($todos as $id => $note): ?>
                <tr data-id="<?=$note['id'] ?>">
                    <td><?=$note['id'] ?></td>
                    <td><?= htmlentities($note['reporter']) ?></td>
                    <td><?= htmlentities($note['assignee']) ?></td>
                    <td><?=Text::limit_chars($note['title'],80) ?></td>
                    <td><?=$note['status'] ?></td>
                    <td><?=$note['priority'] ?></td>
                    <td><?=$note['todo_type_label'] ?></td>
                    <td><?=$note['datetime_end'] ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php else: ?>
    <p>There are no todos yet.</p>
<?php endif; ?>
