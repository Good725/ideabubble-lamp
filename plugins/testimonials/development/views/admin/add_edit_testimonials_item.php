<form class="col-sm-9 form-horizontal" name="form_testimonials_story_add_edit" id="form_testimonials_story_add_edit" action="/admin/testimonials/process_editor/" method="post">
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
	<div class="form-group">
		<div class="col-sm-12" id="page_edit_name">
			<input id="item_title" class="form-control required" placeholder="Enter testimonial title here" type="text" name="item_title" value="<?php echo @$item_data['title'];?>">
			<?php
			/*
				IbHelper::pre_r('<p>News Story Data:</p>');
				IbHelpers::pre_r($item_data);
			*/
			?>
		</div>
	</div>
    <div class="tabbable"> <!-- Only required for left/right tabs -->
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab">Summary</a></li>
            <li><a href="#tab2" data-toggle="tab">Details</a></li>
            <!--<li><a href="#tab3" data-toggle="tab">Preview</a></li>-->
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
				<div class="form-group">
					<label class="col-sm-3 control-label" for="item_summary">Summary</label>
					<div class="col-sm-9">
						<textarea class="form-control" rows="" cols="" class="form-control" name="item_summary" id="item_summary" ><?php echo @$item_data['summary'];?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="item_signature">Signature</label>
					<div class="col-sm-9">
						<input class="form-control" name="item_signature" type="text" id="item_signature" value="<?php echo @$item_data['item_signature'];?>" size="20" />
					</div>
				</div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="item_position">Position</label>
                    <div class="col-sm-9">
                        <input class="form-control" name="item_position" type="text" id="item_position" value="<?php echo @$item_data['item_position'];?>" size="20" />
                    </div>
                </div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="item_company">Company</label>
					<div class="col-sm-9">
						<input class="form-control" name="item_company" type="text" id="item_company" value="<?php echo @$item_data['item_company'];?>" size="20" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="item_website">Website</label>
					<div class="col-sm-9">
						<input class="form-control" name="item_website" type="text" id="item_website" value="<?php echo @$item_data['item_website'];?>" size="20" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="item_image">Image</label>
					<div class="col-sm-4">
						<select class="form-control" id="item_image" name="item_image" onchange="imageChange('item_image');">
							<option value="0">-- Select Image --</option>
							<?php
								echo Model_Media::factory('Media')->get_all_items_based_on(
									'location',
									'testimonials',
									'as_options',
									'=',
									$item_data['image']);
							?>
						</select>
					</div>
					<div class="col-sm-5" id="imagePreview">
						<?php if(isset($item_data['image']) AND !empty($item_data['image'])): ?>
							<img src="<?php echo Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'testimonials/_thumbs_cms');?>" alt="<?php echo $item_data['image'];?>"/>
						<?php endif; ?>
					</div>
				</div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="item_image">Banner</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="item_image" name="item_banner_image" onchange="imageChange('item_banner_image', 'bannerImagePreview');">
                            <option value="0">-- Select image --</option>
                            <?php
                            echo Model_Media::factory('Media')->get_all_items_based_on(
                                'location',
                                'testimonial_banners',
                                'as_options',
                                '=',
                                $item_data['banner_image']);
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-5" id="bannerImagePreview">
                        <?php if (!empty($item_data['banner_image'])): ?>
                            <img src="<?= Model_Media::get_image_path($item_data['banner_image'], 'testimonials/_thumbs_cms') ?>" alt="" />
                        <?php endif; ?>
                    </div>
                </div>

            </div>

			<div class="tab-pane" id="tab2">
				<div class="form-group">
					<label class="col-sm-3 control-label" for="item_content">Content</label>
					<div class="col-sm-9">
						<textarea class="form-control" rows="" cols="" name="item_content" id="item_content" ><?php echo @$item_data['content'];?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="item_category">Category</label>
					<div class="col-sm-9">
                        <?php
                        $options = '<option value="0">-- Select a category --</option>'.Model_Testimonials::get_item_categories_as('options', @$item_data['category_id']);
                        echo Form::ib_select(null, 'item_category_id', $options, null, ['id' => 'item_category_id']);
                        ?>
					<? /* NOT REQUIRED AT THE MOMENT @TODO: to be further developed if required at a later stage ?>
						<input type="input" name="item_new_category" value="" placeholder="Or Add New Category" size="15" />
					<? */?>
					</div>
				</div>

                <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'courses')): ?>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="testimonial-course_item">Course item</label>

                        <?php ob_start(); ?>
                            <?php
                            $course_id          = isset($item_data['course_id'])          ? $item_data['course_id']          : '';
                            $course_category_id = isset($item_data['course_category_id']) ? $item_data['course_category_id'] : '';
                            $subject_id         = isset($item_data['subject_id'])         ? $item_data['subject_id']         : '';
                            ?>

                            <option value="">-- None selected --</option>

                            <optgroup label="Courses">
                                <?php foreach ($courses as $option): ?>
                                    <option value="course-<?= $option->id ?>" <?= ($course_id == $option->id) ? ' selected="selected"' : '' ?>><?= htmlspecialchars($option->title) ?></option>
                                <?php endforeach;?>
                            </optgroup>

                            <optgroup label="Categories">
                                <?php foreach ($course_categories as $option): ?>
                                    <option value="course_category-<?= $option->id ?>" <?= ($course_category_id == $option->id) ? ' selected="selected"' : '' ?>><?= htmlspecialchars($option->category) ?></option>
                                <?php endforeach;?>
                            </optgroup>

                            <optgroup label="Subjects">
                                <?php foreach ($subjects as $option): ?>
                                    <option value="subject-<?= $option->id ?>" <?= ($subject_id == $option->id) ? ' selected="selected"' : '' ?>><?= htmlspecialchars($option->name) ?></option>
                                <?php endforeach;?>
                            </optgroup>
                        <?php $options = ob_get_clean(); ?>


                        <div class="col-sm-9">
                            <?= Form::ib_select(null, 'item_course_item_id', $options, null, ['id' => 'testimonial-course_item']); ?>
                        </div>
                    </div>
                <?php endif; ?>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="item_event_date">Date</label>
					<div class="col-sm-3">
						<input class="form-control datepicker" name="item_event_date" type="text" id="item_event_date" value="<?= @$item_data['event_date'];?>" size="20" />
					</div>
				</div>

				<div class="form-group">
                    <label class="col-sm-3 control-label" for="item_publish">Publish</label>
                    <div class="col-sm-9">
						<select class="form-control" id="item_publish" name="item_publish">
							<option value="0"<?php echo (isset($item_data['publish']) && @$item_data['publish'] == 0)? ' selected="selected"' : '';?>>No</option>
							<option value="1"<?php echo (isset($item_data['publish']) && @$item_data['publish'] == 1)? ' selected="selected"' : ''; if(!isset($item_data['publish']))echo ' selected="selected"' ;?>>Yes</option>
						</select>
					</div>
                </div>

            </div>

