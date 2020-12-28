<div id="multiple_upload_wrapper<?=@$name ? '_' . $name : ''?>" class="multiple_upload_wrapper <?= (empty($browse_directory)) ? 'has_no_browse_button' : ' has_browse_button' ?>"
     data-name="<?=@$name ? '_' . $name : ''?>">
    <script type="text/javascript" src="<?php echo URL::get_engine_plugin_assets_base('media'); ?>/js/h5utils.min.js"></script>

    <div id="drag_and_drop_area<?=@$name ? '_' . $name : ''?>" class="drag_and_drop_area" data-default-preset="<?=@$preset?>" data-onsuccess="<?=@$onsuccess?>" data-presetmodal="<?=@$presetmodal?>" data-check-duplicate="<?=isset($duplicate) ? $duplicate : 1?>">

        <div id="upload_text<?=@$name ? '_' . $name : ''?>" class="upload_text">
            <p id="dnd_notice<?=@$name ? '_' . $name : ''?>"><span class="dnd_notice">[Drag and drop files here]</span><br /><span>or</span></p>

            <?php if (empty($browse_directory)): ?>
                <p id="file_upload_button<?=@$name ? '_' . $name : ''?>" class="file_upload_button">
                    <button type="button" class="btn-link upload-button">
                        <span class="btn btn-primary">
                            <span class="icon-upload" style="font-size: 25px;margin-bottom: 4px;"></span>
                            <span><?= __('Upload') ?></span>
                        </span>
                    </button>
                    <input type="file" class="sr-only"<?=@$single ? '' : ' multiple="multiple"'?> name="<?=@$name ? $name : 'images[]'?>"
                        <?= ! empty($_GET['formats']) ? ' accept="'.$_GET['formats'].'"' : '' ?>
                        <?= @$accept ? ' accept="' . $accept . '"' : '' ?> />
                </p>
            <?php else: ?>
                <button type="button" class="btn-link upload-button file_upload_button" id="file_upload_button<?=@$name ? '_' . $name : ''?>">
                    <span type="button" class="btn btn-primary">
                        <span class="icon-upload" style="font-size: 25px;margin-bottom: 4px;"></span>
                        <span><?= __('Upload') ?></span>
                    </span>

                    <input type="file" <?=@$single ? '' : 'multiple="multiple"'?> name="<?=@$name ? $name : 'images[]'?>"
                        <?= ! empty($_GET['formats']) ? ' accept="'.$_GET['formats'].'"' : '' ?>
                        <?= @$accept ? ' accept="' . $accept . '"' : '' ?> />
                </button>

                <button type="button" class="btn-link upload-button upload_by_media" id="upload-by-media<?=@$name ? '_' . $name : ''?>" data-directory="<?= $browse_directory?>">
                    <span type="button" class="btn btn-success">
                        <span class="flaticon-report"></span>
                        <span><?= __('Browse') ?></span>
                    </span>
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div id="file_previews<?=@$name ? '_' . $name : ''?>" class="file_previews">
    </div>

    <div id="select_preset_modal<?=@$name ? '_' . $name : ''?>" class="modal fade select_preset_modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h3>Select a Preset</h3>
				</div>

				<div class="modal-body">
					<div>
						<label for="preset_selector_prompt<?=@$name ? '_' . $name : ''?>" style="display:inline;">Select Preset</label>
						<select id="preset_selector_prompt<?=@$name ? '_' . $name : ''?>" class="preset_selector preset_selector_prompt">
							<option value="0"
									data-title="" data-directory="" data-height_large="" data-width_large="" data-action_large=""
									data-thumb="" data-height_thumb="" data-width_thumb="" data-action_thumb=""
								>None</option>
							<?php echo Model_Presets::get_presets_items_as('options');?>
						</select>
					</div>
				</div>

				<div class="modal-footer">
					<a href="#" id="preset_selector_done_btn<?=@$name ? '_' . $name : ''?>" class="btn save_btn preset_selector_done_btn">Done</a>
				</div>
			</div>
		</div>
    </div>

    <style type="text/css">
		.upload_error_message + .upload_name{display: none;}
        .upload_details {clear:both;border-top:1px solid #AAA;}
        .upload_item{background:#F6F6F6;border:solid #AAA;border-width: 0 1px 1px;padding:15px 15px 10px;}
        .upload_item:first-child{border-top:1px solid #AAA;}
        .upload_item:after{clear:both;content:'';display:table;}
        .upload_item.error{background:#FDD;}
        .upload_item .details_button{color:#00C;cursor:pointer;float:right;font-size:14px;}
        .upload_name{float:left;}
        .uploaded_image{float:left;margin-bottom:5px;margin-right:5px;}
        .uploaded_notice{color:#5679da;float:right;}
        .uploaded_image img{width:40px;}
        .drag_and_drop_area{border:5px dashed #CCC;height:200px;margin:5px 5px 20px;position:relative;}
        .drag_and_drop_area.hover{border:5px dashed #FCC;}
        .upload_text{color:#CCC;margin-left:-150px;position:absolute;top:20px;left:50%;text-align:center;width:300px;}

        .uploading_notice {
            background-image:-webkit-linear-gradient(135deg, #6187f2 0%, #6187f2 25%, #5679da 25%, #5679da 50%, #6187f2 50%, #6187f2 75%, #5679da 75%, #5679da 100%);
            background-image:   -moz-linear-gradient(135deg, #6187f2 0%, #6187f2 25%, #5679da 25%, #5679da 50%, #6187f2 50%, #6187f2 75%, #5679da 75%, #5679da 100%);
            background-image:     -o-linear-gradient(135deg, #6187f2 0%, #6187f2 25%, #5679da 25%, #5679da 50%, #6187f2 50%, #6187f2 75%, #5679da 75%, #5679da 100%);
            background-image:        linear-gradient(135deg, #6187f2 0%, #6187f2 25%, #5679da 25%, #5679da 50%, #6187f2 50%, #6187f2 75%, #5679da 75%, #5679da 100%);
            background-clip:content-box;background-position:0 0;background-repeat:repeat;background-size:16px 16px;
            border:1px solid #a4a4a4;float:right;height:9px;line-height:32px;position:relative;padding:1px;width:160px;
            -webkit-animation:loading 1s linear infinite;
            -moz-animation:loading 1s linear infinite;
            -o-animation:loading 1s linear infinite;
            animation:loading 1s linear infinite;
        }
        .uploading_notice:after {
            background:#FFF;content:'';height:8px;position:absolute;right:0;top:0;z-index:20;
            -webkit-animation:progress 4s ease-in-out infinite;
            -moz-animation:progress 4s ease-in-out infinite;
            -o-animation:progress 4s ease-in-out infinite;
            animation:progress 4s ease-in-out infinite;
            padding:1px 0;
        }

        @-webkit-keyframes loading {
            from {background-position:0 0;}
            to   {background-position:-16px 0;}
        }

        @-webkit-keyframes progress {
            0%   {min-width:100%;}
            30%  {min-width:80%;}
            50%  {min-width:65%;}
            69%  {min-width:20%;}
            85%  {min-width:0;}
            100% {min-width:0;}
        }

        @keyframes loading {
            from {background-position:0 0;}
            to   {background-position:-16px 0;}
        }

        @keyframes progress {
            0%   {min-width:100%;}
            30%  {min-width:80%;}
            50%  {min-width:65%;}
            69%  {min-width:20%;}
            85%  {min-width:0;}
            100% {min-width:0;}
        }
    </style>
</div>
<?php if (@$selectionDialog) { ?>
    <a href="/admin/media/dialog?photos=yes" class="btn btn-default">go to media selection</a>
    <button class="btn btn-default close-dialog">Close</button>
<?php } ?>

<?php if (!empty($include_js)): ?>
    <script src="<?= URL::get_engine_plugin_asset('media', 'js/multiple_upload.js') ?>"></script>
    <script src="<?= URL::get_engine_plugin_asset('media', 'js/image_edit.js') ?>"></script>
<?php endif; ?>