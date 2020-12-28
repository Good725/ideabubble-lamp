/*
ts:2019-01-10 14:45:00
*/
INSERT INTO
  `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `options`, `publish`, `date_modified`, `summary`, `email_all_fields`, `captcha_version`, `use_stripe`, `form_id`)
VALUES (
  'Newsletter subscription',
  'frontend/formprocessor/',
  'POST',
  '
  <input type=\"hidden\" name=\"subject\"         value=\"Newsletter Sign Up\">
  <input type=\"hidden\" name=\"business_name\"   value=\"CourseCo\" />
  <input type=\"hidden\" name=\"redirect\"        value=\"thank-you-subscribing.html\" />
  <input type=\"hidden\" name=\"event\"           value=\"subscribe-to-newsletter\" />
  <input type=\"hidden\" name=\"trigger\"         value=\"subscribe\" />
  <input type=\"hidden\" name=\"form_type\"       value=\"Newsletter Form\" />
  <input type=\"hidden\" name=\"form_identifier\" value=\"newsletter_\" />
  <input type=\"hidden\" name=\"email_template\"  value=\"subscribeformmail\" />
  <li>
    <label for=\"newsletter_form_name\">Full name*</label>
    <input type=\"text\" name=\"newsletter_form_name\" class=\"validate[required]\" id=\"newsletter_form_name\" />
  </li>
  <li>
    <label for=\"newsletter_form_email_address\">Email address*</label>
    <input type=\"text\" name=\"newsletter_form_email_address\" class=\"validate[required]\" id=\"newsletter_form_email_address\" />
  </li>
  <li>
    <label></label>
    <button type=\"submit\" class=\"button inverse\">SUBSCRIBE</button>
  </li>',
  'redirect:|failpage:',
  '1',
  CURRENT_TIMESTAMP,
  '',
  '0',
  '2',
  '0',
  'newsletter_subscription'
);