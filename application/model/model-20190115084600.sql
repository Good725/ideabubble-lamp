/*
ts:2019-01-15 08:46:00
*/

CREATE TABLE engine_runonce
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  fcall VARCHAR(100) NOT NULL,
  runat DATETIME
)
ENGINE=MYISAM
CHARSET=UTF8;
