/*
ts:2018-07-24 17:00:00
*/

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'event_promoter',
  `value_test`  = 'event_promoter',
  `value_stage` = 'event_promoter',
  `value_live`  = 'event_promoter'
WHERE
  `variable`    = 'course_finder_mode'
;

UPDATE
  `engine_settings`
SET
  `value_dev`   = '12',
  `value_test`  = '12',
  `value_stage` = '12',
  `value_live`  = '12'
WHERE
  `variable`    = 'courses_results_per_page'
;

UPDATE
  `plugin_pages_pages`
SET
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `content`       = '<h1 style=\"text-align:center\">Thank you for your booking!<\/h1>

<p>Your tickets are available to download in <a href=\"\/admin\/events\/mytickets\">My Tickets<\/a>.<br \/>
We&#39;ve also send them to your email along with your receipt. Please check your junk\/spam mail account for this!!<br \/>
We provide mobile tickets, but you can print if needed.<br \/>
<strong>Please show your ticket on entry.<\/strong><\/p>

<p>Have an event coming up? <a href=\"\/admin\/events\/edit_event\/new\">Get started now<\/a>, it&#39;s free!<\/p>

<p><strong>Need help?<\/strong> See our <a href=\"\/ticket-buyer-help\">Ticket Buyer Help<\/a> section or click the <strong>HELP<\/strong> icon in the bottom right of the page to talk via instant chat or <a href=\"mailto:support@uticket.ie?subject=Ticket%20buyer%20help\">contact us<\/a> by email.<\/p>

<h3 style=\"text-align:center\">I just booked with uTicket<\/h3>

<p style=\"text-align:center\">Spread your good news<\/p>

<p style=\"text-align:center\"><a class=\"share_button share_button\-\-facebook\" href=\"https:\/\/www.facebook.com\/sharer\/sharer.php?u=http%3A%2F%2Futicket.ie%2Fhome.html%3Fog_data%3Dsuccess\">Share on Facebook <\/a> <a class=\"share_button share_button\-\-twitter\" href=\"http:\/\/twitter.com\/home\/?status=I+just+booked+with+http%3A%2F%2Futicket.ie.\"> Share on Twitter <\/a><\/p>

<p><a href=\"\/home.html\"><strong>Back to Home<\/strong><\/a><\/p>
'
WHERE
  `name_tag` IN ('success', 'success.html');


UPDATE
  `plugin_pages_pages`
SET
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'news')
WHERE
  `name_tag` IN ('news', 'news.html');


UPDATE
  `engine_settings`
SET
  `value_dev` = '1'
WHERE
  `variable`  = 'frontend_login_link'
;

UPDATE
  `engine_settings`
SET
  `value_dev` = 'Log in / Sign up'
WHERE
  `variable`  = 'frontend_login_link_text'
;

UPDATE
  `engine_settings`
SET
  `value_dev` = '<p>By signing up, you agree to uTicket&#39;s <a href="/privacy-policy.html"><strong>privacy policy</strong></a> and <a href="/terms-of-use.html"><strong>terms of use</strong></a>.</p>'
WHERE
  `variable`  = 'sign_up_disclaimer_text'
;

UPDATE
  `engine_settings`
SET
  `value_dev` = '<h3>Log in to your account</h3>'
WHERE
  `variable`  = 'login_form_intro_text'
;

UPDATE
  `engine_settings`
SET
  `value_dev` = '<h3>Sign up to your account</h3>'
WHERE
  `variable`  = 'signup_form_intro_text'
;

UPDATE
  `engine_settings`
SET
  `value_test`  = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live`  = `value_dev`
WHERE
  `variable` IN ('frontend_login_link', 'frontend_login_link_text', 'sign_up_disclaimer_text', 'login_form_intro_text', 'signup_form_intro_text')
;

INSERT IGNORE INTO
  `engine_localisation_languages` (`code`, `title`, `created_on`, `created_by`, `updated_on`, `updated_by`)
VALUES (
  'en',
  'English',
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);

INSERT IGNORE INTO
  `engine_localisation_translations` (`language_id`, `message_id`, `translation`)
VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code`    = 'en'),
  (SELECT `id` FROM `engine_localisation_messages`  WHERE `message` = 'Upcoming Events'),
  'uTicket recommends'
), (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code`    = 'en'),
  (SELECT `id` FROM `engine_localisation_messages`  WHERE `message` = 'Find Your Course'),
  'Find your event'
);