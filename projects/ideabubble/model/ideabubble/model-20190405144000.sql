/*
ts:2019-01-24 15:00:00
*/

/* Adding new Ideabubble theme settings see IBOC-1046 for changes */

/* Add the 'ideabubble2' (New Ideabubble) theme, if it does not already exist */
DELIMITER  ;;
INSERT INTO
  `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT 'ideabubble2', 'ideabubble2', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_themes`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_themes` WHERE `stub` = 'ideabubble2')
    LIMIT 1
;;


/* Add the 'ideabubble2' theme styles */
UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = '@import url(\'https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i,900\');
\n@import url(\'https://fonts.googleapis.com/css?family=Quicksand:300,400,500,700\');
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
\n .page-content,
\n.banner-overlay-content {
\n    font-size: 1.125rem;
\n    font-family: Quicksand, Roboto, Helvetica, Arial, sans-serif;
\n}
\n
\nh1, h2, .page-content h4, .button {
\n    font-family: Quicksand, Roboto, Helvetica, Arial, sans-serif;
\n}
\n
\n.banner-overlay-content h4 {
\n    font-family: Roboto, Helvetica, Arial, sans-serif;
\n    font-weight: 400;
\n}
\n .page-content h1, .banner-overlay-content h1 { font-weight: 500; color: #1c8da1; border: none; }\n .page-content h2, .banner-overlay-content h2 { font-weight: 700; color: #00385d; }\n .page-content h3, .banner-overlay-content h3 { font-weight: 700; color: #1c8da1; margin: .5em 0; }\n .page-content h4, .banner-overlay-content h4 { font-weight: 400; margin: 0 0 .5em; }\n .page-content p,  .banner-overlay-content p  { font-size: inherit;  }
\n .page-content h4 { color: #1c8da1; }
\n
\n@media screen and (max-width: 767px) {
\n    .content > .page-content {
\n        padding: 0 1em;
\n    }
\n
\n    .page-content,    .banner-overlay-content    { font-size: 1rem; }
\n
\n    .page-content h1, .banner-overlay-content h1 { font-size: 1.625rem;  line-height: 1;  margin: .25em 0; }
\n    .page-content h2, .banner-overlay-content h2 { font-size: 1.375rem; line-height: 1.4; }
\n    .page-content h3, .banner-overlay-content h3 { font-size: 1.125rem;}
\n    .page-content h4, .banner-overlay-content h4 { font-size: 1rem;  font-weight: 400; line-height: 1.25; }
\n    .page-content h5, .banner-overlay-content h5 { font-size: .9rem; font-weight: bold; }
\n    .page-content h6, .banner-overlay-content h6 { font-size: .8rem; font-weight: bold; }
\n    .page-content p,  .banner-overlay-content p  { font-size: inherit; line-height: 1.25; }
\n
\n    .banner-overlay-content .button,
\n    .page-content .button {
\n        border-radius: 2px;
\n        font-size: 1rem;
\n        min-width: 150px;
\n        padding: .67em;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .page-content,    .banner-overlay-content    { font-size: 1.125rem; }
\n
\n    .page-content h1, .banner-overlay-content h1 { font-size: 3rem;     line-height: 1.1; }
\n    .page-content h2, .banner-overlay-content h2 { font-size: 2.25rem; line-height: 1.4; }
\n    .page-content h3, .banner-overlay-content h3 { font-size: 1.875rem; }
\n    .page-content h4, .banner-overlay-content h4 { font-size: 1.5rem; line-height: 1.25; }
\n    .page-content h5, .banner-overlay-content h5 { font-size: 1.375rem; }
\n    .page-content h6, .banner-overlay-content h6 { font-size: 1.25rem;  }
\n    .page-content p,  .banner-overlay-content p  { line-height: 1.5; }
\n
\n    .banner-overlay-content .button,
\n    .page-content .button {
\n        border-radius: 5px;
\n        min-width: 180px;
\n    }
\n}
\n
\n.table thead {
\n    background: #00375e;
\n    color: #fff;
\n}
\n
\n.badge {
\n    background: #00375e;
\n    color: #fff;
\n}
\n
\n.popup-header {
\n    background: #00375e;
\n    color: #fff;
\n}
\n
\n.button.course-banner-button.cl_bg {
\n    background-color: #00375e;
\n}
\n
\n.course-widget-links .button.button\-\-cl_remove {
\n    background-color: #f60000;
\n}
\n
\n/* Autotimetables */
\n.autotimetable tbody tr:nth-child(even) {
\n    background: #f9f9f9;
\n}
\n
\n.autotimetable tbody tr td a {
\n    color: #244683;
\n}
\n
\n.autotimetable tbody a:hover {
\n    color: #00375e;
\n}
\n
\n.autotimetable .new_date {
\n    border-color: #00375e;
\n}
\n
\n.autotimetable .new_date td:nth-child(1) {
\n    background-color: #00375e;
\n    color: #fff;
\n}
\n
\n:checked + .seating-selector-checkbox-helper:after {
\n    color: #00375e;
\n}
\n
\n.seating-selector-option-radio:checked + .button {
\n    background: #00375e;
\n    color: #fff;
\n}
\n
\n.seating-selector-option-hover {
\n    background-color: #00375e;
\n    color: #fff;
\n}
\n
\n/* Forms */
\n.formrt [type=\"text\"],
\n.formrt [type=\"email\"],
\n.formrt [type=\"password\"],
\n.formrt select,
\n.formrt textarea {
\n    background: #fff;
\n    border: 1px solid #efefef;
\n    border-radius: 2px;
\n}
\n
\n.formrt ::-webkit-input-placeholder { font-weight: 300; }
\n.formrt ::-moz-placeholder          { font-weight: 300; }
\n.formrt :-ms-input-placeholder      { font-weight: 300; }
\n
\n#Contact-Us {
\n     margin-bottom: auto;
\n}
\n
\n#Contact-Us ul li label {
\n     float: none;
\n     width: unset;
\n     display: block;
\n }
\n
\n.input_group-icon {
\n    background: #00375e;
\n    color: #fff;
\n}
\n
\n.select:after {
\n    border-top-color: #00375e;
\n}
\n
\n.form-select:before {
\n    background-image: -webkit-linear-gradient(left, transparent 0, transparent calc(100% - 2.75em), #00375e calc(100% - 2.75em), #00375e 100%);
\n    background-image: linear-gradient(to right, transparent 0, transparent calc(100% - 2.75em), #00375e calc(100% - 2.75em), #00375e 100%);
\n}
\n
\n.button,
\n.formrt button,
\n.formrt [type=\"submit\"],
\n.formrt [type=\"reset\"] {
\n    background-color: #1c8da1;
\n    color: #fff;
\n}
\n
\n.button.inverse {
\n    background-color: #fff;
\n    color: #1c8da1;
\n}
\n
\n.button\-\-continue {
\n    background-color: #00375e;
\n    border-color: transparent;
\n    color: #fff;
\n}
\n
\n.button\-\-continue.inverse {
\n    background-color: #fff;
\n    border: 1px solid #00375e;
\n    color: #00375e;
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
\n    background-color: #00375e;
\n}
\n
\n.button\-\-book.inverse {
\n    background: #FFF;
\n    border-color: #00375e;
\n    color: #00375e;
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
\n    background: #dcde34;
\n    color: #1c8da1;
\n}
\n
\n.button\-\-send.inverse {
\n    background: #1c8da1;
\n    border-color: #dcde34;
\n    color: #dcde34;
\n}
\n
\n.button\-\-enquire {
\n    background: #00375e;
\n    color: #fff;
\n}
\n
\n.header-action:nth-child(odd) .button {
\n    background: #008499;
\n    border-radius: 0;
\n}
\n
\n.header-action:nth-child(odd) .button.active {
\n    background: #fff;
\n}
\n
\n.header-action:nth-child(even) .button {
\n    background: none;
\n    border-color: transparent;
\n    color: #222;
\n    font-weight: 500;
\n}
\n
\n.header-action:nth-child(even) .button.active {
\n    color: #1c8da1;
\n}
\n
\n.formErrorContent,
\n.formErrorArrow div {
\n    background: #1c8da1;
\n}
\n
\n.survey-question-block {
\n    color: #1c8da1;
\n}
\n
\n.survey-input[type=\"radio\"]:checked + .survey-input-helper {
\n    background: #dcde34;
\n    border-color: #dcde34;
\n}
\n
\n.survey-input[type=\"radio\"]:checked + .survey-input-helper:after {
\n    border-color: #1c8da1;
\n}
\n
\n/* Alerts */
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
\n.popup_box.alert-add     { border-color: #00375e; }
\n.popup_box.alert-remove  { border-color: #b4b4b4; }
\n
\n.popup_box .alert-icon [fill]   {   fill: #00375e; }
\n.popup_box .alert-icon [stroke] { stroke: #00375e; }
\n
\n
\n/* Header */
\n.header,
\n.mobile-breadcrumbs,
\n.dropdown-menu-header {
\n    background: #ffffff;
\n    color: #1c8da1;
\n}
\n
\n.header-logo img {
\n    height: 35px;
\n    max-height: none;
\n}
\n
\n.header-actions { flex: none;  margin-left: 1em; }
\n.header-left { flex: 1; display: flex; }
\n.header-logo { margin-right: auto; padding-right: 0; }
\n
\n.header-left .header-item > a,
\n.header-action > a {
\n    padding: .45em 2.2em;
\n}
\n
\n.header-left.header-item {
\n    display: flex;
\n}
\n
\n.header-left .header-item:not(.header-logo) {
\n   margin: 0px auto;
\n}
\n
\n.header-item .header-item.header-menu-section {
\n    margin: 0px 5px;
\n}
\n
\n.header-left .header-item > a {
\n    color: #222;
\n    font-family: Quicksand, Roboto, Helvetica, Arial, sans-serif;
\n    font-weight: 500;
\n}
\n
\n.header-action {
\n    padding: 0;
\n}
\n
\n.header-item.header-menu-section .header-menu {
\n    border: 1px solid #e9e9e9;
\n    margin-top: 0px;
\n}
\n
\n.header-menu .level_2 a:hover,
\n.header-menu .level_2:hover > a {
\n    color: #198ebe;
\n}
\n
\n.header-item > a:not(.button) {
\n    color: #222;
\n}
\n
\n.header-item > a.active {
\n    color: #1c8da1;
\n}
\n
\n.header-menu-section > a {
\n    border: none;
\n    padding: 1.847em 2em;
\n}
\n
\na.header-menu-expand.expanded {
\n    color: #4f8085;
\n}
\n
\n.header-menu-section > a:after {
\n    border-width: .5em .333em 0;
\n    border-top-color: initial;
\n}
\n
\n.header-menu .level_1 > a,
\n.mobile-menu .level_1 > a,
\n.mobile-menu .level_1 > button,
\n.mobile-menu-level3-section .mobile-menu-list > a,
\n.mobile-menu-toggle {
\n   color: #323232;
\n   font-family: Quicksand, Roboto, Helvetica, Arial, sans-serif;
\n   text-transform: none;
\n}
\n
\n.header-menu .level_2 a:before {
\n    border-left-color: #00375e;
\n}
\n
\n.header-menu .level_2 a:hover:before,
\n.header-menu .level_2:hover > a:hover {
\n    border-left-color: #198ebe;
\n}
\n
\n.header-menu .level_3 {
\n    border-bottom-color: #00375e;
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
\n@media screen and (min-width: 768px) and (max-width: 1077px) {
\n    body.has_sticky_header {
\n        padding-top: 3.1em;
\n    }
\n
\n    .header-left .header-item > a,
\n    .header-action > a {
\n        font-size: .9rem;
\n    }
\n
\n    .header-left .header-item > a { padding: 1.167em .45em; }
\n    .header-action > a            { padding: 1.167em 1.5em; }
\n}
\n
\n@media screen and (min-width: 1078px) {
\n    .header-actions {
\n        margin-left: 1.25em;
\n    }
\n
\n    .header-left .header-item > a,
\n    .header-action > a {
\n        font-size: 1.125rem;
\n    }
\n
\n    .header-left .header-item > a { padding: 1.167em .5em; }
\n    .header-action > a            { padding: 1.167em 2.2em; }
\n}
\n
\n/* Quick Contact */
\n@media screen and (max-width: 767px) {
\n    .quick_contact {
\n        background: #1c8da1;
\n        border-top: 1px solid #7888b6;
\n    }
\n
\n    .quick_contact .quick_contact-item {
\n        border: none;
\n        position: relative;
\n    }
\n
\n    .quick_contact-item > a {
\n        color: #fff;
\n    }
\n
\n    .quick_contact-item + .quick_contact-item a:before {
\n        content: \'\';
\n        border-left: 1px solid #7888b6;
\n        position: absolute;
\n        top: .25em;
\n        bottom: .25em;
\n        left: 0;
\n    }
\n
\n    .quick_contact-item > a.active,
\n    .quick_contact-item > a:hover,
\n    .quick_contact-item > a:active {
\n        color: #00375e;
\n    }
\n
\n    .quick_contact-item-icon {
\n        font-size: 1.25rem;
\n    }
\n}
\n
\n/* Sidebar */
\n.sidebar-section > h2 {
\n    background: #00375e;
\n    color: #fff;
\n}
\n
\n.sidebar-news-list {
\n     font-family: Quicksand, Roboto, Helvetica, Arial, sans-serif;
\n }
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
\n/* Page content */\n .page-content li:before {
\n    content: \'\f105\a0\';
\n    color: #1c8da1;
\n}
\n .page-content a:not([class]),\n .page-content .button\-\-link {
\n    color: #198ebe;
\n}
\n .page-content a:not([class]):visited {
\n    color: #551a8b;
\n}
\n .page-content ul > li:before {
\n    content: \'\\4e\\a0\';
\n    font-family: \'ElegantIcons\';
\n}
\n .page-content hr {
\n    border-color: #bfbfbf;
\n}
\n .page-content .shadow {
\n    box-shadow: 0px 1.125rem 1.6875rem rgba(0, 0, 0, .18);
\n}
\n
\n.image-hover,
\n:hover > .image-unhover {display: none;}
\n:hover > .image-hover {display: inline;}
\n
\n.simplebox-title {
\n    text-align: left;
\n    margin-bottom: 0;
\n}
\n
\n.simplebox-content ul {
\n    padding-left: 30px;
\n}
\n
\n.simplebox.gray,
\n.simplebox.darkblue,
\n.simplebox.green {
\n    overflow: auto;
\n}
\n
\n.simplebox.gray:before,
\n.simplebox.darkblue:before,
\n.simplebox.green:before {
\n    content: \'\';
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n    z-index: -1;
\n}
\n
\n.simplebox.gray:before     { background: #f4f4f4; }
\n.simplebox.darkblue:before { background: #00385d; }
\n
\n.simplebox.darkblue {
\n    color: #fff;
\n}
\n
\n.simplebox.darkblue h1,
\n.simplebox.darkblue h2,
\n.simplebox.darkblue h3,
\n.simplebox.darkblue h4,
\n.simplebox.darkblue h5,
\n.simplebox.darkblue h6,
\n.simplebox.darkblue p {
\n    color: inherit;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .simplebox.green .simplebox-column:last-child,
\n    .simplebox.green .simplebox-content {
\n        position: relative;
\n    }
\n
\n    .simplebox.green .simplebox-column:last-child:before {
\n        content: \'\';
\n        background: url(\'/shared_media/courseco/media/photos/content/footer_curve.svg\'), linear-gradient(transparent 0, transparent 30%, #dbde34 30%);
\n        background-position-x: -2em;
\n        background-repeat: no-repeat;
\n        background-size: 100%;
\n        background-size: calc(100% + 4em);
\n        display: block;
\n        position: absolute;
\n        top: 0;
\n        right: -1.2em;
\n        bottom: 0;
\n        left: -1.2em;
\n    }
\n
\n    /* If the user has manually changed the indentation, undo it on mobile */
\n    .page-content h1[style*=\"margin-left\"],
\n    .page-content h2[style*=\"margin-left\"],
\n    .page-content h3[style*=\"margin-left\"],
\n    .page-content h4[style*=\"margin-left\"],
\n    .page-content h5[style*=\"margin-left\"],
\n    .page-content h6[style*=\"margin-left\"],
\n    .page-content p[style*=\"margin-left\"],
\n    .page-content ul[style*=\"margin-left\"],
\n    .page-content .formrt[style*=\"margin-left\"] {
\n        margin-left: 0 !important;
\n    }
\n
\n    .page-content .quote {
\n        margin: 0;
\n    }
\n
\n    .page-content .quote img {
\n        width: 35px;
\n    }
\n
\n    .quote ~ p {
\n        padding-left: 1.5rem
\n    }
\n
\n    .quote + p {
\n        margin-top:.375rem;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .simplebox.green:before    {
\n        background: linear-gradient(to right, transparent 0, transparent 200px, #dcde34 200px), url(\'/shared_media/courseco/media/photos/content/right_curve.svg\');
\n        background-size: cover;
\n        left: 50%;
\n    }
\n}
\n
\n/* Banner search */
\n.banner-search-title {
\n    background: #2e4076;
\n    color: #fff;
\n}
\n
\n.banner-search .fa {
\n    color: #00375e;
\n}
\n
\n.banner-search-title .fa {
\n    color: #fff;
\n}
\n
\n.banner-search form {
\n    background: #00375e;
\n}
\n
\n.previous_search_text {
\n    color: #fff;
\n}
\n
\n.banner-overlay-content form {
\n    font-size: 1rem;
\n    margin: 1em auto;
\n    max-width: 1070px;
\n}
\n
\n.banner-overlay-content form li {
\n    float: left;
\n    margin-top: .5em;
\n    padding: .95em;
\n    width: 100%;
\n}
\n
\n.banner-overlay-content form li:nth-of-type(odd):last-child {
\n    text-align: center;
\n}
\n
\n.banner-overlay-content form li input {
\n    background: #fff;
\n    border: none;
\n    font-family: Roboto, Helevtica, Arial, sans-serif;
\n    padding: 1.1em 1.25em;
\n    width: 100%;
\n}
\n
\n.banner-image {
\n    background-color: #f0f0f0;
\n    background-repeat: no-repeat;
\n}
\n
\n.layout-landing_page .banner-image:after {
\n    content: \'\';
\n    background: rgba(43, 76, 143, 0.75);
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n}
\n
\n.layout-landing_page .banner-overlay,
\n.layout-landing_page .banner-slide .banner-overlay .row {
\n    background: none;
\n}
\n
\n.banner-overlay-content .button,\n .page-content .button {
\n	margin-top: 10px;
\n    background: #008499;
\n	color: white;
\n}
\n
\n.banner-overlay-content .button:nth-last-child(even),\n .page-content .button:nth-last-child(even) {
\n    background: #00375e;
\n    color: #fff;
\n}
\n
\n.banner-overlay-content .button.inverse,\n .page-content .button.inverse {
\n    background-color: #fff;
\n    color: #1c8da1;
\n}
\n
\n@media screen and (max-width: 599px) {
\n    .banner-overlay-content .button,
\n    .page-content .button {
\n        margin-bottom: .5em;
\n    }
\n}
\n
\n@media screen and (max-width: 359px) {
\n    .banner-overlay-content .button,
\n    .page-content .button {
\n        width: 100%
\n    }
\n}
\n
\n@media screen and (min-width: 360px) and (max-width: 599px) {
\n    .banner-overlay-content .button,
\n    .page-content .button {
\n        width: calc(50% - .5em - 4px);
\n    }
\n
\n    .banner-overlay-content .button:only-child {
\n        display: block;
\n        margin-left: auto;
\n        margin-right: auto;
\n    }
\n
\n    .page-content .button:only-child {
\n        width: 100%;
\n    }
\n
\n    .banner-overlay-content .button + .button,
\n    .page-content .button + .button {
\n        margin-left: 1em;
\n    }
\n}
\n
\n@media screen and (min-width: 600px) {
\n    .banner-overlay-content .button + .button,
\n    .page-content .button + .button {
\n        margin-left: 1.5em;
\n    }
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
\n
\n    .banner-section\-\-has_mobile_slides {
\n        background: none;
\n    }
\n
\n    .banner-overlay-content form li .button {
\n        padding: 1.1em 1.25em;
\n        width: 100%;
\n    }
\n}
\n
\n.banner-section {
\n    z-index: 1;
\n}
\n
\n
\n
\n/* Extend the background of the first block in the page to go behind the banner */
\n.has_banner .content .page-content > .simplebox:first-child {
\n    margin-top: -260px;
\n    padding-top: 260px;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .banner-image {
\n        height: 650px;
\n    }
\n
\n    .layout-landing_page .banner-image {
\n        height: 500px;
\n    }
\n
\n    body:not(.layout-landing_page) .banner-image {
\n        background-position: top center;
\n        background-size: auto 100%;
\n    }
\n
\n    .banner-overlay-content h1 {
\n        max-width: 10.5rem;
\n    }
\n
\n    .banner-overlay-content h2,
\n    .banner-overlay-content h3,
\n    .banner-overlay-content h4 {
\n        max-width: 11.75rem;
\n    }
\n
\n    .layout-landing_page .banner-overlay-content h1,
\n    .layout-landing_page .banner-overlay-content h2,
\n    .layout-landing_page .banner-overlay-content h3,
\n    .layout-landing_page .banner-overlay-content h4 {
\n         max-width: none;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n
\n    .layout-landing_page .banner-image {
\n        height: 636px;
\n    }
\n
\n    .banner-overlay-content {
\n        max-width: 37.5rem;
\n    }
\n
\n    .banner-overlay .row { background-repeat: no-repeat; }
\n    .swiper-slide .banner-image { background-position: center; }
\n
\n    .swiper-slide .banner-overlay {
\n        background-position: top center;
\n    }
\n
\n    .banner-slide\-\-left .banner-overlay .row {
\n        /*background-image: url(\'/shared_media/courseco/media/photos/content/banner_overlay_left.png\');*/
\n        background-position-x: left;
\n    }
\n
\n    .banner-slide\-\-right .banner-overlay .row {
\n        /*background-image: url(\'/shared_media/courseco/media/photos/content/banner_overlay_right.png\');*/
\n        background-position-x: right;
\n    }
\n
\n    .banner-slide\-\-center .banner-overlay,
\n    .banner-slide\-\-center .banner-overlay .row {
\n        background: none;
\n    }
\n
\n    .banner-slide\-\-center .banner-overlay .row {
\n        max-width: 1200px;
\n    }
\n
\n    .banner-slide\-\-center .banner-overlay-content {
\n        max-width: 1040px;
\n    }
\n
\n    .banner-overlay-content form li {
\n        width: 50%;
\n    }
\n
\n    .banner-overlay-content form li:nth-child(odd):last-child {
\n        width: 100%;
\n    }
\n}
\n
\n.search-drilldown h3 {
\n    color: #00375e;
\n}
\n
\n.search-drilldown-column p {
\n    color: #198ebe;
\n}
\n
\n.search-drilldown-column a.active {
\n    background-color: #00375e;
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
\n        border-top-color: #00375e;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .header-action:only-child {
\n        margin-left: auto;
\n    }
\n
\n    .search-drilldown-column {
\n        border-color: #198ebe;
\n    }
\n}
\n
\n/* Calendar */
\n.eventCalendar-wrap {
\n    border-color: #bfbfbf;
\n}
\n
\n.eventsCalendar-slider {
\n    background: #00375e;
\n    background: -webkit-linear-gradient(#1c8da1, #00375e);
\n    background: linear-gradient(#1c8da1, #00375e);
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
\n    color: #00375e;
\n}
\n
\n.eventsCalendar-subtitle,
\n.eventsCalendar-list > li > time {
\n    color: #1c8da1;
\n}
\n
\n.eventsCalendar-list > li {
\n    border-bottom-color: #bfbfbf;
\n}
\n
\n/* News feeds */
\n.news-section {
\n    background: #e2e2e2;
\n    box-shadow: 1px 1px 10px #ccc;
\n}
\n
\n.news-slider-link {
\n  color: #00375e;
\n}
\n
\n.news-slider-title {
\n    color: #00375e;
\n    background-color: #e2e2e2;
\n}
\n
\n.swiper-pagination-bullet {
\n    background-color: #fff;
\n    border-color: #A6AEAD;
\n    box-shadow: inset 0 1px 1px #aaa;
\n}
\n
\n.swiper-pagination-bullet-active {
\n    background-color: #1c8da1;
\n}
\n
\n.news-result-date {
\n    background-color: #00375e;
\n    color: #FFF;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .news-result + .news-result:before {
\n        background: linear-gradient(to right, transparent 0, #00375e 10%, #00375e 90%, transparent 100%);
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .news-result + .news-result {
\n        border-color: #ccc;
\n    }
\n}
\n
\n.news-story-navigation a {
\n    color: #198ebe;
\n}
\n
\n.news-story-social {
\n    border-color: #00375e;
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
\n    color: #00375e;
\n}
\n
\n/* Panels */
\n.panel {
\n    background-color: #fff;
\n}
\n
\n.carousel-section .panel {
\n    border-color: #bfb8bf;
\n}
\n
\n.panel-title h3 {
\n    font-weight: 400;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .panels-feed\-\-home_content > .column:after {
\n        background: #00375e;
\n        background: linear-gradient(to right, #E6F3C8 0%, #00375e 20%, #00375e 80%, #E6F3C8 100%);
\n    }
\n}
\n
\n.bar {
\n    background: #f3f5f5;
\n    background: rgba(243, 245, 245, .8);
\n    box-shadow: 0 1px 1px #aaa;
\n}
\n
\n.bar-icon {
\n    background: #00375e;
\n    color: #fff;
\n}
\n
\n.bar-icon svg {
\n  fill: #fff;
\n}
\n
\n.bar-text {
\n    color: #00375e;
\n    font-weight: 400;
\n}
\n
\n.panel-item.has_form {
\n    background-color: #00375e;
\n    color: #fff;
\n}
\n
\n.panel-item.has_form .button {
\n    background-color: #fff;
\n    border-color: #00375e;
\n    color: #00375e;
\n}
\n
\n.panel-item-image:after {
\n    background-image: url(\'/shared_media/courseco/media/photos/content/panel_overlay.png\');
\n    height: 142px;
\n    top: unset;
\n}
\n
\n.panel-item.has_image .panel-item-text {
\n    color: #00375e;
\n    padding: 15px 16px 0;
\n    text-align: center;
\n}
\n
\n/* Search results */
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
\n    background: #00375e;
\n    color: #fff;
\n}
\n
\n.course-list\-\-grid .course-widget-price {
\n    background-color: #00375e;
\n    color: #fff;
\n}
\n
\n.course-list\-\-list .course-widget-price-original,
\n.course-list\-\-list .course-widget-price-current {
\n    color: #00375e;
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
\n    color: #00375e;
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
\n    border-color: #00375e;
\n}
\n
\n.availability-timeslot.booked .highlight {
\n    color: #00375e;
\n}
\n
\n.timeline-swiper .swiper-slide.selected {
\n    background: #f9951d;
\n    color: #fff;
\n}
\n
\n.timeline-swiper-highlight {
\n    color: #00375e;
\n}
\n
\n.timeline-swiper-prev,
\n.timeline-swiper-next {
\n    color: #f9951d;
\n}
\n
\n/* Footer */
\n.page-footer {
\n    background: #00375e;
\n    color: #fff;
\n    overflow-x: hidden;
\n    overflow-y: auto;
\n    position: relative;
\n}
\n
\n.layout-landing_page .page-footer {
\n    display: none;
\n}
\n
\n.page-footer-bottom {
\n    color: #1c8da1;
\n    padding-top: 7em;
\n    padding-bottom: 3em;
\n    position: relative;
\n    z-index: 0;
\n}
\n
\n.page-footer .page-content h1,
\n.page-footer .page-content h2,
\n.page-footer .page-content h3,
\n.page-footer .page-content h4,
\n.page-footer .page-content h5,
\n.page-footer .page-content h6,
\n.page-footer .page-content p {
\n    color: inherit;
\n}
\n
\n.page-footer-bottom:before {
\n    content: \'\';
\n    background: #e0db2e;
\n    background: url(\'/shared_media/courseco/media/photos/content/footer_curve.svg\') top center no-repeat;
\n    position: absolute;
\n    top: -3.5em;
\n    right: -50vw;
\n    bottom: 0px;
\n    left: -50vw;
\n    z-index: -1;
\n}
\n
\n@media screen and (max-width: 599px) {
\n    .page-footer-bottom {
\n        padding-top: 3rem;
\n    }
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .page-footer .simplebox-columns {
\n        display: flex;
\n        flex-wrap: wrap;
\n        text-align: center;
\n    }
\n
\n    .page-footer .simplebox-column {
\n        margin: 0 auto;
\n        max-width: 25%;
\n    }
\n
\n    .page-footer-bottom:before {
\n        background-image: url(\'/shared_media/courseco/media/photos/content/footer_curve.svg\'), linear-gradinet(transparent 0, transparent 20%, #e0db2e 100%);
\n        background-size: 100%;
\n        top: -1.5em;
\n    }
\n}
\n
\n.footer {
\n    background: #00385d;
\n    font-family: Quicksand, Roboto, Helvetica, Arial, sans-serif;
\n    margin-top: 0;
\n}
\n
\n.footer:after {
\n    content: \'\';
\n    clear: both;
\n    display: table;
\n}
\n
\n.footer-logo img {
\n    width: 100%;
\n    max-width: 500px;
\n}
\n
\n.footer-stats-list {
\n    color: #00375e;
\n}
\n
\n.footer-slogan {
\n    color: #1c8da1;
\n}
\n
\n.footer-stats {
\n    /*background: #fff url(\'/shared_media/courseco/media/photos/content/footer_background.svg\') top center;*/
\n    min-height: 0;
\n}
\n
\n.footer-stats-list h2 {
\n    color: #1c8da1;
\n}
\n
\n.footer-stat h2:after {
\n    border-color: #1c8da1;
\n}
\n
\n.footer-social {
\n    color: #fff;
\n    padding: 0;
\n}
\n
\n.footer-social .row {
\n    padding: 1.2rem 0;
\n}
\n
\n.footer-social h2 {
\n    font-size: 1.8rem;
\n    margin-right: 1.5em;
\n    margin-left: 1.5em;
\n    margin-bottom: 0px;
\n    margin-top: 0px;
\n}
\n
\n.social-icon {
\n    border-radius: 0;
\n    width: 2.65rem;
\n    height: 2.65rem;
\n}
\n
\n.social-icon\-\-twitter {
\n    background-image: url(\'http://courseco.test.ibplatform.ie/shared_media/courseco/media/photos/content/twitter-outline.svg\');
\n}
\n
\n.social-icon\-\-facebook {
\n    background-image: url(\'http://courseco.test.ibplatform.ie/shared_media/courseco/media/photos/content/facebook-outline.svg\');
\n}
\n
\n.social-icon\-\-linkedin {
\n    background-image: url(\'http://courseco.test.ibplatform.ie/shared_media/courseco/media/photos/content/linkedin-outline.svg\');
\n}
\n
\n.footer-columns {
\n    background: none;
\n    border-top-color: #2b5777;
\n    color: #fff;
\n}
\n
\n.footer-column-title {
\n    color: #fff;
\n    font-weight: 400;
\n    text-transform: uppercase;
\n}
\n
\n.footer-column h4 {
\n    font-weight: bold;
\n}
\n
\n.footer .form-input::-webkit-input-placeholder { color: #fff; font-weight: 300; }
\n.footer .form-input::-moz-placeholder          { color: #fff; font-weight: 300; }
\n.footer .form-input:-ms-input-placeholder      { color: #fff; font-weight: 300; }
\n
\n.newsletter-signup-form input[type=\"text\"] {
\n    background: none;
\n    border: 1px solid #fff;
\n    color: #fff;
\n}
\n
\n.newsletter-signup-form .button {
\n    background-color: #fff;
\n    color: #00375e;
\n}
\n
\n.footer-copyright {
\n    background: none;
\n    border-top-color: #2b5777;
\n    color: #fff;
\n    padding-top: 1.75rem;
\n    padding-bottom: 1rem;
\n}
\n
\n.footer-copyright-cms {
\n	margin-left: auto
\n    }
\n
\n.simplebox-content.workshop-buttons {
\n    text-align: center;
\n}
\n
\n.workshop-buttons p {
\n    background-color: #008499;
\n}
\n
\n.workshop-buttons p a {
\n    display: inline-block;
\n    padding: 10px;
\n    color: #ffffff;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .menu\-\-header1 li.level_1 {
\n    padding-top: 32px;
\n    }
\n
\n    .footer-column-title {
\n        display: block;
\n        font-size: 1rem;
\n        font-weight: 500;
\n        padding: .25em 0 .3125em;
\n    }
\n
\n    .footer-column:not(.has_sublist) {
\n        padding: 0;
\n    }
\n
\n    .footer-copyright .row {
\n        line-height: 1.75;
\n    }
\n}
\n
\n
\n@media screen and (min-width: 768px) {
\n
\n	.menu\-\-header1 li.level_1 {
\n		padding-right: 2em;
\n		padding-left: 2em;
\n		display: inline-block;
\n		padding-top: 17px !important;
\n	}
\n
\n	ul.menu\-\-header1 {
\n	    display: flex;
\n	    width: 100%;
\n	    justify-content: center;
\n	}
\n
\n    .footer-column-title {
\n        font-size: .9375rem;
\n        font-weight: 500;
\n    }
\n}
\n
\n@media screen and (max-width: 1077px) {
\n    .footer-columns,
\n    .footer-columns .container,
\n    .footer-copyright,
\n    .footer-copyright .row { width: auto; }
\n
\n    .footer-columns .container {padding-left: 19px;}
\n    .footer-copyright .row {padding-right: 19px;}
\n}
\n
\n/* Dropdown filters */
\n.search-filter-total {
\n    color: #00375e;
\n}
\n
\n.search-filters :checked ~ .form-checkbox-helper,
\n.search-filters :checked ~ .form-radio-helper,
\n.search-filters :checked ~ .form-checkbox-label,
\n.search-filters :checked ~ .form-radio-label {
\n    border-color: #00375e;
\n    color: #00375e;
\n}
\n
\n.search-filters :checked + .form-radio-helper:after {
\n    background-color: #00375e;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-filter-dropdown.filter-active > button,
\n    .search-filters-clear {
\n        color: #00375e;
\n    }
\n
\n    .checkout-heading {
\n        background-color: #00375e;
\n        color: #fff;
\n    }
\n}
\n
\n
\n
\n/* Landing page */
\n.layout-landing_page .header-right,
\n.layout-landing_page .header-menu-section {
\n    display:none;
\n}
\n
\n.layout-landing_page .banner-overlay-content {
\n    color: #fff;
\n}
\n
\n.layout-landing_page .banner-overlay-content h1,
\n.layout-landing_page .banner-overlay-content h2,
\n.layout-landing_page .banner-overlay-content h3,
\n.layout-landing_page .banner-overlay-content h4,
\n.layout-landing_page .banner-overlay-content h5,
\n.layout-landing_page .banner-overlay-content h6,
\n.layout-landing_page .banner-overlay-content p {
\n    color: inherit;
\n}
\n
\n.layout-landing_page .banner-overlay-content h1 {
\n    margin: .13em 0;
\n}
\n
\n.layout-landing_page .simplebox.gray:before {
\n    content: \'\';
\n    background-color: #ccc;
\n    background-image: url(/shared_media/courseco/media/photos/content/ib_logo_circle.png);
\n    background-repeat: no-repeat;
\n    background-position: 130% 30%;
\n    background-size: 800px;
\n    opacity: .25;
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n    z-index: -1;
\n}
\n
\n
\n.layout-landing_page .simplebox.darkblue ul {
\n    max-width: 14em;
\n    margin: auto;
\n    font-weight: 500;
\n}
\n
\n.layout-landing_page .simplebox.darkblue ul li {
\n    margin: 0;
\n}
\n
\n.layout-landing_page .simplebox.darkblue ul li:before {
\n    background: #fff;
\n    border-radius: 50%;
\n    font-size: .5em;
\n    color: #1c8da1;
\n    margin-top: .75em;
\n    padding: .25em .5em;
\n    text-align: center;
\n    width: 2em;
\n    height: 2em;
\n}
\n
\n
\n@media screen and (min-width: 768px) {
\n    .layout-landing_page .simplebox.darkblue ul {
\n        font-size: 2.25em;
\n        max-width: 14em;
\n        margin: auto;
\n        font-weight: 500;
\n    }
\n}
\n
\n@media screen and (min-width: 992px) {
\n    .layout-landing_page .page-content-banner-overlay .row {
\n        background: rgba(90, 201, 232, .5);
\n    }
\n}
\n
\n
\n
\n
\n/* Misc */
\n.contact\-\-left .ui-tabs-nav .ui-tabs-active a {
\n    background: #00375e;
\n    border-color:#00375e;
\n    color: #fff;
\n}
\n
\n.checkout-right-sect .btn-close:hover {
\n    color: #00375e;
\n    border-color: #00375e;
\n}
\n
\n.checkout-right-sect .sub-total {
\n    color: #198ebe;
\n}
\n
\n.checkout-progress li a:after {
\n    background-color: #fff;
\n    border-color: #00375e;
\n}
\n
\n.checkout-progress li.curr a:after {
\n    background: #00375e;
\n    background: radial-gradient(#95ced7, #00375e);
\n}
\n
\n.checkout-progress li + li:before {
\n    border-color: #00375e;
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
\n    background: #00375e;
\n    color: #fff;
\n}
\n
\n.prepay-box h6 {
\n    color: #00375e;
\n}
\n
\n.custom-calendar .booking-date-button {
\n    background-color: #00375e;
\n    color: #fff;
\n}
\n
\n.custom-calendar .booking-date-button:hover {
\n    background-color: #198ebe;
\n}
\n
\n.custom-calendar button.booking-date-button.active {
\n    background-color: #fff;
\n    color: #00375e;
\n}
\n
\n.course-activity-alert,
\n.details-wrap .left-place {
\n    color: #F75A5F;
\n}
\n
\n.number-of-people-viewing {
\n    color: #00375e;
\n}
\n
\n.search-calendar-course-image .fa {
\n    background-color: #00375e;
\n    color: #fff;
\n}
\n
\n.custom-calendar tbody td.active,
\n.custom-calendar tbody td.active:hover {
\n    background-color: #fff;
\n    color: #00375e;
\n}
\n
\n.custom-calendar tbody tr:first-child td {
\n    color: #222;
\n}
\n
\n.package-offers-wrap h2 {
\n    color: #00375e;
\n    border-color: #c5cecd;
\n}
\n
\n.package-offers-wrap h3 {
\n    color: #00375e;
\n}
\n
\n.package-offers-wrap .summary-wrap .more,
\n.classes-details-wrap .details-wrap li:first-child {
\n    color: #00375e;
\n}
\n
\n.classes-details-wrap .details-wrap li:first-child {
\n  background-color: #00375e;
\n}
\n
\n.details-wrap .remove-booking,
\n.details-wrap .wishlist.remove{
\n    color: #00375e;
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
\n.details-wrap .fa-book {
\n    color: #00375e;
\n}
\n
\n/* course results hover */
\n.details-wrap:hover {
\n    background-color: #f9f9f9;
\n    border-color:#d8d8d8 ;
\n}
\n
\n.details-wrap:hover .time,
\n.details-wrap:hover .price,
\n.details-wrap:hover .fa-book {
\n    color: #00375e;
\n}
\n
\n.details-wrap:hover li:first-child {
\n    background-color: #00375e;
\n}
\n
\n.details-wrap:hover .sidelines::before,
\n.details-wrap:hover .sidelines::after,
\n.details-wrap:hover .price-wrap {
\n    border-color:#00375e;
\n}
\n
\n
\n/* course results booked */
\n.details-wrap.booked {
\n    border-color:#00375e;
\n    background-color: #f3f3f3;
\n}
\n
\n.details-wrap.booked .time,
\n.details-wrap.booked .price,
\n.details-wrap.booked .fa-book {
\n    color: #00375e;
\n}
\n.details-wrap.booked li:first-child {
\n    background-color: #00375e;
\n}
\n
\n.details-wrap.booked .sidelines::before,
\n.details-wrap.booked .sidelines::after,
\n.details-wrap.booked .price-wrap {
\n    border-color:#00375e;
\n}
\n
\n.classes-details-wrap .alert-wrap {
\n    background-color: #00375e;
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
\n    color: #00375e;
\n}
\n
\n.custom-calendar .booking-date-button.already_booked {
\n    background-color: #00375e;
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
\n    background-image: url(\"data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M0%2C22L22%2C0l2.1%2C2.1L4.2%2C22l19.9%2C19.9L22%2C44L0%2C22L0%2C22L0%2C22z\'%20fill%3D\'%2300375e\'%2F%3E%3C%2Fsvg%3E\");
\n}
\n
\n.swiper-button-next {
\n    background-image: url(\"data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M27%2C22L27%2C22L5%2C44l-2.1-2.1L22.8%2C22L2.9%2C2.1L5%2C0L27%2C22L27%2C22z\'%20fill%3D\'%2300375e\'%2F%3E%3C%2Fsvg%3E\");
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
\n.content {
\n    margin-top: 0;
\n}
\n
\n/* Hack, for now, to get text from the page editor to overlay the banner */
\n@media screen and (max-width: 991px) {
\n    .page-content-banner-overlay {
\n        background: #00375e;
\n        text-align: center;
\n        color: #fff;
\n        margin-left: -2em;
\n        margin-right: -2em;
\n        padding: 1em;
\n    }
\n}
\n
\n@media screen and (min-width: 992px) {
\n    .content {
\n        position: relative;
\n    }
\n
\n    .page-content-banner-overlay {
\n        position: absolute;
\n        top: -184px;
\n        left: 0;
\n        right: 0;
\n        text-align: center;
\n        z-index: 1;
\n    }
\n
\n    .page-content-banner-overlay .row {
\n        background: rgba(0, 56, 93, .5);
\n        border-radius: 2em 2em 0 0;
\n        color: #fff;
\n        height: 160px;
\n        padding-top: .1em;
\n    }
\n
\n    .page-content-banner-overlay .row * {
\n        color: inherit;
\n    }
\n
\n    .page-content-banner-overlay .simplebox {
\n        padding-left: 1em;
\n        padding-right: 1em;
\n    }
\n
\n    .page-content-banner-overlay .simplebox-title {
\n        margin-top: 0;
\n        margin-bottom: 0;
\n    }
\n
\n    .page-content-banner-overlay .simplebox-title h4 {
\n        margin: .5em 0;
\n    }
\n
\n    .page-content-banner-overlay .simplebox-content p {
\n        display: flex;
\n        align-items: center;
\n        justify-content: space-around;
\n    }
\n
\n    .page-content-banner-overlay .simplebox-content img {
\n        margin: 0 1em;
\n    }
\n}
\n
\n
\n
\n
\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Diagonal \"Our Work\" page
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.our_products {
\n    width: 100vw;
\n        margin-left: calc(50% - 50vw);
\n}
\n
\n.our_products-row {
\n    font-family: \'Alegreya Sans\', sans-serif;
\n    padding-top: 1vw;
\n    width: 100%;
\n}
\n
\n.our_products-item {
\n    -webkit-box-align: center;
\n    -ms-flex-align: center;
\n    -ms-grid-row-align: center;
\n    align-items: center;
\n    background-repeat: no-repeat;
\n    background-size: 100% auto;
\n    color: #fff;
\n    display: flex;
\n    margin-bottom: 1vw;
\n    min-height: 42vw;
\n    position: relative;
\n    width: 100%
\n}
\n
\n.our_products-item.our_products-item p {
\n    color: inherit;
\n}
\n
\n.our_products-item.our_products-item a:visited {
\n    color: #fff;
\n}
\n
\n.our_products-item:after {
\n    background-color: rgba(0, 0, 0, .6);
\n    content: \'\';
\n    display: block;
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n}
\n
\n.our_products-item\-\-light:after {
\n    background-color: rgba(0, 0, 0, .25);
\n}
\n
\n.our_products-item.no-blackout:after {
\n    display: none;
\n}
\n
\n.our_products-overlay {
\n    line-height: 1.5;
\n    position: relative;
\n    z-index: 1;
\n}
\n
\n.our_products-overlay p {
\n    margin: .8em 0;
\n}
\n
\n.our_products-item a,
\n.our_products-item h1,
\n.our_products-item h2 {
\n    color: inherit;
\n}
\n
\n.our_products-item h1 {
\n    font-size: 60px;
\n    font-weight: normal;
\n    text-align: center;
\n}
\n
\n.our_products-item a:link {
\n    text-decoration: underline;
\n}
\n
\n@media screen and (max-width: 500px) {
\n    .our_products-overlay {
\n        font-size: 1rem;
\n        text-align: center;
\n    }
\n
\n    .our_products-item h1 {
\n        font-size: 29px;
\n    }
\n
\n    .our_products-overlay p {
\n        text-align: left;
\n    }
\n
\n    .our_products-logo {
\n        max-width: 120px;
\n        margin-top: 50px;
\n    }
\n}
\n
\n@media screen and (max-width: 899px) {
\n    .our_products-item {
\n        -webkit-clip-path: polygon(0 20%, 100% 0, 100% 80%, 0 100%);
\n        clip-path: polygon(0 20%, 100% 0, 100% 80%, 0 100%);
\n        height: 80vw;
\n        margin-top: -13.35vw;
\n        margin-bottom: 3vw;
\n    }
\n
\n    .our_products-row:first-child {
\n        padding-top: 0;
\n    }
\n
\n    .our_products-row:first-child .our_products-item:only-child {
\n        -webkit-clip-path: polygon(0 0, 100% 0, 100% 74%, 0 100%);
\n        clip-path: polygon(0 0, 100% 0, 100% 74%, 0 100%);
\n        height: 60vw;
\n        margin-top: 0;
\n    }
\n
\n    .our_products-row:last-child .our_products-item {
\n        -webkit-clip-path: polygon(0 20%, 100% 0, 100% 100%, 0 100%);
\n        clip-path: polygon(0 20%, 100% 0, 100% 100%, 0 100%);
\n    }
\n
\n    .our_products-row:only-child .our_products-item.our_products-item {
\n        -webkit-clip-path: none;
\n        clip-path: none;
\n    }
\n
\n    .our_products-overlay {
\n        width: 90vw;
\n    }
\n
\n    .our_products-item:only-child {
\n        background-size: 200% auto;
\n        background-position: center center;
\n        margin-bottom: 0;
\n    }
\n}
\n
\n@media screen and (min-width: 900px) {
\n    .our_products-row {
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        margin-top: -13.35vw;
\n    }
\n
\n    .our_products-item:nth-last-child(2) {
\n        margin-top: 5.9vw;
\n        margin-right: .5vw;
\n        margin-bottom: 2vw;
\n        -webkit-clip-path: polygon(0 13.1%, 100% 0, 100% 85.9%, 0 100%);
\n        clip-path: polygon(0 13.1%, 100% 0, 100% 85.9%, 0 100%);
\n    }
\n
\n    .our_products-item:nth-last-child(2) + .our_products-item {
\n        height: 0;
\n        margin-left: .5vw;
\n        -webkit-clip-path: polygon(0 13.5%, 100% 0, 100% 86.5%, 0 100%);
\n        clip-path: polygon(0 13.5%, 100% 0, 100% 86.5%, 0 100%);
\n    }
\n
\n    .our_products-item:only-child {
\n        -webkit-clip-path: polygon(0 23.8%, 100% 0, 100% 73.8%, 0 97%);
\n        clip-path: polygon(0 23.8%, 100% 0, 100% 73.8%, 0 97%);
\n        height: 47.7vw;
\n    }
\n
\n    .our_products-row:first-child {
\n        margin-top: 0;
\n        padding-top: 0;
\n    }
\n
\n    .our_products-row:first-child .our_products-item:only-child {
\n        -webkit-clip-path: polygon(0 0, 100% 0, 100% 68.8%, 0 100%);
\n        clip-path: polygon(0 0, 100% 0, 100% 68.8%, 0 100%);
\n        min-height: 35vw;
\n        height: 35vw;
\n    }
\n
\n    .our_products-row:last-child .our_products-item:only-child {
\n        -webkit-clip-path: polygon(0 23.8%, 100% 0, 100% 100%, 0 100%);
\n        clip-path: polygon(0 23.8%, 100% 0, 100% 100%, 0 100%);
\n        margin-bottom: -2px;
\n    }
\n
\n    .our_products-overlay {
\n        width: 32.6vw;
\n    }
\n
\n    .our_products-row:first-child .our_products-item:only-child .our_products-overlay {
\n        margin-left: 5vw;
\n        margin-right: 5vw;
\n        width: 100%;
\n        text-align: center;
\n    }
\n
\n
\n    .our_products-row:only-child .our_products-item:only-child {
\n        -webkit-clip-path: polygon(0 20%, 100% 0, 100% 80%, 0 100%);
\n        clip-path: polygon(0 20%, 100% 0, 100% 80%, 0 100%);
\n        height: 47.7vw;
\n        margin-top: 0;
\n        margin-bottom: -8vw;
\n    }
\n
\n    .our_products-row .our_products-item .our_products-overlay.our_products-overlay.our_products-overlay\-\-bottom_left {
\n        align-self: flex-end;
\n        padding-left: 5vw;
\n        margin-bottom: 6vw;
\n        width: 40%;
\n        text-align: left;
\n    }
\n
\n}
\n
\n@media screen and (min-width: 900px) and (max-width: 1279px) {
\n
\n    .our_products-item:only-child .our_products-overlay {
\n        width: 60vw;
\n    }
\n
\n    .our_products-logo {
\n        max-width: 250px;
\n    }
\n}
\n
\n@media screen and (max-width: 1279px) {
\n    .our_products-overlay {
\n        margin-left: 5vw;
\n    }
\n}
\n
\n@media screen and (min-width: 1280px) {
\n    .our_products-overlay {
\n        font-size: 1.5rem;
\n        margin-left: 12.7vw;
\n    }
\n
\n    .our_products-item:nth-child(2) .our_products-overlay {
\n        margin-left: 7.2vw;
\n    }
\n
\n    .our_products-item:only-child .our_products-overlay {
\n        width: 37.4vw;
\n    }
\n}'
  WHERE
  `stub` = 'ideabubble2'
;;

SELECT
(SELECT
`plugin_pages_layouts`.`id`
FROM engine_site_templates
INNER JOIN plugin_pages_layouts ON `engine_site_templates`.`id` = `plugin_pages_layouts`.`template_id`
WHERE `engine_site_templates`.`stub` = '04' and `plugin_pages_layouts`.`layout` = 'content_wide')
FROM engine_site_templates
INNER JOIN plugin_pages_layouts ON `engine_site_templates`.`id` = `plugin_pages_layouts`.`template_id`
WHERE `engine_site_templates`.`stub` = '04' and `plugin_pages_layouts`.`layout` = 'content_wide';;

UPDATE
  `plugin_pages_pages`
SET
  `last_modified` = CURRENT_TIMESTAMP,
  `content` = '<div class="simplebox">
\n	<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/banners/banner_home1.png" style="height:600px; width:1904px" /></p>
\n</div>
\n
\n<div class="darkblue simplebox">
\n	<div class="simplebox-title">
\n		<h2 style="text-align:center">We can manage your IT projects from start to finish</h2>
\n	</div>
\n
\n	<div class="simplebox-content">
\n		<h4 style="text-align:center">Here&#39;s a taste of what we do...</h4>
\n
\n		<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n	</div>
\n
\n	<div class="simplebox-columns">
\n		<div class="simplebox-column simplebox-column-1" style="max-width:50%">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/idea.png" style="height:88px; width:88px" /></p>
\n
\n				<h4 style="text-align:center">Planning &amp; Design</h4>
\n
\n				<p style="text-align:center">Our team will collaborate with you to seamlessly manage and deliver projects.</p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-2" style="max-width:50%">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/computer.png" style="height:88px; width:88px" /></p>
\n
\n				<h4 style="text-align:center">Software Development</h4>
\n
\n				<p style="text-align:center">With 10 years programming experiance, we design and develop bespoke software solutions.</p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-3" style="max-width:50%">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/qa.png" style="height:88px; width:88px" /></p>
\n
\n				<h4 style="text-align:center">Quality Assurance</h4>
\n
\n				<p style="text-align:center">Our QA experts will detect QA problems and set up project processes.</p>
\n			</div>
\n		</div>
\n	</div>
\n</div>
\n
\n<div class="simplebox">
\n	<div class="simplebox-title">
\n		<h2 style="text-align:center">We have...</h2>
\n	</div>
\n
\n	<div class="simplebox-columns">
\n		<div class="simplebox-column simplebox-column-1" style="max-width:50%">
\n			<div class="simplebox-content">
\n				<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/10_years.png" style="height:88px; width:88px" /></p>
\n
\n				<h4 style="text-align:center">10+ years<br />
\n				experience</h4>
\n
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-2" style="max-width:50%">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/techies.png" style="height:88px; width:88px" /></p>
\n
\n				<h4 style="text-align:center">Straight talking<br />
\n				techies</h4>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-3" style="max-width:50%">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/no1_google_results.png" style="height:88px; width:88px" /></p>
\n
\n				<h4 style="text-align:center">No. 1 Google<br />
\n				results</h4>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-4" style="max-width:50%">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/phased_approach.png" style="height:88px; width:88px" /></p>
\n
\n				<h4 style="text-align:center">Phased<br />
\n				approach</h4>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-5" style="max-width:50%">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/shared_media/ideabubble/media/photos/content/fun.png" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/fun.png" style="height:88px; width:88px" /></p>
\n
\n				<h4 style="text-align:center">Fun<br />
\n				&nbsp;</h4>
\n			</div>
\n		</div>
\n	</div>
\n
\n	<p style="text-align:center"><a class="button" href="/workshop">Book a Workshop</a></p>
\n</div>
\n
\n<div class="simplebox">
\n	<div class="simplebox-title">
\n		<h2 style="text-align:center">What we offer</h2>
\n	</div>
\n
\n	<div class="simplebox-columns">
\n		<div class="simplebox-column simplebox-column-1">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p>What&rsquo;s more, CourseCo is built by our dedicated team of in-house developers and doesn&rsquo;t rely on third party software or extensions. We&rsquo;re proud to offer a robust and secure platform with zero downtime.</p>
\n
\n				<p>Whether you have a school, college or private training school, CourseCo makes it easy to stay organised.</p>
\n
\n				<h4><strong>It&#39;s time to catapult your productivity and make smart sales.</strong></h4>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-2">
\n			<div class="simplebox-content">
\n				<p><img alt="" src="/shared_media/ideabubble/media/photos/content/shutterstock_197557880.png" style="height:270px; width:440px" /></p>
\n
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n			</div>
\n		</div>
\n	</div>
\n</div>
\n
\n<div class="simplebox">
\n	<div class="simplebox-title">
\n		<h2 style="text-align:center">About us</h2>
\n
\n		<p style="text-align:center">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
\n	</div>
\n
\n	<div class="simplebox-columns">
\n		<div class="simplebox-column simplebox-column-1" style="max-width:50%">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/coin.png" style="height:43px; width:42px" /></p>
\n
\n				<h1 style="text-align:center">3 mill</h1>
\n
\n				<p style="text-align:center">Value Saved</p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-2" style="max-width:50%">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/users.png" style="height:43px; width:42px" /></p>
\n
\n				<h1 style="text-align:center">2,472</h1>
\n
\n				<p style="text-align:center">Users</p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-3" style="max-width:50%">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/solutions.png" style="height:43px; width:42px" /></p>
\n
\n				<h1 style="text-align:center">58</h1>
\n
\n				<p style="text-align:center">Integrations</p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-4" style="max-width:50%">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/integrations.png" style="height:43px; width:42px" /></p>
\n
\n				<h1 style="text-align:center">15</h1>
\n
\n				<p style="text-align:center">Solutions</p>
\n			</div>
\n		</div>
\n	</div>
\n</div>
\n
\n<p>&nbsp;</p>
',
`modified_by` = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
  WHERE
  `name_tag` IN ('home', 'home.html')
;;

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'our-story',
  'Our Story, Creating Booking Software, 10 years old',
  '<p>&nbsp;</p>
\n
\n<div class="simplebox">
\n	<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/our-story-banner.png" style="height:720px; width:1920px" /></p>
\n</div>
\n
\n<p>&nbsp;</p>
\n
\n<div class="darkblue simplebox">
\n	<div class="simplebox-columns">
\n		<div class="simplebox-column simplebox-column-1">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<h3 style="text-align:center">We&#39;re CourseCo, and this is our story</h3>
\n
\n				<h4 style="text-align:center">We are a small and passionate team that make great software for the course industry!<br />
\n				We&rsquo;re in business to transform inefficiencies in companies using the latest Website and Booking technologies.</h4>
\n
\n				<p>&nbsp;</p>
\n
\n				<h3 style="text-align:center">We do things right. That&#39;s how we&#39;re still around after 10 years.</h3>
\n
\n				<h4 style="text-align:center">We&rsquo;re techies with a personal touch. We work closely with our customers, listening to their every need and constantly growing with them. Our job is to ensure that our customers have a flexible and scalable Website and Booking solution to drive their businesses forward.</h4>
\n
\n				<p>&nbsp;</p>
\n
\n				<h3 style="text-align:center">And we&#39;re always moving in the right direction...</h3>
\n
\n				<h4 style="text-align:center">We noticed that most course providers are missing something - a good website that converts. We believe it&rsquo;s time to provide course providers with the right tools, one by one, and turn their business dreams into reality.</h4>
\n
\n				<p>&nbsp;</p>
\n
\n				<h3 style="text-align:center">CourseCo Milestones</h3>
\n
\n				<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos//content/milestones.png" style="height:340px; width:938px" /></p>
\n			</div>
\n		</div>
\n	</div>
\n</div>
\n
\n<h2 style="text-align:center">We&#39;re growing every day. Our team is too.</h2>
\n
\n<div class="simplebox simplebox-align-top">
\n	<div class="simplebox-columns">
\n		<div class="simplebox-column simplebox-column-1">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-mike.png" style="height:243px; width:243px" /> <img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-michael.jpg" style="height:243px; width:243px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Michael O&#39;Callaghan</span><br />
\n				<span style="font-size:18px">Managing Director</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill?</strong>&nbsp;Empathy<br />
\n				<strong>Hobbies?</strong>&nbsp;Long distance swimming<br />
\n				<strong>Favourite drink?</strong> Cappuccino<br />
\n				<strong>Can&#39;t live without? </strong>My Stopwatch</span></p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-2">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-tempy.png" style="height:243px; width:243px" /> <img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-tempy.jpg" style="height:243px; width:243px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Tempy&nbsp;Allen</span><br />
\n				<span style="font-size:18px">Operations Manager</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill?</strong>&nbsp;Making Things Happen!<br />
\n				<strong>Hobbies?</strong> Running, Meditating<br />
\n				<strong>Favourite drink?</strong> Glass of Malbec<br />
\n				<strong>Can&#39;t live without?</strong> My family</span></p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-3">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-mary.png" style="height:243px; width:243px" /> <img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-mary-2.jpg" style="height:243px; width:243px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Mary Allen</span><br />
\n				<span style="font-size:18px">Product Manager</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill?</strong>&nbsp;Creativity<br />
\n				<strong>Hobbies?</strong> Drawing<br />
\n				<strong>Favourite drink?</strong> Coffee<br />
\n				<strong>Can&#39;t live without?</strong> Chocolate</span></p>
\n			</div>
\n		</div>
\n	</div>
\n</div>
\n
\n<div class="simplebox simplebox-align-top">
\n	<div class="simplebox-columns">
\n		<div class="simplebox-column simplebox-column-1">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-nick.png" style="height:243px; width:243px" /> <img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-nick.jpg" style="height:243px; width:243px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Nick Gudge</span><br />
\n				<span style="font-size:18px">Director of Finance</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill?</strong>&nbsp;Finance + being mostly human!<br />
\n				<strong>Hobbies?</strong>&nbsp;Keeping my wife happy&nbsp;&amp; Taijiquan<br />
\n				<strong>Favourite drink?&nbsp;&nbsp;</strong><span style="font-size:16px">Raspberry Diaquiri</span><br />
\n				<strong>Can&#39;t live without?&nbsp;</strong>Smiling</span></p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-2">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-ratko.png" style="height:243px; width:243px" /> <img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-ratko.jpg" style="height:243px; width:243px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Ratko Bucic</span><br />
\n				<span style="font-size:18px">Ops Manager</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill?</strong>&nbsp;System administration<br />
\n				<strong>Hobbies?</strong> Fishing, biking<br />
\n				<strong>Favourite drink?</strong>&nbsp;Schweppes Bitterlemon<br />
\n				<strong>Can&#39;t live without?</strong> Internet</span></p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-3">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-rob.png" style="height:243px; width:243px" /> <img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-rob.jpg" style="height:243px; width:243px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Robert O&#39;Neill</span><br />
\n				<span style="font-size:18px">Director of Marketing</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill?</strong>&nbsp;Marketing and Copy<br />
\n				<strong>Hobbies?</strong> Mountain running<br />
\n				<strong>Favourite drink?</strong> Whiskey Ginger<br />
\n				<strong>Can&#39;t live without? </strong>My toothbrush</span></p>
\n			</div>
\n		</div>
\n	</div>
\n</div>
\n
\n<div class="simplebox simplebox-align-top">
\n	<div class="simplebox-columns">
\n		<div class="simplebox-column simplebox-column-1">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-rowan-3.png" style="height:245px; width:245px" /><img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-rowan-3.png" style="height:245px; width:245px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Rowan Copeland</span><br />
\n				<span style="font-size:18px">Sales Manager</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill?</strong> People<br />
\n				<strong>Hobbies?</strong> Cars and road trips<br />
\n				<strong>Favourite drink?</strong> Americano<br />
\n				<strong>Can&#39;t live without?</strong> Spotify</span></p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-2">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-maja.jpg" style="height:243px; width:243px" /> <img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-maja.jpg" style="height:243px; width:243px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Maja Otic</span><br />
\n				<span style="font-size:18px">Senior Product Designer</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill?</strong>&nbsp;UI/UX design<br />
\n				<strong>Hobbies?</strong> Reading books<br />
\n				<strong>Favourite drink?</strong>&nbsp;White wine<br />
\n				<strong>Can&#39;t live without?</strong> My library</span></p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-3">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-adham.png" style="height:243px; width:243px" /> <img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-adham.jpg" style="height:243px; width:243px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Adham Salem</span><br />
\n				<span style="font-size:18px">Lead Test Engineer</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill?</strong>&nbsp;QA/QC<br />
\n				<strong>Hobbies?</strong> Soccer<br />
\n				<strong>Favourite drink?</strong>&nbsp;Soda<br />
\n				<strong>Can&#39;t live without? </strong>Music</span></p>
\n			</div>
\n		</div>
\n	</div>
\n</div>
\n
\n<div class="simplebox simplebox-align-top">
\n	<div class="simplebox-columns">
\n		<div class="simplebox-column simplebox-column-1">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-stephen.png" style="height:243px; width:243px" /> <img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-stephen.jpg" style="height:243px; width:243px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Ste<span class="sr-only" style="font-size:.1px">ab</span><span class="sr-only" style="font-size:.1px"> </span><span class="sr-only" style="font-size:.1px">o</span>phen By<span class="sr-only" style="font-size:.1px">ab</span><span class="sr-only" style="font-size:.1px"> </span><span class="sr-only" style="font-size:.1px">o</span>rne</span><br />
\n				<span style="font-size:18px">Frontend Engineer</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill?</strong><br />
\n				<strong>Hobbies?</strong><br />
\n				<strong>Favourite drink?</strong><br />
\n				<strong>Can&#39;t live without?</strong></span></p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-2">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-mehmet.png" style="height:243px; width:243px" /> <img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-mehmet.jpg" style="height:243px; width:243px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Mehmet Emin Aky&uuml;z</span><br />
\n				<span style="font-size:18px">Software Engineer</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill?</strong>&nbsp;PHP<br />
\n				<strong>Hobbies?</strong> Bicycle riding<br />
\n				<strong>Favourite drink?</strong>&nbsp;Water<br />
\n				<strong>Can&#39;t live without?</strong> Oxygen</span></p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-3">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-alex.png" style="height:243px; width:243px" /> <img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-alexandr.jpg" style="height:243px; width:243px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Alexandr Makarov</span><br />
\n				<span style="font-size:18px">Software Engineer</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill?</strong>&nbsp;Backend development<br />
\n				<strong>Hobbies?</strong> Mountain skiing, online gaming<br />
\n				<strong>Favourite drink?</strong>&nbsp;Milk shake<br />
\n				<strong>Can&#39;t live without? </strong>Internet</span></p>
\n			</div>
\n		</div>
\n	</div>
\n</div>
\n
\n<div class="simplebox simplebox-align-top">
\n	<div class="simplebox-columns">
\n		<div class="simplebox-column simplebox-column-1">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-omura.png" style="height:243px; width:243px" /> <img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-omura.jpg" style="height:243px; width:243px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Omura Dai</span><br />
\n				<span style="font-size:18px">Mobile Software Engineer</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill?</strong>&nbsp;Mobile app development<br />
\n				<strong>Hobbies?</strong>&nbsp;Driving<br />
\n				<strong>Favourite drink?</strong> Liquor<br />
\n				<strong>Can&#39;t live without?</strong>&nbsp;Computer</span></p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-2">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-fanni.png" style="height:243px; width:243px" /> <img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-fanni.jpg" style="height:243px; width:243px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Fanni Boros</span><br />
\n				<span style="font-size:18px">Mobile Software Engineer</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill?</strong>&nbsp;Objectve/C, Swift and Java<br />
\n				<strong>Hobbies?</strong>&nbsp;Music<br />
\n				<strong>Favourite drink?&nbsp;</strong>Unicum<br />
\n				<strong>Can&#39;t live without? </strong>Water<strong> </strong>;)</span></p>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-3">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><img alt="" class="image-unhover" src="/shared_media/ideabubble/media/photos/content/team-liam.png" style="height:243px; width:243px" /> <img alt="" class="image-hover" src="/shared_media/ideabubble/media/photos/content/caricature-liam.jpg" style="height:243px; width:243px" /></p>
\n
\n				<h4 style="text-align:center"><span style="font-size:24px">Liam Sarsfield</span><br />
\n				<span style="font-size:18px">Software Engineer</span></h4>
\n
\n				<p><span style="font-size:16px"><strong>Key skill? </strong>Thinking outside the box.<br />
\n				<strong>Hobbies?</strong>&nbsp;Strategy PC and cycling<br />
\n				<strong>Favourite drink?</strong>&nbsp;cup of coffee<br />
\n				<strong>Can&#39;t live without?&nbsp;</strong>My PC</span></p>
\n			</div>
\n		</div>
\n	</div>
\n</div>
\n
\n<h2 style="text-align:center">Time to turn your business dream into reality...?</h2>
\n
\n<p style="text-align:center"><a class="button" href="/get-a-demo">GET A DEMO</a></p>
\n
\n<p>&nbsp;</p>
\n
\n<p>&nbsp;</p>
\n',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT
`plugin_pages_layouts`.`id`
FROM engine_site_templates
INNER JOIN plugin_pages_layouts ON `engine_site_templates`.`id` = `plugin_pages_layouts`.`template_id`
WHERE `engine_site_templates`.`stub` = '04' and `plugin_pages_layouts`.`layout` = 'content_wide'),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
);;

UPDATE
  `plugin_pages_pages`
SET
  `content`='<div class=\"simplebox\">
\n	<div class=\"simplebox-columns\">
\n		<div class=\"simplebox-column simplebox-column-1\">
\n			<div class=\"simplebox-content\">
\n				<div class=\"simplebox-content-toolbar\"><button><img src=\"/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg\" style=\"height:12px; width:12px\" /></button></div>
\n
\n				<h2>Fill in the form and let&#39;s get the ball rolling.</h2>
\n
\n				<p>You&#39;ll have a chance to talk to a product expert who will guide you through our platform features.</p>
\n
\n				<p>We&#39;re looking forward to showing you how CourseCo will solve your headaches and streamline your business.</p>
\n
\n				<p>This is your opportunity to:</p>
\n
\n				<ul>
\n					<li>Find out how CourseCo will help you sell courses</li>
\n					<li>See how CourseCo can help with admin tasks</li>
\n					<li>Explore features specific to you as a course provider</li>
\n					<li>Get detailed information on our plans and pricing</li>
\n				</ul>
\n
\n				<p>Let&#39;s have a no-commitment conversation that will take just a few minutes of your time.</p>
\n
\n				<h4>Over 100 course providers are using CourseCo to grow their business</h4>
\n			</div>
\n		</div>
\n
\n		<div class=\"simplebox-column simplebox-column-2 simplebox-column-custom_background\" style=\"background-color:rgb(244, 244, 244)\">
\n			<div class=\"simplebox-content\">
\n				<div class=\"simplebox-content-toolbar\"><button><img src=\"/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg\" style=\"height:12px; width:12px\" /></button></div>
\n
\n				<h2 class=\"ap-bottom\" style=\"text-align:center\">We can&#39;t wait to talk!</h2>
\n
\n				<div class=\"formrt\">{form-Contact Us}</div>
\n
\n				<p>&nbsp;</p>
\n
\n				<p>&nbsp;</p>
\n
\n				<div class=\"ap-bottom\"><img alt=\"\" src=\"/shared_media/courseco/media/photos/content/shutterstock_154059272.png\" style=\"display:block; height:734px; width:1101px\" /></div>
\n			</div>
\n		</div>
\n	</div>
\n</div>
\n
\n<p>&nbsp;</p>
\n
\n<p>&nbsp;</p>
',
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by` = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `layout_id` = (SELECT
`plugin_pages_layouts`.`id`
FROM engine_site_templates
INNER JOIN plugin_pages_layouts ON `engine_site_templates`.`id` = `plugin_pages_layouts`.`template_id`
WHERE `engine_site_templates`.`stub` = '04' and `plugin_pages_layouts`.`layout` = 'content_wide')
WHERE
  `name_tag` IN ('contact-us', 'contact-us.html')
