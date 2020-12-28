<?= (isset($alert)) ? $alert : '' ?>

<form name="frm_page_edit" action="/admin/pages/save_page/" method="POST" id="frm_page_edit" class="col-sm-12 form-horizontal validate-on-submit">
    <input type="hidden" name="pages_id" value="<?= $page_data[0]['id'] ?>" id="val_pages_id"/>
    <input type="hidden" name="draft_of" value="<?= $page_data[0]['draft_of'] ?>" />
    <input type="hidden" name="action" value="save" id="action"/>

	<div class="form-group" id="page_edit_name">
		<div class="col-sm-9">
			<input type="hidden" id="page_url" value="<?= URL::base().$page_data[0]['name_tag']?>"/>
			<input id="inp_name" type="text" name="page_name" value="<?=$page_data[0]['page_name']?>" class="form-control ib_text_title_input validate[required]" autofocus="autofocus" tabindex="1" />
		</div>
		<span style="display: none" >Extension: <input type="text" name="page_extension" value="<?=$page_data[0]['page_extension']?>"></span>
	</div>

    <?php if (!empty($page_data[0]['id'])): ?>
        <?php $url = $page_data[0]['name_tag'].(!empty($page_data[0]['draft_of']) ? '?draft=1' : ''); ?>
        <p>URL: <a href="/<?= $url ?>" target="_blank"><?= URL::site().$url ?></a></p>
    <?php endif; ?>

	<div class="tabbable"> <!-- Only required for left/right tabs -->
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab1" data-toggle="tab">Editor</a></li>
			<li><a href="#tab2" data-toggle="tab">SEO</a></li>
			<li><a href="#tab3" data-toggle="tab">Details</a></li>
			<li><a href="#tab4" data-toggle="tab">Banners</a></li>
			<?php if ($page_data[0]['id']): ?>
				<li><a href="#tab5" data-toggle="tab">Preview</a></li>
			<?php elseif (class_exists('Model_Menus')): ?>
				<li><a href="#tab5" data-toggle="tab">Menu</a></li>
			<?php endif; ?>
		</ul>
	  <div class="tab-content">
		<div class="tab-pane active" id="tab1">
			<p><textarea class="form-control" id="page_editor" name="content" cols="" rows="" tabindex="2"><?=$page_data[0]['content']?></textarea></p>

		</div>
		<div class="tab-pane" id="tab2">

			<div class="form-group">
				<label class="col-sm-2 control-label" for="edit_page_title">Page Title</label>
				<div class="col-sm-8">
					<textarea class="form-control" rows="2" name="title" id="edit_page_title"><?=$page_data[0]['title'] ? $page_data[0]['title'] : '' ?></textarea>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label" for="edit_page_seo_keywords">Keywords</label>
				<div class="col-sm-8">
					<textarea class="form-control" rows="2" name="seo_keywords" id="edit_page_seo_keywords"><?=trim($page_data[0]['seo_keywords'])?>
					</textarea>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label" for="edit_page_seo_description">Meta Description</label>
				<div class="col-sm-8">
					<textarea class="form-control" rows="2" name="seo_description" id="edit_page_seo_description"><?=trim($page_data[0]['seo_description'])?>
					</textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="footer_editor">Footer Text</label>
				<div class="col-sm-8">
					<textarea class="form-control" id="footer_editor" rows="2" name="footer" id="footer_editor"><?=$page_data[0]['footer']?></textarea>
				</div>
			</div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="x_robots_tag">X-Robots-Tag</label>
                <div class="col-sm-2">
                    <select class="form-control" name="x_robots_tag" id="edit_page_x_robots_tag">
                        <option value=""></option>
                        <?=HTML::optionsFromArray(
                            array(
                                'noindex' => 'noindex',
                                'nofollow' => 'nofollow',
                                'noarchive' => 'noarchive',
                                'noindex,nofollow' => 'noindex,nofollow'
                            ),
                            @$page_data[0]['x_robots_tag']
                        )?>
                    </select>
                </div>
            </div>
		</div>

		<div class="tab-pane" id="tab3">
			<div class="form-group">
				<label class="col-sm-2 control-label" for="layout_id">Layout</label>
				<div class="col-sm-5">
					<select class="form-control" name="layout_id" id="layout_id">
                        <?php
                        if ($page_data['0']['layout_id']) {
                            $selected = $page_data[0]['layout_id'];
                        }
                        else {
                            $selected = Model_Engine_Layout::get_default_layout();
                        }
                        ?>

						<?php foreach($layouts as $layout):?>

							<option value="<?= $layout->id ?>"<?= ($layout->id == $selected) ? ' selected="selected"' : ''; ?>>
                                <?= $layout->layout ?><?= $layout->template->title ? ' ('.$layout->template->title.')' : '' ?>
                            </option>
						<?php endforeach;?>
					</select>
				</div>
			</div>

			<?php if (Settings::instance()->get('use_config_file') === '0'): ?>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="edit_page_theme">Theme</label>
					<div class="col-sm-5">
						<select name="theme" class="form-control" id="edit_page_theme">
							<?= $theme_options ?>
						</select>
					</div>
				</div>
			<?php endif; ?>

			<div class="form-group">
				<label class="col-sm-2 control-label" for="edit_page_category_id">Category</label>
				<div class="col-sm-5">
					<select class="form-control" name="category_id" id="edit_page_category_id">
						<?php foreach ($categories as $category): ?>
							<option value="<?=$category['id']?>" <?php if($page_data[0]['category_id'] == $category['id']) echo 'selected="selected"'; ?>><?=$category['category']?></option>
						<?php endforeach;?>
					</select>
				</div>
			</div>

            <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'courses')): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="edit_page_course_item_id">Course link</label>
                    <div class="col-sm-5">
                        <select class="form-control" name="course_item_id" id="edit_page_course_item_id">
                            <option value="">-- None selected --</option>

                            <optgroup label="Courses">
                                <?php foreach ($courses as $option): ?>
                                    <option value="course-<?= $option->id ?>" <?= ($page_data[0]['course_id'] == $option->id) ? ' selected="selected"' : '' ?>><?= htmlspecialchars($option->title) ?></option>
                                <?php endforeach;?>
                            </optgroup>

                            <optgroup label="Categories">
                                <?php foreach ($course_categories as $option): ?>
                                    <option value="course_category-<?= $option->id ?>" <?= ($page_data[0]['course_category_id'] == $option->id) ? ' selected="selected"' : '' ?>><?= htmlspecialchars($option->category) ?></option>
                                <?php endforeach;?>
                            </optgroup>

                            <optgroup label="Subjects">
                                <?php foreach ($subjects as $option): ?>
                                    <option value="subject-<?= $option->id ?>" <?= ($page_data[0]['subject_id'] == $option->id) ? ' selected="selected"' : '' ?>><?= htmlspecialchars($option->name) ?></option>
                                <?php endforeach;?>
                            </optgroup>
                        </select>
                    </div>
                </div>
            <?php endif; ?>

			<div class="form-group">
				<label class="col-sm-2 control-label" for="edit_page_parent_id"><?= __('Parent page') ?></label>
				<div class="col-sm-5">
					<select class="form-control" name="parent_id" id="edit_page_parent_id">
						<option value=""><?= __('Please select') ?></option>
						<?php foreach ($pages as $parent_page): ?>
							<?php if ($parent_page['id'] != $page_data[0]['id']): ?>
								<option value="<?= $parent_page['id']?>"<?= ($page_data[0]['parent_id'] == $parent_page['id']) ? ' selected="selected"' : '' ?>><?= $parent_page['name_tag'] ?></option>
							<?php endif; ?>
						<?php endforeach;?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label" for="edit_page_publish">Publish</label>
				<div class="col-sm-2">
					<select class="form-control" name="publish" id="edit_page_publish">
						<option value="1"<?php if($page_data[0]['publish'] == '1') echo ' selected="selected"'; ?>>Yes</option>
						<option value="0"<?php if($page_data[0]['publish'] == '0') echo ' selected="selected"'; ?>>No</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label" for="edit_page_include_sitemap">Include in sitemap</label>
				<div class="col-sm-2">
					<select class="form-control" name="include_sitemap" id="edit_page_include_sitemap">
						<option value="1"<?php if($page_data[0]['include_sitemap'] == '1') echo ' selected="selected"'; ?>>Yes</option>
						<option value="0"<?php if($page_data[0]['include_sitemap'] == '0') echo ' selected="selected"'; ?>>No</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label" for="force_ssl">Force SSL</label>
				<div class="col-sm-2">
					<select class="form-control" name="force_ssl" id="edit_page_force_ssl">
						<option value="1"<?php if($page_data[0]['force_ssl'] == 1) echo ' selected="selected"'; ?>>Yes</option>
						<option value="0"<?php if($page_data[0]['force_ssl'] == 0) echo ' selected="selected"'; ?>>No</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label" for="nocache">Caching</label>
				<div class="col-sm-2">
					<select class="form-control" name="nocache" id="edit_page_nocache">
						<option value="1"<?php if($page_data[0]['nocache'] == 1) echo ' selected="selected"'; ?>>Off</option>
						<option value="0"<?php if($page_data[0]['nocache'] == 0) echo ' selected="selected"'; ?>>On</option>
					</select>
				</div>
			</div>
		</div>

		<div class="tab-pane clearfix" id="tab4">
            <?= View::factory('page_banner_editor', array('page_data' => $page_data[0])) ?>
		</div>
		  <?php if ($page_data[0]['id']): ?>
			  <div class="tab-pane" id="tab5">
				  <iframe style="width:1024px; min-height:400px;" src="<?php echo URL::base() . $page_data[0]['name_tag']?>" id="page_live_preview"></iframe>
			  </div>
		  <?php elseif (class_exists('Model_Menus')): ?>
			  <div class="tab-pane" id="tab5">
				  <div class="form-group">
					  <label class="col-sm-2 control-label" for="menu_group">Add to Menu</label>
					  <div class="col-sm-5">
						  <select class="form-control" name="menu_group" id="menu_group">
							  <?= Model_Menus::getMenus(); ?>
						  </select>
					  </div>
				  </div>

				  <div style="display:none;" id="menu_addition">
					  <div class="form-group">
						  <label class="col-sm-2 control-label" for="submenu_group">Submenu of</label>
						  <div class="col-sm-5">
							  <select class="form-control" name="submenu_group" id="submenu_group">

							  </select>
						  </div>
					  </div>

					  <div class="form-group">
						  <label class="col-sm-2 control-label" for="edit_page_menu_order_no">Order</label>
						  <div class="col-sm-1">
							  <input class="form-control" type="text" name="order_no" id="edit_page_menu_order_no" value="0" onkeypress="return isNumberKey(event)" />
						  </div>
					  </div>
				  </div>
			  </div>
		  <?php endif; ?>
	  </div>
	</div>

	<div id="ActionMenu" class="form-action-group floatingMenu">
        <a class="btn btn-primary" id="<?= $page_data[0]['id'] ? 'btn_save' : 'btn_save_new' ?>">Save and Publish</a>

        <button type="submit" name="action" value="save_and_exit" class="btn btn-primary" id="btn_save_exit">Save, Publish &amp; Exit</button>
        <?php if (!empty($page_data[0]['id']) && Settings::instance()->get('twitter_api_access') == 1): ?>
            <a
                href="http://twitter.com/home/?status=<?= urlencode("New page posted\n".URL::site().$page_data[0]['name_tag']) ?>"
                type="button"
                class="btn btn-default tweet-item-btn"
            >Tweet</a>
        <?php endif; ?>

        <?php if (Auth::instance()->has_access('pages_save_draft')): ?>
            <button type="submit" name="draft" value="1" class="btn btconn-default" id="btn_save_draft">Save as Draft</button>
        <?php endif; ?>

        <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')) { ?>
            <?php if ($page_data[0]['id']) { ?>
                <a class="btn btn-default" id="btn_send">Send Email</a>
            <?php } else { ?>
                <a class="btn btn-default" title="you need to save first to be able to send" disabled="disabled">Send Email</a>
            <?php } ?>
        <?php } ?>

        <?php if ($page_data[0]['id']): ?>
            <button type="button" class="btn btn-danger" data-target="#confirm_delete" id="btn_delete">Delete</button>
        <?php endif; ?>

        <a class="btn btn-default" href="/admin/pages">Cancel</a>
    </div>

	<div class="floating-nav-marker"></div>

	<?php if ($page_data[0]['id']): ?>
		<!-- Confirm window-->
		<div class="modal fade" id="confirm_delete">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h3>Are you sure you wish to delete this page?</h3>
					</div>
					<div class="modal-footer">
						<a href="#" class="btn btn-default" data-dismiss="modal">Cancel</a>
						<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</form>

<div id="upload_files_modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Upload Files</h3>
			</div>
			<div class="modal-body">

			</div>
			<div id="mu_s"></div>

			<div class="modal-footer">
				<a href="#" class="btn btn-default" data-dismiss="modal">Done</a>
			</div>
		</div>
	</div>
</div>

<?php
if ($messaging_enabled) {
    require Kohana::find_file('views', 'messaging_send_modal');
}
?>

<script type="text/javascript" src="<?php echo URL::get_engine_plugin_assets_base('media'); ?>js/multiple_upload.js"></script>
