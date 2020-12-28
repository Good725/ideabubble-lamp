/*
ts:2019-09-05 10:30:00
*/

UPDATE IGNORE `engine_settings`
SET
  `value_live` ='Powered by <a href=\"https://courseco.co\">CourseCo v2.62</a>',
  `value_stage`='Powered by <a href=\"https://courseco.co\">CourseCo v2.62</a>',
  `value_test` ='Powered by <a href=\"https://courseco.co\">CourseCo v2.62</a>',
  `value_dev`  ='Powered by <a href=\"https://courseco.co\">CourseCo v2.62</a>',
  `default`    ='Powered by <a href=\"https://courseco.co\">CourseCo v2.62</a>'
WHERE
  `variable`='cms_copyright';
