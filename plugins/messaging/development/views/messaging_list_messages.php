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
    <table id="list_messages_table" class="table table-striped dataTable">
        <thead>
            <tr>
                <th scope="col">Driver</th>
                <th scope="col">Subject</th>
                <th scope="col">Date Created</th>
				<th scope="col">Schedule</th>
				<th scope="col">Status</th>
				<th scope="col">Sent Started</th>
				<th scope="col">Sent Completed</th>
				<th scope="col">Send Interrupted</th>
				<th scope="col">Details</th>
				<th scope="col"></th>
            </tr>
        </thead>
		<tbody>
			<?php foreach($messages as $message): ?>
				<tr>
					<td><?=$message['driver'] . '.' . $message['provider']?></td>
					<td><?=$message['subject']?></td>
					<td><?=$message['date_created']?></td>
					<td><?=$message['schedule']?></td>
					<td><?=$message['status']?></td>
					<td><?=$message['sent_started']?></td>
					<td><?=$message['sent_completed']?></td>
					<td><?=$message['send_interrupted']?></td>
					<td><a href="/admin/messaging/details?message_id=<?=$message['id']?>">view</a></td>
					<td>
						<?php if($message['status'] == 'SCHEDULED'): ?>
							<a href="/admin/messaging/send_start?message_id=<?=$message['id']?>" target="_blank" onclick="this.disabled = true; this.innerHTML = 'Sending...'; window.open(this.href, 'send_start_<?=$message['id']?>', '', false);return false;">Start Sending</a>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
    </table>
</div>
