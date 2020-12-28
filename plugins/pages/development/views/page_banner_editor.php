<?php $maps_link = '<a href="https://maps.google.com" target="_blank">Go to Google Maps</a></span>'; ?>
<div class="form-group">
	<label class="col-sm-3 control-label" for="banner_type">Banner Type</label>
	<div class="col-sm-6">
		<select class="form-control" id="banner_type" name="banner_type" onchange="toggleBannerType(this.value);">
			<?= Model_PageBanner::get_banner_types_as_options(@$page_data['banner_data']['banner_type']) ?>
		</select>
	</div>
	<div class="col-sm-3" id="banner_type_map_link">
        <?= (@$page_data['banner_data']['banner_type'] == 4) ? $maps_link : '' ?>
	</div>
</div>

<? /*?>
<div id="dialog_banner" title="Banner html">
	<textarea name='textbox3' cols='' rows='18' id='textbox3' class='mceEditor' ></textarea>
	<div>
		<input type="button" value="Save" class="submita" onclick="saveBannerHtml()" />
		<input type="button" value="Close" class="submita" onclick="closeDialog()" />
		<input type="hidden" name="textarea_id" id="textarea_id"  />
	</div>
</div>
<? */?>

<div id="bannerStaticDiv" style="display: <?php echo (isset($page_data['banner_data']['banner_type']) AND $page_data['banner_data']['banner_type'] == 1)? 'block' : 'none';?>;">

    <div class="form-group">
        <label class="col-sm-3 control-label" for="banner_static_img">Static Image</label>
	    <div class="col-sm-9">
		    <select class="form-control" id="banner_static_img" name="banner_static_img" onchange="staticImageChange(this.value);">
				<option value=""> -- Select Image -- </option>
			    <?php echo Model_PageBanner::get_banner_images_as_options(@$page_data['banner_data']['static_image']['filename']);?>
		    </select>
	    </div>

		<div class="col-sm-9" id="imagePreview">
			<?php if(!empty($page_data['banner_data']['static_image'])): ?>
				<img
					src="<?php echo Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,@$page_data['banner_data']['static_image']['filename'], 'banners'.DIRECTORY_SEPARATOR.'_thumbs_cms');?>"
					alt="<?php echo @$page_data['banner_data']['static_image']['filename'];?>"/>
			<?php endif; //echo Model_PageBanner::get_banner_preview($page_data['banner_data']['static_image'], 'static_image'); ?>
		</div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="link_to_page">Link</label>
        <div class="col-sm-9">
            <select class="form-control" id="link_to_page" name="link_to_page">
                <option value="-1">None</option>
                <?php echo Model_Pages::get_pages_as_options(@$page_data['banner_data']['link_to_page']);?>
            </select>
        </div>
    </div>
</div>

<div id="bannerDynamicDiv" style="display: <?php echo (isset($page_data['banner_data']['banner_type']) AND $page_data['banner_data']['banner_type'] == 2)? 'block' : 'none';?>;">
	<div class="form-group">
		<label class="col-sm-3 control-label" for="banner_sequence">Banner Sequence</label>
		<div class="col-sm-9">
			<select class="form-control" id="banner_sequence" name="banner_sequence" onchange="sequenceChange(this.value);">
				<?php echo Model_PageBanner::get_banner_sequences_as_options(@$page_data['banner_data']['banner_sequence']);?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label" for="banner_display_order">Display order</label>
		<div class="col-sm-9">
			<?//=form_dropdown('bannerDisplayOrder', $BannerDisplayOrders, $Page->bannerDisplayOrder, 'id="bannerDisplayOrder"'.$mzpx);?>
			<select class="form-control" id="banner_display_order" name="banner_display_order">
				<?php echo Model_PageBanner::get_banner_display_order_as_options(@$page_data['banner_data']['banner_display_order'])?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label" for="banner_first_image">First image</label>
		<div class="col-sm-9">
			<?//=form_dropdown('bannerFirstImage', $BannerImages, $Page->bannerFirstImage, 'id="bannerFirstImage"'.$mzpx);?>
			<select class="form-control" id="banner_first_image" name="banner_first_image">
				<?php echo Model_PageBanner::get_banner_sequence_images_as_options(@$page_data['banner_data']['banner_sequence'], @$page_data['banner_data']['banner_first_image']);?>
			</select>
		</div>
	</div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="link_to_page">Link</label>
        <div class="col-sm-9">
            <select class="form-control" id="link_to_page" name="link_to_page">
                <option value="-1">None</option>
                <?= Model_Pages::get_pages_as_options(@$page_data['banner_data']['link_to_page']);?>
            </select>
        </div>
    </div>

	<div class="form-group">
		<label class="col-sm-3 control-label">Banner Preview</label>
		<div class="col-sm-9" id="bannerPreview">
			<?php
			if (isset($page_data['banner_data']['banner_sequence']))
			{
		        echo Model_PageBanner::get_banner_preview($page_data['banner_data']['banner_sequence'], 'sequence_list');
			}
			?>
		</div>
	</div>
</div>

<div id="bannerCustomDiv" style="display: <?php echo (isset($page_data['banner_data']['banner_type']) AND $page_data['banner_data']['banner_type'] == 3)? 'block' : 'none';?>;">
	<?=(isset($page_data['banner_data']['banner_sequence_editor_view']))? $page_data['banner_data']['banner_sequence_editor_view'] : ''?>
</div>

