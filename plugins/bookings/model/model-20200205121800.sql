/*
ts:2020-02-05 12:18:00
*/

drop temporary table if exists plugin_ib_educate_bookings_has_card_clean_tmp;
create temporary table plugin_ib_educate_bookings_has_card_clean_tmp as
(select hc.* from plugin_ib_educate_bookings_has_card hc
	inner join
		(select max(id) as id, booking_id from plugin_ib_educate_bookings_has_card group by booking_id) lc on hc.id = lc.id);
delete from plugin_ib_educate_bookings_has_card;
insert into plugin_ib_educate_bookings_has_card (select * from plugin_ib_educate_bookings_has_card_clean_tmp);
