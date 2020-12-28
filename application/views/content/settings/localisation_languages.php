<?=(isset($alert)) ? $alert : '';?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>

<form class="form-horizontal" method="post" action="/admin/settings/localisation_language_add">
	<div class="form-group ">
		<label for="code" class="control-label col-sm-1">Code</label>
		<div class="col-sm-2">		
			<input class="form-control" type="text" name="code" required /></div>
		<label for="title" class="col-sm-1 control-label">Title</label>
		<div class="col-sm-2"><input type="text" class="form-control" name="title" required  /></div>
		<div class="col-sm-2 form-action-group"><button type="submit" name="add" class="add btn">Add</button></div>
	</div>
</form>


<table id="localisation_languages_table" class="table table-striped dataTable">
    <thead>
		<tr>
			<th scope="col">Code</th>
			<th scope="col">Title</th>
			<th scope="col">Created</th>
			<th scope="col">Updated</th>
			<th></th>
		</tr>
    </thead>
    <tbody>
	<?php foreach($languages as $language){ ?>
		<tr>
			<td><?=$language['code']?></td>
			<td><?=$language['title']?></td>
			<td><?=$language['created_on']?></td>
			<td><?=$language['updated_on']?></td>
			<td>
			<form method="post" action="/admin/settings/localisation_language_remove">
				<input type="hidden" name="language_id" value="<?=$language['id']?>" />
				<button class="btn delete" type="submit" name="remove" onclick="return confirm('Are you sure you want to delete this language and all translation for it?');">Remove</button>
			</form>
			</td>
		</tr>
	<?php } ?>
    </tbody>
</table>
