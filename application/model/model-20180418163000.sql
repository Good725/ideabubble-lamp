/*
ts:2018-04-18 16:30:00
*/


/* Add the 35 (PAC school) theme */
DELIMITER  ;;
INSERT INTO
  `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '35', '35', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_themes`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_themes` WHERE `stub` = '35')
    LIMIT 1
;;


/* Add the '35' theme styles */
UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = "@import url(\'https:\/\/fonts.googleapis.com\/css?family=Roboto:300,300i,400,400i,700,700i,900\');
\n
\n:root {
\n    \-\-primary: #425ba9;   \-\-primary-hover: #526cb7;   \-\-primary-active: #384e8f;
\n    \-\-secondary: #f5f5f5; \-\-secondary-hover: #d3d3d3; \-\-secondary-active: #e6e6e6;
\n    \-\-success: #f8951b;   \-\-success-hover: #f6a23c;   \-\-success-active: #e5830b;
\n    \-\-info: #5bc0de;      \-\-info-hover: #31b0d5;      \-\-info-active: #269abc;
\n    \-\-warning: #e8a917;   \-\-warning-hover: #e7b236;   \-\-warning-active: #c89214;
\n    \-\-danger: #df1e39;    \-\-danger-hover: #bd362f;    \-\-danger-active: #c9302c;
\n}
\nhtml,
\nbutton {
\n    font-family: Roboto, Helvetica, Arila, sans-serif;
\n}
\n
\nbody {
\n    background-color: #fff;
\n    color: #212121;
\n}
\n
\n.table thead {
\n    background: #425ba9;
\n    color: #FFF;
\n}
\n
\n.badge {
\n    background: #425ba9;
\n    color: #fff;
\n}
\n
\n.db-sidebar .sidebar-menu li a:hover,
\n.db-sidebar .sidebar-menu li a.active {
\n    background-color: #425ba9;
\n    color: #fff;
\n}
\n
\n.popup-header {
\n    background: #425ba9;
\n    color: #fff;
\n}
\n
\n.button.course-banner-button.cl_bg {
\n    background-color: #425ba9;
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
\n    color: #425ba9;
\n}
\n
\n.autotimetable .new_date {
\n    border-color: #425ba9;
\n}
\n
\n.autotimetable .new_date td:nth-child(1) {
\n    background-color: #425ba9;
\n    color: #fff;
\n}
\n
\n:checked + .seating-selector-checkbox-helper:after {
\n    color: #425ba9;
\n}
\n
\n.seating-selector-option-radio:checked + .button {
\n    background: #425ba9;
\n    color: #fff;
\n}
\n
\n.seating-selector-option-hover {
\n    background-color: #425ba9;
\n    color: #fff;
\n}
\n
\n\/\* Forms \*\/
\n.input_group-icon,
\n.login-form-container.login-form-container .modal-header {
\n    background: #425ba9;
\n    color: #fff;
\n}
\n
\n.select:after {
\n    border-top-color: #425ba9;
\n}
\n
\n.form-select:before {
\n    background-image: -webkit-linear-gradient(left, transparent 0, transparent calc(100% - 2.75em), #425ba9 calc(100% - 2.75em), #425ba9 100%);
\n    background-image: linear-gradient(to right, transparent 0, transparent calc(100% - 2.75em), #425ba9 calc(100% - 2.75em), #425ba9 100%);
\n}
\n
\n.button,
\n.formrt button,
\n.formrt [type=\"submit\"],
\n.formrt [type=\"reset\"] {
\n    background-color: #425ba9;
\n}
\n
\n.button\-\-continue,
\n.btn-primary {
\n    background-color: #f8961d;
\n    border-color: transparent;
\n    color: #fff;
\n}
\n
\n.button\-\-continue.inverse {
\n    background-color: #fff;
\n    border: 1px solid #f8961d;
\n    color: #f8961d;
\n}
\n
\n.button\-\-cancel {
\n    background: #FFF;
\n    border: 1px solid #F00;
\n    color: #F00;
\n}
\n
\n.button\-\-pay {
\n    background-color: #bfb8bf;
\n}
\n
\n.button\-\-pay.inverse {
\n    background: #FFF;
\n    border: 1px solid #bfb8bf;
\n    color: #bfb8bf;
\n}
\n
\n.button\-\-book {
\n    background-color: #425ba9;
\n}
\n
\n.button\-\-book.inverse {
\n    background: #FFF;
\n    border-color: #425ba9;
\n    color: #425ba9;
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
\n    background: #425ba9;
\n    color: #fff;
\n}
\n
\n.header-action:nth-child(odd) .button {
\n    background: #fff;
\n    border-color: #425ba9;
\n    color: #425ba9;
\n}
\n
\n.header-action:nth-child(even) .button {
\n    background: #f8961d;
\n    color: #fff;
\n}
\n
\n.formErrorContent,
\n.formErrorArrow div {
\n    background: #425ba9;
\n}
\n
\n.login-form-container a {
\n    color: #f6a23c;
\n}
\n
\n.login-form-container.login-form-container .nav-tabs > .active > a:after {
\n    background-color: #f6a23c;
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
\n.popup_box.alert-add     { border-color: #f8961d; }
\n.popup_box.alert-remove  { border-color: #b4b4b4; }
\n
\n.popup_box .alert-icon [fill]   {   fill: #425ba9; }
\n.popup_box .alert-icon [stroke] { stroke: #425ba9; }
\n
\n
\n\/\* Header \*\/
\n.header,
\n.mobile-breadcrumbs,
\n.dropdown-menu-header {
\n    background-color: #425ba9;
\n    color: #fff;
\n}
\n
\n.header-menu .level_2 a:hover,
\n.header-menu .level_2:hover > a {
\n    color: #198ebe;
\n}
\n
\n.header-item > a:not(.button) {
\n    color: #fff;
\n}
\n
\n.header-menu-section > a {
\n    border-color: #687bbb;
\n}
\n
\n.header-menu .level_1 > a,
\n.mobile-menu .level_1 > a,
\n.mobile-menu .level_1 > button,
\n.mobile-menu-level3-section .mobile-menu-list > a {
\n    color: #425ba9;
\n}
\n
\n.header-menu .level_2 a:before {
\n    border-left-color: #425ba9;
\n}
\n
\n.header-menu .level_2 a:hover:before,
\n.header-menu .level_2:hover > a:hover {
\n    border-left-color: #198ebe;
\n}
\n
\n.header-menu .level_3 {
\n    border-bottom-color: #425ba9;
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
\n    color: #fff;
\n}
\n
\n.mobile-menu li.active > a,
\n.checkout-item-title {
\n    color: #d02a27;
\n}
\n
\n\/\* Quick Contact \*\/
\n@media screen and (max-width: 767px) {
\n    .quick_contact-item > a.active,
\n    .quick_contact-item > a:hover,
\n    .quick_contact-item > a:active {
\n        color: #425ba9;
\n    }
\n}
\n
\n\/\* Sidebar \*\/
\n.sidebar-section > h2 {
\n    background: #f8961d;
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
\n    color: #198ebe;
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
\n    color: #425ba9;
\n}
\n
\n.page-content a:not([class]),
\n.page-content .button\-\-link {
\n    color: #198ebe;
\n}
\n
\n.page-content a:not([class]):visited {
\n    color: #551a8b;
\n}
\n
\n.page-content hr {
\n    border-color: #425ba9;
\n}
\n
\n\/\* Banner search \*\/
\n.banner-search-title {
\n    background: #2e4076;
\n    color: #fff;
\n}
\n
\n.banner-search .fa {
\n    color: #425ba9;
\n}
\n
\n.banner-search-title .fa {
\n    color: #fff;
\n}
\n
\n.banner-search form {
\n    background: #425ba9;
\n}
\n
\n.previous_search_text {
\n    color: #fff;
\n}
\n
\n.banner-overlay-content {
\n    color: #425ba9;
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
\n    color: #425ba9;
\n}
\n
\n.search-drilldown-column p {
\n    color: #198ebe;
\n}
\n
\n.search-drilldown-column a.active {
\n    background-color: #425ba9;
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
\n        border-top-color: #425ba9;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .header-action:only-child {
\n        border-right: 1px solid #425ba9;
\n    }
\n
\n    .search-drilldown-column {
\n        border-color: #198ebe;
\n    }
\n}
\n
\n\/\* Calendar \*\/
\n.eventCalendar-wrap {
\n    border-color: #bfbfbf;
\n}
\n
\n.eventsCalendar-slider {
\n    background: #425ba9;
\n    background: -webkit-linear-gradient(#425ba9, #5372d3);
\n    background: linear-gradient(#425ba9, #5372d3);
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
\n    color: #425ba9;
\n}
\n
\n.eventsCalendar-subtitle,
\n.eventsCalendar-list > li > time {
\n    color: #425ba9;
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
\n  color: #f8961d;
\n}
\n
\n.news-slider-title {
\n    color: #425ba9;
\n    background-color: #e2e2e2;
\n}
\n
\n.swiper-pagination-bullet {
\n    background-color: #f8961d;
\n    border-color: #A6AEAD;
\n    box-shadow: inset 0 1px 1px #aaa;
\n}
\n
\n.swiper-pagination-bullet-active {
\n    background-color: #FFF;
\n}
\n
\n.news-result-date {
\n    background-color: #425ba9;
\n    color: #FFF;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .news-result + .news-result:before {
\n        background: linear-gradient(to right, transparent 0, #425ba9 10%, #425ba9 90%, transparent 100%);
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .news-result + .news-result {
\n        border-color: #425ba9;
\n    }
\n}
\n
\n.news-story-navigation a {
\n    color: #198ebe;
\n}
\n
\n.news-story-social {
\n    border-color: #425ba9;
\n}
\n
\n.news-story-share_icon {
\n    color: #198ebe;
\n}
\n
\n.news-story-social-link svg {
\n    background: #198ebe;
\n}
\n
\n.testimonial-signature {
\n    color: #425ba9;
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
\n        background: #425ba9;
\n        background: linear-gradient(to right, #E6F3C8 0%, #425ba9 20%, #425ba9 80%, #E6F3C8 100%);
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
\n    background: #f8961d;
\n    color: #fff;
\n}
\n
\n.bar-icon svg {
\n  fill: #fff;
\n}
\n
\n.bar-text {
\n    color: #f8961d;
\n}
\n
\n.panel-item.has_form {
\n    background-color: #425ba9;
\n    color: #fff;
\n}
\n
\n.panel-item.has_form .button {
\n    background-color: #fff;
\n    border-color: #425ba9;
\n    color: #425ba9;
\n}
\n
\n.panel-item-image:after {
\n    background-image: url(\'\/shared_media\/pallaskenry\/media\/photos\/content\/panel_overlay.png\');
\n}
\n
\n.panel-item.has_image .panel-item-text {
\n    color: #425ba9;
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
\n    background: #425ba9;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-grid .course-widget-price {
\n    background-color: #425ba9;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-list .course-widget-price-original,
\n.course-list\-\-list .course-widget-price-current {
\n    color: #425ba9;
\n}
\n
\n.course-list-grid .course-widget-time_and_date {
\n    border-color: #b7b7b7;
\n}
\n
\n.course-list\-\-grid .course-widget-time_and_date\-\-with_options,
\n.course-widget-time_and_date\-\-with_options select {
\n    background: #f8961d;
\n    color: #fff;
\n}
\n
\n.course-list\-\-list .course-widget-location_and_tags {border-color: #CCC; }
\n
\n.pagination-prev a,
\n.pagination-next a {
\n    background: #198ebe;
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
\n    background: #198ebe;
\n    color: #fff;
\n}
\n
\n.booking-form h2 {
\n    border: none;
\n}
\n
\n.booking-required_field-note {
\n    color: #425ba9;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .contact-map-overlay {
\n        background-color: #198ebe;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .contact-map-overlay-content {
\n        background: #198ebe;
\n        background: rgba(25, 142, 190, .8);
\n    }
\n}
\n
\n.availability-timeslot .highlight {
\n    color: #f9951d;
\n}
\n
\n.availability-timeslot.booked {
\n    border-color: #425ba9;
\n}
\n
\n.availability-timeslot.booked .highlight {
\n    color: #425ba9;
\n}
\n
\n.timeline-swiper .swiper-slide.selected {
\n    background: #f9951d;
\n    color: #fff;
\n}
\n
\n.timeline-swiper-highlight {
\n    color: #425ba9;
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
\n    color: #425ba9;
\n}
\n
\n.footer-slogan {
\n    color: #425ba9;
\n}
\n
\n.footer-stats {
\n    background: #fff url(\'\/shared_media\/pallaskenry\/media\/photos\/content\/footer_background.png\') top center;
\n    min-height: 0;
\n}
\n
\n.footer-stat h2:after {
\n    border-color: #f8961d;
\n}
\n
\n.footer-social,
\n.footer-columns,
\n.footer-copyright {
\n    background-color: #f2f2f2;
\n}
\n
\n.footer-social,
\n.footer-columns,
\n.footer-copyright {
\n    border-top: 1px solid #425ba9;
\n}
\n
\n.footer-social h2 {
\n    color: #425ba9;
\n}
\n
\n.footer-column-title {
\n    color: #425ba9;
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
\n.newsletter-signup-form .button {
\n    background-color: #f8961d;
\n}
\n
\n\/\* Dropdown filters \*\/
\n.search-filter-total {
\n    color: #425ba9;
\n}
\n
\n.search-filters :checked ~ .form-checkbox-helper,
\n.search-filters :checked ~ .form-radio-helper,
\n.search-filters :checked ~ .form-checkbox-label,
\n.search-filters :checked ~ .form-radio-label {
\n    border-color: #425ba9;
\n    color: #425ba9;
\n}
\n
\n.search-filters :checked + .form-radio-helper:after {
\n    background-color: #425ba9;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-filter-dropdown.filter-active > button,
\n    .search-filters-clear {
\n        color: #425ba9;
\n    }
\n
\n    .checkout-heading {
\n        background-color: #425ba9;
\n        color: #fff;
\n    }
\n}
\n
\n\/\* Misc \*\/
\n.contact\-\-left .ui-tabs-nav .ui-tabs-active a {
\n    background: #425ba9;
\n    border-color:#425ba9;
\n    color: #fff;
\n}
\n
\n.checkout-right-sect .btn-close:hover {
\n    color: #425ba9;
\n    border-color: #425ba9;
\n}
\n
\n.checkout-right-sect .sub-total {
\n    color: #198ebe;
\n}
\n
\n.checkout-progress li a:after {
\n    background-color: #fff;
\n    border-color: #425ba9;
\n}
\n
\n.checkout-progress li.curr a:after {
\n    background: #425ba9;
\n    background: radial-gradient(#6284f0, #425ba9);
\n}
\n
\n.checkout-progress li + li:before {
\n    border-color: #425ba9;
\n}
\n
\n.checkout-progress .curr ~ li:before {
\n    border-color: #c8c8c8;
\n}
\n
\n.search-package-available h2 {
\n    color: #4f4e4f;
\n}
\n
\n.search-package-available .available-text  h4 {
\n    border-color: #eee;
\n    color: #198ebe;
\n}
\n
\n.search-package-available .show-more {
\n    background: #425ba9;
\n    color: #fff;
\n}
\n
\n.prepay-box h6 {
\n    color: #425ba9;
\n}
\n
\n.custom-calendar .booking-date-button {
\n    background-color: #425ba9;
\n    color: #fff;
\n}
\n
\n.custom-calendar .booking-date-button:hover {
\n    background-color: #198ebe;
\n}
\n
\n.custom-calendar button.booking-date-button.active {
\n    background-color: #fff;
\n    color: #425ba9;
\n}
\n
\n.course-activity-alert,
\n.details-wrap .left-place {
\n    color: #F75A5F;
\n}
\n
\n.number-of-people-viewing {
\n    color: #425ba9;
\n}
\n
\n.search-calendar-course-image .fa {
\n    background-color: #425ba9;
\n    color: #fff;
\n}
\n
\n.custom-calendar tbody td.active,
\n.custom-calendar tbody td.active:hover {
\n    background-color: #fff;
\n    color: #425ba9;
\n}
\n
\n.custom-calendar tbody tr:first-child td {
\n    color: #222;
\n}
\n
\n.package-offers-wrap h2 {
\n    color: #425ba9;
\n    border-color: #c5cecd;
\n}
\n
\n.package-offers-wrap h3 {
\n    color: #425ba9;
\n}
\n
\n.package-offers-wrap .summary-wrap .more,
\n.classes-details-wrap .details-wrap li:first-child {
\n    color: #425ba9;
\n}
\n
\n.classes-details-wrap .details-wrap li:first-child {
\n  background-color: #425ba9;
\n}
\n
\n.details-wrap .remove-booking,
\n.details-wrap .wishlist.remove{
\n    color: #425ba9;
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
\n    color: #425ba9;
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
\n    color: #425ba9;
\n}
\n
\n.details-wrap:hover li:first-child {
\n    background-color: #425ba9;
\n}
\n
\n.details-wrap:hover .sidelines::before,
\n.details-wrap:hover .sidelines::after,
\n.details-wrap:hover .price-wrap {
\n    border-color:#425ba9;
\n}
\n
\n
\n\/\* course results booked \*\/
\n.details-wrap.booked {
\n    border-color:#425ba9;
\n    background-color: #f3f3f3;
\n}
\n
\n.details-wrap.booked .time,
\n.details-wrap.booked .price,
\n.details-wrap.booked .fa-book {
\n    color: #425ba9;
\n}
\n.details-wrap.booked li:first-child {
\n    background-color: #425ba9;
\n}
\n
\n.details-wrap.booked .sidelines::before,
\n.details-wrap.booked .sidelines::after,
\n.details-wrap.booked .price-wrap {
\n    border-color:#425ba9;
\n}
\n
\n.classes-details-wrap .alert-wrap {
\n    background-color: #425ba9;
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
\n    color: #425ba9;
\n}
\n
\n.custom-calendar .booking-date-button.already_booked {
\n    background-color: #425ba9;
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
  `stub` = '35'
;;


/* Add availability layout, if it doesn't already exist */
INSERT IGNORE INTO
  `plugin_pages_layouts` (`layout`, `template_id`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`)
