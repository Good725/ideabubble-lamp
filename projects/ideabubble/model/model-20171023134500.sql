/*
ts:2017-10-23 13:45:00
*/

UPDATE
  `plugin_pages_pages`
SET
  `content` = '<div class=\"fix-container theme-form\" style=\"max-width: 846px;\">
\n    <div style=\"text-align: center;\">
\n        <h1>Want to bring smiles to your customers</h1>
\n
\n        <p style=\"font-size: 1.6em; margin: 1em;\">Whether you deliver websites, digital solutions, marketing campaigns,
\n            Idea Bubble has you covered.</p>
\n    </div>
\n
\n    <div class=\"form-start_your_project-trigger\">{form-new_project_enquiry_trigger}</div>
\n
\n    <p><small>Experience our fresh approach to bringing smiles to technical projects</small></p>
\n</div>
\n
\n<section class=\"our_products\">
\n    <div class=\"our_products-row\">
\n        <div class=\"our_products-item\" style=\"background-image: url(\'/assets/ideabubble/images/campaign/boulder_push.png\');\">
\n            <div class=\"our_products-overlay our_products-overlay\-\-bottom_left\">
\n                <p style=\"font-size: 1.25em;\">Lorem ipsum dolar sit amet, consecteuer adipisicing, sed do euismod tempor incididunt ut labore et</p>
\n            </div>
\n        </div>
\n    </div>
\n</section>
\n
\n<section class=\"full-row diagonal\">
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
\n<section class=\"full-row diagonal\">
\n    <div class=\"ib-campaign\" style=\"align-items: normal; background: #eee; max-width: none;\">
\n        <div class=\"ib-campaign-image\" style=\"background-image: url(\'/assets/ideabubble/images/campaign/helping_hand.png\');\">&nbsp;</div>
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
\n    <div class=\"fix-container theme-form\" style=\"text-align: center;\">
\n        <h2>Want to start growing your business?</h2>
\n        <p> </p>
\n
\n        <button type=\"button\" class=\"btn btn-primary\" data-toggle=\"ib-modal\" data-target=\"#modal-start_your_project\">Get Started</button>
\n    </div>
\n</section>
\n
\n<section class=\"full-row diagonal\" style=\"background: #eee; margin: -120px 0 0; padding: 9.25em 0 3em;\">
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
\n        <button class=\"ib-modal-close\">	✕</button>
\n
\n        <div class=\"ib-modal-content\">
\n            <h2>Start your project</h2>
\n
\n            <div class=\"form-vertical\">{form-new_project_enquiry}</div>
\n        </div>
\n    </div>
\n</div>',
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by` = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `name_tag` = 'your-development-partner' AND `deleted` = 0;


UPDATE
  `plugin_pages_pages`
SET
  `content` = '<div class=\"fix-container theme-form\" style=\"max-width: 846px;\">
\n    <div style=\"text-align: center;\">
\n        <h1>A Powerful PLATFORM for your Business</h1>
\n
\n        <p style=\"font-size: 1.6em; margin: 1em;\">Launch your business on the right PLATFORM. Easy-to-use, flexible, 100&#39;s of integrations.</p>
\n    </div>
\n
\n    <div class=\"form-start_your_project-trigger\">{form-new_project_enquiry_trigger}</div>
\n
\n    <p><small>Test drive our PLATFORM today.<br />No risk, no credit card needed.</small></p>
\n</div>
\n
\n<section class=\"our_products\">
\n    <div class=\"our_products-row\" style=\"margin-top: 0;\">
\n        <div class=\"our_products-item our_products-item\-\-light\" style=\"background-image: url(\'/assets/ideabubble/images/products/ailesbury-banner.png\');\">
\n            <div class=\"our_products-overlay our_products-overlay\-\-bottom_left\">
\n                <p style=\"font-size: 1.25em;\">Lorraine Lambert of Ailesbury Hair Clinic uses IBCMS to advertise her business online.</p>
\n            </div>
\n        </div>
\n    </div>
\n</section>
\n
\n<section class=\"full-row diagonal\">
\n    <div class=\"fix-container ib-campaign\">
\n        <div class=\"ib-campaign-description\">
\n            <h2>Have the budget but don\'t want hassle?</h2>
\n
\n            <p>Every project is possible with our PLATFORM. It was built for end users so unlike other free website builders, it is easy to use but more&nbsp;importantly it is flexible so that you can scale your business online as you grow without massive structural costs.</p>
\n
\n            <p>From start to finish we will handle your project and ensure that any pains are solved giving you back your time. We look at the big picture, we want you to succeed and we want your investment online to be a long-term earner for you and your business.&nbsp;</p>
\n
\n            <p>Talk to us today, we are determined to help you grow your business!</p>
\n
\n            <p>&nbsp;</p>
\n
\n            <p><a href=\"/contact-us\" class=\"btn-primary\" style=\"background:#109aae;font-size: 16px; padding: .5em 3.2em\"><span class=\"icon_chat_alt\" aria-hidden=\"true\"></span> Let&#39;s talk</a></p>
\n        </div>
\n
\n        <div class=\"ib-campaign-image\">
\n            <img src=\"/assets/ideabubble/images/campaign/no_hassle.png\" alt=\"Monthly updates, superb aftercare, problem solvers, Irish, great listeners, innovative, real people , wea care, affordable, fast delivery\" />
\n        </div>
\n    </div>
\n</section>
\n
\n<section class=\"full-row diagonal\" style=\"background-color: #eee;\">
\n
\n    <div class=\"fix-container ib-campaign\" style=\"min-height: 1000px;\">
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
\n
\n    <div class=\"fix-container theme-form\" style=\"text-align: center;\">
\n        <h2>Get started growing your business</h2>
\n        <p> </p>
\n
\n        <button type=\"button\" class=\"btn btn-primary\" data-toggle=\"ib-modal\" data-target=\"#modal-start_your_project\">Get Started</button>
\n    </div>
\n</section>
\n
\n<section class=\"full-row diagonal\" style=\"background: #eee; margin: -120px 0 0; padding: 9.25em 0 3em;\">
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
\n<div class=\"ib-modal\" id=\"modal-start_your_project\">
\n    <div class=\"ib-modal-dialog\">
\n        <button class=\"ib-modal-close\">✕</button>
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
\n    <div class=\"theme-heading\">
\n        <h2>Our Partners</h2>
\n    </div>
\n
\n    <p>{testimonialsfeed-Testimonials}</p>
\n</div>',
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by` = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `name_tag` = 'your-website-platform' AND `deleted` = 0;

