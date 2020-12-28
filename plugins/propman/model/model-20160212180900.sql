/*
ts:2016-02-12 18:09:00
*/

DROP TABLE plugin_propman_ratecards_calendar;

CREATE TABLE plugin_propman_ratecards_calendar
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  ratecard_id INT NOT NULL,
  range_id  INT NOT NULL,
  `date`  DATE NOT NULL,

  KEY (`ratecard_id`),
  KEY (`date`)
)
  ENGINE = InnoDB
  CHARSET = UTF8;
