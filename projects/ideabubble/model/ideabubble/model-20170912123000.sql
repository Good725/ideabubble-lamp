/*
ts:2017-09-12 12:30:00
*/

/* Create the form */
INSERT INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `publish`, `date_created`, `date_modified`, `form_id`, `fields`) VALUES (
  'new_project_enquiry',
  'frontend/formprocessor/',
  'POST',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  'new_project_enquiry_form',
  '<input type=\"hidden\" name=\"subject\"  value=\"Start Your Project\" />
\n<input type=\"hidden\" name=\"redirect\" value=\"thank-you.html\" />
\n<input type=\"hidden\" name=\"event\"    value=\"new_project_enquiry\" />
\n<input type=\"hidden\" name=\"trigger\"  value=\"new_project_enquiry\" />
\n<li>
\n    <label for=\"campaign_form_company\">Your company (optional)</label>
\n    <input type=\"text\" name=\"company\" id=\"campaign_form_company\" />
\n</li>
\n<li>
\n    <label for=\"campaign_form_name\">Your name</label>
\n    <input type=\"text\" name=\"name\" id=\"campaign_form_name\" class=\"validate[required]\" />
\n</li>
\n<li>
\n    <label for=\"campaign_form_email\">Your email</label>
\n    <input type=\"text\" name=\"email\" id=\"campaign_form_email\" class=\"validate[required]\" />
\n</li>
\n<li>
\n    <label for=\"campaign_form_phone\">Your phone (optional)</label>
\n    <input type=\"text\" name=\"phone\" id=\"campaign_form_phone\" />
\n</li>
\n<li>
\n    <label for=\"campaign_form_interested_in\">Interested in (optional)</label>
\n    <select name=\"interested_in\" id=\"campaign_form_interested_in\">
\n        <option name=\"\">Please Select</option>
\n        <option name=\"UX Design\">UX Design</option>
\n        <option name=\"UX Design\">Web Design</option>
\n        <option name=\"UX Design\">eCommerce</option>
\n        <option value=\"Mobile Applications\">Mobile Applications</option>
\n        <option value=\"Web Applications\">Web Applications</option>
\n        <option value=\"Software Applications\">Software Applications</option>
\n    </select>
\n</li>
\n<li>
\n    <label for=\"campaign_form_project_description\">Project description (optional)</label>
\n    <textarea name=\"project_description\" id=\"campaign_form_project_description\"></textarea></li>
\n<li>
\n    <label for=\"campaign_form_budget\">Budget (optional)</label>
\n    <select name=\"budget\" id=\"campaign_form_budget\">
\n        <option value=\"\">Please Select</option>
\n        <option value=\"€5k - €10k\">€5k - €10k</option>
\n        <option value=\"€10k - €25k\">€10k - €25k</option>
\n        <option value=\"€25k - €50k\">€25k - €50k</option>
\n        <option value=\"€50k +\">€50k +</option>
\n    </select>
\n</li>
\n<li>
\n    <label for=\"campaign_form_submit\"></label>
\n    <button id=\"campaign_form_submit\" type=\"submit\">Send Your Request</button>
\n</li>'
);

