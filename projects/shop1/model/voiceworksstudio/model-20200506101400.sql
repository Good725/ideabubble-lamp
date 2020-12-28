/*
ts:2020-05-06 10:14:00
*/

drop temporary table if exists cids;
create temporary table cids as
(select
		gw.id, gw.contact_id, p.id as pid, r.role_id
	from plugin_contacts3_has_paymentgw gw
		inner join plugin_contacts3_contacts c on gw.contact_id = c.id
		inner join plugin_contacts3_contact_has_roles r on c.id = r.contact_id
		left join plugin_contacts3_family f on f.family_id = c.family_id
		left join plugin_contacts3_contacts p on f.primary_contact_id = p.id
	where p.id <> gw.contact_id and role_id = 2
);
update plugin_contacts3_has_paymentgw
	inner join cids on plugin_contacts3_has_paymentgw.id = cids.id
	set plugin_contacts3_has_paymentgw.contact_id = cids.pid;
