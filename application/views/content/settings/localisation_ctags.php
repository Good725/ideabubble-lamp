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

<form method="post" action="/admin/settings/localisation_ctag_add">
	<label for="language">Language</label><input type="text" name="language" required />&nbsp;
	<label for="ctag">Local Tag</label><input type="text" name="ctag" required />&nbsp;
	<button type="submit" name="add">Add</button>
</form>
<br />

<table id="localisation_ctags_table" class="table table-striped dataTable">
    <thead>
		<tr>
			<th scope="col">Language</th>
			<th scope="col">Tag</th>
			<th scope="col">Created</th>
			<th scope="col">Updated</th>
			<th></th>
		</tr>
    </thead>
    <tbody>
	<?php foreach($ctags as $ctag){ ?>
		<tr>
			<td><?=$ctag['language']?></td>
			<td><?=$ctag['ctag']?></td>
			<td><?=$ctag['created_on']?></td>
			<td><?=$ctag['updated_on']?></td>
			<td>
			<form method="post" action="/admin/settings/localisation_ctag_remove">
				<input type="hidden" name="ctag_id" value="<?=$ctag['id']?>" />
				<button class="btn" type="submit" name="remove" onclick="return confirm('Are you sure you want to delete this tag?');">Remove</button>
			</form>
			</td>
		</tr>
	<?php } ?>
    </tbody>
</table>
