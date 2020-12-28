/*
ts:2018-05-24 15:30:00
*/
UPDATE
  `engine_settings`
SET
  `value_dev` = '<p>By signing up, you agree to Kilmartin&#39;s <a href="/privacy-policy.html"><strong>privacy policy</strong></a> and <a href="/terms-of-use.html"><strong>terms of use</strong></a>.</p> '
WHERE
  `variable` = 'sign_up_disclaimer_text'
;

UPDATE
  `engine_settings`
SET
  `value_dev` = '<h3>Log in to your myKES account</h3> '
WHERE
  `variable` = 'login_form_intro_text'
;

UPDATE
  `engine_settings`
SET
  `value_dev` = '<h3>Sign up to myKES</h3>'
WHERE
  `variable` = 'signup_form_intro_text'
;

UPDATE
  `engine_settings`
SET
  `value_test`  = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live`  = `value_dev`
WHERE
  `variable` IN ('sign_up_disclaimer_text', 'login_form_intro_text', 'signup_form_intro_text')
;
