<?= isset($alert) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<form class="col-sm-9 form-horizontal" action="/admin/products/save_tag" method="post" id="edit_tag_form">
	<input type="hidden" name="id" value="<?= $tag['id'] ?>" />

	<div class="form-group">
		<label class="col-sm-12">
			<input type="text" class="form-control validate[required]" id="producttag_title" name="title" placeholder="Enter tag title here" value="<?= $tag['title'] ?> "/>
		</label>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label" for="producttag_description">Description</label>
		<div class="col-sm-9">
			<textarea class="form-control" id="producttag_description" name="description"><?= $tag['description'] ?></textarea>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label" for="producttag_information">Information</label>

		<div class="col-sm-9">
			<textarea class="form-control"  id="producttag_information" name="information"><?= $tag['information'] ?></textarea>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label" for="producttag_order">Order</label>

		<div class="col-sm-9">
			<input class="form-control"  id="producttag_order" type="text" name="order" value="<?= $tag['order'] ?>" />
		</div>
	</div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="publish">Publish</label>
        <div class="btn-group col-sm-9" data-toggle="buttons">
            <label class="btn btn-default<?= (! isset($tag['publish']) OR $tag['publish'] == '1') ? ' active' : '' ?>">
                <input type="radio"<?= (! isset($tag['publish']) OR $tag['publish'] == '1') ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
            </label>
            <label class="btn btn-default<?= (isset($tag['publish']) AND $tag['publish'] == '0') ? ' active' : '' ?>">
                <input type="radio"<?= ( isset($tag['publish']) AND $tag['publish'] == '0') ? ' checked="checked"' : '' ?> value="0" name="publish">No
            </label>
        </div>
    </div>

	<div class="col-sm-12 form-actions">
		<button type="submit" class="btn btn-primary">Save</button>
		<button type="submit" class="btn btn-success" name="redirect" value="exit">Save &amp; Exit</button>
		<button type="reset"  class="btn btn-warning">Reset</button>
		<?php if ($tag['id'] != '') : ?>
			<a href="#" class="btn btn-danger" id="btn_delete" data-id="<?=$tag['id']?>">Delete</a>
		<?php endif; ?>
		<a href="/admin/products/tags" class="btn">Cancel</a>
	</div>

</form>


<?php if ($tag['id'] != '') : ?>
	<div class="modal fade" id="delete_producttag_modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="/admin/products/delete_tag/<?= $tag['id'] ?>" method="post" style="margin:0;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Confirm Deletion</h4>
					</div>
					<div class="modal-body">
						<p>Are you sure you want to delete this tag?</p>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-danger">Delete</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php endif; ?>

<script>
	$('#btn_delete').on('click', function()
	{
		$('#delete_producttag_modal').modal('show');
	});

	$('#edit_tag_form').on('submit', function()
	{
		if ( ! $(this).validationEngine('validate'))
		{
			return false;
		}
	});
</script>
