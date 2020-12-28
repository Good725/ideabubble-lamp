/*
ts:2017-03-23 17:30:00
*/

-- Insert the "home" page, if it doesn't already exist
INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'home',
  'Home',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  `id`,
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_layouts`
WHERE `layout` = 'home'
AND NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('home.html', 'home') AND `deleted` = 0);

-- Update the home page to have the correct content
UPDATE
  `plugin_pages_pages`
SET
  `content`='<section class=\"full-row  pattern blue\">\r 	<div class=\"fix-container\">\r 		<div class=\"padd-top-bottom-80 key-features\">\r 			<div class=\"theme-heading white-txt\">\r 				<h2>Key Features</h2>\r 			</div>\r 			<ul class=\"grid-view grid-\-3\">\r 				<li>\r 					<h4>E-learning</h4>\r 					<figure class=\"imgbox\"><img src=\"/shared_media/ibeducate/media/photos/content/e-learning-img.jpg\"/></figure>\r 					<p>E-learning course design with Billing module, Virtual classroom and User management</p>\r 				</li>\r 				<li>\r 					<h4>Bulk Messaging</h4>\r 					<figure class=\"imgbox\"><img src=\"/shared_media/ibeducate/media/photos/content/bulk-messaging-img.jpg\"/></figure>\r 					<p>Send bulk SMS message quickly to all your teachers. Chat with your staff with instant messaging</p>\r 				</li>\r 				<li>\r 					<h4>Auto Accounting</h4>\r 					<figure class=\"imgbox\"><img src=\"/shared_media/ibeducate/media/photos/content/auto-accounting-img.jpg\"/></figure>\r 					<p>Payroll, Invoicing, Multy Course Packages, Family Deals, Coupon Codes, Buy 1 get 1 free</p>\r 				</li>\r 			</ul>\r 			<div class=\"aligncenter padd-top-70\">\r 				<a href=\"#\" class=\"theme-btn-white\">View More</a>\r 			</div>		\r 		</div>\r 	</div>\r </section>\r <div class=\"pattern gray clearfix\">\r 	<div class=\"center-logo\">\r 		<figure class=\"imgbox\">\r 			<img src=\"/shared_media/ibeducate/media/photos/content/center-logo-img.png\"/>\r 		</figure>\r 	</div>\r 	<div class=\"fr large-txt\">experts</div>\r </div>\r \r <section class=\"full-row why-choose\">\r 	<div class=\"gray-bg padd-top-bottom-50\">\r 		<div class=\"fix-container\">			\r 			<div class=\"theme-heading\">\r 				<h2>Why Choose Us?</h2>\r 			</div>\r 			<ul class=\"grid-view padd-bottom-50 grid-\-4 sonar_effect\">\r 				<li>\r 					<figure class=\"imgbox\"><img src=\"/shared_media/ibeducate/media/photos/content/flexible-icon.png\"/></figure>\r 					<h4>Flexible</h4>\r 					<p> Your Dream it, We Build It</p>\r 				</li>\r 				<li>\r 					<figure class=\"imgbox\"><img src=\"/shared_media/ibeducate/media/photos/content/experts-icon.png\"/></figure>\r 					<h4>Experts</h4>\r 					<p>Tried & Trusted Team</p>\r 				</li>\r 				<li>\r 					<figure class=\"imgbox\"><img src=\"/shared_media/ibeducate/media/photos/content/savings-icon.png\"/></figure>\r 					<h4>Savings</h4>\r 					<p>Long-term cost savings for you</p>\r 				</li>\r 				<li>\r 					<figure class=\"imgbox\"><img src=\"/shared_media/ibeducate/media/photos/content/established-icon.png\"/></figure>\r 					<h4>Established</h4>\r 					<p>Since 2007 we have<br/>been creating solutions</p> \r 				</li>\r 			</ul>\r 		</div>\r 		<div class=\"gray pattern\">\r 			<div class=\"fix-container\">\r 				<div class=\"aligncenter padd-bottom-50\">\r 					<a href=\"#\" class=\"theme-btn\">About us</a>\r 				</div>\r 			</div>	\r 		</div>\r 	</div>	\r </section>\r <div class=\"center-logo\">\r 	<figure class=\"imgbox\">\r 		<img src=\"/shared_media/ibeducate/media/photos/content/center-logo-img.png\"/>\r 	</figure>\r </div>\r <div class=\"full-row let-talk-bg\">\r 	<div class=\"fix-container\">\r 		<h3>want to hire us?</h3>\r 		<h2>let’s talk</h2>\r 		<a href=\"contactus\" class=\"theme-btn-white\">contact us</a>\r 	</div>\r </div>'
WHERE
  `name_tag` IN ('home', 'home.html');

-- Insert the "contactus" page, if it doesn't already exist
INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'contactus',
  'Get in Touch',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  `id`,
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_layouts`
WHERE `layout` = 'contactus'
AND NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('contactus.html', 'contactus') AND `deleted` = 0);

UPDATE
  `plugin_pages_pages`
SET
  `content` = '<address> <h2>Visit Us.</h2>  <p>Thomcor House,<br /> Mungret Street, Limerick, Ireland</p> </address>  <address> <h2>Contact Us.</h2>  <p>Tel: + 353 (0)61 513030<br /> Email: <a href=\"mailto:hello@ideabubble.ie\">hello@ideabubble.ie</a></p> </address>'
WHERE
  `name_tag` IN ('contactus', 'contactus.html');
