/*
ts:2017-09-17 11:45:00
*/

DELIMITER ;;
UPDATE
  `plugin_pages_pages`
SET
  `last_modified` = CURRENT_TIMESTAMP,

  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),

  `content`       = '<div class=\"no-fix-container our_products\">
\n	<div class=\"our_products-row\">
\n		<div class=\"our_products-item\" style=\"background-image:url(\'https://ideabubble.ie/shared_media/ideabubble/media/photos/content/we_build_it_a.png\')\">
\n			<div class=\"our_products-overlay\">
\n				<h1>You Dream It, We Build It</h1>
\n			</div>
\n		</div>
\n	</div>
\n
\n	<div class=\"our_products-logos simplebox\">
\n		<div class=\"simplebox-columns\">
\n			<div class=\"simplebox-column simplebox-column-1\">
\n				<div class=\"simplebox-content\">
\n					<div class=\"simplebox-content-toolbar\"><button><img src=\"/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg\" style=\"height:12px; width:12px\" /></button></div>
\n
\n					<p><a href=\"http://ailesburyhairclinic.ie/\" target=\"_blank\"><img alt=\"Ailesbury Hair Clinic\" src=\"/assets/ideabubble/images/products/logos/ailesbury.png\" /></a></p>
\n				</div>
\n			</div>
\n
\n			<div class=\"simplebox-column simplebox-column-2\">
\n				<div class=\"simplebox-content\">
\n					<div class=\"simplebox-content-toolbar\"><button><img src=\"/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg\" style=\"height:12px; width:12px\" /></button></div>
\n
\n					<p><a href=\"http://yachtsman.ie/\" target=\"_blank\"><img alt=\"Yachtsman Euromarine\" src=\"/assets/ideabubble/images/products/logos/yachtsman.png\" /></a></p>
\n				</div>
\n			</div>
\n
\n			<div class=\"simplebox-column simplebox-column-3\">
\n				<div class=\"simplebox-content\">
\n					<div class=\"simplebox-content-toolbar\"><button><img src=\"/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg\" style=\"height:12px; width:12px\" /></button></div>
\n
\n					<p><a href=\"http://kes.ie/\" target=\"_blank\"><img alt=\"Kilmartin Educational Services\" src=\"/assets/ideabubble/images/products/logos/kilmartin.png\" /></a></p>
\n				</div>
\n			</div>
\n
\n			<div class=\"simplebox-column simplebox-column-4\">
\n				<div class=\"simplebox-content\">
\n					<div class=\"simplebox-content-toolbar\"><button><img src=\"/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg\" style=\"height:12px; width:12px\" /></button></div>
\n
\n					<p><a href=\"http://limerickschoolofmusic.ie/\" target=\"_blank\"><img alt=\"Limerick School of Music\" src=\"/assets/ideabubble/images/products/logos/limerickschoolofmusic.png\" /></a></p>
\n				</div>
\n			</div>
\n
\n			<div class=\"simplebox-column simplebox-column-5\">
\n				<div class=\"simplebox-content\">
\n					<div class=\"simplebox-content-toolbar\"><button><img src=\"/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg\" style=\"height:12px; width:12px\" /></button></div>
\n
\n					<p><a href=\"http://navanballet.com/\" target=\"_blank\"><img alt=\"Navan School of Ballet\" src=\"/assets/ideabubble/images/products/logos/navanschoolofballet.png\" /></a></p>
\n				</div>
\n			</div>
\n
\n			<div class=\"simplebox-column simplebox-column-6\">
\n				<div class=\"simplebox-content\">
\n					<div class=\"simplebox-content-toolbar\"><button><img src=\"/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg\" style=\"height:12px; width:12px\" /></button></div>
\n
\n					<p><a href=\"http://rapecrisis.ie/\" target=\"_blank\"><img alt=\"Rape Crisis Midwest\" src=\"/assets/ideabubble/images/products/logos/rapecrisis.png\" /> </a></p>
\n				</div>
\n			</div>
\n
\n			<div class=\"simplebox-column simplebox-column-7\">
\n				<div class=\"simplebox-content\">
\n					<div class=\"simplebox-content-toolbar\"><button><img src=\"/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg\" style=\"height:12px; width:12px\" /></button></div>
\n
\n					<p><a href=\"http://uticket.ie/\" target=\"_blank\"><img alt=\"uTicket\" src=\"/assets/ideabubble/images/products/logos/uticket.png\" /> </a></p>
\n				</div>
\n			</div>
\n		</div>
\n	</div>
\n	<!\-\- Row: uTicket \-\->
\n
\n	<div class=\"our_products-row\">
\n		<div class=\"our_products-item\" style=\"background-image:url(\'https://ideabubble.ie/shared_media/ideabubble/media/photos/content/product-uticket-a.png\')\">
\n			<div class=\"our_products-overlay\"><img alt=\"uTicket\" class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/uticket2.png\" />
\n				<p>Development of a new event-ticket platform for gig promoters with ongoing support and technical consultancy for Ed and his team.</p>
\n
\n				<p><a class=\"our_products-view\" href=\"http://uticket.ie\" target=\"_blank\">View Website</a></p>
\n			</div>
\n		</div>
\n	</div>
\n	<!\-\- Row: Amber SOS and Ailesbury \-\->
\n
\n	<div class=\"our_products-row\">
\n		<div class=\"our_products-item\" style=\"background-image:url(\'/assets/ideabubble/images/products/product-amber.png\')\">
\n			<div class=\"our_products-overlay\"><img alt=\"Amber SOS\" class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/ambersos2.png\" />
\n				<p>Development of a new retail platform for amber products.</p>
\n
\n				<p><a class=\"our_products-view\" href=\"http://ambersos.com\" target=\"_blank\">View Website</a></p>
\n			</div>
\n		</div>
\n
\n		<div class=\"our_products-item\" style=\"background-image:url(\'/assets/ideabubble/images/products/product-ailesbury.png\')\">
\n			<div class=\"our_products-overlay\"><img alt=\"Ailesbury Hair Clinic\" class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/ailesbury2.png\" />
\n				<p>Development of a bespoke brochure website.</p>
\n
\n				<p><a class=\"our_products-view\" href=\"http://ailesburyhairclinic.ie\" target=\"_blank\">View Website</a></p>
\n			</div>
\n		</div>
\n	</div>
\n	<!\-\- Row: Yachtsman \-\->
\n
\n	<div class=\"our_products-row\">
\n		<div class=\"our_products-item\" style=\"background-image:url(\'/assets/ideabubble/images/products/product-yachtsman.png\')\">
\n			<div class=\"our_products-overlay\"><img alt=\"Yachtsman Euromarine\" class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/yachtsman2.png\" />
\n				<p>Development of a full-back insurance policy workflow product with 3 bespoke brochure websites and ongoing support and technical consultancy for Matt and his team</p>
\n
\n				<p><a class=\"our_products-view\" href=\"http://yachtsman.ie\" target=\"_blank\">View Website</a></p>
\n			</div>
\n		</div>
\n	</div>
\n	<!\-\- Row: Kilmartin and Limerick School of Music \-\->
\n
\n	<div class=\"our_products-row\">
\n		<div class=\"our_products-item\" style=\"background-image:url(\'/assets/ideabubble/images/products/product-kilmartin.png\')\">
\n			<div class=\"our_products-overlay\"><img alt=\"Kilmartin Educational Services\" class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/kilmartin2.png\" />
\n				<p>Development of a full back-office course-management product with bespoke brochure website, support and technical consultancy for Julie and her team.</p>
\n
\n				<p><a class=\"our_products-view\" href=\"http://kes.ie\" target=\"_blank\">View Website</a></p>
\n			</div>
\n		</div>
\n
\n		<div class=\"our_products-item\" style=\"background-image:url(\'/assets/ideabubble/images/products/product-limerickschoolofmusic.png\')\">
\n			<div class=\"our_products-overlay\"><img alt=\"Limerick School of Music. Explore the possibilities!\" class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/limerickschoolofmusic2.png\" />
\n				<p>Development of a full back-office course-management product with bespoke brochure website and ongoing support for David and the group.</p>
\n
\n				<p><a class=\"our_products-view\" href=\"http://limerickschoolofmusic.ie\" target=\"_blank\">View Website</a></p>
\n			</div>
\n		</div>
\n	</div>
\n	<!\-\- Row: Rent a Cottage \-\->
\n
\n	<div class=\"our_products-row\">
\n		<div class=\"our_products-item\" style=\"background-image:url(\'/assets/ideabubble/images/products/product-rentacottage.png\')\">
\n			<div class=\"our_products-overlay\"><img alt=\"Rent an Irish Cottage\" class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/rentacottage2.png\" />
\n				<p>Development of a full back-office property-management product with a bespoke brochure website and ongoing support for Margaret and her team.</p>
\n
\n				<p><a class=\"our_products-view\" href=\"http://rentacottage.ie\" target=\"_blank\">View Website</a></p>
\n			</div>
\n		</div>
\n	</div>
\n	<!\-\- Row: PCSystems \-\->
\n
\n	<div class=\"our_products-row\">
\n		<div class=\"our_products-item\" style=\"background-image:url(\'/assets/ideabubble/images/products/product-pcsystems.png\')\">
\n			<div class=\"our_products-overlay\"><img alt=\"PC Systems\" class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/pcsystems2.png\" />
\n				<p>Development of Stock in the Channel integration and stock management back office with bespoke eCommerce website and ongoing technical support for Liz and the team.</p>
\n
\n				<p><a class=\"our_products-view\" href=\"http://kes.ie\" target=\"_blank\">View Website</a></p>
\n			</div>
\n		</div>
\n
\n		<div class=\"our_products-item\" style=\"background-image:url(\'/assets/ideabubble/images/products/product-tango.png\')\">
\n			<div class=\"our_products-overlay\"><img alt=\"Tango Telecom\" class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/tangotelecom2.png\" />
\n				<p>Development of a bespoke brochure website for a fast-growing telecom group.</p>
\n
\n				<p><a class=\"our_products-view\" href=\"http://tangotelecom.com\" target=\"_blank\">View Website</a></p>
\n			</div>
\n		</div>
\n	</div>
\n	<!\-\- Row: Donal Ryan \-\->
\n
\n	<div class=\"our_products-row\">
\n		<div class=\"our_products-item\" style=\"background-image:url(\'/assets/ideabubble/images/products/product-donalryan.png\')\">
\n			<div class=\"our_products-overlay\"><img alt=\"Donal Ryan Motor Group\" class=\"our_products-logo\" src=\"/assets/ideabubble/images/products/logos/donalryan2.png\" />
\n				<p>Development of Carzone integration and car management back office with bespoke brochure website and ongoing support for Kathrina and the team.</p>
\n
\n				<p><a class=\"our_products-view\" href=\"http://donalryan.ie\" target=\"_blank\">View Website</a></p>
\n			</div>
\n		</div>
\n	</div>
\n</div>
\n'

WHERE
  `name_tag` = 'our-work'
AND
  `deleted` = 0
;;