/*
ts:2019-11-11 11:46:00
*/

insert into engine_remote_sync
	(type, cms_id, remote_id)
	(select 'Xero-Contact', local_contact_id, remote_contact_id from plugin_remoteaccounting_contacts where remote_api = 'Xero');

insert into engine_remote_sync
	(type, cms_id, remote_id)
	(select 'Xero-Transaction', local_transaction_id, remote_transaction_id from plugin_remoteaccounting_transactions where remote_api = 'Xero');

insert into engine_remote_sync
	(type, cms_id, remote_id)
	(select 'Xero-Payment', local_payment_id, remote_payment_id from plugin_remoteaccounting_payments where remote_api = 'Xero');

DROP TABLE IF EXISTS plugin_remoteaccounting_contacts;
DROP TABLE IF EXISTS plugin_remoteaccounting_payments;
DROP TABLE IF EXISTS plugin_remoteaccounting_transactions;