<?php if(isset($item_data['id']) AND $item_data['id'] > 0){ ?>
			<!--<div class="tab-pane" id="tab3">
                <div class="form-group">
                    <label class="col-sm-3 control-label">Preview</label>
                    <div class="col-sm-9">

                    </div>
                </div>
            </div>-->
<?php }?>
        </div>
    </div>

	<input type="hidden" value="<?php echo @$item_data['id']?>" name="item_id" id="item_id"/>
	<input type="hidden" value="<?php echo (isset($item_data['id']) AND $item_data['id'] > 0)? 'edit' : 'add';?>" name="editor_action" id="editor_action"/>
	<input type="hidden" value="/admin/testimonials/add_edit_item" name="editor_redirect" id="editor_redirect"/>

    <div id="ActionMenu" class="floatingMenu">

		<div class="btn-group">
			<a class="btn btn-primary" id="btn_save">Save</a>
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu">
				<li><a href="#" id="btn_save_exit">Save &amp; Exit</a></li>
				<?php if ( ! empty($item_data['id']) AND Settings::instance()->get('twitter_api_access') == 1): ?>
					<li><a
							href="http://twitter.com/home/?status=<?= urlencode("New testimonial posted\n".URL::site().'testimonials/'.$item_data['category'].'/'.$item_data['title']) ?>"
							type="button"
							class="tweet-item-btn"
							>Tweet</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>

        <a class="btn btn-default" href="">Reset</a>
	<?php if(isset($item_data['id']) AND $item_data['id'] > 0){ ?>
		<a class="btn btn-danger" id="btn_delete">Delete</a>
	<?php }?>
    </div>
    <div class="floating-nav-marker"></div>

    <!-- Confirm window-->
    <div class="modal fade" id="confirm_delete">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h3><?= __('Confirm delete') ?></h3>
				</div>
				<div class="modal-body">
					<p><?= __('Are you sure you wish to delete this testimonial?') ?></p>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
				</div>
			</div>
		</div>
    </div>
</form>

<!-- Validation failed -->
<div class="modal fade" id="validation_failed">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<p>
					Please be sure to give a title for the testimonial and select the category.
				</p>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" id="btn_review">Review</a>
			</div>
		</div>
	</div>
</div>
