<?= isset($alert) ? $alert : '' ?>
<form class="col-sm-9 form-horizontal" action="/admin/cars/save_category" method="post">
	<input type="hidden" name="id" value="<?= $category['id'] ?>" />

    <div class="form-group col-sm-12">
        <label for="edit_category_title" class="sr-only">Title</label>
        <input type="text" class="form-control required" id="edit_category_title" name="title" value="<?= $category['title'] ?>" placeholder="Enter title" />
    </div>


    <ul class="nav nav-tabs">
        <li class="active"><a href="#details_tab" data-toggle="tab">Details</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="details_tab">

            <div class="form-group">
                <div class="col-sm-3 control-label">Publish</div>
                <div class="col-sm-9">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default<?= ($category['publish'] == '1') ? ' active' : '' ?>">
                            <input type="radio"<?= ($category['publish'] == '1') ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
                        </label>
                        <label class="btn btn-default<?= ( $category['publish']== '0') ? ' active' : '' ?>">
                            <input type="radio"<?= ( $category['publish'] == '0') ? ' checked="checked"' : '' ?> value="0" name="publish">No
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-3 control-label">
                    <label for="edit_car_order">Order</label>
                </div>
                <div class="col-sm-9">
                    <input class="form-control" type="number" id="edit_car_order" name="order" value="<?= $category['order'] ?>" />
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-3 control-label">
                    <label for="edit_car_summary">Summary</label>
                </div>
                <div class="col-sm-9">
                    <input class="form-control" type="text" id="edit_car_summary" name="summary" value="<?= $category['summary'] ?>" />
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-3 control-label">
                    <label for="edit_car_description">Description</label>
                </div>
                <div class="col-sm-9">
                    <textarea class="form-control" id="edit_car_description" class="ckeditor"><?= $category['description'] ?></textarea>
                </div>
            </div>

        </div><!-- #details_tab -->
    </div><!-- .tab-content -->

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="submit" class="btn btn-success" name="redirect" value="exit">Save &amp; Exit</button>
        <button type="reset"  class="btn btn-warning">Reset</button>
        <?php if (isset($category['id']) AND $category['id'] != '') : ?>
            <a class="btn btn-danger" id="btn_delete" data-id="<?=$category['id']?>">Delete</a>
        <?php endif; ?>
		<a href="/admin/cars/categories" class="btn">Cancel</a>
    </div>
</form>

<?php if (isset($category['id']) AND $category['id'] != '') : ?>
	<div class="modal fade" id="delete_category_modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="/admin/cars/delete_category/<?= $category['id'] ?>" method="post" style="margin:0;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="exampleModalLabel">Confirm Deletion</h4>
					</div>
					<div class="modal-body">
						<p>Are you sure you want to delete this category?</p>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-danger">Delete</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script>
		$('#btn_delete').on('click', function()
		{
			$('#delete_category_modal').modal('show');
		})
	</script>
<?php endif; ?>