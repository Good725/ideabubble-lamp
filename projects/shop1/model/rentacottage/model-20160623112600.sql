/*
ts:2016-06-23 11:26:00
*/

UPDATE plugin_propman_properties p INNER JOIN engine_counties c ON p.city = c.name SET p.county_id = c.id	WHERE p.county_id is null or p.county_id = 0;
