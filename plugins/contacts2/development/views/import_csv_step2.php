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
	<h2 class="">Import CSV</h2>
</div>

<form class="col-sm-12 form-horizontal" id="form_import_csv" name="form_import_csv" action="/admin/contacts2/import_csv?step=3" method="post" enctype="multipart/form-data">
    <input type="hidden" name="tmpfile" value="<?=$tmpfile?>" />
    <input type="hidden" name="encoding" value="<?=html::chars($encoding)?>" />
    <input type="hidden" name="delimiter" value="<?=html::chars($delimiter)?>" />
    <input type="hidden" name="enclosure" value="<?=html::chars($enclosure)?>" />
    <fieldset>
        <legend>Setup</legend>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="table">Import To</label>
            <div class="col-sm-5">
                <select class="form-control" id="table" name="table" required="required">
                    <option value="contacts2" selected="selected">Contacts</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="list_id">Default List</label>
            <div class="col-sm-5">
                <select class="form-control" id="mailing_list" name="mailing_list" required="required">
                    <?=html::optionsFromArray($contactLists, null);?>
                </select>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend>Columns</legend>
        <table class="table" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Source</th><th>Target</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($columns as $column) {
            ?>
                <tr>
                    <td><?=$column?></td>
                    <td>
                        <select name="map[<?=$column?>]">
                            <option value=""></option>
                            <?php
                            foreach ($mapColumns as $option) {
                                echo '<option value="' . $option . '"' .
                                    ($option == $column ? ' selected="selected"' : '') . '>' . $option .
                                    '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </fieldset>

    <div class="well">
        <button type="submit" class="btn btn-primary" name="start">Start Import</button>
        <a href="/admin/contacts2" class="btn">Cancel</a>
    </div>
</form>
