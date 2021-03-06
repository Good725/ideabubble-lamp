<? $data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : array()) ?>

<div class="col-sm-12">
	<?=(isset($alert)) ? $alert : ''?>
	<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
</div>

<form class="col-sm-12 form-horizontal" id="form_add_edit_year" name="form_add_edit_year" action="/admin/courses/save_year/" method="post">

    <input type="hidden" id="redirect" name="redirect" />

    <!-- Name -->
    <div class="form-group">
		<div class="col-sm-7">
			<label class="sr-only" for="year">Year</label>
			<input type="text" class="form-control required" id="year" name="year" placeholder="Enter year name here" value="<?=isset($data['year']) ? $data['year'] : ''?>"/>
		</div>
    </div>

    <!-- Summary -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="summary">Summary</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" id="summary" name="summary" value="<?=isset($data['summary']) ? $data['summary'] : ''?>"/>
        </div>
    </div>

    <!-- Publish -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="publish">Publish</label>
        <div class="col-sm-5">
            <div class="btn-group" data-toggle="buttons">
				<?php $publish = ( ! isset($data['publish']) OR $data['publish'] == '1'); ?>
				<label class="btn btn-plain<?= $publish ? ' active' : '' ?>">
					<input type="radio" name="publish" value="1" id="publish_yes"<?= $publish ? ' checked' : '' ?> />Yes
				</label>
				<label class="btn btn-plain<?= ( ! $publish) ? ' active' : '' ?>">
					<input type="radio" name="publish" value="0" id="publish_no"<?= ( ! $publish) ? ' checked' : '' ?> />No
				</label>
            </div>
        </div>
    </div>
    <!-- Year Identifier -->
    <input type="hidden" id="id" name="id" value="<?=isset($data['id']) ? $data['id'] : ''?>"/>

    <div class="well">
        <button type="button" class="btn btn-primary save_button" data-redirect="save">Save</button>
        <button type="button" class="btn btn-primary save_button" data-redirect="save_and_exit">Save &amp; Exit</button>
        <button type="reset" class="btn">Reset</button>
        <?php if (isset($data['id'])) : ?>
            <a href="#" class="btn btn-danger" id="btn_delete" data-id="<?=$data['id']?>">Delete</a>
        <?php endif; ?>
    </div>
</form>
<?php if (isset($data['id'])) : ?>
	<div class="modal hide fade" id="confirm_delete">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h3>Warning!</h3>
				</div>

				<div class="modal-body">
					<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected Year.</p>
				</div>

				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
