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

<?php
$todos = array(
	array(
		'id'       => 64,
		'reporter' => 'Reporter 1',
		'title'    => 'Example',
		'assignee' => 'Assignee',
		'status'   => 'Not done',
		'priority' => 'Not urgent',
		'type'     => 'Type',
		'due_date' => '2016-12-31 18:00:00'
	),
	array(
		'id'       => 59,
		'reporter' => 'Reporter 2',
		'title'    => 'Title',
		'assignee' => 'Assignee',
		'status'   => 'Not done',
		'priority' => 'Not urgent',
		'type'     => 'Type 2',
		'due_date' => '2017-02-18 18:00:00'
	)
);
?>

<div class="col-sm-12">
	<?php if ( ! empty($todos)): ?>
		<table class="table table-striped dataTable todos_table">
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
				<?php foreach ($todos as $id => $todo): ?>
					<tr data-id="<?=$todo['id'];?>">
						<td><?=$todo['id'] ?></td>
						<td><?=$todo['reporter'] ?></td>
						<td><?=$todo['assignee'] ?></td>
						<td><?=Text::limit_chars($todo['title'],80);?></td>
						<td><?=$todo['status'] ?></td>
						<td><?= $todo['priority'] ?></td>
						<td><?=$todo['type'] ?></td>
						<td><?=IbHelpers::relative_time_with_tooltip($todo['due_date']) ?></td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	<?php else: ?>
		<p>There are no todos.</p>
	<?php endif; ?>
</div>
