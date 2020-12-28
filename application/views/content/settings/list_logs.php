<?php
if (isset($alert))
{
	echo $alert;
}
?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<div class="col-sm-12">
	<h1>Mysql Charset Variables</h1>
	<table class='table table-striped'>
		<tr><th>Variable</th><th>Value</th></tr>
		<?php
        foreach ($charset_vars as $row) {
        ?>
        <tr><td><?=$row['Variable_name']?></td><td><?=$row['Value']?></td></tr>
        <?php
        }
        ?>
	</table>
	<br/>
</div>
<div class="col-sm-12">
	<?php if(count($missing_models)){ ?>
	<h1>Missing Models</h1>
	<form method="post">
	<table class='table table-striped'>
		<thead>
		<tr>
			<th>ID</th>
			<th>Type</th>
			<th>Name</th>
            <th>Version</th>
			<th>Status</th>
			<th>MD5</th>
			<th>Updated</th>
			<th>Last Error</th>
		</tr>
		</thead>
		<tbody>
		<? foreach ($missing_models as $item):?>
			<tr>
				<td><input type="checkbox" name="model_id[]" value="<?=$item['id']?>" /><?=$item['id'];?></td>
				<td><?=$item['type'];?></td>
				<td><?=$item['name'];?></td>
                <td><?=$item['version'];?></td>
				<td><?=$item['status'];?></td>
				<td><?=$item['md5'];?></td>
				<td><?=$item['updated'];?></td>
				<td><?=$item['last_error'];?></td>
			</tr>
		<? endforeach?>
		</tbody>
		<tfoot>
			<tr><th colspan="6">

			<div class=" form-action-group text-left">	
				<button type="submit" class="btn  btn-primary" name="delete_models">Delete Selected Model Entries</button>
			</div>
		</tfoot>
	</table>
	<br/>
	</form>
	<?php } ?>
</div>

<div class="col-sm-12 header">
	<h1 class="left">
		DALM Logs
	</h1>
</div>

<div class="col-sm-12">
	<p>See all DALM Model entries with their status. (0 = OK, -1 = NOT OK)</p>

	<p>Current database : <b><?=Kohana::$config->load('database.default.connection.database').'@'.Kohana::$config->load('database.default.connection.hostname');?></b>  </p>
	<?if(Kohana::$config->load('database.ibis_db.connection.database')):?>
		<p>Current ibis database : <b><?=Kohana::$config->load('database.ibis_db.connection.database').'@'.Kohana::$config->load('database.ibis_db.connection.hostname');?></b>  </p>
	<?endif?>

	<?php if (Settings::instance()->get('database_sync_date')): ?>
		<p>
			Database last refreshed from <?= Settings::instance()->get('database_sync_source_server') ?>,
			`<?= Settings::instance()->get('database_sync_source_database') ?>`, on
			<?= date('D jS F Y', strtotime(Settings::instance()->get('database_sync_date'))) ?>.
		</p>
	<?php endif; ?>

	<table class='table table-striped dataTable'>
		<thead>
		<tr>
			<th>ID</th>
			<th>Type</th>
			<th>Name</th>
            <th>Version</th>
			<th>Status</th>
			<th>MD5</th>
			<th>Updated</th>
			<th>Last Error</th>
			<th>Action</th>
		</tr>
		</thead>
		<tbody>
		<? foreach ($data as $item):?>
			<tr>
				<td><?=$item['id'];?></td>
				<td><?=$item['type'];?></td>
				<td><?=$item['name'];?></td>
                <td><?=$item['version'];?></td>
				<td><?=$item['status'];?></td>
				<td><?=$item['md5'];?></td>
				<td><?=$item['updated'];?></td>
				<td><?=HTML::entities($item['last_query']) . '<br /><br /><b>' . HTML::entities($item['last_error']) . '</b>';?></td>
				<td><?php if($item['status'] != 0):?>
					<a href="/admin/settings/clear_dalm/<?=$item['id'];?>" class="btn btn-danger" style="color:#FFFFFF;">Clear Status</a>
					<?php endif;?>
					<?php if($item['status'] != 0):?>
                        <?php
                        if (stripos($item['last_error'], 'Duplicate entry') !== false || stripos($item['last_error'], 'Duplicate column') !== false || stripos($item['last_error'], 'already exists') !== false) {
                        ?>
                        <br />
						<form method="post" action="/admin/settings/ignore_dalm_error/">
							<input type="hidden" name="id" value="<?=$item['id']?>" />
							<input type="hidden" name="query" value="<?=base64_encode($item['last_query'])?>" />
							<button type="submit" href="<?=$item['id'];?>" class="btn btn-danger" style="color:#FFFFFF;" onclick="return confirm('Are you sure it\'s safe to ignore this error?') ? (prompt('Type i am ninja', '') == 'i am ninja') : false">Ignore Error</button>
						</form>
                        <?php
                        }
                        ?>
					<?php endif;?>
				</td>
			</tr>
		<? endforeach?>
		</tbody>
	</table>

	<h1>DALM Statements</h1>

	<p>See log of all SQL queries run on this projects database</p>

	<table class='table table-striped dataTable'>
		<thead>
		<tr>
			<th>ID</th>
			<th>Model ID</th>
			<th>md5</th>
			<th>statement</th>
			<th>Executed</th>
		</tr>
		</thead>
		<tbody>
		<? foreach ($data2 as $item):?>
			<tr>
				<td><?=$item['id'];?></td>
				<td><?=$item['model_id'];?></td>
				<td><?=$item['md5'];?></td>
				<td><?=HTML::entities($item['statement']);?></td>
				<td><?= $item['ignored'] ? '<i>' . $item['ignored'] . '</i>' : $item['executed']?></td>
			</tr>
		<? endforeach?>
		</tbody>
	</table>

</div>
