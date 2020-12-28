/*
ts:2018-10-31 18:00:00
*/

/* Add the 42 (CourseCo) theme */
DELIMITER  ;;
INSERT INTO
  `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '42', '42', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_themes`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_themes` WHERE `stub` = '42')
    LIMIT 1
;;


/* Add the '42' theme styles */
UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = "@import url(\'https:\/\/fonts.googleapis.com\/css?family=Roboto:300,300i,400,400i,700,700i,900\');
\n@import url('https://fonts.googleapis.com/css?family=Quicksand:300,400,500,700');
\n
\n:root {
\n    \-\-primary: #dcde34;   \-\-primary-hover: #e0e250;   \-\-primary-active: #d0d322;
\n    \-\-secondary: #f5f5f5; \-\-secondary-hover: #d3d3d3; \-\-secondary-active: #e6e6e6;
\n    \-\-success: #37478f;   \-\-success-hover: #465ab4;   \-\-success-active: #29356a;
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
\n.page-content,
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
\n
\n.page-content h1, .banner-overlay-content h1 { font-weight: 500; color: #37478f; border: none; }
\n.page-content h2, .banner-overlay-content h2 { font-weight: 700; color: #37478f; }
\n.page-content h3, .banner-overlay-content h3 { font-weight: 700; color: #37478f; margin: .5em 0; }
\n.page-content h4, .banner-overlay-content h4 { font-weight: 400; margin: 0 0 .5em; }
\n.page-content p,  .banner-overlay-content p  { font-size: inherit;  }
\n
\n.page-content h4 { color: #37478f; }
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
\n        min-width: 250px;
\n        padding: 1em;
\n    }
\n}
\n
\n.table thead {
\n    background: #5ac9e8;
\n    color: #fff;
\n}
\n
\n.badge {
\n    background: #5ac9e8;
\n    color: #fff;
\n}
\n
\n.popup-header {
\n    background: #5ac9e8;
\n    color: #fff;
\n}
\n
\n.button.course-banner-button.cl_bg {
\n    background-color: #5ac9e8;
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
\n    color: #5ac9e8;
\n}
\n
\n.autotimetable .new_date {
\n    border-color: #5ac9e8;
\n}
\n
\n.autotimetable .new_date td:nth-child(1) {
\n    background-color: #5ac9e8;
\n    color: #fff;
\n}
\n
\n:checked + .seating-selector-checkbox-helper:after {
\n    color: #5ac9e8;
\n}
\n
\n.seating-selector-option-radio:checked + .button {
\n    background: #5ac9e8;
\n    color: #fff;
\n}
\n
\n.seating-selector-option-hover {
\n    background-color: #5ac9e8;
\n    color: #fff;
\n}
\n
\n\/\* Forms \*\/
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
\n.input_group-icon {
\n    background: #5ac9e8;
\n    color: #fff;
\n}
\n
\n.select:after {
\n    border-top-color: #5ac9e8;
\n}
\n
\n.form-select:before {
\n    background-image: -webkit-linear-gradient(left, transparent 0, transparent calc(100% - 2.75em), #5ac9e8 calc(100% - 2.75em), #5ac9e8 100%);
\n    background-image: linear-gradient(to right, transparent 0, transparent calc(100% - 2.75em), #5ac9e8 calc(100% - 2.75em), #5ac9e8 100%);
\n}
\n
\n.button,
\n.formrt button,
\n.formrt [type=\"submit\"],
\n.formrt [type=\"reset\"] {
\n    background-color: #37478f;
\n    color: #fff;
\n}
\n
\n.button.inverse {
\n    background-color: #fff;
\n    color: #37478f;
\n}
\n
\n.button\-\-continue {
\n    background-color: #5ac9e8;
\n    border-color: transparent;
\n    color: #fff;
\n}
\n
\n.button\-\-continue.inverse {
\n    background-color: #fff;
\n    border: 1px solid #5ac9e8;
\n    color: #5ac9e8;
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
\n    background-color: #5ac9e8;
\n}
\n
\n.button\-\-book.inverse {
\n    background: #FFF;
\n    border-color: #5ac9e8;
\n    color: #5ac9e8;
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
\n    color: #37478f;
\n}
\n
\n.button\-\-send.inverse {
\n    background: #37478f;
\n    border-color: #dcde34;
\n    color: #dcde34;
\n}
\n
\n.button\-\-enquire {
\n    background: #5ac9e8;
\n    color: #fff;
\n}
\n
\n.header-action:nth-child(odd) .button {
\n    background: #dcde34;
\n    border-radius: 0;
\n    color: #37478f;
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
\n    color: #37478f;
\n}
\n
\n.formErrorContent,
\n.formErrorArrow div {
\n    background: #37478f;
\n}
\n
\n.survey-question-block {
\n    color: #37478f;
\n}
\n
\n.survey-input[type=\"radio\"]:checked + .survey-input-helper {
\n    background: #dcde34;
\n    border-color: #dcde34;
\n}
\n
\n.survey-input[type=\"radio\"]:checked + .survey-input-helper:after {
\n    border-color: #37478f;
\n}
\n
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
\n.popup_box.alert-add     { border-color: #5ac9e8; }
\n.popup_box.alert-remove  { border-color: #b4b4b4; }
\n
\n.popup_box .alert-icon [fill]   {   fill: #5ac9e8; }
\n.popup_box .alert-icon [stroke] { stroke: #5ac9e8; }
\n
\n
\n\/\* Header \*\/
\n.header,
\n.mobile-breadcrumbs,
\n.dropdown-menu-header {
\n    background: #f0f0f0;
\n    color: #37478f;
\n}
\n
\n.header-logo img {
\n    height: 3.5rem;
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
\n    color: #37478f;
\n}
\n
\n.header-menu-section > a {
\n    border: none;
\n    padding: 1.847em 2em;
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
\n    color: #37478f;
\n}
\n
\n.header-menu .level_2 a:before {
\n    border-left-color: #5ac9e8;
\n}
\n
\n.header-menu .level_2 a:hover:before,
\n.header-menu .level_2:hover > a:hover {
\n    border-left-color: #198ebe;
\n}
\n
\n.header-menu .level_3 {
\n    border-bottom-color: #5ac9e8;
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
\n\/\* Quick Contact \*\/
\n@media screen and (max-width: 767px) {
\n    .quick_contact {
\n        background: #37478f;
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
\n        content: '';
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
\n        color: #5ac9e8;
\n    }
\n
\n    .quick_contact-item-icon {
\n        font-size: 1.25rem;
\n    }
\n}
\n
\n\/\* Sidebar \*\/
\n.sidebar-section > h2 {
\n    background: #5ac9e8;
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
\n.page-content li:before {
\n    content: \'\\f105\\a0\';
\n    color: #37478f;
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
\n.page-content ul > li:before {
\n    content: \'\\4e\\a0\';
\n    font-family: \'ElegantIcons\';
\n}
\n
\n.page-content hr {
\n    border-color: #bfbfbf;
\n}
\n
\n.page-content .shadow {
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
\n.simplebox.gray:before     { background: #f0f0f0; }
\n.simplebox.darkblue:before { background: #37478f; }
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
\n        content: '';
\n        background: url('/shared_media/courseco/media/photos/content/footer_curve.svg'), linear-gradient(transparent 0, transparent 30%, #dbde34 30%);
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
\n    \/\* If the user has manually changed the indentation, undo it on mobile \*\/
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
\n        background: linear-gradient(to right, transparent 0, transparent 200px, #dcde34 200px), url(\'\/shared_media\/courseco\/media\/photos\/content\/right_curve.svg\');
\n        background-size: cover;
\n        left: 50%;
\n    }
\n}
\n
\n\/\* Banner search \*\/
\n.banner-search-title {
\n    background: #2e4076;
\n    color: #fff;
\n}
\n
\n.banner-search .fa {
\n    color: #5ac9e8;
\n}
\n
\n.banner-search-title .fa {
\n    color: #fff;
\n}
\n
\n.banner-search form {
\n    background: #5ac9e8;
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
\n    content: '';
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
\n.banner-overlay-content .button,
\n.page-content .button {
\n    background: #d8e138;
\n    color: #37478f;
\n}
\n
\n.banner-overlay-content .button:nth-last-child(even),
\n.page-content .button:nth-last-child(even) {
\n    background: #5ac9e8;
\n    color: #fff;
\n}
\n
\n.banner-overlay-content .button.inverse,
\n.page-content .button.inverse {
\n    background-color: #fff;
\n    color: #37478f;
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
\n.layout-landing_page .banner-section .swiper-wrapper {
\n    clip-path: none;
\n}
\n
\n.banner-section .swiper-container {
\n    filter: drop-shadow(0px 10px 15px rgba(0,0,0,0.2));
\n}
\n
\n.layout-landing_page .banner-section .swiper-container {
\n    filter: none;
\n}
\n
\n\/\* Extend the background of the first block in the page to go behind the banner \*\/
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
\n    body:not(.layout-landing_page) .banner-slide {
\n        height: 350px;
\n    }
\n
\n    body:not(.layout-landing_page) .banner-image:not(.banner-image\-\-mobile) {
\n        background-image: none!important;
\n    }
\n
\n    .banner-overlay-content {
\n        width: calc(100% - 5rem);
\n        margin: auto;
\n        text-align: center;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .banner-section {
\n        clip-path: url(\'#banner-clippath\');
\n    }
\n
\n    .banner-image {
\n        height: 760px;
\n    }
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
\n        \/\*background-image: url(\'\/shared_media\/courseco\/media\/photos\/content\/banner_overlay_left.png\');\*\/
\n        background-position-x: left;
\n    }
\n
\n    .banner-slide\-\-right .banner-overlay .row {
\n        \/\*background-image: url(\'\/shared_media\/courseco\/media\/photos\/content\/banner_overlay_right.png\');\*\/
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
\n        margin-top: 3.5rem;
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
\n@media screen and (min-width: 768px) and (max-width: 1139px) {
\n    .banner-overlay .row {
\n        width: calc(100% - 5rem);
\n    }
\n
\n    .banner-overlay-content {
\n        max-width: 26rem;
\n    }
\n}
\n
\n@media screen and (min-width: 1140px) {
\n    .banner-overlay-content {
\n        max-width: 32rem;
\n    }
\n}
\n
\n.search-drilldown h3 {
\n    color: #5ac9e8;
\n}
\n
\n.search-drilldown-column p {
\n    color: #198ebe;
\n}
\n
\n.search-drilldown-column a.active {
\n    background-color: #5ac9e8;
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
\n        border-top-color: #5ac9e8;
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
\n\/\* Calendar \*\/
\n.eventCalendar-wrap {
\n    border-color: #bfbfbf;
\n}
\n
\n.eventsCalendar-slider {
\n    background: #5ac9e8;
\n    background: -webkit-linear-gradient(#37478f, #5ac9e8);
\n    background: linear-gradient(#37478f, #5ac9e8);
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
\n    color: #5ac9e8;
\n}
\n
\n.eventsCalendar-subtitle,
\n.eventsCalendar-list > li > time {
\n    color: #37478f;
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
\n  color: #5ac9e8;
\n}
\n
\n.news-slider-title {
\n    color: #5ac9e8;
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
\n    background-color: #37478f;
\n}
\n
\n.news-result-date {
\n    background-color: #5ac9e8;
\n    color: #FFF;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .news-result + .news-result:before {
\n        background: linear-gradient(to right, transparent 0, #5ac9e8 10%, #5ac9e8 90%, transparent 100%);
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
\n    border-color: #5ac9e8;
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
\n    color: #5ac9e8;
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
\n.panel-title h3 {
\n    font-weight: 400;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .panels-feed\-\-home_content > .column:after {
\n        background: #5ac9e8;
\n        background: linear-gradient(to right, #E6F3C8 0%, #5ac9e8 20%, #5ac9e8 80%, #E6F3C8 100%);
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
\n    background: #5ac9e8;
\n    color: #fff;
\n}
\n
\n.bar-icon svg {
\n  fill: #fff;
\n}
\n
\n.bar-text {
\n    color: #5ac9e8;
\n    font-weight: 400;
\n}
\n
\n.panel-item.has_form {
\n    background-color: #5ac9e8;
\n    color: #fff;
\n}
\n
\n.panel-item.has_form .button {
\n    background-color: #fff;
\n    border-color: #5ac9e8;
\n    color: #5ac9e8;
\n}
\n
\n.panel-item-image:after {
\n    background-image: url(\'\/shared_media\/courseco\/media\/photos\/content\/panel_overlay.png\');
\n    height: 142px;
\n    top: unset;
\n}
\n
\n.panel-item.has_image .panel-item-text {
\n    color: #5ac9e8;
\n    padding: 15px 16px 0;
\n    text-align: center;
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
\n    background: #5ac9e8;
\n    color: #fff;
\n}
\n
\n.course-list\-\-grid .course-widget-price {
\n    background-color: #5ac9e8;
\n    color: #fff;
\n}
\n
\n.course-list\-\-list .course-widget-price-original,
\n.course-list\-\-list .course-widget-price-current {
\n    color: #5ac9e8;
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
\n    background: #5ac9e8;
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
\n    color: #5ac9e8;
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
\n    border-color: #5ac9e8;
\n}
\n
\n.availability-timeslot.booked .highlight {
\n    color: #5ac9e8;
\n}
\n
\n.timeline-swiper .swiper-slide.selected {
\n    background: #f9951d;
\n    color: #fff;
\n}
\n
\n.timeline-swiper-highlight {
\n    color: #5ac9e8;
\n}
\n
\n.timeline-swiper-prev,
\n.timeline-swiper-next {
\n    color: #f9951d;
\n}
\n
\n\/\* Footer \*\/
\n.page-footer {
\n    background: #5ac9e8;
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
\n    color: #37478f;
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
\n    background: url(\'\/shared_media\/courseco\/media\/photos\/content\/footer_curve.svg\') top center no-repeat;
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
\n        background-image: url('/shared_media/courseco/media/photos/content/footer_curve.svg'), linear-gradinet(transparent 0, transparent 20%, #e0db2e 100%);
\n        background-size: 100%;
\n        top: -1.5em;
\n    }
\n}
\n
\n.footer,
\n.footer button {
\n    font-family: Quicksand, Roboto, Helvetica, Arial, sans-serif;
\n}
\n
\n.footer {
\n    background: #37478f;
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
/* Placed here to avoid a conflict. This can be merged with the above .footer later. */
\n.footer {
\n    color: #fff;
\n}
\n
\n.footer-stats-list {
\n    color: #5ac9e8;
\n}
\n
\n.footer-slogan {
\n    color: #37478f;
\n}
\n
\n.footer-stats {
\n    \/\*background: #fff url(\'\/shared_media\/courseco\/media\/photos\/content\/footer_background.svg\') top center;\*\/
\n    min-height: 0;
\n}
\n
\n.footer-stats-list h2 {
\n    color: #37478f;
\n}
\n
\n.footer-stat h2:after {
\n    border-color: #37478f;
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
\n    font-size: 1rem;
\n    margin-right: 1.5em;
\n    margin-left: 1.5em;
\n}
\n
\n.social-icon {
\n    border-radius: 0;
\n    width: 2.65rem;
\n    height: 2.65rem;
\n}
\n
\n.social-icon\-\-twitter {
\n    background-image: url(\'\/shared_media\/courseco\/media\/photos\/content\/twitter-outline.svg\');
\n}
\n
\n.social-icon\-\-facebook {
\n    background-image: url(\'\/shared_media\/courseco\/media\/photos\/content\/facebook-outline.svg\');
\n}
\n
\n.social-icon\-\-linkedin {
\n    background-image: url(\'\/shared_media\/courseco\/media\/photos\/content\/linkedin-outline.svg\');
\n}
\n
\n.footer-columns {
\n    color: #fff;
\n}
\n
\n.footer-column-title {
\n    color: #fff;
\n    display: inline-block;
\n    font-weight: 400;
\n    text-transform: uppercase;
\n}
\n
\n.footer-column h4 {
\n    font-weight: bold;
\n}
\n
/* Placed here to avoid a conflict. This can be merged with the above .footer-columns later*/
\n.footer-columns {
\n    border-top-color: #fff;
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
\n    color: #37478f;
\n}
\n
\n.footer-copyright {
\n    border-color: #fff;
\n    color: #fff;
\n    padding-top: 1rem;
\n    padding-bottom: 1rem;
\n}
\n
\n\/\* Dropdown filters \*\/
\n.search-filter-total {
\n    color: #5ac9e8;
\n}
\n
\n.search-filters :checked ~ .form-checkbox-helper,
\n.search-filters :checked ~ .form-radio-helper,
\n.search-filters :checked ~ .form-checkbox-label,
\n.search-filters :checked ~ .form-radio-label {
\n    border-color: #5ac9e8;
\n    color: #5ac9e8;
\n}
\n
\n.search-filters :checked + .form-radio-helper:after {
\n    background-color: #5ac9e8;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-filter-dropdown.filter-active > button,
\n    .search-filters-clear {
\n        color: #5ac9e8;
\n    }
\n
\n    .checkout-heading {
\n        background-color: #5ac9e8;
\n        color: #fff;
\n    }
\n}
\n
\n
\n
\n\/\* Landing page \*\/
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
\n    content: '';
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
\n    color: #37478f;
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
\n\/\* Misc \*\/
\n.contact\-\-left .ui-tabs-nav .ui-tabs-active a {
\n    background: #5ac9e8;
\n    border-color:#5ac9e8;
\n    color: #fff;
\n}
\n
\n.checkout-right-sect .btn-close:hover {
\n    color: #5ac9e8;
\n    border-color: #5ac9e8;
\n}
\n
\n.checkout-right-sect .sub-total {
\n    color: #198ebe;
\n}
\n
\n.checkout-progress li a:after {
\n    background-color: #fff;
\n    border-color: #5ac9e8;
\n}
\n
\n.checkout-progress li.curr a:after {
\n    background: #5ac9e8;
\n    background: radial-gradient(#95ced7, #5ac9e8);
\n}
\n
\n.checkout-progress li + li:before {
\n    border-color: #5ac9e8;
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
\n    background: #5ac9e8;
\n    color: #fff;
\n}
\n
\n.prepay-box h6 {
\n    color: #5ac9e8;
\n}
\n
\n.custom-calendar .booking-date-button {
\n    background-color: #5ac9e8;
\n    color: #fff;
\n}
\n
\n.custom-calendar .booking-date-button:hover {
\n    background-color: #198ebe;
\n}
\n
\n.custom-calendar button.booking-date-button.active {
\n    background-color: #fff;
\n    color: #5ac9e8;
\n}
\n
\n.course-activity-alert,
\n.details-wrap .left-place {
\n    color: #F75A5F;
\n}
\n
\n.number-of-people-viewing {
\n    color: #5ac9e8;
\n}
\n
\n.search-calendar-course-image .fa {
\n    background-color: #5ac9e8;
\n    color: #fff;
\n}
\n
\n.custom-calendar tbody td.active,
\n.custom-calendar tbody td.active:hover {
\n    background-color: #fff;
\n    color: #5ac9e8;
\n}
\n
\n.custom-calendar tbody tr:first-child td {
\n    color: #222;
\n}
\n
\n.package-offers-wrap h2 {
\n    color: #5ac9e8;
\n    border-color: #c5cecd;
\n}
\n
\n.package-offers-wrap h3 {
\n    color: #5ac9e8;
\n}
\n
\n.package-offers-wrap .summary-wrap .more,
\n.classes-details-wrap .details-wrap li:first-child {
\n    color: #5ac9e8;
\n}
\n
\n.classes-details-wrap .details-wrap li:first-child {
\n  background-color: #5ac9e8;
\n}
\n
\n.details-wrap .remove-booking,
\n.details-wrap .wishlist.remove{
\n    color: #5ac9e8;
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
\n    color: #5ac9e8;
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
\n    color: #5ac9e8;
\n}
\n
\n.details-wrap:hover li:first-child {
\n    background-color: #5ac9e8;
\n}
\n
\n.details-wrap:hover .sidelines::before,
\n.details-wrap:hover .sidelines::after,
\n.details-wrap:hover .price-wrap {
\n    border-color:#5ac9e8;
\n}
\n
\n
\n\/\* course results booked \*\/
\n.details-wrap.booked {
\n    border-color:#5ac9e8;
\n    background-color: #f3f3f3;
\n}
\n
\n.details-wrap.booked .time,
\n.details-wrap.booked .price,
\n.details-wrap.booked .fa-book {
\n    color: #5ac9e8;
\n}
\n.details-wrap.booked li:first-child {
\n    background-color: #5ac9e8;
\n}
\n
\n.details-wrap.booked .sidelines::before,
\n.details-wrap.booked .sidelines::after,
\n.details-wrap.booked .price-wrap {
\n    border-color:#5ac9e8;
\n}
\n
\n.classes-details-wrap .alert-wrap {
\n    background-color: #5ac9e8;
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
\n    color: #5ac9e8;
\n}
\n
\n.custom-calendar .booking-date-button.already_booked {
\n    background-color: #5ac9e8;
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
\n    background-image: url(\"data:image\/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M0%2C22L22%2C0l2.1%2C2.1L4.2%2C22l19.9%2C19.9L22%2C44L0%2C22L0%2C22L0%2C22z\'%20fill%3D\'%235ac9e8\'%2F%3E%3C%2Fsvg%3E\");
\n}
\n
\n.swiper-button-next {
\n    background-image: url(\"data:image\/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M27%2C22L27%2C22L5%2C44l-2.1-2.1L22.8%2C22L2.9%2C2.1L5%2C0L27%2C22L27%2C22z\'%20fill%3D\'%235ac9e8\'%2F%3E%3C%2Fsvg%3E\");
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
\n        background: #5ac9e8;
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
\n"
WHERE
  `stub` = '42'
;;


INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('sticky_header', 'Sticky header', '0', '0', '0', '0', '0', 'both', 'Make the header menu always stay on the screen as the user scrolls.', 'toggle_button', 'Website', 0, 'Model_Settings,on_or_off')
;;

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('page_footer', 'Page footer', '', '', '', '', '', 'both', 'Footer to appear on all pages throughout the site.', 'wysiwyg', 'Website', 0, '');;
