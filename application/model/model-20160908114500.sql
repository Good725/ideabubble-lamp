/*
ts:2016-09-08 11:45:00
*/

UPDATE IGNORE `engine_settings`
SET
  `value_live` ='Powered by <a href=\"https://ideabubble.ie\">Idea Bubble</a>',
  `value_stage`='Powered by <a href=\"https://ideabubble.ie\">Idea Bubble</a>',
  `value_test` ='Powered by <a href=\"https://ideabubble.ie\">Idea Bubble</a>',
  `value_dev`  ='Powered by <a href=\"https://ideabubble.ie\">Idea Bubble</a>',
  `default`    ='Powered by <a href=\"https://ideabubble.ie\">Idea Bubble</a>'
WHERE
  `variable`='cms_copyright';
