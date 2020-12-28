<? $data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : array()) ?>

<div class="row">
    <div class="span12">
        <?=(isset($alert)) ? $alert : ''?>
        <?php
			if(isset($alert)){
			?>
				<script>
					remove_popbox();
				</script>
			<?php
			}
		?>
    </div>
</div>

<form class="col-sm-9 form-horizontal" id="form_add_edit_option" name="form_add_edit_option" action="/admin/products/save_option/" method="post">
    <!-- Label -->
    <div class="form-group">
		<div class="col-sm-12">
			<input type="text" class="form-control required" id="label" name="label" placeholder="Enter option label here" value="<?=isset($data['label']) ? $data['label'] : ''?>"/>
		</div>
    </div>

    <!-- Group -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="group_id">Group Name</label>
        <div class="col-sm-4">
            <select class="form-control" id="group_id" name="group_id">
                <?php if ( ! isset($data['group_id']) OR ($data['group_id'] == '') ): ?>
                    <option value="">-- Select Group --</option>
                <?php endif; ?>
                <?php foreach ($groups as $itemId => $item): ?>
                    <option value="<?=$itemId?>" <?=( isset($data['group_id']) AND ($data['group_id'] == $itemId) ) ? 'selected="selected"' : '' ?>><?=$item?></option>
                <?php endforeach; ?>
            </select>
		</div>
		<div class="col-sm-5">
            <input type="text" class="form-control" id="new_group" name="new_group" placeholder="Enter new group text" value="<?=isset($data['new_group']) ? $data['new_group'] : ''?>">
        </div>
    </div>

    <!-- Group Label -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="group_label">Group Label</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" id="group_label" name="group_label" value="<?=isset($data['group_label']) ? $data['group_label'] : ''?>" <?=(count($data) > 0) ? 'readonly' : '';?>/>
        </div>
    </div>

	<!-- Value -->
	<div class="form-group">
		<label class="col-sm-3 control-label" for="value">Value</label>
		<div class="col-sm-4">
			<input type="text" class="form-control" id="value" name="value" value="<?=isset($data['value']) ? $data['value'] : ''?>"/>
		</div>
	</div>

	<!-- Value -->
	<div class="form-group">
		<div class="col-sm-3 control-label" >Group Default</div>
		<div class="col-sm-4">
			<div class="btn-group" data-toggle="buttons">
				<?php $default = (isset($data['default']) AND $data['default'] == '1'); ?>
				<label class="btn btn-default<?= $default ? ' active' : '' ?>">
					<input type="radio"<?= $default ? ' checked="checked"' : '' ?> value="1" name="default">Yes
				</label>
				<label class="btn btn-default<?= ( ! $default) ? ' active' : '' ?>">
					<input type="radio"<?= ( ! $default) ? ' checked="checked"' : '' ?> value="0" name="default">No
				</label>
			</div>
		</div>
	</div>

    <!-- Description -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="edit_option_description">Description</label>
        <div class="col-sm-9">
            <textarea id="edit_option_description" name="description"><?= isset($data['description']) ? $data['description'] : '' ?></textarea>
        </div>
    </div>

    <!-- Message -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="edit_option_message">Message</label>
        <div class="col-sm-9">
            <textarea class="ckeditor" id="edit_option_message" name="message"><?= isset($data['message']) ? $data['message'] : '' ?></textarea>
        </div>
    </div>

    <!-- Image -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="image">Image</label>
        <div class="col-sm-4">
            <select class="form-control" id="image" name="image">
                <option value="" <?=( ! isset($data['image']) OR ($data['image'] == '') ) ? 'selected="selected"' : '' ?>>No Image</option>

                <?php foreach ($images as $item): ?>
                    <option value="<?=$item['filename']?>" <?=( isset($data['image']) AND ($data['image'] == $item['filename']) ) ? 'selected="selected"' : '' ?>><?=$item['filename']?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-5">
            <div id="image_preview_container">
                <img id="image_preview" src="" alt=""/>
            </div>
        </div>
    </div>

    <!-- Price -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="price">Price</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" id="price" name="price" value="<?=isset($data['price']) ? $data['price'] : ''?>"/>
        </div>
    </div>

    <!-- Publish -->

    <div class="form-group">
        <label class="col-sm-3 control-label" for="project_publish_toggle">Publish</label>
        <div class="btn-group col-sm-4" data-toggle="buttons">
            <label class="btn btn-default<?= (! isset($data['publish']) OR $data['publish'] == '1') ? ' active' : '' ?>">
                <input type="radio"<?= (! isset($data['publish']) OR $data['publish'] == '1') ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
            </label>
            <label class="btn btn-default<?= (isset($data['publish']) AND $data['publish'] == '0') ? ' active' : '' ?>">
                <input type="radio"<?= (isset($data['publish']) AND $data['publish'] == '0') ? ' checked="checked"' : '' ?> value="0" name="publish">No
            </label>
        </div>
    </div>

    <!-- Option Identifier -->
    <input type="hidden" id="id" name="id" value="<?=isset($data['id']) ? $data['id'] : ''?>"/>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="reset" class="btn">Reset</button>
    </div>
</form>
