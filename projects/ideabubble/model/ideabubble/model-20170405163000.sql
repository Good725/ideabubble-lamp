/*
ts:2017-04-05 16:30:00
*/

/* Split the "About us" page */
INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `layout_id`, `category_id`, `content`, `footer`) VALUES
(
  'meet-the-team',
  'Meet the Team',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content'),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1),
  '<section class=\"about\-\-section full-row\">
\n	<div class=\"fix-container\">
\n		<h2 style=\"text-align:center\">Meet the team</h2>
\n
\n		<p>We share in the ongoing pursuit of mastery. We strive to become better professionals, athletes and partners to our clients.</p>
\n
\n		<p>We encourage you to explore our thinking around UX, engineering and company culture.</p>
\n
\n		<p>&nbsp;</p>
\n
\n		<p>&nbsp;</p>
\n
\n		<ul class=\"grid-view grid-view\-\-borders\">
\n			<li><span style=\"color:#00385d; font-size:1.5em\"><strong><span style=\"font-size:4em\">58,458+</span></strong><br />
\n				TOTAL HOURS WORKED</span></li>
\n			<li style=\"text-align:center\"><span style=\"color:#00385d; font-size:1.5em\"><strong><span style=\"font-size:4em\">16+</span></strong><br />
\n				TEAM MEMBERS</span></li>
\n			<li><span style=\"color:#00385d; font-size:1.5em\"><strong><span style=\"font-size:4em\">87,278+</span></strong><br />
\n				TOTAL SMILES WE RECEIVED SO FAR THIS YEAR FROM OUR CUSTOMERS</span></li>
\n		</ul>
\n
\n		<p>&nbsp;</p>
\n
\n		<p>&nbsp;</p>
\n
\n		<p>&nbsp;</p>
\n	</div>
\n</section>
\n
\n<section class=\"full-row\">
\n	<h2 style=\"text-align:center\">We&#39;re growing every day. Our team is too.</h2>
\n
\n	<div class=\"fix-container our-team\">
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-mike.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Michael O&#39;Callaghan</strong><br />
\n		CEO</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-tempy.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Tempy Allen</strong><br />
\n		Director of Marketing</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-nick.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Nick Gudge</strong><br />
\n		Director of Finance</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-taron.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Taron Sargsyan</strong><br />
\n		Software Developer</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-maja.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Maja Otic</strong><br />
\n		Director of Product Design</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-robert.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Robert McKnight</strong><br />
\n		Scrum Master</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-ratko.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Ratko Bucic</strong><br />
\n		Database Administrator</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-andrew.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Andrew Mathisen</strong><br />
\n		Director of Sales</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-stephen.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Stephen Byrne</strong><br />
\n		Software Developer</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-mehmet.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Mehmet Emin Aky&uuml;z</strong><br />
\n		Software Developer</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-davit.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Davit Petrosyan</strong><br />
\n		Software Developer</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-gevorg.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Gevorg Simonyan</strong><br />
\n		Software Developer</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-olly.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Olly</strong><br />
\n		Software Developer</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-himanshu.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Himanshu Chilain</strong><br />
\n		Front-end Developer</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-peter.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Peter New</strong><br />
\n		Automation Engineer</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-kamal.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Kamal Kumar</strong><br />
\n		Quality Assurance</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-rupali.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Rupali Joshi</strong><br />
\n		Quality Assurance</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-jack.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Jack Finn</strong><br />
\n		Student</p>
\n		</figure>
\n	</div>
\n</section>
\n
\n<div class=\"full-row our\-\-partners\">
\n	<div class=\"fix-container\">
\n		<h2 style=\"text-align:center\">Our Values</h2>
\n
\n		<p>&nbsp;</p>
\n
\n		<p style=\"text-align:center\">&nbsp;</p>
\n
\n		<ul class=\"grid-view\">
\n			<li style=\"text-align:center\"><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/passionate.png\" style=\"height:82px; width:88px\" /><br />
\n				<strong>Passionate</strong> We love providing solutions</li>
\n			<li style=\"text-align:center\"><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/experts.png\" style=\"height:82px; width:77px\" /><br />
\n				<strong>Experts</strong> Tried &amp; Trusted Team</li>
\n			<li style=\"text-align:center\"><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/wallet.png\" style=\"height:82px; width:66px\" /><br />
\n				<strong>Savings</strong> Long-term cost savings for you</li>
\n			<li style=\"text-align:center\"><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/efficiency.png\" style=\"height:82px; width:73px\" /><br />
\n				<strong>Established</strong> Creating solutions since 2007</li>
\n		</ul>
\n	</div>
\n</div>',

  '<div class="full-row let-talk-bg">
	<div class="fix-container">
		<h2>LET&#39;S TALK</h2>
		<a class="btn-primary inverse" href="contactus">contact us</a>
	</div>
</div>'
);

UPDATE
  `plugin_pages_pages`
