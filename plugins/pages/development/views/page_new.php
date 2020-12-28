<form class="form-horizontal" name="frm_page_edit" action="/admin/pages/save_new_pag/" method="POST" id="frm_page_edit">
    <input type="hidden" value="new" name="pages_id" id="val_pages_id"/>
    <?php
    if (isset($alert)) {
        echo $alert;
    }
    ?>
    <?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
	?>
    <div class="form-group" id="page_edit_name">
        <input type="hidden" id="page_url" value="<?= URL::base() ?>"/>
		<div class="col-sm-8">
			<input id="inp_name" type="text" name="page_name" value="" class="form-control ib_text_title_input">
			<span style="display: none">Extension: <input type="text" name="page_extension" value="html"></span>
		</div>
    </div>
    <div class="tabbable"> <!-- Only required for left/right tabs -->
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab">Editor</a></li>
            <li><a href="#tab2" data-toggle="tab">SEO</a></li>
            <li><a href="#tab3" data-toggle="tab">Settings</a></li>
            <li><a href="#tab4" data-toggle="tab">Banners</a></li>
            <li><a href="#tab5" data-toggle="tab">Menu</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                <p>
                    <textarea class="form-control" id="page_editor" name="content" cols="" rows=""></textarea>
                </p>

            </div>
            <div class="tab-pane" id="tab2">
                <div class="form-group">
                    <div class="col-sm-2 control-label">Page Title</div>
                    <div class="col-sm-8"><textarea rows="2" cols="20" class="form-control" name="title"></textarea></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2 control-label">Keywords</div>
                    <div class="col-sm-8"><textarea rows="2" cols="20" class="form-control" name="seo_keywords"></textarea></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2 control-label">Meta Description</div>
                    <div class="col-sm-8"><textarea rows="2" cols="20" class="form-control" name="seo_description"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2 control-label">Footer Text</div>
                    <div class="col-sm-8"><textarea id="footer_editor" rows="2" cols="20" name="footer"></textarea></div>
                </div>
            </div>
            <div class="tab-pane" id="tab3">
                <div class="form-group">
                    <div class="col-sm-2 control-label">Page layout</div>
                    <div class="col-sm-5">
                        <select class="form-control" id="layout_id" name="layout_id">
                            <?php foreach ($layouts as $layout): ?>
                                <option value="<?= $layout['id'] ?>"<?= ($layout['layout'] == 'content') ? ' selected="selected"' : '' ?>><?= $layout['layout'] ?></option>
                            <?php endforeach; ?>
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
                    <div class="col-sm-2 control-label">Publish page</div>
                    <div class="col-sm-3">
                        <select class="form-control" name="publish">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2 control-label">Include in sitemap</div>
                    <div class="col-sm-3">
                        <select class="form-control" name="include_sitemap">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2 control-label">Category</div>
                    <div class="col-sm-5">
                        <select class="form-control" name="category_id">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= $category['category'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="tab-pane clearfix" id="tab4">
                <?php echo View::factory('page_banner_editor'); ?>
            </div>
            <div class="tab-pane" id="tab5">
                <div class="form-group">
                    <div class="col-sm-2 control-label">Add to Menu</div>
                    <div class="col-sm-5">
                        <select class="form-control" name="menu_group" id="menu_group">
                            <?= Model_Menus::getMenus(); ?>
                        </select>
                    </div>
                </div>

                <div style="display:none;" id="menu_addition">
                    <div class="form-group">
                        <div class="col-sm-2 control-label">Submenu of</div>
                        <div class="col-sm-5">
                            <select class="form-control" name="submenu_group" id="submenu_group">

                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-2 control-label">Order</div>
                        <div class="col-sm-1">
                            <input class="form-control" type="text" name="order_no" value="0" onkeypress="return isNumberKey(event)" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="ActionMenu" class="floatingMenu">
        <a class="btn btn-primary" id="btn_save_new">Save</a>
        <a class="btn btn-primary" id="btn_save_exit">Save & Exit</a>
        <a class="btn" href="">Reset</a>
    </div>
    <div class="floating-nav-marker"></div>

    <!-- Confirm window-->
    <div class="modal fade" id="confirm_delete">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h3>Are you sure you wish to delete this page?</h3>
				</div>

				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
				</div>
			</div>
		</div>
    </div>
</form>
