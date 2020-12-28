<?= (isset($alert)) ? $alert : ''; ?>
<form class="col-sm-9 form-horizontal" id="category_edit_form" method="post" action="<?=URL::site();?>admin/reports/save_category">
    <input type="hidden" name="id" value="<?=$category->get_id();?>"/>
    <div class="form-group">
        <label class="col-sm-3 control-label" for="name">Category Name</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="name" name="name" value="<?=$category->get_name();?>">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="parent">Parent</label>
        <div class="col-sm-9">
            <select class="form-control" name="parent" id="parent">
                <option value="">---Select a category---</option>
                <?=$category->get_categories_dropdown();?>
            </select>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" id="category_tab">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="summary">Summary</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="summary" name="summary" value="<?=$category->get_summary();?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="description">Content</label>
                <div class="col-sm-9">
                    <textarea class="form-control" id="content" name="content" rows="4"><?=$category->get_content();?></textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="order">Order</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="order" name="order" value="<?=$category->get_order();?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="project_publish_toggle">Publish</label>
				<div class="col-sm-9">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default<?= ($category->get_publish() === 1) ? ' active' : '' ?>">
                            <input type="radio"<?= ($category->get_publish()  === 1) ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
                        </label>
                        <label class="btn btn-default<?= ($category->get_publish() === 0) ? ' active' : '' ?>">
                            <input type="radio"<?= ( $category->get_publish()  === 0) ? ' checked="checked"' : '' ?> value="0" name="publish">No
                        </label>
                    </div>
				</div>
            </div>
        </div>

		<div class="col-sm-12">
			<div class="well left width-920 bottom-bar">
				<button type="button" data-action="save" class="save_btn btn btn-success">Save</button>
				<button data-action="save_and_exit" class="save_btn btn btn-primary">Save &amp; Exit</button>
				<button id="cancel_button" class="btn">Cancel</button>
			</div>
		</div>
	</div>
</form>