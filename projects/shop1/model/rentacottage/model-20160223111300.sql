/*
ts:2016-02-23 11:13:00
*/

UPDATE `plugin_panels`
  SET
    link_url = '/search-results.html?result_format=map#search-criteria', link_id = 0
  WHERE `text` like '%Browse Properties%';
