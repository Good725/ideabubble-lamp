/*
ts:2017-04-04 11:00:00
*/

UPDATE `engine_settings`
SET
  `value_dev`   = '&copy; 2017 Idea Bubble, all rights reserved.',
  `value_test`  = '&copy; 2017 Idea Bubble, all rights reserved.',
  `value_stage` = '&copy; 2017 Idea Bubble, all rights reserved.',
  `value_live`  = '&copy; 2017 Idea Bubble, all rights reserved.'
WHERE
  `variable` = 'company_copyright'
;

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
\n    <ul class=\"grid-view\">
\n        <li>
\n            <figure>
\n                <img alt=\"\" src=\"/assets/ideabubble/images//our-partners-logo1.png\" style=\"height:101px; width:167px\" />
\n            </figure>
\n
\n            <blockquote>
\n                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\n                <cite>Jane Doe</cite>
\n            </blockquote>
\n        </li>
\n
\n        <li>
\n            <figure>
\n                <img alt=\"\" src=\"/assets/ideabubble/images//our-partners-logo2.png\" style=\"height:60px; width:228px\" />
\n            </figure>
\n
\n            <blockquote>
\n                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\n                <cite>Julie Kilmartin</cite>
\n            </blockquote>
\n        </li>
\n
\n        <li>
\n            <figure>
\n                <img alt=\"\" src=\"/assets/ideabubble/images//our-partners-logo3.png\" style=\"height:75px; width:228px\" />
\n            </figure>
\n
\n            <blockquote>
\n                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\n                <cite>Matt McGrory</cite>
\n            </blockquote>
\n        </li>
\n
\n        <li>
\n            <figure>
\n                <img alt=\"\" src=\"/assets/ideabubble/images//our-partners-logo4.png\" style=\"height:59px; width:221px\" />
\n            </figure>
\n
\n            <blockquote>
\n                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\n                <cite>John Doe</cite>
\n            </blockquote>
\n        </li>
\n
\n        <li>
\n            <figure>
\n                <img alt=\"\" src=\"/assets/ideabubble/images//our-partners-logo5.jpg\" style=\"height:179px; width:280px\" />
\n            </figure>
\n
\n            <blockquote>
\n                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\n                <cite>John Doe</cite>
\n            </blockquote>
\n        </li>
\n    </ul>
\n</div>
\n
\n<div class=\"full-row let-talk-bg\">
\n    <div class=\"fix-container\">
\n        <h2>LET&#39;S TALK</h2>
\n        <a class=\"btn-primary inverse\" href=\"contactus\">Contact Us</a>
\n    </div>
\n</div>"
WHERE `name_tag` IN ('home', 'home.html');

/* About us page */
UPDATE `plugin_pages_pages`
SET
  `title`         = 'About Us',
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content'),
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' LIMIT 1),
  `content`       = '<section class=\"about\-\-section full-row padd-bottom-100\">
