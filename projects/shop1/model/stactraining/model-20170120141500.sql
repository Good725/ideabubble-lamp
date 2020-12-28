/*
ts:2017-01-20 14:15:00
*/

UPDATE `plugin_pages_pages` SET
  `content`       = '<h1>Contact Us</h1>  <div class=\"contact_us-columns\"> <div class=\"contact_us-column\"> <h4>Limerick, Head Office &amp; Training Centre</h4>  <p>Unit 11A Ballycummin Village,</p>  <p>Raheen,</p>  <p>Limerick</p>  <p>Phone: 061-595290</p>  <p>Mobile: 086-8187403</p>  <p>Email: info@stac.ie</p> </div>  <div class=\"contact_us-column\"> <h4>Wicklow Regional Office</h4>  <p>5 Charvey Lane,</p>  <p>Rathnew,</p>  <p>Co. Wicklow</p>  <p>Phone: 0404-32847</p>  <p>Mobile: 089-4128681</p>  <p>Email: leonard@stac.ie</p> </div>  <div class=\"contact_us-column\"> <h4>Dublin (Training Room Only)</h4>  <p>Muscular Dystophy Ireland</p>  <p>75 Lucan Road,</p>  <p>Chapelizod</p>  <p>Dublin D20 DR77</p> </div> </div>  <h2>Make an Online Enquiry</h2>  <div class=\"formrt\">{form-1}</div>',
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
WHERE
  `name_tag` IN ('contact-us', 'contact-us.html')
;

