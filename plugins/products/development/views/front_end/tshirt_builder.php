<?php
$filepath = URL::get_engine_plugin_assets_base('products').'/images/';
$product_filepath = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR, '/'));

// TODO: Move to controller
$matrix_model = new Model_Matrix($matrix);
$groups[0]['value'] = $matrix_model->get_option_1_id();
$groups[0]['options'] = Model_Option::get_options_by_group_id($groups[0]['value'], $matrix_model->get_id());
$groups[1]['value'] = $matrix_model->get_option_2_id();
$groups[1]['options'] = Model_Option::get_options_by_group_id($groups[1]['value']);

if (Model_Option::is_colour_group($groups[0]['value']))
{
	$colors = $groups[0];
}
if (Model_Option::is_colour_group($groups[1]['value']))
{
	$colors = $groups[1];
}

if (isset($groups[0]['options'][0]) && isset($groups[1]['options'][0]))
{
	$default = Model_Product::get_matrix_option_details($groups[0]['options'][0]['id'], $groups[1]['options'][0]['id'], $matrix);
}


if (isset($view_css_files))
{
	foreach ($view_css_files as $css_item_html) echo $css_item_html;
}
?>
	<div id="breadcrumb-nav">
		<?= IbHelpers::breadcrumb_navigation(); ?>
	</div>

	<div id="tshirt_builder_wrapper" class="tshirt_builder_wrapper">
	<h1><?= $title ?></h1>

	<form id="ProductDetailsForm" action="#">
	<input type="hidden" id="tshirt_default_image" value="<?= isset($default['image']) ? $default['image'] : ''; ?>"/>
	<input type="hidden" id="tshirt_overprint" value="<?= (isset($images[0])) ? $product_filepath.$images[0] : '' ?>"/>
	<input type="hidden" id="tshirt_matrix" value="<?= $matrix ?>"/>

	<section class="canvas_section">
		<div id="canvas_wrapper" class="canvas_wrapper">
			<canvas id="tshirt_builder_canvas" width="345" height="424">Your browser does not support the canvas
				element.
			</canvas>
			<canvas class="hidden" id="product_thumbnail_canvas" width="30" height="30" style="display: none;"></canvas>
		</div>

		<?php if (isset($colors)): ?>
			<ul class="color_swatches" id="color_swatches">
				<?php foreach ($colors['options'] as $color_option): ?>
					<?php $colors = explode(',', $color_option['value']); ?>
					<li data-id="<?= $color_option['id'] ?>" style="display: none;">
						<label title="<?= $color_option['label'] ?>">
							<input type="radio" name="swatch_color" value="<?= $color_option['id'] ?>"/>
                                <span>
									<?php if (trim($color_option['image'] != '')): ?>
										<span class="color_swatch"
											  style="background-image:url('<?= $product_filepath.$color_option['image'] ?>');"></span>
									<?php else: ?>
										<?php foreach ($colors as $color): ?>
											<span class="color_swatch" style="background:<?= $color ?>"></span>
										<?php endforeach; ?>
									<?php endif; ?>
                                </span>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</section>

	<div class="controls_sections" id="product_details">
		<?php if (Settings::instance()->get('product_enquiry') != 1 AND $display_price != 0 AND $disable_purchase != 1): ?>
			<div id="product_price">
				<?php if ($display_offer != 0): ?>
					<dl>
						<dt>Price</dt>
						<dd><s>&euro;<?= $price ?>&nbsp;</s></dd>
						<dt>Offer Price</dt>
						<dd id="final_price" data-product_price="<?= $price ?>">&euro;<?= $offer_price ?></dd>
					</dl>
				<?php else: ?>
					<dl>
						<dt>Price</dt>
						<dd id="final_price" data-product_price="<?= $price ?>">&euro;<?= $price ?></dd>
					</dl>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php $j = 0; ?>
		<div class="options_section">
			<dl>
				<?php foreach ($groups as $group): ?>
					<?php
                    $label = Model_Option::get_group_label_id($group['value']);
                    $label = trim(substr($label, 0, strpos($label.'(', '(')));
                    $is_color = (strpos(strtolower($label), 'color') !== FALSE OR strpos(strtolower($label), 'colour') !== FALSE)
                    ?>
                    <dt><label for="option_<?= $j ?>"><?= $label  ?></label></dt>
					<dd>
						<select id="option_<?= $j ?>" class="prod_option product_option_<?= strtolower($label) ?><?= $is_color ? ' product_option_color' : '' ?>"
								name="<?= $group['value'] ?>"
								data-option_number="<?= $j ?>">
							<option value="">Please select</option>
							<?php foreach ($group['options'] as $group_option): ?>
								<?php
                                if (isset($_GET['option'.($j + 1)]) AND $_GET['option'.($j + 1)] != '')
								{
									$selected = ($_GET['option'.($j + 1)] == $group_option['id']);
								}
								else
								{
									$selected = ($is_color AND $group_option['value'] == $ref_code);
								}
								?>
								<option value="<?= $group_option['id'] ?>"
									<?= $selected ? ' selected="selected"' : '' ?>
									data-name="<?= $group_option['value'] ?>"
                                    <?= (! empty($group_option['message'])) ? 'data-message="'.htmlspecialchars($group_option['message']).'"' : '' ?>
                                    >
									<?= $group_option['label'] ?>
								</option>
							<?php endforeach; ?>
						</select>

						<div class="notice"></div>
					</dd>
					<?php $j++ ?>
				<?php endforeach; ?>
			</dl>
		</div>

		<?php $option_groups = Model_Option::get_all_groups_by_product($id); ?>
		<?php if (sizeof($option_groups) > 0): ?>
			<div id="product_options" class="left row">
				<dl>
					<?php
                    foreach($option_groups AS $index => $option_group)
                    {
						$label   = trim(substr($option_group['option_group'], 0, strpos($option_group['option_group'].'(', '(')));
                        $options = Model_Option::get_options_by_field('group', $option_group['option_group']);
                        echo '<dt><label for="',$option_group['option_group'],'" class="text_label">',$label,'</label></dt>';
                        echo '<dd>';
                        if (strpos($option_group['option_group'],'custom') !== FALSE)
                        {
                            echo'<input type="text" ' ; //Text Box

                            $message_attribute = '';
                            foreach($options as $option)
                            {
                                $message_attribute = ( ! empty($option['message'])) ? ' data-message="'.htmlspecialchars($option['message']).'"' : '';

                                $label = ((float)$option['price'] > 0) ? $option['label'] ." + &euro". $option['price'] : $option['label'];
                                echo ' value="" placeholder="',$label,'"';
                            }
                            echo 'id="option_',$index + $j,'" name="',$option_group['option_group'],'"',$message_attribute,' class="validate[required] prod_option';
                            echo ' onchange="updateProductOptionPrice()" >';
                        }
                        else
                        {
                            echo '<select id="option_',$index + $j,'" name="',$option_group['option_group'],'" class="validate[required] prod_option';
                            if(!empty($option_group['required'])){
                                echo ' validate[dropdown,min[1]]';
                            }
                            echo '" onchange="updateProductOptionPrice()" data-option_number="'.$j.'">';
                            echo '<option value="">Please select</option>';
                            foreach($options as $option)
                            {
                                $message_attribute = ( ! empty($option['message'])) ? ' data-message="'.htmlspecialchars($option['message']).'"' : '';

                                $label = ((float)$option['price'] > 0) ? $option['label']  : $option['label'];
                                echo '<option value="',$option['id'],'" data-option_price="'.$option['price'].'"'.$message_attribute.(($option['default'] == 1) ? ' selected="selected"' : '').'>',$label, '</option>';
                            }
                            echo '</select>';
                        }
                        echo '</dd>';
                    }
                    ?>
				</dl>
			</div>
		<?php endif; ?>

		<dl>
			<dt><label for="qty">Quantity</label></dt>
			<dd>
				<select id="qty" class="tshirt_builder_quantity" name="qty">
					<?php $quantity_dropdown = (isset($quantity_enabled) AND $quantity_enabled == '1' AND count($options) == 0 AND $quantity < 11) ? ($quantity + 1) : 11; ?>
					<?php for ($i = 1; $i < $quantity_dropdown; ++$i): ?>
						<option><?=$i;?></option>
					<?php endfor; ?>
				</select>
			</dd>
		</dl>

        <?php $j = 0; ?>
        <?php foreach($groups AS $group): ?>
            <div class="product-option-message" data-option_number="<?= $j ?>" style="display: none;"></div>
            <?php $j++ ?>
        <?php endforeach; ?>

        <?php foreach($option_groups AS $option_group): ?>
            <div class="product-option-message" data-option_number="<?= $j ?>" style="display: none;"></div>
            <?php $j++ ?>
        <?php endforeach; ?>

		<h3>Product Information</h3>
		<?= $description ?>

		<div id="product_purchase" class="product_purchase">
			<!-- Add to Cart / Purchase -->
			<button type="button" id="add_to_cart_button"
					onclick="validateQty(<?= $id ?>, document.getElementById('qty').value, getOptions());">Add to
				Cart &raquo;</button>

			<!-- Continue Shopping -->
			<div id="continue_shopping"><a href="/<?= $products_plugin_page ?>">Continue Shopping &raquo;</a></div>


			<div class="successful"></div>

			<div id="product_social_media" class="product_social_media">

				<div class="addthis_toolbox addthis_default_style">
					<a href="https://www.addthis.com/bookmark.php?v=250&amp;username=ra-537f53e21efc17e5" class="addthis_button_compact"></a>
					<span class="addthis_separator">&#124;</span>

					<?php if (count($images) > 0): ?>
						<?php
							$i = count($images) - 1;
							$image_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, $images[$i], rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR, '/').'/_thumbs');
							$image_path = file_exists($image_path) ? $image_path : str_replace('/_thumbs/', '/', $image_path);
						?>
						<a href="https://www.facebook.com/dialog/share
							?app_id=140586622674265
							&display=popup
							&href=<?= urlencode(URL::base()) ?>
							&picture=<?= urlencode($image_path) ?>
							&title=<?= urlencode($title) ?>
							&description=<?= trim(urlencode(strip_tags(str_replace('<', ' <', $description)))) ?>
							&redirect_uri=http%3A%2F%2Fs7.addthis.com%2Fstatic%2Fthankyou.html
							"
							target="_blank"
							class="at300b"
							id="tshirt-facebook-share"
							title="Facebook"
							>
							<span class="at-icon-wrapper" style="line-height: 16px; height: 16px; width: 16px; background-color: rgb(59, 89, 152);"><svg
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 32 32" title="Facebook" alt="Facebook" style="width: 16px; height: 16px;"
									class="at-icon at-icon-facebook"><g><path d="M22 5.16c-.406-.054-1.806-.16-3.43-.16-3.4 0-5.733 1.825-5.733 5.17v2.882H9v3.913h3.837V27h4.604V16.965h3.823l.587-3.913h-4.41v-2.5c0-1.123.347-1.903 2.198-1.903H22V5.16z" fill-rule="evenodd"></path></g></svg></span>
						</a>
					<?php endif; ?>

					<a class="addthis_button_twitter"></a>
					<a class="addthis_button_google"></a>
					<a class="addthis_button_pinterest_share"></a>
				</div>
				<script type="text/javascript"
						src="https://s7.addthis.com/js/250/addthis_widget.js#username=ra-537f53e21efc17e5"></script>

				<div id="fb-root"></div>
				<script>(function (d, s, id) {
						var js, fjs = d.getElementsByTagName(s)[0];
						if (d.getElementById(id)) return;
						js = d.createElement(s);
						js.id = id;
						js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3";
						fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));
				</script>

				<div class="fb-like"
					 data-href="<?= substr(URL::site(), 0, -1).$_SERVER["REQUEST_URI"] ?>"
					 data-width="280"
					 data-height="22"
					 data-colorscheme="light"
					 data-layout="standard"
					 data-action="like"
					 data-show-faces="false"
					 data-send="false">
				</div>

			</div>

			<!-- Contact Us -->
			<?php
			$url_parts = explode('/', trim($_SERVER['SCRIPT_URL'], '/'));
			$product_name = end($url_parts);
			?>
			<button type="button" id="contact_us_button"><a
					href="<?= URL::site() ?>contact-us.html?pid=<?= $id ?>&pname=<?= $product_name ?>">Contact Us</a>
			</button>
		</div>

	</div>

	<?php if (count($images) > 1): ?>
		<div class="proThum-area additional_images">
			<link rel="stylesheet" type="text/css"
				  href="<?= URL::get_engine_plugin_assets_base('gallery'); ?>css/lytebox.css"/>
			<script type="text/javascript"
					src="<?= URL::get_engine_plugin_assets_base('gallery'); ?>js/lytebox.js"></script>

			<?php for ($i = 1; $i < count($images); $i++): ?>
				<?php
				$image_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, $images[$i], rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR, '/').'/_thumbs');
				$image_path = file_exists($image_path) ? $image_path : str_replace('/_thumbs/', '/', $image_path);
				?>
				<a href="<?= str_replace('/_thumbs/', '/', $image_path) ?>" class="lytebox"
				   data-title="<?= $images[$i] ?>"
				   data-lyte-options="slide:true group:slideshow slideInterval:4500 showNavigation:true autoEnd:false loopSlideshow:true">
					<img
						src="<?= $image_path ?>"
						width="500"
						height="500"
						alt="<?= $images[$i] ?>" class="additional_image" id="prodImage_<?= $i ?>"/>
				</a>
			<?php endfor; ?>
		</div>
	<?php endif; ?>

	<!-- Related products -->
	<?php if (isset($related_to) AND count($related_to) > 0): ?>
		<div id="products_related" class="products_related">
			<h2>Related Products</h2>
			<?= Model_Product::render_related_products_html($related_to); ?>
		</div>
	<?php endif; ?>

	<!-- YouTube videos -->
	<?php if (isset($videos) AND count($videos) > 0): ?>
		<div id="youtube_videos">
			<h2>Videos</h2>
			<?php foreach ($videos as $key => $video): ?>
				<div class="youtube_video">
					<object width="240" height="158" data="https://www.youtube.com/v/<?= $video['video_id']; ?>"
							type="application/x-shockwave-flash">
						<param name="allowFullScreen" value="true"/>
						<param name="src" value="https://www.youtube.com/v/<?= $video['video_id']; ?>"/>
					</object>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>


	</form>

	</div>
<?php
if (isset($view_js_files))
{
	foreach ($view_js_files as $js_item_html) echo $js_item_html;
}
?>