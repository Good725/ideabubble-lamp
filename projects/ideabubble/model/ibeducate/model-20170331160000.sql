/*
ts:2017-03-31 16:00:00
*/

UPDATE `plugin_pages_pages`
SET
  `title`         = 'Products',
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' LIMIT 1),
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content'),
  `content`       = "<article class=\"product-item\">
\n    <h1 data-position=\"left\"><span><span style=\"color: #2488b3;\">#innovative</span></span></h1>
\n
\n    <div>
\n        <h2><span><span style=\"color: #2488b3;\">Powerful Booking Platform</span></span></h2>
\n    </div>
\n
\n    <div>
\n        <img src=\"/assets/educate/images/powerful-booking-img.png\" alt=\"\" />
\n    </div>
\n
\n    <div>
\n        <p>A complete course selection, online registration resource. From setup, registration,
\n            payment, management,
\n            communication and reporting, it is designed to offer pure
\n            simplicity and ease of use by school managers, teachers,
\n            parents and students.</p>
\n    </div>
\n</article>
\n
\n<article class=\"product-item\">
\n    <h1 data-position=\"bottom left\"><span style=\"color: #159f6d;\">#connected</span></h1>
\n
\n    <div>
\n        <h2><span style=\"color: #159f6d;\">Mass communications with teachers and students</span></h2>
\n
\n        <p>Send automated bulk SMS texts or e-mails to your
\n            student and parent groups. Schedule to issue on specific days and times.
\n        </p>
\n        <p>
\n            Keep connected on course changes, meetings,
\n            time-limited early registrations, multi-person or last minute promotional code discounts or incentives.
\n            It\'s so easy to do!
\n        </p>
\n    </div>
\n
\n    <div>
\n        <img src=\"/assets/educate/images/bulk-msg-img.png\" alt=\"\" />
\n    </div>
\n</article>
\n
\n<article class=\"product-item\">
\n    <h1 data-position=\"right\"><span style=\"color: #ed0d6c;\">#efficient</span></h1>
\n
\n    <div>
\n        <h2><span style=\"color: #ed0d6c;\">Reduce Admin by >50%</span></h2>
\n        <img src=\"/assets/educate/images/reduce-admin.png\" alt=\"\" />
\n    </div>
\n
\n    <div>
\n        <p>Take the admin time and effort out of the physical
\n            registration process; classroom activity monitoring and student and teacher performance reporting. You will reduce office work, printing and staff costs, whilst making processes easy and accessible for teachers, parents and students.
\n        </p>
\n        <p>
\n            No more collecting forms, processing applications and postal or phone payments. You get secure, online,
\n            compliant processing of your students payments.
\n        </p>
\n        <p>
\n            Instead, dedicate that precious admin time and effort
\n            towards Course creation, development, student outreach and marketing efforts.
\n        </p>
\n    </div>
\n</article>
\n
\n<article class=\"product-item\">
\n    <h1 data-position=\"bottom right\"><span style=\"color: #fc8149;\">#powerful</span></h1>
\n
\n    <div>
\n        <h2><span style=\"color: #fc8149;\">Better<br />Control</span></h2>
\n
\n        <p>Data never goes away – it is always available to you!
\n            Powerful integrated Financial Performance Tracking.
\n            Analyse term on term; year on year comparisons by all key parameters. Track reports, forms, results and all
\n            relevant documents. View, Sort and Update Course,
\n            Student and Teacher records online, in \'real-time\' 24/7.
\n            Set shared performance goals and measurables for your school and team. Generate and export financials,
\n            statements, database reports with ease.</p>
\n
\n        <p>That\'s just the beginning.....</p>
\n    </div>
\n
\n    <div>
\n        <img src=\"assets/educate/images/better-control-img.png\" alt=\"\" />
\n    </div>
\n</article>
\n
\n<article class=\"product-item\">
\n    <h1 data-position=\"bottom left\"><span style=\"color: #000;\">#accessible</span></h1>
\n
\n    <div>
\n        <h2 style=\"color: #000;\">24/7
\n            access for all users
\n            on mobile and
\n            desktop
\n        </h2>
\n    </div>
\n
\n    <div>
\n        <img src=\"/assets/educate/images/student-24-7.png\" alt=\"\" />
\n    </div>
\n
\n    <div>
\n        <p>View, edit, analyse each
\n            complete Course roster, data,
\n            performance measure and
\n            customised content. Collect any data with an unlimited amount of custom questions.
\n        </p>
\n
\n        <p>Assess Course opportunities for upselling, feasibility, increase in capacity, curriculum changes and more.</p>
\n    </div>
\n</article>
\n
\n<article class=\"product-item\">
\n    <h1 data-position=\"right\"><span style=\"color: #2488b3;\">#creative</span></h1>
\n
\n    <div>
\n        <h2><span style=\"color: #2488b3;\">Course Management</span></h2>
\n
\n        <p>Create, set up and manage your course structures and bookings from start to finish. Easily add/edit course content by subject and curriculum content, categorise by levels, plan ahead for short and long range courses and generate workable course timetables across your day and night course term times. All course elements can be set up within minutes. Full visibility of your course pipeline will support demand planning and sales projections internally while at the same time
\n            externally promoting and boosting your online
\n            bookings and registrations.
\n        </p>
\n    </div>
\n
\n    <div>
\n        <img src=\"/assets/educate/images/course-img.png\" alt=\"\" />
\n    </div>
\n</article>
\n
\n<article class=\"product-item\">
\n    <h1 data-position=\"bottom right\"><span style=\"color: #159f6d;\">#tracktime</span></h1>
\n
\n    <div>
\n        <h2><span style=\"color: #159f6d;\">Timesheets</span></h2>
\n
\n        <img src=\"/assets/educate/images/timesheet.png\" alt=\"\" />
\n    </div>
\n
\n    <div>
\n        <p>Time is a precious asset, so make the most of yours! No more paper. You can manage your trainer, teacher or instructor’s working time online directly or just give them a quick and easy login providing individual secure access and they can update and track their worked hours themselves. You or your admin team can simply review, confirm or query working hours, breaks, annual leave and generate reports for
\n            compliance with internal working time policy and any other regulatory requirements. Keep track of your team easily!                    </p>
\n    </div>
\n</article>
\n
\n<article class=\"product-item\">
\n    <h1 data-position=\"left\"><span style=\"color: #ed0d6c;\">#schedule</span></h1>
\n
\n    <div>
\n        <h2><span style=\"color: #ed0d6c;\">Realtime Timetables</span></h2>
\n    </div>
\n
\n    <div>
\n        <img src=\"/assets/educate/images/real-time-img.png\" alt=\"\" />
\n    </div>
\n
\n    <div>
\n        <p>
\n            Monitor and manage
\n            running timetables by
\n            student, topic and schedule. Receive \'real-time\' alerts on attendance updates,
\n            messages and queries
\n            allowing you to action and resolve quickly and with ease.
\n        </p>
\n    </div>
\n</article>
\n
\n<article class=\"product-item\">
\n    <h1 data-position=\"right\"><span style=\"color: #fc8149;\">#flexible</span></h1>
\n
\n    <div>
\n        <h2><span style=\"color: #fc8149;\">E-Learning Course Design</span></h2>
\n
\n        <p>
\n            Embrace the future of learning and development! Where distance is no object and you can advertise cost-effective, scalable and relevant courses nationally and internationally. Enable students to easily login and attend short or extended courses online, where they can avail of personalised and interactive access offering self-paced learning in a location of their choosing. Reach outside your commute zone!
\n        </p>
\n    </div>
\n
\n    <div>
\n        <img src=\"/assets/educate/images/e-Learning2.png\" alt=\"\" />
\n    </div>
\n</article>
\n
\n<article class=\"product-item\">
\n    <h1 data-position=\"top left\"><span style=\"color: #000;\">#connected</span></h1>
\n
\n    <div>
\n        <h2><span style=\"color: #000;\">Instant Messaging</span></h2>
\n    </div>
\n
\n    <div>
\n        <img src=\"/assets/educate/images/Instant-msg1.png\" alt=\"\" />
\n    </div>
\n
\n    <div>
\n        <p>
\n            Makes communicating fast, easy, private and in ‘real-time’. An ideal business tool to reach your team, because it is flexible, can work anywhere and allows attachments!
\n        </p>
\n    </div>
\n</article>
\n
\n<article class=\"product-item\">
\n    <h1 data-position=\"top right\"><span style=\"color: #2488b3;\">#personalise</span></h1>
\n
\n    <div>
\n        <h2><span style=\"color: #2488b3;\">Calendar Views</span></h2>
\n    </div>
\n
\n    <div>
\n        <img src=\"/assets/educate/images/calendar-img.png\" alt=\"\" />
\n    </div>
\n
\n    <div>
\n        <p>
\n            Designed to save you time and help you to make the most of everyday. View your classes, your appointments and your free time... for all those new opportunities!
\n        </p>
\n    </div>
\n</article>
\n
\n<article class=\"product-item\">
\n    <h1 data-position=\"bottom right\"><span style=\"color: #159f6d;\">#profits</span></h1>
\n
\n    <div>
\n        <h2><span style=\"color: #159f6d;\">Increased<br />Revenue</span></h2>
\n    </div>
\n
\n    <div>
\n        <img src=\"/assets/educate/images/calendar-img.png\" alt=\"\" />
\n    </div>
\n
\n    <div>
\n        <p>
\n            Transform the way you do business. You can be more efficient across ALL functions allowing you to allocate precious time and resources to business-generating activities. Improved data-gathering, reporting and analysis leads to efficiencies, improved
\n            decision-making, investment allocation and market segment development. This in turn leads to improved student, parent and teacher experience which drives satisfaction, sales and revenue.                     </p>
\n    </div>
\n</article>"
WHERE
  `name_tag` IN ('product_page', 'product_page.html');

