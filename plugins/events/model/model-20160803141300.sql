/*
ts:2016-08-03 14:13:00
*/

UPDATE plugin_events_venues
  SET facebook_url = REPLACE(facebook_url, 'https://www.facebook.com/', '');
UPDATE plugin_events_venues
  SET twitter_url = REPLACE(twitter_url, 'https://twitter.com/', '');

UPDATE plugin_events_organizers
  SET facebook = REPLACE(facebook, 'https://www.facebook.com/', '');
UPDATE plugin_events_organizers
  SET twitter = REPLACE(twitter, 'https://twitter.com/', '');
