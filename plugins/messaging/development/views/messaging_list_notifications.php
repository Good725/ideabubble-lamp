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
    <table id="list_messages_table" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Driver</th>
				<th scope="col">Category</th>
				<th scope="col">Template</th>
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
		<?php foreach($notifications as $notification){ ?>
		<tr>
			<td><?=$notification['driver'] . '.' . $notification['provider']?></td>
			<td><?=$notification['category']?></td>
			<td><?=$notification['template']?></td>
			<td><?=$notification['subject']?></td>
			<td><?=$notification['date_created']?></td>
			<td><?=$notification['schedule']?></td>
			<td><?=$notification['status']?></td>
			<td><?=$notification['sent_started']?></td>
			<td><?=$notification['sent_completed']?></td>
			<td><?=$notification['send_interrupted']?></td>
			<td><a href="/admin/messaging/details?message_id=<?=$notification['id']?>">view</a></td>
			<td>
			<?php
			if($notification['status'] == 'SCHEDULED'){
			?>
				<a href="/admin/messaging/send_start?message_id=<?=$notification['id']?>" target="_blank" onclick="this.disabled = true; this.innerHTML = 'Sending...'; window.open(this.href, 'send_start_<?=$notification['id']?>', '', false);return false;">Start Sending</a>
			<?php
			}
			?>	
			</td>
		</tr>
		<?php } ?>
		</tbody>
    </table>
</div>
