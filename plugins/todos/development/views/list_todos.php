<div class="row-fluid header">

	<?php
	if (isset($alert))
	{
		echo $alert;
	}
	?>
	<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
	?>

	<h1 class="left">
		To Dos
	</h1>
    <div class="pull-right btn-toolbar">
        <a class="btn btn-primary" href="<?=URL::site()?>admin/todos/add_todo">Add task</a>
    </div>
</div>

<table class='table table-striped dataTable'>
	<thead>
        <tr>
            <th></th>
            <th scope="col">Reporter</th>
            <th scope="col">Assignee</th>
            <th scope="col">Title</th>
            <th scope="col">Status</th>
            <th scope="col">Priority</th>
            <th scope="col">Type</th>
            <th scope="col">Due Date</th>
            <th scope="col">Created</th>
            <th scope="col">Updated</th>
        </tr>
	</thead>
	<tbody>
	<?php foreach ($todos as $id => $todo): ?>
	<tr>
        <td><span style="background-color:<?=$todo['status_color']?>; color:<?=$todo['status_color']?>"><?=$todo['status_order']?>&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
        <td>
            <a href="<?=URL::adminaction('edit_todo',$todo['todo_id'])?>"><?php echo $todo['from_user_name']; ?></a>
        </td>
        <td>
            <a href="<?=URL::adminaction('edit_todo',$todo['todo_id'])?>"><?php echo $todo['to_user_name']; ?></a>
        </td>
        <td>
            <a href="<?=URL::adminaction('edit_todo',$todo['todo_id'])?>"><?php echo $todo['title']; ?></a>
        </td>
		<td>
			<a href="<?=URL::adminaction('edit_todo',$todo['todo_id'])?>">&nbsp;&nbsp;<?php echo $todo['status_id']; ?></a>
		</td>
        <td>
            <a href="<?=URL::adminaction('edit_todo',$todo['todo_id'])?>"><?php echo $todo['priority_id']; ?></a>
        </td>
        <td>
            <a href="<?=URL::adminaction('edit_todo',$todo['type_id'])?>"><?php echo $todo['type_id']; ?></a>
        </td>
        <td>
            <a href="<?=URL::adminaction('edit_todo',$todo['todo_id'])?>"><?php echo DATE::ymdh_to_dmy($todo['due_date']); ?></a>
        </td>
        <td>
            <a href="<?=URL::adminaction('edit_todo',$todo['todo_id'])?>"><?php echo DATE::ymd_to_dmy($todo['date_created']); ?></a>
        </td>
        <td>
            <a href="<?=URL::adminaction('edit_todo',$todo['todo_id'])?>"><?php echo DATE::ymd_to_dmy($todo['date_updated']); ?></a>
        </td>
    </tr>
	<? endforeach ?>
	</tbody>
</table>