/* No longer needs a dedicated layout. */
UPDATE `plugin_pages_layouts` SET `deleted`='1' WHERE `layout` = 'products_page';



UPDATE `plugin_pages_pages`
SET
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' LIMIT 1),
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'home'),
  `content`       = "<section class=\"full-row\">
\n        <div class=\"fix-container\">
\n            <div class=\"key-features\">
\n                <div class=\"theme-heading\">
\n                    <h2>Key Features</h2>
\n                </div>
\n
\n                <ul class=\"grid-view\">
\n                    <li>
\n                        <h4>Easy-to-Advertise<br />Courses</h4>
\n
\n                        <img src=\"/assets/educate/images/e-learning-img.jpg\" />
\n
\n                        <p>Clearly advertise your courses with the press of a button, our integrated system then allows the course to integrate with your entire school, through our Educate software. From booking to invoicing in one place.</p>
\n                    </li>
\n
\n                    <li>
\n                        <h4>Easy Communications with staff and students</h4>
\n
\n                        <img src=\"/assets/educate/images/bulk-messaging-img.jpg\" />
\n
\n                        <p>Send bulk SMS message quickly to all your teachers. Chat with your staff with instant messaging</p>
\n                    </li>
\n
\n                    <li>
\n                        <h4>Integrated<br />Accounting</h4>
\n
\n                        <img src=\"/assets/educate/images/auto-accounting-img.jpg\" />
\n
\n                        <p>Payroll, Invoicing, Multy Course Packages, Family Deals, Coupon Codes, Buy 1 get 1 free</p>
\n                    </li>
\n                </ul>
\n
\n                <div class=\"aligncenter padd-top-70\">
\n                    <a class=\"btn-primary\" href=\"#\">View More</a>
\n                </div>
\n            </div>
\n        </div>
\n    </section>
\n
\n    <div class=\"center-logo\">
\n        <img src=\"/shared_media/ibeducate/media/photos/content/center-logo-img.png\" />
\n    </div>
\n
\n    <div class=\"fr large-txt\">experts</div>
\n
\n    <section class=\"full-row why-choose\">
\n        <div class=\"gray-bg padd-top-bottom-50\">
\n            <div class=\"fix-container\">
\n                <div class=\"theme-heading\">
\n                    <h2>Why Choose Us?</h2>
\n                </div>
\n
\n                <ul class=\"grid-view padd-bottom-50 sonar_effect\">
\n                    <li>
\n                        <figure class=\"imgbox\"><img src=\"/shared_media/ibeducate/media/photos/content/flexible-icon.png\" /></figure>
\n
\n                        <h4>Flexible</h4>
\n
\n                        <p>Your Dream it, We Build It</p>
\n                    </li>
\n                    <li>
\n                        <figure class=\"imgbox\"><img src=\"/shared_media/ibeducate/media/photos/content/experts-icon.png\" /></figure>
\n
\n                        <h4>Experts</h4>
\n
\n                        <p>Tried &amp; Trusted Team</p>
\n                    </li>
\n                    <li>
\n                        <figure class=\"imgbox\"><img src=\"/shared_media/ibeducate/media/photos/content/savings-icon.png\" /></figure>
\n
\n                        <h4>Savings</h4>
\n
\n                        <p>Long-term cost savings for you</p>
\n                    </li>
\n                    <li>
\n                        <figure class=\"imgbox\"><img src=\"/shared_media/ibeducate/media/photos/content/established-icon.png\" /></figure>
\n
\n                        <h4>Established</h4>
\n
\n                        <p>Since 2007 we have<br />
\n                            been creating solutions</p>
\n                    </li>
\n                </ul>
\n            </div>
\n
\n            <div class=\"gray pattern\">
\n                <div class=\"fix-container\">
\n                    <div class=\"aligncenter padd-bottom-50\"><a class=\"theme-btn\" href=\"#\">About us</a></div>
\n                </div>
\n            </div>
\n        </div>
\n    </section>
\n
\n    <div class=\"center-logo\">
\n        <figure class=\"imgbox\"><img src=\"/shared_media/ibeducate/media/photos/content/center-logo-img.png\" /></figure>
\n    </div>
\n
\n    <div class=\"full-row let-talk-bg\">
\n        <div class=\"fix-container\">
\n            <h3>want to hire us?</h3>
\n
\n            <h2>let&rsquo;s talk</h2>
\n            <a class=\"theme-btn-white\" href=\"contactus\">contact us</a></div>
\n    </div>"
WHERE
  `name_tag` IN ('home', 'home.html');