<div id="bannerGoogleMapDiv" style="display: <?php echo (isset($page_data['banner_data']['banner_type']) AND $page_data['banner_data']['banner_type'] == 4)? 'block' : 'none';?>;">

    <div class="form-group">
        <label class="col-sm-3 control-label" for="banner_google_map_id">Choose Map</label>
        <div class="col-sm-6">
            <select class="form-control" id="banner_google_map_id" name="google_map_id" onchange="loadMapData(this.value)">
                <?= Model_Pagebanner::get_maps_as_options(@$page_data['banner_data']['map_id']); ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="banner_google_map_name">Map Name:</label>
        <div class="col-sm-6">
            <input class="form-control" id="banner_google_map_name" type="text" name="google_map_name" value="<?= @$page_data['banner_data']['map_data']['name'] ?>" />
            <?//=anchor('http://maps.google.com', 'Go to Google Maps', array('target'=>'_blank', 'title'=>'Create you map view in Google Maps'));?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="banner_google_map_code">Google Map code:</label>
        <div class="col-sm-9">
            <textarea class="form-control" id="banner_google_map_code" name="google_map_code" rows="8" placeholder="Enter your Google Map HTML code here" onfocus="this.select();"><?= @$page_data['banner_data']['map_data']['html'] ?></textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="banner_google_map_publish">Publish:</label>
        <div class="col-sm-9">
			<div class="btn-group" data-toggle="buttons">
				<?php $publish = ( ! isset($page_data['banner_data']['map_data']['publish']) OR $page_data['banner_data']['map_data']['publish'] == '1'); ?>
				<label class="btn btn-plain<?= $publish ? ' active' : '' ?>">
					<input type="radio" name="google_map_publish" value="1"<?= $publish ? ' checked' : '' ?> />Yes
				</label>
				<label class="btn btn-plain<?= ( ! $publish) ? ' active' : '' ?>">
					<input type="radio" name="google_map_publish" value="0"<?= ( ! $publish) ? ' checked' : '' ?> />No
				</label>
			</div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
<!--		toggleBannerType('--><?//=(isset($page_data['banner_data']['banner_type'])? $page_data['banner_data']['banner_type'] : 0)?><!--');-->

        // Toggle publish
        $('.btn-group .btn').click(
            function(event){
                event.preventDefault();
                $('#'+$(this).parent().data('toggle-name')).val($(this).val());
            }
        );

	});

	function toggleBannerType(value)
    {
        var static_form = $('#bannerStaticDiv');
        var dynamic_form = $('#bannerDynamicDiv');
        var custom_form = $('#bannerCustomDiv');
        var google_map_form = $('#bannerGoogleMapDiv');

        switch(value)
        {
            case '0': //None
                static_form.hide();
                dynamic_form.hide();
                custom_form.hide();
                google_map_form.hide();
                break;
            case '1': //Static
                static_form.show();
                dynamic_form.hide();
                custom_form.hide();
                google_map_form.hide();
                break;
            case '2': //Dynamic
                static_form.hide();
                dynamic_form.show();
                sequenceChange($('#banner_sequence').val());
                custom_form.hide();
                google_map_form.hide();
                break;
            case '3': //Custom
                static_form.hide();
                dynamic_form.hide();
                load_custom_scroller_editor_view();
                custom_form.show();
                google_map_form.hide();
                break;
            case '4': //Google Map
                static_form.hide();
                dynamic_form.hide();
                custom_form.hide();
                google_map_form.show();
                break;
		}

        if (value == 4) { // Google Map
            $('#banner_type_map_link').html('<?= $maps_link ?>');
        }
        else {
            $('#maps_link').remove();
		}
    } // end of function

    function loadMapData(id)
    {
        $.ajax({
            url      : '/admin/pages/ajax_get_banner_data/'+id,
            type     : 'post',
            dataType : 'json',
            async    : false
        }).done(function(result)
        {
            $('#banner_google_map_name').val(result.name);
            $('#banner_google_map_code').html(result.html);
        });
    }


	function staticImageChange(image) {
		$.post(
				'/admin/pages/ajax_get_image_preview/',
				{
					image_to_preview : image
				},
				function(image_preview){
					$('#imagePreview').html(image_preview);
				}
		);
	}//end of function


	function sequenceChange(banner_sequence) {
		$.post(
				'/admin/pages/ajax_get_banner_sequence_data/',
				{
					banner_sequence : banner_sequence
				},
				function(sequence_data){
					//$('#banner_first_image').html('<option value="0">-- Select First Image --</option>');
					$('#banner_first_image').html(sequence_data.sequence_images_list);
					$('#bannerPreview').html(sequence_data.banner_preview);
				},
				'json'
		);
	}//end of function


	function load_custom_scroller_editor_view() {
		$.post(
				'/admin/customscroller/ajax_get_custom_sequence_editor_view/',
				{
                    sequence_id    : '',
					plugin_item_id : $('#val_pages_id').val(),
					plugin_name	   : 'banners'
				},
				function(result) {
					if(result.err_msg == '') {
						$('#bannerCustomDiv').html(result.cs_editor_view);
					} else {
						$('#bannerCustomDiv').html(result.err_msg);
					}
//					$('#bannerPreview').html(sequence_data.banner_preview);
				},
				'json'
		);
	}

	function deleteSet() {
		alert('Load the Confirm modal dialog and delete Sequence ID: #'+$('#existing_sequences').val()+' now');

//		if ($('#existingSets').val() != '') $.ajax({
//			url: '<?//=base_url()?>//index.php/formcontroller_pages/ib_deleteSet/'+$('#existingSets').val(),
//			async: false,
//			success: function(data) {
//				$('#setsDropdown').html(data);
//				reloadList($('#existingSets').val());
//			}
//		});

	}
</script>