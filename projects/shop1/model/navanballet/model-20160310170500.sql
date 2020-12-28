/*
ts:2016-03-10 17:05:00
*/

INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `options`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
  'PaymentFormQuickOrder',
  'frontend/formprocessor/',
  'POST',
  '<input type=\"hidden\" name=\"item_name\" value=\"Quick Order\"><li><label for=\"payment_total\">Amount (&euro;)</label><input type=\"text\" name=\"payment_total\" class=\"validate[required]\" id=\"payment_total\"></li><li><fieldset id=\"payment_form_contact_details_fieldset\"><legend>Your details</legend><ul><li style=\"\"><label for=\"payment_form_name\">Name</label><input type=\"text\" name=\"name\" class=\"validate[required]\" id=\"payment_form_name\"></li><li style=\"\"><label for=\"email\">Email</label><input type=\"text\" name=\"email\" class=\"validate[required]\" id=\"email\"></li><li style=\"\"><label for=\"\">Phone</label><input type=\"text\" name=\"phone\"></li><li style=\"\"><label for=\"address\">Address</label><textarea name=\"address\" id=\"address\"></textarea></li></ul></fieldset></li><li><fieldset id=\"payment_form_payment_select_fieldset\"><legend>Select class</legend><ul><li>      <label for=\"payment_form_day\">Day</label>   <select name=\"day\" class=\"validate[required]\" id=\"payment_form_day\"><option value=\"\">Please Select</option>          <option value=\"Tuesday\">Tuesday</option>     <option value=\"Wednesday\">Wednesday</option>     <option value=\"Saturday\">Saturday</option>   </select> </li> <li class=\"paymentform-time-li\">      <label for=\"payment_form_times_tuesday\">Time</label>   <select name=\"tuesday_time\" class=\"validate[required]\" id=\"payment_form_times_tuesday\" disabled=\"\"><option value=\"\">Please Select</option>          <option value=\"15:00\">3:00 pm</option>     <option value=\"15:30\">3:30 pm</option>     <option value=\"16:15\">4:15 pm</option>     <option value=\"17:00\">5:00 pm</option>     <option value=\"18:00\">6:00 pm</option>   </select> </li> <li class=\"paymentform-time-li\">      <label for=\"payment_form_times_wednesday\">Time</label>   <select name=\"wednesday_time\" class=\"validate[required]\" id=\"payment_form_times_wednesday\" disabled=\"\"><option value=\"\">Please Select</option>          <option value=\"15:30\">3:30 pm</option>     <option value=\"16:15\">4:15 pm</option>     <option value=\"17:00\">5:00 pm</option>     <option value=\"17:45\">5:45 pm</option>   </select> </li> <li class=\"paymentform-time-li\">      <label for=\"payment_form_times_saturday\">Time</label>   <select name=\"saturday_time\" class=\"validate[required]\" id=\"payment_form_times_saturday\" disabled=\"\"><option value=\"\">Please Select</option>          <option value=\"09:15\">9:15 am</option>     <option value=\"09:45\">9:45 am</option>     <option value=\"10:30\">10:30 am</option>     <option value=\"12:15\">12:15 pm</option>     <option value=\"13:00\">1:00 pm</option>     <option value=\"13:45\">1:45 pm</option>     <option value=\"14:45\">2:45 pm</option>     <option value=\"15:45\">3:45 pm</option>   </select> </li></ul></fieldset></li><li><fieldset id=\"payment_form_cc_payment_fieldset\"><legend>Credit Card Payment</legend><ul><li><label for=\"payment_form_ccType\">Card Type</label><select name=\"ccType\" class=\"validate[required]\" id=\"ccType\"><option value=\"\">Please Select</option><option value=\"visa\">Visa</option><option value=\"mc\">MasterCard</option><option value=\"laser\">Laser</option></select></li><li><label for=\"payment_form_ccName\">Name on Card</label><input type=\"text\" name=\"ccName\" id=\"ccName\" class=\"validate[required]\" autocomplete=\"off\"></li><li><label for=\"payment_form_ccNum\">Card Number</label><input type=\"text\" name=\"ccNum\" id=\"ccNum\" class=\"validate[required]\" autocomplete=\"off\"></li><li><label for=\"payment_form_ccv\">CCV Number</label><input type=\"text\" name=\"ccv\" id=\"ccv\" class=\"validate[required]\" autocomplete=\"off\"></li> <li><label for=\"payment_form_ccExpMM\">Expiry Month</label><select name=\"ccExpMM\" id=\"ccExpMM\" class=\"validate[required]\"><option value=\"\">Select Month</option><option value=\"01\">01</option><option value=\"02\">02</option><option value=\"03\">03</option><option value=\"04\">04</option><option value=\"05\">05</option><option value=\"06\">06</option><option value=\"07\">07</option><option value=\"08\">08</option><option value=\"09\">09</option><option value=\"10\">10</option><option value=\"11\">11</option><option value=\"12\">12</option></select></li><li><label for=\"payment_form_ccExpMM\">Expiry Year</label><select name=\"ccExpYY\" id=\"ccExpYY\" class=\"validate[required]\"><option value=\"\">Select Year</option><option value=\"16\">2016</option><option value=\"17\">2017</option><option value=\"18\">2018</option><option value=\"19\">2019</option><option value=\"20\">2020</option><option value=\"21\">2021</option><option value=\"22\">2022</option><option value=\"23\">2023</option><option value=\"24\">2024</option><option value=\"25\">2025</option><option value=\"26\">2026</option></select></li></ul></fieldset></li><li><label for=\"payment_form_terms\">I have read and agree to the <a href=\"/terms-and-conditions.html\">terms and conditions</a></label><input type=\"checkbox\" name=\"terms\" class=\"validate[required]\" id=\"payment_form_terms\"></li><li><label for=\"pay_online_submit_button\"></label><button id=\"pay_online_submit_button\" type=\"submit\">Pay Now</button> <div id=\"error_message_area\"></div></li>',
  'redirect:|failpage:',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '0',
  '0',
  'payment_form'
);

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'pay-online.html',
  'Pay Online',
  '<div class=\"formrt\">{form-PaymentFormQuickOrder}</div> ',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
);