/*
ts:2016-02-18 09:57:00
*/

UPDATE `plugin_panels`
  SET
    link_url = '/search-results.html?suitabilities_all[]=Wheelchair Friendly'
  WHERE `text` like '%Wheelchair%';

UPDATE `plugin_panels`
  SET
    link_url = '/search-results.html?suitabilities_all[]=Pets welcome (€20 payable on arrival)'
  WHERE `text` like '%Pet friendly%';

UPDATE `plugin_panels`
  SET
    link_url = '/search-results.html?facilities_all[]=Golf (within 3km)'
  WHERE `text` like '%Golf%';

UPDATE `plugin_panels`
  SET
    link_url = '/search-results.html?facilities_any[]=Beach nearby&facilities_any[]=Peeble Beach&facilities_any[]=Walking distance to beach&facilities_any[]=Short drive to beach', link_id = 0
  WHERE `text` like '%Beach%';
