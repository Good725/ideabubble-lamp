<? /* Drop Down Does not work properly ?>
<link rel="stylesheet" href="<?= URL::get_engine_plugin_assets_base('media') ?>css/uploadform.css" />
<link rel="stylesheet" href="<?= URL::get_engine_plugin_assets_base('media') ?>css/jquery.Jcrop.css" type="text/css" />

<script src="<?= URL::get_engine_plugin_assets_base('media') ?>js/jquery.Jcrop.js"></script>
<script src="<?= URL::get_engine_plugin_assets_base('media') ?>js/jquery.filedrop.js"></script>

<!-- The main filedrop script file -->
<script src="<?= URL::get_engine_plugin_assets_base('media') ?>js/uploadform.js"></script>

<script language="Javascript">
    var jcrop_api;
    
    $(function(){
        $('#cropbox').Jcrop({ 
            aspectRatio: 1,
            onSelect: updateCoords
        },function(){
            jcrop_api = this;
        }
    );
                
        if ($.browser.msie) {
            $('.message').text('Choose file with "Browse" button and then send it to preprocessing with "Upload" button.');
            //$('#ie_upload_form').show();
        } else {
            //$('#ie_upload_form').hide();
        }
    });

    function updateCoords(c)
    {
        $('#x').val(c.x);
        $('#y').val(c.y);
        $('#w').val(c.w);
        $('#h').val(c.h);
        $('#jcw').val( $('.jcrop-holder').width() );
        $('#jch').val( $('.jcrop-holder').height() );
    };

    function checkCoords()
    {
        if (parseInt($('#w').val())) return checkFilename();
        alert('Please select a crop region then press submit.');
        return false;
    };
            
    function checkFilename()
    {
        if ($('#filename').val() ) return true;
        alert('Please select file.');
        return false;
    };

</script>        


<div id="dropbox">
    <div class="preview">
        <form id="ie_upload_form" action="/admin/media/upload_media_item" method="post" enctype="multipart/form-data">
            <input type="file"  name="file_to_upload" /> 
            <input type="submit" value="Upload" /> 
        </form>                

        <span class="imageHolder">
            <img id="cropbox"  src="<?= $picname? URL::Media('media') . $picname : '' ?>" />
            <span class="uploaded"></span>
        </span>
        <div class="progressHolder">
            <div class="progress"></div>
        </div>

        <form class="save_form" action="/admin/media/save" method="post">
            <input type="hidden" id="x" name="x" />
            <input type="hidden" id="y" name="y" />
            <input type="hidden" id="w" name="w" />
            <input type="hidden" id="h" name="h" />
            <input type="hidden" id="jcw" name="jcw" />
            <input type="hidden" id="jch" name="jch" />
            <input type="hidden" id="filename" name="filename" value="<?= $picname ? $picname : '' ?>" />
            <input type="submit" name="save_cropped" value="Save cropped image" onclick="return checkCoords();"/>
            <input type="submit" name="save_full" value="Save full image" onclick="return checkFilename();" />
        </form>   

    </div>

    <span class="message">Drop images here to upload.</span>
</div>

<? */?>

<!--
working solution without drag'n'drop:
-->
<h2>Select file to upload:</h2>
<form action="/admin/media/upload_media_item" method="post" enctype="multipart/form-data">
	<input type="file"  name="file_to_upload" />
	<br>
	<select name="preset_id" id="preset_id" onchange="set_preset_details('preset_id');">
		<option value="0"
				data-title="" data-directory=""
				data-height_large="" data-width_large="" data-action_large=""
				data-thumb="" data-height_thumb="" data-width_thumb="" data-action_thumb="">-- Select Preset --</option>
		<?php echo Model_Presets::get_presets_items_as('options');?>
	</select>
	<input type="hidden" name="preset_title" id="preset_title" value="" />
	<input type="hidden" name="preset_directory" id="preset_directory" value="" />
	<input type="hidden" name="preset_height_large" id="preset_height_large" value="" />
	<input type="hidden" name="preset_width_large" id="preset_width_large" value="" />
	<input type="hidden" name="preset_action_large" id="preset_action_large" value="" />
	<input type="hidden" name="preset_thumb" id="preset_thumb" value="" />
	<input type="hidden" name="preset_height_thumb" id="preset_height_thumb" value="" />
	<input type="hidden" name="preset_width_thumb" id="preset_width_thumb" value="" />
	<input type="hidden" name="preset_action_thumb" id="preset_action_thumb" value="" />
	<br />
	<input type="submit" value="Upload" />
</form>

	<script type="text/javascript">
		function set_preset_details(preset_selector_id){
			var element = $('#'+preset_selector_id+' :selected');

			//Prepare the selected Preset Details to be sent with this Upload
			$('#preset_title').val(element.data('title'));
			$('#preset_directory').val(element.data('directory'));
			$('#preset_height_large').val(element.data('height_large'));
			$('#preset_width_large').val(element.data('width_large'));
			$('#preset_action_large').val(element.data('action_large'));
			$('#preset_thumb').val(element.data('thumb'));
			$('#preset_height_thumb').val(element.data('height_thumb'));
			$('#preset_width_thumb').val(element.data('width_thumb'));
			$('#preset_action_thumb').val(element.data('action_thumb'));
		}//end of function
	</script>