SET
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  `content`       = '<section class=\"about\-\-section full-row padd-bottom-100\">
\n	<div class=\"rotate-img\">&nbsp;</div>
\n
\n	<div class=\"fix-container\">
\n		<ul class=\"grid-view slanted_menu\">
\n			<li>
\n				<div class=\"sub-heading\">
\n					<h3>Who we are</h3>
\n				</div>
\n
\n				<div class=\"panel-body\">
\n					<p>iWe are a team of highly skilled developers that love crunching code. Specialising in PHP development, we offer innovation and a fast paced leadership in online technologies.</p>
\n				</div>
\n			</li>
\n			<li>
\n				<div class=\"sub-heading\">
\n					<h3>What we do</h3>
\n				</div>
\n
\n				<div class=\"panel-body\">
\n					<p>Having launched hundreds of websites and developed many PHP development solutions, we offer an abundance of experience and a passion for creating all things technical!</p>
\n				</div>
\n			</li>
\n			<li>
\n				<div class=\"sub-heading\">
\n					<h3>When we started</h3>
\n				</div>
\n
\n				<div class=\"panel-body\">
\n					<p>We opened our doors in 2008 and over the past number of years have grown from strength to strength.</p>
\n				</div>
\n			</li>
\n		</ul>
\n
\n		<div class=\"user-feedback\">
\n			<blockquote class=\"comment-txt\">Quite simply, we love code!</blockquote>
\n			<cite class=\"user-name\">Michael O&#39;Callaghan, MD</cite></div>
\n	</div>
\n
\n	<div class=\"fix-container\">
\n		<div class=\"padd-top-botton-50\">
\n			<div class=\"theme-heading\">
\n				<h2>Long Lasting Customer Relationships</h2>
\n			</div>
\n
\n			<div class=\"after-border panel-body\">
\n				<p>Our business is built on the strength of our reputation. Working nationally and internationally, we partner with business directly and work with agencies. Some of our clients include businesses of all shapes and sizes, marketing agencies, design studios, enterprise boards and county councils. The common bond is clients who want dedicated online solutions, from someone they can trust. Idea Bubble offer that solution.</p>
\n			</div>
\n		</div>
\n	</div>
\n
\n	<p>&nbsp;</p>
\n</section>
\n
\n<p>{testimonialsfeed-Testimonials}</p>
\n
\n<section class=\"full-row\">
\n	<h2 style=\"text-align:center\">We&#39;re growing every day. Our team is too.</h2>
\n
\n	<div class=\"fix-container our-team\">
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-mike.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Michael O&#39;Callaghan</strong><br />
\n		CEO</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-tempy.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Tempy Allen</strong><br />
\n		Director of Marketing</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-nick.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Nick Gudge</strong><br />
\n		Director of Finance</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-maja.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Maja Otic</strong><br />
\n		Director of Product Design</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-taron.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Taron Sargsyan</strong><br />
\n		Director of Engineering</p>
\n		</figure>
\n
\n		<figure><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/team-robert.png\" style=\"height:220px; width:220px\" />
\n		<p><strong>Robert McKnight</strong><br />
\n		Director of PMO</p>
\n		</figure>
\n	</div>
\n
\n	<p style=\"text-align:center\"><a class=\"btn-primary\" href=\"/meet-the-team.html\">Meet the Rest of the Team</a></p>
\n
\n	<p>&nbsp;</p>
\n</section>
\n
\n<div class=\"full-row our\-\-partners\">
\n	<div class=\"fix-container\">
\n		<h2 style=\"text-align:center\">Our Values</h2>
\n
\n		<p>&nbsp;</p>
\n
\n		<p>&nbsp;</p>
\n
\n		<ul class=\"grid-view\">
\n			<li style=\"text-align:center\"><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/passionate.png\" style=\"height:82px; width:88px\" /><br />
\n				<strong>Passionate</strong> We love providing solutions</li>
\n			<li style=\"text-align:center\"><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/experts.png\" style=\"height:82px; width:77px\" /><br />
\n				<strong>Experts</strong> Tried &amp; Trusted Team</li>
\n			<li style=\"text-align:center\"><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/wallet.png\" style=\"height:82px; width:66px\" /><br />
\n				<strong>Savings</strong> Long-term cost savings for you</li>
\n			<li style=\"text-align:center\"><img alt=\"\" src=\"/shared_media/ideabubble/media/photos/content/efficiency.png\" style=\"height:82px; width:73px\" /><br />
\n				<strong>Efficiency</strong> Fine-tune several aspects of the business</li>
\n		</ul>
\n	</div>
\n</div>'
WHERE
  `name_tag` IN ('aboutus', 'aboutus.html')
;

/* Adding typing animation */
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
\n
\n				<p>reinvent the customer experience.</p>
\n
\n				<p>redefine digital education.</p>
\n
\n				<p>transform business processes.</p>
\n
\n				<p>use one login for all.</p>
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