;;


DELETE FROM `plugin_pages_pages` WHERE `name_tag` = 'about-us';;

INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'about-us',
  'About Us',
  '<section class="about\-\-section full-row padd-bottom-50">
\n	<div class="rotate-img">&nbsp;</div>
\n
\n	<div class="fix-container">
\n		<div class="diagonal simplebox">
\n			<div class="simplebox-columns">
\n				<div class="simplebox-column simplebox-column-1">
\n					<div class="simplebox-content">
\n						<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n						<h3 style="text-align:center">Who we are</h3>
\n
\n						<p style="text-align:center">Passionate people who make great software!&nbsp;We are a team of creative and skilled individuals who are determined to transform inefficiencies in companies using the latest technologies. Located in the heart of Limerick City, Ireland.&nbsp;&nbsp;<a href="/contact-us">Come talk to us</a>!</p>
\n					</div>
\n				</div>
\n
\n				<div class="simplebox-column simplebox-column-2">
\n					<div class="simplebox-content">
\n						<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n						<h3 style="text-align:center">What we do</h3>
\n
\n						<p style="text-align:center">We are a software development company.&nbsp;Over the last 10 years we have built many products including a&nbsp;powerful CMS platform. We create, integrate, and develop web applications so that our customers have flexible, affordable and scalable solutions to drive their businesses and succeed.&nbsp;</p>
\n					</div>
\n				</div>
\n
\n				<div class="simplebox-column simplebox-column-3">
\n					<div class="simplebox-content">
\n						<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n						<h3 style="text-align:center">When we started</h3>
\n
\n						<p style="text-align:center">Founded in 2008 in Limerick City, we started out as a small team. Now with almost 20 staff, we support businesses&nbsp;both nationally and internationally. We are a tried and tested software development company, come meet our team for a coffee.&nbsp;:)</p>
\n					</div>
\n				</div>
\n			</div>
\n		</div>
\n
\n		<h2 style="text-align:center">&nbsp;</h2>
\n	</div>
\n</section>
\n
\n<section class="fix-container">
\n	<div>
\n		<h2>Since 2008, Our Reputation Has Grown</h2>
\n
\n		<h3>Quick Facts</h3>
\n
\n		<ul>
\n			<li>Founded in 2008, by Michael O&#39;Callaghan, a former .NET programmer for Microsoft</li>
\n			<li>Launched a CMS PLATFORM in 2009, delivering over 100 hotel websites</li>
\n			<li>Developed retail, insurance, educational &amp; entertainment bespoke products</li>
\n			<li>Merged with Ignite Marketing Solutions in 2012 to offer key online marketing services</li>
\n			<li>Launched over 5 IB Educate Products for schools, training bodies.</li>
\n			<li>2017 developed our first IOS App for uticket.ie</li>
\n		</ul>
\n
\n		<h3>Our Approach</h3>
\n
\n		<ul>
\n			<li>We agree features by priority, so you control your budget.</li>
\n			<li>Our workshops are designed to uncover your needs.</li>
\n			<li>We load balance your solution to ensure optimal performance.</li>
\n			<li>We align your solution to model your long term business goals.</li>
\n			<li>We use certified project methodologies to ensure prompt delivery.</li>
\n			<li>Our rst meeting will include our roadmap planning session.</li>
\n			<li>We&rsquo;ve been in business for 10 years and looking forward for many more.</li>
\n		</ul>
\n
\n		<h2>Are we a good fit?</h2>
\n
\n		<p>For real results and a 5 year+ plan for your business investment, then <a href="/contact-us">get in touch</a><br />
\n		We love to work with innovative, challenging projects... we believe it&#39;s all about the person behind the business, lets win together!</p>
\n	</div>
\n
\n	<div class="diagonal simplebox">
\n		<div class="simplebox-title">
\n			<h2>Our Values</h2>
\n		</div>
\n
\n		<div class="simplebox-columns">
\n			<div class="simplebox-column simplebox-column-1">
\n				<div class="simplebox-content">
\n					<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:8px; width:12px" /></button></div>
\n
\n					<p style="text-align:center"><img alt="" src="/assets/educate/images/flexible-icon.png" style="height:82px; width:88px" /></p>
\n
\n					<p style="text-align:center"><strong>Passionate</strong><br />
\n					We love providing solutions</p>
\n				</div>
\n			</div>
\n
\n			<div class="simplebox-column simplebox-column-2">
\n				<div class="simplebox-content">
\n					<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n					<p style="text-align:center"><img alt="" src="/assets/educate/images/experts-icon.png" style="height:82px; width:77px" /></p>
\n
\n					<p style="text-align:center"><strong>Experts</strong><br />
\n					Tried &amp; Trusted Team</p>
\n				</div>
\n			</div>
\n
\n			<div class="simplebox-column simplebox-column-3">
\n				<div class="simplebox-content">
\n					<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n					<p style="text-align:center"><img alt="" src="/assets/educate/images/savings-icon.png" style="height:82px; width:66px" /></p>
\n
\n					<p style="text-align:center"><strong>Savings</strong><br />
\n					Long-term cost savings for you</p>
\n				</div>
\n			</div>
\n
\n			<div class="simplebox-column simplebox-column-4">
\n				<div class="simplebox-content">
\n					<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n					<p style="text-align:center"><img alt="" src="/assets/educate/images/established-icon.png" style="height:82px; width:73px" /></p>
\n
\n					<p style="text-align:center"><strong>Efficiency</strong><br />
\n					Fine-tune several aspects of your business</p>
\n				</div>
\n			</div>
\n		</div>
\n	</div>
\n</section>
\n',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT
`plugin_pages_layouts`.`id`
FROM engine_site_templates
INNER JOIN plugin_pages_layouts ON `engine_site_templates`.`id` = `plugin_pages_layouts`.`template_id`
WHERE `engine_site_templates`.`stub` = '04' and `plugin_pages_layouts`.`layout` = 'content_wide'),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
);;

