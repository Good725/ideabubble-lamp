/*
ts:2019-06-11 15:00:00
*/

/* Add the '49' (Irish Times Training) theme, if it does not already exist */
DELIMITER  ;;
INSERT INTO
  `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '49', '49', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_themes`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_themes` WHERE `stub` = '49')
    LIMIT 1
;;


/* Add the '49' theme styles */
UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = '@import url(\'https://fonts.googleapis.com/css?family=Noto+Serif:400,400i,700,700i\');
\n@import url(\'https://fonts.googleapis.com/css?family=Raleway:300,300i,400,400i,500,500i\');
\n@import url(\'https://fonts.googleapis.com/css?family=Work+Sans\');
\n
\n:root {
\n    \-\-primary: #19A29E;   \-\-primary-hover: #32BCAD;   \-\-primary-active: #0D7A93;
\n    \-\-secondary: #f5f5f5; \-\-secondary-hover: #d3d3d3; \-\-secondary-active: #e6e6e6;
\n    \-\-success: #1E3B49;   \-\-success-hover: #077C94;   \-\-success-active: #0f1d24;
\n    \-\-info: #5bc0de;      \-\-info-hover: #31b0d5;      \-\-info-active: #269abc;
\n    \-\-warning: #e8a917;   \-\-warning-hover: #e7b236;   \-\-warning-active: #c89214;
\n    \-\-danger: #df1e39;    \-\-danger-hover: #bd362f;    \-\-danger-active: #c9302c;
\n}
\n
\nhtml,
\n.header-item .button,
\n.footer-social-icons h5 {
\n    font-family: Raleway, Helvetica, Arial, sans-serif;
\n}
\n
\nh1, h2, h3, h4, h5, h6,
\n.button,
\n.footer-column-title,
\n.checkout-progress,
\n.header-menu-section > a,
\n.header-menu a[href^="/course-"] /* temporary, until we have a better way of identifying these. */ {
\n    font-family: \'Noto Serif\', Times, \'Times New Roman\', serif;
\n}
\n
\n.news-category-embed p { font-family: \'Work Sans\', Raleway, Helvetica, Arial, sans-serif;}
\n
\nbutton{font-family: inherit;}
\n
\n\/\* Heading sizes \*\/
\n@media screen and (max-width: 767px) {
\n    h1, .page-content h1, .banner-slide h1 {font-size: 30px; font-weight: 700; line-height: 1.13667; }
\n    h2, .page-content h2, .banner-slide h2 {font-size: 24px; font-weight: 700; line-height: 1.13667; margin: 0 0 .8rem; }
\n    h3, .page-content h3, .banner-slide h3 {font-size: 22px; font-weight: 700; line-height: 1.13667; }
\n    h4, .page-content h4, .banner-slide h4 {font-size: 18px; font-weight: 700; line-height: 1.13667; }
\n    h5, .page-content h5, .banner-slide h5 {font-size: 16px; font-weight: 700; line-height: 1.13667; }
\n    h6, .page-content h6, .banner-slide h6 {font-size: 14px; font-weight: 700; line-height: 1.13667; }
\n
\n    .banner-slide h1 { font-size: 36px; line-height: 1; margin: .5rem 0;}
\n    .layout-home .banner-slide h1 { font-size: 22px; line-height: 1.13667;}
\n    .banner-slide p { font-size: 16px; line-height: 1.1875; margin: .5rem 0;}
\n    .layout-home .banner-slide p  { font-size: 14px; line-height: 1.5;}
\n}
\n
\n@media screen and (min-width: 768px) {
\n    h1, .page-content h1, .banner-slide h1 {font-size: 42px; font-weight: 700; line-height: 57px; margin: .6em 0; }
\n    h2, .page-content h2, .banner-slide h2 {font-size: 36px; font-weight: 700; line-height: 49px; margin: 0 0 1rem; }
\n    h3, .page-content h3, .banner-slide h3 {font-size: 28px; font-weight: 700; line-height: 38px; }
\n    h4, .page-content h4, .banner-slide h4 {font-size: 24px; font-weight: 700; line-height: 33px; }
\n    h5, .page-content h5, .banner-slide h5 {font-size: 22px; font-weight: 700; line-height: 30px; margin: .25rem 0; }
\n    h6, .page-content h6, .banner-slide h6 {font-size: 18px; font-weight: 700; line-height: 25px; }
\n
\n    .banner-slide h1 { font-size: 72px; margin: .75rem 0; }
\n    .layout-home .banner-slide h1 { font-size: 42px;}
\n    .banner-slide p { margin: .75rem 0;}
\n}
\n
\nbody {
\n    background-color: #fff;
\n    color: #212121;
\n}
\n
\n.layout-course_list,
\n.layout-course_list2 {
\n    background-color: #f6f6f6;
\n}
\n
\n.container {
\n    max-width: 1440px;
\n    padding-left: 20px;
\n    padding-right: 20px;
\n}
\n
\n.row.gutters {
\n    margin-left: -20px;
\n    margin-right: -20px;
\n}
\n
\n.col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
\n    padding-left: 20px;
\n    padding-right: 20px;
\n}
\n
\n.simplebox-column {
\n    margin-left: 20px;
\n    margin-right: 20px;
\n}
\n
\n.simplebox-thin-margins .simplebox-column {
\n    margin-left: 10px;
\n    margin-right: 10px;
\n}
\n
\n.form-input {
\n    border-color: #ebebeb;
\n}
\n
\n:checked + .form-checkbox-helper:after {
\n    color: var(\-\-primary);
\n}
\n
\n.table thead {
\n    background: #19A29E;
\n    color: #FFF;
\n}
\n
\n.badge {
\n    background: #19A29E;
\n    color: #fff;
\n}
\n
\n.db-sidebar .sidebar-menu li a:hover,
\n.db-sidebar .sidebar-menu li a.active {
\n    background-color: #19A29E;
\n    color: #fff;
\n}
\n
\n.popup-header {
\n    background: #19A29E;
\n    color: #fff;
\n}
\n
\n.button.course-banner-button.cl_bg {
\n    background-color: #1E3B49;
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
\n    color: #19A29E;
\n}
\n
\n.autotimetable .new_date {
\n    border-color: #19A29E;
\n}
\n
\n.autotimetable .new_date td:nth-child(1) {
\n    background-color: #19A29E;
\n    color: #fff;
\n}
\n
\n:checked + .seating-selector-checkbox-helper:after {
\n    color: #1E3B49;
\n}
\n
\n.seating-selector-option-radio:checked + .button {
\n    background: #1E3B49;
\n    color: #fff;
\n}
\n
\n.seating-selector-option-hover {
\n    background-color: #1E3B49;
\n    color: #fff;
\n}
\n
\n\/\* Forms \*\/
\n.select:after {
\n    border-top-color: #19A29E;
\n}
\n
\n.form-select:before {
\n    background-image: -webkit-linear-gradient(left, transparent 0, transparent calc(100% - 2.75em), #19A29E calc(100% - 2.75em), #19A29E 100%);
\n    background-image: linear-gradient(to right, transparent 0, transparent calc(100% - 2.75em), #19A29E calc(100% - 2.75em), #19A29E 100%);
\n}
\n
\n.form-select-plain select {
\n    border-color: #333;
\n    border-radius: 0;
\n}
\n
\n.form-select-plain .form-select:after {
\n    border-color: #333;
\n}
\n
\n.button,
\n.formrt button,
\n.formrt [type=\"submit\"],
\n.formrt [type=\"reset\"] {
\n    background-color: #19A29E;
\n    border-radius: 0;
\n}
\n
\n.button:hover,
\n.formrt button:hover,
\n.formrt [type=\"submit\"]:hover,
\n.formrt [type=\"reset\"]:hover {
\n    background-color: var(\-\-primary-hover);
\n}
\n
\n.button:active,
\n.formrt button:active,
\n.formrt [type=\"submit\"]:active,
\n.formrt [type=\"reset\"]:active {
\n    background-color: var(\-\-primary-active);
\n}
\n
\n.page-content .formrt ul li {
\n    padding: 0;
\n}
\n
\n.button:after {
\n    content: \'\\a0\\bb\';
\n}
\n
\n.header .button:after {
\n    content: none;
\n}
\n
\n.button\-\-continue {
\n    background-color: #1E3B49;
\n    border-color: transparent;
\n    color: #fff;
\n}
\n
\n.button\-\-continue.inverse {
\n    background-color: #fff;
\n    border: 1px solid #1E3B49;
\n    color: #1E3B49;
\n}
\n
\n.button\-\-cancel {
\n    background: #fff;
\n    border: 1px solid #f00;
\n    color: #f00;
\n}
\n
\n.button\-\-pay {
\n    background-color: #19A29E;
\n}
\n
\n.button\-\-pay.inverse {
\n    background: #FFF;
\n    border: 1px solid #19A29E;
\n    color: #19A29E;
\n}
\n
\n.button\-\-book {
\n    background-color: #19A29E;
\n}
\n
\n.button\-\-book.inverse {
\n    background: #fff;
\n    border-color: #19A29E;
\n    color: #19A29E;
\n}
\n
\n.button\-\-book:disabled {
\n    background-color: #dbdbdb;
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
\n    background: #1E3B49;
\n    color: #fff;
\n}
\n
\n.button\-\-send.inverse {
\n    background: #fff;
\n    border-color: #1E3B49;
\n    color: #1E3B49;
\n}
\n
\n.button\-\-enquire {
\n    background: #19A29E;
\n    color: #fff;
\n}
\n
\n.course-list-item-button  {
\n    background: none;
\n    border: 1px solid #333;
\n    color: #333;
\n}
\n
\n.course-list-item-button:hover {
\n    background: none;
\n}
\n
\n.header-action:nth-last-child(odd) .button {
\n    background: #19A29E;
\n    color: #fff;
\n}
\n
\n.header-action:nth-last-child(even) .button,
\n.header-action.header-action\-\-login .button {
\n    background: #1E3B49;
\n    color: #fff;
\n}
\n
\n.share_button {
\n    background: none;
\n    border: 1px solid #fff;
\n    border-radius: 0;
\n    box-shadow: none;
\n    color: #fff;
\n    display: block;
\n    font-size: 15px;
\n    height: auto;
\n    line-height: 1.666667;
\n    margin: 0 auto .5em;
\n    min-width: 203px;
\n    width: 203px;
\n    padding: .6em 2.444444em .6em 1em;
\n    position: relative;
\n    text-align: left;
\n    text-indent: 0;
\n    text-shadow: none;
\n}
\n
\n.share_button:before {
\n    display: none;
\n}
\n
\n.share_button:after {
\n    position: absolute;
\n    top: 0rem;
\n    right: .5em;
\n    font-size: 2em;
\n    line-height: 1.5;
\n    text-align: right;
\n}
\n
\n.share_button\-\-facebook:after { content: \'\\f09a\'; font-family: fontAwesome; }
\n.share_button\-\-twitter:after  { content: \'\\f099\'; font-family: fontAwesome; }
\n.share_button\-\-email:after    { content: \'\\f003\'; font-family: fontAwesome; }
\n
\na.share_button.share_button:hover {
\n    background: #fff;
\n    color: var(\-\-primary);
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .share_button {
\n        display: inline-block;
\n        margin: 0 1.9em 0 0;
\n    }
\n}
\n
\n.header-left {
\n    display: flex;
\n    flex: 1
\n}
\n
\n.header-left .header-item:last-child {
\n    margin-left: auto;
\n}
\n
\n.header-actions {
\n    flex: unset;
\n    margin-left: .25rem;
\n}
\n
\n.header-action {
\n   padding-left: .5rem;
\n   padding-right: .5rem;
\n}
\n
\n.header-right .header-action:last-child {
\n    padding-right: 0;
\n}
\n
\n.formErrorContent,
\n.formErrorArrow div {
\n    background: #19A29E;
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
\n.popup_box.alert-success { border-color: #19A29E; }
\n.popup_box.alert-info    { border-color: #2472AC; }
\n.popup_box.alert-warning { border-color: #FCC14F; }
\n.popup_box.alert-danger,
\n.popup_box.alert-error   { border-color: #D74638; }
\n.popup_box.alert-add     { border-color: #19A29E; }
\n.popup_box.alert-remove  { border-color: #b4b4b4; }
\n
\n.popup_box .alert-icon [fill]   {   fill: #19A29E; }
\n.popup_box .alert-icon [stroke] { stroke: #19A29E; }
\n
\n
\n\/\* Header \*\/
\n.header,
\n.mobile-breadcrumbs {
\n    background-color: #fff;
\n    color: #19A29E;
\n}
\n
\n.mobile-breadcrumbs * { color: #19A29E; }
\n
\n.header {
\n    padding: 0;
\n}
\n
\n.header > .row > div {
\n    padding-left: 10px;
\n    padding-right: 10px;
\n}
\n
\n.header > .row {
\n   max-width: 1360px;
\n   padding-left: 20px;
\n   padding-right: 20px;
\n}
\n
\n.header > .row > div {
\n    padding-left: 10px;
\n    padding-right: 10px;
\n}
\n
\n.mobile-breadcrumbs {
\n    display: none;
\n}
\n
\n.dropdown-menu-header {
\n    background-color: #19A29E;
\n    color: #fff;
\n}
\n
\n.mobile-menu-toggle {
\n    color: #19A29E;
\n}
\n
\n.header-cart-button [fill] { fill: #19A29E; }
\n.header-cart-button [stroke] { stroke: #19A29E; }
\n
\n.header-logo img {
\n    max-height: 50px;
\n}
\n
\n.header-menu.header-menu {
\n    background: #1E3B49;
\n    border-radius: 0;
\n    box-shadow: none;
\n    color: #fff;
\n    margin-top: 0;
\n}
\n
\n.header-menu.header-menu:not(.has_submenus) li {
\n    border-radius: 0;
\n}
\n
\n.header-menu.header-menu a,
\n.header-menu .level_1 > a {
\n    color: #fff;
\n    text-transform: none;
\n}
\n
\n.header-menu a[href^="/course-"] {
\n    color: #19A29E;
\n    font-weight: bold;
\n}
\n
\n.header-menu .level_2 a:hover,
\n.header-menu .level_2:hover > a {
\n    color: #19A29E;
\n}
\n
\n.header-item > a:not(.button) {
\n    color: #1E3B49;
\n    font-size: 1rem;
\n    padding: 1.35em 1.1em;
\n    text-transform: none;
\n}
\n
\n.header-item > .header-menu-expand.expanded {
\n    background: #1E3B49;
\n    color: #fff;
\n}
\n
\n.header-menu-section > a:after {
\n    border-top-color: #1E3B49;
\n}
\n
\n.header-menu-expand.expanded:after {
\n    border-top-color: #fff;
\n}
\n
\n.header-menu-expand > img {
\n    position: relative;
\n    top: -.5em;
\n}
\n
\n.header-menu-section > a {
\n    border: none;
\n    padding-left: 1em;
\n    padding-right: 1em;
\n}
\n
\n.header-item .button {
\n    border-radius: 2px;
\n    font-size: .75rem;
\n    line-height: 1.5;
\n    min-width: 100px;
\n    padding: .667em .9em;
\n}
\n
\n.mobile-menu .level_1 > a,
\n.mobile-menu .level_1 > button,
\n.mobile-menu-level3-section .mobile-menu-list > a {
\n    color: #19A29E;
\n}
\n
\n.header-menu .level_2 a:before {
\n    border-left-color: #19A29E;
\n}
\n
\n.header-menu .level_2 a:hover:before,
\n.header-menu .level_2:hover > a:hover {
\n    border-left-color: #19A29E;
\n}
\n
\n.header-menu .level_3 {
\n    border-bottom-color: #19A29E;
\n}
\n
\n.mobile-menu-top strong,
\n.mobile-menu-top-avatar,
\n.mobile-menu-button-group-icon,
\n.header-cart-breakdown,
\n.final_price_value {
\n    color: #19A29E;
\n}
\n
\n.header-cart-amount,
\n.mobile-menu li.active > a,
\n.checkout-item-title {
\n    color: #19A29E;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .header {
\n        border-bottom: 1px solid #eee;
\n    }
\n
\n    .header > .row,
\n    .header-mobile.row {
\n        height: 60px;
\n    }
\n
\n    .header > .row {
\n        margin: 0;
\n        padding: 0 5px;
\n    }
\n
\n    .header > .row > div:nth-child(2) {
\n        padding-left: 0;
\n        padding-right: 0;
\n    }
\n
\n    \/\* Quick Contact \*\/
\n    .quick_contact {
\n        background: #19A29E;
\n        color: #fff;
\n    }
\n
\n    .quick_contact a {
\n        color: #fff;
\n    }
\n
\n    .quick_contact-item-icon {
\n        font-size: 1.5rem;
\n}
\n
\n    .quick_contact-item > a.active,
\n    .quick_contact-item > a:hover,
\n    .quick_contact-item > a:active {
\n        color: #fff;
\n    }
\n}
\n
\n\/\* Sidebar \*\/
\n.sidebar-section > h2 {
\n    color: #1E3B49;
\n    margin-bottom: .625rem;
\n    padding: 0;
\n    text-align: left;
\n    text-transform: none;
\n}
\n
\n.search-filter-list {
\n    letter-spacing: .055em;
\n}
\n
\n.sidebar-section-collapse {
\n    display: none;
\n}
\n
\n.sidebar-section .form-input,
\n.sidebar-section .input_group {
\n    background: none;
\n    border-color: #c4c4c4;
\n}
\n
\n.sidebar-section .form-input {
\n    letter-spacing: 0.06em;
\n    padding: .5em .5em .5em 1.375em;
\n}
\n
\n.sidebar-section .form-input::-webkit-input-placeholder { color: #333; }
\n
\n.sidebar-section .input_group-icon {
\n    background: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n.sidebar-section-content ul {
\n    padding-left: 0;
\n    padding-right: 0;
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
\n    color: #1E3B49;
\n}
\n
\n.search-criteria-remove .fa {
\n    color: #f60000;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .sidebar {
\n        max-width: 277px;
\n    }
\n
\n    .sidebar + .content_area {
\n        padding-left: 43px;
\n        width: calc(100% - 277px);
\n    }
\n}
\n
\n\/\* Page content \*\/
\n.row.page-content {
\n    max-width: 1080px;
\n    padding-left: 20px;
\n    padding-right: 20px;
\n}
\n
\n.content-area {
\n    font-weight: 400;
\n}
\n
\n.page-content h1,
\n.page-content h2,
\n.page-content h3,
\n.page-content h4,
\n.page-content h5,
\n.page-content h6 {
\n    border: none;
\n}
\n
\n.page-content p {
\n    color: inherit;
\n}
\n
\n.page-content ul > li:before {
\n    content: \'•\';
\n    font-size: .6em;
\n    top: .6em;
\n}
\n
\n.page-content ul > li {
\n    padding-left: .75em;
\n}
\n
\n.page-content a:not([class]),
\n.page-content .button\-\-link {
\n    color: #19A29E;
\n}
\n
\n.banner-overlay-content .button,
\n.page-content .button {
\n    font-weight: bold;
\n    padding: 1em;
\n}
\n
\n.page-content a:not([class]):visited {
\n    color: #551a8b;
\n}
\n
\n.page-content header {
\n    font-weight: normal;
\n}
\n
\n.page-content hr {
\n    border-color: #19A29E;
\n}
\n
\n.page-content strong {
\n    font-weight: bold;
\n}
\n
\n.team-section img {
\n    display: block;
\n}
\n
\n.team-section p {
\n    font-size: 15px;
\n    margin: 0;
\n}
\n
\n.team-section .simplebox-content {
\n    position: relative;
\n}
\n
\n.team-section .simplebox-content div {
\n    background: var(\-\-primary);
\n    color: #fff;
\n    display: none;
\n    position: absolute;
\n    bottom: 0;
\n    padding: .2rem 1rem .45rem;
\n    width: 100%;
\n}
\n
\n.team-section .simplebox-content:hover div {
\n    display: block;
\n}
\n
\n.team-section .simplebox-content div > * {
\n    margin: .25rem 0;
\n}
\n
\n.simplebox-icons .simplebox-content > :first-child{margin-top: 0;}
\n.simplebox-icons .simplebox-content > :last-child{margin-bottom: 0;}
\n
\n.simplebox-icons .simplebox .simplebox-column { margin: 0; }
\n
\n@media screen and (max-width: 767px) {
\n    body:not(.has_banner) .content {
\n        margin-top: 30px;
\n    }
\n
\n    .page-content {
\n        line-height: 1.25;
\n    }
\n
\n    .page-content p {
\n        margin: 0 0 1.25rem;
\n   }
\n
\n    .page-content .simplebox:not(:last-child) {
\n        margin-bottom: 30px;
\n    }
\n
\n    .banner-overlay-content .button,
\n    .page-content .button {
\n        font-size: 13px;
\n        min-width: 170px;
\n    }
\n
\n    .page-content .simplebox.team-section.team-section {
\n        margin-bottom: 0;
\n     }
\n
\n    .team-section .simplebox-columns {
\n        margin-left: -.75em;
\n        margin-right: -.75em
\n    }
\n
\n    .team-section .simplebox-column {
\n        padding: 0 .75em !important;
\n    }
\n
\n    .team-section .simplebox-content div > * {
\n        margin: 0;
\n    }
\n
\n    .team-section p {
\n        font-size: 9px;
\n        font-weight: normal;
\n        line-height: 2;
\n    }
\n
\n    .team-section.team-section.team-section .simplebox-column {
\n         width: 50% !important;
\n    }
\n
\n    .team-section .simplebox-column .simplebox-content img {
\n        width: 100% !important;
\n    }
\n
\n    .page-content .simplebox-icons:not(:last-child) {margin-bottom: 0;}
\n    .page-content .simplebox-icons + .simplebox-icons:not(:last-child) {margin-bottom: 30px;}
\n
\n    .simplebox-icons img { max-width: 40px; }
\n    .simplebox-icons h5 { margin-bottom: .5em; }
\n    .simplebox-icons .simplebox-title { text-align: center;}
\n    .simplebox-icons .simplebox.simplebox .simplebox-column-1 { width: 47px !important; }
\n    .simplebox-icons .simplebox.simplebox .simplebox-column-2 { width: calc(100% - 47px) !important; }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .page-content {
\n        font-size: 18px;
\n        line-height: 1.5;
\n    }
\n
\n    .page-content p {
\n        font-size: 18px;
\n        margin: 0 0 1.6rem;
\n    }
\n
\n    .banner-overlay-content .button,
\n    .page-content .button {
\n        font-size: 1rem;
\n        min-width: 200px;
\n    }
\n
\n    .team-section .simplebox-column {
\n         margin-bottom: 10px;
\n    }
\n
\n    .simplebox-icons img { max-width: 90px; }
\n    .simplebox-icons .simplebox-title { text-align: left;}
\n    .simplebox-icons .simplebox.simplebox .simplebox-column-1 { width: 90px; }
\n    .simplebox-icons .simplebox.simplebox .simplebox-column-2 { width: calc(100% - 90px); }
\n}
\n
\n\/\* Banner search \*\/
\n.banner-search-title {
\n    background: #0E918D;
\n    color: #fff;
\n}
\n
\n.banner-search form {
\n    background: #19A29E;
\n}
\n
\n.banner-search .form-input {
\n   color: #19A29E;
\n}
\n
\n.previous_search_text {
\n    color: #fff;
\n}
\n
\n.banner-overlay .row {
\n    max-width: 800px;
\n}
\n
\n.layout-home .banner-overlay .row,
\n.has_linked_subject .banner-overlay .row {
\n    max-width: 1040px;
\n}
\n
\n.has_linked_subject .banner-overlay p {
\n    max-width: 700px;
\n}
\n
\n.has_linked_subject .banner-overlay h6 {
\n    margin: 0;
\n}
\n
\n.banner-overlay-content {
\n    color: #fff;
\n    font-size: 1.25rem;
\n    line-height: 1.5;
\n}
\n
\n.simplebox-raised .simplebox-content > :first-child,
\n.banner-overlay-content > :first-child {
\n    margin-top: 0;
\n}
\n
\n.simplebox-raised .simplebox-content > :last-child,
\n.banner-overlay-content > :last-child {
\n    margin-bottom: 0;
\n}
\n
\n.banner-overlay-content .button {
\n    min-width: 0;
\n    padding: .84em 2.65em;
\n}
\n
\n.banner-slide\-\-left .banner-overlay .row {
\n    background-image: url(\'\/media\/photos\/content\/banner_overlay_left.png\');
\n    background-position: left;
\n    background-repeat: no-repeat;
\n}
\n
\n.banner-slide\-\-right .banner-overlay .row {
\n    background-image: url(\'\/media\/photos\/content\/banner_overlay_right.png\');
\n    background-position: right;
\n    background-repeat: no-repeat;
\n}
\n
\n.has_category_color .banner-image {
\n    background-color: var(\-\-category-color);
\n    background-blend-mode: multiply;
\n}
\n
\n.simplebox-overlap-left .simplebox-column:last-child .simplebox-content,
\n.simplebox-overlap-right .simplebox-column:first-child .simplebox-content {
\n    box-shadow: 0 1.125rem 1.6875rem rgba(0, 0, 0, .18);
\n}
\n
\n.background-extended {
\n    position: relative;
\n}
\n
\n.background-extended:before {
\n    content: \'\';
\n    background: #f6f6f6;
\n    position: absolute;
\n    top: -50%;
\n    right: 0;
\n    bottom: -50%;
\n    left: 0;
\n    z-index: -1;
\n}
\n
\n.layout-home .simplebox-raised .simplebox-column {
\n    padding: 16px 20px 18px;
\n}
\n
\n.layout-home .simplebox.simplebox-raised h4 {
\n    margin-bottom: 0;
\n}
\n
\n.layout-home .simplebox-raised p {
\n    font-size: 15px;
\n    line-height: 19px;
\n    margin: 10px 0;
\n}
\n
\n.layout-home .simplebox-raised p:last-child {
\n    margin-bottom: 0;
\n}
\n
\n.page-content .simplebox.team-section {
\n    margin-bottom: 30px;
\n}
\n
\n@media screen and (max-width: 479px) {
\n    .banner-overlay-content p {
\n        max-width: 290px;
\n    }
\n
\n    .layout-home .banner-overlay-content p {
\n        max-width: 210px;
\n    }
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .banner-section {min-height: 280px;}
\n
\n    .swiper-slide .banner-image {
\n        background-position: center;
\n        height: 280px
\n    }
\n
\n    .layout-home .banner-image {
\n        height: 428px;
\n    }
\n
\n    .has_linked_subject .banner-overlay .row {
\n        align-items: flex-start;
\n        padding-top: 3rem;
\n    }
\n
\n    .has_linked_subject .banner-image {
\n        height: 300px;
\n    }
\n
\n    .banner-search-title {
\n        border-bottom-color: #FFF;
\n    }
\n
\n    .banner-overlay {
\n        background: none;
\n    }
\n
\n    .banner-image {
\n        background-size: cover;
\n    }
\n
\n    /* Until we upload a separate mobile banner */
\n    .layout-home .banner-image {
\n        background-color: #2a414c;
\n        background-position: 73% bottom;
\n        background-size: auto 350px;
\n    }
\n
\n    .has_linked_subject .banner-image {
\n        background-position: right;
\n    }
\n
\n    .has_linked_subject .banner-overlay h6 + h1 {
\n        font-size: 36px;
\n        line-height: 1;
\n        margin: .85rem 0 .5rem;
\n    }
\n
\n    .layout-home .banner-overlay .row {
\n        align-items: unset;
\n        padding-top: 2.5rem;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .banner-overlay { background-repeat: no-repeat; }
\n    .swiper-slide .banner-image {
\n        background-position: center;
\n        height: var(\-\-slide-height, 600px);
\n    }
\n
\n    .swiper-slide .banner-overlay {
\n        background-position: top center;
\n    }
\n
\n    .banner-overlay-content {
\n        max-width: none;
\n    }
\n
\n    .layout-home .banner-overlay-content {
\n        max-width: 650px;
\n    }
\n
\n    .layout-home .banner-overlay-content p {
\n        max-width: 580px;
\n    }
\n
\n    .banner-slide\-\-center .banner-overlay {
\n        background: rgba(66, 91, 168, .333)
\n    }
\n
\n    .banner-overlay .row {
\n        display: flex;
\n        align-items: flex-end;
\n    }
\n
\n    .has_linked_subject .banner-overlay .row,
\n    .layout-home .banner-overlay .row {
\n        align-items: center;
\n    }
\n
\n    .page-content .simplebox:not(:last-child) {
\n        margin-bottom: 75px;
\n    }
\n
\n    .simplebox.simplebox.simplebox-raised { margin-top: -162px; }
\n    .layout-home .simplebox.simplebox-raised { margin-top: -135px; }
\n    .has_linked_subject .simplebox.simplebox-raised { margin-top: -72px; }
\n
\n    /* So that vertical alignment is not thrown off by raised boxes */
\n    .banner-overlay-content { padding-bottom: 148px; }
\n    .layout-home .banner-overlay-content { padding-bottom: 80px; }
\n    .has_linked_subject .banner-overlay-content { padding-top: 20px;padding-bottom: 0; }
\n
\n
\n    .has_linked_subject .simplebox-raised .simplebox-column + .simplebox-column {
\n        padding-left: 20px;
\n        padding-right: 20px;
\n    }
\n}
\n
\n@media screen and (min-width: 1024px) {
\n    .banner-overlay-content h1 {
\n        white-space: nowrap;
\n    }
\n}
\n
\n.search-drilldown h3 {
\n    color: #19A29E;
\n}
\n
\n.search-drilldown-column p {
\n    color: #19A29E;
\n}
\n
\n.search-drilldown-column a.active {
\n    background-color: #19A29E;
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
\n        border-top-color: #19A29E;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-drilldown-column {
\n        border-color: #19A29E;
\n    }
\n}
\n
\n\/\* Calendar \*\/
\n.eventCalendar-wrap {
\n    border-color: #bfbfbf;
\n}
\n
\n.eventsCalendar-slider {
\n    background: linear-gradient(#19A29E, #137774);
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
\n    border-color: #19A29E #19A29E #19A29E #137774;
\n    color: #19A29E;
\n}
\n
\n.eventsCalendar-subtitle {
\n    color: #19A29E;
\n}
\n
\n.eventsCalendar-list > li > time {
\n    color: #19A29E;
\n}
\n
\n.eventsCalendar-list > li {
\n    border-bottom-color: #bfbfbf;
\n}
\n
\n\/\* News feeds \*\/
\n.news-area[data-category=\"Blog\"] {
\n    \-\-category-color: #c83f80;
\n}
\n
\n.news-area[data-category=\"News\"] {
\n    \-\-category-color: #19a29e;
\n}
\n
\n.news-area[data-category=\"Events\"] {
\n    \-\-category-color: #077c94;
\n}
\n
\n.news-category-tabs.news-category-tabs {
\n    max-width: 1080px;
\n}
\n
\n.news-category-tabs-section + .fullwidth {
\n    background: #f6f6f6;
\n}
\n
\n.news-feed-item-data {
\n    font-size: 12px;
\n    text-transform: uppercase;
\n}
\n
\n.layout-home .news-feed-item-data {
\n    color: var(\-\-primary);
\n}
\n
\n.news-category-embed-intro {
\n    background: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n.news-category-embed-intro-title,
\n.news-category-embed-intro-title a {
\n    color: #fff;
\n}
\n
\n.page-content .news-category-embed-intro * {
\n    color: #fff;
\n}
\n
\n.news-section {
\n    background: #fff;
\n    box-shadow: 1px 1px 10px #ccc;
\n}
\n
\n.news-slider-link {
\n  color: #19A29E;
\n}
\n
\n.news-slider-title {
\n    color: #19A29E;
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
\n    background-color: var(\-\-category-color, #19A29E);
\n}
\n
\n.swiper-button-prev,
\n.swiper-button-next {
\n    background: none;
\n    border-radius: 50%;
\n    width: 50px;
\n    height: 50px;
\n}
\n
\n.swiper-button-prev::after,
\n.swiper-button-next::after {
\n    content: \'\';
\n    display: block;
\n    position: absolute;
\n    top: 30%;
\n    width: 20px;
\n    height: 20px;
\n    border: solid #c4c4c4;
\n    transform: rotate(45deg);
\n}
\n
\n.swiper-button-prev::after {
\n    border-width: 0 0 3px 3px;
\n    left: 39%;
\n}
\n
\n.swiper-button-next::after {
\n    border-width: 3px 3px 0 0;
\n    right: 39%;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .swiper-button-prev,
\n    .swiper-button-next {
\n        background: #fff;
\n    }
\n}
\n
\n.news-result-date {
\n    background-color: #19A29E;
\n    color: #FFF;
\n}
\n
\n.news-page-content h4,
\n.news-page a {
\n    color: var(\-\-category-color);
\n}
\n
\n.news-sidebar-item a {
\n    color: #333;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .news-category-embed .container {
\n        background: var(\-\-primary);
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .news-page-image {
\n        display: flex;
\n        align-items: center;
\n        height: 500px;
\n        overflow: hidden;
\n    }
\n
\n    .news-page-content .fullwidth {
\n        margin-left: calc(75% - 50vw) !important;
\n    }
\n
\n    .news-category-embed {
\n        background: linear-gradient(var(\-\-primary) 53%, transparent 53%);
\n    }
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .news-result + .news-result:before {
\n        background: linear-gradient(to right, transparent 0, #19A29E 10%, #19A29E 90%, transparent 100%);
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .news-result + .news-result {
\n        border-color: #19A29E;
\n    }
\n}
\n
\n.news-story-navigation a {
\n    color: #19A29E;
\n}
\n
\n.news-story-social {
\n    border-color: #19A29E;
\n}
\n
\n.news-story-share_icon {
\n    color: #19A29E;
\n}
\n
\n.news-story-social-link svg {
\n    background: #19A29E;
\n}
\n
\n.testimonials-slider p {
\n    line-height: 23px;
\n    margin: 1.25rem 0;
\n}
\n
\n.testimonials-slider .row {
\n    max-width: 760px;
\n}
\n
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
\n        background: #1E3B49;
\n        background: linear-gradient(to right, #E6F3C8 0%, #1E3B49 20%, #1E3B49 80%, #E6F3C8 100%);
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
\n    background: #19A29E;
\n    color: #fff;
\n}
\n
\n.bar-icon svg {
\n  fill: #fff;
\n}
\n
\n.bar-text {
\n    color: #222;
\n}
\n
\n.panel-item.has_form {
\n    background-color: #1E3B49;
\n    color: #fff;
\n}
\n
\n.panel-item.has_form .button {
\n    background-color: #fff;
\n    border-color: #1E3B49;
\n    color: #1E3B49;
\n}
\n
\n.panel-item-image:after {
\n    background-image: url(\'\/media\/photos\/content\/panel_overlay.png\');
\n}
\n
\n.panel-item:nth-child(odd) .panel-item-image:after {
\n    background-image: url(\'\/media\/photos\/content\/panel_overlay_right.png\');
\n}
\n
\n.panel-item.has_image .panel-item-text {
\n    color: #fff;
\n    text-align: center;
\n    top: 20%;
\n}
\n
\n.panel-item.has_image:nth-child(even) .panel-item-text {
\n    right: 50%;
\n}
\n
\n.panel-item.has_image:nth-child(odd) .panel-item-text {
\n    left: 50%;
\n}
\n
\n\/\* Search results \*\/
\nbody.layout-course_list .content,
\nbody.layout-course_list2 .content,
\nbody.layout-course_detail2 .content,
\nbody.layout-news2 .content {
\n    margin-top: 0;
\n}
\n
\n.checkout-progress { display: block; }
\n
\n.checkout-progress li a:after {
\n    background-color: #fff;
\n    border-color: #19A29E;
\n    box-shadow: none;
\n}
\n
\n.checkout-progress li + li:before {
\n    border-color: var(\-\-primary);
\n}
\n
\n.checkout-progress ul li a {
\n    color: var(\-\-primary);
\n}
\n
\n.checkout-progress li.curr a:after {
\n    background: #19A29E;
\n}
\n
\n.checkout-progress li:before {
\n    border-color: #19A29E;
\n}
\n
\n.checkout-progress .curr {
\n    font-weight: bold;
\n}
\n
\n.checkout-progress a {
\n    font-size: .75rem;
\n}
\n
\n.course-list-intro,
\n.news-list-intro {
\n    background: #1E3B49;
\n    color: #fff;
\n}
\n
\n.course-list-intro a,
\n.news-list-intro a,
\n.course-list-intro .checkout-progress a {
\n    color: #fff;
\n}
\n
\n.checkout-progress ul {
\n    max-width: 828px;
\n}
\n
\n.course-details-header .checkout-progress ul {
\n    width: width: calc(100% + 67px);
\n}
\n
\n.course-list-intro .checkout-progress li a:after,
\n.layout-course_detail2 .checkout-progress li a:after {
\n    background: #1c3949;
\n    border-color: #fff;
\n    font-size: 1rem;
\n}
\n
\n.layout-course_detail2 .checkout-progress li a:after {
\n    background: var(\-\-category-color);
\n}
\n
\n.layout-course_detail2 .checkout-progress li.curr a:after,
\n.course-list-intro .checkout-progress li.curr a:after {
\n    background: #fff;
\n}
\n
\n.layout-course_detail2 .checkout-progress li:before,
\n.course-list-intro .checkout-progress li:before {
\n    border-color: #fff;
\n}
\n
\n.course-list-header {
\n    border-bottom: none;
\n    margin-bottom: .5rem;
\n}
\n
\n.course-list-display-option:after {
\n    background: #d0d0d0;
\n}
\n
\n.course-list.course-list\-\-grid {
\n    margin-left: -15px;
\n    margin-right: -15px;
\n}
\n
\n.course-list.course-list\-\-grid > div {
\n    padding-left: 15px;
\n    padding-right: 15px;
\n}
\n
\n.course-list\-\-grid .course-widget {
\n    border-color: #bfbfbf;
\n}
\n
\n.course-widget-category {
\n    background: #1E3B49;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-grid .course-widget-price {
\n    background-color: #19A29E;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-list .course-widget-price-original,
\n.course-list\-\-list .course-widget-price-current {
\n    color: #19A29E;
\n}
\n
\n.course-list-grid .course-widget-time_and_date {
\n    border-color: #b7b7b7;
\n}
\n
\n.course-list\-\-list .course-widget-location_and_tags {border-color: #CCC; }
\n
\n.course-list-item.list_only {
\n    box-shadow: 0px 4px 25px rgba(0, 0, 0, .11);
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .checkout-progress ul {
\n        margin-left: -67px;
\n        max-width: 828px;
\n    }
\n}
\n
\n.pagination-wrapper {
\n    text-align: right;
\n}
\n
\n.pagination {
\n    text-align: center;
\n}
\n
\n.pagination-prev a,
\n.pagination-next a {
\n    background: #19A29E;
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
\n    background: #19A29E;
\n    color: #fff;
\n}
\n
\n.booking-form h2 {
\n    border: none;
\n}
\n
\n.booking-required_field-note {
\n    color: #19A29E;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .contact-map-overlay {
\n        background-color: #19A29E;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .contact-map-overlay-content {
\n        background: #19A29E;
\n        background: rgba(111,120,170, .8);
\n    }
\n}
\n
\n.availability-timeslot .highlight {
\n    color: #19A29E;
\n}
\n
\n.availability-timeslot.booked {
\n    border-color: #19A29E;
\n}
\n
\n.availability-timeslot.booked .highlight {
\n    color: #19A29E;
\n}
\n
\n.timeline-swiper .swiper-slide.selected {
\n    background: #1E3B49;
\n    color: #fff;
\n}
\n
\n.timeline-swiper-highlight {
\n    color: #1E3B49;
\n}
\n
\n.timeline-swiper-prev,
\n.timeline-swiper-next {
\n    color: #1E3B49;
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
\n    border-top: 1px solid #1E3B49;/* To ensure the background covers the padding */
\n    background: #1E3B49;
\n    color: #fff;
\n    margin-top: 0;
\n}
\n
\n.footer-logo img {
\n    width: 485px;
\n}
\n
\n.footer h2,
\n.footer-column-title {
\n    color: #19A29E;
\n    font-weight: bold;
\n    margin-bottom: 9px;
\n    text-transform: capitalize;
\n}
\n
\n.footer-stats {
\n    min-height: 0;
\n}
\n
\n.footer-stat {
\n    color: #19A29E;
\n    font-size: 1rem;
\n}
\n
\n.footer-stats-row.footer-stats-row {
\n    padding-bottom: 1.25rem;
\n}
\n
\n.footer-stat h2:after {
\n    border-color: #1E3B49;
\n}
\n
\n.footer-stats-row {
\n    border-bottom: 1px solid var(\-\-primary);
\n    max-width: 1040px;
\n}
\n
\n.footer-columns .container {
\n    max-width: 1080px;
\n}
\n
\n.footer-stats-list {
\n    align-items: center;
\n    flex-wrap: wrap;
\n}
\n
\n.footer-social {
\n    display: none;
\n}
\n
\n.footer-copyright {
\n    border: none;
\n    color: #19A29E;
\n    font-size: 13px;
\n    letter-spacing: -.03em;
\n    padding-top: 6px;
\n}
\n
\n.footer-columns {
\n    border-top: none;
\n    padding-top: 1.6875rem;
\n}
\n
\n.footer-column-content {
\n   font-size: 15px;
\n}
\n
\n.footer-column\-\-contact .footer-column-content {
\n   letter-spacing: -.035em;
\n}
\n
\n.footer-column h4 {
\n   line-height: 1.5;
\n}
\n
\n.footer-column li {
\n   line-height: 1.5;
\n   margin: 0;
\n}
\n
\n.footer-contact-items {
\n    margin-top: 23px;
\n}
\n
\n.footer-contact-items dt:last-of-type {
\n    display: none;
\n}
\n
\n.footer .form-input {
\n    background: none;
\n    border-radius: 0;
\n    color: #fff;
\n    padding: .475em .8em;
\n}
\n
\n.footer .form-input::-webkit-input-placeholder { color: #fff; font-weight: 300; }
\n.footer .form-input::-moz-placeholder          { color: #fff; font-weight: 300; }
\n.footer .form-input:-ms-input-placeholder      { color: #fff; font-weight: 300; }
\n
\n.newsletter-signup-form {
\n    padding-top: 0;
\n}
\n
\n.newsletter-signup-form .button {
\n    border-radius: 0;
\n    font-size: 18px;
\n    font-weight: bold;
\n    padding: .7em;
\n    text-transform: none;
\n}
\n
\n.newsletter-signup-form .form-group {
\n    margin-bottom: 11px;
\n}
\n
\n.newsletter-signup-terms a {
\n    color: var(\-\-primary);
\n}
\n
\n.newsletter-signup-terms-text p {
\n    font-size: inherit;
\n    margin: 0;
\n}
\n
\n.newsletter-signup-terms .form-checkbox-helper {
\n    border: 1px solid #c4c4c4;
\n    border-radius: 0;
\n    background: none;
\n    font-size: 1.25rem;
\n    margin-right: .5em
\n}
\n
\n.footer .newsletter-signup-terms .form-checkbox-helper:after {
\n    color: #c4c4c4;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .footer-columns {
\n        padding-top: 0;
\n    }
\n
\n    .footer-column.has_sublist {
\n        border-color: rgba(255, 255, 255, .1);
\n    }
\n
\n    .footer .footer-column-content {
\n        padding-left: 0;
\n        padding-right: 0;
\n    }
\n
\n    .row > .footer-column.has_sublist {
\n        margin-left: 20px;
\n        margin-right: 20px;
\n        padding-left: 0;
\n        padding-right: 0;
\n    }
\n
\n    .footer-stats {
\n        margin-left: 20px;
\n        margin-right: 20px;
\n        padding-top: 0;
\n        padding-bottom: 0;
\n    }
\n
\n    .footer-stat.footer-stat {
\n        margin-top: 1rem;
\n        text-align: left;
\n        width: 100%;
\n    }
\n
\n    .footer-stat .button {
\n        border: none;
\n        font-size: 13px;
\n        line-height: 40px;
\n        min-height: 40px;
\n        min-width: 150px;
\n        padding: 0 .5em;
\n    }
\n
\n    .footer-stat [src\*=\"app-store\"] {
\n        float: right;
\n        height: 40px;
\n        padding: 0 20px;
\n    }
\n
\n    .footer-column-title {
\n        font-size: 18px;
\n        line-height: 25px;
\n        padding:  1rem 0 .5rem;
\n    }
\n
\n    .footer-column.has_sublist .footer-column-title:before {
\n        content: \'\';
\n        border: solid #fff;
\n        border-width: 0 1px 1px 0;
\n        float: right;
\n        width: 12px;
\n        height: 12px;
\n        position: relative;
\n        right: 17px;
\n        top: 4px;
\n        transform: rotate(45deg);
\n    }
\n
\n    .footer-column.has_sublist .footer-column-title.expanded:before {
\n        transform: rotate(225deg);
\n    }
\n
\n    .footer-social-icons {
\n        display: flex;
\n        align-items: center;
\n    }
\n
\n    .footer-social-icons h5 {
\n        margin: 1.25rem 0;
\n    }
\n
\n    .footer-social-icons p {
\n        margin-left: auto;
\n    }
\n
\n    .footer-social-icons img {
\n        width: 42px;
\n        height: 42px;
\n        margin-left: .5rem;
\n    }
\n
\n    .footer-copyright {
\n        padding: 2.45rem 0;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .footer-stats {
\n        padding-top: 4rem;
\n    }
\n
\n    .footer-stat {
\n        font-size: 1rem;
\n        width: auto;
\n        margin: 0 0 0 auto;
\n    }
\n
\n    .footer-stat:first-child {
\n        margin-left: 0;
\n    }
\n
\n    .footer-stat .button {
\n        min-width: 200px;
\n        padding: 1rem;
\n    }
\n
\n    .footer-social-icons h5 {
\n        margin: 0 0 .5rem;
\n    }
\n
\n    .footer-social-icons p {
\n        margin-bottom: 1rem;
\n    }
\n
\n    .footer-social-icons a + a img {
\n        margin-left: .8rem;
\n    }
\n}
\n
\n@media screen and (min-width: 1024px) {
\n    .footer-column:nth-child(1) {width: 26.1%}
\n    .footer-column:nth-child(2) {width: 19.1%;}
\n    .footer-column:nth-child(3) {width: 30.2%;}
\n    .footer-column:nth-child(4) {width: 24.6%}
\n}
\n
\n
\n\/\* Dropdown filters \*\/
\n.search-filter-total {
\n    color: #19A29E;
\n}
\n
\n.search-filters :checked ~ .form-checkbox-helper,
\n.search-filters :checked ~ .form-radio-helper,
\n.search-filters :checked ~ .form-checkbox-label,
\n.search-filters :checked ~ .form-radio-label {
\n    border-color: #19A29E;
\n    color: #19A29E;
\n}
\n
\n.search-filters :checked + .form-radio-helper:after {
\n    background-color: #19A29E;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-filter-dropdown.filter-active > button,
\n    .search-filters-clear {
\n        color: #19A29E;
\n    }
\n
\n    .checkout-heading {
\n        background-color: #19A29E;
\n        border-radius: 0;
\n        color: #fff;
\n    }
\n}
\n
\/\* Login \*\/
\n.login-form-container.login-form-container .modal-header {
\n    background-color: #fff;
\n    color: #19A29E;
\n}
\n
\n.login-form-container.login-form-container .nav-tabs > li > a {
\n    color: #303030;
\n    font-weight: bold;
\n    text-transform: uppercase;
\n}
\n
\n.signup-text a,
\n.login-form-container.login-form-container .modal-footer a,
\n.login-form-container.login-form-container .nav-tabs > li.active > a {
\n    color: var(\-\-primary);
\n}
\n
\n.signup-text a:hover,
\n.login-form-container .modal-footer a:hover,
\n.login-form-container .nav-tabs > li > a:hover {
\n    color: var(\-\-primary-hover);
\n}
\n
\n.login-buttons .btn {
\n    text-transform: uppercase;
\n}
\n
\n
\n
\n\/\* Misc \*\/
\n.contact\-\-left .ui-tabs-nav .ui-tabs-active a {
\n    background: #19A29E;
\n    border-color:#19A29E;
\n    color: #fff;
\n}
\n
\n.checkout-right-sect .btn-close:hover {
\n    color: #1E3B49;
\n    border-color: #1E3B49;
\n}
\n
\n.checkout-right-sect .sub-total,
\n.prepay-box li.total  {
\n    color: #19A29E;
\n}
\n
\n.checkout-heading .fa {
\n    display: none;
\n}
\n
\n.checkout-form .theme-form-content {
\n    border: none;
\n}
\n
\n.checkout-form .theme-form-inner-content {
\n    margin: 0 0 80px
\n}
\n
\n.delegate_box {
\n    border: 0 !important;/* todo: remove absolute classes from this section and style within template CSS. */
\n    padding: 0 !important;
\n}
\n
\n.checkout-privacy-header {
\n    color: var(\-\-primary);
\n}
\n
\n.privacy-content {
\n    border: none;
\n}
\n
\n.privacy-inner-content {
\n    font-size: 14px;
\n    margin: 0;
\n}
\n
\n.terms-txt {
\n    font-size: 12px;
\n    letter-spacing: -0.025em;
\n    line-height: 1.115;
\n}
\n
\n.terms-txt .form-row {
\n    margin: -5px 0 10px;
\n}
\n
\n.terms-txt .form-row > div {
\n    padding: 0 5px;
\n}
\n
\na.item-summary-head {
\n    color: #1E3B49;
\n}
\n
\n.search-package-available h2 {
\n    color: #4f4e4f;
\n}
\n
\n.search-package-available .available-text  h4 {
\n    border-color: #eee;
\n    color: #19A29E;
\n}
\n
\n.search-package-available .show-more {
\n    background: #fff;
\n    border: 1px solid #1E3B49;
\n    color: #1E3B49;
\n}
\n
\n.prepay-box h6 {
\n    color: #19A29E;
\n}
\n
\n.custom-calendar .booking-date-button {
\n    background-color: #19A29E;
\n    color: #fff;
\n}
\n
\n.custom-calendar .booking-date-button:hover {
\n    background-color: #1E3B49;
\n}
\n
\n.custom-calendar button.booking-date-button.active {
\n    background-color: #fff;
\n    color: #19A29E;
\n}
\n
\n.course-activity-alert,
\n.details-wrap .left-place {
\n    color: #F75A5F;
\n}
\n
\n.number-of-people-viewing {
\n    color: #19A29E;
\n}
\n
\n.search-calendar-course-image .fa {
\n    background-color: #19A29E;
\n    color: #fff;
\n}
\n
\n.custom-calendar tbody td.active,
\n.custom-calendar tbody td.active:hover {
\n    background-color: #fff;
\n    color: #19A29E;
\n}
\n
\n.custom-calendar tbody tr:first-child td {
\n    color: #222;
\n}
\n
\n.package-offers-wrap h2 {
\n    color: #19A29E;
\n    border-color: #c5cecd;
\n}
\n
\n.package-offers-wrap h3 {
\n    color: #19A29E;
\n}
\n
\n.package-offers-wrap .summary-wrap .more,
\n.classes-details-wrap .details-wrap li:first-child {
\n    color: #19A29E;
\n}
\n
\n.classes-details-wrap .details-wrap li:first-child {
\n  background-color: #19A29E;
\n}
\n
\n.details-wrap .remove-booking,
\n.details-wrap .wishlist.remove{
\n    color: #19A29E;
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
\n    color: #19A29E;
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
\n    color: #19A29E;
\n}
\n
\n.details-wrap:hover li:first-child {
\n    background-color: #19A29E;
\n}
\n
\n.details-wrap:hover .sidelines::before,
\n.details-wrap:hover .sidelines::after,
\n.details-wrap:hover .price-wrap {
\n    border-color:#19A29E;
\n}
\n
\n
\n\/\* course results booked \*\/
\n.details-wrap.booked {
\n    border-color:#1E3B49;
\n    background-color: #f3f3f3;
\n}
\n
\n.details-wrap.booked .time,
\n.details-wrap.booked .price,
\n.details-wrap.booked .fa-book {
\n    color: #1E3B49;
\n}
\n.details-wrap.booked li:first-child {
\n    background-color: #1E3B49;
\n}
\n
\n.details-wrap.booked .sidelines::before,
\n.details-wrap.booked .sidelines::after,
\n.details-wrap.booked .price-wrap {
\n    border-color:#1E3B49;
\n}
\n
\n.classes-details-wrap .alert-wrap {
\n    background-color: #1E3B49;
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
\n    color: #19A29E;
\n}
\n
\n.custom-calendar .booking-date-button.already_booked {
\n    background-color: #1E3B49;
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
\nbody > div > img {
\n  display: block;
\n}
\n
\n.submit-expand {
\n    background: none;
\n    border: none;
\n}
\n
\n.course-selector-form .form-select-plain select {
\n   border-color: #c4c4c4;
\n   border-radius: 5px;
\n}
\n
\n.course-selector-form .button,
\n.course-details-menu .button,
\n.course-details-brochure-modal .button,
\n.button.checkout-complete_booking{
\n    font-size: 14px;
\n    text-transform: uppercase;
\n    font-family: Raleway, sans-serif;
\n}
\n
\n.course-selector-form .button:after,
\n.course-details-menu .button:after,
\n.course-details-brochure-modal .button:after,
\n.button.checkout-complete_booking:after {
\n    content: none;
\n}
\n
\n.course-details-menu {
\n    box-shadow: 0px 4px 25px rgba(0, 0, 0, .11);
\n}
\n
\n.course-details-menu select {
\n    border: 1px solid #C4C4C4;
\n    border-radius: 4px;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .simplebox-course-columns .simplebox-columns {
\n        width: 100vw;
\n        margin-left: calc(50% - 50vw);
\n    }
\n
\n    .simplebox-course-columns .simplebox-content {
\n        padding-left: 20px;
\n        padding-right: 20px;
\n    }
\n
\n    .simplebox-course-columns .simplebox-content {
\n        padding-top: 2rem;
\n        padding-bottom: 2rem;
\n    }
\n
\n    .simplebox-course-columns .simplebox-content > :first-child { margin-top: 0;}
\n    .simplebox-course-columns .simplebox-content > :last-child { margin-bottom: 0;}
\n
\n    .simplebox-course-columns .simplebox-column-1 {
\n        background: var(\-\-category-color, #19A29E);
\n        color: #fff;
\n    }
\n
\n    .simplebox-course-columns .simplebox-column-2 {
\n        background: var(\-\-success, #1E3B49);
\n        color: #fff;
\n    }
\n
\n    .course-selector-form .form-select-plain {
\n        margin: 1.8rem 0;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .simplebox-course-columns {
\n        background: linear-gradient(to right, var(\-\-category-color, #19A29E) 50%, var(\-\-success, #1E3B49) 50%);
\n        color: #fff;
\n    }
\n
\n    .simplebox-course-columns .simplebox-columns {
\n        max-width: 1298px;
\n    }
\n
\n    .simplebox-course-columns .simplebox-column {
\n        padding: 70px 125px 60px 20px;
\n        margin: 0;
\n    }
\n
\n    .simplebox-course-columns .simplebox-column:nth-child(even) {
\n        padding-left: 117px;
\n        padding-right: 20px;
\n    }
\n
\n    .course-selector-form .form-select-plain {
\n        margin: 1rem 0 1.5rem;
\n    }
\n}
\n
\n.get_in_touch .simplebox-columns { max-width: 1200px; }
\n.get_in_touch .simplebox-column-1 img { display: block; }
\n.get_in_touch h2 { margin-bottom: 10px; }
\n.get_in_touch h2 { margin-bottom: .222em; }
\n.get_in_touch p { margin: .5rem 0 1.3rem; }
\n.get_in_touch p:last-child { margin-bottom: 2rem; }
\n
\n@media screen and (max-width: 479px) {
\n    .get_in_touch {
\n        min-height: 360px;
\n        padding-top: 2.5rem;
\n    }
\n
\n    .layout-home .get_in_touch {
\n        min-height: 410px;
\n        padding-top: 0;
\n    }
\n
\n    .get_in_touch .simplebox-column-1 img:only-child {
\n        width: 245px!important;
\n    }
\n
\n    .get_in_touch .simplebox-column-1 {
\n        position: absolute;
\n        bottom: 0;
\n        right: 17%
\n    }
\n
\n    .get_in_touch .simplebox-column-1 {
\n        padding: 0 !important;\/\* The rule this overwrites needs to be made less specific \*\/
\n    }
\n
\n    .get_in_touch .simplebox-column-2{
\n        z-index: 2;
\n    }
\n
\n    .get_in_touch .simplebox-column-2 .simplebox-content {
\n        padding-left: 90px;
\n    }
\n
\n    .get_in_touch .button {
\n        display: block;
\n        margin-left: auto;
\n        max-width: 170px;
\n    }
\n}
\n
\n@media screen and (min-width: 480px) {
\n
\n    .get_in_touch .button:first-child {
\n        margin-right: 1.5rem;
\n    }
\n
\n    .get_in_touch.get_in_touch.get_in_touch .simplebox-column-1 { width: 42% !important; }
\n    .get_in_touch.get_in_touch.get_in_touch .simplebox-column-2 { width: 58% !important; }
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .get_in_touch .button {
\n        margin-bottom: 1rem;
\n    }
\n
\n    .get_in_touch p {
\n        font-size: 16px;
\n        line-height: 1.375;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .get_in_touch p {
\n        font-size: 20px;
\n        line-height: 1.5;
\n    }
\n}
\n
\n
\n@media screen and (max-width: 767px) {
\n    .simplebox-contact .button {
\n        display: block;
\n        margin-bottom: 1rem;
\n   }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .simplebox-contact .simplebox-column-1 {margin:0;width:55%;}
\n    .simplebox-contact .simplebox-column-2 {margin:0;width:45%;}
\n
\n    .simplebox-contact .simplebox-column-2 .simplebox-content {
\n        padding-bottom: 135px;
\n   }
\n
\n    .simplebox-contact .button {
\n        margin-right: 1.5rem;
\n   }
\n}
\n
\n
'
  WHERE
  `stub` = '49'
;;

INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`)
VALUES (
  'newsletter_signup_terms',
  'Subscription terms text',
  '', '', '', '', '',
  'both',
  'Terms and conditions text to appear next to a checkbox in the newsletter subscription form. Leave blank to remove the checkbox.',
  'wysiwyg',
  'Forms',
  '0'
);;
