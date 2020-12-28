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
<div id="list_messages_wrapper">
    <form method="post">
    <table id="list_outbox_whitelist_table" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Address</th>
                <th scope="col"></th>
            </tr>
        </thead>
		<tbody>
			<?php foreach($whitelist as $address): ?>
				<tr>
					<td><?=$address['email']?></td>
					<td>
						<input type="checkbox" name="address[]" value="<?=$address['email']?>" />
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2"><button type="submit" name="delete" value="delete">Delete selected</button> </td>
			</tr>
		</tfoot>
    </table>
    </form>
</div>