SELECT
  'packages_available',
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
  (SELECT * FROM `plugin_pages_layouts` WHERE `layout` = 'packages_available' AND `deleted` = 0)
LIMIT 1
;;

/* Add image presets, if they don't already exist */
INSERT INTO
  `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
SELECT
  'Home panel',
  'panels',
  '124',
  '320',
  'fith',
  '0',
  '0',
  '0',
  'crop',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
FROM
  `plugin_media_shared_media_photo_presets`
WHERE NOT EXISTS
  (SELECT * FROM `plugin_media_shared_media_photo_presets` WHERE `title` = 'Home panel' AND `deleted` = 0)
LIMIT 1
;;

INSERT INTO
  `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
SELECT
  'Home banner',
  'banners',
  '300',
  '1920',
  'fith',
  '1',
  '150',
  '960',
  'fith',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
FROM
  `plugin_media_shared_media_photo_presets`
WHERE NOT EXISTS
  (SELECT * FROM `plugin_media_shared_media_photo_presets` WHERE `title` = 'Home banner' AND `deleted` = 0)
LIMIT 1
;;

INSERT INTO
  `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
SELECT
  'courses',
  'courses',
  '160',
  '210',
  'fit',
  '0',
  '0',
  '0',
  'fit',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
FROM
  `plugin_media_shared_media_photo_presets`
WHERE NOT EXISTS
  (SELECT * FROM `plugin_media_shared_media_photo_presets` WHERE `title` = 'courses' AND `deleted` = 0)
LIMIT 1
;;

INSERT INTO
  `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
