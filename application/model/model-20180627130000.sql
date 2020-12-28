/*
ts:2018-06-27 13:00:00
*/


DELIMITER ;;

/* Add the "38" theme, if it does not already exist */
INSERT INTO
  `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '38', '38', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '03' LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_themes`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_themes` WHERE `stub` = '38')
    LIMIT 1
;;


UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = '@import url(\'https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i\');
\nbody {
\n    font-family: Roboto, \"Helvetica Neue\", Helvetica, Arial, sans-serif;
\n}
\n
\n\/\* Error bubbles \*\/
\n.formError .formErrorContent,
\n.formError .formErrorArrow div  {
\n    background-color: $base_color_1;
\n}
\n
\n\/\* Links \*\/
\na,
\na:link {
\n    color: $link_color;
\n}
\n
\na:visited {
\n    color: $link_visited_color;
\n}
\n
\na:hover,
\na:focus {
\n    color: $link_hover_color;
\n}
\n
\n\/\* Alerts \*\/
\n.alert-box {
\n    background-color: $base_color_1;
\n    border-color: scale_color($base_color_1, -.3);
\n}
\n
\n.alert-box.success {
\n    background-color: #6db850;
\n    border-color: scale_color(#6db850, -.3);
\n}
\n
\n.alert-box.alert {
\n    background-color: #ca5a4b;
\n    border-color: scale_color(#ca5a4b, -.3);
\n}
\n
\n.alert-box.secondary {
\n    background-color: #199ad6;
\n    border-color: #199ad6;
\n}
\n
\n.alert-box.warning {
\n    background-color: #e5b13b;
\n    border-color: #dba11d;
\n}
\n
\n.alert-box.info {
\n    background-color: #677bde;
\n    border-color: #425bd6;
\n}
\n
\n\/\* Breadcrumbs \*\/
\n.breadcrumbs > *,
\n.breadcrumbs > * a,
\n.breadcrumbs > *:before {
\n    color: $base_color_1;
\n}
\n
\n\/\* Buttons \*\/
\nbutton,
\n.button {
\n    background-color: $base_color_1;
\n    border-color:#e32525;
\n    color: #fff;
\n}
\n
\na.button {
\n    color: #fff;
\n}
\n
\nbutton:hover, button:focus, .button:hover, .button:focus {
\n    background-color: scale_color($base_color_1, -.1);
\n}
\n
\nbutton.secondary, .button.secondary {
\n    background-color: #199ad6;
\n    border-color: #199ad6;
\n}
\n
\nbutton.secondary:hover, button.secondary:focus, .button.secondary:hover, .button.secondary:focus {
\n    background-color: black;
\n}
\n
\nbutton.success, .button.success {
\n    background-color: #6db850;
\n    border-color: #56963d;
\n}
\n
\nbutton.alert, .button.alert {
\n    background-color: #ca5a4b;
\n    border-color: #ab4132;
\n}
\n
\nbutton.alert:hover, button.alert:focus, .button.alert:hover, .button.alert:focus {
\n    background-color: #ab4132;
\n}
\n
\nbutton.warning, .button.warning {
\n    background-color: #e5b13b;
\n    border-color: #cb951b;
\n}
\n
\nbutton.warning:hover, button.warning:focus, .button.warning:hover, .button.warning:focus {
\n    background-color: #cb951b;
\n}
\n
\nbutton.info, .button.info {
\n    background-color: #677bde;
\n    border-color: #324dd2;
\n}
\n
\nbutton.info:hover, button.info:focus, .button.info:hover, .button.info:focus {
\n    background-color: #324dd2;
\n}
\n
\n\/\* Labels \*\/
\n.label {
\n    background-color: $base_color_1;
\n}
\n
\n.label.alert {
\n    background-color: #ca5a4b;
\n}
\n
\n.label.warning {
\n    background-color: #e5b13b;
\n}
\n
\n.label.success {
\n    background-color: #6db850;
\n}
\n
\n.label.secondary {
\n    background-color: #199ad6;
\n}
\n
\n.label.info {
\n    background-color: #677bde;
\n}
\n
\n\/\* Pagination \*\/
\nul.pagination li.current a,
\nul.pagination li.current button,
\nul.pagination li.current a:hover,
\nul.pagination li.current a:focus,
\nul.pagination li.current button:hover,
\nul.pagination li.current button:focus {
\n    background: $base_color_1;
\n}
\n
\n\/\* Top bar \*\/
\n.top-bar {
\n    background: #199ad6;
\n}
\n.header-top-desktop-block, .block-header .top-bar {
\n    background-color: #eee;
\n}
\n.contain-to-grid {
\n    background: #f07523;
\n}
\n
\n.top-bar-section li:not(.has-form) a:not(.button) {
\n    background: #f07523;
\n }
\n
\n.top-bar,
\n.header-top-contact-list a:not(.button) {
\n    color: #199ad6;
\n}
\n
\n.top-bar .toggle-topbar.menu-icon a {
\n    color: #f07523;
\n}
\n
\n.top-bar .toggle-topbar.menu-icon a span::after {
\n    box-shadow: 0 0 0 1px #2D7B31, 0 7px 0 1px #2D7B31, 0 14px 0 1px #2D7B31;
\n}
\n
\n.top-bar-section ul li {
\n    background-color: #eee;
\n}
\n
\n.top-bar-section > ul > li > a {
\n    font-weight: normal;
\n}
\n
\n.top-bar-section ul li.hide-for-large-up > a {
\n    background-color: $base_color_1;
\n}
\n
\n@media only screen and (max-width: 63.9375em) {
\n    .name a {
\n        padding: .5rem 1rem;
\n    }
\n}
\n
\n@media only screen and (min-width: 64em) {
\n    .top-bar-section li.active:not(.has-form) a:not(.button),
\n    .no-js .top-bar-section ul li:active > a {
\n        color: #0a3b61;
\n    }
\n
\n    .top-bar-section li.active:not(.has-form) a:not(.button):hover {
\n        background: #199ad6;
\n    }
\n}
\n
\n@media only screen and (min-width: 64em){
\n    .top-bar-section li:not(.has-form) a:not(.button):hover {\n
\n        background: #199ad6;
\n        color: #fff;
\n    }
\n
\n    .top-bar-section .dropdown li:not(.has-form):not(.active) > a:not(.button) {
\n        background: #199ad6;
\n    }
\n
\n    .top-bar-section.top-bar-section ul li:hover > a {
\n        background: #199ad6;
\n        color: #fff;
\n    }
\n
\n    .top-bar-section .dropdown li:not(.has-form):not(.active):hover > a:not(.button) {
\n        background: #f07523;
\n        color: #fff;
\n    }
\n
\n    .top-bar-section li.active:not(.has-form) a:not(.button) {
\n        background: #f07424;
\n        color: #0a3b61;
\n    }
\n}
\n
\n
\n\/\* Lists \*\/
\nul.tick li:before {
\n    color: #6db850;
\n}
\n
\n.styled-list.chevron li:before,
\n.styled-list.caret li:before,
\n.styled-list.tick li:before {
\n    color: $base_color_1;
\n}
\n
\narticle ul > li:before {
\n    color: $base_color_2;
\n}
\n
\n
\n\/\* Banner \*\/
\n.banner-slide\-\-left  .frontpage-banner-caption { text-align: left;  }
\n.banner-slide\-\-right .frontpage-banner-caption { text-align: right; }
\n
\n.frontpage-banner h1 {
\n    border: 0 solid #f07523;
\n    font-size: 3.5625rem;
\n    letter-spacing: .06em;
\n    line-height: 1.05;
\n    margin-top: .1em;
\n    margin-bottom: .35em;
\n}
\n
\n.frontpage-banner .banner-slide\-\-left h1 {
\n    border-left-width: 2px;
\n    padding-left: 1rem;
\n}
\n
\n.frontpage-banner .banner-slide\-\-right h1 {
\n    border-right-width: 2px;
\n    padding-right: 1rem;
\n
\n}
\n
\n.frontpage-banner .button {
\n    border-radius: 5px;
\n    font-size: 25px;
\n    font-weight: 300;
\n    padding: .333em .5em;
\n}
\n
\n@media screen and (min-width: 1024px) {
\n
\n  .banner-image img {
\n        position: relative;
\n        left: 50%;
\n        transform: translateX(-50%);
\n        width: 1920px;
\n        max-width: none;
\n        height: 300px;
\n    }
\n
\n    .banner-caption-content {
\n        padding-left: 0;
\n        padding-right: 0;
\n    }
\n}
\n
\n
\n\/\* Misc \*\/
\nblockquote {
\n    border-left-color: $base_color_1;
\n}
\n
\n.chat-sticky ul li a:hover {
\n    color: $base_color_1;
\n}
\n.chat-sticky {
\n    background: #199ad6;
\n}
\n
\n.testimonial-block h4 {
\n    color: $base_color_1;
\n}
\n
\n.panel-item.has_form {
\n    background: #64666a;
\n    color: #fff;
\n}
\n
\n.panel-item-image ~ .panel-item-text {
\n    color: #000;
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -webkit-box-orient: vertical;
\n    -webkit-box-direction: normal;
\n    -ms-flex-direction: column;
\n    flex-direction: column;
\n    top: 0;
\n}
\n
\n.panel-item-image ~ .panel-item-text > :first-child {
\n    margin-top: auto;
\n}
\n
\n.panel-item-image ~ .panel-item-text > :last-child {
\n    margin-bottom: auto;
\n}
\n
\n.panel-item-image ~ .panel-item-text h3 {
\n    color: #199ad6;
\n}
\n
\n.panel-item-image:before {
\n    display: none;
\n}
\n
\n.panel-item.has_form iframe {
\n    transform: scale(.85);
\n    margin-left: -8%
\n}
\n
\n@media screen and (max-width: 40rem) {
\n    .quick_contact {
\n        background: #199ad6;
\n    }
\n}
\n
\n.block-footer {
\n    background: #64666a;
\n    color: #fff;
\n}
\n
\n.sidebar .widget ul li.current-cat a,
\n.sidebar .widget ul li.current_page_item a,
\n.sidebar .widget ul li a:hover {
\n    color: $base_color_1;
\n}
\n
\n.widget-widget_monolith_relative_pages_widget ul li.current_page_item a {
\n    color: $base_color_1;
\n}
\n
\n.feature-block > a .feature-block-description {
\n  color: $base_color_1;
\n}
\n
\n.gray-band {
\n    background-color: #f6f6f6;
\n}
\n
\n.partners > li {
\n    min-width: 20%;
\n    margin-bottom: 2em;
\n}
\n
\n.orbit-bullets li.active {
\n    background: $base_color_1;
\n}
\n
\n.team-filtering li a.active {
\n    background-color: #c27800;
\n}
\n
\n.panel.panel-advice {
\n    border-color: $base_color_1;
\n}
\n
\n.panel.panel-advice ul.list-unstyled.chevron li:before {
\n    color: $base_color_1;
\n}
\n'
WHERE
  `stub` = '38'
;;
