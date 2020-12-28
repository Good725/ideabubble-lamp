/*
ts:2018-03-13 16:20:00
*/


/* Add the 33 (STAC red) theme */
INSERT INTO
  `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '33', '33', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_themes`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_themes` WHERE `stub` = '33')
    LIMIT 1
;;


/* Add the '33' theme styles */
DELIMITER  ;;
UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = "@import url(\'https:\/\/fonts.googleapis.com\/css?family=Roboto:300,300i,400,400i,700,700i,900\');
\n
\n:root {
\n    \-\-primary: #d02a27;   \-\-primary-hover: #dd4d4b;   \-\-primary-active: #b42522;
\n    \-\-secondary: #f5f5f5; \-\-secondary-hover: #d3d3d3; \-\-secondary-active: #e6e6e6;
\n    \-\-success: #43b649;   \-\-success-hover: #d3d3d3;   \-\-success-active: #e6e6e6;
\n    \-\-info: #17a2b8;      \-\-info-hover: #2f96b4;      \-\-info-active: #31b0d5;
\n    \-\-warning: #ffc107;   \-\-warning-hover: #f89406;   \-\-warning-active: #ec971f;
\n    \-\-danger: #dc3545;    \-\-danger-hover: #bd362f;    \-\-danger-active: #c9302c;
\n}
\n
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
\n    background: #d02a27;
\n    color: #FFF;
\n}
\n
\n.badge {
\n    background: #43b649;
\n    color: #fff;
\n}
\n
\n.db-sidebar .sidebar-menu li a:hover,
\n.db-sidebar .sidebar-menu li a.active {
\n    background-color: #d02a27;
\n    color: #fff;
\n}
\n
\n.popup-header {
\n    background: #d02a27;
\n    color: #fff;
\n}
\n
\n.button.course-banner-button.cl_bg {
\n    background-color: #d02a27;
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
\n    color: #6dc4eb;
\n}
\n
\n.autotimetable .new_date {
\n    border-color: #69C3ED;
\n}
\n
\n.autotimetable .new_date td:nth-child(1) {
\n    background-color: #6dc4eb;
\n    color: white;
\n}
\n
\n:checked + .seating-selector-checkbox-helper:after {
\n    color: #d02a27;
\n}
\n
\n.seating-selector-option-radio:checked + .button {
\n    background: #d02a27;
\n    color: #fff;
\n}
\n
\n.seating-selector-option-hover {
\n    background-color: #d02a27;
\n    color: #fff;
\n}
\n
\n\/\* Forms \*\/
\n.input_group-icon {
\n    background: #198ebe;
\n    color: #fff;
\n}
\n
\n.select:after {
\n    border-top-color: #198ebe;
\n}
\n
\n.form-select:before {
\n    background-image: -webkit-linear-gradient(left, transparent 0, transparent calc(100% - 2.75em), #198ebe calc(100% - 2.75em), #198ebe 100%);
\n    background-image: linear-gradient(to right, transparent 0, transparent calc(100% - 2.75em), #198ebe calc(100% - 2.75em), #198ebe 100%);
\n}
\n
\n.button,
\n.formrt button,
\n.formrt [type=\"submit\"],
\n.formrt [type=\"reset\"] {
\n    background-color: #d02a27;
\n}
\n
\n.button\-\-continue,
\n.btn-primary {
\n    background-color: #43b649;
\n    border-color: transparent;
\n    color: #fff;
\n}
\n
\n.button\-\-continue.inverse,
\n.banner-search .button\-\-continue {
\n    background-color: #fff;
\n    border: 1px solid #43b649;
\n    color: #43b649;
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
\n    background-color: #d02a27;
\n}
\n
\n.button\-\-book.inverse {
\n    background: #FFF;
\n    border-color: #d02a27;
\n    color: #d02a27;
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
\n    background: #43b649;
\n    color: #fff;
\n}
\n
\n.header-action:nth-child(odd) .button {
\n    background: #fff;
\n    border-color: #d02a27;
\n    color: #d02a27;
\n}
\n
\n.header-action:nth-child(even) .button {
\n    background: #43b649;
\n    color: #fff;
\n}
\n
\n.formErrorContent,
\n.formErrorArrow div {
\n    background: #d02a27;
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
\n.popup_box.alert-add     { border-color: #43b649; }
\n.popup_box.alert-remove  { border-color: #b4b4b4; }
\n
\n.popup_box .alert-icon [fill]   {   fill: #d02a27; }
\n.popup_box .alert-icon [stroke] { stroke: #d02a27; }
\n
\n
\n\/\* Header \*\/
\n.header,
\n.mobile-breadcrumbs,
\n.dropdown-menu-header {
\n    background-color: #d02a27;
\n    color: #fff;
\n}
\n
\n.header-menu .level_2 a:hover,
\n.header-menu .level_2:hover > a {
\n    color: #198ebe;
\n}
\n
\n.header-logo + .header-item .header-menu-expand {
\n    border-left: none;
\n}
\n
\n.header-item > a:not(.button) {
\n    color: #fff;
\n}
\n
\n.header-menu-section > a {
\n    border-color: #d4403d;
\n}
\n
\n.header-menu .level_1 > a,
\n.mobile-menu .level_1 > a,
\n.mobile-menu .level_1 > button,
\n.mobile-menu-level3-section .mobile-menu-list > a {
\n    color: #43b649;
\n}
\n
\n.header-menu .level_2 a:before {
\n    border-left-color: #d4403d;
\n}
\n
\n.header-menu .level_2 a:hover:before,
\n.header-menu .level_2:hover > a:hover {
\n    border-left-color: #198ebe;
\n}
\n
\n.header-menu .level_3 {
\n    border-bottom-color: #43b649;
\n}
\n
\n.mobile-menu-top strong,
\n.mobile-menu-top-avatar,
\n.mobile-menu-button-group-icon,
\n.header-cart-breakdown,
\n.final_price_value {
\n    color: #198ebe;
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
\n        color: #198ebe;
\n    }
\n}
\n
\n\/\* Sidebar \*\/
\n.sidebar-section > h2 {
\n    background: #198ebe;
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
\n.page-content h1 { color: #212121; }
\n.page-content h2 { color: #212121; }
\n.page-content h3 { color: #212121; }
\n.page-content h4 { color: #212121; }
\n.page-content h5 { color: #212121; }
\n.page-content h6 { color: #212121; }
\n
\n.page-content li:before {
\n    color: #43b649;
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
\n    border-color: #bfbfbf;
\n}
\n
\n\/\* Banner search \*\/
\n.banner-search-title {
\n    background: #d02a27;
\n    color: #fff;
\n}
\n
\n.banner-search .fa {
\n    color: #43b649;
\n}
\n
\n.banner-search-title .fa {
\n    color: #d95451;
\n}
\n
\n.banner-search form {
\n    background: #43b649;
\n}
\n
\n.banner-overlay-content {
\n    color: #fff;
\n    font-size: 24px;
\n}
\n
\n.banner-overlay-content h1 {
\n    font-weight: 900;
\n}
\n
\n.banner-overlay-content h2 {
\n    font-size: 1.25em;
\n    font-weight: bold;
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
\n        background: rgba(208, 42, 39, .5);
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .banner-overlay .row { background-repeat: no-repeat; }
\n    .swiper-slide .banner-image { background-position: center; }
\n
\n    .banner-overlay-content {
\n        width: 33.333%;
\n    }
\n
\n    .swiper-slide .banner-overlay {
\n        background-position: top center;
\n    }
\n
\n    .banner-slide\-\-left .banner-overlay {
\n        background-image: url(\'\/shared_media\/stac\/media\/photos\/content\/banner_overlay_left.png\');
\n    }
\n
\n    .banner-slide\-\-right .banner-overlay {
\n        background-image: url(\'\/shared_media\/stac\/media\/photos\/content\/banner_overlay_right.png\');
\n    }
\n
\n    .banner-slide\-\-center .banner-overlay {
\n        background: rgba(208, 42, 39, .333);
\n    }
\n}
\n
\n.search-drilldown h3 {
\n    color: #43b649;
\n}
\n
\n.search-drilldown-column p {
\n    color: #198ebe;
\n}
\n
\n.search-drilldown-column a.active {
\n    background-color: #d02a27;
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
\n        border-top-color: #43b649;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .header-action:only-child {
\n        border-right: 1px solid #d02a27;
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
\n    background: #d02a27;
\n    background: -webkit-linear-gradient(#d02a27, #e14c48);
\n    background: linear-gradient(#d02a27, #e14c48);
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
\n    color: #d02a27;
\n}
\n
\n.eventsCalendar-subtitle,
\n.eventsCalendar-list > li > time {
\n    color: #d02a27;
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
\n  color: #198ebe;
\n}
\n
\n.news-slider-title {
\n    color: #198ebe;
\n    background-color: #e2e2e2;
\n}
\n
\n.swiper-pagination-bullet {
\n    background-color: #d02a27;
\n    border-color: #A6AEAD;
\n    box-shadow: inset 0 1px 1px #aaa;
\n}
\n
\n.swiper-pagination-bullet-active {
\n    background-color: #FFF;
\n}
\n
\n.news-result-date {
\n    background-color: #43b649;
\n    color: #FFF;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .news-result + .news-result:before {
\n        background: linear-gradient(to right, transparent 0, #d02a27 10%, #d02a27 90%, transparent 100%);
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .news-result + .news-result {
\n        border-color: #d02a27;
\n    }
\n}
\n
\n.news-story-navigation a {
\n    color: #198ebe;
\n}
\n
\n.news-story-social {
\n    border-color: #d02a27;
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
\n    color: #d12b28;
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
\n        background: #d02a27;
\n        background: linear-gradient(to right, #E6F3C8 0%, #d02a27 20%, #d02a27 80%, #E6F3C8 100%);
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
\n    background: #43b649;
\n    color: #FFF;
\n}
\n
\n.bar-icon svg {
\n  fill: #fff;
\n}
\n
\n.bar-text {
\n    color: #43b649;
\n}
\n
\n.panel-item.has_form {
\n    background-color: #d12b28;
\n    color: #fff;
\n}
\n
\n.panel-item.has_form .button {
\n    background-color: #fff;
\n    border-color: #d12b28;
\n    color: #d12b28;
\n}
\n
\n.panel-item-image:after {
\n    background-image: url(\'\/shared_media\/stac\/media\/photos\/content\/panel_overlay.png\');
\n}
\n
\n.panel-item.has_image .panel-item-text {
\n    color: #fff;
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
\n    background: #43b649;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-grid .course-widget-price {
\n    background-color: #43b649;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-list .course-widget-price-original,
\n.course-list\-\-list .course-widget-price-current {
\n    color: #d02a27;
\n}
\n
\n.course-list-grid .course-widget-time_and_date {
\n    border-color: #b7b7b7;
\n}
\n
\n.course-list\-\-grid .course-widget-time_and_date\-\-with_options,
\n.course-widget-time_and_date\-\-with_options select {
\n    background: #198ebe;
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
\n    color: #d02a27;
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
\n    color: #198ebe;
\n}
\n
\n.availability-timeslot.booked {
\n    border-color: #43b649;
\n}
\n
\n.availability-timeslot.booked .highlight {
\n    color: #43b649;
\n}
\n
\n.timeline-swiper .swiper-slide.selected {
\n    background: #198ebe;
\n    color: #fff;
\n}
\n
\n.timeline-swiperhighlight {
\n    color: #43b649;
\n}
\n
\n.timeline-swiper-prev,
\n.timeline-swiper-next {
\n    color: #198ebe;
\n}
\n
\n\/\* Footer \*\/
\n.footer-stats-list {
\n    color: #fff;
\n}
\n
\n.footer-slogan {
\n    color: #fff;
\n}
\n
\n.footer-stats {
\n    background: #d02a27 url(\'\/shared_media\/stac\/media\/photos\/content\/footer_background.png\') top center;
\n    min-height: 0;
\n}
\n
\n.footer-stat h2:after {
\n    border-color: #fff;
\n}
\n
\n.footer-social,
\n.footer-columns,
\n.footer-copyright {
\n    background-color: #fff;
\n}
\n
\n.footer-social {
\n    border-top: 1px solid #535353;
\n}
\n
\n.footer-social h2 {
\n    color: #43b649;
\n}
\n
\n.footer-column-title {
\n    color: #43b649;
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
\n    background-color: #d02a27;
\n}
\n
\n\/\* Dropdown filters \*\/
\n.search-filter-total {
\n    color: #43b649;
\n}
\n
\n.search-filters :checked ~ .form-checkbox-helper,
\n.search-filters :checked ~ .form-radio-helper,
\n.search-filters :checked ~ .form-checkbox-label,
\n.search-filters :checked ~ .form-radio-label {
\n    border-color: #43b649;
\n    color: #43b649;
\n}
\n
\n.search-filters :checked + .form-radio-helper:after {
\n    background-color: #43b649;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-filter-dropdown.filter-active > button,
\n    .search-filters-clear {
\n        color: #43b649;
\n    }
\n
\n    .checkout-heading {
\n        background-color: #43b649;
\n        color: #fff;
\n    }
\n}
\n
\n\/\* Misc \*\/
\n.contact\-\-left .ui-tabs-nav .ui-tabs-active a {
\n    background: #43b649;
\n    border-color:#43b649;
\n    color: #fff;
\n}
\n
\n.checkout-right-sect .btn-close:hover {
\n    color: #d02a27;
\n    border-color: #d02a27;
\n}
\n
\n.checkout-right-sect .sub-total {
\n    color: #198ebe;
\n}
\n
\n.checkout-progress li a:after {
\n    background-color: #fff;
\n    border-color: #43b649;
\n}
\n
\n.checkout-progress li.curr a:after {
\n    background: #43b649;
\n    background: radial-gradient(#adeeb1, #43b649);
\n}
\n
\n.checkout-progress li + li:before {
\n    border-color: #43b649;
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
\n    background: #d02a27;
\n    color: #fff;
\n}
\n
\n.prepay-box h6 {
\n    color: #43b649;
\n}
\n
\n.custom-calendar .booking-date-button {
\n    background-color: #43b649;
\n    color: #fff;
\n}
\n
\n.custom-calendar .booking-date-button:hover {
\n    background-color: #198ebe;
\n}
\n
\n.custom-calendar button.booking-date-button.active {
\n    background-color: #fff;
\n    color: #d02a27;
\n}
\n
\n.course-activity-alert,
\n.details-wrap .left-place {
\n    color: #F75A5F;
\n}
\n
\n.number-of-people-viewing {
\n    color: #d02a27;
\n}
\n
\n.search-calendar-course-image .fa {
\n    background-color: #43b649;
\n    color: #fff;
\n}
\n
\n.custom-calendar tbody td.active,
\n.custom-calendar tbody td.active:hover {
\n    background-color: #fff;
\n    color: #43b649;
\n}
\n
\n.custom-calendar tbody tr:first-child td {
\n    color: #222;
\n}
\n
\n.package-offers-wrap h2 {
\n    color: #d02a27;
\n    border-color: #c5cecd;
\n}
\n
\n.package-offers-wrap h3 {
\n    color: #d02a27;
\n}
\n
\n.package-offers-wrap .summary-wrap .more,
\n.classes-details-wrap .details-wrap li:first-child {
\n    color: #d02a27;
\n}
\n
\n.classes-details-wrap .details-wrap li:first-child {
\n  background-color: #d02a27;
\n}
\n
\n.details-wrap .remove-booking,
\n.details-wrap .wishlist.remove{
\n    color: #d02a27;
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
\n    color: #d02a27;
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
\n    color: #43b649;
\n}
\n
\n.details-wrap:hover li:first-child {
\n    background-color: #43b649;
\n}
\n
\n.details-wrap:hover .sidelines::before,
\n.details-wrap:hover .sidelines::after,
\n.details-wrap:hover .price-wrap {
\n    border-color:#43b649;
\n}
\n
\n
\n\/\* course results booked \*\/
\n.details-wrap.booked {
\n    border-color:#d02a27;
\n    background-color: #f3f3f3;
\n}
\n
\n.details-wrap.booked .time,
\n.details-wrap.booked .price,
\n.details-wrap.booked .fa-book {
\n    color: #d02a27;
\n}
\n.details-wrap.booked li:first-child {
\n    background-color: #d02a27;
\n}
\n
\n.details-wrap.booked .sidelines::before,
\n.details-wrap.booked .sidelines::after,
\n.details-wrap.booked .price-wrap {
\n    border-color:#d02a27;
\n}
\n
\n.classes-details-wrap .alert-wrap {
\n    background-color: #d02a27;
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
\n    color: #43b649;
\n}
\n
\n.custom-calendar .booking-date-button.already_booked {
\n    background-color: #d02a27;
\n    color: #fff;
\n}
\n
\n.search_history .remove_search_history {
\n    color: #e9a075;
\n    border-color: #e9a075;
\n}
\n
\n.swiper-button-prev {
\n    background-image: url(\"data:image\/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M0%2C22L22%2C0l2.1%2C2.1L4.2%2C22l19.9%2C19.9L22%2C44L0%2C22L0%2C22L0%2C22z\'%20fill%3D\'%23d02a27\'%2F%3E%3C%2Fsvg%3E\");
\n}
\n
\n.swiper-button-next {
\n    background-image: url(\"data:image\/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M27%2C22L27%2C22L5%2C44l-2.1-2.1L22.8%2C22L2.9%2C2.1L5%2C0L27%2C22L27%2C22z\'%20fill%3D\'%23d02a27\'%2F%3E%3C%2Fsvg%3E\");
\n}
\n
\nbody > div > img {
\n  display: block;
\n}
\n
\n
\n
"
WHERE
  `stub` = '33'
;;


/*
ts:2018-03-13 16:20:00
*/


/* Add the 34 (STAC white) theme */
INSERT INTO
  `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '34', '34', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_themes`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_themes` WHERE `stub` = '34')
    LIMIT 1
