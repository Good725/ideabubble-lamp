<?= (isset($alert)) ? $alert : ''; ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<form class="form-horizontal" id="project_edit_form" method="post" action="<?=URL::site();?>admin/projects/save">
    <input type="hidden" id="id" name="id" value="<?=$project['id'];?>"/>
    <input type="hidden" name="action" id="action" value=""/>
    <div class="form-group col-sm-9">
        <label class="col-sm-3 control-label" for="name">Project Name</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="name" name="name" value="<?=(isset($project['name'])) ? $project['name']: '';?>">
        </div>
    </div>
	<ul class="col-sm-12 nav nav-tabs">
		<li><a href="#details_tab" data-toggle="tab">Details</a></li>
		<li><a href="#images_tab" data-toggle="tab">Images</a></li>
		<li><a href="#related_tab" data-toggle="tab">Related</a></li>
	</ul>
	<div class="tab-content">
		<div class="col-sm-9 tab-pane active" id="details_tab">
				<div class="form-group">
					<label class="col-sm-3 control-label" for="summary">Summary</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="summary" name="summary" value="<?=(isset($project['summary'])) ? $project['summary']: '';?>">
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="category">Category</label>
					<div class="col-sm-9">
						<select class="form-control" name="category" id="category">
							<?=Model_Projects_Categories::get_categories_as_dropdown($project['category']);?>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="sub_category">Sub-Category</label>
					<div class="col-sm-9">
						<select class="form-control" name="sub_category" id="sub_category">
							<?=Model_Projects_Categories::get_sub_categories_as_dropdown($project['sub_category']);?>
						</select>
					</div>
				</div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="project_publish_toggle">Publish</label>
                    <div class="btn-group col-sm-9  " data-toggle="buttons">
                        <label class="btn btn-default<?= (! isset($project['publish']) OR $project['publish'] == '1') ? ' active' : '' ?>">
                            <input type="radio"<?= (! isset($project['publish']) OR $project['publish'] == '1') ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
                        </label>
                        <label class="btn btn-default<?= (isset($project['publish']) AND $project['publish'] == '0') ? ' active' : '' ?>">
                            <input type="radio"<?= ( isset($project['publish']) AND $project['publish'] == '0') ? ' checked="checked"' : '' ?> value="0" name="publish">No
                        </label>
                    </div>
                </div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="order">Order</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="order" name="order" value="<?=(isset($project['order'])) ? $project['order']: '0';?>">
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="description">Description</label>
					<div class="col-sm-9">
						<textarea class="form-control" id="description" name="description" rows="4"><?=(isset($project['description'])) ? $project['description']: '';?></textarea>
					</div>
				</div>
		</div>

		<div class="col-sm-12 tab-pane" id="images_tab">
			<div class="left well col-sm-12">
				<div class="form-group">
					<label class="col-sm-3 control-label" for="project_select_image">Image</label>
					<div class="col-sm-5">
						<select class="form-control" name="project_select_image" id="project_select_image">
							<option value="0">-- Select Image --</option>
							<?php
							echo Model_Media::factory('Media')->get_all_items_based_on('location','projects','as_options','=',NULL);
							?>
						</select>
					</div>
					<div class="col-sm-4">
						<button type="button" class="btn" id="add_image_button">Add Image</button>
					</div>
				</div>
			</div>
			<table class="table table-striped" id="images_table">
				<thead>
					<tr>
						<th scope="col">Thumb</th>
						<th scope="col">File Name</th>
						<th scope="col">Remove</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($images as $image): ?>
					<?php include 'snippets/image_tr.php' ?>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<div class="col-sm-9 tab-pane" id="related_tab">
			<div class="form-group">
				<label class="col-sm-3 control-label" for="project_related_projects">Related</label>
				<div class="col-sm-9">
					<select class="form-control" name="project_related_projects" id="project_related_projects">
						<?=Model_Projects::unrelated_as_options($unrelated);?>
					</select>
					<button type="button" class="btn" id="add_related_button">Add</button>
				</div>
			</div>

			<div id="projects_pane">
				<?=Model_Projects::related_as_well_list($related);?>
			</div>
		</div>

		<div class="well left width-920 bottom-bar">
			<button type="button" data-action="save" class="save_btn btn btn-success">Save</button>
			<button data-action="save_and_exit" class="save_btn btn btn-primary">Save &amp; Exit</button>
			<?php if ($project['id'] != ''): ?>
				<button type="button" class="btn btn-danger" id="delete-project-button" data-toggle="modal" data-target="#delete-project-modal">Delete</button>
			<?php endif; ?>
			<a href="/admin/projects" id="cancel_button" class="btn">Cancel</a>
		</div>
	</div>
</form>

<?php if ($project['id'] != ''): ?>
	<div class="modal fade" id="delete-project-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Confirm Deletion</h4>
				</div>
				<div class="modal-body">
					<p>Are you sure you want to delete this project?</p>
				</div>
				<div class="modal-footer">
					<a href="/admin/projects/delete/<?= $project['id'] ?>" class="btn btn-danger" id="confirm-delete-project-button">Delete</a>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