SELECT
  'Side panel',
  'panels',
  '220',
  '330',
  'fith',
  '0',
  '0',
  '0',
  'fit',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
FROM
  `plugin_media_shared_media_photo_presets`
WHERE NOT EXISTS
  (SELECT * FROM `plugin_media_shared_media_photo_presets` WHERE `title` IN ('Side panel', 'Side panels', 'side panel', 'side panels') AND `deleted` = 0)
LIMIT 1
;;





/* Add contact form, if one does not already exist */
INSERT INTO
  `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `publish`, `date_modified`, `email_all_fields`, `captcha_enabled`, `captcha_version`, `use_stripe`, `form_id`)
SELECT
  'Contact Us',
  'frontend/formprocessor/',
  'POST',
  '<input type=\"hidden\" name=\"subject\" value=\"Contact form\" />
\n<input type=\"hidden\" name=\"business_name\" value=\"\" />
\n<input type=\"hidden\" name=\"redirect\" value=\"thank-you.html\" />
\n<input type=\"hidden\" name=\"event\" value=\"contact-form\" />
\n<input type=\"hidden\" name=\"trigger\" value=\"custom_form\" />
\n<input type=\"hidden\" name=\"form_type\" value=\"Contact Form\" />
\n<input type=\"hidden\" name=\"form_identifier\" value=\"contact_\" />
\n<input type=\"hidden\" name=\"email_template\" value=\"contactformmail\" />
\n<li>
\n    <label for=\"contact_form_name\">Name</label>
\n    <input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"contact_form_name\" placeholder=\"Enter name\" />
\n</li>
\n<li>
\n    <label for=\"contact_form_address\">Address</label>
\n    <textarea name=\"contact_form_address\" id=\"contact_form_address\" class=\"validate[required]\" placeholder=\"Enter address\"></textarea>
\n</li>
\n<li>
\n    <label for=\"contact_form_email_address\">Email</label>
\n    <input type=\"text\" class=\"validate[required]\" name=\"contact_form_email_address\" id=\"contact_form_email_address\" placeholder=\"Enter email address\">
\n</li>
\n<li>
\n    <label for=\"contact_form_tel\">Phone</label>
\n    <input type=\"text\" name=\"contact_form_tel\" id=\"contact_form_tel\" class=\"validate[required]\" placeholder=\"Enter phone number\">
\n</li>
\n<li>
\n    <label for=\"contact_form_message\">Message</label>
\n    <textarea name=\"contact_form_message\" class=\"validate[required]\" id=\"contact_form_message\" placeholder=\"Type your message here\"></textarea>
\n</li>
\n<li>
\n    <label for=\"subscribe\" style=\"\n    float: none;\n\">tick this box to let us get in touch with you</label>
\n    <input type=\"checkbox\" id=\"subscribe\" name=\"contact_form_add_to_list\" />
\n</li>
\n<li>
\n    <label></label>
\n    <button type=\"submit\" name=\"submit1\" id=\"submit1\" value=\"Send Email\">Submit</button>
\n</li>',
  '1',
  CURRENT_TIMESTAMP,
  '0',
  '0',
  '1',
  '0',
  'Contact Us'
FROM
  `plugin_formbuilder_forms`
WHERE NOT EXISTS
  (SELECT * FROM `plugin_formbuilder_forms` WHERE `form_name` IN ('Contact Us', 'ContactUs') AND `deleted` != 1)
LIMIT 1
;;


