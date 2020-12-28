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
<table id="list_families_table" class="table table-striped dataTable">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
            <th scope="col">Primary Contact</th>
            <th scope="col">Address</th>
            <th scope="col">County</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div id="family_menu_wrapper" class="family_menu_wrapper"></div>
