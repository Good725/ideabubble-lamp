/*
ts:2017-12-20 10:40:00
*/

DELIMITER ;;
UPDATE
  `engine_site_templates`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = REPLACE(
    `styles`,
    '\n    .quick_contact-item > a {
\n        color: #fff;
\n        display: block;
\n        padding: .05em .5em;
\n        text-decoration: none;
\n        width: 100%;
\n    }',
  '\n    .quick_contact-item > a {
\n        display: block;
\n        padding: .05em .5em;
\n        text-decoration: none;
\n        width: 100%;
\n    }
\n
\n    li.quick_contact-item > a {
\n        color: #fff;
\n    }'
  )
WHERE
  `stub` = '03';;

/* Remove ::before and ::after pseudo elements, which are interfering with the flex wrap in Safari */
UPDATE
  `engine_site_templates`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = REPLACE(
    `styles`,
    '\n.featured-services ul {
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -ms-flex-wrap: wrap;
\n    flex-wrap: wrap;
\n}',
  '\n.featured-services ul {
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -ms-flex-wrap: wrap;
\n    flex-wrap: wrap;
\n}
\n
\n.featured-services ul:before,
\n.featured-services ul:after {
\n    display: none;
\n}'
  )
WHERE
  `stub` = '03';;

/* Fix logo mobile sizing */
UPDATE
  `engine_site_templates`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = REPLACE(
    `styles`,
    '.name img {
\n    margin-left: 1rem;
\n    max-width: calc(100% - 4rem);
\n}',
    '.name a {
\n    display: block;
\n    height: 100%;
\n    padding: 1rem;
\n}
\n
\n.name img {
\n    max-height: 100%;
\n}'
  )
WHERE
  `stub` = '03';;

-- Banner styling
UPDATE
  `engine_site_templates`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `header`        = REPLACE(
    `header`,
    '<body class=\"layout-<?= $page_data[\'layout\'] ?><?= ( ! empty($page_data[\'banner_slides\'])) ? \'has_banner\' : \'\' ?>\">',
    '<body class=\"layout-<?= $page_data[\'layout\'] ?><?= ( ! empty($page_data[\'banner_slides\'])) ? \' has_banner\' : \'\' ?>\">'
  ),
  `styles`        = REPLACE(
    `styles`,
    '\n.frontpage-banner-caption {
\n    text-align: center;
\n}',
  '\n.layout-home .banner-image img {
\n    display: none;
\n}
\n
\nbody:not(.layout-home) .banner-image {
\n    background-image: none !important;
\n    height: auto;
\n}
\n
\n.frontpage-banner-caption {
\n    text-align: center;
\n}'
  )
WHERE
  `stub` = '03';;

UPDATE
  `engine_site_templates`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `header`        = REPLACE(
    `header`,
    '<div class=\"banner-image attachment-banner size-banner\" style=\"background-image: url(\'/shared_media/<?= $project_media_folder ?>/media/photos/banners/<?= $slide[\'image\'] ?>\')\"></div>',
    '<div class=\"banner-image attachment-banner size-banner\" style=\"background-image: url(\'/shared_media/<?= $project_media_folder ?>/media/photos/banners/<?= $slide[\'image\'] ?>\')\">
\n                                        <img src=\"/shared_media/<?= $project_media_folder ?>/media/photos/banners/<?= $slide[\'image\'] ?>\" alt=\"\" />
\n                                    </div> '
    ),
    `styles`      = REPLACE(
      `styles`,
      '\n    .frontpage-banner-caption {
\n        text-align: left;
\n        position: absolute;
\n        top: 1rem;
\n        width: 100%;
\n        margin-top: 5rem;
\n    }',
    '\n    .frontpage-banner-caption {
\n        text-align: left;
\n        position: absolute;
\n        top: 1rem;
\n        width: 100%;
\n    }
\n
\n    .layout-home .frontpage-banner-caption {
\n        margin-top: 5rem;
\n    }'
    )
WHERE
  `stub` = '03';;



UPDATE
  `engine_site_templates`
SET
  `styles`        = REPLACE(

      `styles`,

      -- old CSS
      '\n.layout-home .banner-image img {
\n    display: none;
\n}
\n
\nbody:not(.layout-home) .banner-image {
\n    background-image: none !important;
\n    height: auto;
\n}',

      -- new CSS
      '\n.banner-image {
\n    background-image: none !important;
\n}'
    )
WHERE
  `stub` = '03';;


UPDATE
  `engine_site_templates`
SET
  `styles`        = REPLACE(
      `styles`,


      -- old CSS
      '.layout-home .frontpage-banner-caption {
\n        margin-top: 5rem;
\n    }',


      -- new CSS
      ''
    )
WHERE
  `stub` = '03';;


UPDATE
  `engine_site_templates`
SET
  `styles`        = REPLACE(
      `styles`,

      -- old CSS
      '\n@media screen and (min-width: 767px) {
\n    .frontpage-banner .banner-image {
\n        height: 359px;
\n    }
\n
\n}
\n',

      -- new CSS
      ''
    )
WHERE
  `stub` = '03';;


UPDATE
  `engine_site_templates`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = REPLACE(
      `styles`,

      -- old CSS
      '\n.frontpage-banner .banner-image {
\n    background-position-x: center;
\n    background-repeat: no-repeat;
\n    background-size: auto 100%;
\n    max-width: 100%;
\n    width: auto;
\n    margin: 0 auto;
\n    height: 182px;
\n}',

      -- new CSS
      '\n.frontpage-banner .banner-image {
\n    background-position-x: center;
\n    background-repeat: no-repeat;
\n    background-size: auto 100%;
\n    max-width: 100%;
\n    width: auto;
\n    margin: 0 auto;
\n}'
    )
WHERE
  `stub` = '03';;


UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = CONCAT(
      `styles`,
      '\n
\n.layout-home .banner-image img {
\n    float: right;
\n}
\n
\n@media only screen and (max-width: 1023px) {
\n    .layout-home .banner-image img {
\n        width: 100%;
\n    }
\n}
\n
\n@media only screen and (min-width: 1024px) {
\n    .layout-home .frontpage-banner-caption {
\n        margin-top: 5rem;
\n    }
\n}'
  )
WHERE
  `stub` = '30';;

