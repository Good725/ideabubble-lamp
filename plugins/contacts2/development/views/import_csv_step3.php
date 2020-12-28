<div class="col-sm-12">
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
	<h2 class="">Import CSV Result</h2>
</div>

<table class="table" cellspacing="0" cellpadding="5">
    <thead>
        <tr><th>Record</th><th>Action</th></tr>
    </thead>
    <tbody>
    <?php
    $insert = 0;
    $skip = 0;
    foreach ($importReport as $report) {
        if ($report['action'] == 'insert') {
            ++$insert;
        }
        if ($report['action'] == 'skip') {
            ++$skip;
        }
    ?>
        <tr>
            <td><?=html::entities($report['key'])?></td>
            <td><?=$report['action']?></td>
        </tr>
    <?php
    }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <th>Summary</th>
            <th></th>
        </tr>
        <tr>
            <th>Imported</th>
            <td><?=$insert?></td>
        </tr>
        <tr>
            <th>Skipped</th>
            <td><?=$skip?></td>
        </tr>
        <tr>
            <th>Total</th>
            <td><?=$skip + $insert?></td>
        </tr>
    </tfoot>
</table>
