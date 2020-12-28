/*
ts:2017-09-17 11:30:00
*/

INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`, `content`) VALUES (
  'your-development-partner',
  'Your Development Partner',
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
\n        <h1>Want to bring smiles to your customers</h1>
\n
\n        <p style=\"font-size: 1.6em; margin: 1em;\">Whether you deliver websites, digital solutions, marketing campaigns,
\n Idea Bubble has you covered.</p>
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
\n    <p><small>Experience our fresh approach to bringing smiles to technical projects</small></p>
\n</div>
\n
\n<section class=\"our_products\">
\n    <div class=\"our_products-row\" style=\"margin-top: 0;\">
\n        <div class=\"our_products-item\" style=\"background-image: url(\'/assets/ideabubble/images/campaign/boulder_push.png\');\">
\n            <div class=\"our_products-overlay our_products-overlay\-\-bottom_left\">
\n                <p style=\"font-size: 1.25em;\">Lorem ipsum dolar sit amet, consecteuer adipisicing, sed do euismod tempor incididunt ut labore et</p>
\n            </div>
\n        </div>
\n    </div>
\n</section>
\n
\n<section class=\"full-row\">
\n    <div class=\"fix-container ib-campaign\">
\n        <div class=\"ib-campaign-description\">
\n            <h2>Does your business have pains?</h2>
\n
\n            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla.</p>
\n        </div>
\n
\n        <div class=\"ib-campaign-image\">
\n            <img src=\"/assets/ideabubble/images/campaign/business_pains.png\" alt=\"Project paralysis, unhappy customers, high development costs, project overruns, hidden support charges, poor communication, large request backlogs, inability to scale, slow technical support, overwhelmed sales inbox, overwhelmed and frustrated?, stuck with repetitive issues\" />
\n        </div>
\n    </div>
\n</section>
\n
\n<section class=\"full-row\">
\n    <div class=\"rotate-img\" style=\"background: #f8f8f8;\"> </div>
\n
\n    <div class=\"fix-container ib-campaign\" style=\"margin-top: -6.3em; margin-bottom: -5em;\">
\n        <div class=\"ib-campaign-image\" style=\"-webkit-clip-path: polygon(0 11.5%, 100% 0, 100% 88.5%, 0 100%); clip-path: polygon(0 11.5%, 100% 0, 100% 88.5%, 0 100%); margin: -1.25em 0 -2.5em;\">
\n            <img src=\"/assets/ideabubble/images/campaign/helping_hand.png\" alt=\"\" />
\n        </div>
\n
\n        <div class=\"ib-campaign-description\">
\n            <h2>How we make you great</h2>
\n
\n            <p>As a company we define ourselves as a group that:</p>
\n
\n            <ul style=\"font-size: .9em;\">
\n                <li>Helps you overcome your goals</li>
\n                <li>Simplifies the technical bits</li>
\n                <li>Brings a vast experience to support you</li>
\n                <li>Provides one platform that delivers all -<br />yes that means just one login :)</li>
\n            </ul>
\n
\n            <p>We want you to succeed. Simple as that</p>
\n        </div>
\n    </div>
\n</section>
\n
\n<section class=\"full-row\" style=\"min-height: 10em; padding: 3.25em 0;\">
\n    <div class=\"rotate-img\"> </div>
\n
\n    <div class=\"fix-container theme-form\" style=\"text-align: center;\">
\n        <h2>Want to start growing your business?</h2>
\n        <p>&nbsp;</p>
\n
\n        <button type=\"button\" class=\"btn btn-primary\" data-toggle=\"ib-modal\" data-target=\"#modal-start_your_project\">Get Started</button>
\n    </div>
\n</section>
\n
\n<section class=\"full-row\" style=\"padding: 3.25em 0;\">
\n    <div class=\"rotate-img\" style=\"background: #f8f8f8;\"> </div>
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
\n                <strong class=\"head-bar\"><span>€7.2M+</span></strong>
\n                <p>Generated on IBCMS</p>
\n            </li>
\n        </ul>
\n
\n    </div>
\n</section>
\n
\n<div class=\"full-row our\-\-partners\" style=\"background: #fff;\">
\n    <div class=\"rotate-img\"> </div>
\n
\n    <div class=\"theme-heading\">
\n        <h2>Our Partners</h2>
\n    </div>
\n
\n    <p>{testimonialsfeed-Testimonials}</p>
\n</div>
\n
\n<div class=\"ib-modal\" id=\"modal-start_your_project\">
\n    <div class=\"ib-modal-dialog\">
\n        <button class=\"ib-modal-close\">	&#10005;</button>
\n
\n        <div class=\"ib-modal-content\">
\n            <h2>Start your project</h2>
\n
\n            <div class=\"form-vertical\">{form-new_project_enquiry}</div>
\n        </div>
\n    </div>
\n</div>'
);