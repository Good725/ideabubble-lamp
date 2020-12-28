/*
ts:2019-07-16 12:11:00
*/

/* Add the 48 (Voiceworks Studio) theme */
DELIMITER  ;;
INSERT INTO
  `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '48', '48', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_themes`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_themes` WHERE `stub` = '48')
    LIMIT 1
;;

/* Add the '48' theme styles */
UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = "@import url(\'https:\/\/fonts.googleapis.com\/css?family=Poppins:300,300i,400,400i,500,500i\');
\n
\n:root {
\n    \-\-primary: #f5b614;   \-\-primary-hover: #fed367;   \-\-primary-active: #f90;
\n    \-\-secondary: #f5f5f5; \-\-secondary-hover: #d3d3d3; \-\-secondary-active: #e6e6e6;
\n    \-\-success: #333;      \-\-success-hover: #4d4d4d;   \-\-success-active: #1a1a1a;
\n    \-\-info: #5bc0de;      \-\-info-hover: #31b0d5;      \-\-info-active: #269abc;
\n    \-\-warning: #ffc107;   \-\-warning-hover: #f89406;   \-\-warning-active: #ec971f;
\n    \-\-danger: #dc3545;    \-\-danger-hover: #bd362f;    \-\-danger-active: #c9302c;
\n}
\n
\nhtml,
\nbutton {
\n    font-family: Poppins, Helvetica, Arila, sans-serif;
\n}
\n
\nbody {
\n    background-color: #fff;
\n    color: #333;
\n}
\n
\nhr {
\n    border: dotted rgba(0, 0, 0,.2);
\n    border-width: 0 0 1px;
\n}
\n
\n.table thead {
\n    background: #F5B614;
\n    color: #FFF;
\n}
\n
\n.badge {
\n    background: #F5B614;
\n    color: #fff;
\n}
\n
\n.db-sidebar .sidebar-menu li a:hover,
\n.db-sidebar .sidebar-menu li a.active {
\n    background-color: #F5B614;
\n    color: #fff;
\n}
\n
\n.popup-header {
\n    background: #F5B614;
\n    color: #fff;
\n}
\n
\n.button.course-banner-button.cl_bg {
\n    background-color: #F5B614;
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
\n    color: #F5B614;
\n}
\n
\n.autotimetable .new_date {
\n    border-color: #F5B614;
\n}
\n
\n.autotimetable .new_date td:nth-child(1) {
\n    background-color: #F5B614;
\n    color: #fff;
\n}
\n
\n:checked + .seating-selector-checkbox-helper:after {
\n    color: #F5B614;
\n}
\n
\n.seating-selector-option-radio:checked + .button {
\n    background: #F5B614;
\n    color: #fff;
\n}
\n
\n.seating-selector-option-hover {
\n    background-color: #F5B614;
\n    color: #fff;
\n}
\n
\n\/\* Forms \*\/
\n.login-form-container {
\n    box-shadow: 0 3px 7px rgba(0, 0, 0, 0.35);
\n}
\n
\n.login-form-container.login-form-container .modal-header {
\n    background: #fff;
\n    border-radius: 5px 5px 0 0;
\n    box-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
\n    color: #333;
\n}
\n
\n.select:after {
\n    border-top-color: #F5B614;
\n}
\n
\n.form-select:before {
\n    background-image: -webkit-linear-gradient(left, transparent 0, transparent calc(100% - 2.75em), #F5B614 calc(100% - 2.75em), #F5B614 100%);
\n    background-image: linear-gradient(to right, transparent 0, transparent calc(100% - 2.75em), #F5B614 calc(100% - 2.75em), #F5B614 100%);
\n}
\n
\n.button,
\n.formrt button,
\n.formrt [type=\"submit\"],
\n.formrt [type=\"reset\"] {
\n    background-color: #F5B614;
\n    color: #fff;
\n    font-weight: 400;
\n}
\n
\n.btn-primary {
\n    background-color: #e2e1df;
\n    border-color: transparent;
\n    color: #fff;
\n}
\n
\n.button\-\-continue.inverse {
\n    background-color: #fff;
\n    border: 1px solid #eee;
\n    color: #eee;
\n}
\n
\n.button\-\-cancel {
\n    background: #FFF;
\n    border: 1px solid #F00;
\n    color: #F00;
\n}
\n
\n.button\-\-pay.inverse {
\n    background: #FFF;
\n    border: 1px solid #bfb8bf;
\n    color: #bfb8bf;
\n}
\n
\n.button\-\-book {
\n    background-color: #F5B614;
\n}
\n
\n.button\-\-book.inverse {
\n    background: #FFF;
\n    border-color: #F5B614;
\n    color: #F5B614;
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
\n.button\-\-send {
\n    background: #198dbe;
\n    color: #fff;
\n}
\n
\n.button\-\-send.inverse {
\n    background: #fff;
\n    border-color: #198dbe;
\n    color: #198dbe;
\n}
\n
\n.button\-\-enquire {
\n    background: #F5B614;
\n    color: #fff;
\n}
\n
\n.button.button\-\-black {
\n    background: #000;
\n    color: #fff;
\n}
\n
\n.button.button\-\-black:hover {
\n    background: #333;
\n    color: #fff;
\n}
\n
\n.header-action:nth-child(odd) .button {
\n    background: #F5B614;
\n    color: #fff;
\n}
\n
\n.header-action:nth-child(even) .button {
\n    background: #333;
\n    color: #fff;
\n}
\n
\n.formErrorContent,
\n.formErrorArrow div {
\n    background: #F5B614;
\n}
\n
\n.login-form-container.login-form-container .nav-tabs > li > a {
\n    color: #333;
\n    font-weight: bold;
\n    opacity: .6;
\n    text-transform: uppercase;
\n}
\n
\n.login-form-container.login-form-container .nav-tabs > .active > a:after {
\n    background-color: #f5b614;
\n}
\n
\n.login-form-container.login-form-container .nav-tabs > li > a:hover {
\n    color: #333;
\n    opacity: 1;
\n}
\n
\n.login-form-container.login-form-container .nav-tabs > li.active > a {
\n    color: #f5b614;
\n    opacity: 1;
\n}
\n
\n.login-form-container.login-form-container .client-logo {
\n    height: 55px;
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
\n.popup_box.alert-add     { border-color: #eee; }
\n.popup_box.alert-remove  { border-color: #b4b4b4; }
\n
\n.popup_box .alert-icon [fill]   {   fill: #F5B614; }
\n.popup_box .alert-icon [stroke] { stroke: #F5B614; }
\n
\n
\n\/\* Header \*\/
\n.header,
\n.mobile-breadcrumbs,
\n.dropdown-menu-header {
\n    background-color: #fff;
\n    color: #333;
\n}
\n
\n.header {
\n    border-bottom: 1px solid #f5b614;
\n}
\n
\n.header-menu .level_2 a:hover,
\n.header-menu .level_2:hover > a {
\n    color: #333333;
\n}
\n
\n.header-item > a:not(.button) {
\n    color: #333;
\n}
\n
\n.header-menu-section > a {
\n    border-color: #C4C2BD;
\n}
\n
\n.header-menu-section\-\-account > a {
\n    border-right: none;
\n}
\n
\n.header-menu .level_1 > a,
\n.mobile-menu .level_1 > a,
\n.mobile-menu .level_1 > button,
\n.mobile-menu-level3-section .mobile-menu-list > a {
\n    color: #F5B614;
\n}
\n
\n.header-menu .level_1 > a {
\n    color: #000;
\n    font-weight: 700;
\n}
\n
\n.header-menu .level_2 a:before {
\n    border-left-color: #F5B614;
\n}
\n
\n.header-menu .level_2 a:hover:before,
\n.header-menu .level_2:hover > a:hover {
\n    border-left-color: #333333;
\n}
\n
\n.header-menu .level_3 {
\n    border-bottom-color: #F5B614;
\n}
\n
\n.mobile-menu-top strong,
\n.mobile-menu-top-avatar,
\n.mobile-menu-button-group-icon,
\n.header-cart-breakdown,
\n.final_price_value {
\n    color: #2e4076;
\n}
\n
\n.header-cart-amount {
\n    color: #333333;
\n}
\n
\n.header-cart > .header-cart-button svg > polyline {
\n    stroke: #F5B614;
\n}
\n
\n.header-cart > .header-cart-button svg > circle {
\n    fill: #F5B614;
\n}
\n
\n.mobile-menu li.active > a,
\n.checkout-item-title {
\n    color: #F5B614;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .header-menu-section > a:after {
\n        border-top-color: #333;
\n    }
\n
\n    .header-right {
\n        display: flex;
\n    }
\n
\n    .header-action\-\-login {
\n        order: 1
\n    }
\n}
\n
\n\/\* Quick Contact \*\/
\n@media screen and (max-width: 767px) {
\n    .quick_contact-item > a.active,
\n    .quick_contact-item > a:hover,
\n    .quick_contact-item > a:active {
\n        color: #F5B614;
\n    }
\n}
\n
\n\/\* Sidebar \*\/
\n.sidebar-section > h2 {
\n    background: #eee;
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
\n    color: #333333;
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
\n    border-color: #bfbfbf;
\n}
\n
\n.page-content h1 { color: #212121; font-weight: normal; }
\n.page-content h2 { color: #212121; font-weight: normal; }
\n.page-content h3 { color: #212121; }
\n.page-content h4 { color: #212121; }
\n.page-content h5 { color: #212121; }
\n.page-content h6 { color: #212121; }
\n.page-content p { font-size: 16px; }
\n
\n.page-content li:before {
\n    color: #F5B614;
\n}
\n
\n.page-content a:not([class]),
\n.page-content .button\-\-link {
\n    color: #333333;
\n}
\n
\n.page-content a:not([class]):visited {
\n    color: #551a8b;
\n}
\n
\n.page-content hr {
\n    border-color: #F5B614;
\n}
\n
\n\/\* Banner search \*\/
\n.banner-search-title {
\n    background: #d2d2d2;
\n    color: #fff;
\n}
\n
\n.banner-search .fa {
\n    color: #F5B614;
\n}
\n
\n.banner-search-title .fa {
\n    color: #fff;
\n}
\n
\n.banner-search form {
\n    background: #F5B614;
\n}
\n
\n.previous_search_text {
\n    color: #fff;
\n}
\n
\n.banner-overlay-content {
\n    color: #F5B614;
\n    font-size: 24px;
\n}
\n
\n.banner-overlay-content h1 {
\n    font-size: 36px;
\n    font-weight: 900;
\n}
\n
\n.banner-overlay-content h2 {
\n    font-size: 1.25em;
\n    font-weight: bold;
\n    line-height: 1;
\n}
\n
\n.banner-overlay-content h2,
\n.banner-overlay-content p {
\n    margin: 0;
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
\n    .banner-overlay .row { background-repeat: no-repeat; }
\n    .swiper-slide .banner-image { background-position: center; }
\n
\n    .swiper-slide .banner-overlay {
\n        background-position: top center;
\n    }
\n
\n    .banner-slide\-\-left .banner-overlay .row {
\n        background-image: url(\'\/shared_media\/pallaskenry\/media\/photos\/content\/banner_overlay_left.png\');
\n        background-position-x: left;
\n    }
\n
\n    .banner-slide\-\-right .banner-overlay .row {
\n        background-image: url(\'\/shared_media\/pallaskenry\/media\/photos\/content\/banner_overlay_right.png\');
\n        background-position-x: right;
\n    }
\n
\n    .banner-slide\-\-center .banner-overlay {
\n        background: rgba(66, 91, 168, .333)
\n    }
\n}
\n
\n.search-drilldown h3 {
\n    color: #F5B614;
\n}
\n
\n.search-drilldown-column p {
\n    color: #333333;
\n}
\n
\n.search-drilldown-column a.active {
\n    background-color: #F5B614;
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
\n        border-top-color: #F5B614;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .header-action:only-child {
\n        border-right: 1px solid #F5B614;
\n    }
\n
\n    .search-drilldown-column {
\n        border-color: #333333;
\n    }
\n}
\n
\n\/\* Calendar \*\/
\n.eventCalendar-wrap {
\n    border-color: #bfbfbf;
\n}
\n
\n.eventsCalendar-slider {
\n    background: #F5B614;
\n    background: -webkit-linear-gradient(#F5B614, #5372d3);
\n    background: linear-gradient(#F5B614, #5372d3);
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
\n    color: #F5B614;
\n}
\n
\n.eventsCalendar-subtitle,
\n.eventsCalendar-list > li > time {
\n    color: #F5B614;
\n}
\n
\n.eventsCalendar-list > li {
\n    border-bottom-color: #bfbfbf;
\n}
\n
\n\/\* News feeds \*\/
\n.news-section {
\n    background: #e2e2e2;
\n    box-shadow: 1px 1px 10px #ccc;
\n}
\n
\n.news-slider-link {
\n  color: #eee;
\n}
\n
\n.news-slider-title {
\n    color: #F5B614;
\n    background-color: #e2e2e2;
\n}
\n
\n.swiper-pagination-bullet {
\n    background-color: #eee;
\n    border-color: #A6AEAD;
\n    box-shadow: inset 0 1px 1px #aaa;
\n}
\n
\n.swiper-pagination-bullet-active {
\n    background-color: #FFF;
\n}
\n
\n.news-result-date {
\n    background-color: #F5B614;
\n    color: #FFF;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .news-result + .news-result:before {
\n        background: linear-gradient(to right, transparent 0, #F5B614 10%, #F5B614 90%, transparent 100%);
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .news-result + .news-result {
\n        border-color: #F5B614;
\n    }
\n}
\n
\n.news-story-navigation a {
\n    color: #333333;
\n}
\n
\n.news-story-social {
\n    border-color: #F5B614;
\n}
\n
\n.news-story-share_icon {
\n    color: #333333;
\n}
\n
\n.news-story-social-link svg {
\n    background: #333333;
\n}
\n
\n.testimonial-signature {
\n    color: #F5B614;
\n}
\n
\n\/\* Panels \*\/
\n.panel {
\n    background-color: #fff;
\n}
\n
\n.carousel-section .panel {
\n    border-color: #bfb8bf;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .panels-feed\-\-home_content > .column:after {
\n        background: #F5B614;
\n        background: linear-gradient(to right, #E6F3C8 0%, #F5B614 20%, #F5B614 80%, #E6F3C8 100%);
\n    }
\n}
\n
\n.bar {
\n    background: #F3F5F5;
\n    background: rgba(243, 245, 245, .8);
\n    box-shadow: 0 1px 1px #aaa;
\n}
\n
\n.bar-icon {
\n    background: #eee;
\n    color: #fff;
\n}
\n
\n.bar-icon svg {
\n  fill: #fff;
\n}
\n
\n.bar-text {
\n    color: #eee;
\n}
\n
\n.panel-item.has_form {
\n    background-color: #F5B614;
\n    color: #fff;
\n}
\n
\n.panel-item.has_form .button {
\n    background-color: #fff;
\n    border-color: #F5B614;
\n    color: #F5B614;
\n}
\n
\n.panel-item-image:after {
\n    background-image: url(\'\/shared_media\/pallaskenry\/media\/photos\/content\/panel_overlay.png\');
\n}
\n
\n.panel-item.has_image .panel-item-text {
\n    color: #F5B614;
\n    top: 25%;
\n    left: 50%;
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
\n    background: #F5B614;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-grid .course-widget-price {
\n    background-color: #F5B614;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-list .course-widget-price-original,
\n.course-list\-\-list .course-widget-price-current {
\n    color: #F5B614;
\n}
\n
\n.course-list-grid .course-widget-time_and_date {
\n    border-color: #b7b7b7;
\n}
\n
\n.course-list\-\-grid .course-widget-time_and_date\-\-with_options,
\n.course-widget-time_and_date\-\-with_options select {
\n    background: #eee;
\n    color: #fff;
\n}
\n
\n.course-list\-\-list .course-widget-location_and_tags {border-color: #CCC; }
\n
\n.pagination-prev a,
\n.pagination-next a {
\n    background: #333333;
\n    color: #fff;
\n}
\n
\n.pagination-prev a:before,
\n.pagination-next a:before {
\n    border-color: #fff;
\n}
\n
\n.course-banner-overlay {
\n    background-color: rgba(25, 142, 190, .8);
\n    color: #fff;
\n}
\n
\n.fixed_sidebar-header {
\n    background: #333333;
\n    color: #fff;
\n}
\n
\n.booking-form h2 {
\n    border: none;
\n}
\n
\n.booking-required_field-note {
\n    color: #F5B614;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .contact-map-overlay {
\n        background-color: #333333;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .contact-map-overlay-content {
\n        background: #333333;
\n        background: rgba(25, 142, 190, .8);
\n    }
\n}
\n
\n.availability-timeslot .highlight {
\n    color: #f9951d;
\n}
\n
\n.availability-timeslot.booked {
\n    border-color: #F5B614;
\n}
\n
\n.availability-timeslot.booked .highlight {
\n    color: #F5B614;
\n}
\n
\n.availability-book .highlight {
\n    color: #FFF;
\n}
\n
\n.availability-book.trial {
\n    margin-top: 1em;
\n}
\n
\n.timeline-swiper .swiper-slide.selected {
\n    background: #F5B614;
\n    color: #fff;
\n}
\n
\n.timeline-swiper-highlight {
\n    color: #F5B614;
\n}
\n
\n.timeline-swiper-prev,
\n.timeline-swiper-next {
\n    color: #f9951d;
\n}
\n
\n\/\* Footer \*\/
\n.footer {
\n    box-shadow: 2px -2px 10px #eee;
\n}
\n.footer-stats-list {
\n    color: #000000;
\n}
\n
\n.footer-slogan {
\n    color: #000000;
\n}
\n
\n.footer-stats {
\n    background: #F5B614;
\n    min-height: 0;
\n}
\n
\n.footer-social,
\n.footer-columns,
\n.footer-copyright {
\n    background-color: #fff;
\n}
\n
\n.footer-social,
\n.footer-columns,
\n.footer-copyright {
\n    border-top: 1px solid #F5B614;
\n}
\n
\n.footer-social h2 {
\n    display: block;
\n    font-size: 1.3rem;
\n    margin: 0 0 1.25rem;
\n}
\n
\n.social-icon {
\n    background: none;
\n    border: 2px solid #333;
\n    color: #333;
\n    width: 3.125rem;
\n    height: 3.125rem;
\n    line-height: 1.5;
\n}
\n
\n.social-icon:hover {
\n    background: #F5B614;
\n    color: #fff;
\n    border-color: #F5B614;
\n}
\n
\n.social-icon:before {
\n    font-family: FontAwesome;
\n    font-size: 2rem;
\n}
\n
\n.social-icon\-\-facebook:before  { content: \"\\f09a\"; }
\n.social-icon\-\-instagram:before { content: \"\\f16d\"; }
\n.social-icon\-\-linkedin:before  { content: \"\\f08c\"; }
\n.social-icon\-\-snapchat:before  { content: \"\\f2ac\"; }
\n.social-icon\-\-twitter:before   { content: \"\\f099\"; }
\n.social-icon\-\-youtube:before   { content: \"\\f16a\"; }
\n
\n.footer-column-title {
\n    color: #F5B614;
\n}
\n
\n.footer-column a:not([class]) {
\n    color: #F5B614;
\n}
\n
\n.footer-column a:not([class]):hover {
\n    color: var(\-\-primary-hover);
\n    text-decoration: underline;
\n}
\n
\n.footer-column h4 {
\n    font-weight: bold;
\n}
\n
\n.footer-columns\-\-footer_bottom {
\n    font-size: 13px;
\n    line-height: 1.5;
\n}
\n
\n.footer .form-input::-webkit-input-placeholder { color: #000; font-weight: 300; }
\n.footer .form-input::-moz-placeholder          { color: #000; font-weight: 300; }
\n.footer .form-input:-ms-input-placeholder      { color: #000; font-weight: 300; }
\n
\n.newsletter-signup-form .button {
\n    background-color: #F5B614;
\n}
\n
\n\/\* Dropdown filters \*\/
\n.search-filter-total {
\n    color: #F5B614;
\n}
\n
\n.search-filters :checked ~ .form-checkbox-helper,
\n.search-filters :checked ~ .form-radio-helper,
\n.search-filters :checked ~ .form-checkbox-label,
\n.search-filters :checked ~ .form-radio-label {
\n    border-color: #F5B614;
\n    color: #F5B614;
\n}
\n
\n.search-filters :checked + .form-radio-helper:after {
\n    background-color: #F5B614;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-filter-dropdown.filter-active > button,
\n    .search-filters-clear {
\n        color: #F5B614;
\n    }
\n
\n    .checkout-heading {
\n        background-color: #F5B614;
\n        color: #fff;
\n    }
\n}
\n
\n@media screen and (max-width: 767px) {
\n	  .search-filter-dropdown .dropdown-menu > li {
\n		  font-size: 13px;
\n		  width: 100%;
\n	  }
\n}
\n
\n\/\* Misc \*\/
\n.contact\-\-left .ui-tabs-nav .ui-tabs-active a {
\n    background: #F5B614;
\n    border-color:#F5B614;
\n    color: #fff;
\n}
\n
\n.checkout-right-sect .btn-close:hover {
\n    color: #F5B614;
\n    border-color: #F5B614;
\n}
\n
\n.checkout-right-sect .sub-total {
\n    color: #333333;
\n}
\n.total-pay .form-checkbox {
\n    margin: 5px 10px;
\n    display: inline-block;
\n }
\n
\n.checkout-right-sect .total-pay {
\n    overflow: auto;
\n    padding: 0px;
\n}
\n
\n.checkout-right-sect .item-summary-head {
\n    color: #F5B514;
\n}
\n
\n.checkout-progress li a:after {
\n    background-color: #fff;
\n    border-color: #F5B614;
\n}
\n
\n.checkout-progress li.curr a:after {
\n    background: #F5B614;
\n}
\n
\n.checkout-progress li + li:before {
\n    border-color: #F5B614;
\n}
\n
\n.checkout-progress .curr ~ li:before {
\n    border-color: #c8c8c8;
\n}
\n
\n.checkout-breakdown > .sub-total{
\n    background-color: #e2e1df;
\n    margin-top: 0px !important;
\n}
\n
\n.total-pay .checkout-breakdown > li{
\n    padding: 10px;
\n}
\n.btn-primary.checkout-complete_booking, input#continue-button, input.button  {
\n    background-color: #f5b614;
\n    color: #fff;
\n}
\n.search-package-available h2 {
\n    color: #f5b614;
\n}
\n
\n.search-package-available .available-text  h4 {
\n    border-color: #eee;
\n    color: #333333;
\n}
\n
\n.search-package-available .show-more {
\n    background: #F5B614;
\n    color: #fff;
\n}
\n
\n.prepay-box h6 {
\n    color: #F5B614;
\n}
\n
\n.prepay-box li.total{
\n    color: #f5b614;
\n}
\n.custom-calendar .booking-date-button {
\n    background-color: #F5B614;
\n    color: #fff;
\n}
\n
\n.custom-calendar .booking-date-button:hover {
\n    background-color: #E28B3D;
\n}
\n
\n.custom-calendar button.booking-date-button.active {
\n    background-color: #fff;
\n    color: #F5B614;
\n}
\n
\n.course-activity-alert,
\n.details-wrap .left-place {
\n    color: #F75A5F;
\n}
\n
\n.number-of-people-viewing {
\n    color: #fe8585;
\n}
\n
\n.search-calendar-course-image .fa {
\n    background-color: #F5B614;
\n    color: #fff;
\n}
\n
\n.custom-calendar tbody td.active,
\n.custom-calendar tbody td.active:hover {
\n    background-color: #fff;
\n    color: #F5B614;
\n}
\n
\n.custom-calendar tbody tr:first-child td {
\n    color: #222;
\n}
\n
\n.package-offers-wrap h2 {
\n    color: #F5B614;
\n    border-color: #c5cecd;
\n}
\n
\n.package-offers-wrap h3 {
\n    color: #F5B614;
\n}
\n
\n.package-offers-wrap .summary-wrap .more,
\n.classes-details-wrap .details-wrap li:first-child {
\n    color: #F5B614;
\n}
\n
\n.classes-details-wrap .details-wrap li:first-child {
\n  background-color: #F5B614;
\n}
\n
\n.details-wrap .remove-booking,
\n.details-wrap .wishlist.remove{
\n    color: #F5B614;
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
\n    color: #F5B614;
\n}
\n
\n.course-details-icon:before {
\n    content: \'\\f001\';
\n    font-family: \'FontAwesome\';
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
\n    color: #F5B614;
\n}
\n
\n.details-wrap:hover li:first-child {
\n    background-color: #F5B614;
\n}
\n
\n.details-wrap:hover .sidelines::before,
\n.details-wrap:hover .sidelines::after,
\n.details-wrap:hover .price-wrap {
\n    border-color:#F5B614;
\n}
\n
\n
\n\/\* course results booked \*\/
\n.details-wrap.booked {
\n    border-color:#F5B614;
\n    background-color: #f3f3f3;
\n}
\n
\n.details-wrap.booked .time,
\n.details-wrap.booked .price,
\n.details-wrap.booked .fa-book {
\n    color: #F5B614;
\n}
\n.details-wrap.booked li:first-child {
\n    background-color: #F5B614;
\n}
\n
\n.details-wrap.booked .sidelines::before,
\n.details-wrap.booked .sidelines::after,
\n.details-wrap.booked .price-wrap {
\n    border-color:#F5B614;
\n}
\n
\n.classes-details-wrap .alert-wrap {
\n    background-color: #F5B614;
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
\n    color: #F5B614;
\n}
\n
\n.custom-calendar .booking-date-button.already_booked {
\n    background-color: #F5B614;
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
\n    background-image: url(\"data:image\/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M0%2C22L22%2C0l2.1%2C2.1L4.2%2C22l19.9%2C19.9L22%2C44L0%2C22L0%2C22L0%2C22z\'%20fill%3D\'%23425ba9\'%2F%3E%3C%2Fsvg%3E\");
\n}
\n
\n.swiper-button-next {
\n    background-image: url(\"data:image\/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M27%2C22L27%2C22L5%2C44l-2.1-2.1L22.8%2C22L2.9%2C2.1L5%2C0L27%2C22L27%2C22z\'%20fill%3D\'%23425ba9\'%2F%3E%3C%2Fsvg%3E\");
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
\n
"
WHERE
  `stub` = '48'
;;

UPDATE `engine_site_themes` SET `email_header_color` = '#f5b614', `email_link_color` = '#f5b614' WHERE `stub` = '48';;
