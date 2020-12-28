<form class="col-sm-12 form-horizontal" name="form_news_story_add_edit" id="form_news_story_add_edit" action="/admin/news/process_editor/" method="post">
    <?php if (isset($alert)): ?>
        <?= $alert ?>
		<script>remove_popbox();</script>
	<?php endif; ?>

    <div class="form-group" id="page_edit_name" style="display: flex; align-items: center;">
		<div class="col-xs-10 col-md-7">
            <?php
            $title = isset($item_data['title']) ? $item_data['title'] : '';
            $attributes = array('class' => 'required', 'id' => 'item_title', 'placeholder' => __('Enter news title here'));
            echo Form::ib_input(null, 'item_title', $title, $attributes); ?>
        </div>

        <div class="col-xs-2 col-md-3">
            <label>
                <?php $published = (!isset($item_data['publish']) || $item_data['publish'] == 1); ?>
                <span class="sr-only"><?= __('Publish') ?></span>
                <input type="hidden" name="item_publish" value="0" /><?php // If the checkbox is unticked, this value will get sent to the server  ?>
                <input type="checkbox" name="item_publish" value="1"<?= $published ? ' checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('Published') ?>" data-off="<?= __('Unpublished') ?>" />
            </label>
        </div>
    </div>

    <div class="tabbable" style="margin-bottom: 1rem;">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#edit-news-tab-details" data-toggle="tab">Details</a></li>
            <li><a href="#edit-news-tab-seo" data-toggle="tab">SEO</a></li>
            <li><a href="#edit-news-tab-share" data-toggle="tab">Share</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="edit-news-tab-details">
                <div class="form-group">
                    <?php $selected_date = isset($item_data['event_date']) ? $item_data['event_date'] : date('Y-m-d'); ?>
                    <label class="col-sm-2 control-label" for="item_event_date">Story date</label>

                    <div class="col-sm-4">
                        <div id="item_event_date_calendar"></div>

                        <input type="text" class="sr-only" id="item_event_date" name="item_event_date" value="<?= $selected_date ?>" />
                    </div>

                    <div class="col-sm-6">
                        <h3 id="edit-news-selected-date"><?= date('j F Y', strtotime($selected_date))?></h3>

                        <div class="edit-news-selected-date-events" id="edit-news-selected-date-events"><?php
                            // There should be no excess whitespace inside this div
                            if (isset($same_date_news)) {
                                foreach ($same_date_news as $existing_item) {
                                    include 'snippets/existing_news_item.php';
                                }
                            }
                        ?></div>

                        <p><?= __('No news stories currently exist for this date.') ?></p>

                        <div class="hidden" id="edit-news-selected-date-event-template">
                            <?php
                            $existing_item = null;
                            include 'snippets/existing_news_item.php';
                            ?>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="item_category_id">Category</label>
                    <div class="col-sm-4">
                        <select id="item_category_id" name="item_category_id" class="form-control">
                            <option value="0">-- Select Category --</option>
                            <?php echo Model_News::get_item_categories_as('options', @$item_data['category_id'])?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="edit-news-item_author">Author</label>
                    <div class="col-sm-4">
                        <input class="form-control" type="text" id="edit-news-item_author" name="item_author" value="<?= isset($item_data['author']) ? $item_data['author'] : ''; ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="item_summary">&nbsp;</label>
                    <div class="col-sm-8">
                        <button type="button" class="btn-link" data-toggle="collapse" data-target="#edit-news-summary_section"><?= __('Add summary') ?></button>


                        <div class="panel panel-default collapse" id="edit-news-summary_section">
                            <div class="panel-heading" style="padding-bottom: 0;">
                                <div class="row gutters" style="margin-bottom: 0;">
                                    <div class="col-sm-10"><?= __('Summary') ?></div>
                                    <div class="col-sm-2 text-right">
                                        <button type="button" class="btn-link" data-toggle="collapse" data-target="#edit-news-summary_section"><?= __('hide') ?></button>
                                    </div>
                                </div>
                            </div>

                            <div class="panel-body">
                                <textarea rows="8" class="form-control" name="item_summary" id="item_summary"><?= @$item_data['summary'] ?></textarea>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="item_content">Content</label>
                    <div class="col-sm-8" style="min-height: 404px;"><?php // The inline style is to prevent content jumping when the CKEditor loads ?>
                        <textarea rows="" cols="" class="form-control" name="item_content"
                                  id="item_content"><?= isset($item_data['content']) ? $item_data['content'] : ''; ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="item_image">Image</label>
                    <div class="col-sm-5">
                        <input type="hidden" id="edit-news-image_id" name="item_image" value="<?= $item_data['image'] ?>" />

                        <?= View::factory('multiple_upload',
                            array(
                                'browse_directory' => 'content',
                                'duplicate'        => 0,
                                'name'             => 'news_image',
                                'onsuccess'        => 'news_image_uploaded',
                                'preset'           => 'news',
                                'presetmodal'      => 'no',
                                'single'           => true
                            )
                        ) ?>
                    </div>
                    <div class="col-sm-3<?= empty($item_data['image']) ? ' hidden' : '' ?>" id="image-preview-wrapper">
                        <button type="button" class="btn-link right" id="image-preview-remove" title="<?= __('Remove') ?>">
                            <span class="sr-only"><?= __('Remove') ?></span>
                            <span class="icon-times"></span>
                        </button>

                        <div id="imagePreview">
                            <?php if ( ! empty($item_data['image'])): ?>
                                <img
                                    src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, $item_data['image'], 'news/_thumbs_cms'); ?>"
                                    alt="<?= $item_data['image']; ?>"/>
                            <?php else: ?>
                                <img src="" alt="" class="hidden" />
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group" id="item_alt_text_row" <?= ( ! isset($item_data['image']) OR empty($item_data['image'])) ? ' style="display:none;"' : ''; ?>>
                    <label class="col-sm-2 control-label" for="item_alt_text">Image <abbr title="Alternative text to be displayed if the image is disabled, unavailable or fails to load.">alt text</abbr></label>
                    <div class="col-sm-4">
                        <input class="form-control" type="text" id="item_alt_text" name="item_alt_text" value="<?= isset($item_data['alt_text']) ? $item_data['alt_text'] : ''; ?>" />
                    </div>
                </div>

                <div class="form-group" id="item_title_text_row" <?= ( ! isset($item_data['image']) OR empty($item_data['image'])) ? ' style="display:none;"' : ''; ?>>
                    <label class="col-sm-2 control-label" for="item_title_text">Image hover text</label>
                    <div class="col-sm-4">
                        <input class="form-control" type="text" id="item_alt_text" name="item_title_text" value="<?= isset($item_data['title_text']) ? $item_data['title_text'] : ''; ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">&nbsp;</label>

                    <div class="col-sm-8">
                        <button type="button" class="btn-link" data-toggle="collapse" data-target="#edit-news-range_section"><?= __('Change display options') ?></button>

                        <div class="panel panel-default collapse" id="edit-news-range_section">
                            <div class="panel-heading" style="padding-bottom: 0;">
                                <div class="row gutters" style="margin-bottom: 0;">
                                    <div class="col-sm-10"><?= __('Display options') ?></div>
                                    <div class="col-sm-2 text-right">
                                        <button type="button" class="btn-link" data-toggle="collapse" data-target="#edit-news-range_section"><?= __('hide') ?></button>
                                    </div>
                                </div>
                            </div>

                            <div class="panel-body">
                                <p><?= __('Display news story between the following dates.') ?></p>

                                <div class="row gutters">
                                    <label class="col-xs-2 col-sm-1 control-label" for="item_date_publish">From</label>

                                    <div class="col-xs-4 col-sm-5">
                                        <?php
                                        $attributes = array('id' => 'item_date_publish');
                                        $calendar_icon = array('icon' => '<span class="icon-calendar"></span>', 'icon_position' => 'right');
                                        echo Form::ib_input(null, 'item_date_publish', @$item_data['date_publish'], $attributes, $calendar_icon);
                                        ?>
                                    </div>

                                    <label class="col-xs-2 col-sm-1 control-label" for="item_date_remove">To</label>

                                    <div class="col-xs-4 col-sm-5">
                                        <?php
                                        $attributes = array('id' => 'item_date_remove');
                                        echo Form::ib_input(null, 'item_date_remove', @$item_data['date_remove'], $attributes, $calendar_icon);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="item_order">Order</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-input" id="item_order" name="item_order" value="<?= isset($item_data['order']) ? $item_data['order'] : '' ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="item_media_type">Media type</label>
                    <div class="col-sm-4">
                        <?php ob_start(); ?>
                            <option value="">-- Please select --</option>
                            <?php foreach ($media_types as $media_type): ?>
                                <option
                                    value="<?= $media_type ?>"
                                    <?= !empty($item_data['media_type']) && $item_data['media_type'] == $media_type ? ' selected="selected"' : '' ?>
                                    ><?= $media_type ?></option>
                            <?php endforeach; ?>
                        <?php $options = ob_get_clean(); ?>

                        <?= Form::ib_select(null, 'item_media_type', $options, null, ['id' => 'item_media_type']); ?>
                    </div>
                </div>

                <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'courses')): ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="news-course_item">Course item</label>

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

                        <div class="col-sm-4">
                            <?= Form::ib_select(null, 'item_course_item_id', $options, null, ['id' => 'news-course_item']); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tab-pane" id="edit-news-tab-seo">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="item_seo_title">Page Title</label>
                    <div class="col-sm-8">
						<textarea name="item_seo_title" id="item_seo_title" rows="2" cols="20" class="form-control"
							><?=@$item_data['seo_title']?></textarea>
					</div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="item_seo_keywords">Keywords</label>
                    <div class="col-sm-8">
						<textarea name="item_seo_keywords" id="item_seo_keywords" rows="2" cols="20"
								  class="form-control"><?= @$item_data['seo_keywords'] ?></textarea></div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="item_seo_description">Meta Description</label>
                    <div class="col-sm-8">
						<textarea name="item_seo_description" id="item_seo_description" rows="2" cols="20"
								  class="form-control"><?= @$item_data['seo_description'] ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="footer_editor">Footer Text</label>
                    <div class="col-sm-8">
						<textarea name="item_seo_footer" id="footer_editor" rows="2" cols="20"
							><?= @$item_data['seo_footer'] ?></textarea>
					</div>
                </div>
            </div>

            <div class="tab-pane" id="edit-news-tab-share">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="edit_report_share_with">Share With</label>
                    <div class="col-sm-9">
                        <select class="form-control"  id="shared_with" name="shared_with">
                            <option value="0" data-value="everyone">Everyone</option>
                            <option value="1" data-value="group"<?= (count($shared_with_groups) > 0) ? ' selected="selected"' : '' ?>>Group</option>
                        </select>

                        <div id="share_with_groups_wrapper"<?= (count($shared_with_groups) == 0) ? ' style="display:none;"' : '' ?>>
                            <label for="share_with_groups"></label>
                            <select multiple="multiple" class="form-control multipleselect" id="shared_with_roles" name="shared_with_roles[]">
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>"<?= in_array($role['id'], $shared_with_groups) ? ' selected="selected"' : '' ?>>
                                        <?= $role['role'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" value="<?= @$item_data['id'] ?>" name="item_id" id="edit-news-item_id" />
    <input type="hidden" value="<?= (isset($item_data['id']) AND $item_data['id'] > 0) ? 'edit' : 'add'; ?>"
           name="editor_action" id="editor_action"/>
    <input type="hidden" value="/admin/news/add_edit_item" name="editor_redirect" id="editor_redirect"/>

    <div class="well floatingMenu">

		<div class="btn-group form-actions-group">
			<a href="#" class="btn btn-primary" id="btn_save">Save</a>
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu">
				<li><a href="#" id="btn_save_exit">Save &amp; Exit</a></li>
				<?php if ( ! empty($item_data['id']) AND Settings::instance()->get('twitter_api_access') == 1): ?>
				<li><a
						href="http://twitter.com/home/?status=<?= urlencode("News item posted\n".URL::site().'news/'.$item_data['category'].'/'.$item_data['title']) ?>"
						type="button"
						class="tweet-item-btn"
						>Tweet</a>
				</li>
				<?php endif; ?>
			</ul>
		</div>


		<a class="btn btn-default" href="">Reset</a>
        <?php if (isset($item_data['id']) AND $item_data['id'] > 0) { ?>
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
					<p><?= __('Are you sure you wish to delete this news story?') ?></p>
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
				<p>Please be sure to give a title for the news and select the category.</p>
			</div>

			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" id="btn_review">Review</a>
			</div>
		</div>
	</div>
</div>
<style>
    .datepicker-inline .datepicker-inline{display:none !important;}
    .edit-news-selected-date-events:not(:empty) + p {display: none;}
</style>