UPDATE
  `plugin_pages_pages`
SET
  `last_modified` = CURRENT_TIMESTAMP,
  `content` = '',
  `modified_by` = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `layout_id` = (SELECT `id` FROM `plugin_pages_layouts`   WHERE `layout`   = 'pay_online' AND `deleted` = 0 LIMIT 1)
  WHERE
  `name_tag` IN ('pay-online', 'pay-online.html');;

UPDATE
  `plugin_pages_pages`
SET
  `last_modified` = CURRENT_TIMESTAMP,
  `content` = '<div class="simplebox">
 \n	<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/Banner-our-work.png" style="height:720px; width:1920px" /></p>
 \n</div>
 \n   <div class="mobile-reverse simplebox">
 \n	<div class="simplebox-title">
 \n		<p>&nbsp;</p>
 \n
 \n		<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/BS_logo1.png" style="height:69px; width:162px"></p>
 \n
 \n		<h2 style="text-align:center">Brookfield College</h2>
 \n
 \n		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
 \n	</div>
 \n
 \n	<div class="simplebox-columns">
 \n		<div class="simplebox-column simplebox-column-1">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>
 \n
 \n				<p><img alt="" src="/shared_media/ideabubble/media/photos/content/minimal-mockup.png" style="height:295px; width:440px"></p>
 \n			</div>
 \n		</div>
 \n
 \n		<div class="simplebox-column simplebox-column-2">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>
 \n
 \n				<h4 style="margin-left:80px">Key Highlights</h4>
 \n
 \n				<ul style="margin-left:80px">
 \n					<li>Content Management System</li>
 \n					<li>Responsive web template</li>
 \n					<li>3 Click Booking</li>
 \n					<li>Integrated SEA tool</li>
 \n					<li>Social Media</li>
 \n					<li>Course Advertising Tool</li>
 \n				</ul>
 \n			</div>
 \n		</div>
 \n	</div>
 \n</div>
 \n
 \n<p>&nbsp;</p>
 \n
 \n<div class="gray mobile-reverse simplebox">
 \n	<div class="simplebox-title">
 \n		<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/Kilmartins_Logos_horizontal.png" style="height:69px; width:262px"></p>
 \n
 \n		<h2 style="text-align:center">Kilmartin Education Services</h2>
 \n
 \n		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
 \n	</div>
 \n
 \n	<div class="simplebox-columns">
 \n		<div class="simplebox-column simplebox-column-1">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>
 \n
 \n				<h4 style="margin-left:80px">Key Highlights</h4>
 \n
 \n				<ul style="margin-left:80px">
 \n					<li>Bulk SMS reminder service has significantly reduced no shows,</li>
 \n					<li>40hrs saving per week on manual printing,</li>
 \n					<li>200% reduction in manual marketing efforts</li>
 \n				</ul>
 \n			</div>
 \n		</div>
 \n
 \n		<div class="simplebox-column simplebox-column-2">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>
 \n
 \n				<p><img alt="" src="/shared_media/courseco/media/photos/content/salasian_web.JPG" style="height:956px; width:1551px"><img alt="" src="/shared_media/ideabubble/media/photos/content/minimal-mockup-2.png" style="height:295px; width:440px"></p>
 \n			</div>
 \n		</div>
 \n	</div>
 \n</div>
 \n
 \n<p>&nbsp;</p>
 \n
 \n<div class="mobile-reverse simplebox">
 \n	<div class="simplebox-title">
 \n		<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/uTicket_Logo.png" style="height:69px; width:329px"></p>
 \n
 \n		<h2 style="text-align:center">uTicket</h2>
 \n
 \n		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
 \n	</div>
 \n
 \n	<div class="simplebox-columns">
 \n		<div class="simplebox-column simplebox-column-1">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>
 \n
 \n				<p><img alt="" src="/shared_media/courseco/media/photos/content/kilmartin_edco.JPG" style="height:956px; width:1545px"></p>
 \n
 \n				<p><img alt="" src="/shared_media/ideabubble/media/photos/content/minimal-mockup-3.png" style="height:295px; width:440px"></p>
 \n			</div>
 \n		</div>
 \n
 \n		<div class="simplebox-column simplebox-column-2">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>
 \n
 \n				<h4 style="margin-left:80px">Key Highlights</h4>
 \n
 \n				<ul style="margin-left:80px">
 \n					<li>View Attendance</li>
 \n					<li>Request Time-Off</li>
 \n					<li>Submit Homework</li>
 \n					<li>View Timetables</li>
 \n					<li>Make Payments</li>
 \n					<li>Manage Bookings</li>
 \n				</ul>
 \n			</div>
 \n		</div>
 \n	</div>
 \n</div>
 \n
 \n<p>&nbsp;</p>
 \n
 \n<div class="gray mobile-reverse simplebox">
 \n	<div class="simplebox-title">
 \n		<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/logo_pag1.png" style="height:69px; width:79px"></p>
 \n
 \n		<h2 style="text-align:center">Pallaskenry Agricultural College</h2>
 \n
 \n		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
 \n	</div>
 \n
 \n	<div class="simplebox-columns">
 \n		<div class="simplebox-column simplebox-column-1">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>
 \n
 \n				<h4 style="margin-left:80px">Key Highlights</h4>
 \n
 \n				<ul style="margin-left:80px">
 \n					<li>Course Discounts</li>
 \n					<li>Easy Checkout</li>
 \n					<li>Card Payment Gateway</li>
 \n					<li>Course Wishlist</li>
 \n					<li>Spread payments</li>
 \n					<li>Tiered Packages</li>
 \n					<li>Refunds and Conversations</li>
 \n				</ul>
 \n			</div>
 \n		</div>
 \n
 \n		<div class="simplebox-column simplebox-column-2">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>
 \n
 \n				<p><img alt="" src="/shared_media/courseco/media/photos/content/salasian_web.JPG" style="height:956px; width:1551px"><img alt="" src="/shared_media/ideabubble/media/photos/content/minimal-mockup-4.png" style="height:295px; width:440px"></p>
 \n
 \n				<p>&nbsp;</p>
 \n			</div>
 \n		</div>
 \n	</div>
 \n</div>
 \n
 \n<div class="mobile-reverse simplebox">
 \n	<div class="simplebox-title">
 \n		<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/STAC_First_Aid_LOGO.png" style="height:69px; width:300px"></p>
 \n
 \n		<h2 style="text-align:center">STAC First Aid</h2>
 \n
 \n		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
 \n	</div>
 \n
 \n	<div class="simplebox-columns">
 \n		<div class="simplebox-column simplebox-column-1">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>
 \n
 \n				<p><img alt="" src="/shared_media/courseco/media/photos/content/kilmartin_edco.JPG" style="height:956px; width:1545px"></p>
 \n
 \n				<p><img alt="" src="/shared_media/ideabubble/media/photos/content/minimal-mockup-5.png" style="height:295px; width:440px"></p>
 \n			</div>
 \n		</div>
 \n
 \n		<div class="simplebox-column simplebox-column-2">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>
 \n
 \n				<h4 style="margin-left:80px">Key Highlights</h4>
 \n
 \n				<ul style="margin-left:80px">
 \n					<li>Direct Messaging</li>
 \n					<li>One click contact search</li>
 \n					<li>Create contact groups</li>
 \n					<li>Easily view Documents</li>
 \n					<li>Web Chat</li>
 \n				</ul>
 \n			</div>
 \n		</div>
 \n	</div>
 \n</div>
 \n
 \n<div class="gray mobile-reverse simplebox">
 \n	<div class="simplebox-title">
 \n		<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/optional_website.png" style="height:51px; width:32px"></p>
 \n
 \n		<h2 style="text-align:center">iOS application</h2>
 \n
 \n		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
 \n	</div>
 \n
 \n	<div class="simplebox-columns">
 \n		<div class="simplebox-column simplebox-column-1" style="width:65%">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>
 \n
 \n				<h4 style="margin-left:80px">Key Highlights</h4>
 \n
 \n				<ul style="margin-left:80px">
 \n					<li>iOS Mobile App</li>
 \n					<li>Room planning</li>
 \n					<li>Exam Results</li>
 \n					<li>eLearning Platform</li>
 \n					<li>Alumni Portal</li>
 \n				</ul>
 \n
 \n				<p>&nbsp;</p>
 \n
 \n				<p>&nbsp;</p>
 \n
 \n				<p>&nbsp;</p>
 \n
 \n				<p>&nbsp;</p>
 \n			</div>
 \n		</div>
 \n
 \n		<div class="simplebox-column simplebox-column-2">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>
 \n
 \n				<p><img alt="" src="/shared_media/ideabubble/media/photos/content/minimal-mockup-6.png" style="height:329px; width:200px"><img alt="" src="/shared_media/courseco/media/photos/content/salasian_web.JPG" style="height:956px; width:1551px"><img alt="" src="/shared_media/ideabubble/media/photos/content/minimal-mockup-6-kes.png" style="height:329px; width:200px"><img alt="" src="/shared_media/ideabubble/media/photos/content/minimal-mockup-6-bc.png" style="height:330px; width:200px"></p>
 \n
 \n				<p>&nbsp;</p>
 \n			</div>
 \n		</div>
 \n	</div>
 \n</div>
 \n
 \n<div class="gray simplebox">
 \n	<div class="simplebox-title">
 \n		<h2 style="text-align:center">Let&#39;s start a new project together...</h2>
 \n	</div>
 \n
 \n	<div class="simplebox-columns">
 \n		<div class="simplebox-column simplebox-column-1">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>
 \n
 \n				<p style="text-align:center"><a class="button" href="/contact-us">Contact Us</a></p>
 \n
 \n				<p style="text-align:center">&nbsp;</p>
 \n			</div>
 \n		</div>
 \n	</div>
 \n</div>
 \n',
