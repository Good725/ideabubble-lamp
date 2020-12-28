<?php
// Should be moved to the controller
$filepath = URL::get_engine_plugin_assets_base('products').'/images/';
$product_filepath = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR, '/'));
$option_groups = Model_Option::get_all_groups_by_product($id);
$palette_colors = array();
$first_options = Model_Product::get_option1();
$second_options = Model_Product::get_option2();
$pages_model = new Model_Pages;
$material_page = $pages_model->get_page('material-description-usage.html');
$help_page = $pages_model->get_page('need-help.html');
$fonts = Model_Media::get_fonts(FALSE);
$fonts = array_unique($fonts);
$cms_editor = isset($cms_editor) ? $cms_editor : FALSE;
$presets = Model_Media::get_presets_like('Signs - %');
$sign_builder_layers = isset($sign_builder_layers) ? trim(htmlspecialchars($sign_builder_layers)) : '';
$blank_sign = (!$cms_editor AND ($sign_builder_layers == '' OR $sign_builder_layers == '[]') AND !isset($images[0]) AND Settings::instance()->get('show_sign_select_options'));
$sb_categories = Model_Category::get_sign_builder_categories();

if (Settings::instance()->get('sign_builder_area_restriction') == 1)
{
    // Coefficient necessary to convert the units used to millimetres
	switch (Settings::instance()->get('sign_builder_area_units'))
	{
		case 'mm':
			$coeff = 1;
			break;
		case 'cm':
			$coeff = 10;
			break;
		case 'm' :
			$coeff = 100;
			break;
		case 'in':
			$coeff = 25.4;
			break;
		case 'ft':
			$coeff = 304.8;
			break;
		default  :
			$coeff = 1;
			break;
	}
	$min_area = Settings::instance()->get('sign_builder_min_area') * $coeff * $coeff;
	$max_area = Settings::instance()->get('sign_builder_max_area') * $coeff * $coeff;
}
else
{
	$min_area = $max_area = '';
}

for ($i = 0; $i < sizeof($option_groups) && empty($palette_colors); $i++)
{
	if (strpos($option_groups[$i]['option_group'], '_palette'))
	{
		$palette_colors = Model_Option::get_options_by_field('group', $option_groups[$i]['option_group'], TRUE);
	}
}
?>
	<link rel="stylesheet" type="text/css"
		  href="<?= URL::get_engine_plugin_assets_base('products') ?>css/front_end/builder.css"/>
	<link rel="stylesheet" type="text/css"
		  href="<?= URL::get_engine_plugin_assets_base('products') ?>css/front_end/spectrum.min.css"/>
	<script type="text/javascript"
			src="<?= URL::get_engine_plugin_assets_base('products') ?>js/front_end/spectrum.min.js"></script>

	<!-- products-breadcrumbs -->
<?php if (!$cms_editor): ?>
	<div id="breadcrumb-nav">
		<?= IbHelpers::breadcrumb_navigation(); ?>
	</div>
<?php endif; ?>
	<input type="hidden" value="<?= $id; ?>" id="id"/>
<?php if (!$cms_editor): ?>
	<input type="hidden" id="sign_builder_layers_input" name="sign_builder_layers" value="<?= $sign_builder_layers ?>"/>
