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
<div id="list_notification_types_wrapper">
    <table id="list_notification_types_table" class="table table-striped dataTable">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Title</th>
                <th scope="col">Summary</th>
            </tr>
        </thead>
		<tbody>
		<?php foreach( $notification_types as $notification_type ){ ?>
		<tr>
			<td><?=$notification_type['id']?></td>
			<td><?=$notification_type['title']?></td>
			<td><?=$notification_type['summary']?></td>
		</tr>
		<?php } ?>
		</tbody>
    </table>
</div>
