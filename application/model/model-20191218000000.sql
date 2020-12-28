/*
ts:2019-12-18 00:00:00
*/

UPDATE `engine_users`
SET `password` = 'b5a6123dbe6a28b25a68566c03194898ecccfc14d527c5bb3f14c7d96da7dd01'
WHERE (`email` = 'trainer@courseco.co');

INSERT INTO `engine_plugins` (`name`, `friendly_name`) VALUES ('accidents', 'Accidents');
