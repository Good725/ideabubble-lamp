/*
ts:2016-02-23 11:04:00
*/

UPDATE `plugin_panels`
  SET
    link_url = '/search-results.html?facilities_all[]=Walking distance to beach', link_id = 0
  WHERE `text` like '%Beach%';