`modified_by` = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
`layout_id` = (SELECT
`plugin_pages_layouts`.`id`
FROM engine_site_templates
INNER JOIN plugin_pages_layouts ON `engine_site_templates`.`id` = `plugin_pages_layouts`.`template_id`
WHERE `engine_site_templates`.`stub` = '04' and `plugin_pages_layouts`.`layout` = 'content_wide')
  WHERE
  `name_tag` IN ('our-work', 'our-work.html')
;;

DELETE FROM `plugin_pages_pages` WHERE `name_tag` = 'workshop';;

INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'workshop',
  'Workshop',
  '<h6>&nbsp;</h6>
 \n
 \n<div class="simplebox simplebox-align-top simplebox-has_nested_box">
 \n	<div class="simplebox-columns">
 \n		<div class="simplebox-column simplebox-column-1" style="width:66.66667%">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
 \n
 \n				<h1><strong>Request a Workshop Today</strong></h1>
 \n
 \n				<ul>
 \n					<li>One-Day Intensive Workshop</li>
 \n					<li>Develop your business model in-line with your technical needs</li>
 \n					<li>Identify your business processes and bottlenecks</li>
 \n					<li>Explore your online roadmap to avoid unnecessary feature development</li>
 \n					<li>Consolidate vital feedback from your staff in our workshops</li>
 \n				</ul>
 \n
 \n				<h2>&nbsp;</h2>
 \n
 \n				<h2 style="text-align:center">&euro;&euro;&euro; MONEY BACK GUARANTEE &euro;&euro;&euro;</h2>
 \n
 \n				<p style="text-align:center">If you are not happy, we will refund you 100% for your Workshop.<br />
 \n				We know that our Workshop solves critical technical requirements and business workflows, therefore we guarantee 100% satisfaction!</p>
 \n			</div>
 \n		</div>
 \n
 \n		<div class="simplebox-column simplebox-column-2" style="width:33.33333%; margin-bottom: auto;">
 \n			<div class="simplebox-content workshop-buttons">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
 \n
 \n				<p><a class="btn" href="/our-work" style="width: 100%;">OUR WORK</a></p>
 \n
 \n				<p><a class="btn" href="/contact-us" style="width: 100%;">REQUEST A DEMO</a></p>
 \n			</div>
 \n		</div>
 \n	</div>
 \n</div>
 \n
 \n<p>&nbsp;</p>
 \n
 \n<div class="diagonal gray row-full simplebox simplebox-align-top">
 \n	<div class="simplebox-columns">
 \n		<div class="simplebox-column simplebox-column-1" style="width:33.33333%">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
 \n
 \n				<p style="text-align:center">&nbsp;</p>
 \n
 \n				<p style="text-align:center"><img alt="" src="/shared_media/ideabubble/media/photos/content/go-yeti-info.png" style="height:100px; width:336px" /></p>
 \n			</div>
 \n		</div>
 \n
 \n		<div class="simplebox-column simplebox-column-2" style="width:66.66667%">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
 \n
 \n				<p style="margin-top:-.25em"><img alt="" src="/shared_media/ideabubble/media/photos/content/quote-open.png" style="height:40px; width:47px" /></p>
 \n
 \n				<p>Idea Bubble tore our business apart, from our tech roadmap to our business roadmap and everything in between. Honestly, by the end of our first workshop, I actually felt like crying. However, by the end of scoping process, we had a technology roadmap and build requirement that reflected what our customers needed most, what we wanted most and what made the most clean-cut, economical sense to our business.</p>
 \n
 \n				<p><img alt="" src="/shared_media/ideabubble/media/photos/content/quote-close.png" style="float:right; height:40px; width:47px" /></p>
 \n
 \n				<p><em>&mdash;&mdash;&nbsp; <span style="font-size:18px">Rowan Copeland, Go Yeti</span></em></p>
 \n			</div>
 \n		</div>
 \n	</div>
 \n</div>
 \n
 \n<h2 style="text-align:center">&nbsp;</h2>
 \n
 \n<h2 style="text-align:center">Case studies</h2>
 \n
 \n<div class="simplebox simplebox-align-top">
 \n	<div class="simplebox-columns">
 \n		<div class="simplebox-column simplebox-column-1">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
 \n
 \n				<h3><img alt="" src="/shared_media/ideabubble/media/photos/content/case_study-kilmartin.png" style="float:left; height:180px; margin-right:30px; width:115px" />KES&nbsp;- IB Educate</h3>
 \n
 \n				<p style="margin:1em 0">Innovative and creative, Julie Kilmartin&nbsp;came to Idea Bubble to be at the forefront of the educational market online.&nbsp;</p>
 \n
 \n				<p>&nbsp;</p>
 \n			</div>
 \n		</div>
 \n
 \n		<div class="simplebox-column simplebox-column-2">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
 \n
 \n				<h3><img alt="" src="/shared_media/ideabubble/media/photos/content/case_study-uticket.png" style="float:left; height:180px; margin-right:30px; width:115px" />uTicket&nbsp;- IB Events</h3>
 \n
 \n				<p style="margin:1em 0">Exhausted from trying other solutions online, Ed came to Ideabubble&nbsp;knowing exactly what he needed. We made it happen!</p>
 \n
 \n				<p>&nbsp;</p>
 \n			</div>
 \n		</div>
 \n	</div>
 \n</div>
 \n
 \n<div class="simplebox simplebox-align-top">
 \n	<div class="simplebox-columns">
 \n		<div class="simplebox-column simplebox-column-1">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
 \n
 \n				<h3><img alt="" src="/shared_media/ideabubble/media/photos/content/navan_ballet_logo.png" style="float:left; height:101px; margin-bottom:10px; margin-right:30px; margin-top:10px; width:167px" />Navan&nbsp;School of Ballet</h3>
 \n
 \n				<p style="margin:1em 0">Eliminating over 15 pains that business owner Michelle wanted to solve with our&nbsp;Educate Plus&nbsp;Package</p>
 \n
 \n				<p>&nbsp;</p>
 \n			</div>
 \n		</div>
 \n
 \n		<div class="simplebox-column simplebox-column-2">
 \n			<div class="simplebox-content">
 \n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
 \n
 \n				<h3>&nbsp; <img alt="" src="/shared_media/ideabubble/media/photos/content/case_study-lsofm.png" style="float:left; height:180px; margin-right:30px; width:115px" />Limerick School of Music</h3>
 \n
 \n				<p style="margin:1em 0">Moving from spreadsheets to IB Educate gave Limerick School of Music one portal to manage all&nbsp;students records.</p>
 \n			</div>
 \n		</div>
 \n	</div>
 \n</div>
 \n
 \n<p>&nbsp;</p>
 \n',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT
