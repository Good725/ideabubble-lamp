/*
ts:2017-04-05 13:00:00
*/

UPDATE `plugin_pages_pages` SET `publish` = 0 WHERE `name_tag` IN ('products', 'products.html');

INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `layout_id`, `category_id`, `content`) VALUES
(
  'products',
  'Products',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'product_page'),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1),
  '<div class=\"fix-container\">
\n    <h2 style=\"text-align: center;\">Our Products</h2>
\n
\n    <p style=\"text-align: center;\">Working with our cross-functional teams in an agile environment guarantees a<br />higher success rate, greater value, reduced cost and accelerated product to market.</p>
\n
\n    <section class=\"ib-product\">
\n        <div class=\"ib-product-description\">
\n            <h2><span class=\"icon_puzzle\"></span> CONTENT</h2>
\n
\n            <p>Our content-management platform for fast and simple website editing</p>
\n        </div>
\n
\n        <div class=\"ib-product-image\">
\n            <img src=\"/assets/ideabubble/images/content-screenshot.png\" alt=\"\" />
\n        </div>
\n    </section>
\n
\n    <hr />
\n
\n    <section class=\"ib-product\">
\n        <div class=\"ib-product-image\">
\n            <img src=\"/assets/ideabubble/images/shop-screen.png\" alt=\"\" />
\n        </div>
\n
\n        <div class=\"ib-product-description\">
\n            <h2><span class=\"icon_cart_alt\"></span> SHOP</h2>
\n
\n            <p>Our e commerce platform for selling products and providing a wide range of payment options. We successfully launched many shops. We support wide range of payment</p>
\n        </div>
\n    </section>
\n
\n    <hr />
\n
\n    <section class=\"ib-product\">
\n        <div class=\"ib-product-description\">
\n            <h2><span class=\"icon_compass_alt\"></span> CONTACT</h2>
\n
\n            <p>Our CRM (Customer Relationship Management) product is seamless. Communicate with all your stakeholders, through whatever medium you prefer.</p>
\n        </div>
\n
\n        <div class=\"ib-product-image\">
\n            <img src=\"/assets/ideabubble/images/contact-screen.png\" alt=\"\" />
\n        </div>
\n    </section>
\n
\n    <hr />
\n
\n    <section class=\"ib-product\">
\n        <div class=\"ib-product-image\">
\n            <img src=\"/assets/ideabubble/images/educate-screen.png\" alt=\"\" />
\n        </div>
\n
\n        <div class=\"ib-product-description\">
\n            <h2><span class=\"icon_book_alt\"></span> EDUCO</h2>
\n
\n            <p>Educo digitises schools of all types, from grind schools to ballet schools. It offers 360 degree digital solutions, from class booking, to class payment to teacher management.
\n            </p>
\n        </div>
\n    </section>
\n
\n    <hr />
\n
\n    <section class=\"ib-product\">
\n        <div class=\"ib-product-description\">
\n            <h2><span class=\"icon_headphones\"></span> TICKET</h2>
\n
\n            <p>Designed to offer a better solution for event management than everything else in the market.</p>
\n        </div>
\n
\n        <div class=\"ib-product-image\">
\n            <img src=\"/assets/ideabubble/images/ticket-screen.png\" alt=\"\" />
\n        </div>
\n    </section>
\n</div>
\n
\n<div style=\"margin: auto; max-width: 1000px;text-align: center;\">
\n    <h2>Process</h2>
\n
\n    <p>We combine design thinking with our platform to create a cohesive cross-functional team to research, design, implement and validate results with actual customers.</p>
\n</div>'
);

