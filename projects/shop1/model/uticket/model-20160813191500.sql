/*
ts:2016-08-13 19:15:00
*/

UPDATE `plugin_reports_reports` SET `sql` = ('SELECT CONCAT(
    \'<div class="text-center"><h3>Total Tickets</h3><span style="font-size: 2em;">\',
    `total`.`total`,
    \'</span><hr /><a href="/admin/dashboards/view_dashboard/\',
    (
      SELECT
        `id`
      FROM
        `plugin_dashboards`
      WHERE
        `title` = \'My Orders Dashboard\'
        AND `deleted` = 0
    ),
    \'" style="color: #fff;">View Dashboard</a></div>\'
  ) as ` `
FROM
  (
    SELECT
      IFNULL(
        SUM(
          `event`.`quantity` - IFNULL(`sold`.`sold`, 0)
        ),
        0
      ) AS `total`
    FROM
      `plugin_events_events` `event`
      LEFT JOIN `plugin_events_events_sold` `sold` ON `sold`.`event_id` = `event`.`id`
    WHERE
      `event`.`deleted` = 0
      AND `event`.`owned_by` = @user_id
      AND `event`.`is_onsale` = 1
  ) AS `total`') WHERE `name` = 'Total Tickets';
