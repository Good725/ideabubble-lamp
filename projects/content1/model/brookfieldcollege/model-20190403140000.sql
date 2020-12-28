/*
ts:2019-04-03 14:00:00
*/

UPDATE `engine_settings` SET `value_dev` = '0', `value_test` = '0', `value_stage` = '0', `value_live` = '0' WHERE `variable` = 'use_config_file';
UPDATE `engine_settings` SET `value_dev` = '0', `value_test` = '0', `value_stage` = '0', `value_live` = '0' WHERE `variable` = 'localisation_content_active';
UPDATE `engine_settings` SET `value_dev` = '',  `value_test` = '',  `value_stage` = '',  `value_live` = ''  WHERE `variable` = 'checkout_terms_and_conditions';
UPDATE `engine_settings` SET `value_dev` = '',  `value_test` = '',  `value_stage` = '',  `value_live` = ''  WHERE `variable` = 'page_footer';

INSERT IGNORE INTO `engine_settings_microsite_overwrites`
  (`setting`,                          `microsite_id`,            `environment`, `value`)
VALUES
  ('assets_folder_path',               'brookfieldlanguage',      'dev',   '43'),
  ('assets_folder_path',               'brookfieldinternational', 'dev',   '43'),

  ('home_page_course_categories_feed', 'brookfieldlanguage',      'dev',   '0'),
  ('home_page_course_categories_feed', 'brookfieldinternational', 'dev',   '0'),

  ('site_footer_logo',                 'brookfieldlanguage',      'dev',   'footer_logo-lang.svg'),
  ('site_footer_logo',                 'brookfieldinternational', 'dev',   'footer_logo-lang.svg'),

  ('localisation_content_active',      'brookfieldlanguage',      'dev',   '1'),

  ('checkout_terms_and_conditions',    'brookfieldlanguage',      'dev',   '<p>[locale:en]</p>\n\n<p>I understand and accept that the deposit is non-refundable and non-transferable.<br />By clicking &lsquo;Complete booking&rsquo;<br />you agree to the <a href="/terms-and-conditions.html" target="_blank">terms and conditions</a> and accept the &euro;2.00 charge.</p>\n\n<p>[locale:it]</p>\n\n<p>Ho capito e accettato che questi costi sono non rimborsabili e non trasferibili<br />Cliccando su completa la prenotazione accetti addebito di 2 euro</p>'),
  ('checkout_terms_and_conditions',    'brookfieldinternational', 'dev',   '<p>I understand and accept that the deposit is non-refundable and non-transferable.<br />By clicking &lsquo;Complete booking&rsquo;<br />you agree to the <a href="/terms-and-conditions.html" target="_blank">terms and conditions</a> and accept the &euro;2.00 charge.</p>'),

  ('page_footer',                      'brookfieldlanguage',      'dev',   '<p>[locale:en]</p>\n\n<h1 style="text-align:center">Earn your English certificate abroad&nbsp; &nbsp;<a class="button button\-\-continue inverse" href="/available-results-international">BOOK NOW</a></h1>\n\n<p>[locale:it]</p>\n\n<h1 style="text-align:center">Consegui il tuo Certificate all&#39;estero</h1>\n\n<h1 style="text-align:center"><a class="button button\-\-continue inverse" href="/available-results-international">SCEGLI IL TUO CORSO</a></h1>'),
  ('page_footer',                      'brookfieldinternational', 'dev',   '<h1 style="text-align:center">Earn your English certificate abroad&nbsp; &nbsp;<a class="button button\-\-continue inverse" href="/available-results-international">BOOK NOW</a></h1>'),




  ('assets_folder_path',               'brookfieldlanguage',      'test',  '43'),
  ('assets_folder_path',               'brookfieldinternational', 'test',  '43'),

  ('home_page_course_categories_feed', 'brookfieldlanguage',      'test',  '0'),
  ('home_page_course_categories_feed', 'brookfieldinternational', 'test',  '0'),

  ('site_footer_logo',                 'brookfieldlanguage',      'test',  'footer_logo-lang.svg'),
  ('site_footer_logo',                 'brookfieldinternational', 'test',  'footer_logo-lang.svg'),

  ('localisation_content_active',      'brookfieldlanguage',      'test',  '1'),

  ('checkout_terms_and_conditions',    'brookfieldlanguage',      'test',  '<p>[locale:en]</p>\n\n<p>I understand and accept that the deposit is non-refundable and non-transferable.<br />By clicking &lsquo;Complete booking&rsquo;<br />you agree to the <a href="/terms-and-conditions.html" target="_blank">terms and conditions</a> and accept the &euro;2.00 charge.</p>\n\n<p>[locale:it]</p>\n\n<p>Ho capito e accettato che questi costi sono non rimborsabili e non trasferibili<br />Cliccando su completa la prenotazione accetti addebito di 2 euro</p>'),
  ('checkout_terms_and_conditions',    'brookfieldinternational', 'test',  '<p>I understand and accept that the deposit is non-refundable and non-transferable.<br />By clicking &lsquo;Complete booking&rsquo;<br />you agree to the <a href="/terms-and-conditions.html" target="_blank">terms and conditions</a> and accept the &euro;2.00 charge.</p>'),

  ('page_footer',                      'brookfieldlanguage',      'test',  '<p>[locale:en]</p>\n\n<h1 style="text-align:center">Earn your English certificate abroad&nbsp; &nbsp;<a class="button button\-\-continue inverse" href="/available-results-international">BOOK NOW</a></h1>\n\n<p>[locale:it]</p>\n\n<h1 style="text-align:center">Consegui il tuo Certificate all&#39;estero</h1>\n\n<h1 style="text-align:center"><a class="button button\-\-continue inverse" href="/available-results-international">SCEGLI IL TUO CORSO</a></h1>'),
  ('page_footer',                      'brookfieldinternational', 'test',  '<h1 style="text-align:center">Earn your English certificate abroad&nbsp; &nbsp;<a class="button button\-\-continue inverse" href="/available-results-international">BOOK NOW</a></h1>'),




  ('assets_folder_path',               'brookfieldlanguage',      'stage', '43'),
  ('assets_folder_path',               'brookfieldinternational', 'stage', '43'),

  ('home_page_course_categories_feed', 'brookfieldlanguage',      'stage', '0'),
  ('home_page_course_categories_feed', 'brookfieldinternational', 'stage', '0'),

  ('site_footer_logo',                 'brookfieldlanguage',      'stage', 'footer_logo-lang.svg'),
  ('site_footer_logo',                 'brookfieldinternational', 'stage', 'footer_logo-lang.svg'),

  ('localisation_content_active',      'brookfieldlanguage',      'stage', '1'),

  ('checkout_terms_and_conditions',    'brookfieldlanguage',      'stage', '<p>[locale:en]</p>\n\n<p>I understand and accept that the deposit is non-refundable and non-transferable.<br />By clicking &lsquo;Complete booking&rsquo;<br />you agree to the <a href="/terms-and-conditions.html" target="_blank">terms and conditions</a> and accept the &euro;2.00 charge.</p>\n\n<p>[locale:it]</p>\n\n<p>Ho capito e accettato che questi costi sono non rimborsabili e non trasferibili<br />Cliccando su completa la prenotazione accetti addebito di 2 euro</p>'),
  ('checkout_terms_and_conditions',    'brookfieldinternational', 'stage', '<p>I understand and accept that the deposit is non-refundable and non-transferable.<br />By clicking &lsquo;Complete booking&rsquo;<br />you agree to the <a href="/terms-and-conditions.html" target="_blank">terms and conditions</a> and accept the &euro;2.00 charge.</p>'),

  ('page_footer',                      'brookfieldlanguage',      'stage', '<p>[locale:en]</p>\n\n<h1 style="text-align:center">Earn your English certificate abroad&nbsp; &nbsp;<a class="button button\-\-continue inverse" href="/available-results-international">BOOK NOW</a></h1>\n\n<p>[locale:it]</p>\n\n<h1 style="text-align:center">Consegui il tuo Certificate all&#39;estero</h1>\n\n<h1 style="text-align:center"><a class="button button\-\-continue inverse" href="/available-results-international">SCEGLI IL TUO CORSO</a></h1>'),
  ('page_footer',                      'brookfieldinternational', 'stage', '<h1 style="text-align:center">Earn your English certificate abroad&nbsp; &nbsp;<a class="button button\-\-continue inverse" href="/available-results-international">BOOK NOW</a></h1>'),




  ('assets_folder_path',               'brookfieldlanguage',      'live',  '43'),
  ('assets_folder_path',               'brookfieldinternational', 'live',  '43'),

  ('home_page_course_categories_feed', 'brookfieldlanguage',      'live',  '0'),
  ('home_page_course_categories_feed', 'brookfieldinternational', 'live',  '0'),

  ('site_footer_logo',                 'brookfieldlanguage',      'live',  'footer_logo-lang.svg'),
  ('site_footer_logo',                 'brookfieldinternational', 'live',  'footer_logo-lang.svg'),

  ('localisation_content_active',      'brookfieldlanguage',      'live',  '1'),

  ('checkout_terms_and_conditions',    'brookfieldlanguage',      'live',  '<p>[locale:en]</p>\n\n<p>I understand and accept that the deposit is non-refundable and non-transferable.<br />By clicking &lsquo;Complete booking&rsquo;<br />you agree to the <a href="/terms-and-conditions.html" target="_blank">terms and conditions</a> and accept the &euro;2.00 charge.</p>\n\n<p>[locale:it]</p>\n\n<p>Ho capito e accettato che questi costi sono non rimborsabili e non trasferibili<br />Cliccando su completa la prenotazione accetti addebito di 2 euro</p>'),
  ('checkout_terms_and_conditions',    'brookfieldinternational', 'live',  '<p>I understand and accept that the deposit is non-refundable and non-transferable.<br />By clicking &lsquo;Complete booking&rsquo;<br />you agree to the <a href="/terms-and-conditions.html" target="_blank">terms and conditions</a> and accept the &euro;2.00 charge.</p>'),

  ('page_footer',                      'brookfieldlanguage',      'live',  '<p>[locale:en]</p>\n\n<h1 style="text-align:center">Earn your English certificate abroad&nbsp; &nbsp;<a class="button button\-\-continue inverse" href="/available-results-international">BOOK NOW</a></h1>\n\n<p>[locale:it]</p>\n\n<h1 style="text-align:center">Consegui il tuo Certificate all&#39;estero</h1>\n\n<h1 style="text-align:center"><a class="button button\-\-continue inverse" href="/available-results-international">SCEGLI IL TUO CORSO</a></h1>'),
  ('page_footer',                      'brookfieldinternational', 'live',  '<h1 style="text-align:center">Earn your English certificate abroad&nbsp; &nbsp;<a class="button button\-\-continue inverse" href="/available-results-international">BOOK NOW</a></h1>')
;

UPDATE `engine_settings` SET `value_dev` = '1', `value_test` = '1', `value_stage` = '1', `value_live` = '1' WHERE `variable` = 'bookings_checkout_login_require';

INSERT IGNORE INTO `engine_settings_microsite_overwrites`
  (`setting`,                         `microsite_id`,            `environment`, `value`)
VALUES
  ('bookings_checkout_login_require', 'brookfieldlanguage',      'dev',   '0'),
  ('bookings_checkout_login_require', 'brookfieldinternational', 'dev',   '0'),
  ('bookings_checkout_login_require', 'brookfieldlanguage',      'test',  '0'),
  ('bookings_checkout_login_require', 'brookfieldinternational', 'test',  '0'),
  ('bookings_checkout_login_require', 'brookfieldlanguage',      'stage', '0'),
  ('bookings_checkout_login_require', 'brookfieldinternational', 'stage', '0'),
  ('bookings_checkout_login_require', 'brookfieldlanguage',      'live',  '0'),
  ('bookings_checkout_login_require', 'brookfieldinternational', 'live',  '0')
;