UPDATE `plugin_pages_pages`
SET
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'stephen@ideabubble.ie' LIMIT 1),
  `content` = '<section class=\"full-row\" style=\"background-color: #F7F7F7;\">
\n        <div class=\"fix-container\">
\n            <div class=\"key-features\">
\n                <div class=\"theme-heading\">
\n                    <h2>Key Features</h2>
\n                </div>
\n
\n                <ul class=\"grid-view\">
\n                    <li>
\n                        <h4>Easy-to-Advertise<br />
\n                            Courses</h4>
\n                        <img src=\"/assets/educate/images/e-learning-img.jpg\" />
\n                        <p>Clearly advertise your courses with the press of a button, our integrated system then allows the course to integrate with your entire school, through our Educate software. From booking to invoicing in one place.</p>
\n                    </li>
\n                    <li>
\n                        <h4>Easy Communications with staff and students</h4>
\n                        <img src=\"/assets/educate/images/bulk-messaging-img.jpg\" />
\n                        <p>Send bulk SMS message quickly to all your teachers. Chat with your staff with instant messaging</p>
\n                    </li>
\n                    <li>
\n                        <h4>Integrated<br />
\n                            Accounting</h4>
\n                        <img src=\"/assets/educate/images/auto-accounting-img.jpg\" />
\n                        <p>Payroll, Invoicing, Multy Course Packages, Family Deals, Coupon Codes, Buy 1 get 1 free</p>
\n                    </li>
\n                </ul>
\n
\n                <p style=\"text-align: center;\"><a class=\"btn-primary\" href=\"/product_page\">Learn More</a></p>
\n            </div>
\n        </div>
\n    </section>
\n
\n    <p class=\"large-txt\" style=\"text-align: right;\">#experts</p>
\n
\n    <section class=\"full-row why-choose\">
\n        <div class=\"padd-top-bottom-50\">
\n            <div class=\"fix-container\">
\n                <div class=\"theme-heading\">
\n                    <h2>Why Choose Us?</h2>
\n                </div>
\n
\n                <ul class=\"grid-view padd-bottom-50 sonar_effect\">
\n                    <li>
\n                        <figure class=\"imgbox\"><img src=\"/assets/educate/images/flexible-icon.png\" /></figure>
\n
\n                        <h4>Passionate</h4>
\n
\n                        <p>We love providing solutions</p>
\n                    </li>
\n                    <li>
\n                        <figure class=\"imgbox\"><img src=\"/assets/educate/images/experts-icon.png\" /></figure>
\n
\n                        <h4>Experts</h4>
\n
\n                        <p>Tried &amp; Trusted Team</p>
\n                    </li>
\n                    <li>
\n                        <figure class=\"imgbox\"><img src=\"/assets/educate/images/savings-icon.png\" /></figure>
\n
\n                        <h4>Savings</h4>
\n
\n                        <p>Long-term cost savings for you</p>
\n                    </li>
\n                    <li>
\n                        <figure class=\"imgbox\"><img src=\"/assets/educate/images/established-icon.png\" /></figure>
\n
\n                        <h4>Efficiency</h4>
\n
\n                        <p>Fine-tune several aspects of the business</p>
\n                    </li>
\n                </ul>
\n            </div>
\n
\n
\n            <p style=\"text-align: center;\"><a class=\"btn-primary\" href=\"/aboutus\">About Us</a></p>
\n        </div>
\n    </section>
\n
\n    <div class=\"full-row let-talk-bg\">
\n        <div class=\"fix-container\">
\n            <h2>LET&#39;S TALK</h2>
\n            <a class=\"btn-primary inverse\" href=\"/contactus\">contact us</a>
\n        </div>
\n    </div>'
WHERE
  `name_tag` IN ('home', 'home.html');


