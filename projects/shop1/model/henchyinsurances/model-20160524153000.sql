/*
ts:2016-05-24 15:30:00
*/

INSERT INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `options`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES (
  'LifeAssuranceQuote',
  'frontend/formprocessor/',
  'POST',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\"><input name=\"event\" value=\"contact-form\" type=\"hidden\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\"><input name=\"form_type\" value=\"Contact Form\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" value=\"contactformmail\"><li><label for=\"life-assurance-form-type_of_cover\">Type of Cover</label><label>   <input type=\"radio\" name=\"type_of_cover\" value=\"Single\" class=\"validate[required]\" id=\"life-assurance-form-type_of_cover-single\"> Single </label> <label>   <input type=\"radio\" name=\"type_of_cover\" value=\"Joint\" class=\"validate[required]\" id=\"life-assurance-form-type_of_cover-joint\"> Joint </label> <label>   <input type=\"radio\" name=\"type_of_cover\" value=\"Dual\" class=\"validate[required]\" id=\"life-assurance-form-type_of_cover-dual\"> Dual </label></li> <li><fieldset id=\"life-assurance-fieldset-life_1\"><legend>Life 1</legend><ul><li><label for=\"life-assurance-form-life_1_dob\">Date of Birth</label><input type=\"text\" class=\"datepicker validate[required]\" name=\"life_1_date_of_birth\" id=\"life-assurance-form-life_1_dob\"></li><li><label for=\"life-assurance-form-life1_smoker\">Smoker</label><label>   <input type=\"radio\" class=\"validate[required]\" name=\"life_1_smoker\" id=\"life-assurance-form-life1_smoker-yes\" value=\"Yes\"> Yes </label> <label>   <input type=\"radio\" class=\"validate[required]\" name=\"life_1_smoker\" id=\"life-assurance-form-life1_smoker-no\" value=\"No\"> No </label> </li></ul></fieldset></li><li><fieldset id=\"life-assurance-fieldset-life_2\"><legend>Life 2</legend><ul><li><label for=\"life-assurance-form-life_2_dob\">Date of Birth</label><input type=\"text\" class=\"datepicker validate[required]\" name=\"life_2_date_of_birth\" id=\"life-assurance-form-life_2_dob\"></li><li><label for=\"life-assurance-form-life1_smoker\">Smoker</label><label>   <input type=\"radio\" class=\"validate[required]\" name=\"life_2_smoker\" id=\"life-assurance-form-life_2_smoker-yes\" value=\"Yes\"> Yes </label> <label>   <input type=\"radio\" class=\"validate[required]\" name=\"life_2_smoker\" id=\"life-assurance-form-life1_smoker-no\" value=\"No\"> No </label> </li></ul></fieldset></li><li><label for=\"life-assurance-form-sum_assured\">Sum Assured (€)</label><input type=\"text\" class=\"validate[required]\" name=\"sum_assured\" id=\"life-assurance-form-sum_assured\"></li><li><label for=\"life-assurance-form-term\">Term – In Years</label><input type=\"number\" class=\"validate[required]\" name=\"term\" id=\"life-assurance-form-term\"></li> <li><label for=\"life-assurance-form-serious_illness\">Serious Ilness</label><label>   <input type=\"radio\" class=\"validate[required]\" name=\"serious_illness\" id=\"life-assurance-form-serious_illness-yes\" value=\"Yes\"> Yes </label> <label>   <input type=\"radio\" class=\"validate[required]\" name=\"serious_illness\" id=\"life-assurance-form-serious_illness-no\" value=\"No\"> No </label> </li><li><label for=\"life-assurance-form-first_name\">First Name</label><input type=\"text\" class=\"validate[required]\" name=\"first_name\" id=\"life-assurance-form-first_name\"></li><li><label for=\"life-assurance-form-last_name\">Last Name</label><input type=\"text\" class=\"validate[required]\" name=\"last_name\" id=\"life-assurance-form-last_name\"></li><li><label for=\"life-assurance-form-telephone\">Telephone</label><input type=\"text\" name=\"telephone\" id=\"life-assurance-form-telephone\"></li><li><label for=\"life-assurance-form-email\">Email</label><input type=\"text\" class=\"validate[required,custom[email]]\" name=\"email\" id=\"life-assurance-form-email\"></li><li><label></label><button type=\"submit\">Submit</button></li>',
  'redirect:|failpage:',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '0',
  '0',
  'LifeAssuranceQuote'
);

INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `options`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES (
  'MotorQuoteRequest',
  'frontend/formprocessor/',
  'POST',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\"><input name=\"event\" value=\"contact-form\" type=\"hidden\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\"><input name=\"form_type\" value=\"Contact Form\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" value=\"contactformmail\"><li><label for=\"motor-quote-form-name\" class=\"mandatory-label\">Your Name:</label><input id=\"motor-quote-form-name\" name=\"name\" type=\"text\" class=\"validate[required]\"></li><li><label for=\"motor-quote-form-email\" class=\"mandatory-label\">Your Email:</label><input id=\"motor-quote-form-email\" name=\"email\" type=\"text\" class=\"validate[required,custom[email]]\"></li><li><label for=\"motor-quote-form-phone\" class=\"mandatory-label\">Your Phone No.:</label><input id=\"motor-quote-form-phone\" name=\"phone\" type=\"text\" class=\"validate[required]\"></li><li><label for=\"motor-quote-form-address\">Your Address:</label><textarea id=\"motor-quote-form-address\" name=\"address\"></textarea></li><li><label for=\"motor-quote-form-renewal_date\" class=\"mandatory-label\">Renewal Date:</label><input id=\"motor-quote-form-renewal_date\" name=\"renewal_date\" class=\"datepicker validate[required]\" type=\"text\"></li><li><label for=\"motor-quote-form-preferred_contact_method\">Preferred Contact Method:</label><select id=\"motor-quote-form-preferred_contact_method\" name=\"preferred_contact_method\">   <option value=\"\">Please Select</option>   <option value=\"Email\">Email</option>   <option value=\"Phone\">Phone</option> </select></li><li><label for=\"motor-quote-form-preferred_time_or_date\">Preferred Time or Date:</label><input id=\"motor-quote-form-preferred_time_or_date\" name=\"preferred_time_or_date\" class=\"datetimepicker\" type=\"text\"></li><li>      <p>To help us prepare in advance, please advise:</p> </li><li><label for=\"motor-quote-form-group_name\" class=\"mandatory-label\">Name of Group:</label><input id=\"motor-quote-form-group_name\" name=\"group_name\" type=\"text\" class=\"validate[required]\"></li><li><label for=\"motor-quote-form-age\" class=\"mandatory-label\">Age:</label><input id=\"motor-quote-form-age\" name=\"age\" type=\"number\" class=\"validate[required]\"></li><li><label for=\"motor-quote-form-licence_type\" class=\"mandatory-label\">Licence Type:</label><input id=\"motor-quote-form-licence_type\" name=\"licence_type\" type=\"text\" class=\"validate[required]\"></li><li><label for=\"motor-quote-form-years_no_claims_bonus\" class=\"mandatory-label\">Years No Claims Bonus:</label><input id=\"motor-quote-form-years_no_claims_bonus\" name=\"years_no_claims_bonus\" type=\"number\" class=\"validate[required]\"></li><li><label for=\"motor-quote-form-car_registration_number\">Car Reg. No.:</label><input id=\"motor-quote-form-car_registration_number\" name=\"car_registration_number\" type=\"text\"></li><li><label for=\"motor-quote-form-current_premium\">Current Premium:</label><input id=\"motor-quote-form-current_premium\" name=\"current_premium\" type=\"text\"></li><li><label for=\"motor-quote-form-current_renewal_date\">Current Renewal Date:</label><input id=\"motor-quote-form-current_renewal_date\" name=\"current_renewal_date\" class=\"datepicker\" type=\"text\"></li><li><label for=\"motor-quote-form-car_specifications\">Make/Model/Engine Size:</label><textarea id=\"motor-quote-form-car_specifications\" name=\"car_specifications\"></textarea></li> <li>      <label></label>   <button type=\"submit\">Send Quote Request</button> </li>',
  'redirect:|failpage:',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '0',
  '0',
  'MotorQuoteRequest'
);

INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `options`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES (
  'HomeInsuranceRequest',
  'frontend/formprocessor/',
  'POST',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\"><input name=\"event\" value=\"contact-form\" type=\"hidden\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\"><input name=\"form_type\" value=\"Contact Form\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" value=\"contactformmail\"><li><label for=\"home-insurance-form-name\" class=\"mandatory-label\">Your Name:</label><input id=\"home-insurance-form-name\" name=\"name\" type=\"text\" class=\"validate[required]\"></li><li><label for=\"home-insurance-form-email\" class=\"mandatory-label\">Your Email:</label><input id=\"home-insurance-form-email\" name=\"email\" type=\"text\" class=\"validate[required]\"></li><li><label for=\"home-insurance-form-phone\" class=\"mandatory-label\">Your Phone No.:</label><input id=\"home-insurance-form-phone\" name=\"phone\" type=\"text\" class=\"validate[required]\"></li><li><label for=\"home-insurance-form-renewal_date\" class=\"mandatory-label\">Renewal Date:</label><input id=\"home-insurance-form-renewal_date\" name=\"renewal_date\" class=\"datepicker validate[required]\" type=\"text\"></li> <li><label for=\"home-insurance-form-preferred_contact_method\">Preferred Contact Method:</label><select id=\"home-insurance-form-contact_method\" name=\"contact_method\">   <option value=\"\">Please Select</option>   <option value=\"1\">Email</option>   <option value=\"0\">Phone</option> </select></li> <li><label for=\"home-insurance-form-preferred_time_or_date\">Preferred Time or Date:</label><input type=\"text\" id=\"home-insurance-form-preferred_time_or_date\" name=\"preferred_time_or_date\"></li><li><p>To help us prepare in advance, please advise:</p></li><li><label for=\"home-insurance-form-house_address\" class=\"mandatory-label\">House Address:</label><textarea id=\"home-insurance-form-house_address\" name=\"house_address\" class=\"validate[required]\"></textarea><span>(tip give full address as often further discounts can be given)</span></li> <li><label for=\"home-insurance-form-buildings_sum_insured\" class=\"mandatory-label\">Buildings Sum Insured:</label><input id=\"home-insurance-form-buildings_sum_insured\" name=\"buildings_sum_insured\" type=\"text\" class=\"validate[required]\"></li> <li><label for=\"home-insurance-form-contents_sum_insured\" class=\"mandatory-label\">Contents Sum Insured:</label><input id=\"home-insurance-form-contents_sum_insured\" name=\"contents_sum_insured\" type=\"text\" class=\"validate[required]\"></li><li><label for=\"home-insurance-form-other_items\" class=\"mandatory-label\">Any other items e.g. jewellery (€):</label><input id=\"home-insurance-form-other_items\" name=\"other_items\" type=\"text\" class=\"validate[required]\"></li><li><label for=\"home-insurance-form-claims\">Details of any claims in last 5 years:</label><textarea id=\"home-insurance-form-claims\" name=\"claims\"></textarea></li><li><label for=\"submit\"></label><button type=\"submit\">Send Home Insurance Request</button></li>',
  'redirect:|failpage:',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '0',
  '0',
  'HomeInsuranceRequest'
);

INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `options`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
  'BusinessInsuranceEnquiry',
  'frontend/formprocessor/',
  'POST',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\"><input name=\"event\" value=\"contact-form\" type=\"hidden\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\"><input name=\"form_type\" value=\"Contact Form\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" value=\"contactformmail\"><li><label for=\"business-equniry-form-name\">Your Name</label><input id=\"business-equniry-form-name\" name=\"name\" type=\"text\"></li><li><label for=\"business-equniry-form-company_name\">Company Name</label><input id=\"business-equniry-form-company_name\" name=\"company_name\" type=\"text\"></li><li><label for=\"business-equniry-form-email\" class=\"mandatory-label\">Email</label><input id=\"business-equniry-form-email\" name=\"email\" type=\"text\" class=\"validate[required,custom[email]]\"></li><li><label for=\"business-equniry-form-phone\" class=\"mandatory-label\">Phone Number</label><input id=\"business-equniry-form-phone\" name=\"phone\" type=\"text\" class=\"validate[required]\"></li><li><label for=\"business-equniry-form-message\">Message</label><textarea id=\"business-equniry-form-message\" name=\"message\"></textarea></li><li><label for=\"business-equniry-form-call_regarding\">Call is Regarding (please state type of business insurance)</label><select id=\"business-equniry-form-call_regarding\" name=\"call_regarding\">   <option value=\"\">Please Select</option>   <option value=\"Comany Insurance\">Company Insurance</option>   <option value=\"Insurance Fraud\">Insurance Fraud</option>   <option value=\"Health Insurance\">Health Insurance</option>   <option value=\"Employee Insurance\">Employee Insurance</option>   <option value=\"Life Insurance\">Life Insurance</option> </select></li><li><label for=\"business-enquiry-form-preferred_time_to_call\">Preferred Time to Call</label><input id=\"business-enquiry-form-preferred_time_to_call\" name=\"business-enquiry-form-preferred_time_to_call\" type=\"text\"></li><li><label></label><button type=\"submit\">Submit</button></li>',
  'redirect:|failpage:',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '0',
  '0',
  'BusinessInsuranceEnquiry'
);

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'home-insurance-request',
  'Home Insurance Request',
  '<h1>Home Insurance Request</h1>  <div class=\"formrt\">{form-HomeInsuranceRequest}</div>   <p>&nbsp;</p>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
),
(
  'business-insurance-enquiry',
  'Business Insurance Enquiry',
  '<h1>Business Insurance Enquiry</h1>  <div class=\"formrt\">{form-BusinessInsuranceEnquiry}</div>   <p>&nbsp;</p>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
),
(
  'motor-quote-request',
  'Motor Quote Request',
  '<h1>Motor Quote Request</h1>  <div class=\"formrt\">{form-MotorQuoteRequest}</div>   <p>&nbsp;</p>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
),
(
  'life-assurance-quote',
  'Life Assurance Quote',
  '<h1>Life Assurance Quote</h1>  <div class=\"formrt\">{form-LifeAssuranceQuote}</div>   <p>&nbsp;</p>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
);
