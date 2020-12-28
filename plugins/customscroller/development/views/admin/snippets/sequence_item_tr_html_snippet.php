<?php
// Get the Image - filename part without the extension
$image_parts = explode('.', $sequence_item_data['image']);
$item_image = $image_parts[0];
?>
<tr id="tr_id_<?=$sequence_item_data['id']?>">
    <td>
        <input type="text" class="span1"
               name="sequence_data[sequence_items][<?=$sequence_item_data['id'].$item_image?>][order_no]"
               id="<?=$item_image?>_order_no" value="<?=$sequence_item_data['order_no']?>" onkeyup="this.value=this.value.replace(/[^0123456789]/,'');" size="5"/>
        <input type="hidden" name="sequence_data[sequence_items][<?=$sequence_item_data['id'].$item_image?>][id]" value="<?=$sequence_item_data['id']?>" />
        <input type="hidden" name="sequence_data[sequence_items][<?=$sequence_item_data['id'].$item_image?>][sequence_id]" value="<?=$sequence_item_data['sequence_id']?>" />
    </td>
	<td>
		<img class="span2"
			 src="<?=Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$sequence_item_data['image'], $sequence_item_data['image_location'].DIRECTORY_SEPARATOR.'_thumbs_cms')?>"
			 alt="<?=$sequence_item_data['image']?>" />
		<input type="hidden" name="sequence_data[sequence_items][<?=$sequence_item_data['id'].$item_image?>][image]" value="<?=$sequence_item_data['image']?>" />
		<input type="hidden" name="sequence_data[sequence_items][<?=$sequence_item_data['id'].$item_image?>][image_location]" value="<?=$sequence_item_data['image_location']?>" />
	</td>
    <td><span id="item_<?=$sequence_item_data['id']?>_image"><?=$sequence_item_data['image']?></span></td>
    <td>
        <span id="item_<?=$sequence_item_data['id']?>_mobile_image"><?=$sequence_item_data['mobile_image']?></span>
        <input type="hidden" name="sequence_data[sequence_items][<?= $sequence_item_data['id'].$item_image ?>][mobile_image]" value="<?=$sequence_item_data['mobile_image']?>" />
    </td>
    <td>
        <input type="text"
               name="sequence_data[sequence_items][<?=$sequence_item_data['id'].$item_image?>][title]"
               id="item_<?=$sequence_item_data['id']?>_title" value="<?=$sequence_item_data['title']?>" size="10"/>
    </td>
    <td>
        <input type="text"
               name="sequence_data[sequence_items][<?=$sequence_item_data['id'].$item_image?>][label]"
               id="item_<?=$sequence_item_data['id']?>_label" value="<?=$sequence_item_data['label']?>" size="10"/>
    </td>
    <td>
		<span class="sequence_item_edit link" onclick="get_sequence_scroller_item_editor(<?=$sequence_item_data['id']?>, '<?=$sequence_item_data['image']?>')">
			<?=((trim($sequence_item_data['html']) != '')? 'Edit' : 'Add')?>
		</span>
		<textarea  cols="30" rows="2" style="display:none;"
				   name="sequence_data[sequence_items][<?=$sequence_item_data['id'].$item_image?>][html]" id="html_<?=$sequence_item_data['image']?>"><?=$sequence_item_data['html']?></textarea>
	</td>
	<td>
		<select
				name="sequence_data[sequence_items][<?=$sequence_item_data['id'].$item_image?>][link_type]"
				id="link_type_<?=$sequence_item_data['image']?>" class="span2"
				onchange="update_sequence_urls_feed_based_on_link_type(this.value, 'sequence_item_<?=$item_image?>_link_url_holder', '<?=$item_image?>');">
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
	</td>
	<td>
		<span class="sequence_item_<?=$item_image?>_link_url_holder">
			<?php
			// Item Link type is INTERNAL -=> Provide a Drop Down with INTERNAL Pages
			if (isset($sequence_item_data['link_type']) AND $sequence_item_data['link_type'] == 'internal')
			{
				echo Model_Customscroller::factory('Customscroller')->get_pages_drop_down_for_editor(
					((isset($sequence_item_data['link_url']))? $sequence_item_data['link_url'] : NULL),
					$item_image
				);
			?>
			<script>
			$("[id='<?='sequence_item_' . $item_image . '_' . 'link_url'?>']").on("change", function(){
				$("[id='sequence_item_<?=$item_image?>_link_url_hidden']").val(this.value);
			});
			</script>
			<?php
			}
			else
			{
				// Item Link type is EXTERNAL or NONE -=> will be Added in an INPUT field
				echo '<input type="text"'
					      .' name="sequence_item_link_url"'
					      .' id="sequence_item_'.$item_image.'link_url"'
					  	  .' class="sequence_item_link_url form-control"'
					 	  .' value="'.((isset($sequence_item_data['link_url']))? $sequence_item_data['link_url'] : '').'"'
					 	  .' onkeyup="if(typeof $(\'#sequence_item_'.$item_image.'_link_url_hidden\') !== \'undefined\') $(\'#sequence_item_'.$item_image.'_link_url_hidden\').val(this.value);"'
					 	  .' size="5"/>';
			}
			?>
		</span>
		<input type="hidden" name="sequence_data[sequence_items][<?=$sequence_item_data['id'].$item_image?>][link_url]" id="sequence_item_<?=$item_image?>_link_url_hidden"
			   value="<?=$sequence_item_data['link_url']?>" />
		<input type="hidden" name="tmp_item_ext_link_url" class="tmp_item_ext_link_url"
			   value="<?=(isset($sequence_item_data['link_url']) AND isset($sequence_item_data['link_type']) AND $sequence_item_data['link_type'] == 'external')? $sequence_item_data['link_url'] : ''?>" />
		<input type="hidden" name="tmp_item_int_link_url" class="tmp_item_int_link_url"
			   value="<?=(isset($sequence_item_data['link_url']) AND isset($sequence_item_data['link_type']) AND $sequence_item_data['link_type'] == 'internal')? $sequence_item_data['link_url'] : ''?>" />
	</td>
	<td>
		<div class="span3 controls">
			<div class="btn-group" data-toggle="buttons">
				<?php $same_tab = ( ! isset($sequence_item_data['link_target']) OR $sequence_item_data['link_target'] == '0'); ?>

				<label class="btn btn-plain<?= $same_tab ? ' active' : '' ?>">
					<input type="radio" name="sequence_data[sequence_items][<?=$sequence_item_data['id'].$item_image?>][link_target]" value="0"<?= $same_tab ? ' checked' : '' ?> />Same Tab
				</label>

				<label class="btn btn-plain<?= ( ! $same_tab) ? ' active' : '' ?>">
					<input type="radio" name="sequence_data[sequence_items][<?=$sequence_item_data['id'].$item_image?>][link_target]" value="1"<?= ( ! $same_tab) ? ' checked' : '' ?> />New Tab
				</label>

			</div>
		</div>
	</td>

    <td>
        <?php $location = isset($sequence_item_data['overlay_position']) ? $sequence_item_data['overlay_position'] : ''; ?>
        <select name="sequence_data[sequence_items][<?=$sequence_item_data['id'].$item_image?>][overlay_position]" id="item<?= $sequence_item_data['id'] ?>_overlay_position">
		    <option value="center"<?= (($location== 'center') ? ' selected="selected"' : '') ?>>Centre</option>
            <option value="left"<?=   (($location == 'left')  ? ' selected="selected"' : '') ?>>Left</option>
            <option value="right"<?=  (($location == 'right') ? ' selected="selected"' : '') ?>>Right</option>
        </select>
    </td>

	<td>
		<i class="icon-<?= (($sequence_item_data['publish'] == 1) ? 'ok' : 'remove') ?>" data-id="<?=$sequence_item_data['id']?>"></i>
		<input type="hidden" name="sequence_data[sequence_items][<?=$sequence_item_data['id'].$item_image?>][publish]" value="<?=$sequence_item_data['publish']?>" />
	</td>
	<td class="delete" data-id="<?=$sequence_item_data['id']?>">
		<i class="icon-remove-circle" onclick="toggleDelete(<?=$sequence_item_data['id']?>)"></i>
	</td>
</tr>