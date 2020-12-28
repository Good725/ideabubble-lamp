<? $field_data = (count($_POST) > 0) ? $_POST : (isset($field_data) ? $field_data : array()) ?>

<div class="row">
    <div class="page-header clearfix">
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
        <h2 class=""><?=($action == 1) ? 'Add Gallery' : 'Edit Gallery'?></h2>
    </div>

</div>

<form class="form-horizontal" id="form_add_edit_gallery" name="form_add_edit_gallery" action="/admin/gallery/save/" method="post">

        <div id="main-controls-container" class="col-sm-4 text-left">
            <div class="form-group">
                <label class="col-sm-12 control-label" for="photo_name">Gallery Pictures:</label>
                <div class="col-sm-12">
                    <div class="selectbox">
                        <select class="form-control" id="photo_name" name="photo_name">
                            <?php if ($action == 1): ?>
                            	<option value="dummy" selected="selected">-- Pick One --</option>
                            <?php endif; ?>

                            <?php foreach ($image_list as $item): ?>
                            	<option value="<?=$item['filename']?>" <?=( isset($field_data['photo_name']) AND ($field_data['photo_name'] == $item['filename']) ) ? 'selected="selected"' : '' ?>><?=$item['filename']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>    
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-12 control-label" for="category">Category</label>
                <div class="col-sm-6">
                    <div class="selectbox">
                        <select class="form-control" id="category" name="category">
                            <?php foreach ($category_list as $item): ?>
                            	<option value="<?=$item?>" <?=( isset($field_data['category']) AND ($field_data['category'] == $item) ) ? 'selected="selected"' : '' ?>><?=$item?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>    
    			</div>
    			<div class="col-sm-6">
                    <input type="text" class="form-control" id="new_category" name="new_category" value="<?=isset($field_data['new_category']) ? $field_data['new_category'] : ''?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-12 control-label" for="title">Title</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control" id="title" name="title" value="<?=isset($field_data['title']) ? $field_data['title'] : ''?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-12 control-label" for="order">Order</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control" id="order" name="order" value="<?=isset($field_data['order']) ? $field_data['order'] : ''?>">
                </div>
            </div>

            <div class="form-group">
				<?php
				$selected = '';
				$active_yes = '';
				$active_no = '';
				/* for the add gallery showing the publish as active declare active_yes variable as active*/
				if(!isset($field_data['publish']))
				{
					$active_yes = 'active';
				}
				if(isset($field_data['publish']) AND ($field_data['publish'] == 1))
				{
					$active_yes = 'active';
				}
				if(isset($field_data['publish']) AND ($field_data['publish'] == 0))
				{
					$active_no = 'active';
				}
				?>
                <label class="col-sm-2 control-label"  for="publish">Publish</label>
                <div class="col-sm-7">
                    <div class="btn-group btn-group-slide" data-toggle="buttons">
                        <label class="btn btn-plain <?php echo $active_yes;?>">
                            <input type="radio" <?=( isset($field_data['publish']) AND ($field_data['publish'] == 1) ) ? 'selected="selected"' : '' ?> value="1" name="publish">Yes
                        </label>
                        <label class="btn btn-plain <?php echo $active_no;?>">
                            <input type="radio" <?=( isset($field_data['publish']) AND ($field_data['publish'] == 0) ) ? 'selected="selected"' : '' ?> value="0" name="publish">No
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <input type="hidden" id="id" name="id" value="<?=isset($field_data['id']) ? $field_data['id'] : ''?>">
                </div>
            </div>
        </div>

        <div class="col-sm-8">
            <label class="col-sm-12 control-label">&nbsp;</label>
            <div id="picture-preview-container">
                <img id="picture-preview" src="" alt="" data-media-root="<?=Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'gallery')?>"/>
            </div>
        </div>
     
<div class="clearfix"></div>
        <div class="form-actions col-sm-8 form-action-group">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="reset" class="btn">Reset</button>
        </div>


</form>