INSERT INTO `plugin_panels` (`title`, `position`, `type_id`, `text`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
(
  'Why Educo',
  'home_right',
  (SELECT `id` FROM `plugin_panels_types` WHERE `name` = 'static' AND `deleted` = 0 LIMIT 1),
  '<h2>Why Educo</h2>
\n<ul>
\n 	<li><span aria-hidden=\"true\" class=\"icon icon_desktop\"></span> Intuitive booking platform</li>
\n 	<li><span aria-hidden=\"true\" class=\"icon social_myspace\"></span> Increased profits</li>
\n 	<li><span aria-hidden=\"true\" class=\"icon icon_creditcard\"></span> Reduced admin costs</li>
\n 	<li><span aria-hidden=\"true\" class=\"icon icon_link\"></span> Course Management</li>
\n 	<li><span aria-hidden=\"true\" class=\"icon icon_documents\"></span> E-learning platform</li>
\n 	<li><span aria-hidden=\"true\" class=\"icon icon_zoom-in\"></span> PLUS 100&rsquo;s of other features...</li>
\n</ul>
\n<h4>Would you like to try a demo?</h4>
\n<p><a href=\"#\">Let&#39;s Talk</a></p>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie'),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie'),
  '1',
  '0'
);

UPDATE `engine_settings`
SET
  `value_dev`   = '&copy; 2017 Idea Bubble, all rights reserved.',
  `value_test`  = '&copy; 2017 Idea Bubble, all rights reserved.',
  `value_stage` = '&copy; 2017 Idea Bubble, all rights reserved.',
  `value_live`  = '&copy; 2017 Idea Bubble, all rights reserved.'
WHERE
  `variable` = 'company_copyright'
;