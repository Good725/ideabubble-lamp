/*
ts:2016-03-19 17:30:00
*/

ALTER IGNORE TABLE `engine_ipwatcher_log` ADD COLUMN `user_agent` VARCHAR(255) NULL AFTER `ip`;
ALTER IGNORE TABLE `engine_ipwatcher_log_tmp` ADD COLUMN `user_agent` VARCHAR(255) NULL AFTER `ip`;

DELIMITER ;;  
CREATE EVENT `ipwatcher_log_write`
ON SCHEDULE EVERY 1 MINUTE
ON COMPLETION PRESERVE
ENABLE
DO
BEGIN
	CREATE TEMPORARY TABLE tmp_iplog LIKE engine_ipwatcher_log_tmp;
	INSERT INTO tmp_iplog (SELECT * FROM engine_ipwatcher_log_tmp);
	DELETE engine_ipwatcher_log_tmp FROM engine_ipwatcher_log_tmp INNER JOIN tmp_iplog ON engine_ipwatcher_log_tmp.id = tmp_iplog.id;
	INSERT INTO engine_ipwatcher_log (ip,user_agent, uri, requested, gethostbyaddr, location_by_ip) (SELECT ip,user_agent, uri, requested, gethostbyaddr, location_by_ip FROM tmp_iplog);
	DROP TEMPORARY TABLE tmp_iplog;
END;;

