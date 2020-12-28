/*
ts:2019-04-18 08:49:00
*/

DELIMITER  ;;

/* Update the '25' (Shannonside Galvanizing) theme */
UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = '@import url(''https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i,900'');
\n
\n@import url(''https://fonts.googleapis.com/css?family=Quicksand:300,400,500,700'');
\nhtml,
\n
\nbutton {
\n
\n    font-family: Roboto, Helvetica, Arila, sans-serif;
\n
\n}
\nbody {
\n
\n    background-color: #fff;
\n
\n    color: #212121;
\n
\n}
\n
\n .page-content,
\n
\n.banner-overlay-content {
\n
\n    font-size: 1.125rem;
\n
\n}
\n
\n.banner-overlay-content h4 {
\n
\n    font-family: Roboto, Helvetica, Arial, sans-serif;
\n
\n    font-weight: 400;
\n
\n}
\n
\n .page-content h1, .banner-overlay-content h1 { font-weight: 500; color: #000000; padding-top: 10px; padding-bottom: 10px; border: 0px;}
\n .page-content h2, .banner-overlay-content h2 { font-weight: 700; color: #000000; }
\n .page-content h3, .banner-overlay-content h3 { font-weight: 700; color: #000000; margin: .5em 0; }
\n .page-content h4, .banner-overlay-content h4 { font-weight: 400; margin: 0 0 .5em; }
\n .page-content p,  .banner-overlay-content p  { font-size: inherit;  }
\n
\n .page-content h4 { color: #1c8da1; }
\n
\n .page-content > h2 {
\n        color: #1d2290;
\n  }
\n
\n.flex-col {
\n display:flex;
\n flex-wrap: wrap;
\n justify-content: space-evenly;
\n width: 100%;
\n}
\n
\n.image-features > img{
\n margin: 15px 0px;
\n}
\n
\n@media screen and (max-width: 767px) {
\n
\n    .content > .page-content {
\n        overflow: auto;
\n        padding: 0 1em;
\n
\n    }
\n    .page-content,    .banner-overlay-content    { font-size: 1rem; }
\n    .page-content h1, .banner-overlay-content h1 { font-size: 1.625rem;  line-height: 1;  margin: .25em 0; }
\n
\n    .page-content h2, .banner-overlay-content h2 { font-size: 1.375rem; line-height: 1.4; }
\n
\n    .page-content h3, .banner-overlay-content h3 { font-size: 1.125rem;}
\n
\n    .page-content h4, .banner-overlay-content h4 { font-size: 1rem;  font-weight: 400; line-height: 1.25; }
\n
\n    .page-content h5, .banner-overlay-content h5 { font-size: .9rem; font-weight: bold; }
\n
\n    .page-content h6, .banner-overlay-content h6 { font-size: .8rem; font-weight: bold; }
\n
\n    .page-content p,  .banner-overlay-content p  { font-size: inherit; line-height: 1.25; }
\n    .banner-overlay-content .button,
\n
\n   .page-content .button {
\n
\n        border-radius: 2px;
\n
\n        font-size: 1rem;
\n
\n        min-width: 150px;
\n
\n        padding: .67em;
\n
\n    }
\n
\n}
\n@media screen and (min-width: 768px) {
\n
\n    .page-content,    .banner-overlay-content    { font-size: 1.125rem; }
\n    .page-content h1, .banner-overlay-content h1 { font-size: 30px;     line-height: 1.1; }
\n
\n    .page-content h2, .banner-overlay-content h2 { font-size: 24px; line-height: 1.4; }
\n
\n    .page-content h3, .banner-overlay-content h3 { font-size: 18px; }
\n
\n    .page-content h4, .banner-overlay-content h4 { font-size: 1.5rem; line-height: 1.25; }
\n
\n    .page-content h5, .banner-overlay-content h5 { font-size: 1.375rem; }
\n
\n    .page-content h6, .banner-overlay-content h6 { font-size: 1.25rem;  }
\n
\n    .page-content p,  .banner-overlay-content p  { line-height: 1.5; }
\n    .banner-overlay-content .button,
\n
\n    .page-content .button {
\n
\n        border-radius: 5px;
\n
\n        min-width: 180px;
\n
\n    }
\n
\n}
\n.table thead {
\n
\n    background: #00375e;
\n
\n    color: #fff;
\n
\n}
\n.badge {
\n
\n    background: #00375e;
\n
\n    color: #fff;
\n
\n}
\n.popup-header {
\n
\n    background: #00375e;
\n
\n    color: #fff;
\n
\n}
\n.button.course-banner-button.cl_bg {
\n
\n    background-color: #00375e;
\n
\n}
\n.course-widget-links .button.button\-\-cl_remove {
\n
\n    background-color: #f60000;
\n
\n}
\n/* Autotimetables */
\n
\n.autotimetable tbody tr:nth-child(even) {
\n
\n    background: #f9f9f9;
\n
\n}
\n.autotimetable tbody tr td a {
\n
\n    color: #244683;
\n
\n}
\n.autotimetable tbody a:hover {
\n
\n    color: #00375e;
\n
\n}
\n.autotimetable .new_date {
\n
\n    border-color: #00375e;
\n
\n}
\n.autotimetable .new_date td:nth-child(1) {
\n
\n    background-color: #00375e;
\n
\n    color: #fff;
\n
\n}
\n:checked + .seating-selector-checkbox-helper:after {
\n
\n    color: #00375e;
\n
\n}
\n.seating-selector-option-radio:checked + .button {
\n
\n    background: #00375e;
\n
\n    color: #fff;
\n
\n}
\n.seating-selector-option-hover {
\n
\n    background-color: #00375e;
\n
\n    color: #fff;
\n
\n}
\n/* Forms */
\n
\n.formrt [type="text"],
\n
\n.formrt [type="email"],
\n
\n.formrt [type="password"],
\n
\n.formrt select,
\n
\n.formrt textarea {
\n
\n    background: #fff;
\n
\n    border: 1px solid #efefef;
\n
\n    border-radius: 2px;
\n
\n}
\n.formrt ::-webkit-input-placeholder { font-weight: 300; }
\n
\n.formrt ::-moz-placeholder          { font-weight: 300; }
\n
\n.formrt :-ms-input-placeholder      { font-weight: 300; }
\n#Contact-Us {
\n
\n     margin-bottom: auto;
\n
\n}
\nform label{
\n
\n     font-size: 1rem;
\n
\n}
\n#Contact-Us ul li label {
\n
\n     float: none;
\n
\n     width: unset;
\n
\n     display: block;
\n
\n }
\n.input_group-icon {
\n
\n    background: #00375e;
\n
\n    color: #fff;
\n
\n}
\n.select:after {
\n
\n    border-top-color: #00375e;
\n
\n}
\n.form-select:before {
\n
\n    background-image: -webkit-linear-gradient(left, transparent 0, transparent calc(100% - 2.75em), #00375e calc(100% - 2.75em), #00375e 100%);
\n
\n    background-image: linear-gradient(to right, transparent 0, transparent calc(100% - 2.75em), #00375e calc(100% - 2.75em), #00375e 100%);
\n
\n}
\n.button,
\n
\n.formrt button,
\n
\n.formrt [type="submit"],
\n
\n.formrt [type="reset"] {
\n
\n    background-color: #1d2390;
\n
\n    color: #fff;
\n
\n}
\n.button.inverse {
\n
\n    background-color: #fff;
\n
\n    color: #1c8da1;
\n
\n}
\n.button\-\-continue {
\n
\n    background-color: #00375e;
\n
\n    border-color: transparent;
\n
\n    color: #fff;
\n
\n}
\n.button\-\-continue.inverse {
\n
\n    background-color: #fff;
\n
\n    border: 1px solid #00375e;
\n
\n    color: #00375e;
\n
\n}
\n.button\-\-cancel {
\n
\n    background: #FFF;
\n
\n    border: 1px solid #F00;
\n
\n    color: #F00;
\n
\n}
\n.button\-\-pay {
\n
\n    background-color: #bfb8bf;
\n
\n}
\n.button\-\-pay.inverse {
\n
\n    background: #FFF;
\n
\n    border: 1px solid #bfb8bf;
\n
\n    color: #bfb8bf;
\n
\n}
\n.button\-\-book {
\n
\n    background-color: #00375e;
\n
\n}
\n.button\-\-book.inverse {
\n
\n    background: #FFF;
\n
\n    border-color: #00375e;
\n
\n    color: #00375e;
\n
\n}
\n.button\-\-book:disabled {
\n
\n    background-color: #888;
\n
\n}
\n.button\-\-book.inverse:disabled {
\n
\n    background-color: #fff;
\n
\n    border-color: #888;
\n
\n    color: #888;
\n
\n}
\n.button\-\-send,
\n
\n.btn-primary {
\n
\n    background: #dcde34;
\n
\n    color: #1c8da1;
\n
\n}
\n.button\-\-send.inverse {
\n
\n    background: #1c8da1;
\n
\n    border-color: #dcde34;
\n
\n    color: #dcde34;
\n
\n}
\n.button\-\-enquire {
\n
\n    background: #00375e;
\n
\n    color: #fff;
\n
\n}
\n.header-action:nth-child(odd) .button {
\n
\n    background: #1d2290;
\n
\n    border-radius: 3px;
\n    font-weight: unset;
\n}
\n.header-action:nth-child(odd) .button.active {
\n
\n    background: #fff;
\n
\n}
\n.header-action:nth-child(even) .button {
\n
\n    background: none;
\n
\n    border-color: transparent;
\n
\n    color: #222;
\n
\n    font-weight: 500;
\n
\n}
\n.header-action:nth-child(even) .button.active {
\n
\n    color: #1c8da1;
\n
\n}
\n.formErrorContent,
\n
\n.formErrorArrow div {
\n
\n    background: #1c8da1;
\n
\n}
\n.survey-question-block {
\n
\n    color: #1c8da1;
\n
\n}
\n.survey-input[type="radio"]:checked + .survey-input-helper {
\n
\n    background: #dcde34;
\n
\n    border-color: #dcde34;
\n
\n}
\n.survey-input[type="radio"]:checked + .survey-input-helper:after {
\n
\n    border-color: #1c8da1;
\n
\n}
\n/* Alerts */
\n
\n.alert-success {
\n
\n    background-color: rgb(223, 240, 216);
\n
\n    border-color: rgb(214, 233, 198);
\n
\n    color: rgb(60, 118, 61);
\n
\n}
\n.alert-info {
\n
\n    background-color: rgb(217, 237, 247);
\n
\n    border-color: rgb(188, 232, 241);
\n
\n    color: rgb(49, 112, 143);
\n
\n}
\n.alert-warning {
\n
\n    background-color: rgb(252, 248, 227);
\n
\n    border-color: rgb(250, 235, 204);
\n
\n    color: rgb(138, 109, 59);
\n
\n}
\n.alert-danger {
\n
\n    background-color: rgb(242, 222, 222);
\n
\n    border-color: rgb(235, 204, 209);
\n
\n    color: rgb(169, 68, 66);
\n
\n}
\n.popup_box { background-color: #fff; }
\n
\n.popup_box.alert-success { border-color: #8CAE38; }
\n
\n.popup_box.alert-info    { border-color: #2472AC; }
\n
\n.popup_box.alert-warning { border-color: #FCC14F; }
\n
\n.popup_box.alert-danger,
\n
\n.popup_box.alert-error   { border-color: #D74638; }
\n
\n.popup_box.alert-add     { border-color: #00375e; }
\n
\n.popup_box.alert-remove  { border-color: #b4b4b4; }
\n.popup_box .alert-icon [fill]   {   fill: #00375e; }
\n
\n.popup_box .alert-icon [stroke] { stroke: #00375e; }/* Header */
\n
\n.header,
\n
\n.mobile-breadcrumbs,
\n
\n.dropdown-menu-header {
\n
\n    background: #ffffff;
\n
\n    color: #1e2290;
\n    border-bottom: 2px solid #1d2290;
\n}
\n.breadcrumbs li a {
\n    color: #1d2290;
\n}
\n.header-logo img {
\n
\n    height: 35px;
\n
\n    max-height: none;
\n
\n}
\n.header-actions { flex: none;  margin-left: 1em; }
\n
\n.header-left { flex: 1; display: flex; }
\n
\n.header-logo { padding-right: 1.2em; }
\n.header-left .header-item > a,
\n
\n.header-action > a {
\n
\n    padding: .45em 2.2em;
\n
\n}
\n
\n.header-item .header-item.header-menu-section {
\n    margin: 0px 5px;
\n    border: solid 1px #f3f3f3;
\n    border-top: 0px;
\n    border-bottom: 0px;
\n}
\n.header-left .header-item > a {
\n
\n    color: #ffffff;
\n
\n    font-family: Quicksand, Roboto, Helvetica, Arial, sans-serif;
\n
\n    font-weight: 500;
\n
\n}
\n.header-action {
\n
\n    padding: 0;
\n
\n}
\n.header-item.header-menu-section .header-menu {
\n
\n    border: 1px solid #e9e9e9;
\n
\n    margin-top: 0px;
\n
\n}
\n.header-menu .level_2 a:hover,
\n
\n.header-menu .level_2:hover > a {
\n
\n    color: #1d2290;
\n
\n}
\n.header-item > a:not(.button) {
\n
\n    color: #000000;
\n
\n}
\n.header-item > a.active {
\n
\n    color: #1c8da1;
\n
\n}
\n.header-menu-section > a {
\n
\n    border: none;
\n
\n    padding: 1.847em 2em;
\n
\n}
\n
\n.header-menu-section > a:after {
\n
\n    border-width: .5em .333em 0;
\n    border-top-color: #1e2290;
\n    border-top-color: initial;
\n    color: #1d2290;
\n}
\n.header-menu .level_1 > a,
\n
\n.mobile-menu .level_1 > a,
\n
\n.mobile-menu .level_1 > button,
\n
\n.mobile-menu-level3-section .mobile-menu-list > a,
\n
\n.mobile-menu-toggle {
\n
\n   color: #292b95;
\n
\n   text-transform: none;
\n
\n}
\n.header-menu .level_2 a:before {
\n
\n    border-color: #ff3c31 !important;
\n
\n}
\n.header-menu .level_2 a:hover:before,
\n
\n.header-menu .level_2:hover > a:hover {
\n
\n    border-left-color: #1d2290;
\n
\n}
\n.header-menu .level_3 {
\n
\n    border-bottom-color: #00375e;
\n
\n}
\n.mobile-menu-top strong,
\n
\n.mobile-menu-top-avatar,
\n
\n.mobile-menu-button-group-icon,
\n
\n.header-cart-breakdown,
\n
\n.final_price_value {
\n
\n    color: #2e4076;
\n
\n}
\n.header-cart-amount {
\n
\n    color: #fff;
\n
\n}
\n.mobile-menu li.active > a,
\n
\n.checkout-item-title {
\n
\n    color: #d02a27;
\n
\n}
\n@media screen and (min-width: 768px) and (max-width: 1077px) {
\n
\n    body.has_sticky_header {
\n
\n        padding-top: 3.1em;
\n
\n    }
\n    .header-left .header-item > a,
\n
\n    .header-action > a {
\n
\n        font-size: .9rem;
\n
\n    }
\n    .header-left .header-item > a { padding: 1.167em .45em; }
\n
\n    .header-action > a            { padding: 1.167em 1.5em; }
\n
\n}
\n@media screen and (min-width: 1078px) {
\n
\n    .header-actions {
\n
\n        margin-left: 1.25em;
\n        margin: auto;
\n    }
\n    .header-left .header-item > a,
\n
\n    .header-action > a {
\n
\n        font-size: 1.2em;
\n
\n    }
\n    .header-left .header-item > a { padding: 1.6em 1.5em; }
\n
\n    .header-action > a            { padding: 10px 20px; }
\n
\n}
\n/* Quick Contact */
\n
\n@media screen and (max-width: 767px) {
\n
\n    .quick_contact {
\n
\n        background: #1e2290;
\n
\n        border-top: 1px solid #7888b6;
\n
\n    }
\n    .quick_contact .quick_contact-item {
\n
\n        border: none;
\n
\n        position: relative;
\n
\n    }
\n    .quick_contact-item > a {
\n
\n        color: #fff;
\n
\n    }
\n    .quick_contact-item + .quick_contact-item a:before {
\n
\n        content: '''';
\n
\n        border-left: 1px solid #7888b6;
\n
\n        position: absolute;
\n
\n        top: .25em;
\n
\n        bottom: .25em;
\n
\n        left: 0;
\n
\n    }
\n    .quick_contact-item > a.active,
\n
\n    .quick_contact-item > a:hover,
\n
\n    .quick_contact-item > a:active {
\n
\n        color: #00375e;
\n
\n    }
\n    .quick_contact-item-icon {
\n
\n        font-size: 1.25rem;
\n
\n    }
\n
\n}
\n/* Sidebar */
\n
\n.sidebar-section > h2 {
\n
\n    background: #405060;
\n
\n    color: #fff;
\n
\n}
\n
\na.sidebar-news-link,
\n
\n.eventTitle {
\n
\n    color: #242693;
\n
\n}
\n.search-criteria-remove .fa {
\n
\n    color: #f60000;
\n
\n}
\n/* Page content */
\n .page-content li:before {
\n
\n    content: ''f105a0'';
\n
\n    color: #1d2290;
\n
\n}
\n
\n .page-content a:not([class]),
\n .page-content .button\-\-link {
\n
\n    color: #262894;
\n
\n}
\n
\n .page-content a:not([class]):hover,
\n .page-content .button\-\-link:hover {
\n
\n    text-decoration: underline;
\n    font-weight: 500;
\n}
\n
\n .page-content a:not([class]):visited {
\n
\n    color: #551a8b;
\n
\n}
\n
\n .page-content ul > li:before {
\n
\n    content: ''\\4e\\a0'';
\n
\n    font-family: ''ElegantIcons'';
\n
\n}
\n
\n .page-content hr {
\n
\n    border-color: #bfbfbf;
\n
\n}
\n
\n .page-content .shadow {
\n
\n    box-shadow: 0px 1.125rem 1.6875rem rgba(0, 0, 0, .18);
\n
\n}
\n.image-hover,
\n
\n:hover > .image-unhover {display: none;}
\n
\n:hover > .image-hover {display: inline;}
\n.simplebox-title {
\n
\n    text-align: left;
\n
\n    margin-bottom: 0;
\n
\n}
\n.simplebox-content ul {
\n
\n    padding-left: 30px;
\n
\n}
\n
\n.home-stats {
\n    margin: 15px;
\n    margin-left: 25px;
\n    margin-bottom: 25px;
\n    display: flex;
\n}
\n
\n.simplebox-content .home-stats h2{
\n	margin-top: 0px;
\n	margin-bottom: 0px;
\n  line-height: 20px;
\n}
\n
\n.simplebox-content .home-stats p{
\n	margin-top: 0px;
\n	margin-bottom: 0px;
\n	margin-left: 5px;
\n  font-size: 1em;
\n}
\n.simplebox-content .page-content-text{
\n
\n font-size: 0.9em;
\n
\n}
\n
\n.home-stats img {
\n	margin-bottom: auto;
\n	margin-top: auto;
\n	margin-right: 15px;
\n}
\n
\n.home-stats-text {
\n
\n    display: inline-block;
\n}
\n
\n.home-stats-text h2{
\n    color: #1d2390;
\n}
\n
\n.simplebox.gray,
\n
\n.simplebox.darkblue,
\n
\n.simplebox.green {
\n
\n    overflow: auto;
\n
\n}
\n.simplebox.gray:before,
\n
\n.simplebox.darkblue:before,
\n
\n.simplebox.green:before {
\n
\n    content: '''';
\n
\n    position: absolute;
\n
\n    top: 0;
\n
\n    right: 0;
\n
\n    bottom: 0;
\n
\n    left: 0;
\n
\n    z-index: -1;
\n
\n}
\n.simplebox.gray:before     { background: #f4f4f4; }
\n
\n.simplebox.darkblue:before { background: #00385d; }
\n.simplebox.darkblue {
\n
\n    color: #fff;
\n
\n}
\n.simplebox.darkblue h1,
\n
\n.simplebox.darkblue h2,
\n
\n.simplebox.darkblue h3,
\n
\n.simplebox.darkblue h4,
\n
\n.simplebox.darkblue h5,
\n
\n.simplebox.darkblue h6,
\n
\n.simplebox.darkblue p {
\n
\n    color: inherit;
\n
\n}
\n@media screen and (max-width: 767px) {
\n
\n    .simplebox.green .simplebox-column:last-child,
\n
\n    .simplebox.green .simplebox-content {
\n
\n        position: relative;
\n
\n    }
\n    .simplebox.green .simplebox-column:last-child:before {
\n
\n        content: '''';
\n
\n        background: url(''/shared_media/courseco/media/photos/content/footer_curve.svg''), linear-gradient(transparent 0, transparent 30%, #dbde34 30%);
\n
\n        background-position-x: -2em;
\n
\n        background-repeat: no-repeat;
\n
\n        background-size: 100%;
\n
\n        background-size: calc(100% + 4em);
\n
\n        display: block;
\n
\n        position: absolute;
\n
\n        top: 0;
\n
\n        right: -1.2em;
\n
\n        bottom: 0;
\n
\n        left: -1.2em;
\n
\n    }
\n    /* If the user has manually changed the indentation, undo it on mobile */
\n
\n    .page-content h1[style*="margin-left"],
\n
\n    .page-content h2[style*="margin-left"],
\n
\n    .page-content h3[style*="margin-left"],
\n
\n    .page-content h4[style*="margin-left"],
\n
\n    .page-content h5[style*="margin-left"],
\n
\n    .page-content h6[style*="margin-left"],
\n
\n    .page-content p[style*="margin-left"],
\n
\n    .page-content ul[style*="margin-left"],
\n
\n    .page-content .formrt[style*="margin-left"] {
\n
\n        margin-left: 0 !important;
\n
\n    }
\n    .page-content .quote {
\n
\n        margin: 0;
\n
\n    }
\n    .page-content .quote img {
\n
\n        width: 35px;
\n
\n    }
\n    .quote ~ p {
\n
\n        padding-left: 1.5rem
\n
\n    }
\n    .quote + p {
\n
\n        margin-top:.375rem;
\n
\n    }
\n
\n}
\n@media screen and (min-width: 768px) {
\n
\n    .simplebox.green:before    {
\n
\n        background: linear-gradient(to right, transparent 0, transparent 200px, #dcde34 200px), url(''/shared_media/courseco/media/photos/content/right_curve.svg'');
\n
\n        background-size: cover;
\n
\n        left: 50%;
\n
\n    }
\n
\n    .header-right {
\n
\n         margin: auto;
\n
\n    }
\n
\n   .header-right .header-item .button{
\n         font-weight: unset;
\n         background: #ffffff;
\n         color: #000000;
\n    }
\n
\n    .header-right .header-item .button:hover {
\n
\n         color: #1d2290;
\n
\n    }
\n}
\n/* Banner search */
\n
\n.banner-search-title {
\n
\n    background: #2e4076;
\n
\n    color: #fff;
\n
\n}
\n.banner-search .fa {
\n
\n    color: #00375e;
\n
\n}
\n.banner-search-title .fa {
\n
\n    color: #fff;
\n
\n}
\n.banner-search form {
\n
\n    background: #00375e;
\n
\n}
\n.previous_search_text {
\n
\n    color: #fff;
\n
\n}
\n.banner-overlay-content form {
\n
\n    font-size: 1rem;
\n
\n    margin: 1em auto;
\n
\n    max-width: 1070px;
\n
\n}
\n.banner-overlay-content form li {
\n
\n    float: left;
\n
\n    margin-top: .5em;
\n
\n    padding: .95em;
\n
\n    width: 100%;
\n
\n}
\n.banner-overlay-content form li:nth-of-type(odd):last-child {
\n
\n    text-align: center;
\n
\n}
\n.banner-overlay-content form li input {
\n
\n    background: #fff;
\n
\n    border: none;
\n
\n    font-family: Roboto, Helevtica, Arial, sans-serif;
\n
\n    padding: 1.1em 1.25em;
\n
\n    width: 100%;
\n
\n}
\n.banner-image {
\n
\n    background-color: #f0f0f0;
\n
\n    background-repeat: no-repeat;
\n
\n}
\n.layout-landing_page .banner-image:after {
\n
\n    content: '''';
\n
\n    background: rgba(43, 76, 143, 0.75);
\n
\n    position: absolute;
\n
\n    top: 0;
\n
\n    right: 0;
\n
\n    bottom: 0;
\n
\n    left: 0;
\n
\n}
\n.layout-landing_page .banner-overlay,
\n
\n.layout-landing_page .banner-slide .banner-overlay .row {
\n
\n    background: none;
\n
\n}
\n.banner-overlay-content .button,
\n .page-content .button {
\n
\n	  margin-top: 10px;
\n
\n    background: #1e2290;
\n
\n	  color: white;
\n
\n}
\n.banner-overlay-content .button:nth-last-child(even),
\n .page-content .button:nth-last-child(even) {
\n
\n    background: #00375e;
\n
\n    color: #fff;
\n
\n}
\n.banner-overlay-content .button.inverse,
\n .page-content .button.inverse {
\n
\n    background-color: #fff;
\n
\n    color: #2f3096;
\n
\n}
\n@media screen and (max-width: 599px) {
\n
\n    .banner-overlay-content .button,
\n
\n    .page-content .button {
\n
\n        margin-bottom: .5em;
\n
\n    }
\n
\n   .simplebox-column.simplebox-column-1.flex-col > img {
\n	
\n	display:none;
\n	
\n    }
\n	
\n    .simplebox-column.simplebox-column-1.flex-col > .simplebox-content {
\n	
\n	width: 100% !important;
\n	
\n     }
\n
\n}
\n@media screen and (max-width: 359px) {
\n
\n    .banner-overlay-content .button,
\n
\n    .page-content .button {
\n
\n        width: 100%
\n
\n    }
\n
\n}
\n@media screen and (min-width: 360px) and (max-width: 599px) {
\n
\n    .banner-overlay-content .button,
\n
\n    .page-content .button {
\n
\n        width: calc(50% - .5em - 4px);
\n
\n    }
\n    .banner-overlay-content .button:only-child {
\n
\n        display: block;
\n
\n        margin-left: auto;
\n
\n        margin-right: auto;
\n
\n    }
\n    .page-content .button:only-child {
\n
\n        width: 100%;
\n
\n    }
\n    .banner-overlay-content .button + .button,
\n
\n    .page-content .button + .button {
\n
\n        margin-left: 1em;
\n
\n    }
\n
\n}
\n@media screen and (min-width: 600px) {
\n
\n    .banner-overlay-content .button + .button,
\n
\n    .page-content .button + .button {
\n
\n        margin-left: 1.5em;
\n
\n    }
\n
\n}
\n@media screen and (max-width: 767px) {
\n
\n    .banner-search-title {
\n
\n        border-bottom-color: #FFF;
\n
\n    }
\n    .banner-overlay {
\n
\n        background: rgba(255, 255, 255, .5);
\n
\n    }
\n    .banner-section\-\-has_mobile_slides {
\n
\n        background: none;
\n
\n    }
\n    .banner-overlay-content form li .button {
\n
\n        padding: 1.1em 1.25em;
\n
\n        width: 100%;
\n
\n    }
\n
\n}
\n.banner-section {
\n
\n    z-index: 1;
\n
\n}.layout-landing_page .banner-section .swiper-wrapper {
\n
\n    clip-path: none;
\n
\n}
\n.banner-section .swiper-container {
\n
\n    filter: drop-shadow(0px 10px 15px rgba(0,0,0,0.2));
\n
\n}
\n.layout-landing_page .banner-section .swiper-container {
\n
\n    filter: none;
\n
\n}
\n/* Extend the background of the first block in the page to go behind the banner */
\n
\n.has_banner .content .page-content > .simplebox:first-child {
\n
\n    margin-top: -260px;
\n
\n    padding-top: 260px;
\n
\n}
\n@media screen and (max-width: 767px) {
\n
\n    .banner-image {
\n
\n        height: 650px;
\n
\n    }
\n    .layout-landing_page .banner-image {
\n
\n        height: 500px;
\n
\n    }
\n    body:not(.layout-landing_page) .banner-image {
\n
\n        background-position: top center;
\n
\n        background-size: auto 100%;
\n
\n    }
\n    .banner-overlay-content h1 {
\n
\n        max-width: 10.5rem;
\n
\n    }
\n    .banner-overlay-content h2,
\n
\n    .banner-overlay-content h3,
\n
\n    .banner-overlay-content h4 {
\n
\n        max-width: 11.75rem;
\n
\n    }
\n    .layout-landing_page .banner-overlay-content h1,
\n
\n    .layout-landing_page .banner-overlay-content h2,
\n
\n    .layout-landing_page .banner-overlay-content h3,
\n
\n    .layout-landing_page .banner-overlay-content h4 {
\n
\n         max-width: none;
\n
\n    }
\n
\n}
\n@media screen and (min-width: 768px) {
\n
\n    .banner-section {
\n
\n        clip-path: url(''#banner-clippath'');
\n        margin-bottom: 50px;
\n    }
\n
\n    .layout-landing_page .banner-image {
\n
\n        height: 636px;
\n
\n    }
\n    .banner-overlay-content {
\n
\n        max-width: 37.5rem;
\n
\n    }
\n    .banner-overlay .row { background-repeat: no-repeat; }
\n
\n    .swiper-slide .banner-image { background-position: center; }
\n    .swiper-slide .banner-overlay {
\n
\n        background-position: top center;
\n
\n    }
\n    .banner-slide\-\-left .banner-overlay .row {
\n
\n        /*background-image: url(''/shared_media/courseco/media/photos/content/banner_overlay_left.png'');*/
\n
\n        background-position-x: left;
\n
\n    }
\n    .banner-slide\-\-right .banner-overlay .row {
\n
\n        /*background-image: url(''/shared_media/courseco/media/photos/content/banner_overlay_right.png'');*/
\n
\n        background-position-x: right;
\n
\n    }
\n    .banner-slide\-\-center .banner-overlay,
\n
\n    .banner-slide\-\-center .banner-overlay .row {
\n
\n        background: none;
\n
\n    }
\n    .banner-slide\-\-center .banner-overlay .row {
\n
\n        max-width: 1200px;
\n
\n    }
\n    .banner-slide\-\-center .banner-overlay-content {
\n
\n        max-width: 1040px;
\n
\n    }
\n    .banner-overlay-content form li {
\n
\n        width: 50%;
\n
\n    }
\n    .banner-overlay-content form li:nth-child(odd):last-child {
\n
\n        width: 100%;
\n
\n    }
\n
\n}
\n.search-drilldown h3 {
\n
\n    color: #00375e;
\n
\n}
\n.search-drilldown-column p {
\n
\n    color: #198ebe;
\n
\n}
\n.search-drilldown-column a.active {
\n
\n    background-color: #00375e;
\n
\n    color: #fff;
\n
\n}
\n@media screen and (max-width: 767px) {
\n
\n    .search-drilldown-close:before,
\n
\n    .search-drilldown-close:after {
\n
\n        background-color: #303030;
\n
\n    }
\n    .search-drilldown-column\-\-category li {
\n
\n        border-top-color: #00375e;
\n
\n    }
\n
\n}
\n@media screen and (min-width: 768px) {
\n
\n    .header-action:only-child {
\n
\n        margin-left: auto;
\n
\n    }
\n    .search-drilldown-column {
\n
\n        border-color: #198ebe;
\n
\n    }
\n
\n}
\n/* Calendar */
\n
\n.eventCalendar-wrap {
\n
\n    border-color: #bfbfbf;
\n
\n}
\n.eventsCalendar-slider {
\n
\n    background: #00375e;
\n
\n    background: -webkit-linear-gradient(#bbbbbb, #e8e8e8);
\n
\n    background: linear-gradient(#bbbbbb, #e8e8e8);
\n
\n    border-bottom-color: #bfbfbf;
\n
\n}
\n.eventsCalendar-currentTitle {
\n
\n    border-bottom-color: #000000;
\n
\n}
\n.eventsCalendar-currentTitle a {
\n
\n    color: #000000;
\n
\n}
\n.eventCalendar-wrap .arrow span {
\n
\n    border-color: #000000 ;
\n
\n}
\n.eventsCalendar-day-header,
\n
\n.eventsCalendar-daysList {
\n
\n    color: #000000;
\n
\n}
\n.eventsCalendar-day.today {
\n
\n    background-color: #1d2290  !important;
\n
\n    color: #fff;
\n
\n}
\n.eventsCalendar-subtitle,
\n
\n.eventsCalendar-list > li > time {
\n
\n    color: #2a2382;
\n
\n}
\n.eventsCalendar-list > li {
\n
\n    border-bottom-color: #bfbfbf;
\n
\n}
\n/* News feeds */
\n
\n.news-section {
\n
\n    background: #ececec;
\n
\n    box-shadow: 1px 1px 10px #ccc;
\n
\n}
\n.news-slider-link {
\n
\n  color: #262894;
\n  text-decoration: underline !important;
\n}
\n.news-slider-title {
\n
\n    color: #1d2290;
\n    margin-left: 40px;
\n
\n}
\n.swiper-pagination-bullet {
\n
\n    background-color: #1d2290;
\n
\n    border-color: #A6AEAD;
\n
\n    box-shadow: inset 0 1px 1px #aaa;
\n
\n}
\n.swiper-pagination-bullet-active {
\n
\n    background-image: linear-gradient(#dcdcdc, #f1f1f1);
\n
\n}
\n.news-result-date {
\n
\n    background-color: #00375e;
\n
\n    color: #FFF;
\n
\n}
\n@media screen and (max-width: 1023px)
\n
\n{
\n
\n    .news-result + .news-result:before {
\n
\n        background: linear-gradient(to right, transparent 0, #00375e 10%, #00375e 90%, transparent 100%);
\n
\n    }
\n
\n}
\n@media screen and (min-width: 1024px)
\n
\n{
\n
\n    .news-result + .news-result {
\n
\n        border-color: #ccc;
\n
\n    }
\n
\n}
\n
\n.news-story {
\n
\n    border-bottom: 1px solid #c7c7c7;
\n
\n}
\n
\n.news-story h1, .news-story h2{
\n
\n    border-bottom: 1px solid #c7c7c7;
\n
\n}
\n
\n.news-story-next > span {
\n
\n    color: #1e2290;
\n
\n}
\n
\n.news-story-next {
\n
\n    color: #000;
\n
\n}
\n
\n.news-story-social {
\n
\n    border-color: #00375e;
\n
\n}
\n.news-story-share_icon {
\n
\n    color: #198ebe;
\n
\n}
\n.news-story-social-link svg {
\n
\n    background: #198ebe;
\n
\n}
\n.testimonial-signature {
\n
\n    color: #00375e;
\n
\n}
\n/* Panels */
\n
\n.panel {
\n
\n    background-color: #fff;
\n
\n}
\n.carousel-section .panel {
\n
\n    border-color: #bfb8bf;
\n
\n}
\n.panel-title h3 {
\n
\n    font-weight: 400;
\n
\n}
\n@media screen and (max-width: 1023px)
\n
\n{
\n
\n    .panels-feed\-\-home_content > .column:after {
\n
\n        background: #00375e;
\n
\n        background: linear-gradient(to right, #E6F3C8 0%, #00375e 20%, #00375e 80%, #E6F3C8 100%);
\n
\n    }
\n
\n}
\n.bar {
\n
\n    background: #f3f5f5;
\n
\n    background: rgba(243, 245, 245, .8);
\n
\n    box-shadow: 0 0px 0px 1px #222222;
\n    border: 0px solid black;
\n    background-color: #f3f5f4;
\n}
\n.bar-icon {
\n    background-color: #fff;
\n    color: #fff;
\n    border-right: 1px solid black;
\n    display: flex;
\n}
\n
\n.bar-icon svg, .bar-icon img{
\n
\n  margin: 0px auto;
\n
\n
\n }
\n.bar-icon svg {
\n
\n  fill: #1b2193;
\n
\n}
\n.bar-text {
\n
\n    color: #000000;
\n
\n    font-weight: 400;
\n
\n}
\n.panel-item.has_form h4{
\n
\n    color: #1e2290;
\n
\n}
\n.panel-item.has_form form {
\n
\n    color: #464646;
\n
\n}
\n.panel-item.has_form .button {
\n
\n    background-color: #1e2290;
\n
\n    border-color: #1e2290;
\n    font-weight: unset;
\n    color: #fff;
\n
\n}
\n.panel-item-image:after {
\n
\n    background-image: url(''/shared_media/courseco/media/photos/content/panel_overlay.png'');
\n
\n    height: 142px;
\n
\n    top: unset;
\n
\n}
\n.panel-item.has_image .panel-item-text {
\n
\n    color: #00375e;
\n
\n    padding: 15px 16px 0;
\n
\n    text-align: center;
\n
\n}
\n.side-form {
\n    background-color: #ececec;
\n    padding: 10px;
\n    }
\n
\n
\n/* Search results */
\n
\n.course-list-header {
\n
\n    border-bottom-color: #B7B7B7;
\n
\n}
\n.course-list-display-option:after {
\n
\n    background: #d0d0d0;
\n
\n}
\n.course-list\-\-grid .course-widget {
\n
\n    border-color: #bfbfbf;
\n
\n}
\n.course-widget-category {
\n
\n    background: #00375e;
\n
\n    color: #fff;
\n
\n}
\n.course-list\-\-grid .course-widget-price {
\n
\n    background-color: #00375e;
\n
\n    color: #fff;
\n
\n}
\n.course-list\-\-list .course-widget-price-original,
\n
\n.course-list\-\-list .course-widget-price-current {
\n
\n    color: #00375e;
\n
\n}
\n.course-list-grid .course-widget-time_and_date {
\n
\n    border-color: #b7b7b7;
\n
\n}
\n.course-list\-\-list .course-widget-location_and_tags {border-color: #CCC; }
\n.pagination-prev a,
\n
\n.pagination-next a {
\n
\n    background: #198ebe;
\n
\n    color: #fff;
\n
\n}
\n.pagination-prev a:before,
\n
\n.pagination-next a:before {
\n
\n    border-color: #fff;
\n
\n}
\n.course-banner-overlay {
\n
\n    background-color: rgba(25, 142, 190, .8);
\n
\n    color: #fff;
\n
\n}
\n.fixed_sidebar-header {
\n
\n    background: #198ebe;
\n
\n    color: #fff;
\n
\n}
\n.booking-form h2 {
\n
\n    border: none;
\n
\n}
\n.booking-required_field-note {
\n
\n    color: #00375e;
\n
\n}
\n@media screen and (max-width: 767px) {
\n
\n    .contact-map-overlay {
\n
\n        background-color: #198ebe;
\n
\n    }
\n
\n}
\n@media screen and (min-width: 768px) {
\n
\n    .contact-map-overlay-content {
\n
\n        background: #198ebe;
\n
\n        background: rgba(25, 142, 190, .8);
\n
\n    }
\n
\n}
\n.availability-timeslot .highlight {
\n
\n    color: #f9951d;
\n
\n}
\n.availability-timeslot.booked {
\n
\n    border-color: #00375e;
\n
\n}
\n.availability-timeslot.booked .highlight {
\n
\n    color: #00375e;
\n
\n}
\n.timeline-swiper .swiper-slide.selected {
\n
\n    background: #f9951d;
\n
\n    color: #fff;
\n
\n}
\n.timeline-swiper-highlight {
\n
\n    color: #00375e;
\n
\n}
\n.timeline-swiper-prev,
\n
\n.timeline-swiper-next {
\n
\n    color: #f9951d;
\n
\n}
\n/* Footer */
\n
\n.page-footer {
\n
\n    background: #ffffff;
\n
\n    color: #000000;
\n
\n    overflow-x: hidden;
\n
\n    overflow-y: auto;
\n
\n    position: relative;
\n
\n}
\n.layout-landing_page .page-footer {
\n
\n    display: none;
\n
\n}
\n.page-footer-bottom {
\n
\n    color: #1c8da1;
\n
\n    padding-top: 7em;
\n
\n    padding-bottom: 3em;
\n
\n    position: relative;
\n
\n    z-index: 0;
\n
\n}
\n.page-footer .page-content h1,
\n
\n.page-footer .page-content h2,
\n
\n.page-footer .page-content h3,
\n
\n.page-footer .page-content h4,
\n
\n.page-footer .page-content h5,
\n
\n.page-footer .page-content h6,
\n
\n.page-footer .page-content p {
\n
\n    color: inherit;
\n
\n}
\n.page-footer-bottom:before {
\n
\n    content: '''';
\n
\n    background: #e0db2e;
\n
\n    background: url(''/shared_media/courseco/media/photos/content/footer_curve.svg'') top center no-repeat;
\n
\n    position: absolute;
\n
\n    top: -3.5em;
\n
\n    right: -50vw;
\n
\n    bottom: 0px;
\n
\n    left: -50vw;
\n
\n    z-index: -1;
\n
\n}
\n.row.gutters .footer-column.has_sublist {
\n
\n    padding-left: 50px;
\n
\n    padding-right: 50px;
\n
\n}
\n@media screen and (max-width: 599px) {
\n
\n    .page-footer-bottom {
\n
\n        padding-top: 3rem;
\n
\n    }
\n
\n}
\n@media screen and (max-width: 767px) {
\n
\n    .page-footer .simplebox-columns {
\n
\n        display: flex;
\n
\n        flex-wrap: wrap;
\n
\n        text-align: center;
\n
\n    }
\n    .page-footer .simplebox-column {
\n
\n        margin: 0 auto;
\n
\n        max-width: 25%;
\n
\n    }
\n    .page-footer-bottom:before {
\n
\n        background-image: url(''/shared_media/courseco/media/photos/content/footer_curve.svg''), linear-gradinet(transparent 0, transparent 20%, #e0db2e 100%);
\n
\n        background-size: 100%;
\n
\n        top: -1.5em;
\n
\n    }
\n
\n}
\n.footer {
\n
\n    background: #ececec;
\n
\n    margin-top: 0;
\n
\n}
\n.footer:after {
\n
\n    content: '''';
\n
\n    clear: both;
\n
\n    display: table;
\n
\n}
\n.footer-logo img {
\n
\n    width: 100%;
\n
\n    max-width: 500px;
\n
\n}
\n.footer-stats-list {
\n
\n    color: #00375e;
\n
\n}
\n.footer-slogan {
\n
\n    color: #1c8da1;
\n
\n}
\n.footer-stats {
\n
\n    /*background: #fff url(''/shared_media/courseco/media/photos/content/footer_background.svg'') top center;*/
\n
\n    min-height: 0;
\n
\n}
\n.footer-stats-list h2 {
\n
\n    color: #1c8da1;
\n
\n}
\n.footer-stat h2:after {
\n
\n    border-color: #1c8da1;
\n
\n}
\n.footer-social {
\n
\n    color: #343699;
\n
\n    padding: 0;
\n
\n}
\n.footer-social .row {
\n
\n    padding: 1.2rem 0;
\n
\n}
\n.footer-social h2 {
\n
\n    font-size: 1.8rem;
\n
\n    margin-right: 1.5em;
\n
\n    margin-left: 1.5em;
\n
\n    margin-bottom: 0px;
\n
\n    margin-top: 0px;
\n
\n}
\n.social-icon {
\n
\n    border-radius: 0;
\n
\n    width: 2.65rem;
\n    background-image: unset;
\n    height: 2.65rem;
\n
\n}
\n.social-icon\-\-twitter {
\n
\n    background-image: url(/shared_media/shannonside/media/photos/content/twitter_social2.svg);
\n
\n}
\n.social-icon\-\-facebook {
\n
\n    background-image: url(/shared_media/shannonside/media/photos/content/facebook_social2.svg);
\n
\n}
\n.social-icon\-\-linkedin {
\n
\n    background-image: url(/shared_media/shannonside/media/photos/content/linkedin_social2.svg);
\n
\n}
\n
\n.social-icon svg {
\n    fill: #343699;
\n}
\n
\n.social-icon svg path{
\n    fill: #343699 !important;
\n}
\n
\n.footer-columns {
\n
\n    background: none;
\n
\n    color: #000000;
\n
\n}
\n.footer-column-title {
\n
\n    color: #1d2290;
\n
\n    font-weight: 400;
\n
\n    text-transform: uppercase;
\n
\n}
\n.footer-column {
\n
\n    width: auto;
\n
\n}
\n.footer-column h4 {
\n
\n    font-weight: bold;
\n
\n}
\n.footer .form-input::-webkit-input-placeholder { color: #7a7a7a; font-weight: 300; }
\n
\n.footer .form-input::-moz-placeholder          { color: #7a7a7a; font-weight: 300; }
\n
\n.footer .form-input:-ms-input-placeholder      { color: #7a7a7a; font-weight: 300; }
\n.newsletter-signup-form input[type="text"] {
\n
\n   border: 1px solid #dfdfdf;
\n   color: #fff;
\n
\n}
\n.newsletter-signup-form .button {
\n    background-color: #1d2290;
\n    color: #ffffff;
\n}
\n.footer-copyright {
\n
\n    background: none;
\n
\n    color: #626262;
\n
\n    padding-top: 1.75rem;
\n
\n    padding-bottom: 1rem;
\n
\n}
\n.footer-copyright .row {
\n
\n}
\n.footer-copyright-cms {
\n
\n	margin-left: auto
\n
\n    }
\n.simplebox-content.workshop-buttons {
\n
\n    text-align: center;
\n
\n}
\n.workshop-buttons p {
\n
\n    background-color: #008499;
\n
\n}
\n.workshop-buttons p a {
\n
\n    display: inline-block;
\n
\n    padding: 10px;
\n
\n    color: #ffffff;
\n
\n}
\n@media screen and (max-width: 767px) {
\n
\n    .menu\-\-header1 li.level_1 {
\n
\n    padding-top: 32px;
\n
\n    }
\n    .footer-column-title {
\n
\n        display: block;
\n
\n        font-size: 1rem;
\n
\n        font-weight: 500;
\n
\n        padding: .25em 0 .3125em;
\n
\n    }
\n    .footer-column:not(.has_sublist) {
\n
\n        padding: 0;
\n
\n    }
\n    .footer-copyright .row {
\n
\n        line-height: 1.75;
\n
\n        max-width: 10.8rem;
\n
\n    }
\n
\n}@media screen and (min-width: 768px) {
\n	.menu\-\-header1 li.level_1 {
\n
\n		padding-right: 2em;
\n
\n		padding-left: 2em;
\n
\n		display: inline-block;
\n
\n		padding-top: 17px !important;
\n
\n	}
\n	ul.menu\-\-header1 {
\n
\n	    display: flex;
\n
\n	    width: 100%;
\n
\n	    justify-content: center;
\n
\n	}
\n    .footer-column-title {
\n
\n	      white-space: nowrap;
\n		
\n        font-size: .1.2rem;
\n
\n        font-weight: 500;
\n
\n    }
\n
\n}
\n@media screen and (min-width: 1040px) {
\n
\n    .footer-columns .container {
\n
\n        display: flex;
\n
\n	justify-content: center;
\n
\n    }
\n    .footer-copyright .row {
\n
\n        display: flex;
\n
\n    }
\n}
\n@media screen and (max-width: 1077px) {
\n
\n    .footer-columns,
\n
\n    .footer-columns .container,
\n
\n    .footer-copyright,
\n
\n    .footer-copyright .row { width: auto; }
\n    .footer-copyright {
\n
\n        float: right;
\n
\n    }
\n    .footer-columns .container {padding-left: 19px;}
\n
\n    .footer-copyright .row {padding-right: 19px;}
\n
\n}
\n@media screen and (min-width: 1078px) {
\n}/* Dropdown filters */
\n
\n.search-filter-total {
\n
\n    color: #00375e;
\n
\n}
\n.search-filters :checked ~ .form-checkbox-helper,
\n
\n.search-filters :checked ~ .form-radio-helper,
\n
\n.search-filters :checked ~ .form-checkbox-label,
\n
\n.search-filters :checked ~ .form-radio-label {
\n
\n    border-color: #00375e;
\n
\n    color: #00375e;
\n
\n}
\n.search-filters :checked + .form-radio-helper:after {
\n
\n    background-color: #00375e;
\n
\n}
\n@media screen and (min-width: 768px) {
\n
\n    .search-filter-dropdown.filter-active > button,
\n
\n    .search-filters-clear {
\n
\n        color: #00375e;
\n
\n    }
\n    .checkout-heading {
\n
\n        background-color: #00375e;
\n
\n        color: #fff;
\n
\n    }
\n
\n}
\n
\n/* Landing page */
\n
\n.layout-landing_page .header-right,
\n
\n.layout-landing_page .header-menu-section {
\n
\n    display:none;
\n
\n}
\n.layout-landing_page .banner-overlay-content {
\n
\n    color: #fff;
\n
\n}
\n.layout-landing_page .banner-overlay-content h1,
\n
\n.layout-landing_page .banner-overlay-content h2,
\n
\n.layout-landing_page .banner-overlay-content h3,
\n
\n.layout-landing_page .banner-overlay-content h4,
\n
\n.layout-landing_page .banner-overlay-content h5,
\n
\n.layout-landing_page .banner-overlay-content h6,
\n
\n.layout-landing_page .banner-overlay-content p {
\n
\n    color: inherit;
\n
\n}
\n.layout-landing_page .banner-overlay-content h1 {
\n
\n    margin: .13em 0;
\n
\n}
\n.layout-landing_page .simplebox.gray:before {
\n
\n    content: '''';
\n
\n    background-color: #ccc;
\n
\n    background-image: url(/shared_media/courseco/media/photos/content/ib_logo_circle.png);
\n
\n    background-repeat: no-repeat;
\n
\n    background-position: 130% 30%;
\n
\n    background-size: 800px;
\n
\n    opacity: .25;
\n
\n    position: absolute;
\n
\n    top: 0;
\n
\n    right: 0;
\n
\n    bottom: 0;
\n
\n    left: 0;
\n
\n    z-index: -1;
\n
\n}.layout-landing_page .simplebox.darkblue ul {
\n
\n    max-width: 14em;
\n
\n    margin: auto;
\n
\n    font-weight: 500;
\n
\n}
\n.layout-landing_page .simplebox.darkblue ul li {
\n
\n    margin: 0;
\n
\n}
\n.layout-landing_page .simplebox.darkblue ul li:before {
\n
\n    background: #fff;
\n
\n    border-radius: 50%;
\n
\n    font-size: .5em;
\n
\n    color: #1c8da1;
\n
\n    margin-top: .75em;
\n
\n    padding: .25em .5em;
\n
\n    text-align: center;
\n
\n    width: 2em;
\n
\n    height: 2em;
\n
\n}@media screen and (min-width: 768px) {
\n
\n    .layout-landing_page .simplebox.darkblue ul {
\n
\n        font-size: 2.25em;
\n
\n        max-width: 14em;
\n
\n        margin: auto;
\n
\n        font-weight: 500;
\n
\n    }
\n
\n}
\n@media screen and (min-width: 992px) {
\n
\n    .layout-landing_page .page-content-banner-overlay .row {
\n
\n        background: rgba(90, 201, 232, .5);
\n
\n    }
\n
\n}
\n/* Misc */
\n
\n.contact\-\-left .ui-tabs-nav .ui-tabs-active a {
\n
\n    background: #00375e;
\n
\n    border-color:#00375e;
\n
\n    color: #fff;
\n
\n}
\n.checkout-right-sect .btn-close:hover {
\n
\n    color: #00375e;
\n
\n    border-color: #00375e;
\n
\n}
\n.checkout-right-sect .sub-total {
\n
\n    color: #198ebe;
\n
\n}
\n.checkout-progress li a:after {
\n
\n    background-color: #fff;
\n
\n    border-color: #00375e;
\n
\n}
\n.checkout-progress li.curr a:after {
\n
\n    background: #00375e;
\n
\n    background: radial-gradient(#95ced7, #00375e);
\n
\n}
\n.checkout-progress li + li:before {
\n
\n    border-color: #00375e;
\n
\n}
\n.checkout-progress .curr ~ li:before {
\n
\n    border-color: #c8c8c8;
\n
\n}
\n.search-package-available h2 {
\n
\n    color: #4f4e4f;
\n
\n}
\n.search-package-available .available-text  h4 {
\n
\n    border-color: #eee;
\n
\n    color: #198ebe;
\n
\n}
\n.search-package-available .show-more {
\n
\n    background: #00375e;
\n
\n    color: #fff;
\n
\n}
\n.prepay-box h6 {
\n
\n    color: #00375e;
\n
\n}
\n.custom-calendar .booking-date-button {
\n
\n    background-color: #00375e;
\n
\n    color: #fff;
\n
\n}
\n.custom-calendar .booking-date-button:hover {
\n
\n    background-color: #198ebe;
\n
\n}
\n.custom-calendar button.booking-date-button.active {
\n
\n    background-color: #fff;
\n
\n    color: #00375e;
\n
\n}
\n.course-activity-alert,
\n
\n.details-wrap .left-place {
\n
\n    color: #F75A5F;
\n
\n}
\n.number-of-people-viewing {
\n
\n    color: #00375e;
\n
\n}
\n.search-calendar-course-image .fa {
\n
\n    background-color: #00375e;
\n
\n    color: #fff;
\n
\n}
\n.custom-calendar tbody td.active,
\n
\n.custom-calendar tbody td.active:hover {
\n
\n    background-color: #fff;
\n
\n    color: #00375e;
\n
\n}
\n.custom-calendar tbody tr:first-child td {
\n
\n    color: #222;
\n
\n}
\n.package-offers-wrap h2 {
\n
\n    color: #00375e;
\n
\n    border-color: #c5cecd;
\n
\n}
\n.package-offers-wrap h3 {
\n
\n    color: #00375e;
\n
\n}
\n.package-offers-wrap .summary-wrap .more,
\n
\n.classes-details-wrap .details-wrap li:first-child {
\n
\n    color: #00375e;
\n
\n}
\n.classes-details-wrap .details-wrap li:first-child {
\n
\n  background-color: #00375e;
\n
\n}
\n.details-wrap .remove-booking,
\n
\n.details-wrap .wishlist.remove{
\n
\n    color: #00375e;
\n
\n}
\n.sidelines:before,
\n
\n.sidelines:after,
\n
\n.details-wrap .price-wrap {
\n
\n    border-color: #e4e4e4;
\n
\n}
\n.details-wrap .time,
\n
\n.details-wrap .price,
\n
\n.details-wrap .fa-book {
\n
\n    color: #00375e;
\n
\n}
\n/* course results hover */
\n
\n.details-wrap:hover {
\n
\n    background-color: #f9f9f9;
\n
\n    border-color:#d8d8d8 ;
\n
\n}
\n.details-wrap:hover .time,
\n
\n.details-wrap:hover .price,
\n
\n.details-wrap:hover .fa-book {
\n
\n    color: #00375e;
\n
\n}
\n.details-wrap:hover li:first-child {
\n
\n    background-color: #00375e;
\n
\n}
\n.details-wrap:hover .sidelines::before,
\n
\n.details-wrap:hover .sidelines::after,
\n
\n.details-wrap:hover .price-wrap {
\n
\n    border-color:#00375e;
\n
\n}/* course results booked */
\n
\n.details-wrap.booked {
\n
\n    border-color:#00375e;
\n
\n    background-color: #f3f3f3;
\n
\n}
\n.details-wrap.booked .time,
\n
\n.details-wrap.booked .price,
\n
\n.details-wrap.booked .fa-book {
\n
\n    color: #00375e;
\n
\n}
\n
\n.details-wrap.booked li:first-child {
\n
\n    background-color: #00375e;
\n
\n}
\n.details-wrap.booked .sidelines::before,
\n
\n.details-wrap.booked .sidelines::after,
\n
\n.details-wrap.booked .price-wrap {
\n
\n    border-color:#00375e;
\n
\n}
\n.classes-details-wrap .alert-wrap {
\n
\n    background-color: #00375e;
\n
\n    color: #fff;
\n
\n}
\n.custom-slider-arrow a {
\n
\n    color: #0e2a6b;
\n
\n}
\n.search_courses_right:hover,
\n
\n.search_courses_left:hover,
\n
\n.arrow-left.for-time-slots:hover,
\n
\n.arrow-right.for-time-slots:hover{
\n
\n    color: #00375e;
\n
\n}
\n.custom-calendar .booking-date-button.already_booked {
\n
\n    background-color: #00375e;
\n
\n    color: #fff;
\n
\n}
\n.search_history > a {
\n
\n    color: #fff;
\n
\n}
\n.search_history .remove_search_history {
\n
\n    color: #fff;
\n
\n    border-color: #fff;
\n
\n}
\n.swiper-button-prev {
\n
\n    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D''http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg''%20viewBox%3D''0%200%2027%2044''%3E%3Cpath%20d%3D''M0%2C22L22%2C0l2.1%2C2.1L4.2%2C22l19.9%2C19.9L22%2C44L0%2C22L0%2C22L0%2C22z''%20fill%3D''%2300375e''%2F%3E%3C%2Fsvg%3E");
\n
\n}
\n.swiper-button-next {
\n
\n    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D''http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg''%20viewBox%3D''0%200%2027%2044''%3E%3Cpath%20d%3D''M27%2C22L27%2C22L5%2C44l-2.1-2.1L22.8%2C22L2.9%2C2.1L5%2C0L27%2C22L27%2C22z''%20fill%3D''%2300375e''%2F%3E%3C%2Fsvg%3E");
\n
\n}
\nbody > div > img {
\n
\n  display: block;
\n
\n}
\n.submit-expand {
\n
\n    background: none;
\n
\n    border: none;
\n
\n}
\n.content {
\n
\n    margin-top: 0;
\n
\n}
\n/* Hack, for now, to get text from the page editor to overlay the banner */
\n
\n@media screen and (max-width: 991px) {
\n
\n    .page-content-banner-overlay {
\n
\n        background: #00375e;
\n
\n        text-align: center;
\n
\n        color: #fff;
\n
\n        margin-left: -2em;
\n
\n        margin-right: -2em;
\n
\n        padding: 1em;
\n
\n    }
\n
\n}
\n@media screen and (min-width: 992px) {
\n
\n    .content {
\n
\n        position: relative;
\n
\n    }
\n    .page-content-banner-overlay {
\n
\n        position: absolute;
\n
\n        top: -184px;
\n
\n        left: 0;
\n
\n        right: 0;
\n
\n        text-align: center;
\n
\n        z-index: 1;
\n
\n    }
\n    .page-content-banner-overlay .row {
\n
\n        background: rgba(0, 56, 93, .5);
\n
\n        border-radius: 2em 2em 0 0;
\n
\n        color: #fff;
\n
\n        height: 160px;
\n
\n        padding-top: .1em;
\n
\n    }
\n    .page-content-banner-overlay .row * {
\n
\n        color: inherit;
\n
\n    }
\n    .page-content-banner-overlay .simplebox {
\n
\n        padding-left: 1em;
\n
\n        padding-right: 1em;
\n
\n    }
\n    .page-content-banner-overlay .simplebox-title {
\n
\n        margin-top: 0;
\n
\n        margin-bottom: 0;
\n
\n    }
\n    .page-content-banner-overlay .simplebox-title h4 {
\n
\n        margin: .5em 0;
\n
\n    }
\n    .page-content-banner-overlay .simplebox-content p {
\n
\n        display: flex;
\n
\n        align-items: center;
\n
\n        justify-content: space-around;
\n
\n    }
\n    .page-content-banner-overlay .simplebox-content img {
\n
\n        margin: 0 1em;
\n
\n    }
\n
\n}',
`template_id` = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '04')
WHERE
  `stub` = '25'
;;

UPDATE `plugin_pages_pages`
SET
`content` = '
\n <div class="simplebox">
\n    <div class="simplebox-columns">
\n        <div class="flex-col simplebox-column simplebox-column-1" style="margin-bottom:auto; width:70%">
\n         <img alt=""src="/shared_media/shannonside/media/photos/content/shannonside_home_landing_logo.png" style="width: 42%; height: 100% !important;"/>
\n            <div class="simplebox-content" style="padding:0px 15px; width: 58%;">
\n                <div class="simplebox-content-toolbar">
\n                    <button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg"
\n                                 style="height:12px; width:12px"/></button>
\n                </div>
\n                <h1 style="margin-top:0px">Complete Coating Systems</h1>
\n                <p class="page-content-text">With over 20 years experience specializing in hot dip galvanizing, shot blasting &amp; the
\n                    application of protective ceilings, Shannonside Galvanizing is one of Ireland&#39;s leading
\n                    Galvanizing plants located in Drombana, Co. Limerick.</p></div>
\n            <p class="flex-col image-features"><img alt=""
\n                                                    src="/shared_media/shannonside/media/photos/content/ellipse1.png"
\n                                                    style="height:135px !important; width:135px"/><img alt=""
\n                                                                                            src="/shared_media/shannonside/media/photos/content/ellipse2.png"
\n                                                                                            style="height:135px !important; width:135px"/><img
\n                        alt="" src="/shared_media/shannonside/media/photos/content/ellipse3.png"
\n                        style="height:135px !important; width:135px"/><img alt=""
\n                                                                src="/shared_media/shannonside/media/photos/content/ellipse4.png"
\n                                                                style="height:135px !important; width:135px"/></p></div>
\n        <div class="simplebox-column simplebox-column-2 simplebox-column-custom_background"
\n             style="background-color:rgb(236, 236, 236); margin-bottom:auto; width:30%">
\n            <div class="simplebox-content">
\n                <div class="simplebox-content-toolbar">
\n                    <button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg"
\n                                 style="height:12px; width:12px"/></button>
\n                </div>
\n                <div class="home-stats"><img alt="" src="/shared_media/shannonside/media/photos/content/cert.png"
\n                                             style="height:45px; width:30px"/>
\n                    <div class="home-stats-text"><h2>4586</h2>
\n                        <p>Years lifetime protection</p></div>
\n                </div>
\n                <div class="home-stats"><img alt="" src="/shared_media/shannonside/media/photos/content/years.png"
\n                                             style="height:49px; width:30px"/>
\n                    <div class="home-stats-text"><h2>4586</h2>
\n                        <p>ISO certificates</p></div>
\n                </div>
\n                <div class="home-stats"><img alt="" src="/shared_media/shannonside/media/photos/content/staff.png"
\n                                             style="height:49px; width:40px"/>
\n                    <div class="home-stats-text"><h2>40</h2>
\n                        <p>Staff</p></div>
\n                </div>
\n                <div class="home-stats"><img alt="" src="/shared_media/shannonside/media/photos/content/bathtub.png"
\n                                             style="height:49px; width:45px"/>
\n                    <div class="home-stats-text"><h2>8</h2>
\n                        <p>Largest galvanizing baths</p></div>
\n                </div>
\n            </div>
\n        </div>
\n    </div>
\n</div>
\n<h1 style="text-align: center; border: 0px;">What we offer</h1>',
`banner_photo` = '1|../content/banner_12.png|-1',
`layout_id` = (SELECT `id` from plugin_pages_layouts WHERE `layout` = 'home_page_content_above')
WHERE
  `name_tag` IN ('home.html', 'home');;

UPDATE `plugin_pages_pages`
SET
`banner_photo` = '1|../content/banner_content1.png|-1'
WHERE
  `name_tag` = 'quality-and-certification';;

UPDATE `plugin_pages_pages`
SET
`banner_photo` = '1|../content/banner_content3.png|-1',
`layout_id` = (SELECT `id` FROM plugin_pages_layouts where `layout` = 'content_wide' LIMIT 1)
WHERE
  `name_tag` = 'projects';;

UPDATE `plugin_pages_pages`
SET
`banner_photo` = '1|../content/banner_content3.png|-1',
`layout_id` = (SELECT `id` FROM plugin_pages_layouts where `layout` = 'news' LIMIT 1),
`content` = ''
WHERE
  `name_tag` IN ('news', 'news.html');;

UPDATE `plugin_pages_pages`
SET
`content` = '<div class="simplebox"><img alt="" src="/shared_media/shannonside/media/photos/content/banner_21.png" style="height:600px; width:1904px"></div>

<div class="simplebox">
	<div class="simplebox-title">
		<h2 style="text-align:center">Contact Us</h2>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="margin-bottom:auto">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div class="formrt">{form-Contact Us}</div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width:50%">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="" src="/shared_media/shannonside/media/photos/content/bc_panel_1.png" style="height:190px; width:285px"><img alt="" src="/shared_media/shannonside/media/photos/content/bc_panel_2.png" style="height:214px; width:285px"><img alt="" src="/shared_media/shannonside/media/photos/content/bc_panel_3.png" style="height:198px; width:285px"><img alt="" src="/shared_media/shannonside/media/photos/content/bc_panel_4.png" style="height:187px; width:285px"></p>
			</div>
		</div>
	</div>
</div>
',
`banner_photo` = '1|../content/banner_content2.png|-1',
`layout_id` = (SELECT `id` FROM plugin_pages_layouts where `layout` = 'content_wide' LIMIT 1)
WHERE
  `name_tag` = 'contact-us.html';;

UPDATE `plugin_pages_pages`
SET `banner_photo` = '1|../content/banner_content2.png|-1',
`content` = '
\n<h1>About Shannonside Galvinizing</h1>
\n
\n<p style="padding: 0px; margin: 1em 0px; box-sizing: border-box; color: rgb(0, 0, 0); font-family: Helvetica, Ariel, sans-serif; line-height: 23px;">With over 20 years experience specialising in <a href="http://shannonside.websitecms.ie/galvanising" style="padding: 0px; margin: 0px; box-sizing: border-box; text-decoration: none; color: rgb(31, 72, 148);">hot dip galvanizing</a>, <a href="http://shannonside.websitecms.ie/shot-blasting" style="padding: 0px; margin: 0px; box-sizing: border-box; text-decoration: none; color: rgb(31, 72, 148);">shot blasting</a> & the application of <a href="http://shannonside.websitecms.ie/painting" style="padding: 0px; margin: 0px; box-sizing: border-box; text-decoration: none; color: rgb(31, 72, 148);">protective coatings</a>, Shannonside Galvanizing is one of Ireland''s leading Galvanizing plants located in Drombana, Co. Limerick. <br />
\n </p>
\n
\n<h2>Our Key Services</h2>
\n
\n<ul>
\n	<li style="padding: 0px 0px 0px 2em; margin: 0px; box-sizing: border-box; position: relative;">Hot Dip Galvanizing Services</li>
\n	<li style="padding: 0px 0px 0px 2em; margin: 0px; box-sizing: border-box; position: relative;">Shot Blasting Services</li>
\n	<li style="padding: 0px 0px 0px 2em; margin: 0px; box-sizing: border-box; position: relative;">Protective Coating Services</li>
\n</ul>
\n
\n<p> </p>
\n
\n<p><strong>GROUP PHOTO TO GO HERE</strong></p>
\n
\n<h2>Highly Skilled Team</h2>
\n
\n<p>We are an energetic and creative team, dedicated to high standards of performance and excellence. All who work within the company have been entrusted with an opportunity to improve the quality of life of our customers, our fellow employees, and the community that surrounds us. Two of our key members are the only ICORR Certified Hot Dip Galvanizing Inspectors in Ireland.</p>
\n
\n<p> </p>
\n
\n<h2>Driving High Standards - Customer Satisfaction is Key</h2>
\n
\n<p>It is the policy of the company to provide services that fully and consistently meet the needs of our customers, delivered through operations which ensure the <img alt="" src="/shared_media/shannonside/media/photos/content/iso-1.png" style="width: 120px; height: 156px; float: right; margin: 5px;" />health and safety of the workforce and public without damage to the environment. The Company values its employees and its success as a company has been built on a spirit of innovation, expertise, and commitment of its employees.</p>
\n
\n<p>It is their motivation and commitment to doing a first rate job that makes us stand out from the competition. We actively recruit and work hard to retain the best employees for the future.</p>
\n
\n<p>With our vast experience in the areas specified below and our ISO accreditation, Shannonside Galvanizing’s understanding of the requirements for cost effective solutions for all your surface preparation and coating requirements is unparalleled.</p>
\n
\n<p> </p>
\n
\n<h2>Talk to Us Today</h2>
\n
\n<p>If you would like to place an order or have some questions about your project, please talk to us today. <strong>Call now - 061-412357 ​</strong></p>
\n'
WHERE
`name_tag` = 'about-us.html';;

UPDATE `plugin_formbuilder_forms`
SET `fields` = '<input name="subject" value="Contact form" type="hidden">
<input name="business_name" value="Test Site" type="hidden" id="">
<input name="redirect" value="thank-you.html" type="hidden" id="">
<input name="event" value="contact-form" type="hidden" id="">
<input name="trigger" value="custom_form" type="hidden" id="trigger">
<input name="form_type" value="Contact Form" id="form_type" type="hidden">
<input name="form_identifier" value="contact_" type="hidden">
<input type="hidden" name="email_template" id="email_template" value="contactformmail">
<li><label for="contact_form_name">
Full Name*</label><input type="text" name="contact_form_name" id="contact_form_name" class="validate[required]">
</li><li><label for="contact_form_tel">
Phone Number*</label><input type="text" name="contact_form_tel" id="contact_form_tel" class="validate[required]">
</li><li><label for="contact_form_address">
Address*</label><textarea name="contact_form_address" id="contact_form_address" class="validate[required]">
</textarea></li><li><label for="contact_form_email_address">
E-mail Address*</label><input type="text" name="contact_form_email_address" id="contact_form_email_address" class="validate[required]">
</li><li><label for="contact_form_message">
Message</label><textarea name="contact_form_message" class="validate[required]" id="contact_form_message">
</textarea></li>                 <li><input type="checkbox"><label for="subscribe">
Subscribe to our newsletter and get the latest news from us.</label>
</li><li><label for="formbuilder-preview-formbuilder-preview-submit1">
</label><button name="submit1" class="button" id="formbuilder-preview-formbuilder-preview-formbuilder-preview-submit1" value="Send Email">
Send</button></li>'
WHERE `form_name` IN ('Contact Us', 'ContactUs');;

DELETE FROM `plugin_menus` WHERE `category` = 'header 1';;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','About',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('contact-us', 'contact-us.html') LIMIT 1),'',1,0,1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

SET @main_parent_id := (SELECT `id` from `plugin_menus` where `category` = 'header 1' and `title` = 'About' LIMIT 1);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Home',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('home', 'home.html') LIMIT 1),'',1,@main_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

SET @home_parent_id := (SELECT `id` from `plugin_menus` where `category` = 'header 1' and `title` = 'Home' and `parent_id` != '0' LIMIT 1);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Services',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('services', 'services.html') LIMIT 1),'',1, @main_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

SET @services_parent_id := (SELECT `id` from `plugin_menus` where `category` = 'header 1' and `title` = 'Services' and `parent_id` != '0' LIMIT 1);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Useful Links',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('sitemap', 'sitemap.html') LIMIT 1),'',1,@main_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

SET @useful_links_parent_id := (SELECT `id` from `plugin_menus` where `category` = 'header 1' and `title` = 'Useful Links' and `parent_id` != '0' LIMIT 1);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','About Us',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('about-us', 'about-us.html') LIMIT 1),'',0,@home_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Services',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('services', 'services.html') LIMIT 1),'',0,@home_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Quality & Certification',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('quality-and-certification', 'quality-and-certification.html') LIMIT 1),'',0,@home_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Projects',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('projects', 'projects.html') LIMIT 1),'',0,@home_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Galvanised Sections',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('galvanizing', 'galvanizing.html') LIMIT 1),'',0,@home_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Hot Dip Galvanizing',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('galvanizing', 'galvanizing.html') LIMIT 1),'',0,@services_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Painting',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('painting', 'painting.html') LIMIT 1),'',0,@services_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Shot Blasting',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('shot-blasting', 'shot-blasting.html') LIMIT 1),'',0,@services_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Galvanizing Assoc',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('galvanizing', 'galvanizing.html') LIMIT 1),'',0,@useful_links_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','LME Zinc',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('domestic-project', 'domestic-project.html') LIMIT 1),'',0,@useful_links_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Delivery/Collection',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('delivery-and-collection', 'delivery-and-collection.html') LIMIT 1),'',0,@useful_links_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Terms & Conditions',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('terms-of-use', 'terms-of-use.html') LIMIT 1),'',0,@useful_links_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('header 1','Quality Policy',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('quality-assurance', 'quality-assurance.html') LIMIT 1),'',0,@useful_links_parent_id, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_media_shared_media_photo_presets` (`title`,`directory`,`height_large`,`width_large`,`action_large`,`thumb`,`height_thumb`,`width_thumb`,`action_thumb`,`date_created`,`date_modified`,`created_by`,`modified_by`,`publish`,`deleted`)
VALUES ('Menu Icons','menus',21,0,'fith',0,0,0,'crop', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),1,0);;

DELETE FROM `plugin_menus` WHERE `category` = 'Bars';;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('Bars','Contact Us',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('home', 'home.html') LIMIT 1),'',0,0, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('Bars','Our Services',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('home', 'home.html') LIMIT 1),'',0,0, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

INSERT INTO `plugin_menus` (`category`,`title`,`link_tag`,`link_url`,`has_sub`,`parent_id`,`menu_order`,`publish`,`deleted`,`date_modified`,`date_entered`,`created_by`,`modified_by`,`menus_target`,`image_id`)
VALUES ('Bars','About Us',(SELECT `id` FROM plugin_pages_pages
WHERE `name_tag` IN ('home', 'home.html') LIMIT 1),'',0,0, 1,1,0,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),(SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),'_self',0);;

UPDATE `plugin_panels` SET `publish` = '0' WHERE `publish` = '1' and `position` = 'home_content';;

INSERT INTO `plugin_panels` (`title`, `position`, `order_no`, `type_id`, `predefined_id`, `image`, `text`, `link_id`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES ('Galvanizing', 'home_content', '0', '2', '0', 'galv_panel.png', '<p>Galvanizing</p>', (SELECT `id` from `plugin_pages_pages` where `name_tag` in ('home.html', 'home') LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), '1', '0');;

INSERT INTO `plugin_panels` (`title`, `position`, `order_no`, `type_id`, `predefined_id`, `image`, `text`, `link_id`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES ('Painting', 'home_content', '0', '2', '0', 'painting_panel.png', '<p>Galvanizing</p>', (SELECT `id` from `plugin_pages_pages` where `name_tag` in ('home.html', 'home') LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), '1', '0');;

INSERT INTO `plugin_panels` (`title`, `position`, `order_no`, `type_id`, `predefined_id`, `image`, `text`, `link_id`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES ('Shot Blasting', 'home_content', '0', '2', '0', 'shot_blasting_panel.png', '<p>Galvanizing', (SELECT `id` from `plugin_pages_pages` where `name_tag` in ('home.html', 'home') LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), '1', '0');;



UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Shannonside Galvanizing'
WHERE
  `variable` = 'address_line_1';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'address_line_1';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Four Films, Cronbanna'
WHERE
  `variable` = 'address_line_2';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'address_line_2';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Limerick'
WHERE
  `variable` = 'address_line_3';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'address_line_3';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = '061 412357'
WHERE
  `variable` = 'telephone';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'telephone';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'info@galv.ie'
WHERE
  `variable` = 'email';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'email';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'https://www.facebook.com/people/Shannon-Side-Galvanizing/100010386542459'
WHERE
  `variable` = 'facebook_url';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'facebook_url';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = ''
WHERE
  `variable` = 'twitter_url';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'twitter_url';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = ''
WHERE
  `variable` = 'linkedin_url';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'linkedin_url';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Shannonside Galvanizing'
WHERE
  `variable` = 'company_title';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'company_title';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = ''
WHERE
  `variable` = 'company_slogan';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'company_slogan';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = '04'
WHERE
  `variable` = 'template_folder_path';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'template_folder_path';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'a:32:{i:0;s:7:"default";i:1;s:2:"01";i:2;s:2:"02";i:3;s:2:"03";i:4;s:2:"04";i:5;s:2:"05";i:6;s:2:"06";i:7;s:2:"07";i:8;s:2:"08";i:9;s:2:"09";i:10;s:2:"10";i:11;s:2:"11";i:12;s:2:"12";i:13;s:2:"13";i:14;s:2:"14";i:15;s:2:"15";i:16;s:2:"16";i:17;s:2:"17";i:18;s:2:"18";i:19;s:2:"19";i:20;s:2:"20";i:21;s:2:"21";i:22;s:2:"22";i:23;s:2:"23";i:24;s:2:"24";i:25;s:2:"25";i:26;s:2:"26";i:27;s:2:"28";i:28;s:2:"29";i:29;s:2:"30";i:30;s:2:"31";i:31;s:2:"32";}'
WHERE
  `variable` = 'available_themes';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'available_themes';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'news'
WHERE
  `variable` = 'home_page_feed_1';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'home_page_feed_1';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = ''
WHERE
  `variable` = 'home_page_feed_2';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'home_page_feed_2';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = '1'
WHERE
  `variable` = 'frontend_login_link';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'frontend_login_link';;

UPDATE
  `engine_settings`
SET
  `value_dev`   = '<div class="simplebox" style="background:#1e2290; color:white">
\n	<div class="simplebox-columns">
\n		<div class="simplebox-column simplebox-column-1 hidden\-\-mobile" style="margin-left: 110px;">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<h1>Need advice? Talk to us, we are happy to help you</h1>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-2" style="max-width: 45%;">
\n			<div class="simplebox-content">
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n
\n				<p style="text-align:center"><a class="button inverse" href="/contact-us.html">CALL US TODAY</a></p>
\n			</div>
\n		</div>
\n	</div>
\n</div>
\n
\n<div class="simplebox">
\n	<div class="simplebox-title" style="max-width:800px">
\n		<h1 style="text-align:center">Over 890 users are using Shannonside Galvonising</h1>
\n	</div>
\n
\n	<div class="simplebox-columns" style="text-align:center">
\n		<div class="simplebox-column simplebox-column-1">
\n			<div class="simplebox-content">
\n				<p><img alt="Conor" src="/shared_media/shannonside/media/photos/content/conor_logo.jpg" style="height:100px; width:115px" /></p>
\n
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-2">
\n			<div class="simplebox-content">
\n				<p><img alt="Abbey Machinery" src="/shared_media/shannonside/media/photos/content/abbey_logo.jpg" style="height:31px; width:113px" /></p>
\n
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-3">
\n			<div class="simplebox-content">
\n				<p><img alt="Bridge" src="/shared_media/shannonside/media/photos/content/bridge_logo.png" style="height:27px; width:116px!important" /></p>
\n
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-4">
\n			<div class="simplebox-content">
\n				<p><img alt="Ard Precision" src="/shared_media/shannonside/media/photos/content/ard_logo.png" style="height:61px; width:115px" /></p>
\n
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-5">
\n			<div class="simplebox-content">
\n				<p><img alt="JPK Precision" src="/shared_media/shannonside/media/photos/content/jpk_logo.jpg" style="height:54px; width:115px!important" /></p>
\n
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n			</div>
\n		</div>
\n
\n		<div class="simplebox-column simplebox-column-6">
\n			<div class="simplebox-content">
\n				<p><img alt="Major Equipment" src="/shared_media/shannonside/media/photos/content/major_logo.png" style="height:54px; width:115px!important" /></p>
\n
\n				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
\n			</div>
\n		</div>
\n	</div>
\n</div>
\n
\n<p style="text-align:center"><a class="button" href="/contact-us.html">CONTACT US</a></p>
\n'
WHERE
  `variable` = 'page_footer';;

UPDATE
  `engine_settings`
SET
  `value_test` = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live` = `value_dev`
WHERE
  `variable` = 'page_footer';;

INSERT IGNORE INTO `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `action_large`, `thumb`, `date_created`) VALUES ('Logos', 'logos', '88', 'fith', '0', CURRENT_TIMESTAMP());;
