<?= (isset($alert)) ? $alert : '' ?>
<form class="form-horizontal" action="/admin/cars/save" method="post">
	<input type="hidden" name="id" value="<?= $car->get_id() ?>" />

    <div class="form-group">
        <label for="edit_car_title" class="sr-only">Title</label>
        <input id="edit_car_title" name="title" type="text" class="form-control ib_text_title_input required" placeholder="Enter car title here" value="<?=$car->get_title();?>"/>
    </div>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#details_tab" data-toggle="tab">Details</a></li>
        <li><a href="#seo_tab"     data-toggle="tab">SEO</a></li>
        <li><a href="#images_tab"  data-toggle="tab">Images</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="details_tab">
            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_category">Category</label>
                </div>
                <div class="col-sm-7">
                    <select class="form-control" id="edit_car_category" name="category_id">
                        <option class="form-control" value="">Select Category</option>
						<?php foreach ($categories as $category): ?>
							<option value="<?= $category['id'] ?>"<?= $category['id'] == $car->get_category_id() ? ' selected="selected"' : '' ?>>
								<?= $category['title'] ?>
							</option>
						<?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">Publish</div>
                <div class="col-sm-7">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default<?= ($car->get_publish() == 1) ? ' active' : '' ?>">
                            <input type="radio"<?= ($car->get_publish() == 1) ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
                        </label>
                        <label class="btn btn-default<?= ($car->get_publish() == 0) ? ' active' : '' ?>">
                            <input type="radio"<?= ($car->get_publish() == 0) ? ' checked="checked"' : '' ?> value="0" name="publish">No
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">Import Overwrite</div>
                <div class="col-sm-7">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default<?= ($car->get_import_overwrite() == 1) ? ' active' : '' ?>">
                            <input type="radio"<?= ($car->get_import_overwrite() == 1) ? ' checked="checked"' : '' ?> value="1" name="import_overwrite">Yes
                        </label>
                        <label class="btn btn-default<?= ($car->get_import_overwrite() == 0) ? ' active' : '' ?>">
                            <input type="radio"<?= ($car->get_import_overwrite() == 0) ? ' checked="checked"' : '' ?> value="0" name="import_overwrite">No
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_make">Make</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_make" name="make" value="<?=$car->get_make();?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_model">Model</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_model" name="model" value="<?=$car->get_model();?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_price">Price</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_price" name="price" value="<?=$car->get_price();?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_engine">Engine</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_engine" name="engine" value="<?=$car->get_engine();?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_body_type">Body Type</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_body_type" name="body_type" value="<?=$car->get_body_type();?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_transmission">Transmission</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_transmission" name="transmission" value="<?=$car->get_transmission();?>"/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_year">Year</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_year" name="year" value="<?=$car->get_year();?>"/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_color">Colour</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_color" name="color" value="<?=$car->get_color();?>"/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_mileage">Mileage</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_mileage" name="mileage" value="<?=$car->get_mileage();?>"/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_no_of_owners">Owners</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_no_of_owners" name="no_of_owners" value="<?=$car->get_no_of_owners();?>"/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_location">Location</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_location" name="location" value="<?=$car->get_location();?>"/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_doors">Doors</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_doors" name="doors" value="<?=$car->get_doors();?>"/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_nct_expiry">NCT Expiry</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_nct_expiry" name="nct_expiry" value="<?=$car->get_nct_expiry_date();?>"/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_type">Type</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_type" name="type" value="<?=$car->get_type();?>"/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_odometer">Odometer</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_odometer" name="odometer" value="<?=$car->get_odometer();?>"/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_no_of_seats">No. of Seats</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_no_of_seats" name="no_of_seats" value="<?=$car->get_no_of_seats();?>">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_extra">Extra</label>
                </div>
                <div class="col-sm-7">
                    <input class="form-control" type="text" id="edit_car_extra" name="extra" value="<?=$car->get_extra();?>"/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_comments">Comments</label>
                </div>
                <div class="col-sm-7">
                    <textarea class="form-control" id="edit_car_comments" name="comments"><?=$car->get_comments();?></textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">
                    <label for="edit_car_additional_info">Additional Information</label>
                </div>
                <div class="col-sm-7">
                    <textarea class="form-control" id="edit_car_additional_info" name="additional_info"><?=$car->get_additional_info();?></textarea>
                </div>
            </div>

        </div><!-- details tab -->

        <div class="tab-pane" id="seo_tab">
            <div class="form-group">
                <label class="col-sm-2 control-label" for="edit_car_seo_title">Page Title</label>
                <div class="col-sm-7">
                    <textarea class="form-control" id="edit_car_seo_title" name="seo_title" rows="1"><?=$car->get_seo_title();?></textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="edit_car_seo_keywords">Keywords</label>
                <div class="col-sm-7">
                    <textarea class="form-control" id="edit_car_seo_keywords" name="seo_keywords" rows="2"><?=$car->get_seo_keywords();?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="edit_car_seo_description">Meta Description</label>
                <div class="col-sm-7">
                    <textarea class="form-control" id="edit_car_seo_description" name="seo_description" rows="2"><?=$car->get_seo_description();?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="edit_car_footer_editor">Footer Text</label>
                <div class="col-sm-7">
                    <textarea class="form-control ckeditor" id="edit_car_footer_editor" name="seo_footer"><?=$car->get_seo_footer();?></textarea>
                    </div>
            </div>
        </div><!-- SEO tab -->

        <div class="tab-pane active" id="images_tab">
            <button id="multi_upload_button" type="button" class="btn">Upload Images</button>
            <button id="add_existing_image_button" type="button" class="btn">Add Existing Image</button>

            <table class="table table-striped" id="images_table">
                <thead>
                    <tr>
                        <th>Thumb</th>
                        <th>File Name</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <input type="hidden" id="edit_car_images" name="images" value="[]">
        </div><!-- Images tab -->
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary" data-redirect="save">Save</button>
        <button type="submit" class="btn btn-primary" data-redirect="save_and_exit" name="redirect" value="exit">Save &amp; Exit</button>
        <button type="reset"  class="btn btn-warning">Reset</button>
        <?php if ($car->get_id() != '') : ?>
            <a class="btn btn-danger" id="btn_delete" data-id="<?=$car->get_id() ?>">Delete</a>
        <?php endif; ?>
		<a class="btn" href="/admin/cars">Cancel</a>
    </div>

</form>

<?php if ($car->get_id() != '') : ?>
	<div class="modal fade" id="delete_car_modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="/admin/cars/delete/<?= $car->get_id() ?>" method="post" style="margin:0;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="exampleModalLabel">Confirm Deletion</h4>
					</div>
					<div class="modal-body">
						<p>Are you sure you want to delete this car?</p>
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
			$('#delete_car_modal').modal('show');
		})
	</script>
<?php endif; ?>