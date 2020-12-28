<?php
	if(isset($results) && !empty($results)){
	?>
		<table class="table table-striped dataTable" >
			<div class="count_displayed">Total users whom <?php echo $format_type;?> = <?php echo count($results);?></div>
			<thead>
				<tr role="row">
					<th>Users Who Viewed Discount</th>
					<th>IP</th>
					<th>When</th>
					<th>Session ID</th>
				</tr>
			</thead>

			<tbody role="alert" aria-live="polite" aria-relevant="all"><tr data-report_id="25" class="odd">
				<?php
					foreach($results as $result){
						$name = '';
						if(isset($result['form_data'])){
							$form_data = json_decode($result['form_data'], true);
							$name = ucwords($form_data['ccName'].' '.$form_data['ccFirstName'].' '.$form_data['ccLastName']);
						}	
					?>	
						<tr>
							<td><?php echo $name;?></td>
							<td><?php echo $result['ip'];?></td>
							<td><?php echo $result['date_created'];?></td>
							<td><?php echo $result['session_id'];?></td>
						</tr>
					<?php	
					}
				?>
			</tbody>
		</table>
	<?php	
	}
?>
