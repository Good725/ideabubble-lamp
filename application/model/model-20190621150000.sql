/*
ts:2019-06-21 15:00:00
*/

/* Add the '47' (Ailesbury) theme, if it does not already exist */
DELIMITER  ;;
INSERT INTO
  `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '47', '47', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_themes`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_themes` WHERE `stub` = '47')
    LIMIT 1
;;


/* Add the '47' theme styles */
UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = '@import url(\'https://fonts.googleapis.com/css?family=Quicksand:300,400,500,700&display=swap\');
\n
\n:root {
\n    \-\-primary: #339abb;   \-\-primary-hover: #53b1d0;   \-\-primary-active: #297994;
\n    \-\-secondary: #f5f5f5; \-\-secondary-hover: #d3d3d3; \-\-secondary-active: #e6e6e6;
\n    \-\-success: #174159;   \-\-success-hover: #215e82;   \-\-success-active: #0d2331;
\n    \-\-info: #17a2b8;      \-\-info-hover: #2f96b4;      \-\-info-active: #31b0d5;
\n    \-\-warning: #ffc107;   \-\-warning-hover: #f89406;   \-\-warning-active: #ec971f;
\n    \-\-danger: #dc3545;    \-\-danger-hover: #bd362f;    \-\-danger-active: #c9302c;
\n}
\n
\nhtml,
\nbutton {
\n    font-family: Quicksand, Helvetica, Arial, sans-serif;
\n}
\n
\nbody {
\n    background-color: #fff;
\n    color: #212121;
\n}
\n
\n.table thead {
\n    background: #339abb;
\n    color: #FFF;
\n}
\n
\n.badge {
\n    background: #339abb;
\n    color: #fff;
\n}
\n
\n.db-sidebar .sidebar-menu li a:hover,
\n.db-sidebar .sidebar-menu li a.active {
\n    background-color: #339abb;
\n    color: #fff;
\n}
\n
\n.popup-header {
\n    background: #339abb;
\n    color: #fff;
\n}
\n
\n.button.course-banner-button.cl_bg {
\n    background-color: #339abb;
\n}
\n
\n.course-widget-links .button.button\-\-cl_remove {
\n    background-color: #f60000;
\n}
\n
\n\/\* Autotimetables \*\/
\n.autotimetable tbody tr:nth-child(even) {
\n    background: #f9f9f9;
\n}
\n
\n.autotimetable tbody tr td a {
\n    color: #244683;
\n}
\n
\n.autotimetable tbody a:hover {
\n    color: #339abb;
\n}
\n
\n.autotimetable .new_date {
\n    border-color: #339abb;
\n}
\n
\n.autotimetable .new_date td:nth-child(1) {
\n    background-color: #339abb;
\n    color: #fff;
\n}
\n
\n:checked + .seating-selector-checkbox-helper:after {
\n    color: #339abb;
\n}
\n
\n.seating-selector-option-radio:checked + .button {
\n    background: #339abb;
\n    color: #fff;
\n}
\n
\n.seating-selector-option-hover {
\n    background-color: #339abb;
\n    color: #fff;
\n}
\n
\n\/\* Forms \*\/
\n.input_group-icon {
\n    background: #339abb;
\n    color: #fff;
\n}
\n
\n.select:after {
\n    border-top-color: #339abb;
\n}
\n
\n.form-select:before {
\n    background-image: -webkit-linear-gradient(left, transparent 0, transparent calc(100% - 2.75em), #339abb calc(100% - 2.75em), #339abb 100%);
\n    background-image: linear-gradient(to right, transparent 0, transparent calc(100% - 2.75em), #339abb calc(100% - 2.75em), #339abb 100%);
\n}
\n
\n.button,
\n.formrt button,
\n.formrt [type=\"submit\"],
\n.formrt [type=\"reset\"] {
\n    background-color: #339abb;
\n}
\n
\n.button\-\-continue {
\n    background-color: #339abb;
\n    border-color: transparent;
\n    color: #fff;
\n}
\n
\n.button\-\-continue.inverse {
\n    background-color: #fff;
\n    border: 1px solid #339abb;
\n    color: #339abb;
\n}
\n
\n.button\-\-cancel {
\n    background: #fff;
\n    border: 1px solid #f00;
\n    color: #f00;
\n}
\n
\n.button\-\-pay {
\n    background-color: #339abb;
\n}
\n
\n.button\-\-pay.inverse {
\n    background: #FFF;
\n    border: 1px solid #339abb;
\n    color: #339abb;
\n}
\n
\n.button\-\-book {
\n    background-color: #339abb;
\n}
\n
\n.button\-\-book.inverse {
\n    background: #fff;
\n    border-color: #339abb;
\n    color: #339abb;
\n}
\n
\n.button\-\-book:disabled {
\n    background-color: #888;
\n}
\n
\n.button\-\-book.inverse:disabled {
\n    background-color: #fff;
\n    border-color: #888;
\n    color: #888;
\n}
\n
\n.button\-\-send,
\n.btn-primary {
\n    background: #339abb;
\n    color: #fff;
\n}
\n
\n.button\-\-send.inverse {
\n    background: #fff;
\n    border-color: #339abb;
\n    color: #339abb;
\n}
\n
\n.button\-\-enquire {
\n    background: #339abb;
\n    color: #fff;
\n}
\n
\n.header-action:only-child {
\n    margin-left: auto;
\n}
\n
\n.header-action:nth-child(odd) .button {
\n    background: #339abb;
\n    color: #fff;
\n}
\n
\n.header-action:nth-child(even) .button {
\n    background: #fff;
\n    border-color: #339abb;
\n    color: #339abb;
\n}
\n
\n.formErrorContent,
\n.formErrorArrow div {
\n    background: #339abb;
\n}
\n
\n\/\* Alerts \*\/
\n.alert-success {
\n    background-color: rgb(223, 240, 216);
\n    border-color: rgb(214, 233, 198);
\n    color: rgb(60, 118, 61);
\n}
\n
\n.alert-info {
\n    background-color: rgb(217, 237, 247);
\n    border-color: rgb(188, 232, 241);
\n    color: rgb(49, 112, 143);
\n}
\n
\n.alert-warning {
\n    background-color: rgb(252, 248, 227);
\n    border-color: rgb(250, 235, 204);
\n    color: rgb(138, 109, 59);
\n}
\n
\n.alert-danger {
\n    background-color: rgb(242, 222, 222);
\n    border-color: rgb(235, 204, 209);
\n    color: rgb(169, 68, 66);
\n}
\n
\n.popup_box { background-color: #fff; }
\n.popup_box.alert-success { border-color: #8CAE38; }
\n.popup_box.alert-info    { border-color: #2472AC; }
\n.popup_box.alert-warning { border-color: #FCC14F; }
\n.popup_box.alert-danger,
\n.popup_box.alert-error   { border-color: #D74638; }
\n.popup_box.alert-add     { border-color: #339abb; }
\n.popup_box.alert-remove  { border-color: #b4b4b4; }
\n
\n.popup_box .alert-icon [fill]   {   fill: #339abb; }
\n.popup_box .alert-icon [stroke] { stroke: #339abb; }
\n
\n
\n\/\* Header \*\/
\n.header,
\n.mobile-breadcrumbs {
\n    background-color: #fff;
\n    color: #339abb;
\n}
\n
\n.dropdown-menu-header {
\n    background-color: #339abb;
\n    color: #fff;
\n}
\n
\n.mobile-menu-toggle {
\n    color: #339abb;
\n}
\n
\n.header-cart-button [fill] { fill: #339abb; }
\n.header-cart-button [stroke] { stroke: #339abb; }
\n
\n.header-logo img {
\n    height: 55px;
\n    max-height: 55px;
\n}
\n
\n.header-menu .level_2 a:hover,
\n.header-menu .level_2:hover > a {
\n    color: #339abb;
\n}
\n
\n.header-item > a:not(.button) {
\n    color: #000;
\n}
\n
\n.header-menu-section > a:after {
\n    border-top-color: #339abb;
\n}
\n
\n.header-menu-section > a {
\n    border-color: transparent;
\n}
\n
\n.header-menu .level_1 > a,
\n.mobile-menu .level_1 > a,
\n.mobile-menu .level_1 > button,
\n.mobile-menu-level3-section .mobile-menu-list > a {
\n    color: #339abb;
\n}
\n
\n.header-menu .level_2 a:before {
\n    border-left-color: #339abb;
\n}
\n
\n.header-menu .level_2 a:hover:before,
\n.header-menu .level_2:hover > a:hover {
\n    border-left-color: #339abb;
\n}
\n
\n.header-menu .level_3 {
\n    border-bottom-color: #339abb;
\n}
\n
\n.mobile-menu-top strong,
\n.mobile-menu-top-avatar,
\n.mobile-menu-button-group-icon,
\n.header-cart-breakdown,
\n.final_price_value {
\n    color: #339abb;
\n}
\n
\n.header-cart-amount,
\n.mobile-menu li.active > a,
\n.checkout-item-title {
\n    color: #339abb;
\n}
\n
\n@media screen and (min-width:768px) {
\n    /* Position logo so that it runs outside of the header */
\n    .header-logo {
\n        background: #fff;
\n        padding: 0;
\n        position: absolute;
\n        left: 0;
\n        width: 216px;
\n        height: 137px;
\n        z-index: 1;
\n    }
\n
\n    .header-logo img {
\n        margin: auto;
\n        width: 201px;
\n        height: 127px;
\n        max-height: none;
\n    }
\n
\n    .header-left {
\n        padding-left: 216px;
\n        position: relative;
\n    }
\n
\n    body:not(.has_banner) .content_area {
\n        margin-top: 45px;/* Logo height, minus header height. Not exactly semantic. */
\n    }
\n
\n    body:not(.has_banner) .header {
\n        border-bottom: 1px solid #ececec;
\n    }
\n}
\n
\n\/\* Quick Contact \*\/
\n@media screen and (max-width: 767px) {
\n    .quick_contact-item > a.active,
\n    .quick_contact-item > a:hover,
\n    .quick_contact-item > a:active {
\n        color: #339abb;
\n    }
\n}
\n
\n\/\* Sidebar \*\/
\n.sidebar-section > h2 {
\n    background: #339abb;
\n    color: #fff;
\n}
\n
\n.sidebar-news-list li {
\n    border-bottom: 1px solid #bfbfbf;
\n    padding: .4em 1.5em .15em;
\n    margin-bottom: 1em;
\n}
\n
\na.sidebar-news-link,
\n.eventTitle {
\n    color: #339abb;
\n}
\n
\n.search-criteria-remove .fa {
\n    color: #f60000;
\n}
\n
\n\/\* Page content \*\/
\n.page-content h1,
\n.page-content h2,
\n.page-content h3,
\n.page-content h4,
\n.page-content h5,
\n.page-content h6 {
\n    border-color: #339abb;
\n    font-family: Quicksand, Helvetica, Arial, sans-serif;
\n}
\n
\n.page-content h1 { border: none; color: #339abb; font-weight: 500; }
\n.page-content h2 { color: #212121; font-size: 30px; font-weight: 500; line-height: 1; }
\n.page-content h3 { color: #212121; font-size: 24px; font-weight: 500; }
\n.page-content h4 { color: #212121; }
\n.page-content h5 { color: #212121; }
\n.page-content h6 { color: #212121; }
\n.page-content p { font-size: 16px; }
\n
\n.page-content li:before {
\n    color: #339abb;
\n}
\n
\n.page-content a:not([class]),
\n.page-content .button\-\-link {
\n    color: #339abb;
\n}
\n
\n.page-content a:not([class]):visited {
\n    color: #551a8b;
\n}
\n
\n.page-content hr {
\n    border-color: #339abb;
\n}
\n
\n.home-stats img {
\n    margin: 0 2rem;
\n}
\n
\n.home-stats {
\n    padding-top: 2rem;
\n}
\n
\n.home-stats h1,
\n.home-stats p {
\n    margin: 0;
\n}
\n
\n.home-stats:last-child {
\n    padding-bottom: 2rem;
\n}
\n
\n.happy_customers p,
\n.happy_customers img {
\n    margin: 0;
\n}
\n
\n.happy_customers img {
\n    border-radius: 5px 5px 0 0;
\n    float: left;
\n}
\n
\n.happy_customers h3 {
\n    background: #ececec;
\n    border-radius: 0 0 5px 5px;
\n    clear: both;
\n    margin: 0 0 1.3em;
\n    padding: .5em 0;
\n}
\n
\n@media screen and (max-width: 768px) {
\n    .simplebox-about .simplebox-column-1 {
\n        margin: 0 auto;
\n        max-width: 360px;
\n    }
\n
\n    .simplebox-about .simplebox-column-2 .simplebox-column-1,
\n    .happy_customers .simplebox-column-1,
\n    .happy_customers .simplebox-column .simplebox-column,
\n    .home-stats .simplebox-columns {
\n        margin: 0 auto;
\n        max-width: 330px;
\n    }
\n
\n    .happy_customers h3 {
\n        margin-bottom: 0;
\n    }
\n}
\n
\n\/\* Banner search \*\/
\n.banner-search-title {
\n    background: #0071a6;
\n    color: #fff;
\n}
\n
\n.banner-search form {
\n    background: #339abb;
\n}
\n
\n.banner-search .form-input {
\n   color: #339abb;
\n}
\n
\n.banner-search .button\-\-continue {
\n    background-color: #fff;
\n    color: #339abb;
\n}
\n
\n.previous_search_text {
\n    color: #fff;
\n}
\n
\n.banner-overlay-content {
\n    text-align: left;
\n}
\n
\n.banner-slide\-\-center .banner-overlay-content {
\n    text-align: left;
\n}
\n
\n[class*=\"layout-home\"] .banner-section,
\n[class*=\"layout-home\"] .banner-image {
\n    height: 768px;
\n}
\n
\n.banner-overlay-content h1 {
\n    font-size: 2.25rem;
\n    font-weight: 700;
\n    line-height: 1.2;
\n    margin: .5em 0;
\n}
\n
\n.banner-overlay-content h2 {
\n    font-size: 2.125rem;
\n    font-weight: bold;
\n    line-height: 1.2;
\n}
\n
\n.banner-overlay-content h2,
\n.banner-overlay-content p {
\n    margin: 1rem 0;
\n}
\n
\n.banner-overlay-content .button {
\n    min-width: 0;
\n    padding: .84em 2.65em;
\n}
\n
\n[class*=\"layout-content\"] .banner-slide\-\-right .banner-overlay .row:before,
\n[class*=\"layout-content\"] .banner-slide\-\-left .banner-overlay .row:before {
\n    content: \'\';
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n    background: url(\'\/shared_media\/ailesbury\/media\/photos\/content\/banner_overlay.svg\') no-repeat right;
\n    opacity: .9;
\n}
\n
\n[class*=\"layout-content\"] .banner-slide\-\-left .banner-overlay .row:before {
\n    transform: scaleX(-1);
\n}
\n
\n[class*=\"layout-content\"] .banner-overlay-content {
\n    max-width: 400px;
\n    text-align: center;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .banner-search-title {
\n        border-bottom-color: #FFF;
\n    }
\n
\n    .banner-overlay {
\n        background: rgba(255, 255, 255, .5);
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .banner-overlay { background-repeat: no-repeat; }
\n    .swiper-slide .banner-image { background-position: center; }
\n
\n    .swiper-slide .banner-overlay {
\n        background-position: top center;
\n    }
\n
\n    .banner-slide\-\-center .banner-overlay {
\n        background: rgba(66, 91, 168, .333)
\n    }
\n
\n    .banner-overlay-content { max-width: 600px; }
\n}
\n
\n.search-drilldown h3 {
\n    color: #339abb;
\n}
\n
\n.search-drilldown-column p {
\n    color: #339abb;
\n}
\n
\n.search-drilldown-column a.active {
\n    background-color: #339abb;
\n    color: #fff;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .search-drilldown-close:before,
\n    .search-drilldown-close:after {
\n        background-color: #303030;
\n    }
\n
\n    .search-drilldown-column\-\-category li {
\n        border-top-color: #339abb;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-drilldown-column {
\n        border-color: #339abb;
\n    }
\n}
\n
\n\/\* Calendar \*\/
\n.eventCalendar-wrap {
\n    border-color: #bfbfbf;
\n}
\n
\n.eventsCalendar-slider {
\n    background: linear-gradient(#339abb, #339abb);
\n    border-bottom-color: #bfbfbf;
\n}
\n
\n
\n.eventsCalendar-currentTitle {
\n    border-bottom-color: #bfbfbf;
\n}
\n
\n.eventsCalendar-currentTitle a {
\n    color: #fff;
\n}
\n
\n.eventCalendar-wrap .arrow span {
\n    border-color: #fff;
\n}
\n
\n.eventsCalendar-day-header,
\n.eventsCalendar-daysList {
\n    color: #fff;
\n}
\n
\n.eventsCalendar-day.today {
\n    background-color: #fff;
\n    border: 1px solid;
\n    border-color: #0098d2 #0093d2 #0098d2 #028bc1;
\n    color: #339abb;
\n}
\n
\n.eventsCalendar-subtitle {
\n    color: #339abb;
\n}
\n
\n.eventsCalendar-list > li > time {
\n    color: #339abb;
\n}
\n
\n.eventsCalendar-list > li {
\n    border-bottom-color: #bfbfbf;
\n}
\n
\n\/\* News feeds \*\/
\n.layout-home .content {
\n    margin-top: 0;
\n}
\n
\n
\n.news-section {
\n    background: #ececec;
\n    box-shadow: 1px 1px 10px #ccc;
\n    margin-top: 0;
\n}
\n
\n.news-slider-link {
\n  color: #339abb;
\n}
\n
\n.news-slider-title {
\n    color: #339abb;
\n    background-color: #ececec;
\n}
\n
\n.swiper-pagination-bullet {
\n    background-color: #fff;
\n    border-color: #A6AEAD;
\n    box-shadow: inset 0 1px 1px #aaa;
\n}
\n
\n.swiper-pagination-bullet-active {
\n    background-color: #339abb;
\n}
\n
\n.news-result-date {
\n    background-color: #339abb;
\n    color: #FFF;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .news-result + .news-result:before {
\n        background: linear-gradient(to right, transparent 0, #339abb 10%, #339abb 90%, transparent 100%);
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .news-result + .news-result {
\n        border-color: #339abb;
\n    }
\n}
\n
\n.news-story-navigation a {
\n    color: #339abb;
\n}
\n
\n.news-story-social {
\n    border-color: #339abb;
\n}
\n
\n.news-story-share_icon {
\n    color: #339abb;
\n}
\n
\n.news-story-social-link svg {
\n    background: #339abb;
\n}
\n
\n.testimonial-signature {
\n    color: #339abb;
\n}
\n
\n\/\* Panels \*\/
\n.panel {
\n    background-color: #fff;
\n}
\n
\n.carousel-section .panel-title {
\n    background: #fff;
\n    color: #000;
\n}
\n
\n.carousel-section .panel-title h3 {
\n    font-weight: 400;
\n}
\n
\n.panels-feed\-\-home_content .panel-link {
\n    border-top: 1px solid #fff;
\n}
\n
\n.carousel-section .panel {
\n    border-color: #bfb8bf;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .panels-feed\-\-home_content > .column:after {
\n        background: #339abb;
\n        background: linear-gradient(to right, #E6F3C8 0%, #339abb 20%, #339abb 80%, #E6F3C8 100%);
\n    }
\n}
\n
\n.bar {
\n    background: #F3F5F5;
\n    background: rgba(243, 245, 245, .8);
\n    border: 1px solid #339abb;
\n    box-shadow: 0 1px 1px #ccc;
\n}
\n
\n.bar-icon {
\n    background: #fff;
\n    border-right: 1px solid #339abb;
\n    color: #339abb;
\n}
\n
\n.bar-icon svg {
\n  fill: #339abb;
\n}
\n
\n.bar-text {
\n    color: #222;
\n}
\n
\n.panel-item.has_form {
\n    background-color: #339abb;
\n    color: #fff;
\n}
\n
\n.panel-item.has_form .button {
\n    background-color: #fff;
\n    border-color: #339abb;
\n    color: #339abb;
\n}
\n
\n.panel-item-image:after {
\n    background-image: url(\'\/shared_media\/ailesbury\/media\/photos\/content\/panel_overlay_right.svg\');
\n    opacity: .9;
\n}
\n
\n.panel-item:nth-child(odd) .panel-item-image:after {
\n    background-image: url(\'\/shared_media\/ailesbury\/media\/photos\/content\/panel_overlay_left.svg\');
\n}
\n
\n.panel-item.has_image .panel-item-text {
\n    color: #fff;
\n    display: flex;
\n    align-items: center;
\n    height: auto;
\n    padding: .5em;
\n    position: absolute;
\n    top: 0;
\n    bottom: 0;
\n    text-align: center;
\n}
\n
\n.panel-item.has_image:nth-child(even) .panel-item-text {
\n    left: 50%;
\n}
\n
\n.panel-item.has_image:nth-child(odd) .panel-item-text {
\n    right: 50%;
\n}
\n
\n.panel-item.has_form {
\n    background-color: #ececec;
\n    color: #000;
\n}
\n
\n\/\* Search results \*\/
\n.course-list-header {
\n    border-bottom-color: #B7B7B7;
\n}
\n
\n.course-list-display-option:after {
\n    background: #d0d0d0;
\n}
\n
\n.course-list\-\-grid .course-widget {
\n    border-color: #bfbfbf;
\n}
\n
\n.course-widget-category {
\n    background: #339abb;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-grid .course-widget-price {
\n    background-color: #339abb;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-list .course-widget-price-original,
\n.course-list\-\-list .course-widget-price-current {
\n    color: #339abb;
\n}
\n
\n.course-list-grid .course-widget-time_and_date {
\n    border-color: #b7b7b7;
\n}
\n
\n.course-list\-\-list .course-widget-location_and_tags {border-color: #CCC; }
\n
\n.pagination-prev a,
\n.pagination-next a {
\n    background: #339abb;
\n    color: #fff;
\n}
\n
\n.pagination-prev a:before,
\n.pagination-next a:before {
\n    border-color: #fff;
\n}
\n
\n.course-banner-overlay {
\n    background-color: rgba(255, 255, 255, .8);
\n    color: #000;
\n}
\n
\n.fixed_sidebar-header {
\n    background: #339abb;
\n    color: #fff;
\n}
\n
\n.booking-form h2 {
\n    border: none;
\n}
\n
\n.booking-required_field-note {
\n    color: #339abb;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .contact-map-overlay {
\n        background-color: #339abb;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .contact-map-overlay-content {
\n        background: #339abb;
\n        background: rgba(111,120,170, .8);
\n    }
\n}
\n
\n.availability-timeslot .highlight {
\n    color: #339abb;
\n}
\n
\n.availability-timeslot.booked {
\n    border-color: #339abb;
\n}
\n
\n.availability-timeslot.booked .highlight {
\n    color: #339abb;
\n}
\n
\n.timeline-swiper .swiper-slide.selected {
\n    background: #339abb;
\n    color: #fff;
\n}
\n
\n.timeline-swiper-highlight {
\n    color: #339abb;
\n}
\n
\n.timeline-swiper-prev,
\n.timeline-swiper-next {
\n    color: #339abb;
\n}
\n
\n\/\* Footer \*\/
\n.page-footer {
\n    margin-top: 1.5em;
\n    padding: 1.75em;
\n}
\n.page-footer .page-content h1 {
\n    border: none;
\n    color: inherit;
\n    margin: .5em 0;
\n}
\n
\n.footer {
\n    background: #fff;
\n}
\n
\n.footer-logo img {
\n    width: 485px;
\n}
\n
\n.footer h2,
\n.footer-stat,
\n.footer-column-title {
\n    color: #339abb;
\n}
\n
\n.footer-stats-list {
\n    color: #000;
\n}
\n
\n.footer-slogan {
\n    color: #000;
\n}
\n
\n.footer-stats {
\n    background: #fff url(\'/shared_media/ailesbury/photos/content/rainbow_footer_background.png\') top center;
\n    min-height: 0;
\n    padding-top: 3.5rem;
\n}
\n
\n.footer-stat h2:after {
\n    border-color: #339abb;
\n}
\n
\n.footer-apps {
\n    background: #fff;
\n}
\n
\n.footer-copyright {
\n    color: #787878;
\n}
\n
\n.footer-apps,
\n.footer-social,
\n.footer-columns,
\n.footer-copyright {
\n    border-top: 1px solid #535353;
\n}
\n
\n.footer-column h4 {
\n    font-weight: bold;
\n}
\n
\n.footer .form-input::-webkit-input-placeholder { color: #000; font-weight: 300; }
\n.footer .form-input::-moz-placeholder          { color: #000; font-weight: 300; }
\n.footer .form-input:-ms-input-placeholder      { color: #000; font-weight: 300; }
\n
\n\/\* Dropdown filters \*\/
\n.search-filter-total {
\n    color: #339abb;
\n}
\n
\n.search-filters :checked ~ .form-checkbox-helper,
\n.search-filters :checked ~ .form-radio-helper,
\n.search-filters :checked ~ .form-checkbox-label,
\n.search-filters :checked ~ .form-radio-label {
\n    border-color: #339abb;
\n    color: #339abb;
\n}
\n
\n.search-filters :checked + .form-radio-helper:after {
\n    background-color: #339abb;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-filter-dropdown.filter-active > button,
\n    .search-filters-clear {
\n        color: #339abb;
\n    }
\n
\n    .checkout-heading {
\n        background-color: #339abb;
\n        color: #fff;
\n    }
\n}
\n
\n\/\* Misc \*\/
\n.contact\-\-left .ui-tabs-nav .ui-tabs-active a {
\n    background: #339abb;
\n    border-color:#339abb;
\n    color: #fff;
\n}
\n
\n.checkout-right-sect .btn-close:hover {
\n    color: #339abb;
\n    border-color: #339abb;
\n}
\n
\n.checkout-right-sect .sub-total,
\n.prepay-box li.total  {
\n    color: #339abb;
\n}
\n
\na.item-summary-head {
\n    color: #339abb;
\n}
\n
\n.checkout-progress li a:after {
\n    background-color: #fff;
\n    border-color: #339abb;
\n}
\n
\n.checkout-progress li.curr a:after {
\n    background: #339abb;
\n    background: radial-gradient(#2f80b8, #339abb);
\n}
\n
\n.checkout-progress li + li:before {
\n    border-color: #339abb;
\n}
\n
\n.checkout-progress .curr ~ li:before {
\n    border-color: #339abb;
\n}
\n
\n.search-package-available h2 {
\n    color: #4f4e4f;
\n}
\n
\n.search-package-available .available-text  h4 {
\n    border-color: #eee;
\n    color: #339abb;
\n}
\n
\n.search-package-available .show-more {
\n    background: #fff;
\n    border: 1px solid #339abb;
\n    color: #339abb;
\n}
\n
\n.prepay-box h6 {
\n    color: #339abb;
\n}
\n
\n.custom-calendar .booking-date-button {
\n    background-color: #339abb;
\n    color: #fff;
\n}
\n
\n.custom-calendar .booking-date-button:hover {
\n    background-color: #339abb;
\n}
\n
\n.custom-calendar button.booking-date-button.active {
\n    background-color: #fff;
\n    color: #339abb;
\n}
\n
\n.course-activity-alert,
\n.details-wrap .left-place {
\n    color: #F75A5F;
\n}
\n
\n.number-of-people-viewing {
\n    color: #339abb;
\n}
\n
\n.search-calendar-course-image .fa {
\n    background-color: #339abb;
\n    color: #fff;
\n}
\n
\n.custom-calendar tbody td.active,
\n.custom-calendar tbody td.active:hover {
\n    background-color: #fff;
\n    color: #339abb;
\n}
\n
\n.custom-calendar tbody tr:first-child td {
\n    color: #222;
\n}
\n
\n.package-offers-wrap h2 {
\n    color: #339abb;
\n    border-color: #c5cecd;
\n}
\n
\n.package-offers-wrap h3 {
\n    color: #339abb;
\n}
\n
\n.package-offers-wrap .summary-wrap .more,
\n.classes-details-wrap .details-wrap li:first-child {
\n    color: #339abb;
\n}
\n
\n.classes-details-wrap .details-wrap li:first-child {
\n  background-color: #339abb;
\n}
\n
\n.details-wrap .remove-booking,
\n.details-wrap .wishlist.remove{
\n    color: #339abb;
\n}
\n
\n.sidelines:before,
\n.sidelines:after,
\n.details-wrap .price-wrap {
\n    border-color: #e4e4e4;
\n}
\n
\n.details-wrap .time,
\n.details-wrap .price,
\n.details-wrap .fa {
\n    color: #339abb;
\n}
\n
\n\/\* course results hover \*\/
\n.details-wrap:hover {
\n    background-color: #f9f9f9;
\n    border-color:#d8d8d8 ;
\n}
\n
\n.details-wrap:hover .time,
\n.details-wrap:hover .price,
\n.details-wrap:hover .fa-book {
\n    color: #339abb;
\n}
\n
\n.details-wrap:hover li:first-child {
\n    background-color: #339abb;
\n}
\n
\n.details-wrap:hover .sidelines::before,
\n.details-wrap:hover .sidelines::after,
\n.details-wrap:hover .price-wrap {
\n    border-color:#339abb;
\n}
\n
\n
\n\/\* course results booked \*\/
\n.details-wrap.booked {
\n    border-color:#339abb;
\n    background-color: #f3f3f3;
\n}
\n
\n.details-wrap.booked .time,
\n.details-wrap.booked .price,
\n.details-wrap.booked .fa-book {
\n    color: #339abb;
\n}
\n.details-wrap.booked li:first-child {
\n    background-color: #339abb;
\n}
\n
\n.details-wrap.booked .sidelines::before,
\n.details-wrap.booked .sidelines::after,
\n.details-wrap.booked .price-wrap {
\n    border-color:#339abb;
\n}
\n
\n.classes-details-wrap .alert-wrap {
\n    background-color: #339abb;
\n    color: #fff;
\n}
\n
\n.custom-slider-arrow a {
\n    color: #0e2a6b;
\n}
\n
\n.search_courses_right:hover,
\n.search_courses_left:hover,
\n.arrow-left.for-time-slots:hover,
\n.arrow-right.for-time-slots:hover{
\n    color: #339abb;
\n}
\n
\n.custom-calendar .booking-date-button.already_booked {
\n    background-color: #339abb;
\n    color: #fff;
\n}
\n
\n.search_history > a {
\n    color: #fff;
\n}
\n
\n.search_history .remove_search_history {
\n    color: #fff;
\n    border-color: #fff;
\n}
\n
\n.swiper-button-prev {
\n    background-image: url(\"data:image\/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M0%2C22L22%2C0l2.1%2C2.1L4.2%2C22l19.9%2C19.9L22%2C44L0%2C22L0%2C22L0%2C22z\'%20fill%3D\'%23339abb\'%2F%3E%3C%2Fsvg%3E\");
\n}
\n
\n.swiper-button-next {
\n    background-image: url(\"data:image\/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M27%2C22L27%2C22L5%2C44l-2.1-2.1L22.8%2C22L2.9%2C2.1L5%2C0L27%2C22L27%2C22z\'%20fill%3D\'%23339abb\'%2F%3E%3C%2Fsvg%3E\");
\n}
\n
\nbody > div > img {
\n  display: block;
\n}
\n
\n.submit-expand {
\n    background: none;
\n    border: none;
\n}
\n
\n'
  WHERE
  `stub` = '47'
;;

/* Add the "home_page_content_above" layout, if it does not already exist */
INSERT IGNORE INTO
  `plugin_pages_layouts` (`layout`, `template_id`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`)
SELECT
  'home_page_content_above',
  (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '04' AND `deleted` = 0),
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
FROM
  `engine_site_templates`
WHERE NOT EXISTS
  (SELECT * FROM `plugin_pages_layouts` WHERE `layout` = 'home_page_content_above' AND `deleted` = 0)
LIMIT 1
;;