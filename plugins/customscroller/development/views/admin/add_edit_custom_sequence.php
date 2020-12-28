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
?>

<div id="custom_sequence_editor">
	<input type="hidden" name="sequence_data[sequence_holder_id]" id="sequence_holder_id" value="<?=(isset($sequence_holder_id) AND $sequence_holder_id !== NULL)? $sequence_holder_id : ''?>" />
	<input type="hidden" name="sequence_data[sequence_holder_plugin]" id="sequence_holder_plugin" value="<?=isset($sequence_holder_plugin)? $sequence_holder_plugin : ''?>" />

	<div class="form-group">
		<label class="col-sm-3 control-label" for="sequence_id">Select sequence:</label>
		<div class="col-sm-6">
			<select class="form-control" id="sequence_id" name="sequence_data[id]" onchange="update_sequence_information(this)">
				<option value="new" <?php if (!isset($sequence_data['id'])) echo 'selected="selected"';?>> -- New Sequence -- </option>
				<?php
                if (isset($existing_sequences) AND is_array($existing_sequences) AND count($existing_sequences) > 0)
                {
                    foreach ($existing_sequences as $sequence_id => $available_sequence)
                    {
                        echo '<option value="'.$available_sequence['id'].'" '
                            .'data-sequence_title = "'.$available_sequence['title'].'" '
                            .'data-animation_type = "'.$available_sequence['animation_type'].'" '
                            .'data-order_type     = "'.$available_sequence['order_type'].'" '
                            .'data-rotating_speed = "'.$available_sequence['rotating_speed'].'" '
                            .'data-timeout        = "'.$available_sequence['timeout'].'" '
                            .'data-pagination     = "'.$available_sequence['pagination'].'" '
                            .'data-controls       = "'.$available_sequence['controls'].'" '
                            .'data-publish        = "'.$available_sequence['publish'].'"'
                            .((isset($sequence_data['id']) AND $sequence_data['id'] == $available_sequence['id'])? ' selected="selected"' : '')
                            .'>'
                            .$available_sequence['title']
                            .'</option>';
                    }
                }
				?>
			</select>
		</div>

		<?php /* WPPROD-171 <span class="span3" onclick="alert('deleteSequence() now');" style="cursor: pointer;">Delete this Sequence</span><br /> */?>
	</div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="sequence_title">Sequence Name:</label>
        <div class="col-sm-6">
            <input class="form-control" name="sequence_data[sequence_title]" type="text" id="sequence_title"
                   value="<?=(isset($sequence_data['title']))? $sequence_data['title'] : ''?>" size="35"

            />
        </div>
    </div>



	<? /* @TODO: Enable this Option if Required ?>
	<label for="banner_text_position">Text position:</label>
	<?//=form_dropdown('banner_text_positions', $banner_text_positions, $Page->text_position, ' id="banner_text_positions"'.$mzpx);?><br />
	<? */ ?>

	<div class="form-group">
		<label class="col-sm-3 control-label" for="animation_type">Scrolling Type:</label>
		<div class="col-sm-6">
			<select class="form-control" id="animation_type" name="sequence_data[animation_type]">
				<option value="0"> -- Select Animation Type -- </option>
				<?php
				if (isset($animation_types) AND is_array($animation_types))
				{
					foreach ($animation_types as $animation_key => $animation_type)
					{
						echo '<option value="'.$animation_key.'"'
							 .((isset($sequence_data['animation_type']) AND $sequence_data['animation_type'] == $animation_key)? ' selected="selected"' : '')
							 .'>'.$animation_type.'</option>';
					}
				}
				?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label" for="order_type">Display Order:</label>
		<div class="col-sm-6">
			<select class="form-control" id="order_type" name="sequence_data[order_type]">
				<option value="0"> -- Select Order -- </option>
				<?php
				if (isset($order_types) AND is_array($order_types))
				{
					foreach ($order_types as $order_key => $order_type)
					{
						echo '<option value="'.$order_key.'"'
							 .((isset($sequence_data['order_type']) AND $sequence_data['order_type'] == $order_key)? ' selected="selected"' : '')
							 .'>'.$order_type.'</option>';
					}
				}
				?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label" for="rotating_speed">Rotating Speed:</label>
		<div class="col-sm-6">
			<input class="form-control popinit" name="sequence_data[rotating_speed]" type="text" id="rotating_speed" rel="popover" size="10"
                   value="<?=(isset($sequence_data['rotating_speed']))? $sequence_data['rotating_speed'] : '2000'?>"
                   onkeyup="this.value=this.value.replace(/[^0123456789]/,'');" data-original-title="Rotating speed"
                   data-content="The amount of time in milliseconds it will take for one item to transition onto the next. (Default: 2000)"
            />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label" for="timeout">Timeout Speed:</label>
		<div class="col-sm-6">
			<input class="form-control popinit" name="sequence_data[timeout]" type="text" id="timeout" rel="popover" size="10"
                   value="<?=(isset($sequence_data['timeout']))? $sequence_data['timeout'] : '8000'?>"
                   onkeyup="this.value=this.value.replace(/[^0123456789]/,'');" data-original-title="Timeout speed"
                   data-content="The amount of time in milliseconds each item will be displayed before transitioning on to the next item. (Default: 8000)"
            />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label">Pagination:</label>
		<div class="col-sm-6">
			<div class="btn-group" data-toggle="buttons">
				<?php $pagination = ( ! isset($sequence_data['pagination']) OR $sequence_data['pagination'] == '1'); ?>
				<label class="btn btn-plain<?= $pagination ? ' active' : '' ?>">
					<input type="radio" name="sequence_data[pagination]" value="1"<?= $pagination ? ' checked' : '' ?> />Yes
				</label>
				<label class="btn btn-plain<?= ( ! $pagination) ? ' active' : '' ?>">
					<input type="radio" name="sequence_data[pagination]" value="0"<?= ( ! $pagination) ? ' checked' : '' ?> />No
				</label>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label">Controls:</label>
		<div class="col-sm-6">
			<div class="btn-group" data-toggle="buttons">
				<?php $show_controls = ( ! isset($sequence_data['controls']) OR $sequence_data['controls'] == '1'); ?>
				<label class="btn btn-plain<?= $show_controls ? ' active' : '' ?>">
					<input type="radio" name="sequence_data[controls]" value="1"<?= $show_controls ? ' checked' : '' ?> />Yes
				</label>
				<label class="btn btn-plain<?= ( ! $show_controls) ? ' active' : '' ?>">
					<input type="radio" name="sequence_data[controls]" value="0"<?= ( ! $show_controls) ? ' checked' : '' ?> />No
				</label>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-3 control-label">Publish:</div>
		<div class="col-sm-6">
			<div class="btn-group" data-toggle="buttons">
				<?php $publish = ( ! isset($sequence_data['publish']) OR $sequence_data['publish'] == '1'); ?>
				<label class="btn btn-plain<?= $publish ? ' active' : '' ?>">
					<input type="radio" name="sequence_data[publish]" value="1"<?= $publish ? ' checked' : '' ?> />Yes
				</label>
				<label class="btn btn-plain<?= ( ! $publish) ? ' active' : '' ?>">
					<input type="radio" name="sequence_data[publish]" value="0"<?= ( ! $publish) ? ' checked' : '' ?> />No
				</label>
			</div>
		</div>
	</div>

	<div class="row">
		<h2>Sequence Items:</h2>
		<div class="col-sm-12">
			<table class="table table-striped table-bordered table-condensed dataTable" id="custom_sequence_table">
			    <thead>
			    <tr>
			        <th>Order</th>
			        <th>Image</th>
                    <th>Image name</th>
                    <th>Mobile image</th>
                    <th>Title</th>
			        <th>Label</th>
			        <th>HTML</th>
			        <th>Link type</th>
                    <th>Link target</th>
                    <th>Link location</th>
                    <th>Overlay position</th>
			        <th>Publish</th>
			        <th>Delete</th>
			    </tr>
			    </thead>
			    <tbody>

			    </tbody>
			</table>
		</div>

	</div>

	<div class="row">
		<h2>Available images:</h2>
		<div id="available_images">
			<?
            if (isset($all_images) AND count($all_images) > 0)
            {
                foreach ($all_images as $available_image)
                {
                    echo View::factory (
                        'admin/snippets/sequence_available_image_html_snippet',
                        array (
                            'available_image' => $available_image
                        )
                    )->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render ();
                }
            }
			?>
		</div>
	</div>

	<!-- Scroller Item Editor Pop-Up -->
	<div class="modal fade" id="scroller_item_pop_up_editor">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					Add / Edit Sequence Item
				</div>
				<div class="modal-error-area"></div>
				<div class="modal-body clearfix">
					<p>Sequence Item Editor Screen Goes Here</p>
				</div>
				<div class="modal-footer">
					<a href="#" role="button" class="btn" data-dismiss="modal" id="btn_cancel_add_to_sequence">Cancel</a>
					<a href="#" role="button" class="btn btn-primary" id="btn_add_to_sequence">Add Item to Sequence</a>
				</div>
			</div>
		</div>
	</div>
	<!-- /Scroller Item Editor Pop-Up -->

</div>
<script>
$(document).ready(function(){
	$("#frm_page_edit").on("submit", function(){
		if(this.banner_type.value == "3"){ // custom sequence
			if(this["sequence_data[sequence_title]"].value == ""){
				alert("Please enter sequence name");
				this["sequence_data[sequence_title]"].focus();
				return false;
			}
			if(this["sequence_data[animation_type]"].selectedIndex < 1){
				alert("Please select scrolling type");
				this["sequence_data[animation_type]"].focus();
				return false;
			}
		}
		return true;
	});
});
</script>
