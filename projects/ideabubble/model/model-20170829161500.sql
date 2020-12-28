/*
ts:2017-08-29 16:15:00
*/

INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`, `content`) VALUES (
  'our-platform',
  'Our Platform',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT IFNULL(`id`, 1) FROM `plugin_pages_layouts` WHERE `layout` = 'content' LIMIT 1),
  (SELECT IFNULL(`id`, 1) FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1),
  '<section>
\n    <div class=\"our_products-row\">
\n        <div class=\"our_products-item no-blackout\" style=\"background-image: url(\'/assets/ideabubble/images/platform/platform_banner.png\');\">
\n            <div class=\"our_products-overlay\">
\n                <h1>Launch your idea on the right platform</h1>
\n            </div>
\n        </div>
\n    </div>
\n
\n    <div class=\"platform-section fix-container\" style=\"font-size: 24px; max-width: 1166px;\">
\n        <h2>Our platform features</h2>
\n
\n        <p style=\"text-align: left;\">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit</p>
\n
\n        <hr />
\n    </div>
\n</section>
\n
\n<nav class=\"platform-section\" role=\"navigation\">
\n    <h2>FEATURES</h2>
\n
\n    <ul class=\"list-inline\">
\n        <li><a class=\"btn-feature\" href=\"#section-Integrations\">Integrations</a></li>
\n        <li><a class=\"btn-feature\" href=\"#section-Security\">Security</a></li>
\n        <li><a class=\"btn-feature\" href=\"#section-Automation\">Automation</a></li>
\n        <li><a class=\"btn-feature\" href=\"#section-Support\">Support</a></li>
\n        <li><a class=\"btn-feature\" href=\"#section-Messaging\">Messaging</a></li>
\n        <li><a class=\"btn-feature\" href=\"#section-Search_Engine_Optimisation\">Search engine optimisation</a></li>
\n        <li><a class=\"btn-feature\" href=\"#section-Reporting\">Reporting</a></li>
\n        <li><a class=\"btn-feature\" href=\"#section-Design\">Design</a></li>
\n        <li><a class=\"btn-feature\" href=\"#section-Languages\">Languages</a></li>
\n        <li><a class=\"btn-feature\" href=\"#section-Help\">Help</a></li>
\n    </ul>
\n</nav>
\n
\n<section class=\"platform-section\">
\n    <h3 id=\"section-Integrations\">Integrations</h3>
\n
\n    <ul class=\"platform-icons\">
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/google_analytics.png\" alt=\"\" />
\n            <span>View Google Analytics data</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/google_maps.png\" alt=\"\" />
\n            <span>View Google Maps</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/oauth.png\" alt=\"Oauth\" />
\n            <span>Log in with your Google login</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/google_plus.png\" alt=\"\" />
\n            <span>Post and display from and to Google Plus</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/sugar_crm.png\" alt=\"Sugar CRM\" />
\n            <span>Extend your contacts</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/bing.png\" alt=\"\" />
\n            <span>View Bing Analytics data</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/facebook.png\" alt=\"\" />
\n            <span>Like news stories with Facebook</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/oauth.png\" alt=\"\" />
\n            <span>Log in with your Facebook login</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/mailchimp.png\" alt=\"\" />
\n            <span>Send to and share your contacts with MailChimp</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/mandrill.png\" alt=\"\" />
\n            <span>Send to and share your contacts with Mandrill</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/sparkpost.png\" alt=\"\" />
\n            <span>Send to and share your contacts with Sparkpost</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/printer.png\" alt=\"\" />
\n            <span>Send dynamic documents to your printer</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/serp.png\" alt=\"\" />
\n            <span>Manage Serp campaigns</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/addthis.png\" alt=\"\" />
\n            <span>Integrate with AddThis sharing tool</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/twitter.png\" alt=\"\" />
\n            <span>Post and display from and to twitter</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/linkedin.png\" alt=\"\" />
\n            <span>Post and display from and to LinkedIn</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/flickr.png\" alt=\"\" />
\n            <span>Post and display from and to Flickr</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/pinterest.png\" alt=\"\" />
\n            <span>Post and display from and to Pinterest</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/youtube.png\" alt=\"\" />
\n            <span>Post and display from and to YouTube</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/tripadvisor.png\" alt=\"\" />
\n            <span>Post and display from and to TripAdvisor</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/snapchat.png\" alt=\"\" />
\n            <span>Post and display from and to Snapchat</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/slaask.png\" alt=\"\" />
\n            <span>Post and display from and to Slaask</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/twilio.png\" alt=\"\" />
\n            <span>Send and receive sms using Twilio</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/word.png\" alt=\"\" />
\n            <span>Create docx files from editable templates</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/pdf.png\" alt=\"\" />
\n            <span>Generate custom pdfs</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/stripe.png\" alt=\"\" />
\n            <span>Receive and send payments via Stripe</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/paypal.png\" alt=\"\" />
\n            <span>Receive and send payments via PayPal</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/sagepay.png\" alt=\"\" />
\n            <span>Receive and send payments via sagePay</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/realex.png\" alt=\"\" />
\n            <span>Receive and send payments via Realex payments</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/dcs.png\" alt=\"\" />
\n            <span>Integrate your vec contacts from DCS</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/weather.png\" alt=\"\" />
\n            <span>Display weather reports
\n        </li>
\n    </ul>
\n</section>
\n
\n<section class=\"platform-section\">
\n    <h3 id=\"section-Security\">Security</h3>
\n
\n    <ul class=\"platform-icons\">
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/secure_globe.png\" alt=\"\" />
\n            <span>IP spam and whitelisting</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/secure_file.png\" alt=\"\" />
\n            <span>Stop form spam with CAPTCHAs</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/footprints.png\" alt=\"\" />
\n            <span>Track all activities</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/arrows.png\" alt=\"\" />
\n            <span>Auto ban visitors on misuse</span>
\n        </li>
\n    </ul>
\n</section>
\n
\n<section class=\"platform-section\">
\n    <h3 id=\"section-Automation\">Automation</h3>
\n
\n    <ul class=\"platform-icons\">
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/clock.png\" alt=\"\" />
\n            <span>Automate regular housekeeping</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/database_backup.png\" alt=\"\" />
\n            <span>Database backups</span>
\n        </li>
\n    </ul>
\n</section>
\n
\n<section class=\"platform-section\">
\n    <h3 id=\"section-Support\">Support</h3>
\n
\n    <ul class=\"platform-icons\">
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/browsers.png\" alt=\"\" />
\n            <span>Support for multiple browsers</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/devices.png\" alt=\"\" />
\n            <span>Runs on multiple devices</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/cookies.png\" alt=\"\" />
\n            <span>Cookie alert for compliance</span>
\n        </li>
\n    </ul>
\n</section>
\n
\n<section class=\"platform-section\">
\n    <h3 id=\"section-Messaging\">Messaging</h3>
\n
\n    <ul class=\"platform-icons\">
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/sms.png\" alt=\"\" />
\n            <span>Send bulk texts to your contacts</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/email.png\" alt=\"\" />
\n            <span>Send bulk email to your contacts</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/notification.png\" alt=\"\" />
\n            <span>Send notifications to your users</span>
\n        </li>
\n    </ul>
\n</section>
\n
\n<section class=\"platform-section\">
\n    <h3 id=\"section-Search_Engine_Optimisation\">Search Engine Optimisation</h3>
\n
\n    <ul class=\"platform-icons\">
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/seo.png\" alt=\"\" />
\n            <span>Add SEO tags to all content</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/spider.png\" alt=\"\" />
\n            <span>Manage search engine spider crawl settings</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/custom_content.png\" alt=\"\" />
\n            <span>Add custom content to head and footer</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/redirect_arrow.png\" alt=\"\" />
\n            <span>Manage your urls and redirects</span>
\n        </li>
\n    </ul>
\n</section>
\n
\n<section class=\"platform-section\">
\n    <h3 id=\"section-Reporting\">Reporting</h3>
\n
\n    <ul class=\"platform-icons\">
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/report.png\" alt=\"\" />
\n            <span>Interactive dashboards</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/file.png\" alt=\"\" />
\n            <span>Generate docx, csv, excel or pdf</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/search.png\" alt=\"\" />
\n            <span>Search across the entire platform</span>
\n        </li>
\n    </ul>
\n</section>
\n
\n<section class=\"platform-section\">
\n    <h3 id=\"section-Design\">Design</h3>
\n
\n    <ul class=\"platform-icons\">
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/themes.png\" alt=\"\" />
\n            <span>Create custom themes</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/responsive.png\" alt=\"\" />
\n            <span>Fully mobile responsive</span>
\n        </li>
\n    </ul>
\n</section>
\n
\n<section class=\"platform-section\">
\n    <h3 id=\"section-Languages\">Languages</h3>
\n
\n    <ul class=\"platform-icons\">
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/globe.png\" alt=\"\" />
\n            <span>Support for all the major languages</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/infinity.png\" alt=\"\" />
\n            <span>Add unlimited no of languages</span>
\n        </li>
\n    </ul>
\n</section>
\n
\n<section class=\"platform-section\">
\n    <h3 id=\"section-Help\">Help</h3>
\n
\n    <ul class=\"platform-icons\">
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/compass.png\" alt=\"\" />
\n            <span>Clean and simple navigation</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/life_ring.png\" alt=\"\" />
\n            <span>Help at your finger tips</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/keyboard.png\" alt=\"\" />
\n            <span>Quick short key access to features</span>
\n        </li>
\n
\n        <li>
\n            <img src=\"/assets/ideabubble/images/platform/responsive.png\" alt=\"\" />
\n            <span>Experience our amazing after care</span>
\n        </li>
\n    </ul>
\n</section>'
);