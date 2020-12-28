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
<div id="messaging_read">
	<form name="messaging_read_form" method="post">
	<?php if($delete_result === false){ ?>
	<p>Failed</p>
	<?php } ?>
	<?php if($delete_result === true){ ?>
	<p>Succeeded</p>
	<?php } ?>
    <table id="messaging_read_table" class="table table-striped dataTable">
		<tr><th>Subject:</th><td><?=$message['subject']?></td></tr>
		<tr>
			<th valign="top">Message:</th>
			<td><?=$message['message']?></td>
		</tr>
		<tr><th colspan="2"><button type="submit" name="delete" value="delete">Delete</button></th></tr>
    </table>
	</form>
</div>