`plugin_pages_layouts`.`id`
FROM engine_site_templates
INNER JOIN plugin_pages_layouts ON `engine_site_templates`.`id` = `plugin_pages_layouts`.`template_id`
WHERE `engine_site_templates`.`stub` = '04' and `plugin_pages_layouts`.`layout` = 'content_wide'),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
);;

DELETE FROM `plugin_menus` WHERE `category` = 'header';;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header','Lets talk',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'contact-us' LIMIT 1),'',0,0,1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

DELETE FROM `plugin_menus` WHERE `category` = 'header 1';;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','About',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'contact-us' LIMIT 1),'',1,0,1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

SET @about_header_menu_id := (SELECT `id` FROM `plugin_menus` WHERE `title` = 'about' AND `link_tag` = (SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'contact-us' LIMIT 1) LIMIT 1);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','About the company',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'about-us' LIMIT 1),'',0,@about_header_menu_id,1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Meet the team',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'our-story' LIMIT 1),'',0,@about_header_menu_id,1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Book a Workshop',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'workshop' LIMIT 1),'',0,@about_header_menu_id,1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Get a Demo',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'contact-us' LIMIT 1),'',0,@about_header_menu_id,1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Our Work',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'our-work' LIMIT 1),'',0,'0',1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','News',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'news' LIMIT 1),'',0,'0',1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Contact',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'contact-us' LIMIT 1),'',0,'0',1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','061513030',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'contact-us' LIMIT 1),'',0,'0',1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

