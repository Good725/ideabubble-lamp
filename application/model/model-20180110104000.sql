/*
ts:2018-01-01 10:40:00
*/

/* Vertically centre the banner overlay */

DELIMITER ;;

UPDATE
  `engine_site_templates`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = REPLACE(
      `styles`,

      -- old CSS
      '.frontpage-banner-caption {
\n        text-align: left;
\n        position: absolute;
\n        top: 1rem;
\n        width: 100%;
\n    }',

      -- new CSS
      '.frontpage-banner-caption {
\n        -webkit-box-align: center;
\n        -ms-flex-align: center;
\n        align-items: center;
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        position: absolute;
\n        top: 0;
\n        bottom: 0;
\n        text-align: left;
\n        width: 100%;
\n    }
\n
\n    .frontpage-banner-caption p:last-child,
\n    .frontpage-banner-caption p:last-child .button:last-child {
\n        margin-bottom: 0;
\n    }'
      )
WHERE
  `stub` = '03';;


UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = REPLACE(
      `styles`,
      '\n
\n@media only screen and (min-width: 1024px) {
\n    .layout-home .frontpage-banner-caption {
\n        margin-top: 5rem;
\n    }
\n}',
      ''
  )
WHERE
  `stub` = '30';;

