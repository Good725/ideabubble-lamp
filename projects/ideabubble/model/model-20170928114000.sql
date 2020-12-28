/*
ts:2017-08-29 16:15:00
*/

-- Form that triggers the "Start Your Project" modal
INSERT INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `options`, `deleted`, `publish`, `date_created`, `date_modified`, `form_id`) VALUES
(
  'new_project_enquiry_trigger',
  'javascript:return false;',
  'POST',
  '<li><label for=\"campaign-name\" class=\"sr-only\">Name</label><input type=\"text\" class=\"form-input validate[required]\" placeholder=\"Name*\" id=\"campaign-name\"></li>
\n<li><button type=\"button\" class=\"btn btn-primary\" data-toggle=\"ib-modal\" data-target=\"#modal-start_your_project\">Get Started</button></li>',
  'redirect:|failpage:',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  'new_project_enquiry_trigger_form'
);

-- Update pages to use the form-builder form
UPDATE
  `plugin_pages_pages`
SET
  `content` = '<div class=\"fix-container theme-form\" style=\"max-width: 846px;\">
\n    <div style=\"text-align: center;\">
\n        <h1>The website platform made for you</h1>
\n
\n        <p style=\"font-size: 1.6em; margin: 1em;\">Whether you advertise online, on social media, in store or out of the trunk of your car, Idea Bubble has you covered.</p>
\n    </div>
\n
\n    <div class=\"form-start_your_project-trigger\">{form-new_project_enquiry_trigger}</div>
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
WHERE
  `name_tag` = 'your-website-platform';

UPDATE
  `plugin_pages_pages`
SET
  `content` =   '<div class=\"fix-container theme-form\" style=\"max-width: 846px;\">
\n    <div style=\"text-align: center;\">
\n        <h1>Want to bring smiles to your customers</h1>
\n
\n        <p style=\"font-size: 1.6em; margin: 1em;\">Whether you deliver websites, digital solutions, marketing campaigns,
\n Idea Bubble has you covered.</p>
\n    </div>
\n
\n    <div class=\"form-start_your_project-trigger\">{form-new_project_enquiry_trigger}</div>
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
WHERE
  `name_tag` = 'your-development-partner';
