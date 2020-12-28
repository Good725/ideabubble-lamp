/*
ts:2017-11-19 11:03:00
*/

CREATE TABLE engine_api_plugins
(
  plugin VARCHAR(100) PRIMARY KEY,
  enabled TINYINT NOT NULL DEFAULT 0
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO engine_api_plugins (`plugin`, `enabled`) VALUES ('engine', 0);
