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
<table class='table table-striped dataTable'>
    <thead>
    <tr>
        <th>ID</th>
        <th>Category</th>
        <th>Summary</th>
        <th>Parent</th>
        <th>Published</th>
        <th>Delete</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($categories AS $category): ?>
        <tr>
            <td><a href='<?php echo URL::Site('admin/projects/add_edit_category/'.$category['id']); ?>'><?=$category['id']?></a></td>
            <td><a href='<?php echo URL::Site('admin/projects/add_edit_category/'.$category['id']); ?>'><?=$category['name']?></a></td>
            <td><a href='<?php echo URL::Site('admin/projects/add_edit_category/'.$category['id']); ?>'><?=$category['summary']?></a></td>
            <td><a href='<?php echo URL::Site('admin/projects/add_edit_category/'.$category['id']); ?>'><?=$category['parent']?></a></td>
            <td><a href='<?php echo URL::Site('admin/projects/add_edit_category/'.$category['id']); ?>'><?=$category['publish']?></a></td>
            <td><a href='<?php echo URL::Site('admin/projects/add_edit_category/'.$category['id']); ?>'>X</a></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

