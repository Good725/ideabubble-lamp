/*
ts:2017-06-27 11:50:00
*/

UPDATE `plugin_pages_pages` SET
  `content`       = '<h1>Contact Us</h1>
\n<div class=\"contact_us-columns\">
\n	<div class=\"contact_us-column\">
\n		<h4>Limerick, Head Office &amp; Training Centre</h4>
\n		<p>Unit 11A Ballycummin Village,</p>
\n		<p>Raheen,</p>
\n		<p>Limerick</p>
\n		<p>Phone: 061-595290</p>
\n		<p>Mobile: 086-8187403</p>
\n		<p>Email: info@stac.ie</p>
\n	</div>
\n	<div class=\"contact_us-column\">
\n		<h4>Wicklow Regional Office</h4>
\n			<p>5 Charvey Lane,</p>
\n			<p>Rathnew,</p>
\n			<p>Co. Wicklow</p>
\n			<p>Phone: 0404-32847</p>
\n			<p>Mobile: 089-4128681</p>
\n			<p>Email: leonard@stac.ie</p>
\n		</div>
\n		<div class=\"contact_us-column\">
\n			<h4>Dublin (Training Room Only)</h4>
\n			<p>Muscular Dystophy Ireland</p>
\n			<p>75 Lucan Road,</p>
\n			<p>Chapelizod</p>
\n			<p>Dublin D20 DR77</p>
\n		</div>
\n		<div class=\"contact_us-column\">
\n			<h4>Carlow (Training Room Only)</h4>
\n			<p>Carlow Enterprise Centre</p>
\n			<p>Enterprise House</p>
\n			<p>O&#39;Brien Rd.</p>
\n			<p>Carlow</p>
\n		</div>
\n	</div>
\n<h2>Make an Online Enquiry</h2>
\n<div class=\"formrt\">{form-1}</div>',
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
WHERE
  `name_tag` IN ('contact-us', 'contact-us.html')
;