/* MENU - Footer changes */

DELETE FROM `plugin_menus` WHERE `category` = 'footer';;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('footer','About',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'about-us' LIMIT 1),'',1,0,1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

SET @about_footer_menu_id = LAST_INSERT_ID();;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('footer','Lorem ipsum',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'privacy-policy.html' LIMIT 1),'',0,@about_footer_menu_id,1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('footer','Terms & Conditions',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'terms-and-conditions.html' LIMIT 1),'',1,0,1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

SET @terms_conditions_menu_id := (SELECT `id` FROM `plugin_menus` WHERE `title` = 'Terms & Conditions' AND `category` = 'footer');;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('footer','Privacy Policy',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'privacy-policy.html' LIMIT 1),'',0,@terms_conditions_menu_id,1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('footer','Returns & Refunds',(SELECT `id` FROM plugin_pages_pages
WHERE`name_tag` = 'privacy-policy.html' LIMIT 1),'',0,@terms_conditions_menu_id,1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

/* Changes Contact Form form */

UPDATE `plugin_formbuilder_forms`
SET `fields` = '<input type=\"hidden\" name=\"subject\" value=\"Contact form\">
<input type=\"hidden\" name=\"business_name\" value=\"\">
<input type=\"hidden\" name=\"redirect\" value=\"thank-you.html\">
<input type=\"hidden\" name=\"event\" value=\"contact-form\">
<input type=\"hidden\" name=\"trigger\" value=\"custom_form\">
<input type=\"hidden\" name=\"form_type\" value=\"Contact Form\">
<input type=\"hidden\" name=\"form_identifier\" value=\"contact_\">
<input type=\"hidden\" name=\"email_template\" value=\"contactformmail\">
<li><label for=\"contact_form_name\">Full Name*</label>
<input type=\"text\" name=\"contact_form_name\" id=\"contact_form_name\" class=\"validate[required]\"></li>
<li><label for=\"contact_form_email_address\">E-mail address*</label>
<input type=\"text\" name=\"contact_form_email_address\" id=\"contact_form_email_address\" class=\"validate[required]\">
</li><li><label for=\"contact_form_tel\">Phone number*</label>
<input type=\"text\" name=\"contact_form_tel\" id=\"contact_form_tel\" class=\"validate[required]\"></li>
<li style=\"\"><label for=\"contact_form_company\">Company</label>
<input type=\"text\" name=\"contact_form_company\" id=\"contact_form_company\"></li>
<li><label for=\"formbuilder-preview-formbuilder-preview-formbuilder-preview-formbuilder-preview-formbuilder-preview-formbuilder-preview-formbuilder-preview-submit1\"></label>
<button name=\"submit1\" id=\"formbuilder-preview-formbuilder-preview-formbuilder-preview-formbuilder-preview-formbuilder-preview-formbuilder-preview-formbuilder-preview-formbuilder-preview-submit1\" value=\"Book a Workshop\">Book a Workshop</button></li>'
WHERE `form_name` = 'Contact Us';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Idea bubble',
  `value_test`  = 'Idea bubble',
  `value_stage` = 'Idea bubble',
  `value_live`  = 'Idea bubble'
WHERE
  `variable` = 'address_line_1';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Thomcor House',
  `value_test`  = 'Thomcor House',
  `value_stage` = 'Thomcor House',
  `value_live`  = 'Thomcor House'
WHERE
  `variable` = 'addres_line_2';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Mungret Street, Limerick',
  `value_test`  = 'Mungret Street, Limerick',
  `value_stage` = 'Mungret Street, Limerick',
  `value_live`  = 'Mungret Street, Limerick'
WHERE
  `variable` = 'addres_line_3';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = '+ 353 (0)61 513030',
  `value_test`  = '+ 353 (0)61 513030',
  `value_stage` = '+ 353 (0)61 513030',
  `value_live`  = '+ 353 (0)61 513030'
WHERE
  `variable` = 'telephone';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Idea Bubble',
  `value_test`  = 'Idea Bubble',
  `value_stage` = 'Idea Bubble',
  `value_live`  = 'Idea Bubble'
WHERE
  `variable` = 'company_title';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'https://www.facebook.com/ideabubble',
  `value_test`  = 'https://www.facebook.com/ideabubble',
  `value_stage` = 'https://www.facebook.com/ideabubble',
  `value_live`  = 'https://www.facebook.com/ideabubble'
WHERE
  `variable` = 'facebook_url';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'https://twitter.com/ideabubble?lang=en',
  `value_test`  = 'https://twitter.com/ideabubble?lang=en',
  `value_stage` = 'https://twitter.com/ideabubble?lang=en',
  `value_live`  = 'https://twitter.com/ideabubble?lang=en'
WHERE
  `variable` = 'twitter_url';;
UPDATE
  `engine_settings`
SET
  `value_dev`   = 'https://ie.linkedin.com/in/ideabubble',
  `value_test`  = 'https://ie.linkedin.com/in/ideabubble',
  `value_stage` = 'https://ie.linkedin.com/in/ideabubble',
  `value_live`  = 'https://ie.linkedin.com/in/ideabubble'
WHERE
  `variable` = 'linkedin_url';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Idea Bubble',
  `value_test`  = 'Idea Bubble',
  `value_stage` = 'Idea Bubble',
  `value_live`  = 'Idea Bubble'
WHERE
  `variable` = 'company_title';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = '',
  `value_test`  = '',
  `value_stage` = '',
  `value_live`  = ''
WHERE
  `variable` = 'company_slogan';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = '04',
  `value_test`  = '04',
  `value_stage` = '04',
  `value_live`  = '04'
WHERE
  `variable` = 'template_folder_path';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'a:6:{i:0;s:7:"default";i:1;s:2:"30";i:2;s:2:"31";i:3;s:2:"32";i:4;s:10:"ideabubble";i:5;s:11:"ideabubble2";}',
  `value_test`  = 'a:6:{i:0;s:7:"default";i:1;s:2:"30";i:2;s:2:"31";i:3;s:2:"32";i:4;s:10:"ideabubble";i:5;s:11:"ideabubble2";}',
  `value_stage` = 'a:6:{i:0;s:7:"default";i:1;s:2:"30";i:2;s:2:"31";i:3;s:2:"32";i:4;s:10:"ideabubble";i:5;s:11:"ideabubble2";}',
  `value_live`  = 'a:6:{i:0;s:7:"default";i:1;s:2:"30";i:2;s:2:"31";i:3;s:2:"32";i:4;s:10:"ideabubble";i:5;s:11:"ideabubble2";}'
WHERE
  `variable` = 'available_themes';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'ideabubble2',
  `value_test`  = 'ideabubble2',
  `value_stage` = 'ideabubble2',
  `value_live`  = 'ideabubble2'
WHERE
  `variable` = 'assets_folder_path';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = '',
  `value_test`  = '',
  `value_stage` = '',
  `value_live`  = ''
WHERE
  `variable` = 'home_page_feed_1';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = '',
  `value_test`  = '',
  `value_stage` = '',
  `value_live`  = ''
WHERE
  `variable` = 'home_page_feed_2';;