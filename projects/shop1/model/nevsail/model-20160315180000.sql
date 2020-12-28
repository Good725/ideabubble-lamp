/*
ts:2016-03-15 18:00:00
*/

UPDATE IGNORE `settings` SET
  `value_dev`   = 'https://www.tripadvisor.ie/Attraction_Review-g212520-d2302777-Reviews-Nevsail_Watersports-Kilkee_County_Clare.html',
  `value_test`  = 'https://www.tripadvisor.ie/Attraction_Review-g212520-d2302777-Reviews-Nevsail_Watersports-Kilkee_County_Clare.html',
  `value_stage` = 'https://www.tripadvisor.ie/Attraction_Review-g212520-d2302777-Reviews-Nevsail_Watersports-Kilkee_County_Clare.html',
  `value_live`  = 'https://www.tripadvisor.ie/Attraction_Review-g212520-d2302777-Reviews-Nevsail_Watersports-Kilkee_County_Clare.html'
WHERE `variable` = 'tripadvisor_url';
