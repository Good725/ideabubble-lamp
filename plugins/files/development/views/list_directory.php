<?= isset($alert) ? $alert : ''?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<div id="pathBreadcrumbs"></div>

<table class="table table-striped dataTable" id="filesTable">
    <thead>
    <tr>
        <th></th>
        <th></th>
        <th>Name</th>
        <th>Size (KiB)</th>
        <th>Date Created</th>
        <th>Date Modified</th>
        <th>Modified By</th>
        <th>Actions</th>
        <th>Path</th>
    </tr>
    </thead>
</table>

<input type="hidden" id="directoryId" value="<?php echo $directory_id?>"/>
