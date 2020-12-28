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
<div id="list_drivers_wrapper">
	<form method="post">
    <table id="list_drivers_table" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Driver</th>
                <th scope="col">Provider</th>
				<th scope="col">Send</th>
				<th scope="col">Receive</th>
                <th scope="col">Status</th>
				<th scope="col">Default</th>
				<th scope="col">Action</th>
            </tr>
        </thead>
		<tbody>
		<?php foreach($messaging_drivers_data as $provider){ ?>
		<tr>
			<td><?=ucfirst($provider['driver'])?></td>
			<td><?=$provider['provider']?></td>
			<td><?=$messaging_drivers[$provider['driver']][$provider['provider']]->has_send() ? 'Yes' : 'No'?></td>
			<td><?=$messaging_drivers[$provider['driver']][$provider['provider']]->has_receive_cron() || $messaging_drivers[$provider['driver']][$provider['provider']]->has_receive_callback() ? 'Yes' : 'No'?></td>
			<td><select name="status[<?=$provider['driver']?>][<?=$provider['provider']?>]"><option value="ACTIVE" <?=$provider['status'] == 'ACTIVE' ? 'selected="selected"' : ''?>>Active</option><option value="UNUSED" <?=$provider['status'] != 'ACTIVE' ? 'selected="selected"' : ''?>>Unused</option></select></td>
			<td><input type="radio" name="default_provider[<?=$provider['driver']?>]" value="<?=$provider['provider']?>" <?=$provider['is_default'] == 'YES' ? 'checked' : ''?>/></td>
			<td>
				<?php if ($messaging_drivers[$provider['driver']][$provider['provider']]->has_receive_cron()) { ?>
				<a class="sync" href="/admin/messaging/receive_sync/<?=$provider['driver'] . '-' . $provider['provider']?>"><?= __('Sync Now') ?></a>
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
		</tbody>
		<tfoot>
			<tr><th colspan="7"><button type="submit" name="save">Save</button></th></tr>
		</tfoot>
    </table>
	</form>
</div>
