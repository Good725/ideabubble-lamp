<?php
// Render CSS Files for THIS View
if (isset($view_css_files))
{
	foreach ($view_css_files as $css_item_html) echo $css_item_html;
}
// Render JS Files for This View
if (isset($view_js_files))
{
	foreach ($view_js_files as $js_item_html) echo $js_item_html;
}

// Check if is passed Image to be Added to current Sequence, i.e. NEW Image is going to be added
$item_image = (isset($image_to_add) AND trim($image_to_add) != '')? $image_to_add : '';
// If we are EDITING an Existent Sequence Item
if (isset($sequence_item_data['image'])) $item_image = $sequence_item_data['image'];

// Check if is passed Current Sequence ID to which Item should be Added
$item_sequence_id = (isset($item_sequence_id) AND $item_sequence_id > 0)? $item_sequence_id : 'new';
// If we are EDITING an Existent Sequence Item
if (isset($sequence_item_data['sequence_id'])) $item_sequence_id = $sequence_item_data['sequence_id'];

?>

<div class="col-sm-12 form-horizontal" id="custom_sequence_item_editor">
	<input type="hidden" name="item_sequence_id" id="item_sequence_id" value="<?=$item_sequence_id?>" />
	<input type="hidden" name="sequence_item_id" id="sequence_item_id" value="<?=isset($sequence_item_data['id'])? $sequence_item_data['id'] : 'new'?>" />
	<input type="hidden" name="sequence_item_image_location" id="sequence_item_image_location"
		   value="<?=isset($sequence_item_data['image_location'])? $sequence_item_data['image_location'] : $sequence_holder_plugin?>" />

	<div class="form-group">
		<label class="col-sm-2 control-label" for="sequence_item_title">Title:</label>
		<div class="col-sm-10">
			<input class="form-control" name="sequence_item_title" type="text" id="sequence_item_title" value="<?=(isset($sequence_item_data['title']))? $sequence_item_data['title'] : ''?>" size="35" />
		</div>
	</div>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="sequence_item_image">Image:</label>

        <div class="col-sm-5">
            <?php
            $options = '<option value="0" data-image_location="" data-image_filename=""> -- Select Image -- </option>';
            if (isset($available_images) && is_array($available_images)) {
                foreach ($available_images as $image_key => $media_image) {
                    //@TODO: check if we can use the Media Image $image_key which is an INT (id?) or the image Filename
                    // $options .= '<option value="'.$image_key.'"'
                    $options .= '<option value="'.$media_image['filename'].'"'
                        .(($item_image == $media_image['filename'])? ' selected="selected"' : '')
                        .' data-image_location="'.$media_image['location'].'"'
                        .' data-image_filename="'.$media_image['filename'].'"'.
                        '>'.$media_image['filename'].'</option>';
                }
            }
            $attributes = ['id' => 'sequence_item_image', 'onchange' => 'update_item_editor_image_preview()'];
            echo Form::ib_select(null, 'sequence_item_image', $options, null, $attributes);
            ?>
        </div>

        <div class="col-sm-5" id="image_preview">
            <?php
            if (trim($item_image) != '') {
                echo '<img src="'
                    .Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_image, $sequence_holder_plugin.DIRECTORY_SEPARATOR.'_thumbs_cms'
                    )
                    .'" alt="'.$item_image.'" />';
            }
            ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="sequence_item_image">Mobile Image:</label>

        <div class="col-sm-5">
            <?php
            $item_mobile_image = isset($sequence_item_data['mobile_image']) ? $sequence_item_data['mobile_image'] : '';
            $options = '<option value="0" data-image_location="" data-image_filename=""> -- Select Image -- </option>';
            if (isset($available_mobile_images) && is_array($available_images)) {
                foreach ($available_mobile_images as $image_key => $media_image) {
                    $options .= '<option value="'.$media_image['filename'].'"'
                        .(($item_mobile_image == $media_image['filename'])? ' selected="selected"' : '')
                        .' data-image_location="'.$media_image['location'].'"'
                        .' data-image_filename="'.$media_image['filename'].'"'.
                        '>'.$media_image['filename'].'</option>';
                }
            }
            $attributes = ['id' => 'sequence_item_mobile_image', 'onchange' => 'update_item_editor_image_preview(\'mobile\')'];
            echo Form::ib_select(null, 'sequence_item_mobile_image', $options, null, $attributes);
            ?>
        </div>

        <div class="col-sm-5" id="mobile_image_preview">
            <?php
            if (trim($item_mobile_image) != '') {
                echo '<img
                    src="'.Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_mobile_image, $sequence_holder_plugin.DIRECTORY_SEPARATOR.'_thumbs_cms').'"
                    alt="'.$item_mobile_image.'" />';
            }
            ?>
        </div>
    </div>

	<div class="form-group">
		<label class="col-sm-2 control-label" for="sequence_item_order_no">Order No:</label>
		<div class="col-sm-10">
			<input class="form-control" name="sequence_item_order_no" type="text" id="sequence_item_order_no"
				   value="<?=(isset($sequence_item_data['order_no']))? $sequence_item_data['order_no'] : ''?>"
				   onkeyup="this.value=this.value.replace(/[^0123456789]/,'');" size="5"/>
		</div>
	</div>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="sequence_item_label">Label:</label>
        <div class="col-sm-10">
            <input class="form-control" name="sequence_item_label" type="text" id="sequence_item_label" value="<?= @$sequence_item_data['label'] ?>" size="35" />
        </div>
    </div>

    <div class="form-group">
		<label class="col-sm-2 control-label" for="sequence_item_html">HTML:</label>
		<div class="col-sm-10">
			<textarea class="form-control" name="sequence_item_html_contents" id="sequence_item_html" cols="" rows=""><?=(isset($sequence_item_data['html']))? $sequence_item_data['html'] : ''?></textarea>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label" for="sequence_item_link_type">Link Type:</label>
		<div class="col-sm-5">
			<select class="form-control" name="sequence_item_link_type" id="sequence_item_link_type" onchange="update_sequence_urls_feed_based_on_link_type(this.value, 'sequence_item_link_url_holder');">
				<option value="">-- Select Link Type --</option>
				<?php
				if (isset($link_types))
				{
					foreach($link_types as $link_type_key => $link_type)
					{
						$isSelected = !empty($sequence_item_data['link_type']) && $sequence_item_data['link_type'] == $link_type_key ? ' selected="selected"' : '';
						echo '<option value="' . $link_type_key . '"'
						. $isSelected
						. '>' . $link_type . '</option>';
					}
				}
				?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label" for="sequence_item_link_url">Link URL:</label>
		<div class="col-sm-5 sequence_item_link_url_holder">
			<?php
				// Item Link type is INTERNAL -=> Provide a Drop Down with INTERNAL Pages
				if (isset($sequence_item_data['link_type']) AND $sequence_item_data['link_type'] == 'internal')
				{
					echo Model_Customscroller::factory('Customscroller')->get_pages_drop_down_for_editor(
						((isset($sequence_item_data['link_url']))? $sequence_item_data['link_url'] : NULL),
						NULL
					);
				}
				else
				{
					// Item Link type is EXTERNAL or NONE -=> will be Added in an INPUT field
					echo '<input type="text"'
							  .' name="sequence_item_link_url"'
							  .' class="form-control sequence_item_link_url"'
						 	  .' id="sequence_item_link_url"'
							  .' value="'.((isset($sequence_item_data['link_url']))? $sequence_item_data['link_url'] : '').'"'
							  .' size="5"/>';
				}
			?>
		</div>
		<input type="hidden" name="tmp_item_ext_link_url" class="tmp_item_ext_link_url"
			   value="<?=(isset($sequence_item_data['link_url']) AND isset($sequence_item_data['link_type']) AND $sequence_item_data['link_type'] == 'external')? $sequence_item_data['link_url'] : ''?>" />
		<input type="hidden" name="tmp_item_int_link_url" class="tmp_item_int_link_url"
			   value="<?=(isset($sequence_item_data['link_url']) AND isset($sequence_item_data['link_type']) AND $sequence_item_data['link_type'] == 'internal')? $sequence_item_data['link_url'] : ''?>" />
	</div>

	<div class="form-group">
		<div class="col-sm-2 control-label">Link Target:</div>
		<div class="col-sm-10">
			<div class="btn-group" data-toggle="buttons">
				<?php $same_tab = ( ! isset($sequence_item_data['link_target']) OR $sequence_item_data['link_target'] == '0'); ?>
				<label class="btn btn-plain<?= $same_tab ? ' active' : '' ?>">
					<input type="radio" id="sequence_item_link_target_yes" name="sequence_item_link_target" value="1"<?= $same_tab ? ' checked' : '' ?> />Same Window/Tab
				</label>
				<label class="btn btn-plain<?= ( ! $same_tab) ? ' active' : '' ?>">
					<input type="radio" id="sequence_item_link_target_no" name="sequence_item_link_target" value="0"<?= ( ! $same_tab) ? ' checked' : '' ?> />New Window/Tab
				</label>
			</div>
		</div>
	</div>

    <div class="form-group">
        <?php $overlay_position = (isset($sequence_item_data['overlay_position'])) ? $sequence_item_data['overlay_position'] : ''; ?>
        <label class="col-sm-2 control-label" for="sequence_item_overlay_position">Overlay Position</label>
        <div class="col-sm-5">
            <select name="sequence_item_overlay_position" id="sequence_item_overlay_position">
                <option value="center"<?= ($overlay_position == 'center') ? ' selected="selected"' : '' ?>>Centre</option>
                <option value="left"<?= ($overlay_position == 'left') ? ' selected="selected"' : '' ?>>Left</option>
                <option value="right"<?= ($overlay_position == 'right') ? ' selected="selected"' : '' ?>>Right</option>
            </select>
        </div>
    </div>

	<div class="form-group">
		<div class="col-sm-2 control-label">Publish:</div>
		<div class="col-sm-10">
			<div class="btn-group" data-toggle="buttons">
				<?php $publish = ( ! isset($sequence_item_data['publish']) OR $sequence_item_data['publish'] == '1'); ?>

				<label class="btn btn-plain<?= $publish ? ' active' : '' ?>">
					<input type="radio" id="sequence_item_publish_yes" name="sequence_item_publish" value="1"<?= $publish ? ' checked' : '' ?> />Yes
				</label>

				<label class="btn btn-plain<?= ( ! $publish) ? ' active' : '' ?>">
					<input type="radio" id="sequence_item_publish_no" name="sequence_item_publish" value="0"<?= ( ! $publish) ? ' checked' : '' ?> />No
				</label>
			</div>
		</div>
	</div>
</div>