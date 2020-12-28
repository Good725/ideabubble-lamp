/*
ts:2018-05-23 17:00:00
*/

INSERT INTO `engine_settings` (`variable`, `name`, `note`, `type`, `group`) VALUES (
  'sign_up_disclaimer_text',
  'Sign up disclaimer text',
  'Disclaimer text to appear in the sign-up form, such as links to the terms of use',
  'wysiwyg',
  'Engine'
);

INSERT INTO `engine_settings` (`variable`, `name`, `note`, `type`, `group`) VALUES (
  'login_form_intro_text',
  'Log-in form intro text',
  'Text to appear at the top of the log-in form',
  'wysiwyg',
  'Engine'
), (
  'signup_form_intro_text',
  'Sign-up form intro text',
  'Text to appear at the top of the sign-up form',
  'wysiwyg',
  'Engine'
);