/* Panels */
INSERT INTO `plugin_panels` (`title`, `position`, `order_no`, `type_id`, `text`, `link_id`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
(
  'Strategy',
  'content_content',
  '1',
  (SELECT `id` FROM `plugin_panels_types` WHERE `name` = 'static' AND `deleted` = 0 LIMIT 1),
  '<div class=\"panel-icon\"><span class=\"flaticon-search-page\"></span></div>
\n<h3>01.<br /><strong>STRATEGY</strong></h3>
\n<hr />
\n<p>We plan your</p>',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
),
(
  'Prototype',
  'content_content',
  '2',
  (SELECT `id` FROM `plugin_panels_types` WHERE `name` = 'static' AND `deleted` = 0 LIMIT 1),
  '<div class=\"panel-icon\"><span class=\"flaticon-paint\"></span></div>
\n<h3>02.<br /><strong>PROTOTYPE</strong></h3>
\n<hr />
\n<p>Lorem ipsum</p>',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
),
(
  'Test',
  'content_content',
  '3',
  (SELECT `id` FROM `plugin_panels_types` WHERE `name` = 'static' AND `deleted` = 0 LIMIT 1),
  '<div class=\"panel-icon\"><span class=\"flaticon-settings\"></span></div>
\n<h3>03.<br /><strong>TEST</strong></h3>
\n<hr />
\n<p>Lorem ipsum</p>',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
),
(
  'Code',
  'content_content',
  '4',
  (SELECT `id` FROM `plugin_panels_types` WHERE `name` = 'static' AND `deleted` = 0 LIMIT 1),
  '<div class=\"panel-icon\"><span class=\"flaticon-command-prompt\"></span></div>
\n<h3>04.<br /><strong>CODE</strong></h3>
\n<hr />
\n<p>Lorem ipsum</p>',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
),
(
  'Launch',
  'content_content',
  '5',
  (SELECT `id` FROM `plugin_panels_types` WHERE `name` = 'static' AND `deleted` = 0 LIMIT 1),
  '<div class=\"panel-icon\"><span class=\"flaticon-rocket-ship\"></span></div>
\n<h3>05.<br /><strong>LAUNCH</strong></h3>
\n<hr />
\n<p>Lorem ipsum</p>',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
);

UPDATE `plugin_pages_pages`
SET `footer` = '<div class=\"full-row let-talk-bg\">
\n    <div class=\"fix-container\">
\n        <h2>LET&#39;S TALK</h2>
\n        <a class=\"btn-primary inverse\" href=\"contactus\">contact us</a>
\n    </div>
\n</div>'
WHERE `name_tag` IN ('products', 'aboutus', 'home', 'home.html');


UPDATE `plugin_pages_pages`
SET
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' LIMIT 1),
  `content`       = "<section class=\"full-row home\-\-banner\">
\n    <div class=\"fix-container\">
\n        <div class=\"page-caption\">
\n            <h2>We enable our clients to<br /><span class=\"light-blue-txt\">accelerate results</span></h2>
\n
\n            <hr />
\n
\n            <p>We turn business ideas into extraordinary digital products. You Dream it, We Build it.</p>
\n        </div>
\n    </div>
\n</section>
\n
\n<section class=\"about\-\-section full-row\">
\n    <div class=\"rotate-img\">&nbsp;</div>
\n
\n    <div class=\"fix-container\">
\n        <div class=\"theme-heading\">
\n            <h2>About Us</h2>
\n        </div>
\n
\n        <div class=\"section\-\-about_us\">
\n            <p>At Idea Bubble we are passionate about developing cutting edge solutions. We opened our doors in 2008 and over the past number of years have grown from strength to strength. Now, having launched hundreds of websites and developed many PHP development solutions, we offer an abundance of experience and a passion for creating all things technical!</p>
\n        </div>
\n
\n        <div class=\"user-feedback\">
\n            <blockquote class=\"comment-txt\">Quite simply, we love code!</blockquote>
\n
\n            <div class=\"user-name\">Michael O&#39;Callaghan, MD</div>
\n        </div>
\n    </div>
\n
\n    <div class=\"full-row\">
\n        <ul class=\"grid\-\-5 grid-view slanted_menu\" style=\"color: #00385d;\">
\n            <li>
\n                <div class=\"head-bar\"><img src=\"/assets/ideabubble/images/visitors-icon.png\" alt=\"\" /> <span>3.2 Mill</span></div>
\n                <p>visitors</p>
\n            </li>
\n
\n            <li>
\n                <div class=\"head-bar\"><img src=\"/assets/ideabubble/images/value-saved-icon.png\" alt=\"Euro\" /> <span>1.2 Mill</span></div>
\n                <p>value saved</p>
\n            </li>
\n
\n            <li>
\n                <div class=\"head-bar\"><img src=\"/assets/ideabubble/images/integrations-icon.png\" /> <span>58</span></div>
\n                <p>integrations</p>
\n            </li>
\n
\n            <li>
\n                <div class=\"head-bar\"><img src=\"/assets/ideabubble/images/solutions-icon.png\" /> <span>15</span></div>
\n                <p>solutions</p>
\n            </li>
\n
\n            <li>
\n                <div class=\"head-bar\"><img src=\"/assets/ideabubble/images/service-icon.png\" /> <span>12</span></div>
\n                <p>servers</p>
\n            </li>
\n        </ul>
\n    </div>
\n</section>
\n
\n<section class=\"full-row strength-sect\">
\n    <div class=\"rotate-img\">&nbsp;</div>
\n
\n    <div class=\"fix-container\">
\n        <div class=\"left-sect\">
\n            <div class=\"theme-heading\">
\n                <h2>Our Strengths</h2>
\n            </div>
\n
\n            <p>Over the past 10 years, we have learned a lot about what makes a great site. Combining form and function, to deliver end to end digital solutions has become our passion. Let us help you create the perfect digital solution for your business, from website to accounting dashboards, to event management. We design custom digital projects like, fit for your business, like no one else.</p>
\n        </div>
\n
\n        <div class=\"right-sect\">
\n            <ul class=\"number-list\">
\n                <li>Innovative</li>
\n                <li>passionate</li>
\n                <li>Visionary</li>
\n                <li>Experts</li>
\n                <li>Value Driven</li>
\n            </ul>
\n        </div>
\n    </div>
\n</section>
\n
\n<div class=\"full-row our\-\-partners\">
\n    <div class=\"theme-heading\">
\n        <h2>Our Partners</h2>
\n    </div>
\n
\n    <p>{testimonialsfeed-Testimonials}</p>
\n</div>"
WHERE `name_tag` IN ('home', 'home.html');