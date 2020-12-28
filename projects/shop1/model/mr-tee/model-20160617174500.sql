/*
ts:2016-06-17 17:45:00
*/

UPDATE IGNORE `plugin_pages_layouts` SET
  `use_db_source` = 1,
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  `source`        = '<?php include Kohana::find_file(\'template_views\', \'html_document_header\'); ?>\n	<body id=\"<?= $page_data[\'layout\'] ?>\" class=\"<?= $page_data[\'category\'] ?> pagename-<?= str_replace(\'.html\', \'\', $page_data[\'name_tag\']) ?>\">\n		<div id=\"wrap\">\n			<div id=\"container\">\n				<?php include PROJECTPATH.\'/views/templates/\'.Kohana::$config->load(\'config\')->template_folder_path.\'/header.php\'; ?>\n\n				<div id=\"main\">\n					<div id=\"sideLt\">\n						<div class=\"panels_lt\">\n							<?php $show_products = (Settings::instance()->get(\'products_menu\') === FALSE OR Settings::instance()->get(\'products_menu\') == 1) ?>\n							<div class=\"products_menu\">\n								<div class=\"products_menu\">\n									<?php if ( ! $show_products): ?>\n										<div>\n											<?= menuhelper::add_menu_editable_heading(\'left\', \'ul_level_1\'); ?>\n										</div>\n									<?php else: ?>\n										<?= Model_Product::render_products_menu(); ?>\n									<?php endif; ?>\n								</div>\n							</div>\n							<?=Model_Panels::get_panels_feed(\'content_left\');?>\n						</div>\n					</div>\n					<div id=\"ct\">\n						<div id=\"banner\"><?= Model_PageBanner::render_frontend_banners($page_data[\'banner_photo\']); ?></div>\n						<div id=\"checkout_messages\"><?= IbHelpers::get_messages(); ?></div>\n						<div class=\"content\"><?= $page_data[\'content\'] ?></div>\n\n						<?php // ================================================ // ?>\n						<?php extract($checkout_data); ?>\n\n						<?php if (!empty($products_list)): ?>\n							<script type=\"text/javascript\">\n								var shared_assets = \"<?=URL::get_engine_assets_base() ?>\";\n								var urlBase = \"<?= URL::base(); ?>\";\n								var payPalRedirect = <?= intval(Settings::instance()->get(\'paypal_payment_mode\')); ?>;\n							</script>\n							<form id=\"creditCardForm\">\n								<input type=\"hidden\" value=\"<?= Model_Payments::get_thank_you_page(FALSE); ?>\" name=\"thanks_page\" id=\"thanks_page\">\n								<input type=\"hidden\" value=\"\" name=\"template_name\" id=\"template_name\">\n\n								<div class=\"min-height\">\n									<div id=\"checkoutTable\">\n										<table class=\"checkoutTable\">\n											<thead>\n												<tr>\n													<th scope=\"col\" class=\"checkout_title\"><?= __(\'Product Name\') ?></th>\n													<th scope=\"col\"><?= __(\'Price\') ?></th>\n													<th scope=\"col\"><?= __(\'Qty\') ?></th>\n													<th scope=\"col\"><?= __(\'Subtotal\') ?></th>\n													<th></th>\n												</tr>\n											</thead>\n											<tbody>\n												<?= $products_list ?>\n\n												<?php if (isset($accept_coupons) AND $accept_coupons): ?>\n													<tr class=\"tr_totalprice product_line_first_td\">\n														<td>\n															<div class=\"checkout_option\">\n																<div class=\"txt_lable\"><label for=\"coupon_code\"><?= __(\'Enter Coupon Code\') ?></label></div>\n																<div class=\"input_lable\">\n																	<input type=\"text\" id=\"coupon_code\"/><input class=\"coupon_button\" type=\"button\" value=\"Validate\" onclick=\"validate_coupon()\" />\n																</div>\n															</div>\n														</td>\n														<td><?= __(\'Subtotal\') ?></td>\n														<td></td>\n														<td class=\"priceField subtotal\">€<?= @$subtotal ?></td>\n														<td></td>\n													</tr>\n												<?php else: ?>\n													<tr class=\"tr_totalprice product_line_first_td\">\n														<td>&nbsp;</td>\n														<th scope=\"row\"><?= __(\'Subtotal\') ?>:</th>\n														<td></td>\n														<td class=\"priceField subtotal\">&euro;<?= @number_format($subtotal, 2) ?></td>\n														<td></td>\n													</tr>\n												<?php endif; ?>\n\n												<?php // Moved The destination table row to below the Subtotal ?>\n												<tr class=\"tr_destination\">\n													<td class=\"product_line_first_td\">\n\n														<?php if (Settings::instance()->get(\'checkout_delivery_options\') == 1): ?>\n															<h3><?= __(\'Delivery Method\') ?></h3>\n															<div class=\"checkout_control_group\">\n																<div class=\"checkout_label\">\n																	<label for=\"checkout_delivery_method\"><?= __(\'Select Method\') ?></label>\n																</div>\n																<div class=\"checkout_controls\">\n																	<select class=\"validate[required]\" id=\"checkout_delivery_method\" name=\"delivery_method\">\n																		<option value=\"\"><?= __(\'Please Select\') ?></option>\n																		<option value=\"reserve_and_collect\"><?= __(\'Reserve and Collect\') ?></option>\n																		<option value=\"pay_and_post\"><?= __(\'Pay and Post\') ?></option>\n																	</select>\n																</div>\n															</div>\n\n															<div class=\"checkout_control_group\" id=\"checkout_store_wrapper\">\n																<div class=\"checkout_label\">\n																	<label for=\"checkout_store\"><?= __(\'Select Store\') ?></label>\n																</div>\n																<div class=\"checkout_controls\">\n																	<select class=\"validate[required]\" id=\"checkout_store\" name=\"store_id\">\n																		<?php $stores = Model_Location::get(NULL, array(array(\'type\', \'in\', array(\'Pharmacy\',\'Store\'))));?>\n																		<option value=\"\"><?= __(\'Please Select\') ?></option>\n																		<?php foreach ($stores as $store): ?>\n																			<option value=\"<?= $store[\'id\'] ?>\">\n																				<?= $store[\'title\'].\n																					((trim($store[\'address_1\']) == \'\') ? \'\' : \', \'.trim($store[\'address_1\'])).\n																					((trim($store[\'address_2\']) == \'\') ? \'\' : \', \'.trim($store[\'address_2\'])).\n																					((trim($store[\'address_3\']) == \'\') ? \'\' : \', \'.trim($store[\'address_3\'])).\n																					((trim($store[\'county\'])    == \'\') ? \'\' : \', \'.trim($store[\'county\']));\n																				?>\n																			</option>\n																		<?php endforeach; ?>\n																	</select>\n																</div>\n															</div>\n														<?php endif; ?>\n\n														<div class=\"checkout_control_group\" id=\"checkout_postal_zone_wrapper\">\n															<div class=\"checkout_label\">\n																<label for=\"postalZone\" class=\"label-mandatory\">\n																	<?= Model_Localisation::get_ctag_translation(Settings::instance()->get(\'postal_destination_string\'), I18n::$lang) ?>\n																</label>\n															</div>\n															<div class=\"checkout_controls\">\n																<select id=\"postalZone\" class=\"validate[required]\" onchange=\"changeZone(this.value)\" name=\"zones\">\n																	<option value=\"\">\n																		<?= (isset($postal_methods) AND $postal_methods) ? __(\'Select Delivery Method\') : __(\'Select Postal Zone\') ?>\n																	</option>\n																	<?php foreach ($postage as $place): ?>\n																		<option value=\"<?= $place[\'id\'] ?>\"<?php if ($place[\'id\'] == @$zone_id) echo \' selected=\"selected\" \' ?>><?= $place[\'title\'] ?></option>\n																	<?php endforeach; ?>\n																</select>\n																<input type=\"hidden\" id=\"use_postal_methods\" value=\"<?= (isset($postal_methods) AND $postal_methods) ? 1 : 0 ?>\"/>\n															</div>\n														</div>\n													</td>\n													<th scope=\"row\"><?= __(\'Postage\') ?>:</th>\n													<td></td>\n													<td class=\"priceField postage\">&euro;<?= @number_format($shipping_price, 2) ?></td>\n													<td></td>\n												</tr>\n\n												<tr>\n													<td colspan=\"5\" id=\"checkout_discount_applied\"></td>\n												</tr>\n\n												<tr class=\"tr_totalprice product_line_first_td\" <?= (empty($discounts)) ? \'style=\"display:none;\"\' : \'\' ?>>\n													<td>&nbsp;</td>\n													<th scope=\"row\"><?= __(\'Discounts\') ?>:</th>\n													<td></td>\n													<td class=\"priceField discounts\">&minus;&nbsp;&euro;<?= @$discounts ?></td>\n													<td></td>\n												</tr>\n\n												<tr class=\"tr_totalprice product_line_first_td\">\n													<td>\n														<span class=\"continue-shopping\">\n															<?php\n															$continue_shopping_url = Session::instance()->get(\'last_product_browsing_url\');\n															$continue_shopping_url = ($continue_shopping_url == \'\') ? \'/products.html\' : $continue_shopping_url;\n															?>\n\n															<a href=\"<?= $continue_shopping_url ?>\"><?= __(\'Continue Shopping\') ?></a>\n														</span>\n													</td>\n													<th scope=\"row\" class=\"highlight_total\"><?= __(\'Total\') ?>:</th>\n													<td class=\"highlight_total\"></td>\n													<td class=\"highlight_total priceField totalprice\">&euro;<?= @number_format($final_price, 2) ?></td>\n													<td></td>\n												</tr>\n\n											</tbody>\n										</table>\n									</div>\n									<div class=\"DeliveryDeals\" id=\"DeliveryDeals\">Delivery Deals:</div>\n									<div id=\"checkout_discount_options\" class=\"chkout_options\"></div>\n\n									<?php if ((isset($paypal_enabled) AND $paypal_enabled == TRUE) OR ($stripe[\'enabled\'])): ?>\n										<script>\n											$(\"#payment_method_selector\").change(function () {\n												if ($(\"#payment_method_selector\").val() === \"card\") {\n													$(\"#method_1\").click();\n												}\n												else if ($(\"#payment_method_selector\").val() === \"paypal\") {\n													$(\"#method_2\").click();\n												}\n												else if ($(\"#payment_method_selector\").val() === \"stripe\") {\n													$(\"#method_3\").click();\n												}\n											});\n\n											$(document).ready(function () {\n												$(\"#payment_method_selector option:first\").change();\n											});\n										</script>\n									<?php endif; ?>\n\n									<div id=\"checkoutForm2\">\n										<input type=\"hidden\" value=\"\" id=\"total\" name=\"total\" />\n										<div id=\"CartDetailsBox\">\n											<div id=\"CustomerAddress\">\n												<h2><?= __(\'Billing Information\') ?></h2>\n												<table class=\"CartDetails\">\n													<tbody>\n														<tr>\n															<th scope=\"col\" colspan=\"2\"></th>\n														</tr>\n														<tr id=\"full_name_row\">\n															<td class=\"label\"><label for=\"ccName\" class=\"label-mandatory\"><?= __(\'Full Name\') ?>:</label></td>\n															<td>\n																<input type=\"text\" style=\"width: 200px;\" maxlength=\"50\" name=\"ccName\" id=\"ccName\" value=\"\" class=\"validate[required] text-input\" />\n															</td>\n														</tr>\n														<tr id=\"first_name_row\">\n															<td class=\"label\"><label for=\"first_name\" class=\"label-mandatory\"><?= __(\'First Name\') ?>:</label></td>\n															<td>\n																<input type=\"text\" style=\"width: 200px;\" maxlength=\"50\" name=\"ccFirstName\" id=\"first_name\" value=\"\" class=\"\" />\n															</td>\n														</tr>\n														<tr id=\"last_name_row\">\n															<td class=\"label\"><label for=\"last_name\" class=\"label-mandatory\"><?= __(\'Surname\') ?>:</label></td>\n															<td>\n																<input type=\"text\" style=\"width: 200px;\" maxlength=\"50\" name=\"ccLastName\" id=\"last_name\" value=\"\" />\n															</td>\n														</tr>\n														<tr>\n															<td class=\"label\"><label for=\"address_1\" class=\"label-mandatory\"><?= __(\'Address\') ?>:</label></td>\n															<td>\n																<textarea style=\"width: 200px;\" name=\"address_1\" id=\"address_1\" class=\"validate[required]\"></textarea>\n															</td>\n														</tr>\n														<script type=\"text/javascript\">\n															$(\'#first_name_row\').hide();\n															$(\'#last_name_row\').hide();\n														</script>\n														<tr>\n															<td class=\"label\"><label for=\"address_2\" class=\"label-mandatory\"><?= __(\'Town/City\') ?>:</label></td>\n															<td>\n																<input type=\"text\" style=\"width: 100px;\" maxlength=\"70\" name=\"address_2\" id=\"address_2\" value=\"\" class=\"validate[required] text-input\">\n															</td>\n														</tr>\n														<tr>\n															<td class=\"label\"><label for=\"address_3\" class=\"label-mandatory\" data-string-state_county=\"<?= __(\'State/County\') ?>\" data-string-county=\"<?= __(\'County\') ?>\"><?= __(\'State/County\') ?>:</label></td>\n															<td>\n																<input type=\"text\" style=\"width: 100px;\" maxlength=\"70\" name=\"address_3\" id=\"address_3\" value=\"\" class=\"validate[required] text-input\">\n															</td>\n														</tr>\n														<tr>\n															<td class=\"label\"><label for=\"address_4\" class=\"label-mandatory\"><?= __(\'Country\') ?>:</label></td>\n															<td>\n																<?php $countries = explode(\"\n\", trim(Settings::instance()->get(\'checkout_countries\'))); ?>\n																<?php if (sizeof($countries) > 0 AND $countries[0] != \'\'): ?>\n																	<select id=\"address_4\" class=\"validate[required]\">\n																		<?php foreach ($countries as $country): ?>\n																			<option><?= $country ?></option>\n																		<?php endforeach; ?>\n																	</select>\n																<?php else: ?>\n																	<input type=\"text\" style=\"width: 100px;\" maxlength=\"70\" name=\"address_4\" id=\"address_4\"\n																		   value=\"<?= __(\'Ireland\') ?>\" class=\"validate[required] text-input\">\n																<?php endif; ?>\n															</td>\n														</tr>\n														<tr>\n															<td class=\"label\"><label for=\"phone\" class=\"label-mandatory\"><?= __(\'Tel/Mobile\') ?>:</label></td>\n															<td>\n																<input type=\"text\" style=\"width: 100px;\" maxlength=\"70\" name=\"phone\" id=\"phone\" value=\"\" class=\"validate[required] text-input\">\n															</td>\n														</tr>\n														<tr id=\"email_row\">\n															<td class=\"label\"><label for=\"email\" class=\"label-mandatory\"><?= __(\'Email\') ?>:</label></td>\n															<td>\n																<input type=\"text\" style=\"width: 180px;\" maxlength=\"70\" name=\"email\" id=\"email\" value=\"\" class=\"validate[required,custom[email]] text-input\" />\n															</td>\n														</tr>\n													</tbody>\n												</table>\n												<input type=\"hidden\" name=\"comments\" value=\"\" />\n												<div class=\"shipping_heading_wrapper\" id=\"shipping_heading_wrapper\">\n													<h2><?= Model_Localisation::get_ctag_translation(Settings::instance()->get(\'shipping_information_string\'), I18n::$lang) ?></h2>\n													<label>\n														<input type=\"checkbox\" name=\"addressCheckbox\" id=\"addressCheckbox\" onchange=\"shippingAddress()\" value=\"1\" checked=\"checked\"> <?= __(\'ship to the billing address\') ?>\n													</label>\n												</div>\n\n												<div style=\"display: none;\" id=\"shippingAddressDiv\">\n													<table class=\"CartDetails\">\n														<tbody>\n															<tr>\n																<th scope=\"col\" colspan=\"2\"></th>\n															</tr>\n															<tr>\n																<td><label for=\"shipping_name\" class=\"label-mandatory\"><?= __(\'First Name\') ?>:</label></td>\n																<td>\n																	<input type=\"text\" name=\"shipping_name\" id=\"shipping_name\" />\n																</td>\n															</tr>\n															<tr>\n																<td><label for=\"shipping_surname\"><?= __(\'Surname\') ?>:</label></td>\n																<td>\n																	<input type=\"text\" name=\"shipping_surname\" id=\"shipping_surname\" />\n																</td>\n															</tr>\n															<tr>\n																<td><label for=\"shipping_address_1\" class=\"label-mandatory\"><?= __(\'Address\') ?>:</label></td>\n																<td>\n																	<textarea style=\"width: 200px;\" name=\"shipping_address_1\" id=\"shipping_address_1\"\n																			  class=\"validate[required]\"></textarea>\n																</td>\n															</tr>\n															<tr>\n																<td><label for=\"shipping_address_2\" class=\"label-mandatory\"><?= __(\'Town/City\') ?>:</label></td>\n																<td>\n																	<input type=\"text\" style=\"width: 100px;\" maxlength=\"70\" name=\"shipping_address_2\"\n																		   id=\"shipping_address_2\" value=\"\">\n																</td>\n															</tr>\n															<tr>\n																<td><label for=\"shipping_address_3\" class=\"label-mandatory\"><?= __(\'State/County\') ?>:</label></td>\n																<td>\n																	<input type=\"text\" style=\"width: 100px;\" maxlength=\"70\" name=\"shipping_address_3\"\n																		   id=\"shipping_address_3\" value=\"\">\n																</td>\n															</tr>\n															<tr>\n																<td><label for=\"shipping_address_4\" class=\"label-mandatory\"><?= __(\'Country\') ?>:</label></td>\n																<td>\n																	<?php if (sizeof($countries) > 0 AND $countries[0] != \'\'): ?>\n																		<select id=\"shipping_address_4\" class=\"validate[required]\">\n																			<?php foreach ($countries as $country): ?>\n																				<option><?= __($country) ?></option>\n																			<?php endforeach; ?>\n																		</select>\n																	<?php else: ?>\n																		<input type=\"text\" style=\"width: 100px;\" maxlength=\"70\" name=\"shipping_address_4\" id=\"shipping_address_4\" value=\"<?= __(\'Ireland\') ?>\" class=\"validate[required] text-input\">\n																	<?php endif; ?>\n																</td>\n															</tr>\n															<tr>\n																<td class=\"label\"><label for=\"shipping_postcode\"><?= __(\'Postcode\') ?>:</label></td>\n																<td>\n																	<input type=\"text\" style=\"width: 180px;\" maxlength=\"70\" name=\"shipping_postcode\" id=\"shipping_postcode\" value=\"\" />\n																</td>\n															</tr>\n															<tr>\n																<td class=\"label\"><label for=\"shipping_phone\" class=\"label-mandatory\"><?= __(\'Tel/Mobile\') ?>:</label></td>\n																<td>\n																	<input type=\"text\" style=\"width: 100px;\" maxlength=\"70\" name=\"shipping_phone\" id=\"shipping_phone\">\n\n																	<div class=\"shipping_details_phone_notice\"><?= __(\'Please provide a phone number for the shipping address\') ?></div>\n																</td>\n															</tr>\n															<tr id=\"row_shipping_email\">\n																<td class=\"label\"><label for=\"shipping_email\"><?= __(\'Email\') ?>:</label></td>\n																<td>\n																	<input type=\"text\" style=\"width: 180px;\" maxlength=\"70\" name=\"shipping_email\" id=\"shipping_email\" value=\"\" />\n																</td>\n															</tr>\n														</tbody>\n													</table>\n												</div>\n											</div>\n\n											<div id=\"cart_display_options\" style=\"float:left;\"></div>\n\n											<div id=\"paymentSelect\" class=\"paymentSelect\"<?= ($payment_method_count <= 1) ? \' style=\"display: none;\"\' : \'\' ?>>\n												<div>\n													<h2><?= __(\'Select your Payment Method\') ?></h2>\n													<select id=\"payment_method_selector\" style=\"display:none;\">\n														<?php if ($realex_enabled): ?>\n															<option value=\"card\" selected><?= __(\'Debit/Credit Card\') ?></option>\n														<?php endif; ?>\n														<?php if ($paypal_enabled): ?>\n															<option value=\"paypal\"><?= __(\'Paypal\') ?></option>\n														<?php endif; ?>\n														<?php if ($stripe[\'enabled\']): ?>\n															<option value=\"stripe\"><?= __(\'Stripe\') ?></option>\n														<?php endif; ?>\n													</select>\n													<?php if ($realex_enabled): ?>\n														<span onclick=\"changeMethod(this);\" class=\"payment_method grey\" id=\"method_1\"><?= __(\'method 1\') ?></span><?php endif; ?>\n													<?php if ($paypal_enabled): ?>\n														<span onclick=\"changeMethod(this);\" class=\"payment_method grey\" id=\"method_2\"><?= __(\'method 2\') ?></span><?php endif; ?>\n													<?php if ($stripe[\'enabled\']): ?>\n														<span onclick=\"changeMethod(this);\" class=\"payment_method grey\" id=\"method_3\"><?= __(\'method 3\') ?></span><?php endif; ?>\n												</div>\n											</div>\n\n											<?php if ($realex_enabled): ?>\n												<div id=\"CardDetails\"\n													 class=\"payment_method_view\"<?= ($default_payment != \'Realex\') ? \' style=\"display: none\"\' : \'\' ?>>\n													<table class=\"CardDetails\">\n														<tbody>\n															<tr>\n																<td colspan=\"2\"><h2><?= __(\'Credit Card Payment Details\') ?></h2></td>\n															</tr>\n															<tr>\n																<td class=\"label\"><label for=\"ccType\" class=\"label-mandatory\"><?= __(\'Card type\') ?>:</label></td>\n																<td>\n																	<select name=\"ccType\" id=\"ccType\" class=\"validate[required]\">\n																		<option value=\"\"><?= __(\'Please select\') ?></option>\n																		<option value=\"visa\"><?= __(\'Visa\') ?></option>\n																		<option value=\"mc\"><?= __(\'Mastercard\') ?></option>\n																	</select>\n																</td>\n															</tr>\n															<tr>\n																<td class=\"label\"><label for=\"ccNum\" class=\"label-mandatory\"><?= __(\'Card number\') ?>:</label></td>\n																<td>\n																	<input type=\"text\" style=\"width: 180px;\" maxlength=\"19\" name=\"ccNum\" value=\"\" id=\"ccNum\" class=\"validate[required,funcCall[luhnTest]] text-input\">\n																</td>\n															</tr>\n															<tr>\n																<td class=\"label\"><label for=\"ccv\" class=\"label-mandatory\"><?= __(\'CVV number\') ?>:</label></td>\n																<td>\n																	<input type=\"text\" style=\"width: 50px;\" maxlength=\"4\" id=\"ccv\" name=\"ccv\" value=\"\" class=\"validate[required,custom[onlyNumberSp]] text-input\">\n																</td>\n															</tr>\n															<tr>\n																<td class=\"label\"><label for=\"ccExpMM\" class=\"label-mandatory\"><?= __(\'Expiry date\') ?>:</label></td>\n																<td>\n																	<select name=\"ccExpMM\" id=\"ccExpMM\" class=\"validate[required]\">\n																		<option value=\"\"><?= __(\'MM\') ?></option>\n																		<option value=\"01\">01</option>\n																		<option value=\"02\">02</option>\n																		<option value=\"03\">03</option>\n																		<option value=\"04\">04</option>\n																		<option value=\"05\">05</option>\n																		<option value=\"06\">06</option>\n																		<option value=\"07\">07</option>\n																		<option value=\"08\">08</option>\n																		<option value=\"09\">09</option>\n																		<option value=\"10\">10</option>\n																		<option value=\"11\">11</option>\n																		<option value=\"12\">12</option>\n																	</select>\n																	<select name=\"ccExpYY\" id=\"ccExpYY\" class=\"validate[required]\">\n																		<option value=\"\"><?= __(\'YYYY\') ?></option>\n																		<?php\n																		for ($i = date(\'y\'); $i <= (date(\'y\') + 10); $i++)\n																		{\n																			$j = str_pad($i, 2, \"0\", STR_PAD_LEFT);\n																			echo \"<option value=\'$j\'>20$j</option>\n\";\n																		}\n																		?>\n																	</select>\n																</td>\n															</tr>\n															<tr>\n																<td colspan=\"2\" class=\"credit_cars\">\n																	<img alt=\"master card image\" src=\"<?= URL::get_engine_plugin_assets_base(\'products\') ?>images/mastercard.png\" />\n																	<img alt=\"visa card image\" src=\"<?= URL::get_engine_plugin_assets_base(\'products\') ?>images/visa.png\" />\n																	<img alt=\"master card image\" src=\"<?= URL::get_engine_plugin_assets_base(\'products\') ?>images/realex_icon.png\" />\n																</td>\n															</tr>\n															<tr>\n																<td colspan=\"2\">\n																	<span class=\"realexmessage\"><?= __(\'We use a secure certificate for all our payments and Realex our payment partner provide all secure connections for your transaction.\') ?></span>\n																</td>\n															</tr>\n														</tbody>\n													</table>\n												</div>\n											<?php endif; ?>\n\n											<div id=\"checkoutMessageBar\"></div>\n\n											<div id=\"FinalDetails\">\n												<table class=\"FinalDetails\">\n													<tbody>\n														<tr>\n															<td>\n																<label><input type=\"checkbox\" id=\"signupCheckbox\" value=\"1\" name=\"signupCheckbox\"> <?= __(\'I would like to sign up the newsletter\') ?></label>\n															</td>\n														</tr>\n														<tr>\n															<td>\n																<label>\n																	<input type=\"checkbox\" id=\"termsCheckbox\" name=\"termsCheckbox\" class=\"validate[required]\">\n																	<?= __(\'I accept the $items\', array(\'$items\' => \'<a target=\"_blank\" href=\"/terms-and-conditions.html\">\'.__(\'terms and conditions\').\'</a>\')) ?>\n																</label>\n															</td>\n														</tr>\n														<?php if ( ! empty($size_guide)): ?>\n															<tr>\n																<td>\n																	<label>\n																		<input type=\"checkbox\" name=\"size_guide_read\" class=\"validate[required]\" id=\"checkout-size_guide_read\" />\n																		<?= __(\'I have read the $size_guide\', array(\'$size_guide\' => \'<a target=\"_blank\" href=\"/\'.$size_guide.\'\">\'.__(\'size guide\').\'</a>\')) ?>\n																	</label>\n																</td>\n															</tr>\n														<?php endif; ?>\n													</tbody>\n												</table>\n\n												<div id=\"payment_button_area\">\n													<button type=\"button\" id=\"submit_checkout_button\"\n															class=\"left product_btn payment_method_view submit_checkout_button\"\n															onclick=\"submitCheckout();\"<?= ($default_payment != \'Realex\') ? \' style=\"display:none;\"\' : \'\'; ?>>\n														<?= __(\'BUY NOW\') ?>\n													</button>\n\n													<div id=\"paypalButton\"\n														 class=\"payment_method_view\"<?= ($default_payment != \'PayPal\') ? \' style=\"display:none;\"\' : \'\'; ?>>\n														<a href=\"<?= \"javascript:CHECKOUT.checkoutWithPayPal(\'\".Model_Payments::get_thank_you_page().\"?clear_cart=1\', \'\".URL::site().\"\', function(status,data){ checkout_data.paypal_error(status,data); });\" ?>\">\n															<img src=\"https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif\" align=\"left\" style=\"margin-right:7px;\">\n														</a>\n													</div>\n\n													<?php if ($stripe[\'enabled\']): ?>\n														<div id=\"stripeButton\"\n															 class=\"payment_method_view\"<?= ($default_payment != \'Stripe\') ? \' style=\"display:none;\"\' : \'\'; ?>>\n															<div style=\"float:right;width:180px;height:41px;\">\n																<img src=\"<?= URL::get_engine_assets_base(); ?>img/stripe.png\"/></div>\n															<button type=\"button\" style=\"height: 25px;width: 90px;margin: 5px;\" class=\"payment_button\"\n																	id=\"stripe-button\" data-key=\"<?= $stripe[\'publishable_key\']; ?>\"><?= __(\'Place Order\') ?>\n															</button>\n														</div>\n														<script src=\"https://checkout.stripe.com/checkout.js\"></script>\n													<?php endif; ?>\n												</div>\n\n											</div>\n										</div>\n									</div>\n								</div>\n\n							</form>\n							<div id=\"collect_dialogue\" class=\"ui-dialog\" title=\"Collect in Store\" style=\"display:none;\">\n								<p><?= __(\'We will contact you when your purchases are ready for collection.\') ?></p>\n							</div>\n							<?php\n\n							// Render CSS Files for THIS View\n							if (isset($view_css_files))\n							{\n								foreach ($view_css_files as $css_item_html) echo $css_item_html;\n							}\n							// Render JS Files for This View\n							if (isset($view_js_files))\n							{\n								foreach ($view_js_files as $js_item_html) echo $js_item_html;\n							}\n							?>\n							<script>\n								$.ajax({\n									type: \"POST\",\n									url: \'/frontend/products/ajax_get_discount_html\',\n									success: function(html){\n										if($(html).filter(\'.offer-con\').html()){\n											$(\'#DeliveryDeals\').show();\n											$(\'#checkout_discount_options\').html(html);\n										}else{\n											$(\'#DeliveryDeals\').hide();\n											$(\'#checkout_discount_options\').html(\'\');\n										}\n									}\n								});\n\n								$.ajax({\n									type: \"POST\",\n									url: \'/frontend/products/ajax_get_applied_discount_html\',\n									success: function(html){\n										$(\'#checkout_discount_applied\').html(html);\n									}\n							   });\n							</script>\n\n						<?php else: ?>\n							<div class=\"alert\"><?= __(\'Your shopping cart is empty.\') ?></div>\n						<?php endif; ?>\n						<script>\n							$(document).ready(function () {\n								update_county_list();\n\n								if (jQuery().datepicker)\n								{\n									$(\".datepicker\").datepicker();\n									$(\".datepicker\").datepicker( \"option\", \"dateFormat\", \'dd-mm-yy\');\n								}\n\n							});\n							$(\'#postalZone\').on(\'change\', function ()\n							{\n								var destination = $(\'#postalZone\').find(\':selected\').html().toLowerCase();\n								if (destination == \'collect in store\' || destination == \'for collection\')\n								{\n									// Shipping destination not needed, when the user is collecting\n									$(\'#addressCheckbox\').prop(\'checked\', true).trigger(\'change\');\n									$(\'#shipping_heading_wrapper\').hide();\n\n									// Display message\n									$(\'#collect_dialogue\').dialog({\n										resizable: false,\n										modal: true,\n										buttons: {\n											OK: function () {\n												$(this).dialog(\'close\');\n											}\n										}\n									});\n								}\n								else\n								{\n									$(\'#shipping_heading_wrapper\').show();\n								}\n								update_county_list();\n							});\n\n							function update_county_list() {\n								var zone = $(\'#postalZone\').find(\':selected\').html();\n								if (document.getElementById(\'use_postal_methods\').value == 1) {\n									zone = document.getElementById(\'address_4\').value;\n								}\n\n								$.ajax({\n									url: \'/frontend/products/ajax_update_county_list/\',\n									data: { zone: zone},\n									type: \'POST\',\n									dataType: \'json\'\n								}).done(function (results) {\n									var $address3_label = $(\'[for=\"address_3\"]\');\n									if (results != \'\') {\n										$address3_label.html($address3_label.data(\'string-county\'));\n										$(\'#address_3\').parent().html(\'<select id=\"address_3\" name=\"address_3\">\' + results + \'</select>\');\n										$(\'[for=\"shipping_address_3\"]\').html($address3_label.data(\'string-county\'));\n										$(\'#shipping_address_3\').parent().html(\'<select id=\"shipping_address_3\" name=\"shipping_address_3\">\' + results + \'</select>\');\n									}\n									else {\n										$address3_label.html($address3_label.data(\'string-state_county\'));\n										$(\'#address_3\').parent().html(\'<input type=\"text\" style=\"width: 100px;\" maxlength=\"70\" name=\"address_3\" id=\"address_3\" value=\"\">\');\n										$(\'[for=\"shipping_address_3\"]\').html($address3_label.data(\'string-state_county\'));\n										$(\'#shipping_address_3\').parent().html(\'<input type=\"text\" style=\"width: 100px;\" maxlength=\"70\" name=\"shipping_address_3\" id=\"shipping_address_3\" value=\"\">\');\n									}\n								});\n							}\n						</script>\n\n\n\n						<?php // ================================================ // ?>\n\n					</div>\n				</div>\n\n				<div id=\"footer\">\n					<?php include PROJECTPATH.\'/views/templates/\'.Kohana::$config->load(\'config\')->template_folder_path.\'/footer.php\'; ?>\n				</div>\n			</div>\n		</div>\n\n		<?= Settings::instance()->get(\'footer_html\'); ?>\n	</body>\n\n<?php include Kohana::find_file(\'template_views\', \'html_document_header\'); ?>'
WHERE `layout` = 'checkout'
;

UPDATE IGNORE `plugin_pages_pages` SET
  `layout_id` = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'checkout' LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
WHERE `name_tag` = 'checkout.html';

UPDATE `plugin_pages_layouts` SET `use_db_source` = 0 WHERE `layout` = 'checkout';

UPDATE `engine_settings`
SET `value_live`='Method', `value_stage`='Postal Method', `value_test`='Postal Method', `value_dev`='Postal Method'
WHERE `variable`='postal_destination_string';