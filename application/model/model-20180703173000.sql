/*
ts:2018-07-06 17:30:00
*/

/* Add the 39 (uTicket) theme */
DELIMITER  ;;
INSERT INTO
  `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '39', '39', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_themes`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_themes` WHERE `stub` = '39')
    LIMIT 1
;;


/* Add the '39' theme styles */
DELIMITER  ;;
UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = "@import url(\'https:\/\/fonts.googleapis.com\/css?family=Roboto:300,300i,400,400i,700,700i,900\');
\n
\n:root {
\n    \-\-primary: #3dc2a6;   \-\-primary-hover: #5fc7ac;   \-\-primary-active: #37ae95;
\n    \-\-secondary: #f5f5f5; \-\-secondary-hover: #d3d3d3; \-\-secondary-active: #e6e6e6;
\n    \-\-success: #fe0074;   \-\-success-hover: #f763ad;   \-\-success-active: #e60067;
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
\n.layout-home {
\n    background: #e9e9e9;
\n}
\n
\n.table thead {
\n    background: #555;
\n    color: #fff;
\n}
\n
\n.badge {
\n    background: black;
\n    color: #fff;
\n}
\n
\n.db-sidebar {
\n    background-color: black;
\n    color: #fff;
\n}
\n
\n.db-sidebar .sidebar-menu li a {
\n    color: #fff;
\n}
\n
\n.db-sidebar .sidebar-menu li.sidebar-wishlist ~ li {
\n    background: #1a1a1a;
\n    color: #000;
\n}
\n
\n.db-sidebar .sidebar-menu li a:hover,
\n.db-sidebar .sidebar-menu li a.active {
\n    background-color: #1a1a1a;
\n    color: #fff;
\n}
\n
\n.popup-header {
\n    background: black;
\n    color: #fff;
\n}
\n
\n.button.course-banner-button.cl_bg {
\n    background-color: black;
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
\n    color: black;
\n}
\n
\n.autotimetable .new_date {
\n    border-color: black;
\n}
\n
\n.autotimetable .new_date td:nth-child(1) {
\n    background-color: black;
\n    color: #fff;
\n}
\n
\n:checked + .seating-selector-checkbox-helper:after {
\n    color: black;
\n}
\n
\n.seating-selector-option-radio:checked + .button {
\n    background: black;
\n    color: #fff;
\n}
\n
\n.seating-selector-option-hover {
\n    background-color: black;
\n    color: #fff;
\n}
\n
\n\/\* Forms \*\/
\n.input_group-icon {
\n    background: #31cdb5;
\n    color: #fff;
\n}
\n
\n#payment-tabs-credit_card input::-webkit-outer-spin-button,
\n#payment-tabs-credit_card input::-webkit-inner-spin-button {
\n    -webkit-appearance: none;
\n    margin: 0;
\n}
\n
\n#payment-tabs-credit_card input[type=number] {
\n    -moz-appearance:textfield;
\n}
\n
\n.form-input\-\-active .form-input\-\-pseudo-label {
\n    opacity: .7;
\n}
\n
\n.select:after {
\n    border-top-color: black;
\n}
\n
\n.form-select:before {
\n    background-image: -webkit-linear-gradient(left, transparent 0, transparent calc(100% - 2.75em), #31cdb5 calc(100% - 2.75em), #31cdb5 100%);
\n    background-image: linear-gradient(to right, transparent 0, transparent calc(100% - 2.75em), #31cdb5 calc(100% - 2.75em), #31cdb5 100%);
\n    background-image: none;
\n}
\n
\n.form-select:after {
\n    border-color: #787878;
\n}
\n
\n.button,
\n.formrt button,
\n.formrt [type=\"submit\"],
\n.formrt [type=\"reset\"] {
\n    background-color: #fe0074;
\n    color: #fff;
\n}
\n
\n.button\-\-continue {
\n    background-color: #fe0074;
\n    border-color: transparent;
\n    color: #fff;
\n}
\n
\n.button\-\-continue.inverse {
\n    background-color: #fff;
\n    border: 1px solid #fe0074;
\n    color: #fe0074;
\n}
\n
\n.button\-\-cancel {
\n    background: #fff;
\n    border: 1px solid #f00;
\n    color: #f00;
\n}
\n
\n.button\-\-pay {
\n    background-color: #30cdb5;
\n}
\n
\n.button\-\-pay.inverse {
\n    background: #fff;
\n    border: 1px solid #fe0074;
\n    color: #bfb8bf;
\n}
\n
\n.button\-\-book {
\n    background-color: #fe0074;
\n}
\n
\n.button\-\-book.inverse {
\n    background: #fff;
\n    border-color: #fe0074;
\n    color: #fe0074;
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
\n    background: #31cdb5;
\n    color: #fff;
\n}
\n
\n.button\-\-send.inverse {
\n    background: #fff;
\n    border-color: #31cdb5;
\n    color: #31cdb5;
\n}
\n
\n.button\-\-enquire {
\n    background: #31cdb5;
\n    color: #fff;
\n}
\n
\n.button.button\-\-add_person {
\n    background: #e2e2e2;
\n    color: #303030;
\n    border: 1px solid #adadad;
\n    font-weight: 200;
\n}
\n
\n.header-actions {
\n    justify-content: normal;
\n}
\n
\n.header-action:nth-last-child(even) .button {
\n    background: #fe0074;
\n    color: #fff;
\n}
\n
\n.header-action:nth-last-child(odd) .button {
\n    background: none;
\n    border: none;
\n    color: #fff;
\n    font-weight: 400;
\n}
\n
\n.header-action [href=\"\/admin\/events\/edit_event\/new\"]::before {
\n    content: \'\\50\';
\n    font-family: ElegantIcons;
\n    font-size: .95em;
\n    margin-right: .5em;
\n    opacity: .7;
\n}
\n
\n.formErrorContent,
\n.formErrorArrow div {
\n    background: #fe0074;
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
\n.popup_box.alert-add     { border-color: #fe0074; }
\n.popup_box.alert-remove  { border-color: #b4b4b4; }
\n
\n.popup_box .alert-icon [fill]   {   fill: black; }
\n.popup_box .alert-icon [stroke] { stroke: black; }
\n
\n
\n\/\* Header \*\/
\n.header,
\n.mobile-breadcrumbs,
\n.dropdown-menu-header {
\n    background-color: black;
\n    color: #fff;
\n}
\n
\n.header-menu .level_2 a:hover,
\n.header-menu .level_2:hover > a {
\n    color: #fe0074;
\n}
\n
\n.header-item > a:not(.button) {
\n    color: #fff;
\n}
\n
\n.header-menu-section > a {
\n    border-color: #1a1a1a;
\n}
\n
\n.header-menu .level_1 > a,
\n.mobile-menu .level_1 > a,
\n.mobile-menu .level_1 > button,
\n.mobile-menu-level3-section .mobile-menu-list > a {
\n    color: black;
\n}
\n
\n.header-menu .level_2 a:before {
\n    border-left-color: black;
\n}
\n
\n.header-menu .level_2 a:hover:before,
\n.header-menu .level_2:hover > a:hover {
\n    border-left-color: #fe0074;
\n}
\n
\n.header-menu .level_3 {
\n    border-bottom-color: black;
\n}
\n
\n.mobile-menu-top strong,
\n.mobile-menu-top-avatar,
\n.mobile-menu-button-group-icon,
\n.header-cart-breakdown,
\n.final_price_value {
\n    color: #909090;
\n}
\n
\n.header-cart-amount {
\n    color: #fff;
\n}
\n
\n.mobile-menu li.active > a,
\n.checkout-item-title {
\n    color: #fe0074;
\n}

#mobile-menu,
.mobile-menu-level3-section{
    background: #000;
    color: #fff;
}

.mobile-menu-list li:not(.active) > a {
    color: #909090;
}

.mobile-menu .level2 li:not(.active) > a:hover,
.mobile-menu .level3 li:not(.active) > a:hover {
    background: none;
    color: #fff;
}

.mobile-menu .svg-sprite,
.mobile-menu-list li.active > a .svg-sprite,
.mobile-menu-list li > a:hover .svg-sprite,
.svg-color.svg-sprite use,
.svg-color .svg-sprite use,
.svg-color-hover.svg-sprite:hover use,
.svg-color-hover:hover .svg-sprite use {
    fill: #fff;
}

.sidebar-menu-active-sublist li > a:hover {
    background: #8c8c8c;
}

.sidebar-menu-active-sublist li.active > a {
    background: #444;
}


.mobile-menu-action_buttons .button.button {
    background: #ff296f;
    color: #fff;
    text-shadow: none;
}

\n
\n\/\* Quick Contact \*\/
\n@media screen and (max-width: 767px) {
\n    .quick_contact-item > a.active,
\n    .quick_contact-item > a:hover,
\n    .quick_contact-item > a:active {
\n        color: black;
\n    }
\n}
\n
\n\/\* Sidebar \*\/
\n.sidebar-section > h2 {
\n    background: #31cdb5;
\n    color: #fff;
\n}
\n
\n.sidebar-news-list li {
\n    border-bottom: 1px solid #bfbfbf;
\n}
\n
\na.sidebar-news-link,
\n.eventTitle {
\n    color: #fe0074;
\n}
\n
\n.search-criteria-remove .fa {
\n    color: #f60000;
\n}
\n
\n\/\* Page content \*\/
\n.page-content,
\n.page-content h1 small,
\n.ticket-container {
\n    color: #555;
\n}
\n
\n.page-content h1,
\n.page-content h2,
\n.page-content h3,
\n.page-content h4,
\n.page-content h5,
\n.page-content h6 {
\n    border-color: #bfbfbf;
\n}
\n
\n.page-content h1 { color: #fe0074; font-weight: 500; border: none; }
\n.page-content h2 { color: #fe0074; }
\n.page-content h3 { color: #212121; }
\n.page-content h4 { color: #212121; }
\n.page-content h5 { color: #212121; }
\n.page-content h6 { color: #212121; }
\n.page-content p  { color: #555; font-size: 1rem; line-height: 1.5; }
\n
\n.page-content li:before {
\n    color: #fe0074;
\n}
\n
\n.page-content a:not([class]),
\n.page-content .button\-\-link {
\n    color: #fe0074;
\n}
\n
\n.page-content a:not([class]):visited {
\n    color: #551a8b;
\n}
\n
\n.page-content hr {
\n    border: solid #eee;
\n    border-width: 1px 0 0;
\n}
\n
\n\n.is_success_page .page-content > p {
\n    color: #777;
\n    font-weight: 500;
\n}
\n
\n.toggleable_height-toggles .button\-\-link {
\n    color: #31cdb5;
\n}
\n
\n
\n\/\* Banner \*\/
\n.banner-section .swiper-container {
\n    background-color: #222;
\n    box-shadow: 1px 1px 50rem #777 inset;
\n}
\n
\n.banner-section .swiper-slide {
\n    background-color: #373737;
\n}
\n
\n.banner-slide\-\-columns {
\n    color: #fff;
\n}
\n
\n.banner-slide\-\-columns a {
\n    color: inherit;
\n}
\n
\n.banner-search-title {
\n    background: #fe0074;
\n    color: #fff;
\n}
\n
\n.banner-search .fa {
\n    color: black;
\n}
\n
\n.banner-search-title .fa {
\n    color: #fff;
\n}
\n
\n.banner-search form {
\n    background: black;
\n}
\n
\n.banner-search .form-input {
\n    color: #31cdb5;
\n    font-weight: normal;
\n}
\n
\n.previous_search_text {
\n    color: #fff;
\n}
\n
\n.banner-overlay-content {
\n    color: #000;
\n}
\n
\n.banner-overlay-content h2,
\n.banner-overlay-content p {
\n    margin: 0;
\n}
\n
\n.video-cover {
\n    background-color: hsla(171, 61%, 70%, .3);
\n    border-color: #31cdb5;
\n    color: #31cdb5;
\n}
\n
\n.event-details-back.event-details-back a {
\n    color: #303030;
\n}
\n
\n.ticket-date {
\n    color: black;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .banner-search-title {
\n        border-bottom-color: #FFF;
\n    }
\n
\n    .banner-slide .banner-image {
\n        background-position: center;
\n    }
\n
\n    .banner-slide\-\-center .banner-overlay {
\n        text-align: center;
\n    }
\n
\n    .banner-slide\-\-left .banner-overlay,
\n    .banner-slide\-\-right .banner-overlay {
\n        background-color: transparent;
\n        background-size: cover;
\n    }
\n
\n    .banner-slide\-\-left .banner-overlay {
\n        background-image: url(\'\/shared_media\/uticket\/media\/photos\/content\/banner_overlay_left_mobile.png\');
\n        text-align: left;
\n    }
\n
\n    .banner-slide\-\-right .banner-overlay {
\n        background-image: url(\'\/shared_media\/uticket\/media\/photos\/content\/banner_overlay_right_mobile.png\');
\n        text-align: right;
\n    }
\n
\n    .banner-slide\-\-left .banner-overlay-content,
\n    .banner-slide\-\-right .banner-overlay-content {
\n        padding: 1.25em;
\n        position: absolute;
\n        width: 60%;
\n    }
\n
\n    .banner-section .swiper-button-prev,
\n    .banner-section .swiper-button-next {
\n        transform: scale(.5);
\n    }
\n
\n    .banner-section .swiper-button-prev {
\n        left: 0;
\n    }
\n
\n    .banner-section .swiper-button-next {
\n        right: 0;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .banner-overlay { background-repeat: no-repeat; }
\n    .swiper-slide .banner-image { background-position: center; }
\n
\n    .swiper-slide .banner-overlay {
\n        background-position: top center;
\n        text-align: center;
\n    }
\n
\n    .banner-slide\-\-left .banner-overlay {
\n        background-image: url(\'\/shared_media\/uticket\/media\/photos\/content\/banner_overlay_left.png\');
\n    }
\n
\n    .banner-slide\-\-right .banner-overlay {
\n        background-image: url(\'\/shared_media\/uticket\/media\/photos\/content\/banner_overlay_right.png\');
\n    }
\n}
\n
\n.search-drilldown h3 {
\n    color: #fe0074;
\n}
\n
\n.search-drilldown-column p {
\n    color: #198ebe;
\n}
\n
\n.search-drilldown-column a.active {
\n    background-color: #31cdb5;
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
\n        border-top-color: black;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .header-action:only-child {
\n        border-right: 1px solid #1a1a1a;
\n    }
\n
\n    .search-drilldown-column {
\n        border-color: black;
\n    }
\n}
\n
\n\/\* Calendar \*\/
\n.eventCalendar-wrap {
\n    border-color: #bfbfbf;
\n}
\n
\n.eventsCalendar-slider {
\n    background: #fe0074;
\n    background: -webkit-linear-gradient(#890a3f, #fe0074);
\n    background: linear-gradient(#890a3f, #fe0074);
\n    border-bottom-color: #bfbfbf;
\n}
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
\n    color: #fe0074;
\n}
\n
\n.eventsCalendar-subtitle,
\n.eventsCalendar-list > li > time {
\n    color: #fe0074;
\n}
\n
\n.eventsCalendar-list > li {
\n    border-bottom-color: #bfbfbf;
\n}
\n
\n\/\* News feeds \*\/
\n.news-section {
\n    background: #fff;
\n    box-shadow: 1px 1px 10px #ccc;
\n}
\n
\n.news-slider-link {
\n  color: #fe0074;
\n}
\n
\n.news-slider-title {
\n    color: #fe0074;
\n    background-color: #fff;
\n}
\n
\n.swiper-pagination-bullet {
\n    background-color: #fff;
\n    border-color: #A6AEAD;
\n    box-shadow: inset 0 1px 1px #aaa;
\n}
\n
\n.swiper-pagination-bullet-active {
\n    background-color: #fe0074;
\n}
\n
\n.news-result-date {
\n    background-color: black;
\n    color: #FFF;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .news-result + .news-result:before {
\n        background: linear-gradient(to right, transparent 0, black 10%, black 90%, transparent 100%);
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .news-result + .news-result {
\n        border-color: black;
\n    }
\n}
\n
\n.news-story-navigation a {
\n    color: #198ebe;
\n}
\n
\n.news-story-navigation a {
\n    color: #000;
\n
\n}
\n
\n.news-story-navigation a .fa,
\n.news-story-navigation .news-story-return,
\n.news-story-share_icon {
\n    color: #fe0074;
\n}
\n
\n.news-story-social {
\n    border-color: #bfbfbf;
\n}
\n
\n.news-story-social-link svg {
\n    background: #fe0074;
\n}
\n
\n.testimonial-signature {
\n    color: black;
\n}
\n
\n\/\* Panels \*\/
\n.panel {
\n    background-color: #fff;
\n}
\n
\n.events_feed\-\-recommended .panel {
\n    background: #f3f5f5;
\n    background: rgba(243, 245, 245, .8);
\n}
\n
\n.panel-date {
\n    background-color: #31cdb5;
\n    background-color: rgba(49, 205, 181, .75);
\n    background-color: #31cdb5bf;
\n    top: auto;
\n    top: unset;
\n    right: auto;
\n    right: unset;
\n    bottom: 0;
\n    left: 0;
\n}
\n
\n.carousel-section .panel {
\n    border-color: #bfb8bf;
\n    box-shadow: 1px 0 2px #ccc;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .panels-feed\-\-home_content > .column:after {
\n        background: black;
\n        background: linear-gradient(to right, #E6F3C8 0%, black 20%, black 80%, #E6F3C8 100%);
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
\n    background: #31cdb5;
\n    color: #fff;
\n}
\n
\n.bar-icon svg {
\n  fill: #fff;
\n}
\n
\n.bar-text {
\n    color: black;
\n}
\n
\n@media screen and (max-width: 1023px) {
\n    .bars-section {
\n        background: #f3f3f3 url(\'\/shared_media\/uticket\/media\/photos\/content\/footer_background.svg\') top center;
\n    }
\n}
\n
\n.panel-item.has_form {
\n    background-color: black;
\n    color: #fff;
\n}
\n
\n.panel-item.has_form .button {
\n    background-color: #fff;
\n    border-color: black;
\n    color: black;
\n}
\n
\n.panel-item-image:after {
\n    display: none;
\n}
\n
\n.panel-item.has_image .panel-item-text {
\n    background-image: url(\'\/shared_media\/uticket\/media\/photos\/content\/panel_overlay.png\');
\n    background-color: rgba(48, 205, 181, .5);
\n    color: #fff;
\n    height: auto;
\n    min-height: 2em;
\n    padding: 0 .5rem;
\n    text-align: center;
\n}
\n
\n.widget-heading {
\n    background-color: #31cdb5;
\n    color: #fff;
\n}
\n
\n.widget\-\-checkout .widget-heading {
\n    background-color: #555;
\n}
\n
\n.widget-view_more a {
\n    color: #fe0074;
\n    text-decoration: underline;
\n}
\n
\n.widget-view_more a:hover {
\n    text-decoration: none;
\n}
\n
\n.widget\-\-organizers,
\n.widget\-\-venue {
\n    background-color: #f7f7f7;
\n}
\n
\n.widget\-\-organizers .widget-title,
\n.widget\-\-venue .widget-title {
\n    text-transform: uppercase;
\n}
\n
\n.widget-contact_details-item a:hover,
\n.widget-contact_details-item button:hover {
\n    color: #31cdb5;
\n}
\n
\n.social_media-list a:hover {
\n    background-color: #31cdb5;
\n    color: #fff;
\n}
\n
\n.widget\-\-tickets {
\n    border: none;
\n    box-shadow: none;
\n}
\n
\n.widget\-\-tickets .widget-heading {
\n    background-color: #000;
\n}
\n
\n.widget\-\-tickets .widget-footer {
\n    border-top: none;
\n    padding: 0;
\n}
\n
\n.widget\-\-tickets .widget-footer .button {
\n    border-radius: 0 0 3px 3px;
\n    text-transform: uppercase;
\n}
\n
\n
\n\/\* Search results \*\/
\n.course-list-header {
\n    border-bottom-color: #B7B7B7;
\n}
\n.course-list-display-option :checked + label {
\n    color: #31cdb5;
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
\n    background: black;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-grid .course-widget-price {
\n    background-color: #31cdb5;
\n    color: #fff;
\n}
\n
\n.course-list\-\-list .course-widget-price-original,
\n.course-list\-\-list .course-widget-price-current {
\n    color: #fe0074;
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
\n    background: #31cdb5;
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
\n    color: black;
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
\n    color: black;
\n}
\n
\n.timeline-swiper-prev,
\n.timeline-swiper-next {
\n    color: #f9951d;
\n}
\n
\n\/\* Footer \*\/
\n.footer {
\n    box-shadow: 0px -1px 5px #ccc;
\n}
\n.footer-stats-list {
\n    color: black;
\n}
\n
\n.footer-slogan {
\n    color: black;
\n}
\n
\n.footer-stats {
\n    background: #fff url(\'\/shared_media\/uticket\/media\/photos\/content\/footer_background.svg\') top center;
\n    min-height: 0;
\n}
\n
\n.footer-logo img {
\n    height: 7rem;
\n}
\n
\n.footer-stat h2:after {
\n    border-color: #fe0074;
\n}
\n
\n.footer-social,
\n.footer-columns,
\n.footer-copyright {
\n    background-color: #252525;
\n    border-top: 1px solid #fff;
\n    color: #fff;
\n}
\n
\n.footer-copyright {
\n    background-color: #000;
\n}
\n.footer-apps {
\n    border-top: 1px solid #c8c8c8;
\n}
\n
\n.footer-column-title {
\n    color: #fff;
\n}
\n
\n.footer-column h4 {
\n    font-weight: bold;
\n}
\n
\n.footer-column dt {
\n    color: #31cdb5;
\n}
\n
\n.footer .form-input::-webkit-input-placeholder { color: #000; font-weight: 300; }
\n.footer .form-input::-moz-placeholder          { color: #000; font-weight: 300; }
\n.footer .form-input:-ms-input-placeholder      { color: #000; font-weight: 300; }
\n
\n.newsletter-signup-form .button {
\n    background-color: #fe0074;
\n}
\n
\n
\n@media screen and (max-width: 768px) {
\n    .footer-stat.footer-stat {
\n        width: 100%;
\n        margin: 1em 0;
\n        padding: 0;
\n    }
\n}
\n
\n\/\* Dropdown filters \*\/
\n.search-filter-total {
\n    color: black;
\n}
\n
\n.search-filters :checked ~ .form-checkbox-helper,
\n.search-filters :checked ~ .form-radio-helper,
\n.search-filters :checked ~ .form-checkbox-label,
\n.search-filters :checked ~ .form-radio-label {
\n    border-color: black;
\n    color: black;
\n}
\n
\n.search-filters :checked + .form-radio-helper:after {
\n    background-color: black;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-filter-dropdown.filter-active > button,
\n    .search-filters-clear {
\n        color: black;
\n    }
\n
\n    .checkout-heading {
\n        background-color: #31cdb5;
\n        color: #fff;
\n    }
\n}
\n
\n\/\* Login \*\/
\n.login-form-container.login-form-container .modal-header {
\n    background: #000;
\n    color: #ebebeb;
\n}
\n
\n.login-form-container a {
\n    color: #fe0074;
\n}
\n
\n.signup-text a {
\n    font-weight: bolder;
\n}
\n
\n.login-form-container .btn {
\n    text-shadow: none;
\n}
\n
\n.login-form-container .client-logo {
\n    width: 165px;
\n}
\n
\n.login .modal-footer hr {
\n    border-color: #383838;
\n}
\n
\n.login-form-container .nav-tabs {
\n    border: none;
\n}
\n
\n.login-form-container.login-form-container .nav-tabs > li > a,
\n.login-form-container.login-form-container .nav-tabs > li.active > a {
\n    color: #fff;
\n}
\n
\n
\n.login-form-container.login-form-container .nav-tabs > .active > a:after,
\n:checked + .checkbox-switch-helper:before {
\n    background-color: #4ccfb2;
\n}
\n
\n
\n
\n
\n\/\* Misc \*\/
\n.contact\-\-left .ui-tabs-nav .ui-tabs-active a {
\n    background: #fe0074;
\n    border-color: #fe0074;
\n    color: #fff;
\n}
\n
\n.guest-user-bg {
\n    background: #fff url(\'\/shared_media\/uticket\/media\/photos\/content\/footer_background.svg\') top center;
\n}
\n
\n.checkout-right-sect .item-summary-head {
\n    background-color: black;
\n    border: none;
\n    color: #fff;
\n}
\n
\n.item-summary-head button {
\n    color: inherit;
\n}
\n
\n.checkout-right-sect .btn-close:hover {
\n    color: black;
\n    border-color: black;
\n}
\n
\n.checkout-breakdown {
\n    color: #fe0074;
\n}
\n
\n.checkout-progress li a:after {
\n    background-color: #fff;
\n    border-color: #fe0074;
\n}
\n
\n.checkout-progress li.curr a:after {
\n    background: #fe0074;
\n    background: radial-gradient(#ff69ad, #fe0074);
\n    border-color: #960d4a;
\n}
\n
\n.checkout-progress li + li:before {
\n    border-color: #fe0074;
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
\n    color: #fe0074;
\n}
\n
\n.search-package-available .show-more {
\n    background: black;
\n    color: #fff;
\n}
\n
\n.prepay-box h6 {
\n    color: black;
\n}
\n
\n.custom-calendar .booking-date-button {
\n    background-color: black;
\n    color: #fff;
\n}
\n
\n.custom-calendar .booking-date-button:hover {
\n    background-color: #fe0074;
\n}
\n
\n.custom-calendar button.booking-date-button.active {
\n    background-color: #fff;
\n    color: black;
\n}
\n
\n.course-activity-alert,
\n.details-wrap .left-place {
\n    color: #F75A5F;
\n}
\n
\n.number-of-people-viewing {
\n    color: black;
\n}
\n
\n.search-calendar-course-image .fa {
\n    background-color: black;
\n    color: #fff;
\n}
\n
\n.custom-calendar tbody td.active,
\n.custom-calendar tbody td.active:hover {
\n    background-color: #fff;
\n    color: black;
\n}
\n
\n.custom-calendar tbody tr:first-child td {
\n    color: #222;
\n}
\n
\n.package-offers-wrap h2 {
\n    color: black;
\n    border-color: #c5cecd;
\n}
\n
\n.package-offers-wrap h3 {
\n    color: black;
\n}
\n
\n.package-offers-wrap .summary-wrap .more,
\n.classes-details-wrap .details-wrap li:first-child {
\n    color: black;
\n}
\n
\n.classes-details-wrap .details-wrap li:first-child {
\n  background-color: black;
\n}
\n
\n.details-wrap .remove-booking,
\n.details-wrap .wishlist.remove{
\n    color: black;
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
\n    color: black;
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
\n    color: black;
\n}
\n
\n.details-wrap:hover li:first-child {
\n    background-color: black;
\n}
\n
\n.details-wrap:hover .sidelines::before,
\n.details-wrap:hover .sidelines::after,
\n.details-wrap:hover .price-wrap {
\n    border-color:black;
\n}
\n
\n
\n\/\* course results booked \*\/
\n.details-wrap.booked {
\n    border-color:black;
\n    background-color: #f3f3f3;
\n}
\n
\n.details-wrap.booked .time,
\n.details-wrap.booked .price,
\n.details-wrap.booked .fa-book {
\n    color: black;
\n}
\n.details-wrap.booked li:first-child {
\n    background-color: black;
\n}
\n
\n.details-wrap.booked .sidelines::before,
\n.details-wrap.booked .sidelines::after,
\n.details-wrap.booked .price-wrap {
\n    border-color:black;
\n}
\n
\n.classes-details-wrap .alert-wrap {
\n    background-color: black;
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
\n    color: black;
\n}
\n
\n.custom-calendar .booking-date-button.already_booked {
\n    background-color: black;
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
\n    background-image: url(\"data:image\/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M0%2C22L22%2C0l2.1%2C2.1L4.2%2C22l19.9%2C19.9L22%2C44L0%2C22L0%2C22L0%2C22z\'%20fill%3D\'%23fe0074\'%2F%3E%3C%2Fsvg%3E\");
\n}
\n
\n.swiper-button-next {
\n    background-image: url(\"data:image\/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M27%2C22L27%2C22L5%2C44l-2.1-2.1L22.8%2C22L2.9%2C2.1L5%2C0L27%2C22L27%2C22z\'%20fill%3D\'%23fe0074\'%2F%3E%3C%2Fsvg%3E\");
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
\n.ticket-description,
\n.ticket-sales_ended {
\n    color: #787878;
\n}
\n
\n.event-related {
\n    background: #000;
\n    color: #fff;
\n}
\n
\n.event-related a {
\n    color: #fff;
\n}
\n
\n.event-related img {
\n    border: 1px solid #fff;
\n}
\n
\n
\n
\n
\n\/\* Header customisation \*\/
\n.header {
\n    font-size: 1rem;
\n}
\n
\n.header-action,
\n.header-menu-section > a {
\n    display: flex;
\n    align-items: center;
\n    min-height: 3.875rem;
\n    padding-top: 0;
\n    padding-bottom: 0;
\n}
\n
\n.header-menu-section > a:after {
\n    border-width: .6em .3em 0;
\n}
\n
\n.header-menu-expand.expanded:before {
\n    top: 3.5em;
\n}
\n
\n.header-item.header-logo {
\n    margin-right: 2.5em;
\n}
\n"
WHERE
  `stub` = '39'
;;


INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('app_store_link',   'App Store Link',   '', '', '', '', '', 'both', 'Link to download your App from the Apple App Store',   'text', 'Apps', 0, ''),
  ('google_play_link', 'Google Play Link', '', '', '', '', '', 'both', 'Link to download your App from the Google Play Store', 'text', 'Apps', 0, '');;

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('checkout_terms_and_conditions', 'Terms and conditions text', '', '', '', '', '', 'both', '&quot;Terms and conditions&quot; text to appear on the checkout screen.', 'wysiwyg', 'Checkout', 0, '');;


UPDATE
  `engine_settings`
SET
  `linked_plugin_name` = null
WHERE
  `variable` IN ('course_finder_mode', 'courses_results_per_page')
;;


INSERT IGNORE INTO
  `plugin_pages_layouts` (`layout`, `template_id`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`)
VALUES (
  'thankyou',
  (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '04' AND `deleted` = 0),
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
);;

INSERT IGNORE INTO
  `engine_localisation_messages` (`message`, `created_on`, `updated_on`)
VALUES
  ('Recommended Events', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);;

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('frontend_login_link_text', 'Log-in button text', 'Log in', 'Log in', 'Log in', 'Log in', 'Log in', 'both', 'Text to appear in the front-end log-in link', 'text', 'Website', 0, '');;
