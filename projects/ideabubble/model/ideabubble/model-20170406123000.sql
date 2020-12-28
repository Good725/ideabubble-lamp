/*
ts:2017-04-06 12:30:00
*/

/* News */

INSERT INTO plugin_pages_layouts (`layout`) VALUES ('news');

INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `layout_id`, `category_id`, `content`, `footer`) VALUES
(
  'news',
  'News',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'news'),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1),
  '',

  '<div class="full-row let-talk-bg">
	<div class="fix-container">
		<h2>LET&#39;S TALK</h2>
		<a class="btn-primary inverse" href="contactus">contact us</a>
	</div>
</div>'
);

UPDATE
  `engine_settings`
SET
  `value_dev`   = '1',
  `value_test`  = '1',
  `value_stage` = '1',
  `value_live`  = '1'
WHERE
  `variable` = 'images_in_news_feed';

INSERT INTO `plugin_media_shared_media_photo_presets` (`title`, `directory`, `width_large`, `height_large`, `action_large`, `thumb`, `width_thumb`, `height_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
(
  'News item',
  'news',
  '1028',
  '452',
  'crop',
  1,
  '424',
  '316',
  'crop',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0'
);


/* Update "typewriter" text */
UPDATE `plugin_pages_pages`
SET
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  `content`       = '<section class=\"full-row home\-\-banner\">
\n	<div class=\"fix-container\">
\n		<div class=\"page-caption\">
\n			<h2>We enable our clients to</h2>
\n
\n			<div class=\"typed-text\">
\n				<p>accelerate results</p>
\n				<p>disrupt outdated processes</p>
\n				<p>identify solutions quickly</p>
\n				<p>transform customer experiences</p>
\n			</div>
\n
\n			<hr />
\n			<p>We turn business ideas into extraordinary digital products. You Dream it, We Build it.</p>
\n		</div>
\n	</div>
\n</section>
\n
\n<section class=\"about\-\-section full-row\">
\n	<div class=\"rotate-img\">&nbsp;</div>
\n
\n	<div class=\"fix-container\">
\n		<div class=\"theme-heading\">
\n			<h2>About Us</h2>
\n		</div>
\n
\n		<div class=\"section\-\-about_us\">
\n			<p>At Idea Bubble we are passionate about developing cutting edge solutions. We opened our doors in 2008 and over the past number of years have grown from strength to strength. Now, having launched hundreds of websites and developed many PHP development solutions, we offer an abundance of experience and a passion for creating all things technical!</p>
\n		</div>
\n
\n		<div class=\"user-feedback\">
\n			<blockquote class=\"comment-txt\">Quite simply, we love code!</blockquote>
\n
\n			<div class=\"user-name\">Michael O&#39;Callaghan, MD</div>
\n		</div>
\n	</div>
\n
\n	<div class=\"full-row\">
\n		<ul class=\"grid\-\-5 grid-view slanted_menu\">
\n			<li>
\n				<div class=\"head-bar\"><img src=\"/assets/ideabubble/images/visitors-icon.png\" alt=\"\" /> <span>3.2 Mill</span></div>
\n
\n				<p>visitors</p>
\n			</li>
\n			<li>
\n				<div class=\"head-bar\"><img src=\"/assets/ideabubble/images/value-saved-icon.png\" alt=\"Euro\" /> <span>1.2 Mill</span></div>
\n
\n				<p>value saved</p>
\n			</li>
\n			<li>
\n				<div class=\"head-bar\"><img src=\"/assets/ideabubble/images/integrations-icon.png\" alt=\"\" /> <span>58</span></div>
\n
\n				<p>integrations</p>
\n			</li>
\n			<li>
\n				<div class=\"head-bar\"><img src=\"/assets/ideabubble/images/solutions-icon.png\" alt=\"\" /> <span>15</span></div>
\n
\n				<p>solutions</p>
\n			</li>
\n			<li>
\n				<div class=\"head-bar\"><img src=\"/assets/ideabubble/images/service-icon.png\" alt=\"\" /> <span>12</span></div>
\n
\n				<p>servers</p>
\n			</li>
\n		</ul>
\n	</div>
\n</section>
\n
\n<section class=\"full-row strength-sect\">
\n	<div class=\"rotate-img\">&nbsp;</div>
\n
\n	<div class=\"fix-container\">
\n		<div class=\"left-sect\">
\n			<div class=\"theme-heading\">
\n				<h2>Our Strengths</h2>
\n			</div>
\n
\n			<p>Over the past 10 years, we have learned a lot about what makes a great site. Combining form and function, to deliver end to end digital solutions has become our passion. Let us help you create the perfect digital solution for your business, from website to accounting dashboards, to event management. We design custom digital projects like, fit for your business, like no one else.</p>
\n		</div>
\n
\n		<div class=\"right-sect\">
\n			<ul class=\"number-list\">
\n				<li>Innovative</li>
\n				<li>passionate</li>
\n				<li>Visionary</li>
\n				<li>Experts</li>
\n				<li>Value Driven</li>
\n			</ul>
\n		</div>
\n	</div>
\n</section>
\n
\n<div class=\"full-row our\-\-partners\">
\n	<div class=\"theme-heading\">
\n		<h2>Our Partners</h2>
\n	</div>
\n
\n	<p>{testimonialsfeed-Testimonials}</p>
\n</div>'
WHERE
  `name_tag` IN ('home', 'home.html')
;