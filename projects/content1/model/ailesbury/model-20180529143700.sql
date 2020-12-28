/*
ts:2018-05-29 14:37:00
*/

UPDATE plugin_panels
  SET
    text=REPLACE(text, '</ul>', '<li><span>[CAPTCHA]</span></li></ul>')
  WHERE title = 'Enquire Today';

