/*
ts:2015-12-10 11:49:00
*/

UPDATE IGNORE `plugin_formbuilder_forms`
SET `fields` = '<input type=\"hidden\" name=\"item_name\" value=\"Quick Order\"><input name=\"event\" value=\"contact-form\" type=\"hidden\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"trigger\"><input type=\"hidden\" name=\"payment_type\" value=\"\"><li><label></label><input type=\"radio\" name=\"donation_type\" value=\"once_off\" id=\"payment_form_type_once_off\"><label for=\"payment_form_type_once_off\">Once-Off Donation</label>\n<input type=\"radio\" name=\"donation_type\" value=\"direct_debit\" id=\"payment_form_type_direct_debit\"><label for=\"payment_form_type_direct_debit\">Direct Debit</label>\n<input type=\"radio\" name=\"donation_type\" value=\"postal\" id=\"payment_form_type_postal\"><label for=\"payment_form_type_postal\">Postal</label></li><li><fieldset id=\"payment_form_amount_fieldset\"><legend>Step 1: Enter Donation Amount</legend><ul><li><label for=\"payment_total\">Amount (&euro;)</label><input type=\"text\" name=\"payment_total\" class=\"validate[required]\" id=\"payment_total\"></li><li></li></ul></fieldset></li><li><fieldset id=\"payment_form_contact_details_fieldset\"><legend>Step 2: Enter Your Details</legend><ul><li><label for=\"email\">Email</label><input type=\"text\" name=\"email\" class=\"validate[required]\" id=\"email\"></li><li><label for=\"payment_form_name\">Name</label><input type=\"text\" name=\"name\" class=\"validate[required]\" id=\"payment_form_name\"></li><li><label for=\"\">Phone</label><input type=\"text\" name=\"phone\"></li><li><label for=\"address\">Address</label><textarea name=\"address\" id=\"address\"></textarea></li><li></li></ul></fieldset></li><li><fieldset id=\"payment_form_payment_select_fieldset\"><legend>Step 3: Select Payment Method</legend><ul><li><label></label><input type=\"radio\" name=\"payment_method\" value=\"Stripe\" id=\"payment_form_method_stripe\" class=\"payment_select payment_select_stripe\"><label for=\"payment_form_method_stripe\">Stripe</label>\n<input type=\"radio\" name=\"payment_method\" value=\"PayPal\" id=\"payment_form_method_paypal\" class=\"payment_select payment_select_paypal\"><label for=\"payment_form_method_paypal\"> PayPal</label></li><li></li></ul></fieldset></li><li><fieldset id=\"payment_form_payment_details_fieldset\"><legend>Step 3: Your Payment Details</legend><ul><li><label for=\"payment_form_account_holder_name\">Account Holder Name</label><input type=\"text\" name=\"account_holder_name\" id=\"payment_form_account_holder_name\" class=\"validate[required]\"></li><li><label for=\"payment_form_bic_number\">BIC Number</label><input type=\"text\" name=\"bic_number\" autocomplete=\"off\" id=\"payment_form_bic_number\" class=\"validate[required]\"></li><li><label for=\"payment_form_iban_number\">IBAN Number</label><input type=\"text\" name=\"iban_number\" autocomplete=\"off\" id=\"payment_form_iban_number\" class=\"validate[required]\"></li><li><label for=\"payment_form_monthly_date\">I would like to donate on this date each month</label><select id=\"payment_form_monthly_date\" name=\"monthly_date\" class=\"validate[required]\"><option value="">Please Select</option><option value=\"7\">7th Monthly</option><option value=\"12\">12th Monthly</option></select></li><li><label for=\"payment_form_start_date\">Start my donation</label><input type=\"text\" class=\"datepicker validate[required]\" name=\"donation_start_date\" id=\"payment_form_start_date\"></li></ul></fieldset></li><li><label for=\"payment_form_terms\">I have read and agree to the <a href=\"/terms-and-conditions.html\">terms and conditions</a></label><input type=\"checkbox\" name=\"terms\" class=\"validate[required]\" id=\"payment_form_terms\"></li><li><label for=\"pay_online_submit_button\"></label><button id=\"pay_online_submit_button\" type=\"button\" name=\"submit\" value=\"once_off\">Pay Now</button></li><li><label for=\"payment_form_direct_debit_submit\"></label><button name=\"submit\" id=\"payment_form_direct_debit_submit\" value=\"direct_debit\" type=\"submit\">Confirm</button></li>\n<li><label for=\"payment_form_postal_submit\"></label><a href=\"/postal-donation.html\" id=\"payment_form_postal_submit\">Postal Information</a></li>'
WHERE `form_name` = 'PaymentFormQuickOrder';

UPDATE IGNORE `plugin_media_shared_media_photo_presets`
SET
`directory`     = 'gallery',
`height_large`  = '800',
`width_large`   = '1200',
`action_large`  = 'fith',
`thumb`         = '1',
`height_thumb`  = '200',
`width_thumb`   = '300',
`action_thumb`  = 'fith',
`date_modified` = CURRENT_TIMESTAMP()
WHERE `title`   = 'Gallery';