<?php endif; ?>
	<div id="sign_builder_wrapper" class="sign_builder_wrapper">
	<div class="sign_builder_description">
		<?= (!isset($brief_description) OR $brief_description == '')
			? Settings::instance()->get('sign_builder_description')
			: str_replace('\n', '</p><p>', '<p>'.$brief_description.'</p>');
		?>
	</div>

	<?= ($cms_editor) ? '<div id="sign_builder_form" >' : '<form id="sign_builder_form" action="#">' ?>
	<input type="hidden" id="sign_builder_bleedline_margin" value="<?= Settings::instance()->get('pdf_bleedline') ?>"/>

	<!-- Canvas -->
	<section class="canvas_section">
		<fieldset>
			<legend><span class="preview_text">Preview</span> <span id="preview_units"
																	class="preview_units">millimetres</span></legend>
			<div class="delete_toggle_wrapper">
				<label>
					<input type="checkbox" class="show_delete_icons" id="show_delete_icons"
						   checked="checked"><span><?php // span for styling the checkbox ?></span> Show guidelines and
					delete icons
				</label>
			</div>
			<div id="canvas_wrapper" class="canvas_wrapper">

				<!-- Existing or from scratch modal -->
				<div id="existing_or_scratch_modal" class="existing_or_scratch_modal sb-modal-overlay"
					 style="display:<?= ($blank_sign) ? 'block' : 'none' ?>;">
					<div class="sb-modal">
						<div class="sb-modal-body">
							<p>To Begin:</p>
							<button type="button" class="sb-button-primary sb-modal-dismiss" id="start_blank_button">
								Start with a Blank Sign
							</button>
							<p style="margin:0 0 .5em;">OR</p>
							<button type="button" class="sb-button-primary" id="select_existing_sign_button">Select
								&amp; Edit Existing Sign
							</button>
						</div>
					</div>
				</div>

				<div class="origin">0</div>
				<ul class="ruler ruler-horizontal">
					<li>100</li>
					<li>200</li>
					<li>300</li>
					<li>400</li>
					<li>500</li>
					<li>600</li>
					<li>700</li>
					<li>800</li>
					<li>900</li>
					<li>1000</li>
				</ul>
				<ul class="ruler ruler-vertical">
					<li>100</li>
					<li>200</li>
					<li>300</li>
					<li>400</li>
					<li>500</li>
					<li>600</li>
					<li>700</li>
					<li>800</li>
					<li>900</li>
					<li>1000</li>
				</ul>

				<div id="canvas_guidelines" class="canvas_guidelines">
					<div id="bleed_line" class="bleed_line">
					</div>
					<div id="safe_line" class="safe_line">
					</div>
				</div>
				<canvas id="builder_canvas" class="builder_canvas" width="400" height="400">
					This feature is not supported in your browser.
				</canvas>
				<canvas id="print_canvas" class="print_canvas"></canvas>
			</div>

			<div class="builder_canvas_controls">
				<div class="controls_cover"<?= $blank_sign ? ' style="display:block;"' : '' ?>></div>
				<button type="reset" class="sb-button-primary clear_canvas" id="clear_canvas">Start Over</button>
				<button type="button" class="sb-button-primary preview_sign" id="preview_sign">Preview Your Sign
				</button>
			</div>

			<?php
			if (Kohana::$environment != Kohana::PRODUCTION):
				?>
				<div class="builder_canvas_controls">
					<label>
						<button class="sb-button" type="button" id="preview_pdf">Preview PDF</button>
						<a href="/frontend/products/get_latest_pdf" id="get_pdf"></a>
					</label>
				</div>
			<?php
			endif;
			?>
		</fieldset>
	</section>

	<div class="control_sections">
	<div class="controls_cover"<?= $blank_sign ? ' style="display:block;"' : '' ?>></div>
	<fieldset>

        <div class="controls_sections-steps">

            <?php if (!$cms_editor): ?>
                <div class="product_price">
                    <dl>
                        <dt>Base Price</dt>
                        <dd>
                            <span id="final_price" data-product_price="<?= $price ?>">&euro;<span
                                    id="static_price"><?= $price ?></span></span>
                        </dd>
                    </dl>
                </div>
            <?php endif; ?>
            <?php $step = 1; ?>

            <!-- Size -->
            <button type="button" class="sb-button-plain" data-step="<?= $step ?>" data-pane="size_editor"><strong
                    class="sb_step_label">Step <?= $step ?></strong> <span class="sb_step">Choose your size</span></button>
            <?php $step++; ?>
            <section id="size_editor" class="toggleable-block" style="display:block;">
                <dl>
                    <dt><label for="builder_size">Preset Size</label></dt>
                    <dd>
                        <select id="builder_size" class="builder_size validate[required]" data-select_id="option1">
                            <option value="">Please select</option>
                            <?php foreach ($first_options AS $key => $option1): ?>
                                <?php $dimensions = Model_Product::get_dimensions($option1['label']); ?>
                                <option
                                    value="<?= ($option1['label'] != 'Custom') ? $option1['option1'] : 'custom'; ?>"
                                    data-width="<?= $dimensions['width'] ?>" data-height="<?= $dimensions['height'] ?>"
                                    ><?=$option1['label'];?></option>
                            <?php endforeach; ?>
                        </select>
                    </dd>
                    <dt><label for="builder_orientation">Orientation</label></dt>
                    <dd>
                        <div id="builder_orientation" class="builder_orientation radio-toggle">
                            <label class="selected">
                                <input id="builder_orientation_portrait" type="radio" name="orientation" value="portrait"
                                       checked="checked"/> Portrait
                            </label>
                            <label>
                                <input id="builder_orientation_landscape" type="radio" name="orientation" value="landscape"/>
                                Landscape
                            </label>
                        </div>
                    </dd>

                    <dt><label for="builder_units">Unit of Measure</label></dt>
                    <dd>
                        <select id="builder_units" name="units">
                            <option value="mm" data-name="millimetres" data-coeff="1" data-gap="100" selected="selected">mm
                                (millimetres)
                            </option>
                            <option value="cm" data-name="centimetres" data-coeff="10" data-gap="10">cm (centimetres)</option>
                            <option value="in" data-name="inches" data-coeff="25.4" data-gap="5">in (inches)</option>
                            <option value="ft" data-name="feet" data-coeff="304.8" data-gap=".5">ft (feet)</option>
                        </select>
                        <input type="hidden" id="builder_previous_units" value="mm"/>
                    </dd>

                    <dt id="builder_dimensions_labels"><label for="builder_width">Width</label> &times; <label
                            for="builder_height">Height</label></dt>
                    <dd id="builder_dimensions"
                        data-always_show="<?= Settings::instance()->get('show_dimensions_with_preset_selected') ?>">
                        <input id="builder_width" type="number" min="0" inputmode="numeric" pattern="[0-9]*" value="420"
                               class="dimension_input validate[required]"/> &times;
                        <input id="builder_height" type="number" min="0" inputmode="numeric" pattern="[0-9]*" value="594"
                               class="dimension_input validate[required]"/>
                        <label for="lock_ratio"><input id="lock_ratio" type="checkbox"/> Lock ratio</label>
                        <input type="hidden" id="builder_ratio" value="1"/>

                        <input type="hidden" id="sb_min_width"
                               value="<?= Settings::instance()->get('sign_builder_min_width'); ?>"/>
                        <input type="hidden" id="sb_max_width"
                               value="<?= Settings::instance()->get('sign_builder_max_width'); ?>"/>
                        <input type="hidden" id="sb_min_height"
                               value="<?= Settings::instance()->get('sign_builder_min_height'); ?>"/>
                        <input type="hidden" id="sb_max_height"
                               value="<?= Settings::instance()->get('sign_builder_max_height'); ?>"/>
                        <input type="hidden" id="sb_min_area" value="<?= $min_area ?>"/>
                        <input type="hidden" id="sb_max_area" value="<?= $max_area ?>"/>
                    </dd>
                </dl>
            </section>

            <!-- Material -->
            <button type="button" class="sb-button-plain" data-step="<?= $step ?>" data-pane="material_editor"><strong
                    class="sb_step_label">Step <?= $step ?></strong> <span class="sb_step">Choose your material</span></button>
            <?php $step++ ?>
            <section id="material_editor" class="toggleable-block">
                <dl>
                    <dt><label for="builder_material">Choose Material Type</label></dt>
                    <dd>
                        <select id="builder_material" class="validate[required]" data-select_id="option2">
                            <option value="">Please Select</option>
                            <?php foreach ($second_options AS $key => $option2): ?>
                                <option value="<?= $option2['option2']; ?>"
                                        data-description="<?= htmlentities($option2['description']) ?>">
                                    <?=$option2['label'];?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <abbr id="material_tooltip" class="tooltip_icon" title="" style="display:none;">i</abbr>
                        <?php if ($material_page AND $material_page[0]['publish'] == 1): ?>
                            <a id="material_description_link" class="material_description_link"
                               href="/material-description-usage.html" target="_blank" title="Material Description &amp; Usage">Material
                                Description &amp; Usage</a>
                        <?php endif; ?>
                    </dd>

                    <dt>
                        <label for="builder_background_color">Background Colour</label>
                        <label for="builder_background_color">Default is white</label>
                    </dt>
                    <dd>
                        <div class="color_picker color_picker-background" id="builder_background_color_wrapper" title="Colour">
                            <?php $default_background_color = Settings::instance()->get('sign_builder_background_color') ?>
                            <input type="hidden" id="builder_background_color" class="color_value"
                                   value="<?= $default_background_color ?>" data-default="<?= $default_background_color ?>"/>
                            <span class="picker_label"
                                  data-color="<?= isset($default_background_color) ? $default_background_color : '#FFFFFF' ?>"></span>
                        </div>
                    </dd>
                </dl>
            </section>

            <!-- Images -->
            <button type="button" class="sb-button-plain" data-step="<?= $step ?>" data-pane="image_editor"><strong
                    class="sb_step_label">Step <?= $step ?></strong> <span class="sb_step">Add images</span></button>
            <?php $step++ ?>
            <section id="image_editor" class="toggleable-block editor_block image_editor">

                <nav id="image_layer_tabs" class="sb-tabs">
                    <ul>
                    </ul>
                </nav>
                <div class="sb-tabs-panel">
                    <h3>Image</h3>

                    <div id="builder_image_preview" class="builder_image_preview" style="float:right;"></div>
                    <div class="builder_image_buttons">
                        <button class="sb-button-plain" type="button" id="browse_images_btn">Browse Gallery</button>
                        <p>OR</p>
                        <button class="sb-button-plain" type="button" id="upload_image_btn">Upload your image</button>
                    </div>

                    <dl id="builder_scale_wrapper">
                        <dt><label for="builder_scale">Scale</label></dt>
                        <dd>
                            <input id="builder_scale" class="slider update_sign" type="range" min="0" max="100" step="1"
                                   value="75"/>
                        </dd>
                    </dl>
                </div>

                <?php // <a href="#" class="delete_layer">Delete this layer</a> ?>

            </section>

            <!-- Text Editor -->
            <button type="button" class="sb-button-plain" data-step="<?= $step ?>" data-pane="text_editor"><strong
                    class="sb_step_label">Step <?= $step ?></strong> <span class="sb_step">Add text (if required)</span>
            </button>
            <?php $step++ ?>
            <section id="text_editor" class="toggleable-block text_editor editor_block">
                <nav id="text_layer_tabs" class="sb-tabs">
                    <ul>
                    </ul>
                </nav>
                <div class="sb-tabs-panel">
                    <h3>Text Editor</h3>

                    <div class="text_editor_controls">

                        <label for="builder_font" class="accessible-hide">Font</label>

                        <select id="builder_font" class="update_sign" style="width:64px;">
                            <option value="arial" data-category="sans-serif">Arial</option>
                            <option value="'Times New Roman'" data-category="serif">Times New Roman</option>
                            <?php foreach ($fonts as $font): ?>
                                <option value="'<?= $font ?>'"><?= $font ?></option>
                            <?php endforeach; ?>
                        </select>

                        <div class="color_picker color_picker-text" id="builder_font_color_wrapper" title="Text color">
                            <input type="hidden" id="builder_font_color" class="color_value"/>
                            <span class="picker_label" data-color="#000000">A</span>
                        </div>

                        <div id="builder_text_align" class="radio-toggle">
                            <label title="align left">
                                <input type="radio" class="update_sign" name="text_align" value="left"/>
                                <img src="<?= $filepath ?>icons/align-left.png" alt="align left" width="10"/>
                            </label>
                            <label title="align centre" class="selected">
                                <input type="radio" class="update_sign" name="text_align" value="center" checked="checked"/>
                                <img src="<?= $filepath ?>icons/align-center.png" alt="align centre" width="10"/>
                            </label>
                            <label title="align right">
                                <input type="radio" class="update_sign" name="text_align" value="right"/>
                                <img src="<?= $filepath ?>icons/align-right.png" alt="align right" width="10"/>
                            </label>
                        </div>
                    </div>

                    <label for="builder_text" class="accessible-hide">Text</label>
                    <textarea id="builder_text" class="update_sign"></textarea>

                    <dl>
                        <dt><label for="builder_font_size">Font Size</label></dt>
                        <dd>
                            <input id="builder_font_size" class="slider update_sign" type="range" min="0" max="100" step="1"
                                   value="35" data-unit="pt"/>
                        </dd>
                    </dl>

                    <h3>Text Box</h3>

                    <div class="text_container_editor_controls">
                        <dl>
                            <dt>Background Colour</dt>
                            <dd>
                                <div class="color_picker color_picker-background" id="builder_text_background_color_wrapper"
                                     title="Background Colour">
                                    <input type="hidden" id="builder_text_background_color" class="color_value"/>
                                    <span class="picker_label" data-color="#FFFFFF"></span>
                                </div>
                            </dd>

                            <dt style="width:70px;"><label for="builder_text_width">Width</label></dt>
                            <dd style="margin-left:70px;">
                                <input type="range" id="builder_text_width" class="slider" min="0" max="100" step="1"/>
                                <label>&nbsp;Autofit <input id="builder_text_width_auto" type="checkbox"
                                                            checked="checked"/></label>
                            </dd>


                            <dt style="width:70px;"><label for="builder_text_height">Height</label></dt>
                            <dd style="margin-left:70px;">
                                <input type="range" id="builder_text_height" class="slider" min="0" max="100" step="1"/>
                                <label>&nbsp;Autofit <input id="builder_text_height_auto" type="checkbox"
                                                            checked="checked"/></label>
                            </dd>
                        </dl>

                    </div>

                    <h3>Text Box Border</h3>

                    <div class="text_border_editor_controls">
                        <dl>
                            <dt><label for="builder_border_width">Thickness</label></dt>
                            <dd><input id="builder_border_width" type="number" min="0" inputmode="numeric" pattern="[0-9]*"
                                       value="0" style="width:35px;"/></dd>

                            <dt>Color</dt>
                            <dd>
                                <div class="color_picker border_color_picker" id="builder_text_border_color_wrapper"
                                     title="Border Colour">
                                    <input type="hidden" id="builder_text_border_color" class="color_value"/>
                                    <span class="picker_label" data-color="#000000"></span>
                                </div>
                            </dd>

                            <dt>Shape</dt>
                            <dd>
                                <div id="builder_rounded" class="radio-toggle rounded-toggle">
                                    <label title="straight corners">
                                        <input type="radio" name="rounded" value="0"/>
                                        <img src="<?= $filepath ?>icons/straight_corners.png" alt="align left"/>
                                    </label>
                                    <label title="round corners" class="selected">
                                        <input type="radio" name="rounded" value="1" checked="checked"/>
                                        <img src="<?= $filepath ?>icons/rounded_corners.png" alt="align left"/>
                                    </label>
                                </div>
                            </dd>
                        </dl>
                    </div>

                    <?php // <a href="#" class="delete_layer">Delete this layer</a> ?>
                </div>
            </section>

            <?php if (Settings::instance()->get('sign_builder_finish_on') == 1): ?>
                <!-- Finish -->
                <button type="button" class="sb-button-plain" data-step="<?= $step ?>" data-pane="finish_editor"><strong
                        class="sb_step_label">Step <?= $step ?></strong> <span class="sb_step">Add your finish</span></button>
                <?php $step++ ?>
                <section id="finish_editor" class="toggleable-block finish_editor">
                    <dl>
                        <dt>
                            Laminate
                        </dt>
                        <dd>
                            <div id="builder_laminate" class="radio-toggle">
                                <label>
                                    <input type="radio" name="laminate" value="1"/> Yes
                                </label>
                                <label class="selected">
                                    <input type="radio" name="laminate" value="0" checked="checked"/> No
                                </label>
                            </div>

                            <div id="builder_lamination_type_wrapper" class="builder_lamination_type_wrapper">
                                <label for="builder_lamination_type" class="accessible-hide">Lamination type</label>
                                <select id="builder_lamination_type" name="lamination_type">
                                    <option value="">Select Type</option>
                                    <?php
                                    foreach (Model_Option::get_options_by_field('group', 'Laminate') as $key => $option):
                                        ?>
                                        <option value="<?= $option['id']; ?>"><?=$option['label'];?></option>
                                    <?php
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                        </dd>

                        <dt>
                            Adhesive
                        </dt>
                        <dd>
                            <div id="builder_adhesive" class="radio-toggle">
                                <?php
                                foreach (Model_Option::get_options_by_field('group', 'Adhesive') as $key => $option):
                                    ?>
                                    <label class="<?= ($option['value'] == 0) ? 'selected' : ''; ?>">
                                        <input type="radio" name="adhesive" value="<?= $option['value']; ?>"
                                               data-option_id="<?= $option['id']; ?>"/> <?=$option['label'];?>
                                    </label>
                                <?php
                                endforeach;
                                ?>
                            </div>
                        </dd>
                    </dl>
                </section>
            <?php endif; ?>

            <?php if (Settings::instance()->get('sign_builder_select_quantity_step') == 1): ?>
                <!-- Quantity -->
                <button type="button" class="sb-button-plain" data-step="<?= $step ?>" data-pane="quantity_editor"><strong
                        class="sb_step_label">Step <?= $step ?></strong> <span class="sb_step">Select Quantity</span></button>
                <?php $step++ ?>
                <section id="quantity_editor" class="toggleable-block quantity_editor">
                    <dl>
                        <dt class="builder_quantity_label_wrapper"><label for="builder_quantity">Quantity</label></dt>
                        <dd><input id="builder_quantity" class="builder_quantity" type="number" min="0" inputmode="numeric"
                                   pattern="[0-9]*" value="1"/></dd>
                    </dl>
                </section>
            <?php else: ?>
                <input type="hidden" class="builder_quantity" id="builder_quantity" value="1" />
            <?php endif; ?>

        </div>

	<div id="layer_list" class="layer_list">
		<h3>Layers</h3>
		<ol>
		</ol>
	</div>
	<div class="offer_price" style="display:none;">
		<dl>
			<dt><b>UNIT PRICE</b></dt>
			<dd data-product_price="<?= isset($price) ? $price : ''; ?>">&euro;<span
                    id="unit_price"><?= isset($price) ? $price : ''; ?></span></dd>
		</dl>
	</div>
	<div class="offer_price offer_detail" style="display:none;">
		<dl>
			<dt><b>TOTAL</b>
				<span class="offer_text">(-<span class="offer_percent_discount"></span>% discount)</span></dt>
			<dd data-product_price="<?= isset($price) ? $price : ''; ?>">&euro;<span
					class="full_price"><?=isset($price) ? $price : '';?></span></dd>
		</dl>
	</div>

	<section id="builder_purchase_section">
		<div id="product_purchase" class="product_purchase">
			<div id="modal" class="sb-blackout">
				<div style=""> Please Wait while we generate your print-ready file. Larger files may take slightly
					longer ... <img src="<?= URL::get_engine_plugin_assets_base('products'); ?>images/ajax-loader.gif">
				</div>
			</div>
			<?php if (!$cms_editor): ?>
				<button type="button" class="sb-button-secondary purchase_button" id="add_to_cart_warning">Add to Cart
				</button>
				<button type="button" class="sb-button-primary purchase_button" id="purchase_button_warning">Buy Now
				</button>
			<?php endif; ?>
		</div>
	</section>
	<?php if ($help_page AND $help_page[0]['publish'] == 1 AND !$cms_editor): ?>
		<a class="sb-button-plain need_help_link" href="/need-help.html" target="_blank" title="Need Help?">Need
			Help?</a>
	<?php endif; ?>

	<div class="sign_builder_description">
		<?= (!isset($description) OR $description == '') ? Settings::instance()->get('sign_builder_secondary_description') : $description ?>
	</div>

	</fieldset>

	</div>

	<!-- Modal boxes -->
	<div id="browse_images_modal" class="sb-modal-overlay browse_images_modal">
		<div class="sb-modal">
			<div class="sb-modal-head">
				<div class="sb-modal-dismiss">&times;</div>
				<h3>Select an image</h3>
			</div>

			<div class="sb-modal-body">
				<fieldset class="image_search_zone">
					<legend>Browse existing images</legend>

					<label for="builder_category_list" class="accessible-hide">Select Category</label>
					<select id="builder_category_list">
						<option value="">Select Category</option>
						<?php foreach ($presets as $builder_category): ?>
							<?php $selected = (isset($builder_category_id) AND $builder_category_id == $builder_category['id']) ? ' selected="selected"' : ''; ?>
							<option
								value="<?= $builder_category['id'] ?>"<?= $selected ?>><?= trim(str_replace('Signs - ', '', $builder_category['title'])) ?></option>
						<?php endforeach; ?>
					</select>

					<label for="builder_image_search" style="float:right;">Search images
						<input id="builder_image_search" type="text"/>
					</label>

					<div id="available_images" class="available_images"
						 data-default="<?= (isset($images[0])) ? $images[0] : '' ?>"></div>
				</fieldset>
			</div>

			<div class="sb-modal-foot">
				<button class="sb-button-plain sb-modal-dismiss" type="button">Cancel</button>
			</div>
		</div>
	</div>

	<div id="upload_image_modal" class="sb-modal-overlay upload_image_modal">
		<div class="sb-modal">
			<div class="sb-modal-head">
				<div class="sb-modal-dismiss">&times;</div>
				<h3>Upload an Image</h3>
			</div>

			<div class="sb-modal-body">
				<fieldset class="upload_zone">
					<legend>Upload an image</legend>
					<p>Please ensure your image is print quality, typically high resolution 300 <abbr
							title="dots per inch">dpi</abbr> or equivalent. Accepted image file types include JPG, JPEG,
						PNG, GIF and SVG.</p>

					<p>Failure to provide correct quality imagery may result in poor quality sign finish. Please note
						that enlarging your image may reduce print quality.</p>

					<div>
						<div id="builder_drop_upload" class="drag_and_drop_area">
							<p>Drop image here</p>

							<p style="margin:5px;">or</p>
							<input type="file" id="builder_upload" class="sb-button-plain upload_button"/>
							<input type="hidden" id="builder_drop_upload_file"/>
						</div>
					</div>
					<div id="upload_error_message" class="upload_error_message"></div>
				</fieldset>
			</div>

			<div class="sb-modal-foot">
				<button class="sb-button-plain sb-modal-dismiss" type="button">Cancel</button>
			</div>
		</div>
	</div>

	<div id="delete_layer_modal" class="sb-modal-overlay delete_layer_modal">
		<div class="sb-modal">
			<div class="sb-modal-head">
				<div class="sb-modal-dismiss">&times;</div>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="sb-modal-body">
				<p>Are you sure you want to delete this layer?</p>
				<hr/>
				<div id="delete_layer_preview" class="delete_layer_preview">

				</div>
			</div>
			<div class="sb-modal-foot">
				<button id="delete_layer_button" class="sb-button-plain sb-modal-dismiss sb-modal-delete" type="button">
					Delete
				</button>
				<button class="sb-button-plain sb-modal-dismiss" type="button">Cancel</button>
			</div>
		</div>
	</div>

	<div id="clear_canvas_modal" class="sb-modal-overlay">
		<div class="sb-modal">
			<div class="sb-modal-head">
				<div class="sb-modal-dismiss">&times;</div>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="sb-modal-body">
				<p>Are you sure you want to clear the entire canvas?</p>
			</div>
			<div class="sb-modal-foot">
				<button id="confirm_clear_canvas_button" class="sb-button-plain sb-modal-dismiss sb-modal-delete"
						type="button">Clear All
				</button>
				<button class="sb-button-plain sb-modal-dismiss" type="button">Cancel</button>
			</div>
		</div>
	</div>

	<div id="complete_step_1_modal" class="sb-modal-overlay">
		<div class="sb-modal">
			<div class="sb-modal-head">
				<div class="sb-modal-dismiss">&times;</div>
				<h3>Complete Step <span class="step_number">1</span></h3>
			</div>
			<div class="sb-modal-body">
				<p>You must complete Step <span class="step_number">1</span> to continue building your Sign.</p>

				<p>Please ensure you have chosen a Preset Size.</p>
			</div>
			<div class="sb-modal-foot">
				<button class="sb-button-plain sb-modal-dismiss" type="button">OK</button>
			</div>
		</div>
	</div>

	<div id="complete_step_2_modal" class="sb-modal-overlay">
		<div class="sb-modal">
			<div class="sb-modal-head">
				<div class="sb-modal-dismiss">&times;</div>
				<h3>Complete Step <span class="step_number">2</span></h3>
			</div>
			<div class="sb-modal-body">
				<p>You must complete step <span class="step_number">2</span> to continue.</p>

				<p>You must specify a material type and colour.</p>
			</div>
			<div class="sb-modal-foot">
				<button class="sb-button-plain sb-modal-dismiss" type="button">OK</button>
			</div>
		</div>
	</div>

	<div id="confirm_continue_buy_now" class="sb-modal-overlay">
		<div class="sb-modal">
			<div class="sb-modal-head">
				<div class="sb-modal-dismiss">&times;</div>
				<h3>Confirm Page Navigation</h3>
			</div>
			<div class="sb-modal-body">
				<p>You will not be able to edit this sign later.</p>

				<p>Are you sure you wish to continue?</p>
			</div>
			<div class="sb-modal-foot">
				<button type="button" class="sb-modal-dismiss sb-button-secondary purchase_button" type="button"
						id="purchase_button" onclick="disable_screen();">Continue
				</button>
				<button class="sb-button-plain sb-modal-dismiss purchase_button" type="button">Go Back</button>
			</div>
		</div>
	</div>

	<div id="confirm_continue_add_to_cart" class="sb-modal-overlay">
		<div class="sb-modal">
			<div class="sb-modal-head">
				<div class="sb-modal-dismiss">&times;</div>
				<h3>Confirm Page Navigation</h3>
			</div>
			<div class="sb-modal-body">
				<p>You will not be able to edit this sign later.</p>

				<p>Are you sure you wish to continue?</p>
			</div>
			<div class="sb-modal-foot">
				<button type="button" class="sb-modal-dismiss sb-button-primary purchase_button" type="button"
						id="add_to_cart_button" onclick="disable_screen();">Continue
				</button>
				<button class="sb-button-plain sb-modal-dismiss purchase_button" type="button">Go Back</button>
			</div>
		</div>
	</div>

	<?php if (Settings::instance()->get('sign_builder_finish_on') == 1): ?>
		<div id="complete_finish_step_modal" class="sb-modal-overlay">
			<div class="sb-modal">
				<div class="sb-modal-head">
					<div class="sb-modal-dismiss">&times;</div>
					<h3>Complete Step <span class="step_number">5</span></h3>
				</div>
				<div class="sb-modal-body">
					<p>You must complete step <span class="step_number">5</span> to continue.</p>

					<p>If you have selected "Laminate", you must also choose a lamination type.</p>
				</div>
				<div class="sb-modal-foot">
					<button class="sb-modal-dismiss" type="button">OK</button>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div id="preview_modal" class="sb-modal-overlay preview_modal">
		<div class="sb-modal">
			<div class="sb-modal-head">
				<div class="sb-modal-dismiss">&times;</div>
				<h3>Preview of Your Sign</h3>
			</div>
			<div class="sb-modal-body">
				<div id="complete_sign_preview_area" class="complete_sign_preview_area">
					<img id="complete_sign_preview_image" alt="Preview" src=""/>
				</div>
			</div>
			<div class="sb-modal-foot">
				<a href="#" class="sb-button-plain sb-modal-dismiss">OK</a>
			</div>
		</div>
	</div>

	<div id="checkout_preview_modal" class="sb-modal-overlay preview_modal">
		<div class="sb-modal">
			<div class="sb-modal-head">
				<div class="sb-modal-dismiss">&times;</div>
				<h3>Preview of Your Sign</h3>
			</div>
			<div class="sb-modal-body">
				<!-- <p>Your sign has been successfully added to the cart.</p> -->
				<div id="checkout_complete_sign_preview_area" class="complete_sign_preview_area">
					<img id="checkout_complete_sign_preview_image" alt="Preview" src=""/>
				</div>
			</div>
			<div class="sb-modal-foot">
				<a href="/checkout.html" class="sb-button-primary">Checkout</a>
				<a href="#" id="create_another_button" class="sb-button-plain sb-modal-dismiss">Create Another</a>
			</div>
		</div>
	</div>

	<?php if ($material_page AND $material_page[0]['publish'] == 1): ?>
		<div id="material_description_modal" class="sb-modal-overlay">
			<div class="sb-modal">
				<div class="sb-modal-head">
					<div class="sb-modal-dismiss">&times;</div>
					<h1 style="border-bottom:none;">Material Description &amp; Usage</h1>
				</div>
				<div class="sb-modal-body"><?= $material_page[0]['content'] ?></div>
				<div class="sb-modal-foot">
					<button class="sb-button-plain sb-modal-dismiss" type="button">OK</button>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if ($blank_sign): ?>
		<div id="browse_existing_signs_modal" class="sb-modal-overlay browse_existing_signs_modal">
			<div class="sb-modal">
				<div class="sb-modal-head">
					<div class="sb-modal-dismiss">&times;</div>
					<h3>Choose a Sign</h3>
				</div>
				<div class="sb-modal-body">
					<label for="existing_sign_category_list" class="accessible-hide">Select Category</label>
					<select id="existing_sign_category_list">
						<option value="">Select Category</option>
						<?php foreach ($sb_categories as $sb_category): ?>
							<option value="<?= $sb_category['id'] ?>"><?= $sb_category['name'] ?></option>
						<?php endforeach; ?>
					</select>

					<div id="browse_existing_signs_body"></div>
				</div>
				<div class="sb-modal-foot">
					<button type="button" class="sb-button-plain sb-modal-dismiss">Cancel</button>
				</div>
			</div>
		</div>
	<?php endif; ?>


	<div id="error_message_modal" class="sb-modal-overlay">
		<div class="sb-modal">
			<div class="sb-modal-head">
				<div class="sb-modal-dismiss">&times;</div>
				<h3>Error</h3>
			</div>
			<div class="sb-modal-body" id="error_message_area"></div>
			<div class="sb-modal-foot">
				<button class="sb-button-plain sb-modal-dismiss" type="button">OK</button>
			</div>
		</div>
	</div>

	<?= ($cms_editor) ? '</div>' : '</form>' ?>

	<div id="color_palette" class="color_palette">
		<table>
			<thead>
			<tr>
				<th colspan="10">Image Colours</th>
			</tr>
			</thead>
			<tbody class="image_palette">
			<?php for ($i = 0; $i < count($palette_colors) + 8 - count($palette_colors) % 8; $i++)
			{
				echo ($i % 8 == 0) ? '<tr>' : '';
				echo ($i < count($palette_colors)) ? '<td style="background-color:'.$palette_colors[$i]['value'].';"></td>' : '<td></td>';
				echo ($i % 8 == 7) ? '</tr>' : '';
			}?>
			</tbody>

			<thead>
			<tr>
				<th colspan="8">Standard Colours</th>
			</tr>
			</thead>
			<tbody class="standard_palette">
			<tr>
				<td style="background-color:#000000;"></td>
				<td style="background-color:#434343;"></td>
				<td style="background-color:#666666;"></td>
				<td style="background-color:#999999;"></td>
				<td style="background-color:#B7B7B7;"></td>
				<td style="background-color:#CCCCCC;"></td>
				<td style="background-color:#D9D9D9;"></td>
				<td style="background-color:#EFEFEF;"></td>
				<td style="background-color:#F3F3F3;"></td>
				<td style="background-color:#FFFFFF;"></td>
			</tr>
			<tr>
				<td colspan="8" style="border:none;height:2px;"></td>
			</tr>
			<tr>
				<td style="background-color:#990000;"></td>
				<td style="background-color:#FF0000;"></td>
				<td style="background-color:#FF9900;"></td>
				<td style="background-color:#FFFF00;"></td>
				<td style="background-color:#00FF00;"></td>
				<td style="background-color:#00FFFF;"></td>
				<td style="background-color:#3399FF;"></td>
				<td style="background-color:#0000FF;"></td>
				<td style="background-color:#800080;"></td>
				<td style="background-color:#FF00FF;"></td>
			</tr>
			<tr>
				<td colspan="8" style="border:none;height:2px;"></td>
			</tr>
			<tr>
				<td style="background-color:#e6b8af;"></td>
				<td style="background-color:#f4cccc;"></td>
				<td style="background-color:#fce5cd;"></td>
				<td style="background-color:#fff2cc;"></td>
				<td style="background-color:#d9ead3;"></td>
				<td style="background-color:#d0e0e3;"></td>
				<td style="background-color:#c9daf8;"></td>
				<td style="background-color:#cfe2f3;"></td>
				<td style="background-color:#d9d2e9;"></td>
				<td style="background-color:#ead1dc;"></td>
			</tr>
			<tr>
				<td style="background-color:#db7e6b;"></td>
				<td style="background-color:#e89898;"></td>
				<td style="background-color:#f7c99b;"></td>
				<td style="background-color:#fde398;"></td>
				<td style="background-color:#b5d5a7;"></td>
				<td style="background-color:#a1c2c7;"></td>
				<td style="background-color:#a3c0f2;"></td>
				<td style="background-color:#9ec3e6;"></td>
				<td style="background-color:#b3a6d4;"></td>
				<td style="background-color:#d3a5bc;"></td>
			</tr>
			<tr>
				<td style="background-color:#ca4126;"></td>
				<td style="background-color:#de6666;"></td>
				<td style="background-color:#f4b16b;"></td>
				<td style="background-color:#fdd766;"></td>
				<td style="background-color:#92c27d;"></td>
				<td style="background-color:#76a4ae;"></td>
				<td style="background-color:#6d9de9;"></td>
				<td style="background-color:#6fa7da;"></td>
				<td style="background-color:#8d7cc1;"></td>
				<td style="background-color:#c07b9f;"></td>
			</tr>
			<tr>
				<td style="background-color:#a51d02;"></td>
				<td style="background-color:#ca0202;"></td>
				<td style="background-color:#e49039;"></td>
				<td style="background-color:#efc033;"></td>
				<td style="background-color:#6aa74f;"></td>
				<td style="background-color:#45808d;"></td>
				<td style="background-color:#3d78d6;"></td>
				<td style="background-color:#3e84c4;"></td>
				<td style="background-color:#674ea6;"></td>
				<td style="background-color:#a54d79;"></td>
			</tr>
			<tr>
				<td style="background-color:#85200c;"></td>
				<td style="background-color:#990000;"></td>
				<td style="background-color:#b45f06;"></td>
				<td style="background-color:#bf9000;"></td>
				<td style="background-color:#38761d;"></td>
				<td style="background-color:#134f5c;"></td>
				<td style="background-color:#1155cc;"></td>
				<td style="background-color:#0b5394;"></td>
				<td style="background-color:#351c75;"></td>
				<td style="background-color:#741b47;"></td>
			</tr>
			<tr>
				<td style="background-color:#5b0f00;"></td>
				<td style="background-color:#660000;"></td>
				<td style="background-color:#783f04;"></td>
				<td style="background-color:#7f6000;"></td>
				<td style="background-color:#274e13;"></td>
				<td style="background-color:#0c343d;"></td>
				<td style="background-color:#1c4587;"></td>
				<td style="background-color:#073763;"></td>
				<td style="background-color:#20124d;"></td>
				<td style="background-color:#4c1130;"></td>
			</tr>

			<tr>
				<td colspan="9">Transparent&nbsp;</td>
				<td style="background-color:transparent;" class="transparent_option" title="transparent"></td>
			</tr>
			</tbody>

			<thead>
			<tr>
				<th colspan="10">Custom Colours</th>
			</tr>
			</thead>
			<tbody class="custom_palette">
			</tbody>
			<tfoot>
			<tr>
				<td colspan="5">
					<div id="custom_color_link" style="text-align:left;"><input type="hidden"/><a href="#">More
							Colours...</a></div>
				</td>
				<td colspan="5"><a id="dropper_tool" href="#" style="font-size:11px">Dropper tool</a></td>
			</tr>
			</tfoot>
		</table>
	</div>
	<div class="accessible-hide"><img id="dummy_image"/></div>
	</div>
	<script type="text/javascript"
			src="<?= URL::get_engine_plugin_assets_base('products') ?>js/front_end/builder_layer.js"></script>
	<script type="text/javascript"
			src="<?= URL::get_engine_plugin_assets_base('products') ?>js/front_end/builder.js"></script>
<?php if (!$cms_editor): ?>
	<script type="text/javascript"
			src="<?= URL::get_skin_urlpath() ?>/js/product_details.js"></script>
	<script type="text/javascript"
			src="<?= URL::get_engine_plugin_assets_base('products') ?>js/front_end/checkout.js"></script>
<?php endif; ?>

<script type="text/javascript" src="//gabelerner.github.io/canvg/canvg.js"></script>
