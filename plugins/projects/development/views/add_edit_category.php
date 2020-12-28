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
<form class="col-sm-9 form-horizontal" id="category_edit_form" method="post" action="<?=URL::site();?>admin/projects/save_category">
    <input type="hidden" name="id" value="<?=$category['id'];?>"/>
    <div class="form-group">
        <label class="col-sm-3 control-label" for="name">Category Name</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="name" name="name" value="<?=(isset($category['name'])) ? $category['name']: '';?>">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="parent">Parent</label>
        <div class="col-sm-9">
            <select class="form-control" name="parent" id="parent">
                <?=Model_Projects_Categories::get_categories_as_dropdown($category['parent']);?>
            </select>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" id="category_tab">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="summary">Summary</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="summary" name="summary" value="<?=(isset($category['summary'])) ? $category['summary']: '';?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="description">Description</label>
                <div class="col-sm-9">
                    <textarea class="form-control" id="description" name="description" rows="4"><?=(isset($category['description'])) ? $category['description']: '';?></textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="order">Order</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="order" name="order" value="<?=(isset($category['order'])) ? $category['order']: '0';?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="image">Image</label>
                <div class="col-sm-9">
                    <select class="form-control" name="image" id="image">
                        <option value="0">-- Select Image --</option>
                        <?php
                        echo Model_Media::factory('Media')->get_all_items_based_on('',NULL,'as_options','=',$category['image']);
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="publish">Publish</label>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-default<?= (! isset($category['publish']) OR $category['publish'] == '1') ? ' active' : '' ?>">
                        <input type="radio"<?= (! isset($category['publish']) OR $category['publish'] == '1') ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
                    </label>
                    <label class="btn btn-default<?= (isset($category['publish']) AND $category['publish'] == '0') ? ' active' : '' ?>">
                        <input type="radio"<?= ( isset($category['publish']) AND $category['publish'] == '0') ? ' checked="checked"' : '' ?> value="0" name="publish">No
                    </label>
                </div>
            </div>
        </div>

        <div class="well left width-920 bottom-bar">
            <button type="button" data-action="save" class="save_btn btn btn-success">Save</button>
            <button data-action="save_and_exit" class="save_btn btn btn-primary">Save &amp; Exit</button>
            <button id="cancel_button" class="btn">Cancel</button>
        </div>
	</div>
</form>
