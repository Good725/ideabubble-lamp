/*
ts:2017-08-29 12:15:00
*/

INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`, `content`) VALUES (
  'our-work',
  'Our Work',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT IFNULL(`id`, 1) FROM `plugin_pages_layouts` WHERE `layout` = 'content' LIMIT 1),
  (SELECT IFNULL(`id`, 1) FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1),
  '
<div class=\"our_products no-fix-container\">
\n    <div class=\"our_products-row\">
\n        <div class=\"our_products-item\" style=\"background-image: url(\'/assets/ideabubble/images/products/we_build_it_banner.png\');\">
\n            <div class=\"our_products-overlay\">
\n                <h1>You Dream It, We Build It</h1>
\n            </div>
\n        </div>
\n    </div>
\n
\n    <div class=\"our_products-logos\">
\n        <ul>
\n            <li>
\n                <a href=\"http://ailesburyhairclinic.ie/\" target=\"_blank\">
\n                    <img src=\"/assets/ideabubble/images/products/logos/ailesbury.png\" alt=\"Ailesbury Hair Clinic\" />
\n                </a>
\n            </li>
\n
\n            <li>
\n                <a href=\"http://yachtsman.ie/\" target=\"_blank\">
\n                    <img src=\"/assets/ideabubble/images/products/logos/yachtsman.png\" alt=\"Yachtsman Euromarine\" />
\n                </a>
\n            </li>
\n
\n            <li>
\n                <a href=\"http://kes.ie/\" target=\"_blank\">
\n                    <img src=\"/assets/ideabubble/images/products/logos/kilmartin.png\" alt=\"Kilmartin Educational Services\" />
\n                </a>
\n            </li>
\n            <li>
\n                <a href=\"http://limerickschoolofmusic.ie/\" target=\"_blank\">
\n                    <img src=\"/assets/ideabubble/images/products/logos/limerickschoolofmusic.png\" alt=\"Limerick School of Music\" />
\n                </a>
\n            </li>
\n
\n            <li>
\n                <a href=\"http://navanballet.com/\" target=\"_blank\">
\n                    <img src=\"/assets/ideabubble/images/products/logos/navanschoolofballet.png\" alt=\"Navan School of Ballet\" />
\n                </a>
\n            </li>
\n            <li>
\n                <a href=\"http://rapecrisis.ie/\" target=\"_blank\">
\n                    <img src=\"/assets/ideabubble/images/products/logos/rapecrisis.png\" alt=\"Rape Crisis Midwest\" />
\n                </a>
\n            </li>
\n
\n            <li>
\n                <a href=\"http://uticket.ie/\" target=\"_blank\">
\n                    <img src=\"/assets/ideabubble/images/products/logos/uticket.png\" alt=\"uTicket\" />
\n                </a>
\n            </li>
\n        </ul>
\n    </div>
\n
\n    <!\-\- Row: uTicket \-\->
\n    <div class=\"our_products-row\">
\n        <div class=\"our_products-item\" style=\"background-image: url(\'/assets/ideabubble/images/products/product-uticket.png\');\">
\n            <div class=\"our_products-overlay\">
\n                <img class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/uticket2.png\" alt=\"uTicket\" />
\n
\n                <p>Development of a new event-ticket platform for gig promoters with ongoing support and technical consultancy for Ed and his team.</p>
\n
\n                <p><a class=\"our_products-view\" href=\"http://uticket.ie\" target=\"_blank\">View Website</a></p>
\n            </div>
\n        </div>
\n    </div>
\n
\n    <!\-\- Row: Amber SOS and Ailesbury \-\->
\n    <div class=\"our_products-row\">
\n        <div class=\"our_products-item\" style=\"background-image: url(\'/assets/ideabubble/images/products/product-amber.png\');\">
\n            <div class=\"our_products-overlay\">
\n                <img class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/ambersos2.png\" alt=\"Amber SOS\" />
\n
\n                <p>Development of a new retail platform for amber products.</p>
\n
\n                <p><a class=\"our_products-view\" href=\"http://ambersos.com\" target=\"_blank\">View Website</a></p>
\n            </div>
\n        </div>
\n
\n        <div class=\"our_products-item\" style=\"background-image: url(\'/assets/ideabubble/images/products/product-ailesbury.png\');\">
\n            <div class=\"our_products-overlay\">
\n                <img class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/ailesbury2.png\" alt=\"Ailesbury Hair Clinic\" />
\n
\n                <p>Development of a bespoke brochure website.</p>
\n
\n                <p><a class=\"our_products-view\" href=\"http://ailesburyhairclinic.ie\" target=\"_blank\">View Website</a></p>
\n            </div>
\n        </div>
\n    </div>
\n
\n    <!\-\- Row: Yachtsman \-\->
\n    <div class=\"our_products-row\">
\n        <div class=\"our_products-item\" style=\"background-image: url(\'/assets/ideabubble/images/products/product-yachtsman.png\');\">
\n            <div class=\"our_products-overlay\">
\n                <img class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/yachtsman2.png\" alt=\"Yachtsman Euromarine\" />
\n
\n                <p>Development of a full-back insurance policy workflow product with 3 bespoke brochure websites and ongoing support and technical consultancy for Matt and his team</p>
\n
\n                <p><a class=\"our_products-view\" href=\"http://yachtsman.ie\" target=\"_blank\">View Website</a></p>
\n            </div>
\n        </div>
\n    </div>
\n
\n    <!\-\- Row: Kilmartin and Limerick School of Music \-\->
\n    <div class=\"our_products-row\">
\n        <div class=\"our_products-item\" style=\"background-image: url(\'/assets/ideabubble/images/products/product-kilmartin.png\');\">
\n            <div class=\"our_products-overlay\">
\n                <img class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/kilmartin2.png\" alt=\"Kilmartin Educational Services\" />
\n
\n                <p>Development of a full back-office course-management product with bespoke brochure website, support and technical consultancy for Julie and her team.</p>
\n
\n                <p><a class=\"our_products-view\" href=\"http://kes.ie\" target=\"_blank\">View Website</a></p>
\n            </div>
\n        </div>
\n
\n        <div class=\"our_products-item\" style=\"background-image: url(\'/assets/ideabubble/images/products/product-limerickschoolofmusic.png\');\">
\n            <div class=\"our_products-overlay\">
\n                <img class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/limerickschoolofmusic2.png\" alt=\"Limerick School of Music. Explore the possibilities!\" />
\n
\n                <p>Development of a full back-office course-management product with bespoke brochure website and ongoing support for David and the group.</p>
\n
\n                <p><a class=\"our_products-view\" href=\"http://limerickschoolofmusic.ie\" target=\"_blank\">View Website</a></p>
\n            </div>
\n        </div>
\n    </div>
\n
\n    <!\-\- Row: Rent a Cottage \-\->
\n    <div class=\"our_products-row\">
\n        <div class=\"our_products-item\" style=\"background-image: url(\'/assets/ideabubble/images/products/product-rentacottage.png\');\">
\n            <div class=\"our_products-overlay\">
\n                <img class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/rentacottage2.png\" alt=\"Rent an Irish Cottage\" />
\n
\n                <p>Development of a full back-office property-management product with a bespoke brochure website and ongoing support for Margaret and her team.</p>
\n
\n                <p><a class=\"our_products-view\" href=\"http://rentacottage.ie\" target=\"_blank\">View Website</a></p>
\n            </div>
\n        </div>
\n    </div>
\n
\n    <!\-\- Row: PCSystems \-\->
\n    <div class=\"our_products-row\">
\n        <div class=\"our_products-item\" style=\"background-image: url(\'/assets/ideabubble/images/products/product-pcsystems.png\');\">
\n            <div class=\"our_products-overlay\">
\n                <img class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/pcsystems2.png\" alt=\"PC Systems\" />
\n
\n                <p>Development of Stock in the Channel integration and stock management back office with bespoke eCommerce website and ongoing technical support for Liz and the team.</p>
\n
\n                <p><a class=\"our_products-view\" href=\"http://kes.ie\" target=\"_blank\">View Website</a></p>
\n            </div>
\n        </div>
\n
\n        <div class=\"our_products-item\" style=\"background-image: url(\'/assets/ideabubble/images/products/product-tango.png\');\">
\n            <div class=\"our_products-overlay\">
\n                <img class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/tangotelecom2.png\" alt=\"Tango Telecom\" />
\n
\n                <p>Development of a bespoke brochure website for a fast-growing telecom group.</p>
\n
\n                <p><a class=\"our_products-view\" href=\"http://tangotelecom.com\" target=\"_blank\">View Website</a></p>
\n            </div>
\n        </div>
\n    </div>
\n
\n    <!\-\- Row: Donal Ryan \-\->
\n    <div class=\"our_products-row\">
\n        <div class=\"our_products-item\" style=\"background-image: url(\'/assets/ideabubble/images/products/product-donalryan.png\');\">
\n            <div class=\"our_products-overlay\">
\n                <img class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/donalryan2.png\" alt=\"Donal Ryan Motor Group\" />
\n
\n                <p>Development of Carzone integration and car management back office with bespoke brochure website and ongoing support for Kathrina and the team.</p>
\n
\n                <p><a class=\"our_products-view\" href=\"http://donalryan.ie\" target=\"_blank\">View Website</a></p>
\n            </div>
\n        </div>
\n    </div>
\n</div>'

);