\n    <div class=\"rotate-img\">&nbsp;</div>
\n
\n    <div class=\"fix-container\">
\n        <ul class=\"grid-view slanted_menu\">
\n            <li>
\n                <div class=\"sub-heading\">
\n                    <h3>Who we are</h3>
\n                </div>
\n
\n                <div class=\"panel-body\">
\n                    <p>We are a team of highly skilled developers that love crunching code. Speciliasing in PHP development, we offer innovation and a fast paced leadership in online technologies.</p>
\n                </div>
\n            </li>
\n            <li>
\n                <div class=\"sub-heading\">
\n                    <h3>What we do</h3>
\n                </div>
\n
\n                <div class=\"panel-body\">
\n                    <p>Having launched hundreds of websites and developed many PHP development solutions, we offer an abundance of experience and a passion for creating all things technical!</p>
\n                </div>
\n            </li>
\n            <li>
\n                <div class=\"sub-heading\">
\n                    <h3>When we started</h3>
\n                </div>
\n
\n                <div class=\"panel-body\">
\n                    <p>We opened our doors in 2008 and over the past number of years have grown from strength to strength.</p>
\n                </div>
\n            </li>
\n        </ul>
\n
\n        <div class=\"user-feedback\">
\n            <blockquote class=\"comment-txt\">Quite simply, we love code!</blockquote>
\n
\n            <cite class=\"user-name\">Michael O&#39;Callaghan, MD</cite>
\n        </div>
\n    </div>
\n
\n    <div class=\"fix-container\">
\n        <div class=\"padd-top-botton-50\">
\n            <div class=\"theme-heading\">
\n                <h2>Long Lasting Customer Relationships</h2>
\n            </div>
\n
\n            <div class=\"after-border panel-body\">
\n                <p>Our business is built on the strength of our reputation. Working nationally and internationally, we partner with business directly and work with agencies. Some of our clients include businesses of all shapes and sizes, marketing agencies, design studios, enterprise boards and county councils. The common bond is clients who want dedicated online solutions, from someone they can trust. Idea Bubble offer that solution.</p>
\n            </div>
\n        </div>
\n    </div>
\n
\n    <p>&nbsp;</p>
\n
\n</section>
\n
\n<p>{testimonialsfeed-Testimonials}</p>
\n
\n<section class=\"full-row\">
\n
\n    <h2 style=\"text-align: center;\">We&#39;re growing every day. Our team is too.</h2>
\n
\n    <div class=\"fix-container our-team\">
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-mike.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Michael O&#39;Callaghan</strong><br />CEO
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-tempy.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Tempy Allen</strong><br />Director of Marketing
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-nick.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Nick Gudge</strong><br />Director of Finance
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-maja.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Maja Otic</strong><br />Director of Product Design
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-robert.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Robert McKnight</strong><br />Scrum Master
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-ratko.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Ratko Bucic</strong><br />Database Administrator
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-mehmet.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Mehmet Emin Akyüz</strong><br />Software Developer
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-taron.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Taron Sargsyan</strong><br />Software Developer
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-stephen.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Stephen Byrne</strong><br />Software Developer
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-davit.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Davit Petrosyan</strong><br />Software Developer
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-gevorg.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Gevorg Simonyan</strong><br />Software Developer
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-olly.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Olly</strong><br />Software Developer
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-himanshu.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Himanshu Chilain</strong><br />Front-end Developer
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-kamal.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Kamal Dixit</strong><br />Quality Assurance
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-rupali.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Rupali</strong><br />Quality Assurance
\n            </figcaption>
\n        </figure>
\n
\n        <figure>
\n            <img src=\"/shared_media/ideabubble/media/photos/content/team-peter.png\" alt=\"\" style=\"width: 250px; height: 250px;\" />
\n            <figcaption>
\n                <strong>Peter New</strong><br />Automation Engineer
\n            </figcaption>
\n        </figure>
\n    </div>
\n</section>
\n
\n<div class=\"full-row our\-\-partners\">
\n    <div class=\"fix-container\">
\n        <h2 style=\"text-align: center;\">Our Values</h2>
\n
\n        <p>&nbsp;</p>
\n
\n        <p>&nbsp;</p>
\n
\n        <ul class=\"grid-view\" style=\"text-align: center;\">
\n            <li>
\n                <img src=\"/shared_media/ideabubble/media/photos/content/passionate.png\" alt=\"\" style=\"width: 88px;height: 82px;\" />
\n                <br />
\n                <strong style=\"color: #00385d;\">Passionate</strong>
\n                We love providing solutions
\n            </li>
\n            <li>
\n                <img src=\"/shared_media/ideabubble/media/photos/content/experts.png\" alt=\"\" style=\"width: 77px;height: 82px;\" />
\n                <br />
\n                <strong style=\"color: #00385d;\">Experts</strong>
\n                Tried &amp; Trusted Team
\n            </li>
\n            <li>
\n                <img src=\"/shared_media/ideabubble/media/photos/content/wallet.png\" alt=\"\" style=\"width: 66px;height: 82px;\" />
\n                <br />
\n                <strong style=\"color: #00385d;\">Savings</strong>
\n                Long-term cost savings for you
\n            </li>
\n            <li>
\n                <img src=\"/shared_media/ideabubble/media/photos/content/efficiency.png\" alt=\"\" style=\"width: 73px;height: 82px;\" />
\n                <br />
\n                <strong style=\"color: #00385d;\">Efficiency</strong>
\n                Fine-tune several aspects of the business
\n            </li>
\n        </ul>
\n    </div>
\n
\n</div>'
WHERE `name_tag` IN ('aboutus', 'aboutus.html');


/* No longer needs a dedicated layout. */
UPDATE `plugin_pages_layouts` SET `deleted`='1' WHERE `layout` = 'aboutus';