/*
ts:2017-10-31 18:01:00
*/
UPDATE
  `engine_settings`
SET
  `value_dev` = '<p>Make it simple with&nbsp; <img src="/assets/kes1/images/login-logo.png" alt="Kilmartin Educational Services"  width=\"185\" height=\"49\" /></p>
\n
\n<ul class=\"hidden-xs\">
\n    <li>Special offer just for you</li>
\n    <li>Faster booking</li>
\n    <li>Save your searches</li>
\n    <li>Book the best courses for your child</li>
\n    <li>Find the best deals</li>
\n</ul>'
WHERE
  `variable` = 'login_form_offers_text'
;

UPDATE
  `engine_settings`
SET
  `value_dev` = '<p style=\"text-align: center;\"><img src=\"/assets/kes1/images/login-logo.png\" alt=\"Kilmartin Educational Services\"  width=\"185\" height=\"49\" /></p>
\n
\n<p style="text-align: center;"><strong>Maximise your exam results</strong></p>
\n
\n<ul class=\"hidden-xs\" style=\"font-size: .666667em;\">
\n    <li>Find the best deals</li>
\n    <li>Save time and book online</li>
\n    <li>Special offers just for you</li>
\n    <li>Provide an ideal and friendly study environment</li>
\n    <li>Join some of Ireland&#39;s top teachers</li>
\n    <li>Revise vital exam topics with weekly grinds and revision courses</li>
\n    <li>Plan your future with our Career Guidance Services.</li>
\n</ul>'
WHERE
  `variable` = 'login_form_offers_text'
;

UPDATE
  `engine_settings`
SET
  `value_test`   = `value_dev`,
  `value_stage`  = `value_dev`,
  `value_live`  = `value_dev`
WHERE
  `variable` = 'login_form_offers_text'
;