;;


/* Add the '34' theme styles */
DELIMITER  ;;
UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = "@import url(\'https:\/\/fonts.googleapis.com\/css?family=Roboto:300,300i,400,400i,700,700i,900\');
\n
\n:root {
\n    \-\-primary: #d02a27;   \-\-primary-hover: #dd4d4b;   \-\-primary-active: #b42522;
\n    \-\-secondary: #f5f5f5; \-\-secondary-hover: #d3d3d3; \-\-secondary-active: #e6e6e6;
\n    \-\-success: #43b649;   \-\-success-hover: #d3d3d3;   \-\-success-active: #e6e6e6;
\n    \-\-info: #17a2b8;      \-\-info-hover: #2f96b4;      \-\-info-active: #31b0d5;
\n    \-\-warning: #ffc107;   \-\-warning-hover: #f89406;   \-\-warning-active: #ec971f;
\n    \-\-danger: #dc3545;    \-\-danger-hover: #bd362f;    \-\-danger-active: #c9302c;
\n}
\n
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
\n    background: #d02a27;
\n    color: #FFF;
\n}
\n
\n.badge {
\n    background: #43b649;
\n    color: #fff;
\n}
\n
\n.db-sidebar .sidebar-menu li a:hover,
\n.db-sidebar .sidebar-menu li a.active {
\n    background-color: #d02a27;
\n    color: #fff;
\n}
\n
\n.popup-header {
\n    background: #d02a27;
\n    color: #fff;
\n}
\n
\n.button.course-banner-button.cl_bg {
\n    background-color: #d02a27;
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
\n    color: #6dc4eb;
\n}
\n
\n.autotimetable .new_date {
\n    border-color: #69C3ED;
\n}
\n
\n.autotimetable .new_date td:nth-child(1) {
\n    background-color: #6dc4eb;
\n    color: white;
\n}
\n
\n:checked + .seating-selector-checkbox-helper:after {
\n    color: #d02a27;
\n}
\n
\n.seating-selector-option-radio:checked + .button {
\n    background: #d02a27;
\n    color: #fff;
\n}
\n
\n.seating-selector-option-hover {
\n    background-color: #d02a27;
\n    color: #fff;
\n}
\n
\n\/\* Forms \*\/
\n.input_group-icon {
\n    background: #198ebe;
\n    color: #fff;
\n}
\n
\n.select:after {
\n    border-top-color: #198ebe;
\n}
\n
\n.form-select:before {
\n    background-image: -webkit-linear-gradient(left, transparent 0, transparent calc(100% - 2.75em), #198ebe calc(100% - 2.75em), #198ebe 100%);
\n    background-image: linear-gradient(to right, transparent 0, transparent calc(100% - 2.75em), #198ebe calc(100% - 2.75em), #198ebe 100%);
\n}
\n
\n.button,
\n.formrt button,
\n.formrt [type=\"submit\"],
\n.formrt [type=\"reset\"] {
\n    background-color: #d02a27;
\n}
\n
\n.button\-\-continue,
\n.btn-primary {
\n    background-color: #43b649;
\n    border-color: transparent;
\n    color: #fff;
\n}
\n
\n.button\-\-continue.inverse,
\n.banner-search .button\-\-continue {
\n    background-color: #fff;
\n    border: 1px solid #43b649;
\n    color: #43b649;
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
\n    background-color: #d02a27;
\n}
\n
\n.button\-\-book.inverse {
\n    background: #FFF;
\n    border-color: #d02a27;
\n    color: #d02a27;
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
\n    background: #43b649;
\n    color: #fff;
\n}
\n
\n.header-action:nth-child(odd) .button {
\n    background: #d02a27;
\n    border-color: #d02a27;
\n    color: #fff;
\n}
\n
\n.header-action:nth-child(even) .button {
\n    background: #43b649;
\n    color: #fff;
\n}
\n
\n.formErrorContent,
\n.formErrorArrow div {
\n    background: #d02a27;
\n}
\n
\n.login-form-container.login-form-container .modal-header {
\n    background: #d02a27;
\n}
\n
\n.login-form-container a {
\n    color: #d02a27;
\n}
\n
\n.login-form-container.login-form-container .nav-tabs > .active > a:after {
\n    background-color: #43b649;
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
\n.popup_box.alert-add     { border-color: #43b649; }
\n.popup_box.alert-remove  { border-color: #b4b4b4; }
\n
\n.popup_box .alert-icon [fill]   {   fill: #d02a27; }
\n.popup_box .alert-icon [stroke] { stroke: #d02a27; }
\n
\n
\n\/\* Header \*\/
\n.header,
\n.mobile-breadcrumbs {
\n    background-color: #f4f4f4;
\n    color: #000;
\n}
\n
\n.dropdown-menu-header {
\n    background-color: #d02a27;
\n    color: #fff;
\n}
\n
\n.mobile-menu-toggle {
\n    color: #d02a27;
\n}
\n
\n.header-cart-button [fill] { fill: #d02a27; }
\n.header-cart-button [stroke] { stroke: #d02a27; }
\n
\n.header-menu .level_2 a:hover,
\n.header-menu .level_2:hover > a {
\n    color: #198ebe;
\n}
\n
\n.header-item > a:not(.button) {
\n    color: #000;
\n}
\n
\n.header-menu-section > a {
\n    border-color: #f0dfdf;
\n}
\n
\n.header-menu-section > a:after {
\n    border-top-color: #d02a27;
\n}
\n
\n.header-menu .level_1 > a,
\n.mobile-menu .level_1 > a,
\n.mobile-menu .level_1 > button,
\n.mobile-menu-level3-section .mobile-menu-list > a {
\n    color: #43b649;
\n}
\n
\n.header-menu .level_2 a:before {
\n    border-left-color: #d4403d;
\n}
\n
\n.header-menu .level_2 a:hover:before,
\n.header-menu .level_2:hover > a:hover {
\n    border-left-color: #198ebe;
\n}
\n
\n.header-menu .level_3 {
\n    border-bottom-color: #43b649;
\n}
\n
\n.mobile-menu-top strong,
\n.mobile-menu-top-avatar,
\n.mobile-menu-button-group-icon,
\n.header-cart-breakdown,
\n.final_price_value {
\n    color: #198ebe;
\n}
\n
\n.header-cart-amount,
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
\n        color: #198ebe;
\n    }
\n}
\n
\n\/\* Sidebar \*\/
\n.sidebar-section > h2 {
\n    background: #198ebe;
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
\n.page-content h1 { color: #198ebe; font-weight: 400; }
\n.page-content h2 { color: #212121; }
\n.page-content h3 { color: #212121; }
\n.page-content h4 { color: #212121; }
\n.page-content h5 { color: #212121; }
\n.page-content h6 { color: #212121; }
\n
\n.page-content li:before {
\n    color: #43b649;
\n}
\n
\n.page-content ul > li:before,
\n.page-content ul > li:before {
\n    font-family: ElegantIcons;
\n    content: '\\4e'
\n}
\n
\n.page-content ul[type=\"square\"] > li:before,
\n.page-content ul[style\*=\"square\"] > li:before {
\n    content: '\\4d'
\n}
\n
\n.page-content ul[type=\"circle\"] > li,
\n.page-content ul[style\*=\"circle\"] > li,
\n.page-content ul[type=\"disc\"] > li,
\n.page-content ul[style\*=\"disc\"] > li {
\n    list-style: none;
\n    position: relative;
\n}
\n
\n.page-content ul[type=\"circle\"] > li:before,
\n.page-content ul[style\*=\"circle\"] > li:before,
\n.page-content ul[type=\"disc\"] > li:before,
\n.page-content ul[style\*=\"disc\"] > li:before {
\n    content: '';
\n    background: #d02a27;
\n    color: #fff;
\n    border-radius: 50%;
\n    width: 1em;
\n    height: 1em;
\n    position: absolute;
\n    top: .4em;
\n    left: 0;
\n}
\n
\n.page-content ul[type=\"circle\"] > li:after,
\n.page-content ul[style\*=\"circle\"] > li:after,
\n.page-content ul[type=\"disc\"] > li:after,
\n.page-content ul[style\*=\"disc\"] > li:after {
\n    color: #fff;
\n    font-family: FontAwesome;
\n    font-size: .67em;
\n    position: absolute;
\n    top: .55em;
\n}
\n
\n.page-content ul[type=\"circle\"] > li:after,
\n.page-content ul[style\*=\"circle\"] > li:after {
\n    content: '\\f00c';
\n    left: .3em;
\n}
\n
\n.page-content ul[type=\"disc\"] > li:after,
\n.page-content ul[style\*=\"disc\"] > li:after {
\n    content: '\\f068';
\n    left: .4em;
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
\n    border-color: #bfbfbf;
\n}
\n
\n.simplebox {
\n    padding-top: 1rem;
\n    padding-bottom: 1rem;
\n}
\n
\n.simplebox-testimonials .simplebox-columns {
\n    align-items: unset;
\n}
\n
\n.simplebox-testimonials .simplebox-column {
\n    box-shadow: 5px 5px 10px #ccc;
\n    padding-left: 1em;
\n    padding-right: 1em;
\n    margin-bottom: 1em;
\n}
\n
\n@media screen and (max-width: 1080px) {
\n    .simplebox-testimonials .simplebox-columns {
\n        padding-left: 1em;
\n        padding-right: 1em;
\n    }
\n}
\n
\n
\n\/\* Banner search \*\/
\n.banner-search-title {
\n    background: #d02a27;
\n    color: #fff;
\n}
\n
\n.banner-search .fa {
\n    color: #43b649;
\n}
\n
\n.banner-search-title .fa {
\n    color: #d95451;
\n}
\n
\n.banner-search form {
\n    background: #43b649;
\n}
\n
\n.banner-overlay-content {
\n    color: #fff;
\n    font-size: 24px;
\n}
\n
\n.banner-slide\-\-left .banner-overlay-content {
\n    left: 0;
\n}
\n
\n.banner-overlay-content h1 {
\n    font-weight: 900;
\n}
\n
\n.banner-overlay-content h2 {
\n    font-size: 1.25em;
\n    font-weight: bold;
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
\n        background: rgba(208, 42, 39, .333);
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .banner-overlay .row { background-repeat: no-repeat; }
\n    .swiper-slide .banner-image { background-position: center; }
\n
\n    .banner-overlay-content {
\n        width: 33.333%;
\n    }
\n
\n    .swiper-slide .banner-overlay {
\n        background-position: top center;
\n    }
\n
\n    .banner-slide\-\-left .banner-overlay {
\n        background-image: url(\'\/shared_media\/stac\/media\/photos\/content\/banner_overlay_left.png\');
\n    }
\n
\n    .banner-slide\-\-right .banner-overlay {
\n        background-image: url(\'\/shared_media\/stac\/media\/photos\/content\/banner_overlay_right.png\');
\n    }
\n
\n    .banner-slide\-\-center .banner-overlay {
\n        background: rgba(208, 42, 39, .333);
\n    }
\n}
\n
\n.search-drilldown h3 {
\n    color: #43b649;
\n}
\n
\n.search-drilldown-column p {
\n    color: #198ebe;
\n}
\n
\n.search-drilldown-column a.active {
\n    background-color: #d02a27;
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
\n        border-top-color: #43b649;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .header-action:only-child {
\n        border-right: 1px solid #f0dfdf;
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
\n    background: #d02a27;
\n    background: -webkit-linear-gradient(#d02a27, #e14c48);
\n    background: linear-gradient(#d02a27, #e14c48);
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
\n    color: #d02a27;
\n}
\n
\n.eventsCalendar-subtitle,
\n.eventsCalendar-list > li > time {
\n    color: #d02a27;
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
\n  color: #198ebe;
\n}
\n
\n.news-slider-title {
\n    color: #198ebe;
\n    background-color: #e2e2e2;
\n}
\n
\n.swiper-pagination-bullet {
\n    background-color: #d02a27;
\n    border-color: #A6AEAD;
\n    box-shadow: inset 0 1px 1px #aaa;
\n}
\n
\n.swiper-pagination-bullet-active {
\n    background-color: #FFF;
\n}
\n
\n.news-result-date {
\n    background-color: #43b649;
\n    color: #FFF;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .news-result + .news-result:before {
\n        background: linear-gradient(to right, transparent 0, #d02a27 10%, #d02a27 90%, transparent 100%);
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .news-result + .news-result {
\n        border-color: #d02a27;
\n    }
\n}
\n
\n.news-story-navigation a {
\n    color: #198ebe;
\n}
\n
\n.news-story-social {
\n    border-color: #d02a27;
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
\n    color: #d12b28;
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
\n        background: #d02a27;
\n        background: linear-gradient(to right, #E6F3C8 0%, #d02a27 20%, #d02a27 80%, #E6F3C8 100%);
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
\n    background: #43b649;
\n    color: #FFF;
\n}
\n
\n.bar-icon svg {
\n  fill: #fff;
\n}
\n
\n.bar-text {
\n    color: #43b649;
\n}
\n
\n.panel-item.has_form {
\n    background-color: #d12b28;
\n    color: #fff;
\n}
\n
\n.panel-item.has_form .button {
\n    background-color: #fff;
\n    border-color: #d12b28;
\n    color: #d12b28;
\n}
\n
\n.panel-item-image:after {
\n    background-image: url(\'\/shared_media\/stac\/media\/photos\/content\/panel_overlay.png\');
\n}
\n
\n.panel-item.has_image .panel-item-text {
\n    color: #fff;
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
\n    background: #43b649;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-grid .course-widget-price {
\n    background-color: #43b649;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-list .course-widget-price-original,
\n.course-list\-\-list .course-widget-price-current {
\n    color: #d02a27;
\n}
\n
\n.course-list-grid .course-widget-time_and_date {
\n    border-color: #b7b7b7;
\n}
\n
\n.course-list\-\-grid .course-widget-time_and_date\-\-with_options,
\n.course-widget-time_and_date\-\-with_options select {
\n    background: #198ebe;
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
\n    color: #d02a27;
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
\n    color: #198ebe;
\n}
\n
\n.availability-timeslot.booked {
\n    border-color: #43b649;
\n}
\n
\n.availability-timeslot.booked .highlight {
\n    color: #43b649;
\n}
\n
\n.timeline-swiper .swiper-slide.selected {
\n    background: #198ebe;
\n    color: #fff;
\n}
\n
\n.timeline-swiper-highlight {
\n    color: #43b649;
\n}
\n
\n.timeline-swiper-prev,
\n.timeline-swiper-next {
\n    color: #198ebe;
\n}
\n
\n\/\* Footer \*\/
\n.footer-stats-list {
\n    color: #d02a27;
\n}
\n
\n.footer-slogan {
\n    color: #d02a27;
\n}
\n
\n.footer-stats {
\n    background: #f2f2f2 url(\'\/shared_media\/stac\/media\/photos\/content\/footer_background_2.png\') top center;
\n    min-height: 0;
\n}
\n
\n.footer-stat h2:after {
\n    border-color: #d02a27;
\n}
\n
\n.footer-social,
\n.footer-columns,
\n.footer-copyright {
\n    background-color: #fff;
\n}
\n
\n.footer-social h2 {
\n    color: #43b649;
\n}
\n
\n.footer-column-title {
\n    color: #43b649;
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
\n    background-color: #d02a27;
\n}
\n
\n\/\* Dropdown filters \*\/
\n.search-filter-total {
\n    color: #43b649;
\n}
\n
\n.search-filters :checked ~ .form-checkbox-helper,
\n.search-filters :checked ~ .form-radio-helper,
\n.search-filters :checked ~ .form-checkbox-label,
\n.search-filters :checked ~ .form-radio-label {
\n    border-color: #43b649;
\n    color: #43b649;
\n}
\n
\n.search-filters :checked + .form-radio-helper:after {
\n    background-color: #43b649;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-filter-dropdown.filter-active > button,
\n    .search-filters-clear {
\n        color: #43b649;
\n    }
\n
\n    .checkout-heading {
\n        background-color: #43b649;
\n        color: #fff;
\n    }
\n}
\n
\n\/\* Misc \*\/
\n.contact\-\-left .ui-tabs-nav .ui-tabs-active a {
\n    background: #43b649;
\n    border-color:#43b649;
\n    color: #fff;
\n}
\n
\n.checkout-right-sect .btn-close:hover {
\n    color: #d02a27;
\n    border-color: #d02a27;
\n}
\n
\n.checkout-progress li a:after {
\n    background-color: #fff;
\n    border-color: #43b649;
\n}
\n
\n.checkout-progress li.curr a:after {
\n    background: #43b649;
\n    background: radial-gradient(#adeeb1, #43b649);
\n}
\n
\n.checkout-progress li + li:before {
\n    border-color: #43b649;
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
\n    background: #d02a27;
\n    color: #fff;
\n}
\n
\n.prepay-box h6 {
\n    color: #43b649;
\n}
\n
\n.custom-calendar .booking-date-button {
\n    background-color: #43b649;
\n    color: #fff;
\n}
\n
\n.custom-calendar .booking-date-button:hover {
\n    background-color: #198ebe;
\n}
\n
\n.custom-calendar button.booking-date-button.active {
\n    background-color: #fff;
\n    color: #d02a27;
\n}
\n
\n.course-activity-alert,
\n.details-wrap .left-place {
\n    color: #F75A5F;
\n}
\n
\n.number-of-people-viewing {
\n    color: #d02a27;
\n}
\n
\n.search-calendar-course-image .fa {
\n    background-color: #43b649;
\n    color: #fff;
\n}
\n
\n.custom-calendar tbody td.active,
\n.custom-calendar tbody td.active:hover {
\n    background-color: #fff;
\n    color: #43b649;
\n}
\n
\n.custom-calendar tbody tr:first-child td {
\n    color: #222;
\n}
\n
\n.package-offers-wrap h2 {
\n    color: #d02a27;
\n    border-color: #c5cecd;
\n}
\n
\n.package-offers-wrap h3 {
\n    color: #d02a27;
\n}
\n
\n.package-offers-wrap .summary-wrap .more,
\n.classes-details-wrap .details-wrap li:first-child {
\n    color: #d02a27;
\n}
\n
\n.classes-details-wrap .details-wrap li:first-child {
\n  background-color: #d02a27;
\n}
\n
\n.details-wrap .remove-booking,
\n.details-wrap .wishlist.remove{
\n    color: #d02a27;
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
\n    color: #d02a27;
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
\n    color: #43b649;
\n}
\n
\n.details-wrap:hover li:first-child {
\n    background-color: #43b649;
\n}
\n
\n.details-wrap:hover .sidelines::before,
\n.details-wrap:hover .sidelines::after,
\n.details-wrap:hover .price-wrap {
\n    border-color:#43b649;
\n}
\n
\n
\n\/\* course results booked \*\/
\n.details-wrap.booked {
\n    border-color:#d02a27;
\n    background-color: #f3f3f3;
\n}
\n
\n.details-wrap.booked .time,
\n.details-wrap.booked .price,
\n.details-wrap.booked .fa-book {
\n    color: #d02a27;
\n}
\n.details-wrap.booked li:first-child {
\n    background-color: #d02a27;
\n}
\n
\n.details-wrap.booked .sidelines::before,
\n.details-wrap.booked .sidelines::after,
\n.details-wrap.booked .price-wrap {
\n    border-color:#d02a27;
\n}
\n
\n.classes-details-wrap .alert-wrap {
\n    background-color: #d02a27;
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
\n    color: #43b649;
\n}
\n
\n.custom-calendar .booking-date-button.already_booked {
\n    background-color: #d02a27;
\n    color: #fff;
\n}
\n
\n.search_history .remove_search_history {
\n    color: #e9a075;
\n    border-color: #e9a075;
\n}
\n
\n.swiper-button-prev {
\n    background-image: url(\"data:image\/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M0%2C22L22%2C0l2.1%2C2.1L4.2%2C22l19.9%2C19.9L22%2C44L0%2C22L0%2C22L0%2C22z\'%20fill%3D\'%23d02a27\'%2F%3E%3C%2Fsvg%3E\");
\n}
\n
\n.swiper-button-next {
\n    background-image: url(\"data:image\/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M27%2C22L27%2C22L5%2C44l-2.1-2.1L22.8%2C22L2.9%2C2.1L5%2C0L27%2C22L27%2C22z\'%20fill%3D\'%23d02a27\'%2F%3E%3C%2Fsvg%3E\");
\n}
\n
\nbody > div > img {
\n  display: block;
\n}
\n
\n
\n
"
WHERE
  `stub` = '34'
;;

INSERT IGNORE INTO `plugin_pages_layouts` (`layout`, `template_id`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES (
  'content2',
  (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '04' AND `deleted` = 0),
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
);;


-- Request a Callback form
INSERT INTO
  `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `deleted`, `publish`, `date_modified`, `email_all_fields`, `captcha_version`, `use_stripe`, `form_id`)
SELECT
  'Request a Callback',
  'frontend/formprocessor/',
  'POST',
 '<input type=\"hidden\" name=\"subject\"         value=\"Callback Request\" />
\n<input type=\"hidden\" name=\"redirect\"        value=\"thank-you.html\"   />
\n<input type=\"hidden\" name=\"event\"           value=\"contact-form\" />
\n<input type=\"hidden\" name=\"trigger\"         value=\"custom_form\" id=\"trigger\" />
\n<input type=\"hidden\" name=\"form_type\"       value=\"Contact Form\" id=\"form_type\" />
\n<input type=\"hidden\" name=\"form_identifier\" value=\"contact_\" />
\n<input type=\"hidden\" name=\"email_template\"  value=\"contactformmail\" id=\"email_template\" />
\n<li><label for=\"contact_form_name\">Name:</label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"contact_form_name\"></li>
\n<li><label for=\"contact_form_tel\">Phone:</label><input type=\"text\" name=\"contact_form_tel\" class=\"validate[required,custom[phone]]\" id=\"contact_form_tel\"></li>
\n<li><label for=\"contact_form_submit\"></label><button id=\"formbuilder-preview-contact_form_submit\" class=\"button\" type=\"submit\">Request a Callback</button></li>',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  '0',
  '2',
  '0',
  'Request a Callback'
FROM
  (SELECT 'temp') `temp`
WHERE NOT EXISTS
  (SELECT 1 FROM `plugin_formbuilder_forms` WHERE `form_name` = 'Request a Callback' AND `deleted` != 1)
;;

-- Quick Quote form
INSERT INTO
  `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `deleted`, `publish`, `date_modified`, `email_all_fields`, `captcha_version`, `use_stripe`, `form_id`)
SELECT
  'Quick Quote',
  'frontend/formprocessor/',
  'POST',
 '<input type=\"hidden\" name=\"subject\"         value=\"Contact form\" />
\n<input type=\"hidden\" name=\"redirect\"        value=\"thank-you.html\" />
\n<input type=\"hidden\" name=\"event\"           value=\"contact-form\" />
\n<input type=\"hidden\" name=\"trigger\"         value=\"custom_form\" id=\"trigger\" />
\n<input type=\"hidden\" name=\"form_type\"       value=\"Contact Form\" id=\"form_type\" />
\n<input type=\"hidden\" name=\"form_identifier\" value=\"contact_\" />
\n<input type=\"hidden\" name=\"email_template\"  value=\"contactformmail\" id=\"email_template\" />
\n<li><label for=\"contact_form_name\"></label><input type=\"text\" name=\"contact_form_name\"  class=\"validate[required]\"               id=\"enquiry_form_name\"    placeholder=\"Enter Name*\"></li>
\n<li><label for=\"contact_form_tel\" ></label><input type=\"text\" name=\"contact_form_tel\"   class=\"validate[required,custom[phone]]\" id=\"enquiry_form_tel\"     placeholder=\"Enter Phone No*\"></li>
\n<li><label for=\"contact_form_tel\" ></label><input type=\"text\" name=\"contact_form_email\" class=\"validate[required,custom[email]]\" id=\"enquiry_form_email\"   placeholder=\"Enter E-mail*\"></li>
\n<li><label for=\"contact_form_email_address\"></label><textarea name=\"contact_form_message\" class=\"validate[required]\"               id=\"enquiry_form_message\" placeholder=\"Message*\"></textarea></li>
\n<li><label></label><button type=\"submit\" name=\"submit1\" value=\"Send Email\" class=\"button\" id=\"enquiry_form_submit\">Send Your Enquiry</button></li>',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  '0',
  '2',
  '0',
  'Quick Quote'
FROM
  (SELECT 'temp') `temp`
WHERE NOT EXISTS
  (SELECT 1 FROM `plugin_formbuilder_forms` WHERE `form_name` = 'Quick Quote' AND `deleted` != 1)
;;


-- Course finder-mode setting
INSERT INTO
  `engine_settings` (`variable`, `name`, `linked_plugin_name`, `type`, `group`, `options`)
VALUES
  ('course_finder_mode', 'Finder Mode', 'courses', 'select', 'Courses', 'Model_Courses,get_finder_modes')
;;

-- Favicon preset (.ico files cannot be re-sized. The preset only serves to categorise them.)
INSERT INTO
  `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES (
  'Favicons',
  'favicons',
  '32',
  '32',
  'fit',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0'
);;

-- Favicon-selection setting
INSERT INTO
  `engine_settings` (`variable`, `name`, `note`, `type`, `group`, `options`)
VALUES
  ('site_favicon', 'Favicon', 'The icon to display in tabs and bookmarks', 'select', 'Website', 'Model_Media,get_favicons_as_options')
;;


-- Setting to control if the user is asked for student and guardian contact details on the course checkout or just student details
INSERT INTO
  `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES (
  'course_checkout_guardian_fields',
  'Guardian information on checkout',
  'courses',
  '1',
  '1',
  '1',
  '1',
  '1',
  'When enabled, the user is asked for both student and guardian details at the checkout. When disabled, the user is only asked for student details',
  'toggle_button',
  'Courses',
  'Model_Settings,on_or_off'
);;
