/*
ts:2017-07-10 10:55:00
*/

UPDATE `plugin_pages_pages`
SET
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `content`        = '<div style=\"text-align: center;\">
\n    <img src=\"/assets/kes1/img/thanks.jpg\" />
\n
\n    <h1 style=\"border: none; color: #0e2a6b;\">Thank you for booking with Julie’s</h1>
\n
\n    <p>We have confirmed your attendance to every class on your booking.<br/>
\n        If this changes, please update <a href=\"/frontend/contacts3/attendance\">your attendance.</a>
\n    </p>
\n
\n    <p>Looking forward to seeing you at Julie’s.</p>
\n
\n    <hr />
\n
\n    <h2 style=\"border: none; color: #222222; font-weight: normal;\">I just booked with Julie’s</h2>
\n
\n    <p>Spread a good word about us. Invite your friends to join our community.</p>
\n
\n    <p>
\n        <a href=\"https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fkes.ie%2F\" class=\"share_button share_button\-\-facebook\">
\n            Share on Facebook
\n        </a>
\n
\n        <a href=\"http://twitter.com/home/?status=I+just+booked+with+http%3A%2F%2Fjulies.ie.+Book+your+course+now%21\" class=\"share_button share_button\-\-twitter\">
\n            Share on Twitter
\n        </a>
\n    </p>
\n
\n    <hr />
\n
\n    <p>If you would like to book another course with us, check out our <a href=\"/available-results.html\">availability</a>.</p>
\n</div>'
WHERE `name_tag` IN ('thankyou', 'thankyou.html');


UPDATE `plugin_pages_pages`
SET
  `last_modified`   = CURRENT_TIMESTAMP,
  `modified_by`     = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `seo_description` = 'I just booked with Julies.ie. Book your course now!',
  `content`         = '<div style=\"text-align: center;\">
\n    <img src=\"/assets/kes1/img/thanks.jpg\" />
\n
\n    <h1 style=\"border: none; color: #0e2a6b;\">Thank you for booking with Julie’s</h1>
\n
\n    <p>We have confirmed your attendance to every class on your booking.<br/>
\n        If this changes, please update <a href=\"/frontend/contacts3/attendance\">your attendance.</a>
\n    </p>
\n
\n    <p>Looking forward to seeing you at Julie’s.</p>
\n
\n    <hr />
\n
\n    <h2 style=\"border: none; color: #222222; font-weight: normal;\">I just booked with Julie’s</h2>
\n
\n    <p>Spread a good word about us. Invite your friends to join our community.</p>
\n
\n    <p>
\n        <a href=\"https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fkes.ie%2F\home.html%3Fog_data%3Dthankyou" class=\"share_button share_button\-\-facebook\">
\n            Share on Facebook
\n        </a>
\n
\n        <a href=\"http://twitter.com/home/?status=I+just+booked+with+http%3A%2F%2Fjulies.ie.+Book+your+course+now%21\" class=\"share_button share_button\-\-twitter\">
\n            Share on Twitter
\n        </a>
\n    </p>
\n
\n    <hr />
\n
\n    <p>If you would like to book another course with us, check out our <a href=\"/available-results.html\">availability</a>.</p>
\n</div>'
WHERE `name_tag` IN ('thankyou', 'thankyou.html');