/* Create the message templates */
INSERT INTO `plugin_messaging_notification_templates`
(`name`, `driver`, `type_id`, `subject`, `sender`, `message`, `date_created`, `created_by`, `date_updated`, `publish`, `deleted`) VALUES
(
  'new_project_enquiry_admin',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
  'New project enquiry',
  'noreply@ideabubble.ie',
  '<p>A new submission has been made from the campaign form.</p>
\n<p>Company:  $company</p> <p>Name: $name</p>
\n<p>Email: $email</p> <p>Phone: $phone</p>
\n<p>Interested in: $interested_in</p>
\n<p>Project description: $project_description</p>
\np>Budget: $budget</p>',
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT INTO `plugin_messaging_notification_template_targets` (`template_id`, `target_type`, `target`, `x_details`, `date_created`) VALUES (
  (SELECT `id` FROM `plugin_messaging_notification_templates` WHERE `name` = 'new_project_enquiry_admin'),
  'EMAIL',
  'sales@ideabubble.ie',
  'to',
  CURRENT_TIMESTAMP
);

INSERT INTO `plugin_messaging_notification_templates` (`name`, `driver`, `type_id`, `subject`, `sender`, `message`, `date_created`, `created_by`, `date_updated`, `publish`, `deleted`) VALUES (
  'new_project_enquiry_customer',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
  'Thank you for contacting Idea Bubble',
  'sales@ideabubble.ie',
  '<p>Thank you for contacting Idea Bubble.</p>
\n<p>Details of your enquiry are listed below: </p>
\n<p>Company:  $company</p>
\n<p>Name: $name</p>
\n<p>Email: $email</p>
\n<p>Phone: $phone</p>
\n<p>Interested in: $interested_in</p>
\n<p>Project description: $project_description</p>
\n<p>Budget: $budget</p>',
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT INTO `plugin_messaging_notification_template_targets` (`template_id`, `target_type`, `target`, `x_details`, `date_created`) VALUES (
  (SELECT `id` FROM `plugin_messaging_notification_templates` WHERE `name` = 'new_project_enquiry_customer'),
  'POST_VAR',
  'email',
  'to',
  CURRENT_TIMESTAMP
);


/* Create the page */
INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`, `content`) VALUES (
  'your-website-platform',
  'Your Website Platform',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT IFNULL(`id`, 1) FROM `plugin_pages_layouts`   WHERE `layout`   = 'content' LIMIT 1),
  (SELECT IFNULL(`id`, 1) FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1),
  '<div class=\"fix-container theme-form\" style=\"max-width: 846px;\">
\n    <div style=\"text-align: center;\">
\n        <h1>The website platform made for you</h1>
\n
\n        <p style=\"font-size: 1.6em; margin: 1em;\">Whether you advertise online, on social media, in store or out of the trunk of your car, Idea Bubble has you covered.</p>
\n    </div>
\n
\n    <div class=\"input_columns\" style=\"margin-top: 3em;\">
\n        <div class=\"input_column\" style=\"flex: 2;\">
\n            <label class=\"sr-only\" for=\"campaign-name\">Name</label>
\n            <input type=\"text\" class=\"form-input validate[required]\" placeholder=\"Name*\" id=\"campaign-name\" />
\n        </div>
\n
\n        <div class=\"input_column\">
\n            <button type=\"button\" class=\"btn btn-primary\" data-toggle=\"ib-modal\" data-target=\"#modal-start_your_project\">Get Started</button>
\n        </div>
\n    </div>
\n
\n    <p><small>Try IBCMS free for 14 days. No risk and no credit card required.</small></p>
\n</div>
\n
\n<section class=\"our_products\">
\n    <div class=\"our_products-row\" style=\"margin-top: 0;\">
\n        <div class=\"our_products-item\" style=\"background-image: url(\'/assets/ideabubble/images/products/ailesbury-banner.png\');\">
\n            <div class=\"our_products-overlay our_products-overlay\-\-bottom_left\">
\n                <p style=\"font-size: 1.25em;\">Lorraine Lambert of Ailesbury Hair Clinic uses IBCMS to advertise her business online.</p>
\n            </div>
\n        </div>
\n    </div>
\n</section>
\n
\n<section class=\"full-row\">
\n    <div class=\"fix-container ib-campaign\">
\n        <div class=\"ib-campaign-description\">
\n            <h2>Have the budget but don&#39;t want hassle?</h2>
\n
\n            <p>Every project is possible with IBCMS with its flexible set of features, our superb aftercare and ongoing technical support we guarantee a lifetime of updates with or technical team at your fingertips.</p>
\n        </div>
\n
\n        <div class=\"ib-campaign-image\">
\n            <img src=\"/assets/ideabubble/images/campaign/no_hassle.png\" alt=\"Monthly updates, superb aftercare, problem solvers, Irish, great listeners, innovative, real people , wea care, affordable, fast delivery\" />
\n        </div>
\n    </div>
\n</section>
\n
\n<section class=\"full-row\">
\n    <div class=\"rotate-img\" style=\"background: #f8f8f8;\">&nbsp;</div>
\n
\n    <div class=\"fix-container ib-campaign\">
\n        <div class=\"ib-campaign-image\">
\n            <img src=\"/assets/ideabubble/images/campaign/what_we_deliver.png\" alt=\"\" />
\n        </div>
\n
\n        <div class=\"ib-campaign-description\">
\n            <h2>What we deliver</h2>
\n
\n            <p>Dedicate website leader who:</p>
\n
\n            <ul>
\n                <li>Gets your content online</li>
\n                <li>Uploads your images</li>
\n                <li>Reviews your SEO needs</li>
\n                <li>Sorts out your hosting</li>
\n                <li>Handles your site launch</li>
\n            </ul>
\n
\n        </div>
\n    </div>
\n</section>
\n
\n<section class=\"full-row\" style=\"min-height: 10em; padding: 3.25em 0;\">
\n    <div class=\"rotate-img\">&nbsp;</div>
\n
\n    <div class=\"fix-container theme-form\" style=\"text-align: center;\">
\n        <h2>Get started growing your business</h2>
\n        <p>&nbsp;</p>
\n
\n        <button type=\"button\" class=\"btn btn-primary\" data-toggle=\"ib-modal\" data-target=\"#modal-start_your_project\">Get Started</button>
\n    </div>
\n</section>
\n
\n<section class=\"full-row\" style=\"padding: 3.25em 0;\">
\n    <div class=\"rotate-img\" style=\"background: #f8f8f8;\">&nbsp;</div>
\n
\n    <div class=\"fix-container about\-\-section\" style=\"text-align: center;background:url(\'/assets/ideabubble/images/campaign/connect-map.png\') no-repeat top center;min-height: 22em;\">
\n        <h2>We power ambitious entrepreneurs all over the world</h2>
\n
\n        <ul class=\"grid-view\" style=\"margin: 5em auto 1em;max-width: 1200px;\">
\n            <li>
\n                <strong class=\"head-bar\"><span>58k+</span></strong>
\n                <p>Leads generated</p>
\n            </li>
\n
\n            <li>
\n                <strong class=\"head-bar\"><span>3.7k+</span></strong>
\n                <p>Active users</p>
\n            </li>
\n
\n            <li>
\n                <strong class=\"head-bar\"><span>&euro;7.2M+</span></strong>
\n                <p>Generated on IBCMS</p>
\n            </li>
\n        </ul>
\n
\n    </div>
\n</section>
\n
\n<div class=\"ib-modal\" id=\"modal-start_your_project\">
\n    <div class=\"ib-modal-dialog\">
\n        <button class=\"ib-modal-close\">&#x2715;</button>
\n
\n        <div class=\"ib-modal-content\">
\n            <h2>Start your project</h2>
\n
\n            <div class=\"form-vertical\">{form-new_project_enquiry}</div>
\n        </div>
\n    </div>
\n</div>
\n
\n<div class=\"full-row our\-\-partners\" style=\"background: #fff;\">
\n    <div class=\"rotate-img\">&nbsp;</div>
\n
\n    <div class=\"theme-heading\">
\n        <h2>Our Partners</h2>
\n    </div>
\n
\n    <p>{testimonialsfeed-Testimonials}</p>
\n</div>'
);


UPDATE `plugin_formbuilder_forms`
SET `fields` = '<input type=\"hidden\" name=\"subject\"  value=\"Start Your Project\" />
\n<input type=\"hidden\" name=\"redirect\" value=\"thank-you.html\" />
\n<input type=\"hidden\" name=\"event\"    value=\"new_project_enquiry\" />
\n<input type=\"hidden\" name=\"trigger\"  value=\"new_project_enquiry\" />
\n<li>
\n    <label for=\"campaign_form_company\">Your company (optional)</label>
\n    <input type=\"text\" name=\"company\" id=\"campaign_form_company\" />
\n</li>
\n<li>
\n    <label for=\"campaign_form_name\">Your name</label>
\n    <input type=\"text\" name=\"name\" id=\"campaign_form_name\" class=\"validate[required]\" />
\n</li>
\n<li>
\n    <label for=\"campaign_form_email\">Your email</label>
\n    <input type=\"text\" name=\"email\" id=\"campaign_form_email\" class=\"validate[required,custom[email]]\" />
\n</li>
\n<li>
\n    <label for=\"campaign_form_phone\">Your phone (optional)</label>
\n    <input type=\"text\" name=\"phone\" id=\"campaign_form_phone\" />
\n</li>
\n<li>
\n    <label for=\"campaign_form_interested_in\">Interested in (optional)</label>
\n    <select name=\"interested_in\" id=\"campaign_form_interested_in\">
\n        <option name=\"\">Please Select</option>
\n        <option name=\"UX Design\">UX Design</option>
\n        <option name=\"UX Design\">Web Design</option>
\n        <option name=\"UX Design\">eCommerce</option>
\n        <option value=\"Mobile Applications\">Mobile Applications</option>
\n        <option value=\"Web Applications\">Web Applications</option>
\n        <option value=\"Software Applications\">Software Applications</option>
\n    </select>
\n</li>
\n<li>
\n    <label for=\"campaign_form_project_description\">Project description (optional)</label>
\n    <textarea name=\"project_description\" id=\"campaign_form_project_description\"></textarea></li>
\n<li>
\n    <label for=\"campaign_form_budget\">Budget (optional)</label>
\n    <select name=\"budget\" id=\"campaign_form_budget\">
\n        <option value=\"\">Please Select</option>
\n        <option value=\"EUR 5k - EUR 10k\">&euro;5k - &euro;10k</option>
\n        <option value=\"EUR 10k - EUR 25k\">&euro;10k - &euro;25k</option>
\n        <option value=\"EUR 25k - EUR 50k\">&euro;25k - &euro;50k</option>
\n        <option value=\"EUR 50k +\">&euro;50k +</option>
\n    </select>
\n</li>
\n<li>
\n    <label for=\"campaign_form_submit\"></label>
\n    <button id=\"campaign_form_submit\" type=\"submit\">Send Your Request</button>
\n</li>'
WHERE `form_id` = 'new_project_enquiry_form';