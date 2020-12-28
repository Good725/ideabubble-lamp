<?php $data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : array()) ?>

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

<form class="col-sm-12 form-horizontal" id="form_add_topic" name="form_add_topic" action="/admin/courses/save_topic/" method="post">

    <input type="hidden" id="redirect" name="redirect" />

    <!-- Name -->
    <div class="form-group">
		<div class="col-sm-7">
			<label class="sr-only" for="name">Name</label>
			<input type="text" class="form-control required" id="name" name="name" placeholder="Enter topic name here" value="<?=isset($data['name']) ? $data['name'] : ''?>"/>
		</div>
    </div>

    <!-- Description -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="summary">Description</label>
        <div class="col-sm-5">
            <textarea class="form-control" id="description" name="description" cols="30" rows="10"><?=isset($data['description']) ? $data['description'] : ''?></textarea>
        </div>
    </div>

    <!-- Topic Identifier -->
    <input type="hidden" id="id" name="id" value="<?=isset($data['id']) ? $data['id'] : ''?>"/>

    <div class="well">
        <button type="button" class="btn btn-primary save_button" data-redirect="save">Save</button>
        <button type="button" class="btn btn-link cancel_button" >Cancel</button>
        <button type="reset" class="btn">Reset</button>
        <?php if (isset($data['id'])) : ?>
<!--            <a href="#" class="btn btn-danger" id="btn_delete" data-id="--><?//=$data['id']?><!--">Delete</a>-->
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
					<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected Topic.</p>
				</div>

				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
