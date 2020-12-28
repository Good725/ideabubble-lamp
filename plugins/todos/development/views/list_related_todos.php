<?
if ($data)
{
	extract($data);
}
?>
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

</div>

<?php
   if ( count($todos) ) :
?>

<table class='table table-striped dataTable'>
	<thead>
        <tr>
            <th></th>
            <th scope="col">Title</th>
            <th scope="col">Status</th>
            <th scope="col">Priority</th>
            <th scope="col">Type</th>
            <th scope="col">Reporter</th>
            <th scope="col">Assignee</th>
        </tr>
	</thead>
	<tbody>
	<?php foreach ($todos as $id => $todo): ?>
    <?php
        //$edit_url = URL::adminaction('edit_todo',$todo['todo_id'])."?related_plugin_name=$related_plugin_name&related_to_id=$related_to_id"
        $edit_url = '/admin/todos/edit_todo/' . $todo['todo_id'] . "?return_url=$return_url";
    ?>
	<tr>
        <td><span style="background-color:<?=$todo['status_color']?>; color:<?=$todo['status_color']?>"><?=$todo['status_order']?>&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
		<td>
			<a href="<?=$edit_url?>"><?php echo $todo['title']; ?></a>
		</td>
		<td>
			<a href="<?=$edit_url?>">&nbsp;&nbsp;<?php echo $todo['status_id']; ?></a>
		</td>
        <td>
            <a href="<?=$edit_url?>"><?php echo $todo['priority_id']; ?></a>
        </td>
        <td>
            <a href="<?=$edit_url?>"><?php echo $todo['type_id']; ?></a>
        </td>
        <td>
            <a href="<?=$edit_url?>"><?php echo $todo['from_user_name']; ?></a>
        </td>
        <td>
            <a href="<?=$edit_url?>"><?php echo $todo['to_user_name']; ?></a>
        </td>

	</tr>
		<? endforeach ?>
	</tbody>
</table>
<?php endif; ?>

<a class="btn btn-primary" href="<?= "/admin/todos/add_todo/?related_plugin_name=$related_plugin_name&related_to_id=$related_to_id&return_url=$return_url" ?>" target="_blank">Add task</a>
