/*
ts:2018-03-05 16:00:00
*/


/* Add the "04" template, if it does not already exist. */
INSERT INTO
  `engine_site_templates` (`title`, `stub`, `type`, `date_created`, `date_modified`)
  SELECT '04', '04', 'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_templates`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_templates` WHERE `stub` = '04')
    LIMIT 1
;

/* Update the template */
DELIMITER ;;
UPDATE
  `engine_site_templates`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = "\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Generic
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n\/\* Fonts \*\/
\n@import url(\'https:\/\/fonts.googleapis.com\/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900\');
\n@import url(\'\/engine\/shared\/css\/elegant_icons.css\');
\n@import url(\'\/engine\/shared\/css\/flaticon.css?cb=4\');
\n@import url(\'\/engine\/shared\/css\/font-awesome.min.css\');
\n@import url(\'\/engine\/shared\/css\/shared_footer.css?cb=2\');
\n
\n\/\* Resets and browser synchronisations \*\/
\n\/\*! normalize.css v5.0.0 | MIT License | github.com\/necolas\/normalize.css
\n\*\/button,hr,input{overflow:visible}audio,canvas,progress,video{display:inline-block}progress,sub,sup{vertical-align:baseline}[type=checkbox],[type=radio],legend{box-sizing:border-box;padding:0}html{font-family:sans-serif;line-height:1.15;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}body{margin:0}article,aside,details,figcaption,figure,footer,header,main,menu,nav,section{display:block}h1{font-size:2em;margin:.67em 0}figure{margin:1em 40px}hr{box-sizing:content-box;height:0}code,kbd,pre,samp{font-family:monospace,monospace;font-size:1em}a{background-color:transparent;-webkit-text-decoration-skip:objects}a:active,a:hover{outline-width:0}abbr[title]{border-bottom:none;text-decoration:underline;text-decoration:underline dotted}b,strong{font-weight:bolder}dfn{font-style:italic}mark{background-color:#ff0;color:#000}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative}sub{bottom:-.25em}sup{top:-.5em}audio:not([controls]){display:none;height:0}img{border-style:none}svg:not(:root){overflow:hidden}button,input,optgroup,select,textarea{font-family:sans-serif;font-size:100%;line-height:1.15;margin:0}button,select{text-transform:none}[type=reset],[type=submit],button,html [type=button]{-webkit-appearance:button}[type=button]::-moz-focus-inner,[type=reset]::-moz-focus-inner,[type=submit]::-moz-focus-inner,button::-moz-focus-inner{border-style:none;padding:0}[type=button]:-moz-focusring,[type=reset]:-moz-focusring,[type=submit]:-moz-focusring,button:-moz-focusring{outline:ButtonText dotted 1px}fieldset{border:1px solid silver;margin:0 2px;padding:.35em .625em .75em}legend{color:inherit;display:table;max-width:100%;white-space:normal}textarea{overflow:auto}[type=number]::-webkit-inner-spin-button,[type=number]::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}[type=search]::-webkit-search-cancel-button,[type=search]::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}[hidden],template{display:none}
\n
\nhtml {
\n    -webkit-box-sizing: border-box;
\n    box-sizing: border-box;
\n}
\n
\n\*, :before, :after {
\n    -webkit-box-sizing: inherit;
\n    box-sizing: inherit;
\n}
\n
\nhtml,
\nbody {
\n    font-size: 100%;
\n    max-width: 100%;
\n    height: 100%;
\n}
\n
\nimg {
\n    max-width: 100%;
\n    height: auto;
\n}
\n
\ntable {
\n    border-collapse: collapse;
\n    border-spacing: 0;
\n}
\n
\nfigure {
\n    margin: 0;
\n}
\n
\nbutton, input, optgroup, select, textarea {
\n    font-family: inherit;
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Base
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\nhtml {
\n    font-size: 16px;
\n}
\n
\nbody {
\n    -webkit-font-smoothing: antialiased;
\n    -moz-osx-font-smoothing: grayscale;
\n    color: #212121;
\n}
\n
\na:link {text-decoration: none;}
\na:hover {text-decoration: none;}
\n
\nul {
\n    list-style: none;
\n}
\na.disabled {
\n    cursor: not-allowed;
\n    pointer-events: none;
\n    opacity: .5;
\n}
\n
\n.disabled :disabled {
\n    opacity: 1; \/\* Prevent double opacity \*\/
\n}
\n
\nh1, h2, h3, h4, h5, h6 {
\n    font-style: normal;
\n    font-weight: normal;
\n    line-height: 1.4;
\n    text-rendering: optimizeLegibility;
\n}
\n
\nh1 {
\n    font-size: 24px; }
\n
\nh2 {
\n    font-size: 22px; }
\n
\nh3 {
\n    font-size: 20px; }
\n
\nh4 {
\n    font-size: 18px; }
\n
\nh5 {
\n    font-size: 16px; }
\n
\nh6 {
\n    font-size: 14px;
\n}
\n
\np {
\n    margin-top: 1em;
\n    margin-bottom: 1em;
\n}
\n
\nhr {
\n    margin: .5em auto;
\n}
\n
\naddress {
\n    font-style: normal;
\n}
\n
\naddress .line {
\n    display: block;
\n}
\n
\naddress .line:after {
\n    content: \', \';
\n}
\n
\naddress .line:last-child:after {
\n    content: none;
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Utility
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n\/\* Row \*\/
\n.container {
\n    margin-left: auto;
\n    margin-right: auto;
\n    max-width: 1040px;
\n}
\n
\n.row {
\n    box-sizing: border-box;
\n    margin-left: auto;
\n    margin-right: auto;
\n    max-width: 1040px;
\n    width: 100%;
\n}
\n
\n.row.gutters {
\n    margin-left: -15px;
\n    margin-right: -15px;
\n    max-width: none;
\n    width: auto;
\n}
\n
\n.row.gutters > [class\*=\"-column\"] {
\n    padding-left: 15px;
\n    padding-right: 15px;
\n}
\n
\n.clearfix:after,
\n.row:after {
\n    content: \'\';
\n    clear: both;
\n    display: table;
\n}
\n
\n\/\* Lists \*\/
\n.list-unstyled,
\n.list-unstyled.list-unstyled li {
\n    margin-left: 0;
\n    padding-left: 0;
\n    list-style: none;
\n}
\n
\n.list-unstyled.list-unstyled li::before,
\n.accordion-basic.accordion-basic > li::before,
\n.formrt.formrt li::before {
\n    display: none;
\n}
\n
\n.accordion-basic h3:not(.active) ~ * { display: none; }
\n
\n\/\* Tables \*\/
\n.table {
\n    border-collapse: collapse;
\n    border-style: hidden;
\n    box-shadow: 0 0 0 1px #CCC;
\n    width: 100%;
\n}
\n
\n.table th,
\n.table td {
\n    border: 1px solid #CCC;
\n    padding: .5em;
\n}
\n
\n.table thead td,
\n.table thead th {
\n    border-color: #fff;
\n}
\n
\n.table\-\-checkout th,
\n.table\-\-checkout td {
\n    padding: .7em 1.7rem;
\n}
\n
\n.table\-\-checkout tr > :first-child {
\n    padding-left: 2rem;
\n    padding-right: 2rem;
\n}
\n
\n.badge {
\n    border-radius: .3em;
\n    display: inline-block;
\n    font-size: 1rem;
\n    margin-right: 1.5em;
\n    padding: .125em .5em .2em;
\n}
\n
\n.nowrap {
\n    white-space: nowrap;
\n}
\n
\n\/\* AJAX spinner \*\/
\n.ajax_loader {
\n    background: rgba(0, 0, 0, .5);
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -webkit-box-align: center;
\n    -ms-flex-align: center;
\n    align-items: center;
\n    text-align: center;
\n    position: fixed;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n    z-index: 10;
\n}
\n
\n.ajax_loader:after {
\n    content: \'\\f110\';
\n    font-family: fontAwesome;
\n    font-size: 35px;
\n    -webkit-animation:spin 2s linear infinite;
\n    -moz-animation:spin 2s linear infinite;
\n    animation:spin 2s linear infinite;
\n    width: 100%;
\n}
\n
\n@-moz-keyframes spin { 100% { -moz-transform: rotate(360deg); } }
\n@-webkit-keyframes spin { 100% { -webkit-transform: rotate(360deg); } }
\n@keyframes spin { 100% { -webkit-transform: rotate(360deg); transform:rotate(360deg); } }
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Dashboard layout
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.open{display:block;}
\n.close{display:none;}
\n
\ninput[type=\"radio\"]:disabled{cursor: not-allowed;}
\n
\n.row ul{margin:0;list-style: none; padding: 0;}
\n
\n\/\*dashboard nav style =================\*\/
\n.db-sidebar{width:20%;background:#f0f0f0;border:1px solid #ccc;border-radius:2px; float: left;}
\n.db-sidebar ul{padding:0;position:relative}
\n.db-sidebar .sidebar-menu li{border-bottom:1px solid #ccc}
\n.db-sidebar .sidebar-menu li a{display:block;padding:13px 23px;color:#222;font-size:18px}
\n.db-sidebar .sidebar-menu li.sidebar-home a{background-position:12px 19px;}
\n.db-sidebar .sidebar-menu li.sidebar-home a:after{ content:\"\"; }
\n
\n.db-sidebar .sidebar-menu li:hover a span, .db-sidebar .sidebar-menu li:hover a i.fa{color:#fff;}
\n.db-sidebar .sidebar-menu li.sidebar-wishlist ~ li {background: rgb(255, 255, 255);}
\n.db-sidebar .sidebar-menu li.sidebar-wishlist{margin-bottom: 195px;}
\n
\n
\n\/\*\-\-\-popup section=================\*\/
\n.sectionOverlay{position:fixed;left:0;top:0;bottom:0;right:0;z-index:8888;display:none; overflow-y:auto;}
\n.sectionOverlay .overlayer{background-color:#333;background:rgba(0,0,0,0.5);position:fixed;left:0;top:0;bottom:0;right:0; z-index:77;}
\n.screenTable{position:absolute;left:0;right:0;top:0;bottom:0;display:table;width:100%;height:100%; z-index:88;}
\n.sectioninner{position:relative;left:0;top:0;margin:0 auto;max-width:810px; background:#fff; width: 98%;}
\n.sectioninner h3{font-size: 18px;}
\n.sectioninner .close{position:absolute;right:25px;top:15px}
\n.basic_close{width:35px;height:35px;line-height:35px;font-size:25px;font-weight:300;color:inherit;text-align:center;display:block;cursor:pointer;z-index:99999;float:right;position:absolute; right:5px; top:5px;}
\n.popup-header{padding:15px 20px;font-size:18px;}
\n.popup-content{padding: 30px 20px;}
\n.popup-content td{position:relative;}
\n
\n.popup-header h1,
\n.popup-header h2,
\n.popup-header h3,
\n.popup-header h4,
\n.popup-header h5,
\n.popup-header h6 {
\n    margin: 0;
\n}
\n
\n.popup-title .popup-subtitle::before {
\n    content: \' (\';
\n}
\n
\n.popup-title .popup-subtitle::after {
\n    content: \')\';
\n}
\n
\n.popup-title .popup-subtitle:empty {
\n    display: none;
\n}
\n
\n.popup-footer {
\n    text-align: center;
\n    padding: 1em;
\n}
\n
\n.popup-footer .button {
\n    min-width: 8em;
\n}
\n
\n@media screen and (max-height: 768px) {
\n    .screenCell {
\n        display: table-cell;
\n        vertical-align: middle;
\n    }
\n}
\n
\n@media screen and (min-height: 768px) {
\n    .screenCell {
\n        padding-top: 100px;
\n    }
\n}
\n
\n.booking_popup .sectioninner {
\n    max-width: 725px;
\n}
\n
\n.topics-list {
\n    text-align: left;
\n}
\n
\n.topics-list ul {
\n    -webkit-columns: 9em 3;
\n    -moz-columns: 9em 3;
\n    columns: 9em 3;
\n    margin-bottom: 1.5em;
\n}
\n
\n.topics-list ul li {
\n    -webkit-column-break-inside: avoid;
\n    page-break-inside: avoid;
\n    break-inside: avoid;
\n    line-height: 1.25;
\n    margin: 0;
\n    min-height: 1.5em;
\n}
\n
\n.topics_list ul li:before {
\n    color: inherit;
\n}
\n
\n@-webkit-keyframes zoomIn {
\n  from {
\n    opacity: 0;
\n    -webkit-transform: scale3d(.3, .3, .3);
\n    transform: scale3d(.3, .3, .3);
\n  }
\n
\n  50% {
\n    opacity: 1;
\n  }
\n}
\n
\n@keyframes zoomIn {
\n  from {
\n    opacity: 0;
\n    -webkit-transform: scale3d(.3, .3, .3);
\n    transform: scale3d(.3, .3, .3);
\n  }
\n
\n  50% {
\n    opacity: 1;
\n  }
\n}
\n
\n.zoomIn {
\n  -webkit-animation-name: zoomIn;
\n  animation-name: zoomIn;
\n   -webkit-animation-duration: 0.5s;
\n  animation-duration: 0.5s;
\n  -webkit-animation-fill-mode: both;
\n  animation-fill-mode: both;
\n}
\n
\n.popup-content .form-label{display: block; font-size: 16px; font-weight: 300; padding-bottom: 5px;}
\n
\n.db-sidebar .sidebar-menu  .sidebar-homework a {background-image: url(..\/images\/sidebar-homework.png); background-position:12px 15px; }
\n.db-sidebar .sidebar-menu  .sidebar-homework a:hover, .db-sidebar .sidebar-menu  .sidebar-homework a.active{background-position:12px -20px;}
\n.right-section-content{width:78%;float:right}
\n.clear{clear:both}
\n
\n\/\*=== for hover and desktop style =================\*\/
\n@-webkit-keyframes fadeInDown {
\n  0% {
\n    opacity: 0;
\n    -webkit-transform: translate3d(0, -100%, 0);
\n    transform: translate3d(0, -100%, 0);
\n  }
\n  100% {
\n    opacity: 1;
\n    -webkit-transform: none;
\n    transform: none;
\n  }
\n}
\n@keyframes fadeInDown {
\n  0% {
\n    opacity: 0;
\n    -webkit-transform: translate3d(0, -100%, 0);
\n    transform: translate3d(0, -100%, 0);
\n  }
\n100% {
\n    opacity: 1;
\n    -webkit-transform: none;
\n    transform: none;
\n  }
\n}
\n
\n.course-widget-links .button.button\-\-cl_remove {
\n    font-size: 1em;
\n    padding: 0.928em 0.2em;
\n}
\n
\n@media (min-width: 993px){
\n	.db-sidebar{display: block !important;}
\n}
\n
\n@media (max-width: 992px){
\n	.db-sidebar{-webkit-transition:.4s all ease-in-out;-moz-transition:.4s all ease-in-out;-o-transition:.4s all ease-in-out;transition:.4s all ease-in-out; width: 100%;
\n		-webkit-transform: translateX(-120%);
\n		-moz-transform: translateX(-120%);
\n		transform: translateX(-120%);
\n		position: absolute;
\n		z-index: 5;top:126px;
\n		border: none;
\n		overflow: scroll;}
\n	.db-sidebar.open{
\n        -webkit-transform: translateX(0px);
\n        -moz-transform: translateX(0px);
\n        transform: translateX(0px);
\n        left: 0;
\n        background: rgba(0,0,0,0.5);
\n    }
\n	.db-sidebar ul{
\n        min-height: 100%;
\n        width: 50%;
\n        background:#f0f0f0;
\n        display:inline-block;
\n        border:1px solid #ccc;
\n        border-radius:2px;
\n        overflow-y:auto;
\n    }
\n	.right-section-content{width: 100%;}
\n}
\n
\n@media(min-width: 768px) and (max-width: 992px){
\n    .db-sidebar{height: calc(100vh - 126px);}
\n}
\n
\n@media (max-width: 767px){
\n	.db-sidebar{top:52px;height: calc(100vh - 52px);}
\n	.db-sidebar ul{min-width: 245px;}
\n}
\n
\n
\n@media screen and (min-width: 768px)
\n{
\n    .table {
\n        border-radius: 5px;
\n    }
\n
\n    .table thead tr > :first-child {
\n        border-top-left-radius: 5px;
\n    }
\n
\n    .table thead tr > :last-child {
\n        border-top-right-radius: 5px;
\n    }
\n}
\n
\n@media screen and (max-width: 1077px)
\n{
\n    .row {
\n        padding-left: 19px;
\n        padding-right: 19px;
\n    }
\n
\n    .row.gutters,
\n    .content > .row {
\n        padding-left: 0;
\n        padding-right: 0;
\n    }
\n}
\n
\n.autotimetable {
\n    background: #fff;
\n    width: 100%;
\n    margin: 0 0 18px;
\n    border: 1px solid #ddd;
\n}
\n
\n.autotimetable caption {
\n    color: #465863;
\n    font-family: lucida_sans_unicoderegular, Roboto, Helevtica, Arial, sans-serif;
\n    font-size: 20px;
\n    font-weight: bold;
\n    padding: 8px;
\n}
\n
\n.autotimetable thead {
\n    background: #f5f5f5;
\n}
\n
\n.autotimetable thead th {
\n    font-weight: bold;
\n    padding: 8px 10px 9px;
\n}
\n
\n.autotimetable thead tr th:first-child {
\n    border-left: none;
\n}
\n
\n.autotimetable thead tr th:last-child {
\n    border-right: none;
\n}
\n
\n.autotimetable tbody tr td {
\n    padding: 9px 10px;
\n    vertical-align: top;
\n    border: none;
\n}
\n
\n
\n.autotimetable tbody tr td a {
\n    text-decoration: underline;
\n}
\n
\n.autotimetable tbody a:hover {
\n    text-decoration: none;
\n}
\n
\n.autotimetable tbody td:nth-child(odd),
\n.autotimetable tbody td:nth-child(even) {
\n    border-right-style: dashed;
\n    border-width: thin;
\n    border-color: #d1d1d1;
\n}
\n
\n.autotimetable .new_date {
\n    border-top: 2px solid;
\n}
\n
\n.autotimetable .new_date td:nth-child(1) {
\n    font-weight: bold;
\n}
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Slide-in menus
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\nbody.body\-\-slidein {
\n    overflow: hidden;
\n}
\n.slidein {
\n    font-size: 1rem;
\n    position: fixed;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n    z-index: 50;
\n}
\n
\n.slidein:not(.slidein\-\-active) {
\n    display: none;
\n}
\n
\n.slidein:before {
\n    content: \'\';
\n    -webkit-backface-visibility: hidden;
\n    -webkit-animation: slidein-fadein .5s;
\n    animation: slidein-fadein .5s;
\n    background: rgba(50, 50, 50, .75);
\n    position: fixed;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n}
\n
\n.slidein-content {
\n    background: #fff;
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    width: 100%;
\n    max-width: 500px;
\n}
\n
\n.slidein-header,
\n.slidein-footer {
\n    background: #FFF;
\n    border: 0 solid #ccc;
\n    padding-left: 1em;
\n    padding-right: 1em;
\n}
\n
\n.slidein-header {
\n    border-bottom-width: 1px;
\n    height: 2em;
\n}
\n
\n.slidein-body {
\n    height: calc(100vh - 8em - 2px);
\n    overflow-y: auto;
\n    padding: .5em 1em;
\n}
\n
\n.slidein-footer {
\n    border-top-width: 1px;
\n    height: 5em;
\n    padding: 1em;
\n    position: absolute;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n    text-align: center;
\n}
\n
\n\/\* Seating-zone selector \*\/
\n.seating-selector-footer {
\n    margin-top: 1em;
\n    text-align: center;
\n}
\n
\n.seating-selector-footer .button {
\n    font-size: .75em;
\n    min-width: 8em;
\n}
\n
\n.seating-selector-map {
\n    margin: auto;
\n    max-width: 300px;
\n}
\n
\n.seating-selector-row .button {
\n    width: 100%;
\n}
\n
\n.seating-selector-row {
\n    padding: .5em;
\n}
\n
\n.seating-selector-checkbox-helper {
\n    background: #f3f3f3;
\n    border: 1px solid #cecece;
\n    border-radius: .25em;
\n    display: inline-block;
\n    margin-top: .3em;
\n    margin-bottom: -.3em;
\n    position: relative;
\n    width: 1.5em;
\n    height: 1.5em;
\n}
\n
\n:checked + .seating-selector-checkbox-helper:after {
\n    cursor: pointer;
\n    content: \'\\2714\';
\n    position: absolute;
\n    top: .15em;
\n    left: .3em;
\n}
\n
\n.seating-selector-option {
\n    display: block;
\n    position: relative;
\n}
\n
\n.seating-selector-option-radio:disabled + .button {
\n    background: #fff;
\n    border-color: #999;
\n    color: #999;
\n    cursor: not-allowed;
\n    opacity: .75;
\n}
\n
\n.seating-selector-option-hover {
\n    display: none;
\n    border-radius: .375em;
\n    padding: .333em;
\n    position: absolute;
\n    z-index: 1;
\n    top: -1em;
\n    right: -.5em;
\n}
\n
\n.seating-selector-option:hover .seating-selector-option-hover{
\n    display: block;
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Forms
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.form-group {
\n    margin-bottom: 15px;
\n}
\n
\n.content_area .form-group {
\n    margin-bottom: 1.25em;
\n}
\n
\n.formrt [type=\"text\"],
\n.formrt [type=\"email\"],
\n.formrt [type=\"password\"],
\n.formrt select,
\n.formrt textarea,
\n.formrt .StripeElement {
\n    background: #fff;
\n    border: 1px solid #cdcdcd;
\n    border-radius: 4px;
\n    height: 2.625em;
\n    padding: .6875em 1.25em;
\n    width: 100%;
\n}
\n
\n.formrt textarea {
\n    line-height: 2.38em;
\n    height: 5em;
\n    padding-top: .3em;
\n    padding-bottom: .3em;
\n}
\n
\n.formrt textarea[rows] {
\n    height: auto;
\n}
\n
\n.formrt:not(.formrt-vertical)  li > label ~ [type=\"text\"],
\n.formrt:not(.formrt-vertical)  li > label ~ [type=\"email\"],
\n.formrt:not(.formrt-vertical)  li > label ~ [type=\"password\"],
\n.formrt:not(.formrt-vertical)  li > label ~ select,
\n.formrt:not(.formrt-vertical)  li > label ~ textarea {
\n    width: calc(100% - 130px);
\n}
\n
\n.focus_group {
\n    position: relative;
\n}
\n
\n.focus_group .form-input {
\n    height: 2.9em;
\n    padding-bottom: 0;
\n    padding-left: .7em;
\n}
\n
\n.focus_group label {
\n    width: 100%;
\n    font-size: .7777em;
\n    padding: .4em .857143em 0;
\n    position: absolute;
\n    top: 0;
\n    left: 0;
\n}
\n
\n.focus_group input:focus {
\n    outline: none;
\n}
\n
\n.focus_group input + label {
\n    -webkit-transition: all .2s linear;
\n    transition: all .2s linear;
\n}
\n
\n.focus_group :focus + label {
\n    background: #e8f3f9;
\n    border-radius: 5px 5px 0 0;
\n    padding-bottom: .5em;
\n    top: -1em;
\n}
\n
\n
\n\/\* style select lists \*\/
\n.select select {
\n    -webkit-appearance: none;
\n    -moz-appearance: none;
\n    -o-appearance: none;
\n    appearance: none;
\n    background: none;
\n    cursor: pointer;
\n    padding-right: 2.5em;
\n    padding-right: calc(2.5em + 8px);
\n    position: relative;
\n    text-indent: .01px;
\n    text-overflow: \'\';
\n    z-index: 1;
\n}
\n
\n.select select::-ms-expand {
\n    display: none;
\n}
\n
\n.select {
\n    display: block;
\n    position: relative;
\n}
\n
\n.select:before {
\n    content: \'\';
\n    border-left: 1px solid #CCC;
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    width: 2.5em;
\n    width: calc(2.5em + 8px);
\n}
\n
\n.select:after {
\n    content: \'\';
\n    border-left: 4px solid transparent;
\n    border-right: 4px solid transparent;
\n    border-top: 5px solid #CCC;
\n    position: absolute;
\n    right: 1.25em;
\n    top: 56%;
\n    transform: translate(0%, -50%);
\n    z-index: 0;
\n}
\n
\n\/\* Formbuilder \*\/
\n.formrt ul,
\n.formrt ul > li {
\n    list-style: none;
\n    margin: 0;
\n    padding: 0;
\n}
\n
\n.formrt ul > li {
\n    clear: both;
\n    margin-bottom: 1em;
\n    position: relative;
\n}
\n
\n.formrt li:before {
\n    display: none;
\n}
\n
\n.formrt li:after {
\n    content: \'\';
\n    clear: both;
\n    display: table;
\n}
\n
\n.formrt [type=\"checkbox\"] {
\n    float: left;
\n    margin-right: .25em;
\n    margin-top: .2em;
\n}
\n
\n.formrt:not(.formrt-vertical) li > label:first-child {
\n    float: left;
\n    width: 120px;
\n}
\n
\n.formrt-vertical li > label:first-child {
\n    float: left;
\n}
\n
\n.formrt:not(.formrt-vertical)  li > label ~ [type=\"text\"],
\n.formrt:not(.formrt-vertical)  li > label ~ [type=\"email\"],
\n.formrt:not(.formrt-vertical)  li > label ~ [type=\"password\"],
\n.formrt:not(.formrt-vertical)  li > label ~ select,
\n.formrt:not(.formrt-vertical)  li > label ~ textarea {
\n    width: calc(100% - 130px);
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .formrt-grid ul > li {
\n        clear: none;
\n        float: left;
\n        width: 50%;
\n        padding-left: .5em;
\n        padding-right: .5em;
\n    }
\n
\n    .formrt-grid ul {
\n        margin-left: -.5em;
\n        margin-right: -.5em;
\n    }
\n
\n    .formrt-grid:after {
\n        content: \'\';
\n        clear: both;
\n        display: table;
\n    }
\n
\n    .formrt-grid ul > li:last-child:nth-child(odd) {
\n        width: 100%;
\n        text-align: center;
\n    }
\n}
\n
\n
\n\/\* Buttons \*\/
\n.button,
\n.formrt button,
\n.formrt [type=\"submit\"],
\n.formrt [type=\"reset\"] {
\n    display: inline-block;
\n    text-align: center;
\n    line-height: 1;
\n    cursor: pointer;
\n    -webkit-appearance: none;
\n    transition: background-color .25s ease-out,color .25s ease-out;
\n    vertical-align: middle;
\n    border: 1px solid transparent;
\n    border-radius: 3px;
\n    padding: .85em 1.5em;
\n    margin: 0;
\n    background-color: #12387f;
\n    color: #fff;
\n    font-weight: bold;
\n}
\n
\na.button {
\n    text-decoration: none;
\n}
\n
\n.button\-\-continue:hover,
\n.button\-\-pay:hover,
\n.button\-\-book:hover,
\n.button\-\-send:hover {
\n    opacity: .9;
\n}
\n
\n.button\-\-continue {
\n    background-color: #12387f;
\n}
\n
\n.button\-\-continue.inverse {
\n    background: #FFF;
\n    border: 1px solid #12387f;
\n    color: #12387f;
\n}
\n
\n.button\-\-cancel {
\n    background: #FFF;
\n    border: 1px solid #F00;
\n    color: #F00;
\n}
\n
\n.button\-\-pay {
\n    background-color: #b8d12f;
\n}
\n
\n.button\-\-pay.inverse {
\n    background: #FFF;
\n    border: 1px solid #b8d12f;
\n    color: #b8d12f;
\n}
\n
\n.button\-\-book {
\n    background-color: #b8d12f;
\n}
\n
\n.button\-\-book.inverse {
\n    background: #fff;
\n    border: 1px solid #b8d12f;
\n    color: #b8d12f;
\n}
\n
\n.button\-\-send,
\n.btn-primary {
\n    background: #00c6ee;
\n    color: #fff;
\n}
\n
\n.button\-\-send.inverse {
\n    background: #FFF;
\n    border: 1px solid #00c6ee;
\n    color: #00c6ee;
\n}
\n
\n.button\-\-plain {
\n    background: none;
\n    border: none;
\n    cursor: pointer;
\n    padding: 0;
\n}
\n
\n.button:disabled {
\n    opacity: .75;
\n}
\n
\n.payment_form-ccExp {
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -webkit-box-pack: justify;
\n    -ms-flex-pack: justify;
\n    justify-content: space-between;
\n    max-width: 400px;
\n}
\n
\n.payment_form-ccExp > .select {
\n    width: 49%;
\n}
\n
\n.formErrorContent {
\n    font-size: 13px;
\n    width: 160px;
\n}
\n
\n
\/\* Donation form custom rules \*\/
\n[id=\"payment_form\"] fieldset {
\n    border: none;
\n}
\n
\n
\n[id=\"payment_form\"] legend {
\n    font-weight: bold;
\n}
\n
\n[name=\"donation_type\"] {
\n    opacity: 0;
\n    position: absolute;
\n    z-index: -1;
\n}
\n
\n[id=\"payment_form\"] [name=\"donation_type\"] + label {
\n    border-radius: .25em;
\n    cursor: pointer;
\n    display: block;
\n    font-size: 1.33333333em;
\n    font-weight: bold;
\n    width: 100%;
\n    border: 1px solid #aaa;
\n    margin-bottom: .4em;
\n    padding: .3em;
\n    text-align: center;
}

[id=\"payment_form\"] [name=\"donation_type\"]:not(:checked) + label {
\n    background: #f9f9f9;
\n    color: #000;
}

.payment_select {
\n    opacity: 0;
\n    position: absolute;
\n    z-index: -1;
}
input.payment_select:focus + label {
\n    outline: 2px solid skyblue;
}

input.payment_select + label {
\n    background-repeat: no-repeat;
\n    background-size: 120px;
\n    color: transparent;
\n    cursor: pointer;
\n    display: inline-block;
\n    float: none;
\n    font-size: 0.001px;
\n    height: 24px;
\n    width: 120px;
\n}
\n
\n.payment_select_cc + label {
\n    background-image: url(\'\/engine\/shared\/img\/pay_with_credit_card_grey.png\');
\n}
\n
\n.payment_select_cc:checked + label,
\n.payment_select_cc + label:hover {
\n    background-image: url(\'\/engine\/shared\/img\/pay_with_credit_card.png\');
\n}
\n
\n.payment_select_paypal + label {
\n    background-image: url(\'\/engine\/plugins\/payments\/images\/checkout-paypal_grey.png\');
\n    background-size: 124px;
\n    width: 124px;
\n}
\n
\n.payment_select_paypal:checked + label,
\n.payment_select_paypal + label:hover {
\n    background-image: url(\'\/engine\/plugins\/payments\/images\/checkout-paypal.png\');
\n}
\n
\ninput.payment_select_stripe + label {
\n    background: #9c9c9c url(\'\/engine\/shared\/img\/stripe_logo.png\') no-repeat center top;
\n    background-size: 52px;
\n    border: 1px solid #888;
\n    border-radius: 2px;
\n    width: 124px;
\n}
\n
\ninput.payment_select_stripe:checked + label {
\n    background-color: #f8c740;
\n    border-color: #f8b320;
\n}
\n
\n.formrt label[for=\"payment_form_terms\"] {
\n    display: inline;
\n    float: none;
\n}
\n
\n.stripe-button + .stripe-button-el {
\n    display: none;
\n}
\n
\n.content_area .survey-question-block {
\n    font-weight: normal;
\n}
\n
\n[type=\"radio\"] + .survey-input-helper {
\n    background: #fff;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .content_area #survey-question-blocks {
\n        display: flex;
\n        flex-wrap: wrap;
\n    }
\n
\n    .sidebar + .content_area .survery-question-block {
\n        width: 100%;
\n    }
\n
\n    .content_area .survey-question-block {
\n        padding-left: 1rem;
\n        padding-right: 1rem;
\n        width: 50%;
\n    }
\n}
\n
\n
\n\/\* Alerts \*\/
\n.alert {
\n    border: 1px solid transparent;
\n    border-radius: 4px;
\n    font-size: 14px;
\n    margin-bottom: 1.5em;
\n    padding: 1em;
\n    position: relative;
\n    text-align: left;
\n}
\n
\n.alert .close {
\n    color: #666;
\n    cursor: pointer;
\n    display: inline-block;
\n    font-size: 1em;
\n    font-weight: normal;
\n    position: absolute;
\n    top: 1em;
\n    right: .71em;
\n}
\n
\n.popup_box {
\n    background: #fff;
\n    border-radius: 5px;
\n    border-width: 1px 1px 1px 2.666667em;
\n    color: #333;
\n    font-size: 15px;
\n    max-width: calc(100vw - 40px);
\n    min-height: 2.8em;
\n    padding: 1em;
\n    position: fixed;
\n    top: 1.333333em;
\n    right: 1.333333em;
\n    width: 26.666667em;
\n    z-index: 5;
\n}
\n
\n.popup_box.alert-add,
\n.popup_box.alert-remove {
\n    -webkit-box-align: center;
\n    -ms-flex-align: center;
\n    -ms-grid-row-align: center;
\n    align-items: center;
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n}
\n
\n.popup_box .close {
\n    opacity: .5;
\n}
\n
\n.popup_box .close-btn {
\n    float: right;
\n}
\n
\n.popup_box.alert:before {
\n    content: \'\\f05a\';
\n    color: #fff;
\n    display: block;
\n    font-family: fontAwesome;
\n    font-size: 20px;
\n    margin: auto;
\n    position: absolute;
\n    top: 12px;
\n    top: calc(50% - 12px);
\n    left: -2em;
\n    text-align: center;
\n    width: 40px;
\n}
\n
\n.popup_box.alert-success:before { content: \'\\f058\'; }
\n.popup_box.alert-info:before    { content: \'\\f05a\'; }
\n.popup_box.alert-warning:before { content: \'\\f06a\'; }
\n.popup_box.alert-error:before,
\n.popup_box.alert-danger:before  { content: \'\\f071\'; }
\n.popup_box.alert-add:before     { content: \'\\4e\'; font-family: \'ElegantIcons\'; }
\n.popup_box.alert-remove:before  { content: \'\\4b\'; font-family: \'ElegantIcons\'; }
\n
\n.popup_box + .popup_box                           { top:  5.333333em;}
\n.popup_box + .popup_box + .popup_box              { top:  9.333333em;}
\n.popup_box + .popup_box + .popup_box + .popup_box { top: 13.333333em;}
\n
\n.popup_box .alert-icon {
\n    margin-left: auto;
\n    margin-right: 1em;
\n    width: 38px;
\n}
\n
\n.popup_box .alert-icon [fill]   {   fill: #00c6ee; }
\n.popup_box .alert-icon [stroke] { stroke: #00c6ee; }
\n
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Header
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.header {
\n    font-size: .8125rem;
\n}
\n
\nbody.has_sticky_header {
\n    padding-top: 3.875rem;
\n}
\n
\n.has_sticky_header .header {
\n    position: fixed;
\n    top: 0;
\n    right: 0;
\n    left: 0;
\n    z-index: 6;
\n}
\n
\n.header > .row {
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n}
\n
\n.header-top-nav {
\n    font-size: .875rem;
\n    padding-top: .5rem;
\n    padding-bottom: .4375rem;
\n}
\n
\n.header .header-top-nav.row {
\n    max-width: none;
\n}
\n
\n.header-top-nav ul {
\n    display: flex;
\n    align-items: center;
\n    margin: auto;
\n    max-width: 1140px;
\n    text-align: right;
\n    width: 100%;
\}
\n
\n.header-top-nav li {
\n    display: inline-block;
\}
\n
\n.header-top-nav-li:first-child {
\n    margin-left: auto;
\n}
\n
\n.header-top-nav-li-search {
\n    border-left: 2px solid var(\-\-primary);
\n    margin-left: .642857143em;
\n    padding-left: 1.64285714em;
\n}
\n
\n.header-top-nav-li a {
\n    padding: 0 .857142857em;
\n}
\n
\n.header-top-nav-li-search img {
\n    float: left;
\n}
\n
\n.header-top-nav-li-search {
\n    position: relative;
\n}
\n
\n.top-nav-searchbar-wrapper {
\n    overflow: hidden;
\n    position: absolute;
\n    top: -.5rem;
\n    right: 2rem;
\n    transition: all 250ms ease-in-out;
\n    width: 0;
\n    max-width: 26rem;
\n}
\n
\n.top-nav-searchbar-wrapper.shown {
\n    width: 18rem
\n}
\n
\n.header-left {
\n    float: left;
\n}
\n
\n.header-right {
\n    float: right;
\n}
\n
\n.header-item {
\n    float: left;
\n}
\n
\n.header-logo img {
\n    max-height: 44px;
\n}
\n
\n.header-action {
\n    padding: .75em 1.3725em;
\n    text-transform: uppercase;
\n}
\n
\n.top-nav-searchbar-button {
\n    float: left;
\n}
\n
\n.header-menu-expand.expanded:before {
\n    content: \'\';
\n    display: block;
\n    position: absolute;
\n    top: 3em;
\n    left: calc(50% - 12px);
\n    border: 12px solid transparent;
\n    border-bottom-color: #F5F5F5;
\n    z-index: 1;
\n}
\n
\n.header-menu-section > a {
\n    display: inline-block;
\n    position: relative;
\n    text-decoration: none;
\n    text-transform: uppercase;
\n}
\n
\n.header-menu-expand > img {
\n    border-radius: .3em;
\n    margin-top: -1.25em;
\n    margin-bottom: -1.25em;
\n}
\n
\n.submenu-expand {
\n    background: none;
\n    border: none;
\n    float: right;
\n    padding: 0;
\n}
\n
\n.header .form-input,
\n.header .input_group {
\n    font-size: 1rem;
\n}
\n
\n@media screen and (max-width: 767px)
\n{
\n    body {
\n        overflow-x: hidden;
\n    }
\n
\n    .header,
\n    .mobile-breadcrumbs.row.row {
\n        padding-left: 20px;
\n        padding-right: 20px;
\n    }
\n
\n    .has_sticky_header.mobile-menu-open .header {
\n        left: 19.375rem;
\n        width: 100%;
\n    }
\n
\n    .header .form-input,
\n    .header .input_group {
\n        max-width: 400px;
\n    }
\n    .menu-search-wrapper {
\n        padding: 0 1.5rem
\n    }
\n}
\n
\n@media screen and (min-width: 768px)
\n{
\n    .header > .row {
\n        flex-wrap: wrap;
\n    }
\n
\n    .header-right {
\n        margin-left: auto;
\n    }
\n
\n    .header-logo {
\n        -webkit-box-align: center;
\n        -ms-flex-align: center;
\n        align-items: center;
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        height: 100%;
\n        padding-right: 1.2em;
\n    }
\n
\n    .header-actions {
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        -webkit-box-flex: 1;
\n        -ms-flex: 1;
\n        flex: 1;
\n        -webkit-box-pack: justify;
\n        -ms-flex-pack: justify;
\n        justify-content: space-between;
\n    }
\n
\n    .header-action {
\n        padding: .92em 1.3725em;
\n        text-transform: uppercase;
\n    }
\n
\n    .header-menu-section > a {
\n        border: solid;
\n        border-width: 0 1px;
\n        margin-left: -1px;
\n        padding: 1.85em 2em;
\n    }
\n
\n    .header-menu-section > a:after {
\n        content: \'\';
\n        display: inline-block;
\n        border: solid transparent;
\n        border-width: .3em .3em 0;
\n        border-top-color: #fff;
\n        margin-left: .5em;
\n        vertical-align: .265em;
\n    }
\n
\n    .header-menu-section > a:only-child:after {
\n        content: none;
\n    }
\n
\n    .header-menu-expand.expanded:before {
\n        top: 4.33em;
\n    }
\n
\n    .header .form-input,
\n    .header .input_group {
\n        max-width: 250px;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) and (max-width: 1079px) {
\n    .header {
\n        font-size: 11px;
\n    }
\n
\n    .header-action,
\n    .header-menu-section > a {
\n        padding-left: 1em;
\n        padding-right: 1em;
\n    }
\n}
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Main menu
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.header-menu-section\-\-account {
\n    position: relative;
\n}
\n
\n.header-menu\-\-account {
\n    width: 11em;
\n    left: auto;
\n    right: 0;
\n    padding: 1em 0;
\n}
\n
\n@media screen and (min-width: 768px)
\n{
\n    .header-menu-row > ul {
\n        display: table;
\n        width: 100%;
\n    }
\n
\n    .header-menu.has_submenus .level_1 {
\n        padding-right: 1em;
\n    }
\n
\n    .header-menu .level_1.has_submenu {
\n        display: table-cell;
\n    }
\n
\n    .header-menu .level_1:not(.has_submenu) > a {
\n        margin-bottom: 0;
\n    }
\n
\n    .header-menu .level_1:not(.has_submenu) + li:not(.has_submenu) {
\n        padding-top: .6em;
\n    }
\n
\n    .header-menu .level_2 a:hover,
\n    .header-menu .level_2:hover > a {
\n        text-decoration: none;
\n    }
\n
\n    .header-menu .level3 {
\n        position: absolute;
\n        top: -12px;
\n        left: 60%;
\n        width: 100%;
\n        width: -webkit-calc(100% + 2em);
\n        width: calc(100% + 2em);
\n    }
\n
\n    .header-menu .level_2:hover .level3 {
\n        display: block;
\n    }
\n
\n    .header-menu .level_3 {
\n        border-bottom: 1px solid;
\n    }
\n
\n    .header-menu .level_3:last-child {
\n        border-bottom: none;
\n    }
\n
\n    .header-menu .level_3 a {
\n        padding: .1875em 1em .1875em 1.5em;
\n    }
\n
\n    .header-menu .level_3 a:before {
\n        left: 0;
\n        margin-top: .25em;
\n    }
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Quick contact
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n@media screen and (max-width: 767px)
\n{
\n    .quick_contact {
\n        background: #f7f7f7;
\n        position: fixed;
\n        bottom: 0;
\n        left: 0;
\n        width: 100%;
\n        z-index: 10;
\n    }
\n
\n    .quick_contact > ul {
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        margin: 0;
\n    }
\n
\n    .quick_contact-item {
\n        -webkit-box-flex: 1;
\n        -ms-flex: 1;
\n        flex: 1;
\n        font-size: 2rem;
\n        text-align: center;
\n    }
\n
\n    .quick_contact-item + .quick_contact-item {
\n        border-left: 1px solid #f1f1f1;
\n    }
\n
\n    .quick_contact-item > a {
\n        color: #787878;
\n        display: block;
\n        padding: .4375em;
\n        text-decoration: none;
\n        width: 100%;
\n    }
\n
\n    .quick_contact-item.has_text > a {
\n        padding: .1875em;
\n    }
\n
\n    .quick_contact-item-text {
\n        display: block;
\n        font-size: .375em;
\n        margin-top: .25em;
\n    }
\n
\n    \/\* Put some space at the bottom of the page, to ensure the \"quick contact\" section
\n       does not cover anything when the user scrolls to the bottom of the screen. \*\/
\n    .has_mobile_footer_menu .wrapper {
\n        padding-bottom: 4rem;
\n    }
\n
\n    .layout-event .wrapper {
\n        padding-bottom: 6.9rem;
\n    }
\n
\n    \/\* Stop the slaask button overlapping the menu and shrink it \*\/
\n    .slaask-button.slaask-button {
\n        font-size: 43px !important;
\n        bottom: .25rem;
\n        right: .15em;
\n    }
\n
\n    .has_mobile_footer_menu .slaask-button.slaask-button {
\n        bottom: 4.25rem;
\n    }
\n}
\n
\n@media screen and (min-width: 768px)
\n{
\n    .quick_contact {
\n        display: none;
\n    }
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Sidebar
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.sidebar-section > h2 {
\n    border-radius: 3px;
\n    font-size: 24px;
\n    font-weight: bold;
\n    margin-top: 0;
\n    margin-bottom: 22px;
\n    padding: .20833333333em;
\n    position: relative;
\n    text-align: center;
\n    text-transform: uppercase;
\n}
\n
\n.sidebar-section li {
\n    font-size: 15px;
\n    line-height: 18px;
\n    margin-bottom: 1rem;
\n}
\n
\n.li.search-criteria-li {
\n    margin: 15px 0;
\n}
\n
\n.sidebar-section li button {
\n    font-weight: inherit;
\n    letter-spacing: inherit;
\n}
\n
\n.search-criteria-remove .fa,
\n.search-criteria-reset .fa {
\n    width: 1.8125rem
\n}
\n
\n.sidebar-section .icon_search {
\n    position: relative;
\n    top: 3px
\n}
\n
\n.sidebar-filter-li .form-checkbox {
\n    font-size: 17px;
\n    position: relative;
\n    top: -.1rem;
\n}
\n
\ncourse-filter-keyword-input-wrapper {
\n    margin-top: -1.5rem;
\n}
\n
\n@media screen and (max-width: 767px)
\n{
\n    .content-columns {
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        -webkit-box-orient: vertical;
\n        -webkit-box-direction: normal;
\n        -ms-flex-direction: column;
\n        flex-direction: column;
\n    }
\n
\n    .content-columns .sidebar {
\n        order: 1;
\n    }
\n
\n    .sidebar-section > h2 {
\n        margin-top: 2rem;
\n    }
\n
\n    .content-columns .course-list-sidebar {
\n        order: 0;
\n    }
\n
\n    .course-list-sidebar {
\n        margin-left: -9px;
\n        margin-right: -9px;
\n    }
\n
\n    .course-filters-toggle {
\n        background: none;
\n        border: none;
\n        float: right;
\n        padding: .5em;
\n        position: relative;
\n    }
\n
\n    .course-filters-toggle.expanded {
\n        background: #fff;
\n        box-shadow: 0 4px 25px rgba(0, 0, 0, .11);
\n    }
\n
\n    /* This is needed to stop the expanded section's shadow from overlapping the button  */
\n    .course-filters-toggle.expanded:after {
\n        content: \'\';
\n        display: block;
\n        top: 0;
\n        right: 0;
\n        bottom: 0;
\n        left: 0;
\n        background: #fff;
\n        position: absolute;
\n        z-index: 4;
\n    }
\n
\n    .course-filters-toggle svg {
\n        position: relative;
\n        z-index: 5;
\n    }
\n    /* End of shadow fix */
\n
\n    .course-list-sidebar-content {
\n        background: #fff;
\n        box-shadow: 0 4px 25px rgba(0, 0, 0, .11);
\n        padding: 15px;
\n        position: absolute;
\n        left: 10px;
\n        right: 10px;
\n        z-index: 2;
\n    }
\n
\n    .course-list-sidebar .sidebar-section h2 {
\n        color: var(\-\-primary);
\n        font-size: 16px;
\n        line-height: 1.5;
\n        margin: 15px 0;
\n    }
\n
\n    .sidebar-filter-li > label {
\n        flex-direction: row-reverse;
\n    }
\n
\n    .sidebar-filter-li .form-checkbox {
\n        float: right;
\n        margin-left: .5rem;
\n    }
\n}
\n
\n@media screen and (min-width: 768px)
\n{
\n    .sidebar {
\n        float: left;
\n        width: 100%;
\n        max-width: 330px;
\n    }
\n
\n    .sidebar\-\-right {
\n        float: right;
\n    }
\n
\n    .sidebar + .content_area {
\n        float: left;
\n        padding-left: 25px;
\n        width: -webkit-calc(100% - 330px);
\n        width: calc(100% - 330px);
\n    }
\n
\n    .sidebar\-\-right + .content_area {
\n        padding-right: 25px;
\n        padding-left: 0;
\n    }
\n
\n    .sidebar-section {
\n        margin-bottom: 40px;
\n    }
\n
\n    .sidebar-section-collapse {
\n        background: none;
\n        border: none;
\n        color: inherit;
\n        font-size: .5em;
\n        padding: 0;
\n        position: absolute;
\n        top: 1.1em;
\n        right: 1.66666667em;
\n    }
\n
\n    .sidebar-filter-li > label {
\n        display: flex;
\n    }
\n
\n      .sidebar-filter-li .form-checkbox {
\n          margin-right: .5rem;
\n      }
\n
\n    .sidebar-section .form-input {
\n        background: #f2f2f2;
\n        border-radius: 4px 0 0 4px;
\n        padding: .782em;
\n    }
\n
\n    .sidebar-section-content ul {
\n        padding-left: 26px;
\n        padding-right: 26px;
\n    }
\n
\n    .sidebar-section li {
\n        line-height: 1.25;
\n    }
\n
\n    .sidebar-news-list li {
\n        border-bottom: 1px solid;
\n        padding: .4em 1.5em .15em;
\n        margin-bottom: 1em;
\n    }
\n
\n    a.sidebar-news-link,
\n    .eventTitle {
\n        text-decoration: underline;
\n    }
\n
\n    .search-filter-list > li:only-child {
\n        display: none;
\n    }
\n
\n    .search-criteria-category:after {
\n        content: \': \';
\n    }
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Breadcrumbs
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.breadcrumbs {
\n    list-style: none;
\n    margin: 0;
\n    padding: 0;
\n}
\n
\n.breadcrumbs li {
\n    float: left;
\n    margin: 0;
\n    padding: 1em 0;
\n}
\n
\n.breadcrumbs li a {
\n    color: #198ebe;
\n    color: var(\-\-primary);
\n}
\n
\n.breadcrumbs li a:hover {
\n    color: var(\-\-primary-hover);
\n    text-decoration: underline;
\n}
\n
\n.breadcrumbs li + li:before {
\n    display: inline-block;
\n    padding-right: .5rem;
\n    padding-left: .5rem;
\n    content: \'\/\';
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Content
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.content_area {
\n    font-weight: 200;
\n}
\n
\n.content_area > :first-child,
\n.page-content > :first-child {
\n    margin-top: 0;
\n}
\n
\n.page-content {
\n    line-height: 1.75;
\n}
\n
\n.page-content p,
\n.page-content h1 small {
\n    color: #222;
\n}
\n
\n.page-content h1,
\n.page-content h2,
\n.page-content h3,
\n.page-content h4,
\n.page-content h5,
\n.page-content h6 {
\n    margin: 1.5rem 0;
\n}
\n
\n.page-content h1,
\n.page-content h2 {
\n    font-weight: bold;
\n}
\n
\n.page-content h1 {
\n    border-bottom: 1px solid;
\n    font-size: 36px;
\n}
\n
\n.page-content h1 small {
\n    display: inline-block;
\n    font-size: 1rem;
\n    font-weight: 300;
\n    margin-left: 1em;
\n    position: relative;
\n    top: -.2em;
\n}
\n
\n.page-content h2 {
\n    font-size: 24px;
\n}
\n
\n.page-content header {
\n    font-weight: bold;
\n    margin: 1.5em 0 .5em;
\n}
\n
\n.page-content header * {
\n    line-height: 1.25;
\n    margin: 0;
\n}
\n
\n.page-content address {
\n    margin-top: 1em;
\n    margin-bottom: 1em;
\n}
\n
\n.simplebox h1, .simplebox h2, .simplebox h3, .simplebox h4, .simplebox h5, .simplebox h6 { border: none; }
\n
\n.page-content ol,
\n.page-content ul {
\n    padding-left: 0;
\n}
\n
\n.page-content ol {
\n    counter-reset: li;
\n}
\n
\n.page-content li {
\n    list-style: none;
\n    margin: .375em 0;
\n    padding-left: 2em;
\n    position: relative;
\n}
\n
\n.page-content li:before {
\n    position: absolute;
\n    left: 0;
\n    margin-right: .5em;
\n}
\n
\n.page-content ul > li:before {
\n    content: \'\\f00c\\a0 \';
\n    font-family: FontAwesome;
\n}
\n
\n.page-content ol > li:before {
\n    content: counter(li) \'.\\a0 \';
\n    counter-increment: li;
\n}
\n
\n.page-content a:not([class]),
\n.page-content .button\-\-link {
\n    text-decoration: underline;
\n}
\n
\n.page-content a:not([class]):hover,
\n.page-content .button\-\-link:hover {
\n    text-decoration: none;
\n}
\n
\n.page-content a:not([class]):visited {
\n    color: #551a8b;
\n}
\n
\n.page-content hr {
\n    border: solid;
\n    border-width: 0 0 1px;
\n}
\n
\n.page-content img {
\n    max-width: 100%;
\n    height: auto !important;
\n}
\n
\n.page-content .shadow {
\n    box-shadow: 0 1.125rem 1.6875rem rgba(0, 0, 0, .18);
\n}
\n
\n.banner-overlay-content .button,
\n.page-content .button {
\n    font-size: 18px;
\n    font-weight: 500;
\n    min-width: 180px;
\n    padding: .695em 1em;
\n}
\n
\n.banner-overlay-content .button {
\n    min-width: 240px;
\n}
\n
\n.event-details h1 {
\n    border: none;
\n    font-size: 1.875em;
\n    font-weight: 500;
\n}
\n
\n.search-result-url,
\n.search-result-content {
\n    font-size: 14px;
\n}
\n
\n.search-result > a { color: var(\-\-primary);}
\n
\n@media screen and (max-width: 767px) {
\n    .event-details h1 {
\n        font-size: 1.25em;
\n    }
\n
\n    .page-content header p {
\n        font-size: .75em;
\n        line-height: 1.25;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .event-details {
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n    }
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Banner
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\nbody.has_banner_search .content,
\nbody.has_breadcrumbs .content,
\n.layout-course_list .content,
\n.layout-course_list2 .content {
\n    margin-top: 0;
\n}
\n
\n.banner-section {
\n    min-height: 300px;
\n    position: relative;
\n}
\n
\nbody.has_banner_search .banner-section {
\n    min-height: 364px;
\n}
\n
\n.banner-section .swiper-container {
\n    z-index: 0;
\n}
\n
\n.banner {
\n    padding: 0;
\n}
\n
\n.banner-slide {
\n    overflow: hidden;
\n}
\n
\n.banner-section .swiper-pagination {
\n    position: absolute;
\n    bottom: 45px;
\n}
\n
\n.banner-image {
\n    background-position: center center;
\n    background-repeat: no-repeat;
\n    display: block;
\n    overflow: hidden;
\n    width: 100%;
\n    height: 300px;
\n}
\n
\n.layout-landing_page .banner-image {
\n    height: 700px;
\n}
\n
\n.banner-search {
\n    width: 100%;
\n    z-index: 11;
\n}
\n
\n.banner-search-title {
\n    border-radius: 5px 5px 0 0;
\n    font-size: 18px;
\n    padding: 10px 17px;
\n    position: relative;
\n    z-index: 1;
\n}
\n
\n.banner-search-title .fa {
\n    margin-right: .5em;
\n}
\n
\n.banner-search form {
\n    clear: both;
\n    font-size: 18px;
\n    position: relative;
\n}
\n
\n.banner-search .form-input {
\n    border: none;
\n}
\n
\n.banner-search form:after {
\n    content: \'\';
\n    clear: both;
\n    display: table;
\n}
\n
\n.banner-search .button\-\-continue {
\n    font-weight: 400;
\n    width: 100%;
\n}
\n
\n.banner,
\n.banner-overlay .row {
\n    height: 100%;
\n    position: relative;
\n}
\n
\n.banner-overlay {
\n    position: absolute;
\n    top: 0;
\n    width: 100%;
\n    height: 100%;
\n}
\n
\n.banner-overlay .row {
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -webkit-box-align: center;
\n    -ms-flex-align: center;
\n    align-items: center;
\n}
\n
\n.banner-overlay-content {
\n    position: absolute;
\n    width: 100%;
\n}
\n
\n.banner-slide\-\-right .banner-overlay-content {
\n    right: 0;
\n}
\n
\n.banner-slide\-\-center .banner-overlay-content {
\n    margin: auto;
\n    position: relative;
\n}
\n
\n.banner-overlay-content h1 {
\n    font-weight: bold;
\n    line-height: 1;
\n}
\n
\n.banner-slide-backdrop,
\n.banner-slide-backdrop:after {
\n    content: '';
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n}
\n
\n.banner-slide\-\-columns figure {
\n    position: relative;
\n}
\n
\n.banner-slide\-\-columns figure img {
\n    float: left;
\n}
\n
\n.banner-slide\-\-columns figcaption {
\n    background-color: rgba(0,0,0,.5);
\n    padding: 1.375em 2em;
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n}
\n
\n.banner-slide\-\-columns .button {
\n    font-size: .8125em;
\n    padding: .5em .67em;
\n}
\n
\n.banner-slide\-\-columns .button + .button {
\n    margin-left: 1em;
\n}
\n
\n.banner-slide\-\-event h2,
\n.banner-event-detail {
\n    text-shadow: -1px 1px 1px #555;
\n}
\n
\n.banner-slide\-\-event h2 {
\n    font-weight: bold;
\n    margin: .25em 0;
\n}
\n
\n.banner-slide\-\-columns .video-wrapper {
\n    margin-top: 1.375em;
\n}
\n
\n.banner-event-detail {
\n    font-size: 1.125em;
\n    line-height: 1;
\n    margin-bottom: 1.25em;
\n}
\n
\n.banner-event-detail-icon {
\n    font-size: 1.75em;
\n}
\n
\n@media screen and (max-width: 1959px)
\n{
\n    .banner-slide\-\-left .banner-image {
\n        background-position-x: right;
\n    }
\n
\n    .banner-slide\-\-right .banner-image {
\n        background-position-x: left;
\n    }
\n}
\n
\n@media screen and (max-width: 767px)
\n{
\n    .banner-section\-\-single {
\n        min-height: 325px;
\n    }
\n
\n    .banner-search {
\n        margin-top: -46px;
\n        position: relative;
\n    }
\n
\n    .banner-search > .row {
\n        padding-left: 0;
\n        padding-right: 0;
\n    }
\n
\n    .banner-search-title {
\n        width: 212px;
\n        width: fit-content;
\n        margin: 0 auto;
\n    }
\n
\n    .banner-search-title .fa {
\n        color: inherit;
\n    }
\n
\n    .banner-search form {
\n        padding: 9px 4px 11px;
\n    }
\n
\n    .banner-search .button\-\-continue {
\n        font-size: 1.1em;
\n        padding: .75em 1em;
\n    }
\n
\n    .banner-overlay {
\n        background: rgba(255, 255, 255, .5);
\n    }
\n
\n    .banner-overlay .row {
\n        background: none;
\n    }
\n
\n    .banner-overlay-content {
\n        position: relative;
\n    }
\n
\n    .banner-slide h1 {
\n        font-size: 2.125rem;
\n        font-weight: 500;
\n    }
\n
\n    .banner-slide h2 {
\n        font-size: 1.5rem;
\n    }
\n
\n    .banner-search-column {
\n        width: 100%;
\n        padding: 10px 15px;
\n    }
\n
\n    .banner-slide\-\-columns figure,
\n    .banner-slide-backdrop {
\n        height: 300px;
\n    }
\n
\n    .banner-slide-backdrop {
\n        background-size: cover;
\n        height: 300px;
\n    }
\n}
\n
\n@media screen and (min-width: 768px)
\n{
\n    .content {
\n        margin-top: 3rem;
\n    }
\n
\n    .banner-search {
\n        position: absolute;
\n        bottom: 0;
\n    }
\n
\n    .banner-search-title {
\n        float: left;
\n        margin: 0;
\n        padding-right: 41px;
\n    }
\n
\n    .banner-search form {
\n        border-radius: 0 5px 5px 5px;
\n        box-shadow: 0 1px 5px #333;
\n        padding: 22px 20px 21px;
\n    }
\n
\n    .banner-search form:before {
\n        content: \'\';
\n        position: absolute;
\n        z-index: -1;
\n        width: 96%;
\n        bottom: 7px;
\n        height: 9px;
\n        left: 2%;
\n        border-radius: 50%;
\n        box-shadow: 0 12px 28px rgba(0,0,0,.9);
\n    }
\n
\n    .banner-search .button\-\-continue {
\n        text-transform: uppercase;
\n    }
\n
\n    .banner-overlay-content {
\n        max-width: 480px;
\n    }
\n
\n    .banner-slide\-\-center .banner-overlay .row {
\n        background: rgba(255, 255, 255, .5);
\n    }
\n
\n    .banner-slide h1 {
\n        font-size: 2.875em;
\n        font-weight: 900;
\n    }
\n
\n    .banner-slide h2 {
\n        font-size: 2.25em;
\n    }
\n
\n    .banner-search-column {
\n        float: left;
\n        padding-left: 10px;
\n        padding-right: 10px;
\n        width: 35%;
\n    }
\n
\n    .banner-slide-backdrop {
\n        background-position: center bottom;
\n        -webkit-filter: blur(25px);
\n        filter: blur(25px);
\n        transform: scale(1.2); /* Enlarge the image, so that the blurry edges from the filter blur are cropped out */
\n        background-size: 83.333% auto; /* Undo the enlargement, while leaving the edges cropped out. */
\n    }
\n
\n    .banner-slide-backdrop:after {
\n        background-color: rgba(255, 255, 255, .1);
\n    }
\n
\n    .banner-search-column\-\-continue{
\n        width: 30%;
\n    }
\n}
\n
\n@media screen and (max-width: 1099px) {
\n    .banner-section\-\-has_mobile_slides .banner-image {
\n        height: 650px;
\n    }
\n
\n    .banner-section\-\-has_mobile_slides .banner-image\-\-desktop {
\n        display: none;
\n    }
\n}
\n
\n@media screen and (min-width: 1100px) {
\n    .banner-image\-\-mobile {
\n        display: none;
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .banner-search-column {
\n        width: 40.6%;
\n    }
\n
\n    .banner-search-column\-\-continue{
\n        width: 18.8%;
\n    }
\n}
\n
\n\/\* Subject drilldown menu \*\/
\n.search-drilldown {
\n    display: none;
\n    background: #fff;
\n    position: absolute;
\n    left: 0;
\n    width: 100%;
\n    z-index: 11;
\n}
\n
\n.search-drilldown:before {
\n    content: \'\';
\n    border: 10px solid transparent;
\n    border-bottom-color: #FFF;
\n    display: block;
\n    font-size: 16px;
\n    height: 8px;
\n    width: 20px;
\n    left: 10%;
\n    position: absolute;
\n    bottom: 100%;
\n}
\n
\n.search-drilldown h3 {
\n    font-size: 1em;
\n    font-weight: bold;
\n    margin-top: .66666667em;
\n    margin-bottom: .27777778em;
\n    padding-left: 15px;
\n    padding-right: 15px;
\n    text-transform: uppercase;
\n}
\n
\n.search-drilldown a {
\n    color: inherit;
\n}
\n
\n.search-drilldown-column {
\n    font-weight: 200;
\n    line-height: 1.66666667;
\n    padding: 10px 18px;
\n}
\n
\n.search-drilldown-column a,
\n.search-drilldown-column p {
\n    border-radius: 1em;
\n    display: block;
\n    margin-top: 0;
\n    margin-bottom: 0;
\n    padding-left: 15px;
\n    padding-right: 15px;
\n    position: relative;
\n}
\n
\n.search-drilldown-column p {
\n    font-weight: bolder;
\n}
\n
\n.search-drilldown-column a:hover {
\n    background-color: #e8f3f9;
\n    text-decoration: none;
\n}
\n
\n@media screen and (max-width: 767px)
\n{
\n    .search-drilldown {
\n        background-color: #F6F6F6;
\n        margin-top: 6px;
\n    }
\n
\n    .search-drilldown:before {
\n        border-bottom-color: #F6F6F6;
\n    }
\n
\n    .search-drilldown.active {
\n        display: block;
\n    }
\n
\n    .search-drilldown-close {
\n        position: absolute;
\n        top: 16px;
\n        right: 16px;
\n        cursor: pointer;
\n        width: 27px;
\n        height: 27px;
\n    }
\n
\n    .search-drilldown-close:before,
\n    .search-drilldown-close:after {
\n        content: \' \';
\n        position: absolute;
\n        top: 0;
\n        left: 13px;
\n        width: 2px;
\n        height: 28px;
\n    }
\n
\n    .search-drilldown-close:before {
\n        -webkit-transform: rotate(45deg);
\n        transform: rotate(45deg);
\n    }
\n
\n    .search-drilldown-close:after {
\n        -webkit-transform: rotate(-45deg);
\n        transform: rotate(-45deg);
\n    }
\n
\n    .search-drilldown-column + .search-drilldown-column {
\n        background: #FFF;
\n        display: none;
\n        padding: 0 19px;
\n        position: absolute;
\n        width: 100%;
\n    }
\n
\n    .search-drilldown-column + .search-drilldown-column > div {
\n        border-radius: 5px;
\n        box-shadow: 1px 1px 1px #ccc;
\n        border: 1px solid #EAEAEA;
\n    }
\n
\n    .search-drilldown-column\-\-category li {
\n        border-top: 1px solid;
\n    }
\n
\n    .search-drilldown-column a,
\n    .search-drilldown h3  {
\n        padding: 2px 19px;
\n    }
\n}
\n
\n@media screen and (min-width: 768px)
\n{
\n    .search-drilldown {
\n        top: 100%;
\n    }
\n
\n    .search-drilldown.active {
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n    }
\n
\n    .search-drilldown\-\-subject:before {
\n        left: 50%;
\n    }
\n
\n    .search-drilldown-close {
\n        display: none;
\n    }
\n
\n    .search-drilldown-column {
\n        -webkit-box-flex: 1;
\n        -ms-flex: 1;
\n        flex: 1;
\n    }
\n
\n    .search-drilldown-column {
\n        border-right: 1px solid;
\n    }
\n
\n    .search-drilldown-column:last-of-type {
\n        border-right: none;
\n    }
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Calendar
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.eventCalendar-wrap {
\n    border: solid #e1e1e1;
\n    border-width: 1px 0 1px 1px;
\n    border-radius: 3px 3px 0 3px;
\n    margin-top: .5em;
\n    text-align: center;
\n}
\n
\n.eventsCalendar-slider {
\n    border-bottom: 1px solid #ddd;
\n    min-height: 23em;
\n}
\n
\n.eventsCalendar-slider ul {
\n    margin: 0;
\n    padding: 0;
\n}
\n
\n.eventsCalendar-currentTitle {
\n    padding: 1em;
\n    border-bottom: 1px solid #99bbc1;
\n    text-transform: uppercase;
\n}
\n
\n.eventCalendar-wrap .arrow {
\n    color: #fff;
\n    line-height: 1.75em;
\n    padding: .5em .625em;
\n    text-decoration: none;
\n    top: .75em;
\n}
\n
\n.eventCalendar-wrap .arrow.prev {
\n    left: .5em;
\n}
\n
\n.eventCalendar-wrap .arrow.next {
\n    right: .5em;
\n}
\n
\n.eventCalendar-wrap .arrow:hover {
\n    opacity: .7;
\n}
\n
\n.eventCalendar-wrap .arrow span {
\n    border: solid #fff;
\n    height: .6875rem;
\n    width: .6875rem;
\n    font-size: 0;
\n    line-height: 0;
\n    float:left;
\n    text-indent: -5000px;
\n    -webkit-transform: rotate(45deg);
\n    transform: rotate(45deg);
\n}
\n
\n.eventCalendar-wrap .arrow.prev span {
\n    border-width: 0 0 2px 2px;
\n}
\n
\n.eventCalendar-wrap .arrow.next span {
\n    border-width: 2px 2px 0 0;
\n}
\n
\n.eventsCalendar-daysList {
\n    color: #212121;
\n}
\n
\n.eventsCalendar-daysList.showAsWeek li {
\n    height: 3em;
\n    line-height: 3em;
\n    margin: 0;
\n}
\n
\n.eventsCalendar-daysList.showAsWeek .eventsCalendar-day-header {
\n    height: 2em;
\n}
\n
\n.eventsCalendar-daysList li a {
\n    color: inherit;
\n    font-size: 1em;
\n}
\n
\n.eventsCalendar-day.today {
\n    background-color: #fff;
\n    border-style: solid;
\n    border-width: 0 1px 1px;
\n    color: #000;
\n}
\n
\n.eventsCalendar-day.today.dayWithEvents {
\n    background-color: rgba(255, 255, 255, .9);
\n}
\n
\n.eventsCalendar-day.today.current {
\n    background-color: rgba(255, 255, 255, .8);
\n}
\n
\n.dayWithEvents a {
\n    text-decoration: underline;
\n}
\n
\n.eventsCalendar-list {
\n    margin: 0;
\n    padding: 0;
\n    list-style: none;
\n}
\n
\n.eventsCalendar-list time em {
\n    font-style: normal;
\n    margin-right: .5em;
\n}
\n
\n.eventsCalendar-list-wrap,
\n.sidebar-news-list li {
\n    max-width: 100%;
\n    padding-left: 1.625rem;
\n    padding-right: 1.625rem;
\n    text-align: left;
\n}
\n
\n.eventsCalendar-subtitle {
\n    margin: 1.5em 0 .5em;
\n    padding: 0;
\n    text-transform: uppercase;
\n}
\n
\n.eventsCalendar-list > li,
\n.sidebar-news-list > li {
\n    border-bottom: 1px solid #bbb;
\n    margin: .7em 0;
\n    min-height: 5.25em;
\n}
\n
\n.eventsCalendar-list > li:last-child {
\n    border-bottom: none;
\n}
\n
\n.eventsCalendar-list > li > time {
\n    display: block;
\n    margin-bottom: .25em;
\n}
\n
\n.sidebar-news-link {
\n    text-decoration: underline;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .eventCalendar-wrap {
\n        border-width: 1px;
\n        border-bottom-right-radius: 3px;
\n    }
\n}
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #News feed (home page)
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.news-section {
\n    padding-top: .75em;
\n    position: relative;
\n}
\n
\n.news-section .swiper-wrapper {
\n    padding-bottom: 35px;
\n}
\n
\n.news-section .swiper-slide {
\n    -webkit-box-sizing: border-box;
\n    box-sizing: border-box;
\n    padding-left: 45px;
\n    padding-right: 45px;
\n}
\n
\n.news-slider-title {
\n    font-size: 1.375em;
\n    margin: 0;
\n    white-space: nowrap;
\n    text-transform: uppercase;
\n}
\n
\n.news-slider-title:after {
\n    content: \':\';
\n}
\n
\n.news-slider-summary {
\n    font-weight: 200;
\n}
\n
\n.news-slider-summary p {
\n    margin: 0;
\n}
\n
\n.news-slider-link {
\n    white-space: nowrap;
\n}
\n
\n.news-slider-link:after {
\n    content: \'\\0a\\bb\';
\n}
\n
\n.swiper-pagination-bullet {
\n    border-radius: 50%;
\n    display: inline-block;
\n    margin: 2px;
\n    opacity: 1;
\n    width: 10px;
\n    height: 10px;
\n}
\n
\n.news-section .swiper-button-next,
\n.news-section .swiper-button-prev {
\n    -webkit-transform: scale(.5);
\n    transform: scale(.5);
\n}
\n
\n@media screen and (max-width: 767px)
\n{
\n    .news-section {
\n        margin-bottom: 13px;
\n    }
\n}
\n
\n@media screen and (min-width: 768px)
\n{
\n    .news-section {
\n        margin-top: 34px;
\n        margin-bottom: 42px;
\n        padding-bottom: .75em;
\n    }
\n
\n    .news-section .row {
\n        -webkit-box-align: center;
\n        -ms-flex-align: center;
\n        align-items: center;
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        position: relative;
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .news-section .swiper-slide {
\n        -webkit-box-align: center;
\n        -ms-flex-align: center;
\n        align-items: center;
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        padding: 37px 45px 0 10px;
\n    }
\n
\n    .news-section\-\-testimonials {
\n        padding: 0;
\n    }
\n
\n    .news-slider-title-link {
\n        padding-left: 45px;\/\*\ space needed for the swiper arrow *\/
\n    }
\n
\n    .news-slider-summary {
\n        margin: 0;
\n        overflow: hidden;
\n        padding-right: 1em;
\n        text-overflow: ellipsis;
\n        white-space: nowrap;
\n    }
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #News feed (news page)
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.news-area {
\n    \-\-category-color: var(\-\-primary);
\n}
\n
\n.news-result .news-result-title {
\n    border-bottom: 1px solid #ccc;
\n    font-size: 1.5rem;
\n    font-weight: 500;
\n    margin-top: 0;
\n    padding: .7em 0;
\n}
\n
\n.news-result-image figure {
\n    margin-bottom: 1em;
\n    position: relative;
\n}
\n
\n.news-result-image img {
\n    border: 1px solid #EEE;
\n    border-radius: 5px;
\n    display: block;
\n    min-height: 5rem;
\n    width: 100%;
\n}
\n
\n.news-result-date {
\n    font-size: 15px;
\n    font-weight: normal;
\n    line-height: 1;
\n}
\n
\n.news-result-image .news-result-date {
\n    border-radius: 5px 0;
\n    padding: .75em 1em;
\n    position: absolute;
\n    right: 0;
\n    bottom: 0;
\n    text-align: center;
\n}
\n
\n.news-result-text .news-result-date {
\n    border-radius: 5px;
\n    margin-top: 1em;
\n    padding: .55em 1.558em;
\n}
\n
\n.news-result-summary {
\n    margin-bottom: 1em;
\n}
\n
\n.news-result-read_more {
\n    border-radius: 5px;
\n    font-size: 17px;
\n    font-weight: normal;
\n    padding: .677em 2.1em;
\n    text-transform: uppercase;
\n}
\n
\n.news-category-tabs-section {
\n    border-bottom: 3px solid #777;
\n    border-bottom-color: var(\-\-category-color);
\n}
\n
\n.news-category-tab {
\n    background: #f6f6f6;
\n    display: inline-block;
\n    padding: .657rem 1rem;
\n    text-align: center;
\n}
\n
\n.news-category-tab h3 {
\n    color: #777;
\n}
\n
\n.page-content .news-page-title {
\n    margin-bottom: 11px;
\n}
\n
\n.news-page-content > :first-child {
\n    margin-top: 0;
\n}
\n
\n.news-page-content > :last-child {
\n    margin-bottom: 0;
\n}
\n
\n.news-sidebar-item {
\n    border-bottom: 1px dotted #ccc;
\n    line-height: 1.25;
\n}
\n
\n.news-page-subscribe {
\n    background: #f6f6f6;
\n    padding: 30px;
\n}
\n
\n.news-page-subscribe h5 {
\n    margin-top: 0;
\n}
\n
\n.news-page-subscribe .form-input {
\n    background: none;
\n    border-color:#c4c4c4;
\n    border-radius: 0;
\n}
\n
\n
\n.news-sidebar-author {
\n    color: #777;
\n    font-size: 12px;
\n    text-transform: uppercase;
\n}
\n
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .news-result {
\n        margin-bottom: 1.875em;
\n    }
\n
\n    .news-result + .news-result:before {
\n        content: \'\';
\n        display: block;
\n        margin: 0 -19px 1.875em;
\n        height: 1px;
\n    }
\n
\n    .news-result .news-result-title {
\n        border-bottom: 0;
\n        margin-bottom: 0;
\n    }
\n
\n    .news-result-summary {
\n        margin: 1em 0;
\n    }
\n}
\n
\n@media screen and (min-width: 530px) and (max-width: 767px)
\n{
\n    .page-content\-\-news {
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        -ms-flex-wrap: wrap;
\n        flex-wrap: wrap;
\n    }
\n
\n    .news-result {
\n        width: 50%;
\n    }
\n
\n    .news-result:nth-child(2):before {
\n        display: none;
\n    }
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .news-category-tab {
\n        float: left;
\n        min-width: 104px;
\n    }
\n
\n    .news-category-tab + .news-category-tab {
\n        margin-left: 4px;
\n    }
\n
\n    .news-category-tabs-section + .fullwidth {
\n        padding-top: 32px;
\n    }
\n
\n    .news-category-tab h3 {
\n        font-size: 16px;
\n    }
\n
\n    .news-category-embed {
\n        padding-bottom: 1.25rem;
\n    }
\n
\n    .news-category-embed .container {
\n        padding-top: .8rem;
\n    }
\n
\n    .page-content .news-category-embed-intro h1 {
\n        margin-top: 1.3rem;
\n        margin-bottom: .75rem;
\n    }
\n
\n    .page-content .news-category-embed-intro p {
\n        margin-top: .75rem;
\n        margin-bottom: 1.3rem
\n    }
\n
\n    .layout-news .news-feed-item-data,
\n    .layout-news2 .news-feed-item-data {
\n        color: var(\-\-category-color);
\n        font-size: 10px;
\n    }
\n
\n    .news-feed-item-button.button {
\n        min-width: 160px;
\n        padding: 1em;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .news-result-text .news-result-date {
\n        float: right;
\n        margin-left: .5em;
\n    }
\n
\n    .news-category-tabs-section + .fullwidth {
\n        padding-top: 70px;
\n    }
\n
\n    .news-category-tab {
\n        min-width: 198px;
\n    }
\n
\n    .news-feed-item-button.button {
\n        min-width: 200px;
\n        padding: 1em;
\n    }
\n
\n    .news-sidebar-item,
\n    .news-page-subscribe {
\n        width: 270px;
\n    }
\n
\n    .row.gutters > .news-column-content {
\n        width: 70%;
\n        padding-right: 37px;
\n        border-right: 1px dotted #ccc;
\n    }
\n
\n    .row.gutters > .news-column-feed {
\n        padding-left: 37px;
\n        width: 30%;
\n    }
\n
\n    .news-category-embed {
\n        padding-top: 1.45rem;
\n        padding-bottom: 3.45rem;
\n    }
\n
\n    .page-content .news-category-embed-intro h1 {
\n        margin-top: 2.5rem;
\n        margin-bottom: .5rem;
\n    }
\n
\n    .page-content .news-category-embed-intro p {
\n        font-size: 20px;
\n        line-height: 1.5;
\n        margin-top: .5rem;
\n        margin-bottom: 2.5rem;
\n        max-width: 520px;
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .news-result + .news-result {
\n        border-top: 1px solid;
\n        margin-top: 1.5em;
\n    }
\n
\n    .news-result-image + .news-result-text {
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        -webkit-box-orient: vertical;
\n        -webkit-box-direction: normal;
\n        -ms-flex-direction: column;
\n        flex-direction: column;
\n    }
\n
\n    .news-result-read_more {
\n        margin-right: auto;
\n        margin-top: auto;
\n    }
\n}
\n
\n.row.news-filters {
\n    background: #fff;
\n    box-shadow: 0 4px 25px rgba(0, 0, 0, .11);
\n    padding: 15px 3px 15px 5px;
\n    margin-bottom: 35px;
\n    z-index: 5;
\n}
\n
\n.news-filter-group h3 {
\n    margin: 0;
\n}
\n
\n.news-filter-group-btn {
\n    background: none;
\n    border: none;
\n    color: inherit;
\n    font: inherit;
\n    padding: 0;
\n}
\n
\n.news-filter-group .input_group {
\n    border-radius: 0;
\n}
\n
\n.news-filter-group-list.news-filter-group-list {
\n    font-size: 14px;
\n    padding: 0;
\n}
\n
\n.news-filter-group-list li + li{
\n    margin-top: 1em
\n}
\n
\n.news-filter-group-list .form-checkbox-helper {
\n    margin-right: 8px
\n}
\n.news-list-by-media_type.news-list-by-media_type h2 {
\n    margin: 0 0 29px
\n}
\n
\n.news-filter-reset {
\n    background: #f4f4f4;
\n    border-radius: 2em;
\n    color: #555;
\n    display: inline-block;
\n    font-size: 14px;
\n    line-height: 1.43;
\n    margin-bottom: 25px;
\n    padding: 0 10px;
\n}
\n
\n.news-filter-reset::before {
\n    content: \'x\\a0  \';
\n    font-weight: bold;
\n}
\n
\n.news-feed-item-body {
\n    padding: 1rem 1.5rem 1.5rem;
\n}
\n.news-category-column {
\n    margin-bottom: 1.5rem;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .news-filters-toggle-row {
\n        padding: 17px 12px 0;
\n    }
\n
\n    .news-filters {
\n        display: flex;
\n        flex-direction: column;
\n        margin-left: 10px;
\n        margin-right: 10px;
\n        position: absolute;
\n        width: calc(100% - 22px);
\n        z-index: 2;
\n    }
\n
\n    .news-filters ~ .news-container {
\n        margin-top: -65px;
\n    }
\n
\n    .news-filter-group[data-filter=\"keyword\"] {
\n        order: -1;
\n    }
\n
\n    .news-filter-group {
\n        margin-bottom: 13px;
\n    }
\n
\n    .news-filter-group h3 {
\n        line-height: 1.5;
\n        margin: 15px 0;
\n    }
\n
\n    .news-filter-group[data-filter=\"keyword\"] h3 {
\n        margin-top: 0;
\n        margin-bottom: 5px;
\n    }
\n
\n    .news-filter-group-btn {
\n        outline: none;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .news-filters {
\n        display: flex;
\n        align-items: center;
\n    }
\n
\n    .row.news-filters {
\n        padding: 15px 4px 15px 11px;
\n    }
\n
\n    .news-filter-group-btn::after {
\n        content: \'\';
\n        border: solid currentColor;
\n        border-width: 0 1px 1px 0;
\n        display: inline-block;
\n        margin-left: 17px;
\n        margin-right: 3px;
\n        position: relative;
\n        top: -4px;
\n        transform: rotate(45deg);
\n        width: .5em;
\n        height: .5em;
\n    }
\n
\n    .news-filter-group-list {
\n        background: #fff;
\n        box-shadow: 0 4px 25px rgba(0, 0, 0, .11);
\n        position: absolute;
\n        top: 100%;
\n        top: calc(100% + 20px);
\n        min-width: calc(100% - 20px);
\n        width: max-content;
\n        width: intrinsic;           /* Safari/WebKit uses a non-standard name */
\n        width: -moz-max-content;    /* Firefox/Gecko */
\n        white-space: nowrap;
\n        z-index: 1;
\n    }
\n
\n    .news-filter-group-list.news-filter-group-list {
\n        padding: 2em;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) and (max-width: 1279px) {
\n    .news-filter-group-btn {
\n        font-size: 1rem;
\n    }
\n}
\n
\n@media screen and (min-width: 1280px) {
\n    .news-filter-group[data-filter=\"course_category\"] { width: 28.2%;  }
\n    .news-filter-group[data-filter=\"course_type\"]     { width: 24.25%; }
\n    .news-filter-group[data-filter=\"media_type\"]      { width: 20.2%;  }
\n    .news-filter-group[data-filter=\"keyword\"]         { width: 27.35%; }
\n}
\n
\n
\n
\n
\na.news-result-link.news-result-link-override{
\n    text-decoration: none;
\n    color: inherit;
\n}
\n\/\* News details page \*\/
\n.news-story-image img {
\n    border-radius: 5px;
\n}
\n
\n.news-story-navigation a {
\n    font-weight: bolder;
\n    text-decoration: none;
\n}
\n
\n.news-story-social {
\n    border: solid;
\n    border-width: 1px 0;
\n    margin: .5em 0;
\n    padding: 1em 0;
\n    min-height: 78px;
\n    min-height: -webkit-calc(46px + 2em);
\n    min-height: calc(48px + 2em);
\n}
\n
\n.news-story-social a {
\n    color: #212121;
\n    text-decoration: none;
\n}
\n
\n.news-story-social .at-icon-wrapper {
\n    background: none !important;
\n}
\n
\n.news-story-social-link {
\n    display: inline-block;
\n    font-weight: bold;
\n    margin: 5px .25em;
\n    min-width: 36px;
\n    min-height: 36px;
\n}
\n
\n.news-story-social-link\-\-share {
\n    float: left;
\n    margin-right: 1em;
\n}
\n
\n.news-story-share_icon {
\n    margin-right: 1em;
\n}
\n
\n.news-story-social-link svg {
\n    border-radius: 50%;
\n    padding: .25em;
\n}
\n
\n.news-page-image {
\n    margin-bottom: 30px;
\n}
\n
\n.addthis_toolbox {
\n    border: dotted #ccc;
\n    border-width: 1px 0;
\n    padding: 9px 0 4px;
\n}
\n
\n.addthis_toolbox a {
\n    display: inline-block;
\n    line-height: 1;
\n    margin: 0 0 0 9px;
\n}
\n
\n.addthis_toolbox svg { color: var(\-\-success); }
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Testimonials
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.testimonial-signature {
\n    font-size: 1.125rem;
\n    font-style: normal;
\n    font-weight: 400;
\n}
\n
\n.testimonial-company {
\n    font-weight: 400;
\n    margin-top: .5em;
\n}
\n
\n.testimonial-content {
\n    background: #fff;
\n    border: 3px solid #ededed;
\n    margin-left: 0;
\n    margin-bottom: 2.75rem;
\n    padding: 1.2rem;
\n    position: relative;
\n}
\n
\n.testimonial-content p {
\n    font-size: 1.25rem;
\n    line-height: 1.333;
\n}
\n
\n.testimonial-content p:before {
\n    content: \'\\f10e\';
\n    color: #ededed;
\n    float: left;
\n    font-family: \'fontAwesome\';
\n    position: absolute;
\n    top: 7px;
\n}
\n
\n.testimonials-slider-testimonial {
\n    padding-right: 2.5em;
\n    position: relative;
\n}
\n
\n.testimonials-slider-testimonial:before,
\n.testimonials-slider-testimonial:after {
\n    font-family: fontAwesome;
\n    font-size: 1.5em;
\n}
\n
\n.testimonials-slider-testimonial:before {
\n    content: \'\\f10d\';
\n}
\n
\n.testimonials-slider-testimonial:after {
\n    content: \'\\f10e\';
\n    position: absolute;
\n    right: 0;
\n    bottom: 0;
\n}
\n
\n.testimonials-slider:not([data-slides=\"1\"]) .swiper-slide:not(.swiper-slide-active) {
\n    visibility: hidden;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .testimonials-slider-testimonial,
\n    .testimonials-slider-signature {
\n        display: block;
\n        margin-left: auto;
\n        margin-right: auto;
\n        max-width: 330px;
\n    }
\n
\n    .testimonials-slider .swiper-button-prev,
\n    .testimonials-slider .swiper-button-next {
\n        background: #fff;
\n        top: unset;
\n        bottom: 0;
\n        z-index: 11;
\n    }
\n
\n    .testimonials-slider .swiper-button-prev { left: 15px;  }
\n    .testimonials-slider .swiper-button-next { right: 15px; }
\n}
\n
\n@media only screen and (min-width: 641px) {
\n    .testimonial-content {
\n        padding-left: 3.5rem;
\n    }
\n
\n    .testimonial-content p:before {
\n        font-size: 1.4rem;
\n        top: 2.5rem;
\n        left: 1.1rem;
\n    }
\n}
\n
\n.testimonial-content:after,
\n.testimonial-content:before {
\n    top: 100%;
\n    left: 30px;
\n    border: solid transparent;
\n    content: \' \';
\n    height: 0;
\n    width: 0;
\n    position: absolute;
\n    pointer-events: none;
\n}
\n
\n.testimonial-content:after {
\n    border-color: rgba(255, 255, 255, 0);
\n    border-top-color: #fff;
\n    border-width: 20px;
\n    margin-left: -20px;
\n}
\n
\n.testimonial-content:before {
\n    border-color: rgba(238, 238, 238, 0);
\n    border-top-color: #ededed;
\n    border-width: 24px;
\n    margin-left: -24px;
\n}
\n
\n/* Embedded testimonial */
\n.testimonial-embed {
\n    background-color: var(\-\-primary);
\n    background-position: center;
\n    background-size: cover;
\n    color: #fff;
\n    padding-top: 3.5625rem;
\n    padding-bottom: 3rem;
\n}
\n
\n.testimonial-embed * {
\n    color: inherit;
\n}
\n
\n.testimonial-embed-text {
\n    font-weight: 300;
\n    max-width: 32.75rem;
\n}
\n
\n.testimonial-embed .testimonial-embed-content,
\n.testimonial-embed .testimonial-embed-content p,
\n.testimonial-embed-read_more {
\n    font-size: 1.125rem;
\n    line-height: 1.2777777778;
\n}
\n
\n.testimonial-embed-content::before {
\n    content: url(\'data:image/svg+xml; utf8, <svg width=\"54\" height=\"38\" viewBox=\"0 0 54 38\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M25.2581 26.5479C25.2581 29.8447 24.2129 32.4475 22.1226 34.7032C20.0323 36.7854 17.2452 38 13.9355 38C9.75484 38 6.44516 36.4384 3.83226 33.4886C1.39355 30.3653 0 26.7215 0 22.5571C0 16.3105 2.26452 11.105 6.96774 6.76712C11.4968 2.25571 17.071 0 23.5161 0L23.6903 1.90868C19.6839 1.90868 16.0258 3.2968 12.7161 6.07306C9.40645 8.84932 7.83871 12.1461 7.83871 15.79C7.83871 16.6575 8.0129 17.1781 8.1871 17.6986C8.36129 18.0457 8.70968 18.3927 9.05806 18.3927C9.58064 18.3927 10.6258 18.0457 11.671 17.5251C12.8903 16.8311 13.9355 16.6575 14.8065 16.6575C17.5935 16.6575 20.0323 17.5251 22.1226 19.4338C24.2129 21.3425 25.2581 23.7717 25.2581 26.5479ZM54 26.5479C54 29.8447 52.9548 32.4475 50.8645 34.7032C48.7742 36.7854 45.9871 38 42.6774 38C38.4968 38 35.1871 36.4384 32.5742 33.4886C29.9613 30.3653 28.5677 26.7215 28.5677 22.5571C28.5677 16.3105 30.8323 11.105 35.5355 6.76712C40.0645 2.25571 45.6387 0 52.2581 0L52.4323 1.90868C48.4258 1.90868 44.7677 3.2968 41.4581 6.07306C38.1484 8.84932 36.4065 12.1461 36.4065 15.79C36.4065 16.6575 36.5806 17.1781 36.7548 17.6986C36.929 18.0457 37.2774 18.3927 37.6258 18.3927C38.1484 18.3927 39.1936 18.0457 40.2387 17.5251C41.4581 17.0046 42.5032 16.6575 43.3742 16.6575C46.1613 16.6575 48.6 17.5251 50.6903 19.4338C52.7806 21.3425 54 23.7717 54 26.5479Z\" fill=\"white\"/></svg>\');
\n    float: left;
\n    margin: -1em .25em -.575em 0;
\n}
\n
\n.testimonial-embed-content p:last-child::after {
\n    content: url(\'data:image/svg+xml; utf8, <svg width=\"54\" height=\"38\" viewBox=\"0 0 54 38\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M28.7419 11.4521C28.7419 8.15525 29.7871 5.55251 31.8774 3.2968C33.9677 1.21461 36.7548 1.50762e-06 40.0645 1.21828e-06C44.2452 8.52795e-07 47.5548 1.56165 50.1677 4.51142C52.6064 7.6347 54 11.2785 54 15.4429C54 21.6895 51.7355 26.895 47.0323 31.2329C42.5032 35.7443 36.929 38 30.4839 38L30.3097 36.0913C34.3161 36.0913 37.9742 34.7032 41.2839 31.9269C44.5935 29.1507 46.1613 25.8539 46.1613 22.21C46.1613 21.3425 45.9871 20.8219 45.8129 20.3014C45.6387 19.9543 45.2903 19.6073 44.9419 19.6073C44.4194 19.6073 43.3742 19.9543 42.329 20.4749C41.1097 21.169 40.0645 21.3425 39.1935 21.3425C36.4065 21.3425 33.9677 20.4749 31.8774 18.5662C29.7871 16.6575 28.7419 14.2283 28.7419 11.4521ZM-2.3209e-06 11.4521C-2.60911e-06 8.15526 1.04516 5.55251 3.13548 3.29681C5.2258 1.21462 8.0129 4.02032e-06 11.3226 3.73098e-06C15.5032 3.36549e-06 18.8129 1.56165 21.4258 4.51142C24.0387 7.63471 25.4323 11.2785 25.4323 15.4429C25.4323 21.6895 23.1677 26.895 18.4645 31.2329C13.9355 35.7443 8.36129 38 1.74194 38L1.56774 36.0913C5.57419 36.0913 9.23226 34.7032 12.5419 31.9269C15.8516 29.1507 17.5935 25.8539 17.5935 22.21C17.5935 21.3425 17.4194 20.8219 17.2452 20.3014C17.071 19.9543 16.7226 19.6073 16.3742 19.6073C15.8516 19.6073 14.8064 19.9543 13.7613 20.4749C12.5419 20.9954 11.4968 21.3425 10.6258 21.3425C7.83871 21.3425 5.4 20.4749 3.30968 18.5662C1.21935 16.6575 -2.07819e-06 14.2283 -2.3209e-06 11.4521Z\" fill=\"white\"/></svg>\');
\n    position: absolute;
\n    margin-left: .444444444444444em;
\n    margin-top: .2em;
\n}
\n
\n.testimonial-embed-signature {
\n    margin-top: 2.75rem;
\n}
\n
\n.testimonial-embed-more {
\n    flex: 1;
\n}
\n
\n.testimonial-embed-read_more::after {
\n    content: url(\'data:image/svg+xml; utf8, <svg width=\"32\" height=\"24\" viewBox=\"0 0 32 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M31.5 11.4L20.7 0.600049C20.5 0.400049 20.3 0.300049 20 0.300049C19.8 0.300049 19.5 0.400049 19.3 0.600049C19.1 0.800049 19.1 1.00005 19 1.20005V1.30005C19 1.50005 19.1 1.80005 19.3 1.90005L28.5 11.1H1.19999C0.699988 11.1 0.299988 11.5 0.299988 12C0.299988 12.5 0.699988 12.9 1.19999 12.9H28.6L19.5 22.1C19.1 22.5 19.1 23.1 19.5 23.4C19.7 23.6 19.9 23.7001 20.2 23.7001C20.4 23.7001 20.7 23.6 20.9 23.4L31.6 12.6C31.8 12.4 31.8 11.8 31.5 11.4Z\" fill=\"white\"/></svg>\');
\n    margin-left: .5em;
\n    position: relative;
\n    top: .125em;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .testimonial-embed .container {
\n        padding-left: 1.25rem;
\n        padding-right: 1.25rem;
\n    }
\n
\n    .testimonial-embed .testimonial-embed-content p {
\n        margin-bottom: 1.625rem;
\n    }
\n
\n    .testimonial-embed-signature {
\n        font-size: 14px;
\n        line-height: 19px;
\n        margin-top: 2.5rem;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .testimonial-embed {
\n        min-height: 43.75rem;
\n        padding-top: 11.125rem;
\n    }
\n
\n    .testimonial-embed .testimonial-embed-content,
\n    .testimonial-embed .testimonial-embed-content p,
\n    .testimonial-embed-read_more {
\n        font-size: 1.5rem;
\n        line-height: 1.25;
\n    }
\n
\n    .testimonial-embed .testimonial-embed-content p {
\n        margin-bottom: 2rem;
\n    }
\n
\n    .testimonial-embed-content::before {
\n        margin-top: -.575em;
\n    }
\n
\n    .testimonial-embed-content p:last-child::after {
\n        margin-left: .8em;
\n    }
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Panels
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.panel {
\n    border: 1px solid #DFE1E0;
\n    border-radius: 5px;
\n    margin-top: 10px;
\n    margin-bottom: 10px;
\n}
\n
\n.panels-feed\-\-home_content .panel {
\n    background: #F3F4F4;
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -webkit-box-orient: vertical;
\n    -webkit-box-direction: normal;
\n    -ms-flex-direction: column;
\n    flex-direction: column;
\n    width: 100%;
\n}
\n
\n.panel-image,
\n.panel-image img {
\n    display: block;
\n    position: relative;
\n    width: 100%;
\n}
\n
\n.panel-image:first-child img {
\n    border-radius: 5px 5px 0 0;
\n}
\n
\n.panel-image:first-child .panel-date {
\n    border-top-right-radius: 5px;
\n}
\n
\n.panel-date {
\n    background: #000;
\n    color: #fff;
\n    line-height: 1;
\n    padding: .5em 1.05em .75em;
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n    text-align: center;
\n}
\n
\n.panel-date-day {
\n    display: block;
\n    font-weight: 500;
\n    font-size: 2em;
\n}
\n
\n.panel-date-month {
\n    font-size: 1.1em;
\n    text-transform: uppercase;
\n}
\n
\n.panel-link {
\n    border-radius: 0 0 5px 5px;
\n    display: block;
\n    text-transform: uppercase;
\n}
\n
\n.panel-title,
\n.panel-text {
\n    align-items: center;
\n    display: flex;
\n    min-height: 3.75rem;
\n    overflow: hidden;
\n    padding: 0 1.0625rem;
\n}
\n
\n.panel-title:first-child {
\n    min-height: 4.5rem;
\n}
\n
\n.panel-title > *,
\n.panel-text > * {
\n    width: 100%;
\n}
\n
\n.panel-title h3 {
\n    font-size: 1.25em;
\n    font-weight: 500;
\n    line-height: 1.25;
\n    margin: .5em 0;
\n}
\n
\n.panel-text {
\n    font-size: 0.875em;
\n    font-weight: 200;
\n    line-height: 1.25;
\n    min-height: 3.125rem;
\n}
\n
\n.panels-feed\-\-home_content .panel-title {
\n    margin: auto;
\n    padding-left: 15px;
\n    padding-right: 15px;
\n    text-align: center;
\n}
\n
\n.panels-feed\-\-home_content .panel-title h3 {
\n    font-weight: 400;
\n    font-size: 1.5em;
\n}
\n
\n.events_feed .panel-title {
\n    align-items: flex-start;
\n}
\n
\n.carousel-section .panel-title {
\n    line-height: 1;
\n    margin: auto;
\n    text-align: center;
\n    padding: 0 1.25em;
\n}
\n
\n.carousel-section .panel-title h3 {
\n    -webkit-box-orient: vertical;
\n    display: -webkit-box;
\n    -webkit-line-clamp: 2;
\n    max-height: 2.25rem;
\n    text-overflow: ellipsis;
\n}
\n
\n.feed-heading {
\n    text-align: center;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .feed-heading {
\n        font-size: 1.6875em;
\n        margin-bottom: .375em;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .feed-heading {
\n        font-size: 2.25rem;
\n    }
\n
\n    .panels-feed {
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        -ms-flex-wrap: wrap;
\n        flex-wrap: wrap;
\n    }
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .bars-section {
\n        margin-top: 9px;
\n        padding: 9px 0 11px;
\n    }
\n
\n    .panels-feed\-\-home_content > [class*=\"col-\"]:after {
\n        content: \'\';
\n        display: block;
\n        margin: 19px -19px 9px;
\n        height: 1px;
\n    }
\n
\n    .panels-feed\-\-home_content > [class*=\"col-\"]:last-child:after {
\n        content: none;
\n    }
\n
\n    .panels-feed\-\-home .panel,
\n    .panels-feed\-\-home .bar {
\n        max-width: 330px;
\n        margin-left: auto;
\n        margin-right: auto;
\n    }
\n
\n    .panels-feed\-\-home .bar {
\n        margin: 10px auto;
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .panels-feed\-\-home,
\n    .panels-feed\-\-courses {
\n        margin-left: -12px;
\n        margin-right: -12px;
\n    }
\n
\n    .panels-feed\-\-home > [class*=\"col-\"],
\n    .panels-feed\-\-courses > [class*=\"col-\"] {
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        float: left;
\n        padding-left: 12px;
\n        padding-right: 12px;
\n    }
\n
\n    .panels-feed\-\-home .panel,
\n    .panels-feed\-\-courses .panel {
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        -webkit-box-orient: vertical;
\n        -webkit-box-direction: normal;
\n        -ms-flex-direction: column;
\n        flex-direction: column;
\n    }
\n
\n    .panels-feed\-\-home .panel-text,
\n    .panels-feed\-\-courses .panel-image {
\n        margin-top: auto;
\n    }
\n}
\n
\n.bar {
\n    -webkit-box-align: center;
\n    -ms-flex-align: center;
\n    align-items: center;
\n    border-radius: 5px;
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    font-size: 24px;
\n    margin: 15px 0;
\n    width: 100%;
\n    vertical-align: middle;
\n}
\n
\na.bar {
\n    text-decoration: none;
\n}
\n
\n.bar-icon {
\n    align-items: center;
\n    border-radius: 5px 0 0 5px;
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    width: 3.333333em;
\n    height: 3.33333em;
\n}
\n
\n.bar-icon svg,
\n.bar-icon img {
\n    margin: auto;
\n    width: 42px;
\n    height: auto;
\n}
\n
\n.bar-text {
\n    font-weight: 300;
\n    padding-left: 1em;
\n    padding-right: .25em;
\n}
\n
\n\/\* Sidebar panels \*\/
\n.panel-item.has_form {
\n    padding: 1rem;
\n}
\n
\n.panel-item.has_form h4 {
\n    font-weight: bolder;
\n    margin: .5em 0;
\n}
\n
\n.panel-item input:not([type=\"file\"]):not([type=\"submit\"]),
\n.panel-item textarea {
\n    border: 1px solid #e1e1e1;
\n    border-radius: 5px;
\n    margin-bottom: 10px;
\n    padding: .5625em;
\n    width: 100%;
\n    height: 2.375em;
\n}
\n
\n.panel-item textarea {
\n    height: 10em
\n}
\n
\n.panel-item.has_form .button {
\n    font-size: 22px;
\n    font-weight: 500;
\n    padding: .625em;
\n    width: 100%;
\n}
\n
\n.sidebar .panel-item {
\n    margin-bottom: 29px;
\n}
\n
\n.panel-item.has_image {
\n    position: relative;
\n}
\n
\n.panel-item-image img {
\n    display: block;
\n    width: 100%;
\n}
\n
\n.panel-item-image:after {
\n    content: \'\';
\n    background-size: cover;
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n}
\n
\n.panel-item.has_image .panel-item-text {
\n    font-size: 24px;
\n    font-weight: bold;
\n    padding: 0 35px;
\n    position: absolute;
\n    bottom: 0;
\n    left: 0;
\n    right: 0;
\n    height: 90px;
\n}
\n
\n.panel-item-text p {
\n    margin: .75em 0;
\n}
\n
\n\/\* Widgets \*\/
\n.widget {
\n    background-color: #fff;
\n    border: 1px solid #e8e8e8;
\n    border-radius: .3rem;
\n    box-shadow: 0 3px 3px #ccc;
\n    font-size: .9125rem;
\n    margin-bottom: 1rem;
\n    width: 100%;
\n}
\n
\n.widget\-\-organizers,
\n.widget\-\-venue {
\n    text-align: center;
\n}
\n
\n.widget .sectionOverlay {
\n    text-align: left;
\n}
\n
\n.widget-heading {
\n    background-color: #ccc;
\n    color: #fff;
\n    border-bottom: 1px solid #eee;
\n    border-radius: .25rem .25rem 0 0;
\n    padding: .5rem .5rem .25rem;
\n    text-align: center;
\n}
\n
\n.widget-heading .button\-\-plain {
\n    color: inherit;
\n}
\n
\n.widget-title {
\n    color: #fff;
\n    margin: 0;
\n}
\n
\n.widget\-\-checkout .widget-heading {
\n    text-align: left;
\n    padding: .9rem 2rem;
\n}
\n
\n.widget\-\-checkout .widget-title {
\n    color: #fff;
\n    font-size: .9375rem;
\n    margin: 0;
\n}
\n
\n.widget\-\-checkout .widget-body {
\n    padding-left: 2rem;
\n    padding-right: 2rem;
\n}
\n
\n.ticket-widget-heading {
\n    border-radius: 5px 5px 0 0;
\n    font-weight: 500;
\n    padding: 1em 1.5em;
\n}
\n
\n.ticket-widget-heading .row,
\n.ticket-widget-heading .row > div {
\n    align-items: center;
\n    display: flex;
\n}
\n
\n.ticket-widget-heading .row > div > div {
\n    width: 100%;
\n}
\n
\n.ticket-widget-heading strong {
\n    display: inline-block;
\n    font-size: 2em;
\n    margin-left: .5em;
\n}
\n
\n.widget\-\-checkout .widget-heading {
\n    text-align: left;
\n    padding: .9rem 2rem;
\n}
\n
\n.widget\-\-checkout .widget-title {
\n    color: #fff;
\n    font-size: .9375rem;
\n    margin: 0;
\n}
\n
\n.widget\-\-checkout .widget-body {
\n    padding-left: 2rem;
\n    padding-right: 2rem;
\n}
\n
\n.ticket-widget-heading {
\n    border-radius: 5px 5px 0 0;
\n    font-weight: 500;
\n    padding: 1em 1.5em;
\n}
\n
\n.ticket-widget-heading .row,
\n.ticket-widget-heading .row > div {
\n    align-items: center;
\n    display: flex;
\n}
\n
\n.widget-body {
\n    padding: 1rem;
\n}
\n
\n.widget-body + .widget-body {
\n    border-top: 1px solid #eee;
\n}
\n
\n.widget-body .line:after {
\n    content: \', \';
\n}
\n
\n.widget-body .line:last-child:after {
\n    content: none;
\n}
\n
\n.widget-footer {
\n    border-top: 1px solid #eee;
\n    padding: 1rem;
\n}
\n
\n.widget-contact_details {
\n    color: #555;
\n    font-weight: 500;
\n    text-align: center;
\n}
\n
\n.widget-contact_details-item {
\n    padding-left: .5em;
\n    padding-right: .5em;
\n}
\n
\n.course-widget .widget-contact_details-item {
\n    padding-left: .25em;
\n    padding-right: .25em;
\n}
\n
\n.widget-contact_details a,
\n.widget-contact_details button {
\n    color: inherit;
\n    padding: .125em;
\n}
\n
\n.widget-contact_details [class*=\"flaticon\"] {
\n    font-size: 1.25em;
\n    position: relative;
\n    top: .1em;
\n}
\n
\n.widget-contact_details\-\-vertical {
\n    text-align: left;
\n}
\n
\n.widget-contact_details\-\-vertical .widget-contact_details-item {
\n    display: block;
\n    padding-left: 0;
\n    padding-right: 0;
\n    margin: .5em 0
\n}
\n
\n.social_media-list {
\n    clear: both;
\n    margin: 1rem 0 0;
\n    white-space: nowrap;
\n}
\n
\n.widget .social_media-list {
\n    font-size: 1.6875em;
\n}
\n
\n.widget\-\-organizers .social_media-list,
\n.widget\-\-venue .social_media-list {
\n    font-size: 1.1875em;
\n}
\n
\n.social_media-list li {
\n    display: inline-block;
\n    margin: .63em .25em;
\n}
\n
\n.widget .social_media-list li {
\n    margin: 1em .25em 0;
\n}
\n
\n.social_media-list a {
\n    background: #888;
\n    color: #fff;
\n    border-radius: 50%;
\n    display: block;
\n    min-width: 2em;
\n    min-height: 2em;
\n    text-align: center;
\n}
\n
\n.social_media-list a:hover {
\n    background-color: #555;
\n}
\n
\n.social_media-list a:after {
\n    content: \'\';
\n    clear: both;
\n    display: table;
\n}
\n
\n.social_media-list a [class*=\"flaticon-\"] {
\n    position: relative;
\n    top: .5em;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .widget-title {
\n        font-size: 1.25rem;
\n    }
\n
\n    .widget\-\-mobile_menu {
\n        background: #fff;
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        -webkit-box-orient: vertical;
\n        -webkit-box-direction: normal;
\n        -ms-flex-direction: column;
\n        flex-direction: column;
\n        margin: 0;
\n        position: fixed;
\n        top: 0;
\n        right: 0;
\n        bottom: 0;
\n        left: 0;
\n        z-index: 11;
\n    }
\n
\n    .widget\-\-mobile_menu .widget-body {
\n        max-height: 100%;
\n        overflow: auto;
\n    }
\n
\n    .widget\-\-mobile_menu .widget-footer {
\n        margin-top: auto;
\n    }
\n
\n    .ticket-widget-bottom {
\n        background: #f7f7f7;
\n        padding: .25em 1em 1em;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .widget-title {
\n        font-size: 1.5rem;
\n        font-weight: bold;
\n    }
\n
\n    .row.gutters.event-organizer_and_venue {
\n        display: flex;
\n        margin-bottom: 1em;
\n        flex-wrap: wrap;
\n    }
\n
\n    .row.gutters.event-organizer_and_venue  > div > .widget {
\n        height: 100%;
\n        margin-bottom: 0;
\n    }
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Courses carousel
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.carousel-section {
\n    position: relative;
\n}
\n
\n.carousel-section .swiper-wrapper {
\n    display: -ms-flexbox;
\n    display: flex;
\n}
\n
\n.carousel-section .swiper-slide,
\n.carousel-section .panel {
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -webkit-box-orient: vertical;
\n    -webkit-box-direction: normal;
\n    -ms-flex-direction: column;
\n    flex-direction: column;
\n    -webkit-box-flex: 1;
\n    -ms-flex-positive: 1;
\n    flex-grow: 1;
\n    -ms-flex-negative: none;
\n    flex-shrink: none;
\n    height: auto;
\n}
\n
\n.carousel-section .panel {
\n    margin-left: auto;
\n    margin-right: auto;
\n    width: 240px;
\n    min-height: 292px;
\n}
\n
\n@media screen and (max-width: 479px)
\n{
\n    .carousel-section {
\n        padding: 10px 40px;
\n        padding: 10px calc(50% - 140px);
\n    }
\n}
\n
\n@media screen and (min-width: 480px)
\n{
\n    .carousel-section {
\n        max-width: 1280px;
\n        margin-left: auto;
\n        margin-right: auto;
\n        padding-left: 88px;
\n        padding-right: 88px;
\n    }
\n
\n    .carousel-section .swiper-button-prev,
\n    .carousel-section .swiper-button-next {
\n        background-color: #F3F3F3;
\n        border: 1px solid #BFBFBF;
\n        box-shadow: -2px 4px 5px #ccc;
\n        width: 86px;
\n        height: 86px;
\n        position: absolute;
\n    }
\n}
\n
\n.upcoming-courses-embed .swiper-slide {
\n    min-width: 320px;
\n}
\n
\n.course-feed-item {
\n    background-color: var(\-\-primary);
\n    color: #fff;
\n    min-height: 16.875rem;
\n}
\n
\n.course-feed-item-category {
\n    font-size: .75rem;
\n    letter-spacing: -0.01em;
\n    line-height: 1.25;
\n}
\n
\n.course-feed-item .course-feed-item-title {
\n    letter-spacing: -0.01em;
\n    margin-top: .1875rem;
\n}
\n
\n.course-feed-item-date {
\n    display: flex;
\n    align-items: center;
\n}
\n
\n.course-feed-item .course-feed-item-date {
\n    font-size: .875rem;
\n    line-height: 1.25;
\n}
\n
\n.course-feed-item-date::before {
\n    content: url(\'data:image/svg+xml; utf8, <svg width=\"25\" height=\"22\" viewBox=\"0 0 25 22\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><g><path fill-rule=\"evenodd\" clip-rule=\"evenodd\" d=\"M22.9641 19.2829C22.9641 19.9841 22.3506 20.5976 21.6494 20.5976H2.71713C2.01594 20.5976 1.40239 19.9841 1.40239 19.2829V5.25896C1.40239 4.55777 2.01594 3.94422 2.71713 3.94422H4.90837V5.52191C4.90837 5.87251 5.25896 6.22311 5.69721 6.22311C6.04781 6.22311 6.31076 5.87251 6.39841 5.52191V3.94422H11.5697V5.52191C11.5697 5.87251 11.9203 6.22311 12.3586 6.22311C12.7092 6.22311 12.9721 5.87251 13.0598 5.52191V3.94422H18.2311V5.52191C18.2311 5.96016 18.5817 6.22311 18.9323 6.22311C19.3705 6.22311 19.6335 5.87251 19.6335 5.52191V3.94422H21.8247C22.5259 3.94422 23.1394 4.55777 23.1394 5.25896C22.9641 5.25896 22.9641 19.2829 22.9641 19.2829ZM21.7371 2.54183H19.5458V0.701195C19.5458 0.262948 19.1952 0 18.8446 0C18.4064 0 18.1434 0.350598 18.1434 0.701195V2.54183H12.9721V0.701195C12.9721 0.262948 12.6215 0 12.2709 0C11.8327 0 11.5697 0.350598 11.5697 0.701195V2.54183H6.31076V0.701195C6.31076 0.350598 6.04781 0 5.60956 0C5.25896 0 4.90837 0.350598 4.90837 0.701195V2.54183H2.71713C1.22709 2.54183 0 3.76892 0 5.25896V19.2829C0 20.7729 1.22709 22 2.71713 22H21.7371C23.2271 22 24.4542 20.7729 24.4542 19.2829V5.25896C24.4542 3.76892 23.2271 2.54183 21.7371 2.54183Z\" fill=\"white\"/><path fill-rule=\"evenodd\" clip-rule=\"evenodd\" d=\"M6.57379 9.11548H5.69729C5.25904 9.11548 4.90845 9.37843 4.90845 9.72902C4.90845 10.0796 5.25904 10.3426 5.69729 10.3426H6.57379C7.01203 10.3426 7.36263 10.0796 7.36263 9.72902C7.36263 9.46608 7.01203 9.11548 6.57379 9.11548ZM10.781 9.11548H9.90446C9.46622 9.11548 9.11562 9.37843 9.11562 9.72902C9.11562 10.0796 9.46622 10.3426 9.90446 10.3426H10.781C11.2192 10.3426 11.5698 10.0796 11.5698 9.72902C11.5698 9.46608 11.2192 9.11548 10.781 9.11548ZM15.0758 9.11548H14.1993C13.761 9.11548 13.4104 9.37843 13.4104 9.72902C13.4104 10.0796 13.761 10.3426 14.1993 10.3426H15.0758C15.514 10.3426 15.8646 10.0796 15.8646 9.72902C15.8646 9.46608 15.514 9.11548 15.0758 9.11548ZM18.7571 9.11548H17.8806C17.4423 9.11548 17.0917 9.37843 17.0917 9.72902C17.0917 10.0796 17.4423 10.3426 17.8806 10.3426H18.7571C19.1953 10.3426 19.5459 10.0796 19.5459 9.72902C19.5459 9.46608 19.1953 9.11548 18.7571 9.11548ZM6.57379 12.1832H5.69729C5.25904 12.1832 4.90845 12.4462 4.90845 12.7968C4.90845 13.1474 5.25904 13.4103 5.69729 13.4103H6.57379C7.01203 13.4103 7.36263 13.1474 7.36263 12.7968C7.36263 12.4462 7.01203 12.1832 6.57379 12.1832ZM10.781 12.1832H9.90446C9.46622 12.1832 9.11562 12.4462 9.11562 12.7968C9.11562 13.1474 9.46622 13.4103 9.90446 13.4103H10.781C11.2192 13.4103 11.5698 13.1474 11.5698 12.7968C11.5698 12.4462 11.2192 12.1832 10.781 12.1832ZM15.0758 12.1832H14.1993C13.761 12.1832 13.4104 12.4462 13.4104 12.7968C13.4104 13.1474 13.761 13.4103 14.1993 13.4103H15.0758C15.514 13.4103 15.8646 13.1474 15.8646 12.7968C15.8646 12.4462 15.514 12.1832 15.0758 12.1832ZM18.7571 12.1832H17.8806C17.4423 12.1832 17.0917 12.4462 17.0917 12.7968C17.0917 13.1474 17.4423 13.4103 17.8806 13.4103H18.7571C19.1953 13.4103 19.5459 13.1474 19.5459 12.7968C19.5459 12.4462 19.1953 12.1832 18.7571 12.1832ZM6.57379 15.2509H5.69729C5.25904 15.2509 4.90845 15.5139 4.90845 15.8645C4.90845 16.2151 5.25904 16.478 5.69729 16.478H6.57379C7.01203 16.478 7.36263 16.2151 7.36263 15.8645C7.36263 15.5139 7.01203 15.2509 6.57379 15.2509ZM10.781 15.2509H9.90446C9.46622 15.2509 9.11562 15.5139 9.11562 15.8645C9.11562 16.2151 9.46622 16.478 9.90446 16.478H10.781C11.2192 16.478 11.5698 16.2151 11.5698 15.8645C11.5698 15.5139 11.2192 15.2509 10.781 15.2509ZM15.0758 15.2509H14.1993C13.761 15.2509 13.4104 15.5139 13.4104 15.8645C13.4104 16.2151 13.761 16.478 14.1993 16.478H15.0758C15.514 16.478 15.8646 16.2151 15.8646 15.8645C15.8646 15.5139 15.514 15.2509 15.0758 15.2509ZM18.7571 15.2509H17.8806C17.4423 15.2509 17.0917 15.5139 17.0917 15.8645C17.0917 16.2151 17.4423 16.478 17.8806 16.478H18.7571C19.1953 16.478 19.5459 16.2151 19.5459 15.8645C19.5459 15.5139 19.1953 15.2509 18.7571 15.2509Z\" fill=\"white\"/></g><defs><clipPath id=\"clip0\"><rect width=\"24.5418\" height=\"22\" fill=\"white\"/></clipPath></defs></svg>\');
\n    margin-right: .8em;
\n}
\n
\n.course-feed-item-read_more {
\n    display: flex;
\n    align-items: center;
\n}
\n
\n.course-feed-item-read_more::after {
\n    content: url(\'data:image/svg+xml; utf8, <svg width=\"32\" height=\"24\" viewBox=\"0 0 32 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M31.5 11.4L20.7001 0.600049C20.5 0.400049 20.3 0.300049 20 0.300049C19.8 0.300049 19.5 0.400049 19.3 0.600049C19.1 0.800049 19.1 1.00005 19 1.20005V1.30005C19 1.50005 19.1 1.80005 19.3 1.90005L28.5 11.1H1.20005C0.700049 11.1 0.300049 11.5 0.300049 12C0.300049 12.5 0.700049 12.9 1.20005 12.9H28.6L19.5 22.1C19.1 22.5 19.1 23.1 19.5 23.4C19.7001 23.6 19.9001 23.7001 20.2001 23.7001C20.4001 23.7001 20.7 23.6 20.9 23.4L31.6 12.6C31.8001 12.4 31.8 11.8 31.5 11.4Z\" fill=\"white\"/></svg>\');
\n    margin-left: .75em;
\n    position: relative;
\n    top: 2px;
\n}
\n
\n.upcoming-courses-embed .swiper-slide { box-sizing: border-box; }
\n
\n.upcoming-courses-embed-see_more {
\n    color: var(\-\-primary);
\n    font-size: 1.5rem;
\n    font-weight: bold;
\n    line-height: 1.25;
\n    margin: 1.75rem 0;
\n}
\n
\n.upcoming-courses-embed-see_more::after {
\n    position: relative;
\n    top: .2083em;
\n    margin-left: .6666666667em;
\n}
\n
\n.upcoming-courses-carousel-prev::before,
\n.upcoming-courses-carousel-next::before {
\n    width: 50px;
\n    height: 50px;
\n    display: block;
\n    float: left;
\n    margin-top: .1875rem;
\n}
\n
\n.upcoming-courses-carousel-prev:before {
\n    content: url(\'data:image/svg+xml,%3Csvg width=\"50\" height=\"50\" viewBox=\"0 0 50 50\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M25 45.8333C36.4583 45.8333 45.8333 36.4583 45.8333 25C45.8333 13.5417 36.4583 4.16667 25 4.16666C13.5417 4.16666 4.16667 13.5417 4.16667 25C4.34028 36.4583 13.5417 45.8333 25 45.8333ZM25 50C11.2847 50 -1.69989e-06 38.8889 -1.09278e-06 25C-4.85682e-07 11.1111 11.2847 -1.6923e-06 25 -1.09278e-06C38.7153 -4.93271e-07 50 11.1111 50 25C50 38.8889 38.8889 50 25 50Z\" fill=\"%23555555\"/%3E%3Cpath d=\"M34.2015 23.0903C35.4168 23.0903 36.2849 23.9584 36.2849 25.1737C36.2849 26.3889 35.4168 27.257 34.2015 27.257L15.7988 27.257C14.5835 27.257 13.7154 26.3889 13.7154 25.1737C13.7154 23.9584 14.5835 23.0903 15.7988 23.0903L34.2015 23.0903Z\" fill=\"%23555555\"/%3E%3Cpath d=\"M26.5625 32.8126C27.4305 33.6807 27.4305 34.896 26.5625 35.764C25.6944 36.6321 24.4792 36.6321 23.6111 35.764L14.4097 26.5626C13.5417 25.6946 13.5417 24.4793 14.4097 23.6112C15.2778 22.7432 16.493 22.7432 17.3611 23.6112L26.5625 32.8126Z\" fill=\"%23555555\"/%3E%3Cpath d=\"M23.7849 14.2362C24.653 13.3682 25.8682 13.3682 26.7363 14.2362C27.6043 15.1043 27.6043 16.3196 26.7363 17.1876L17.5349 26.389C16.6668 27.2571 15.4516 27.2571 14.5835 26.389C13.7155 25.521 13.7155 24.3057 14.5835 23.4376L23.7849 14.2362Z\" fill=\"%23555555\"/%3E%3C/svg%3E\');
\n}
\n
\n.upcoming-courses-carousel-next:before {
\n    content: url(\'data:image/svg+xml,%3Csvg width=\"50\" height=\"50\" viewBox=\"0 0 50 50\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M25 4.16667C13.5417 4.16667 4.16666 13.5417 4.16666 25C4.16667 36.4583 13.5417 45.8333 25 45.8333C36.4583 45.8333 45.8333 36.4583 45.8333 25C45.6597 13.5417 36.4583 4.16667 25 4.16667ZM25 -1.09278e-06C38.7153 -1.6923e-06 50 11.1111 50 25C50 38.8889 38.7153 50 25 50C11.2847 50 -4.85682e-07 38.8889 -1.09278e-06 25C-1.69989e-06 11.1111 11.1111 -4.85682e-07 25 -1.09278e-06Z\" fill=\"%23555555\"/%3E%3Cpath d=\"M15.7985 26.9097C14.5832 26.9097 13.7151 26.0416 13.7151 24.8263C13.7151 23.6111 14.5832 22.743 15.7985 22.743L34.2012 22.743C35.4165 22.743 36.2846 23.6111 36.2846 24.8263C36.2846 26.0416 35.4165 26.9097 34.2012 26.9097L15.7985 26.9097Z\" fill=\"%23555555\"/%3E%3Cpath d=\"M23.4375 17.1874C22.5695 16.3193 22.5695 15.104 23.4375 14.236C24.3056 13.3679 25.5208 13.3679 26.3889 14.236L35.5903 23.4374C36.4583 24.3054 36.4583 25.5207 35.5903 26.3888C34.7222 27.2568 33.507 27.2568 32.6389 26.3888L23.4375 17.1874Z\" fill=\"%23555555\"/%3E%3Cpath d=\"M26.2151 35.7638C25.347 36.6318 24.1318 36.6318 23.2637 35.7638C22.3957 34.8957 22.3957 33.6804 23.2637 32.8124L32.4651 23.611C33.3332 22.7429 34.5484 22.7429 35.4165 23.611C36.2845 24.479 36.2845 25.6943 35.4165 26.5624L26.2151 35.7638Z\" fill=\"%23555555\"/%3E%3C/svg%3E\');
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .upcoming-courses-embed .row {
\n        padding-right: 0;
\n        padding-left: 0;
\n    }
\n
\n    .course-feed-item {
\n        padding: 1.25rem;
\n    }
\n
\n    .upcoming-courses-carousel-prev::before,
\n    .upcoming-courses-carousel-next::before {
\n        margin: .25rem;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .course-feed-item {
\n        padding: 1.25rem 1.875rem 1.3125rem;
\n    }
\n
\n    .upcoming-courses-carousel-prev::before,
\n    .upcoming-courses-carousel-next::before {
\n        margin-right: .875rem;
\n    }
\n
\n    .upcoming-courses-embed-see_more {
\n        float: right;
\n    }
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Search results
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.course-list-header {
\n    border-bottom: 1px solid;
\n    margin-bottom: 25px;
\n}
\n
\n.course-list-header h1 {
\n    font-size: 30px;
\n    font-weight: bold;
\n    margin: 0 0 .6666666667em;
\n    text-align: center;
\n    text-transform: uppercase;
\n}
\n
\n@media screen and (max-width: 767px)
\n{
\n    .course-list-header h1 {
\n        margin-bottom: 15px;
\n    }
\n}
\n
\n\/\* Result count and display options \*\/
\n.course-list-display_options {
\n    text-align: right;
\n}
\n
\n.course-list-display_options > span {
\n    display: inline-block;
\n}
\n
\n.course-list-display_options ul,
\n.course-list-display_options li {
\n    display: inline;
\n    list-style: none;
\n    margin: 0;
\n    padding: 0;
\n}
\n
\n.course-list-display-option:after {
\n    content: \'\';
\n    display: inline-block;
\n    width: 1px;
\n    height: .75em;
\n}
\n
\n.course-list-display-option:last-child:after {
\n    content: none;
\n}
\n
\n.course-list-display-option label {
\n    color: #818181;
\n    cursor: pointer;
\n    display: inline-block;
\n    text-align: center;
\n    width: 2em;
\n}
\n
\n.course-list-display-option [type=\"radio\"] {
\n    position: absolute;
\n    width: 1px;
\n    height: 1px;
\n    padding: 0;
\n    margin: -1px;
\n    overflow: hidden;
\n    clip: rect(0, 0, 0, 0);
\n    border: 0;
\n}
\n
\n.course-list-display-option [type=\"radio\"]:focus  + label {
\n    outline: 1px dotted skyblue;
\n}
\n
\n.course-list-display-option :checked + label {
\n    color: #198dbe;
\n    color: var(\-\-primary, #198dbe);
\n}
\n
\n@media screen and (max-width: 767px)
\n{
\n    .course-list-result_count {
\n        font-size: 14px;
\n        line-height: 16px;
\n        margin-top: -2.5rem;
\n        padding-bottom: 1.5rem;
\n        padding-right: 3rem;
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .course-list-result_count,
\n    .course-list-display_options {
\n        float: left;
\n        line-height: 2;
\n        padding-bottom: .25em;
\n        width: 50%;
\n    }
\n}
\n
\n\/\* Search results \*\/
\n.course-list\-\-grid .list_only,
\n.course-list\-\-list .grid_only {
\n    display: none ! important;
\n}
\n
\n.course-list:after {
\n    content: \'\';
\n    clear: both;
\n    display: table;
\n}
\n
\n.course-list\-\-grid {
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -ms-flex-wrap: wrap;
\n    flex-wrap: wrap;
\n    margin-left: -12px;
\n    margin-right: -12px;
\n}
\n
\n.course-list\-\-grid .course-list-column {
\n    display: flex;
\n    float: left;
\n    padding: 12px;
\n}
\n
\n.course-list\-\-list .course-list-item {
\n    margin-bottom: 1.5rem;
\n    padding: 17px 22px 24px;
\n}
\n
\n.course-list\-\-list .course-list-grid {
\n    margin-bottom: .5rem;
\n}
\n
\n.course-list.course-list\-\-list .form-select-plain select {
\n    margin-top: -3px;
\n}
\n
\n.course-list.course-list\-\-grid .form-select-plain select {
\n    margin-top: 2px;
\n    margin-bottom: 2px;
\n    padding-left: 14px;
\n    padding-right: 14px;
\n}
\n
\n.course-list.course-list\-\-grid .course-list-item-button {
\n    font-size: 18px;
\n}
\n
\n.course-list-item-read_more {
\n    font-size: 1.125em;
\n    font-weight: normal;
\n    line-height: 1.25;
\n    padding: .225em 0;
\n}
\n
\n.course-list\-\-list .course-list-item-header {
\n    letter-spacing: -0.01em;
\n    margin-top: .25rem;
\n    margin-bottom: 0;
\n}
\n
\n.course-list-item {
\n    \-\-category-color: #000;
\n    line-height: 1.25;
\n}
\n
\n.course-list-item.list_only {
\n    border-left: 10px solid;
\n}
\n
\n.course-list-item.grid_only {
\n    border-top: 10px solid;
\n}
\n
\n.course-list-item p {
\n    margin-bottom: 5px;
\n}
\n
\n.course-list-item-category {
\n    font-size: .75rem;
\n    line-height: 1.25;
\n    letter-spacing: -.01em;
\n}
\n
\n.course-details-intro-data,
\n.course-list-item-data.course-list-item-data {
\n    font-size: .875rem;
\n    line-height: 1.78571429;
\n}
\n
\n.course-details-intro-data,
\n.course-list\-\-list .course-list-item-data {
\n    display: flex;
\n    margin: 1rem 0 .4375rem;
\n}
\n
\n.course-details-intro-data > li {
\n    min-width: 9em;
\n}
\n
\n.course-list\-\-list .course-list-item-data > li {
\n    min-width: 8.65em;
\n}
\n
\n.course-details-intro-data.course-details-intro-data {
\n    margin: 1.6875rem 0 1.25rem;
\n}
\n
\n.course-list\-\-grid .course-list-item-data > li {
\n    margin: .5rem 0;
\n}
\n
\n.course-details-intro-data svg,
\n.course-list-item-data svg {
\n    margin-right: .5625em;
\n}
\n
\n@media screen and (max-width: 1023px) {
\n    .course-list\-\-list .course-list-item-data {
\n        flex-wrap: wrap;
\n    }
\n
\n    .course-list\-\-list .course-list-item-data > li {
\n        margin-bottom: .5em;
\n    }
\n}
\n
\n/* If there is a banner and no content, this should overlap the banner. */
\n.has_banner .course-list-intro:not(.has_content) {
\n    background: none;
\n    margin-top: -7.5rem;
\n    padding-top: 0;
\n    padding-bottom: 1.9rem;
\n}
\n
\n
\n@media screen and (max-width: 767px) {
\n    .course-list-intro,
\n    .news-list-intro {
\n        margin-bottom: 3rem;
\n        padding-top: 3.25rem;
\n        padding-bottom: 1.5rem;
\n    }
\n
\n    .course-list-intro .checkout-progress {
\n       margin-top: .5rem;
\n    }
\n
\n    .layout-checkout .checkout-progress {
\n        margin-bottom: 0;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .course-list-intro,
\n    .news-list-intro {
\n        padding-bottom: 1.1rem;
\n    }
\n
\n    .has_banner .course-list-intro,
\n    .course-list-intro.has_content,
\n    .news-list-intro.has_content {
\n        margin-bottom: 4rem;
\n        padding-top: 3.875rem;
\n    }
\n
\n    .course-list-intro .checkout-progress {
\n       margin-top: 2rem;
\n    }
\n
\n    .course-list-intro.has_content .checkout-progress {
\n       margin-top: 3.875rem;
\n    }
\n}
\n
\n\/\* Target IE10 and 11 only \*\/
\n@media screen and (-ms-high-contrast: active), screen and (-ms-high-contrast: none) {
\n    .course-list\-\-grid .course-list-column {
\n        display: block;
\n    }
\n}
\n
\n.course-widget {
\n    font-size: 1rem;
\n    font-weight: normal;
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    width: 100%;
\n}
\n
\n.course-list\-\-grid .course-widget {
\n    border: 1px solid ;
\n    border-radius: 5px;
\n    box-shadow: 0 2px 5px #ccc;
\n    -webkit-box-orient: vertical;
\n    -webkit-box-direction: normal;
\n    -ms-flex-direction: column;
\n    flex-direction: column;
\n}
\n
\n.course-list\-\-list .course-widget {
\n    margin-bottom: 2.5em;
\n}
\n
\n.course-list\-\-list .course-widget-details {
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -webkit-box-flex: 1;
\n    -ms-flex: 1;
\n    flex: 1;
\n    -webkit-box-orient: vertical;
\n    -webkit-box-direction: normal;
\n    -ms-flex-direction: column;
\n    flex-direction: column;
\n    width: calc(100% - 230px);
\n}
\n
\n.course-list\-\-grid .course-widget-header {
\n    margin-top: auto;
\n    margin-bottom: auto;
\n}
\n
\n
\n.course-list\-\-grid .course-widget-title,
\n.course-list\-\-grid .course-widget-category,
\n.course-list\-\-grid .course-widget-level,
\n.course-list\-\-grid .course-widget-time_and_date-text,
\n.course-list\-\-grid .course-widget-summary {
\n    padding-left: .8rem;
\n    padding-right: .8rem;
\n    min-height: 3.25rem;
\n}
\n
\n.course-widget-title {
\n    font-size: 1.25em;
\n    font-weight: 500;
\n    margin: 0;
\n}
\n
\n.course-list\-\-grid .course-widget-title {
\n    line-height: 1.5;
\n    padding-top: 7px;
\n    padding-bottom: 7px;
\n}
\n
\n.course-list\-\-list .course-widget-title {
\n    line-height: 2.5;
\n    margin-top: -.87em;
\n    width: 100%;
\n}
\n
\n.course-widget-title-id {
\n    font-size: .75em;
\n}
\n
\n.course-list\-\-list .course-widget-title-id {
\n    float: right;
\n    margin-top: .25em;
\n}
\n
\n.course-widget-category {
\n    padding-top: 6px;
\n    padding-bottom: 6px;
\n}
\n
\n.course-widget-image,
\n.course-widget-image img {
\n    display: block;
\n}
\n
\n.course-widget-image {
\n    position: relative;
\n}
\n
\na.course-widget-image {
\n    text-decoration: none;
\n}
\n
\n.course-list\-\-list .course-widget-image {
\n    margin-right: 1em;
\n}
\n
\n.course-list\-\-list .course-widget-image img {
\n    border-radius: 5px;
\n    width: 230px;
\n}
\n
\n.course-list\-\-grid .course-widget-image img {
\n    border-radius: 5px 5px 0 0;
\n    width: 100%;
\n}
\n
\n.course-widget-price {
\n    display: none;
\n    padding: .5em 1em;
\n}
\n
\n.course-widget-price-original,
\n.course-widget-price-current {
\n    font-weight: bold;
\n}
\n
\n.course-list\-\-grid .course-widget-price {
\n    background-color: #00c6ee;
\n    border-top-left-radius: 5px;
\n    color: #FFF;
\n    position: absolute;
\n    bottom: 0;
\n    right: 0;
\n}
\n
\n.course-list\-\-list .course-widget-price {
\n    float: left;
\n    font-size: 18px;
\n    padding-right: 0;
\n    text-align: right;
\n}
\n
\n.course-list\-\-list .course-widget-price-discount_text {
\n    font-size: .77777em
\n}
\n
\n.course-list\-\-list .course-widget-price-original {
\n    font-size: 1.33333em;
\n}
\n
\n.course-list\-\-list .course-widget-header.list_only + div {
\n    border: solid #b7b7b7;
\n    border-width: 1px 0;
\n}
\n
\n.course-widget-location {
\n    background-color: #ececec;
\n    border-top-right-radius: 5px;
\n    box-shadow: 1px 0 5px #ccc;
\n    color: #222;
\n    padding: .375em 1em;
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n}
\n
\n.course-list\-\-list .course-widget-tags {
\n    font-size: .875em;
\n    padding: .5em 0;
\n}
\n
\n.course-list\-\-grid .course-widget-level,
\n.course-list\-\-grid .course-widget-time_and_date {
\n    border-top: 1px solid #bfbfbf;
\n}
\n
\n.course-widget-level {
\n    padding-top: .5em;
\n    padding-bottom: .5em;
\n}
\n
\n.course-list\-\-grid .course-widget-level {
\n    font-size: .875em;
\n    font-weight: 200;
\n    line-height: 1.25;
\n    min-height: 3.125rem;
\n}
\n
\n.course-list\-\-list .course-widget-time_and_date {
\n    align-items: center;
\n    display: flex;
\n}
\n
\n.course-list\-\-list .course-widget-time_and_date\-\-with_options {
\n    min-height: 90px;
\n}
\n
\n.course-widget-time_and_date-text {
\n    -webkit-box-align: center;
\n    -ms-flex-align: center;
\n    align-items: center;
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -webkit-box-flex: 1;
\n    -ms-flex: 1;
\n    flex: 1;
\n    font-size: .875em;
\n    padding-top: .5rem;
\n    padding-bottom: .5rem;
\n}
\n
\n.course-widget-time_and_date select {
\n    background-color: #f2f2f2;
\n    border: none;
\n    height: 2.5em;
\n    padding-top: 0;
\n    padding-bottom: 0;
\n}
\n
\n.course-list\-\-list .course-widget-time_and_date select {
\n    border: 1px solid #b7b7b7;
\n    float: left;
\n    width: 55%;
\n}
\n
\n.course-list\-\-list .course-widget-time_and_date\-\-with_select .course-widget-price {
\n    width: 45%;
\n}
\n
\n.course-widget-time_and_date\-\-with_options select {
\n    background: none;
\n    font-weight: normal;
\n}
\n
\n.course-widget-time_and_date\-\-with_options option {
\n    background: #fff;
\n    color: #000;
\n}
\n
\n.course-widget-links {
\n    clear: both;
\n}
\n
\n.course-list\-\-list .course-widget-links {
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -webkit-box-pack: justify;
\n    -ms-flex-pack: justify;
\n    justify-content: space-between;
\n    margin-top: auto;
\n    -webkit-box-orient: horizontal;
\n    -webkit-box-direction: reverse;
\n    -ms-flex-direction: row-reverse;
\n    flex-direction: row-reverse;
\n}
\n
\n.course-widget-links .button {
\n    font-weight: normal;
\n    padding: .722222em;
\n    text-transform: uppercase;
\n}
\n
\n.course-list\-\-grid .course-widget-links .button {
\n    border-radius: 0;
\n    font-size: 1.125em;
\n    width: 100%;
\n}
\n
\n.course-list\-\-grid .course-widget-links .button:last-child {
\n    border-radius: 0 0 5px 5px;
\n}
\n
\n.course-list\-\-list .course-widget-links .button {
\n    -webkit-box-flex: 1;
\n    -ms-flex: 1;
\n    flex: 1;
\n    max-width: 12.75rem;
\n}
\n
\n.course-list\-\-list .course-widget-links .button:not(:first-child) {
\n    margin-right: 1.7em;
\n}
\n
\n@media screen and (max-width: 419px)
\n{
\n    .course-list-column {
\n        margin-bottom: 20px;
\n    }
\n
\n    .course-list\-\-grid .course-widget-title {
\n        font-weight: 200;
\n        line-height: 1.45;
\n    }
\n
\n    .course-list\-\-grid .course-widget-title-id:before {
\n        content: \' - \';
\n    }
\n
\n    .course-widget-details {
\n        text-align: center;
\n    }
\n
\n    .course-widget-location-name {
\n        padding-right: 2.5em;
\n    }
\n
\n    .course-widget-schedule {
\n        text-align-last: center;
\n    }
\n}
\n
\n@media screen and (min-width: 420px) and (max-width: 599px)
\n{
\n    .course-list\-\-grid .course-list-column {
\n        width: 50%;
\n    }
\n}
\n
\n@media screen and (min-width: 600px) and (max-width: 767px)
\n{
\n    .course-list\-\-grid .course-list-column {
\n        width: 33.33333%;
\n    }
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .course-list\-\-grid .course-list-item {
\n        padding: 15px;
\n    }
\n
\n    .form-select-plain .course-widget-schedule {
\n        text-align-last: left;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .course-list\-\-grid .course-list-item {
\n        padding: 10px;
\n    }
\n
\n    .course-list\-\-grid .course-list-item-header-wrapper {
\n        height: 5.85rem;
\n        border-bottom: 1px solid var(\-\-light_gray);
\n        margin-bottom: .3rem;
\n    }
\n
\n    .course-list\-\-grid .course-list-item-header {
\n        display: -webkit-box;
\n        -webkit-line-clamp: 3;
\n        min-height: 0;
\n        overflow: hidden;
\n        -webkit-box-orient: vertical;
\n        border-bottom: 0;
\n        padding-bottom: 0;
\n    }
\n
\n    .form-select-plain .course-widget-schedule {
\n        text-align-last: center;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) and (max-width: 1023px)
\n{
\n    .course-list\-\-grid .course-list-column {
\n        width: 50%;
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .course-list\-\-grid .course-list-column {
\n        width: 33.33333%;
\n    }
\n
\n    .course-list\-\-grid .course-widget-title-id {
\n        display: block;
\n    }
\n}
\n
\n\/\* Pagination \*\/
\n.pagination-wrapper {
\n    text-align: center;
\n}
\n
\n.pagination {
\n    border-radius: 5px;
\n}
\n
\n.pagination,
\n.pagination > li {
\n    display: inline-block;
\n    list-style: none;
\n    margin: 0;
\n    padding: 0;
\n}
\n
\n.pagination > li {
\n    background: #E7E7E7;
\n    border-right: 1px solid #CDCDCD;
\n    float: left;
\n}
\n
\n.pagination > li:last-child {
\n    border-right: none;
\n}
\n
\n.pagination a {
\n    color: #212121;
\n    display: block;
\n    height: 2.25em;
\n    min-width: 2.25em;
\n    padding: .5em;
\n}
\n
\n.pagination.pagination a {
\n    text-decoration: none;
\n}
\n
\n.pagination a.disabled {
\n    opacity: .5;
\n    pointer-events: none;
\n}
\n
\n.pagination a.current {
\n    box-shadow: inset 0px 0px .5em #bbb;
\n}
\n
\n.pagination-prev,
\n.pagination-prev a {
\n    border-radius: 5px 0 0 5px;
\n}
\n
\n.pagination-next,
\n.pagination-next a {
\n    border-radius: 0 5px 5px 0;
\n}
\n
\n.pagination-prev a:before,
\n.pagination-next a:before {
\n    content: \'\';
\n    border: solid;
\n    display: inline-block;
\n    -webkit-transform: rotate(45deg);
\n    transform: rotate(45deg);
\n    width: .5em;
\n    height: .5em;
\n}
\n
\n.pagination-prev a:before {
\n    border-width: 0 0 2px 2px;
\n    margin-left: .25em;
\n}
\n
\n.pagination-next a:before {
\n    border-width: 2px 2px 0 0;
\n    margin-right: .25em;
\n}
\n
\n@media screen and (max-width: 400px)
\n{
\n    .pagination > li {
\n        font-size: 13px;
\n    }
\n}
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Course details
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.layout-course_detail .banner-section {
\n    min-height: 354px;
\n}
\n
\n\/\* Header \*\/
\n.course-header {
\n    position: relative;
\n}
\n
\n.course-details-header h1 {
\n    line-height: 1.2;
\n    margin: 13px 0;
\n    max-width: 615px;
\n}
\n
\n.course-details-header h6 {
\n    margin: 0;
\n}
\n
\n.course-details-header a {
\n    color: #fff;
\n}
\n
\n.course-details-header-summary {
\n    font-size: 20px;
\n    line-height: 23px;
\n    min-height: 95px;
\n}
\n
\n.course-details-header-summary p {
\n    max-width: 670px;
\n}
\n
\n.course-details-header .checkout-progress ul li a {
\n    color: #fff;
\n}
\n
\n.course-details-intro {
\n    background: #fff;
\n    box-shadow: 0px 4px 35px rgba(0, 0, 0, .18);
\n    margin-top: 30px;
\n    margin-bottom: 38px;
\n    padding: 19px 21px;
\n}
\n
\n.course-details-intro h3 {
\n    margin-bottom: 20px;
\n}
\n
\n.course-details-intro p {
\n    line-height: 1.25;
\n    margin: 14px 0 15px;
\n}
\n
\n.course-details-intro > :first-child {
\n    margin-top: 0;
\n}
\n
\n.course-details-intro > :last-child {
\n    margin-bottom: 0;
\n}
\n
\n.course-details-intro hr {
\n    border: 0;
\n    border-bottom: 1px dashed var(\-\-light_gray);
\n}
\n
\n.course-details-intro-timeslots li {
\n    font-size: 1em;
\n    line-height: 1.25;
\n    margin: 0 0 .3125em;
\n    padding-left: 1.75em;
\n    position: relative;
\n}
\n
\n.course-details-intro-timeslots li:before {
\n    content: \'\';
\n    border-radius: 50%;
\n    display: block;
\n    position: absolute;
\n    left: .5em;
\n    top: 7px;
\n    width: .375em;
\n    height: .375em;
\n}
\n
\n.course-details-menu {
\n    background: #fff;
\n    color: #000;
\n}
\n
\n.course-details-menu-header {
\n    min-height: 52px;
\n}
\n
\n.course-details-menu-header h2 {
\n    margin: 0;
\n    line-height: 1;
\n}
\n
\n.course-details-price-per {
\n    font-size: 10px;
\n}
\n
\n.course-details-menu h6 {
\n    font-size: 14px;
\n    line-height: 1;
\n    margin: .15rem .2rem .5rem;
\n}
\n
\n.course-details-collapse,
\n.course-details-collapsed_title {
\n    display: none;
\n}
\n
\n.course-details-menu .form-select {
\n    margin-bottom: .5rem;
\n}
\n
\n.course-details-menu .form-select select {
\n    font-size: .75rem;
\n    padding-left: .8333em;
\n}
\n
\n.course-details-actions {
\n    padding: 2px;
\n}
\n
\n.course-details-menu .button {
\n    padding: 1em;
\n}
\n
\n.course-details-menu .button + .button {
\n    margin-top: 1rem;
\n}
\n
\n.course-details-menu-header {
\n    border-bottom: 1px solid #bdbdbd;
\n    padding: .7rem 1rem .6rem;
\n}
\n
\n.course-details-menu-body {
\n    padding: .6rem 1.1rem;
\n}
\n
\n.course-details-menu-footer {
\n    border-top: 1px solid #bdbdbd;
\n    padding: .7rem 1rem .9rem;
\n}
\n
\n.course-details-menu-footer button {
\n    color: var(\-\-primary);
\n    display: block;
\n    font-family: 'Noto Serif', serif;
\n    font-size: 1rem;
\n    line-height: 1.375;
\n    font-weight: bold;
\n    text-align: center;
\n    width: 100%;
\n}
\n
\n.course-details-wishlist-checkbox ~ .checkbox-icon-checked {
\n    color: #f2c03a;
\n}
\n
\n.course-details-menu-footer .fa {
\n    font-size: 1.375rem;
\n    bottom: -1px;
\n    left: 2px;
\n    position: relative;
\n}
\n
\n.course-details-brochure-modal .form-row {
\n    margin-bottom: 18px
\n}
\n
\n.course-details-brochure-modal .popup-content {
\n    padding: 20px 42px 40px;
\n}
\n
\n.course-details-brochure-modal .form-input ::-webkit-input-placeholder {
\n    color: #787878;
\n}
\n
\n.course-details-attendees.course-details-attendees {
\n    padding-left: 1em;
\n}
\n
\n.course-details-menu .course-details-price {
\n    font-size: 30px;
\n    flex: 0;
\n    margin-right: .5rem;
\n}
\n
\n.course-details-menu-header.with_discount {
\n    padding: .1875rem 0 .6875rem;
\n}
\n
\n.course-details-price-header.with_discount > span {
\n    flex: 1;
\n    text-align: center;
\n}
\n
\n.course-details-price\-\-member,
\n.course-details-price\-\-nonmember {
\n    font-size: 1.5rem;
\n    line-height: 1;
\n}
\n
\n.course-details-price-header .unavailable {
\n    opacity: .6;
\n}
\n
\n.course-details-price-header .unavailable > .cdh-price {
\n    text-decoration: line-through;
\n}
\n
\n.course-details-price-description {
\n    display: block;
\n    font-size: .75rem;
\n    font-weight: normal;
\n    line-height: 1.25;
\n    text-align: center;
\n}
\n
\n.course-details-discount-description {
\n    background: var(\-\-primary);
\n    color: #fff;
\n    font-size: .75rem;
\n    line-height: 1.25;
\n    margin-top: -1px;
\n    padding: 0.458333333em;
\n    text-align: center;
\n}
\n
\n.course-header .course-results-link {
\n    color: #212121;
\n    display: inline-block;
\n    font-size: 20px;
\n    margin-bottom: 1em;
\n    text-decoration: none;
\n}
\n
\n.course-results-link .link-text {
\n    border-bottom: 1px solid #212121;
\n}
\n
\n.course-results-link:hover .link-text {
\n    border-bottom: none;
\n}
\n
\n@media screen and (min-width: 768px) and (max-width: 1023px) {
\n    .course-details-menu {
\n        right: calc(50vw - 50%);
\n        margin-right: 10px
\n    }
\n}
\n
\n@media screen and (min-width: 1024px) {
\n    .course-details-menu {
\n        right: calc(50vw - 520px);
\n    }
\n}
\n
\n@media screen and (max-width: 767px)
\n{
\n    .course-details-header > .row {
\n        padding-left: 0;
\n        padding-right: 0;
\n    }
\n
\n    .course-details-header-main {
\n        background: var(\-\-category-color);
\n        color: #fff;
\n        padding: 35px 22px 14px;
\n    }
\n
\n    .course-details-menu {
\n        margin: 11px 20px 0;
\n    }
\n
\n    .course-details-header h6 {
\n        line-height: 19px;
\n    }
\n
\n    .course-details-header h6 a {
\n        display: block;
\n    }
\n
\n    .course-details-header-summary {
\n        font-size: 16px;
\n        line-height: 1.1875;
\n    }
\n
\n    .course-details-menu .course-details-price {
\n        color: var(\-\-primary);
\n    }
\n
\n    .course-details-header + .page-content {
\n        margin-top: 2rem;
\n    }
\n}
\n
\n@media screen and (min-width: 768px)
\n{
\n    .course-header h1 {
\n        padding-right: 260px;
\n    }
\n
\n    .course-header .course-results-link {
\n        position: absolute;
\n        top: .2em;
\n        right: 0;
\n    }
\n
\n    .course-details-header {
\n        background: var(\-\-category-color);
\n        color: #fff;
\n        padding-top: 1.75rem;
\n        padding-bottom: .8rem;
\n    }
\n
\n    .course-details-intro {
\n        margin-top: -39px;
\n        padding: 39px 40px 43px;
\n        width: calc(100% - 310px);
\n    }
\n
\n    .course-details-header + .page-content {
\n        margin-top: 3.6875rem;
\n    }
\n
\n    .course-details-header-main {
\n        float: left;
\n        margin-top: 3rem;
\n        width: calc(100% - 280px);
\n    }
\n
\n    .course-details-intro-timeslots ul {
\n        column-count: 2;
\n        margin-top: 1.5625em;
\n    }
\n
\n    .course-details-menu {
\n        float: left;
\n        margin-right: -7px;
\n        width: 280px;
\n        z-index: 4;
\n    }
\n
\n    .course-details-menu.is_fixed {
\n        position: fixed;
\n        top: 0;
\n    }
\n
\n    body.has_sticky_header .course-details-menu.is_fixed {
\n        top: auto;
\n        top: unset;
\n    }
\n
\n    .course-details-menu-header {
\n        background: var(\-\-primary);
\n        color: #fff;
\n    }
\n
\n    .course-details-collapsed_title {
\n        font-size: 28px;
\n        line-height: 30px;
\n        padding-left: 7px;
\n    }
\n
\n    .course-details-collapse {
\n        background: none;
\n        border: none;
\n        font-size: 1rem;
\n        margin-left: auto;
\n        margin-right: 7px;
\n        position: relative;
\n        width: 1.5em;
\n        height: 1.5em;
\n    }
\n
\n    .course-details-collapse:after {
\n        content: \'\';
\n        display: block;
\n        border: solid #fff;
\n        border-width: 2px 0 0 2px;
\n        position: absolute;
\n        top: .5em;
\n        right: .2em;
\n        transform: rotate(45deg);
\n        width: 1em;
\n        height: 1em;
\n    }
\n
\n    .course-details-menu.is_fixed .course-details-collapse {
\n        display: block;
\n    }
\n
\n    .course-details-menu\-\-collapsed .course-details-collapse:after {
\n        transform: rotate(225deg);
\n        top: 0;
\n    }
\n
\n    .course-details-menu\-\-collapsed.is_fixed .course-details-menu-header {
\n        border-bottom: none;
\n    }
\n
\n    .course-details-menu\-\-collapsed.is_fixed .course-details-collapsed_title {
\n        display: block;
\n    }
\n
\n    .course-details-menu\-\-collapsed.is_fixed .course-details-price,
\n    .course-details-menu\-\-collapsed.is_fixed .course-details-price-per,
\n    .course-details-menu\-\-collapsed.is_fixed .course-details-menu-body,
\n    .course-details-menu\-\-collapsed.is_fixed .course-details-menu-footer {
\n        display: none;
\n    }
\n
\n    .course-details-brochure-modal .form-input {
\n        font-size: 14px;
\n    }
\n
\n    .course-details-brochure-modal .form-input input {
\n        padding: 20px 12px;
\n    }
\n
\n    .course-details-brochure-modal .form-input\-\-active .form-input\-\-pseudo-label {
\n        left: 12px;
\n    }
\n}
\n
\n\/\* Banner and actions \*\/
\n.course-banner {
\n    border-radius: 8px;
\n    overflow: hidden;
\n    position: relative;
\n}
\n
\n.course-banner-image {
\n    border-radius: 5px;
\n    display: block;
\n    max-width: 1040px;
\n}
\n
\n.price_wrapper {
\n    font-size: 1.5em;
\n    font-weight: bold;
\n    visibility: hidden;
\n    text-align: right;
\n}
\n
\n.course-details-summary {
\n    overflow: auto;
\n}
\n
\n.fixed_sidebar {
\n    background-color: #fff;
\n    border-radius: 5px;
\n    margin-bottom: 1em;
\n}
\n
\n.fixed_sidebar-header {
\n    border-radius: 5px 5px 0 0;
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -webkit-box-align: center;
\n    -ms-flex-align: center;
\n    align-items: center;
\n    height: 58px;
\n    padding: 10px 18px;
\n}
\n
\n.fixed_sidebar-content {
\n    border: solid #bfbfbf;
\n    border-width: 0 1px;
\n    padding: 20px 18px 25px;
\n}
\n
\n.fixed_sidebar-content .select {
\n    background-color: #f2f2f2;
\n    margin-top: .5em;
\n}
\n
\n.fixed_sidebar-footer {
\n    border: solid #bfbfbf;
\n    border-width: 0 1px 1px;
\n    border-radius: 0 0 5px 5px;
\n}
\n
\n.fixed_sidebar .button {
\n    font-size: 18px;
\n    font-weight: 400;
\n    padding: .639em;
\n    width: 100%;
\n}
\n
\n.fixed_sidebar-footer .button:not(:first-child) {
\n    border-top-right-radius: 0;
\n    border-top-left-radius: 0;
\n}
\n
\n.fixed_sidebar-footer .button:not(:last-child) {
\n    border-bottom-right-radius: 0;
\n    border-bottom-left-radius: 0;
\n}
\n
\n.right-section .button-action button {
\n    text-transform: uppercase;
\n}
\n
\n.course-details-price {
\n    -webkit-box-flex: 1;
\n    -ms-flex: 1;
\n    flex: 1;
\n    font-size: 14px;
\n    text-align: center;
\n}
\n
\n.course-details-price\-\-normal {
\n    opacity: .6;
\n}
\n
\n.course-details-price strong {
\n    font-size: 1.7143em;
\n}
\n
\n@media screen and (min-width: 768px)
\n{
\n    .course-details-summary-wrapper {
\n        position: relative;
\n    }
\n
\n    .course-details-summary-wrapper:after {
\n        content: \'\';
\n        clear: both;
\n        display: table;
\n    }
\n
\n    .course-details-summary {
\n        float: left;
\n        padding-right: 20px;
\n        width: calc(100% - 300px);
\n    }
\n
\n    .fixed_sidebar-wrapper {
\n        float: right;
\n        width: 300px;
\n    }
\n
\n    .fixed_sidebar-wrapper.fixed-top,
\n    .fixed_sidebar-wrapper.fixed-bottom {
\n        max-width: 300px;
\n        width: 100%;
\n    }
\n    #fixed_sidebar-wrapper {
\n      position: -webkit-sticky;
\n      position: sticky;
\n    }
\n
\n    .fixed_sidebar {
\n        float: right;
\n        top: 0;
\n        right: 0;
\n        width: 300px;
\n    }
\n
\n    .course-details-summary-wrapper .fixed_top > .fixed_sidebar,
\n    .course-details-summary-wrapper .fixed_bottom > .fixed_sidebar {
\n        position: absolute;
\n    }
\n
\n    \/\* overlap banner, if present \*\/
\n    .course-banner + .course-details-summary-wrapper .fixed_sidebar {
\n        top: -58px;
\n    }
\n
\n    .course-details-summary-wrapper .fixed-top > .fixed_sidebar {
\n        top: 0;
\n    }
\n}
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Event details
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.event-details-back.event-details-back p {
\n    font-size: 1.125em;
\n    text-align: right;
\n}
\n
\n.ticket-container {
\n    align-items: center;
\n    border-bottom: 1px solid #eee;
\n    color: #555;
\n    display: flex;
\n    font-size: 1rem;
\n    margin-bottom: 1em;
\n    padding-bottom: 1em;
\n}
\n
\n.ticket-container > div:last-child {
\n    text-align: right;
\n}
\n
\n.ticket-date,
\n.ticket-title,
\n.ticket-description {
\n    margin: .5rem 0;
\n}
\n
\n.ticket-date {
\n    font-weight: 500;
\n    font-style: italic;
\n    font-size: 1.1em
\n}
\n
\n.ticket-description,
\n.ticket-sales_ended {
\n    font-size: .75em;
\n}
\n
\n.ticket-sales_ended {
\n    font-size: .9em;
\n    margin: 0;
\n}
\n
\n.ticket-val {
\n    margin-top: .25em;
\n}
\n
\n.ticket-val select {
\n    padding: 0;
\n    width: 3em;
\n}
\n
\n.event-related {
\n    margin-bottom: 1rem;
\n    padding: .5rem 30px;
\n}
\n
\n.event-related-title {
\n    font-size: 1.875em;
\n    font-weight: bold;
\n    margin: .5em 0 .4em;
\n}
\n
\n.event-related .row.gutters {
\n    margin-left: -10px;
\n    margin-right: -10px;
\n}
\n
\n.event-related [class*=\"col-\"] {
\n    padding: 0 10px;
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Booking\/enquiry form
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.booking-table {
\n    font-weight: 200;
\n    text-align: center;
\n}
\n
\n.booking-table p {
\n    margin-top: calc(1em + 1px);
\n}
\n
\n.booking-table a:link,
\n.booking-table a:visited {
\n    color: #12387f;
\n    font-weight: normal;
\n}
\n
\n.booking-table thead th {
\n    border-bottom: none;
\n    font-weight: normal;
\n}
\n
\n.booking-table tbody tr:first-child td,
\n.booking-table tbody tr:first-child th {
\n    border-top: none;
\n}
\n
\n.booking-table tr > :first-child {
\n    text-align: left;
\n}
\n
\n.booking-form h2 {
\n    border-bottom: 1px solid;
\n    font-size: 24px;
\n    font-weight: normal;
\n    margin-bottom: 1em;
\n}
\n
\n.booking-row {
\n    clear: both;
\n    margin-left: -13px;
\n    margin-right: -13px;
\n}
\n
\n.booking-row:after {
\n    content: \'\';
\n    clear: both;
\n    display: table;
\n}
\n
\n.booking-column {
\n    float: left;
\n    padding-left: 13px;
\n    padding-right: 13px;
\n    width: 100%;
\n}
\n
\n.booking-section\-\-actions {
\n    margin-left: -11px;
\n    margin-right: -11px;
\n    text-align: center;
\n}
\n
\n.booking-section\-\-actions .button {
\n    font-weight: normal;
\n    margin: 11px;
\n    text-transform: uppercase;
\n    min-width: 210px;
\n}
\n
\n.booking-form .form-input {
\n    box-shadow: 0 1px #ddd;
\n}
\n
\n.booking-form textarea[name$=\"_address\"] {
\n    height: 10.25em;
\n}
\n
\n.booking-required_field-note {
\n    font-weight: 200;
\n}
\n
\n.booking-preferred {
\n    padding-left: 1.3125em;
\n    padding-right: 1.3125em;
\n}
\n
\n.booking-preferred label {
\n    display: inline-block;
\n    margin-right: 1.625em;
\n}
\n
\n.booking-use_guardian {
\n    display: block;
\n    text-align: right;
\n}
\n
\n@media screen and (max-width: 767px)
\n{
\n    .booking-section\-\-table {
\n        margin-left: -19px;
\n        margin-right: -19px;
\n        padding-left: 1px;
\n        padding-right: 1px;
\n    }
\n
\n    .booking-table th {
\n        padding: 10px 21px;
\n    }
\n    .booking-table td {
\n        padding: 5px 21px;
\n    }
\n}
\n
\n@media screen and (min-width: 480px)
\n{
\n    .booking-column\-\-half {
\n        width: 50%;
\n    }
\n}
\n
\n@media screen and (min-width: 768px)
\n{
\n    .booking-table thead {
\n        font-size: 24px;
\n    }
\n
\n    .booking-table tbody th,
\n    .booking-table tbody td {
\n        padding: 5px 33px 7px;
\n    }
\n
\n    .booking-table thead th {
\n        padding: 8px 24px;
\n    }
\n
\n    .booking-form h2 {
\n        margin-bottom: 2em;
\n    }
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Contact page
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.layout-contact .banner-section {
\n    min-height: 300px;
\n}
\n
\n.contact-map-iframe {
\n    border: none;
\n    width: 100%;
\n}
\n
\n.contact-map-overlay-content {
\n    color: #fff;
\n    display: flex;
\n    font-size: 17px;
\n}
\n
\n.contact-map-overlay-content > div {
\n    margin-top: auto;
\n    margin-bottom: auto;
\n}
\n
\n.contact-map-overlay-content h2 {
\n    font-weight: bold;
\n    font-size: 1.70588235294em;
\n}
\n
\n.contact-column h2 {
\n    font-weight: bold;
\n}
\n
\n.contact-column li {
\n    line-height: 2;
\n}
\n
\n.contact-column dt {
\n    text-decoration: underline;
\n}
\n
\n.contact-column dd {
\n    margin: .75em 0 .75em 0;
\n    padding-left: 1.375em;
\n}
\n
\n.contact-column dt:after {
\n    content: \':\';
\n}
\n
\n.contact-form {
\n    color: #555;
\n}
\n
\n.contact-form-bottom {
\n    width: 170px;
\n    margin: 2em auto;
\n}
\n
\n.contact-form-bottom .button {
\n    text-transform: uppercase;
\n}
\n
\n.contact-form-required_note {
\n    margin-top: .5em;
\n}
\n
\n.contact-form-required_note > span {
\n    color: #00c6ee;
\n}
\n
\n@media screen and (min-width: 640px)
\n{
\n    .contact-columns:after,
\n    .contact-form-row:after{
\n        content: \'\';
\n        clear: both;
\n        display: table;
\n    }
\n
\n    .contact-column {
\n        float: left;
\n        padding-left: 2em;
\n        padding-right: 2em;
\n        width: 50%;
\n    }
\n
\n    .contact-form-row {
\n        margin-left: -13px;
\n        margin-right: -13px;
\n    }
\n
\n    .contact-form-column {
\n        float: left;
\n        margin-bottom: .75rem;
\n        padding-left: 13px;
\n        padding-right: 13px;
\n    }
\n
\n    .contact-form-column\-\-half {
\n        width: 50%;
\n    }
\n
\n    .contact-form-column\-\-middle {
\n        float: none;
\n        margin: auto;
\n        max-width: 510px;
\n    }
\n}
\n
\n@media screen and (max-width: 767px)
\n{
\n    .contact-map-overlay {
\n        padding-top: 10px;
\n        padding-bottom: 23px;
\n    }
\n
\n    .contact-map-overlay-content h2 {
\n        line-height: 1.1;
\n        text-align: center;
\n    }
\n
\n    .contact-map-overlay-content p {
\n        font-size: 18px;
\n        line-height: 1.4;
\n    }
\n
\n    .contact-map-map {
\n        margin-bottom: 8px;
\n        padding: 19px 19px 12px;
\n    }
\n
\n    .contact-map-iframe {
\n        border-radius: 5px;
\n        height: 332px;
\n    }
\n
\n    .contact-form {
\n        margin-top: 1em;
\n    }
\n
\n    .contact-form .form-group {
\n        margin-bottom: 1em;
\n    }
\n
\n    .contact-form .form-input {
\n        box-shadow: 1px 1px 1px #CCC;
\n    }
\n}
\n
\n@media screen and (min-width: 768px)
\n{
\n    .layout-contact .banner-search {
\n        bottom: -64px;
\n    }
\n
\n    .contact-map-overlay {
\n        position:absolute;
\n        top: 0;
\n        left: 0;
\n        width: 100%;
\n        height: 1px;
\n    }
\n
\n    .contact-map-overlay > .row {
\n        height: 1px;
\n    }
\n
\n    .contact-map-overlay-content {
\n        background: #00c6ee;
\n        background: rgba(68,197,236,.85);
\n        margin-top: 1px;
\n        width: 450px;
\n        height: 455px;
\n        padding-left: 2em;
\n        padding-right: 2em;
\n    }
\n
\n
\n    .contact-map {
\n        height: 455px;
\n        position: relative;
\n    }
\n
\n    .contact-map-iframe {
\n        margin-top: 1px;
\n        height: 455px;
\n    }
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Checkout
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.checkout-countdown {
\n    cursor: auto;
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    font-weight: 300;
\n    -webkit-box-pack: center;
\n    -ms-flex-pack: center;
\n    justify-content: center;
\n    padding: .3em;
\n}
\n
\n.checkout-countdown > div {
\n    -webkit-box-flex: 1;
\n    -ms-flex: 1;
\n    flex: 1;
\n    max-width: 3.5em;
\n    position: relative;
\n}
\n
\n.checkout-countdown > div:after {
\n    content: \':\';
\n    position: absolute;
\n    top: 50%;
\n    right: -.15em;
\n}
\n
\n.checkout-countdown > div:last-child:after {
\n    display: none;
\n}
\n
\n.checkout-countdown-label {
\n    display: block;
\n    font-size: .75em;
\n    text-transform: uppercase;
\n}
\n
\n.checkout-countdown-figure {
\n    display: block;
\n    font-size: 2.3em;
\n}
\n
\n.checkout-prices {
\n    margin: 1em 0 1.5em;
\n    font-size: 1.375em;
\n}
\n
\n.checkout-cvv-icon {
\n    position: relative;
\n    bottom: -.5rem;
\n}
\n
\n
\n
\n
\n\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Accessibility and visibility
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.collapse {
\n    display: none;
\n}
\n
\n.collapse.in {
\n    display: block;
\n}
\n
\n.show-for-sr {
\n    clip: rect(1px, 1px, 1px, 1px);
\n    height: 1px;
\n    opacity: 0;
\n    overflow: hidden;
\n    position: absolute !important;
\n    width: 1px;
\n    z-index: -1;
\n}
\n
\n.fullwidth {
\n    width: 100vw !important;
\n    margin-left: calc(50% - 50vw) !important;
\n}
\n
\n@media screen and (max-width: 767px)
\n{
\n    .hidden\-\-mobile {
\n        display: none !important;
\n    }
\n
\n    .fullwidth\-\-mobile {
\n        width: 100vw !important;
\n        margin-left: calc(50% - 50vw) !important;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) and (max-width: 1024px)
\n{
\n    .hidden\-\-tablet {
\n        display: none !important;
\n    }
\n}
\n
\n@media screen and (min-width: 1025px)
\n{
\n    .hidden\-\-desktop {
\n        display: none !important;
\n    }
\n}
\n
\n.visible {
\n    visibility: visible !important;
\n}
\n
\n.hidden {
\n    display: none !important;
\n}
\n
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #MISC
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.col-xs-1,.col-sm-1,.col-md-1,.col-lg-1,.col-xs-2,.col-sm-2,.col-md-2,.col-lg-2,.col-xs-3,.col-sm-3,.col-md-3,.col-lg-3,.col-xs-4,.col-sm-4,.col-md-4,.col-lg-4,.col-xs-5,.col-sm-5,.col-md-5,.col-lg-5,.col-xs-6,.col-sm-6,.col-md-6,.col-lg-6,.col-xs-7,.col-sm-7,.col-md-7,.col-lg-7,.col-xs-8,.col-sm-8,.col-md-8,.col-lg-8,.col-xs-9,.col-sm-9,.col-md-9,.col-lg-9,.col-xs-10,.col-sm-10,.col-md-10,.col-lg-10,.col-xs-11,.col-sm-11,.col-md-11,.col-lg-11,.col-xs-12,.col-sm-12,.col-md-12,.col-lg-12
\n{
\n  position:relative;
\n  min-height:1px;
\n  padding-left:15px;
\n  padding-right:15px
\n}
\n
\n.col-xs-1,.col-xs-2,.col-xs-3,.col-xs-4,.col-xs-5,.col-xs-6,.col-xs-7,.col-xs-8,.col-xs-9,.col-xs-10,.col-xs-11,.col-xs-12
\n{
\n  float:left
\n}
\n
\n.col-xs-12
\n{
\n  width:100%
\n}
\n
\n.col-xs-11
\n{
\n  width:91.666666666667%
\n}
\n
\n.col-xs-10
\n{
\n  width:83.333333333333%
\n}
\n
\n.col-xs-9
\n{
\n  width:75%
\n}
\n
\n.col-xs-8
\n{
\n  width:66.666666666667%
\n}
\n
\n.col-xs-7
\n{
\n  width:58.333333333333%
\n}
\n
\n.col-xs-6
\n{
\n  width:50%
\n}
\n
\n.col-xs-5
\n{
\n  width:41.666666666667%
\n}
\n
\n.col-xs-4
\n{
\n  width:33.333333333333%
\n}
\n
\n.col-xs-3
\n{
\n  width:25%
\n}
\n
\n.col-xs-2
\n{
\n  width:16.666666666667%
\n}
\n
\n.col-xs-1
\n{
\n  width:8.3333333333333%
\n}
\n
\n.col-xs-pull-12
\n{
\n  right:100%
\n}
\n
\n.col-xs-pull-11
\n{
\n  right:91.666666666667%
\n}
\n
\n.col-xs-pull-10
\n{
\n  right:83.333333333333%
\n}
\n
\n.col-xs-pull-9
\n{
\n  right:75%
\n}
\n
\n.col-xs-pull-8
\n{
\n  right:66.666666666667%
\n}
\n
\n.col-xs-pull-7
\n{
\n  right:58.333333333333%
\n}
\n
\n.col-xs-pull-6
\n{
\n  right:50%
\n}
\n
\n.col-xs-pull-5
\n{
\n  right:41.666666666667%
\n}
\n
\n.col-xs-pull-4
\n{
\n  right:33.333333333333%
\n}
\n
\n.col-xs-pull-3
\n{
\n  right:25%
\n}
\n
\n.col-xs-pull-2
\n{
\n  right:16.666666666667%
\n}
\n
\n.col-xs-pull-1
\n{
\n  right:8.3333333333333%
\n}
\n
\n.col-xs-pull-0
\n{
\n  right:auto
\n}
\n
\n.col-xs-push-12
\n{
\n  left:100%
\n}
\n
\n.col-xs-push-11
\n{
\n  left:91.666666666667%
\n}
\n
\n.col-xs-push-10
\n{
\n  left:83.333333333333%
\n}
\n
\n.col-xs-push-9
\n{
\n  left:75%
\n}
\n
\n.col-xs-push-8
\n{
\n  left:66.666666666667%
\n}
\n
\n.col-xs-push-7
\n{
\n  left:58.333333333333%
\n}
\n
\n.col-xs-push-6
\n{
\n  left:50%
\n}
\n
\n.col-xs-push-5
\n{
\n  left:41.666666666667%
\n}
\n
\n.col-xs-push-4
\n{
\n  left:33.333333333333%
\n}
\n
\n.col-xs-push-3
\n{
\n  left:25%
\n}
\n
\n.col-xs-push-2
\n{
\n  left:16.666666666667%
\n}
\n
\n.col-xs-push-1
\n{
\n  left:8.3333333333333%
\n}
\n
\n.col-xs-push-0
\n{
\n  left:auto
\n}
\n
\n.col-xs-offset-12
\n{
\n  margin-left:100%
\n}
\n
\n.col-xs-offset-11
\n{
\n  margin-left:91.666666666667%
\n}
\n
\n.col-xs-offset-10
\n{
\n  margin-left:83.333333333333%
\n}
\n
\n.col-xs-offset-9
\n{
\n  margin-left:75%
\n}
\n
\n.col-xs-offset-8
\n{
\n  margin-left:66.666666666667%
\n}
\n
\n.col-xs-offset-7
\n{
\n  margin-left:58.333333333333%
\n}
\n
\n.col-xs-offset-6
\n{
\n  margin-left:50%
\n}
\n
\n.col-xs-offset-5
\n{
\n  margin-left:41.666666666667%
\n}
\n
\n.col-xs-offset-4
\n{
\n  margin-left:33.333333333333%
\n}
\n
\n.col-xs-offset-3
\n{
\n  margin-left:25%
\n}
\n
\n.col-xs-offset-2
\n{
\n  margin-left:16.666666666667%
\n}
\n
\n.col-xs-offset-1
\n{
\n  margin-left:8.3333333333333%
\n}
\n
\n.col-xs-offset-0
\n{
\n  margin-left:0
\n}
\n
\n@media(min-width:768px) {
\n  .col-sm-1,.col-sm-2,.col-sm-3,.col-sm-4,.col-sm-5,.col-sm-6,.col-sm-7,.col-sm-8,.col-sm-9,.col-sm-10,.col-sm-11,.col-sm-12
\n  {
\n    float:left
\n  }
\n
\n  .col-sm-12
\n  {
\n    width:100%
\n  }
\n
\n  .col-sm-11
\n  {
\n    width:91.666666666667%
\n  }
\n
\n  .col-sm-10
\n  {
\n    width:83.333333333333%
\n  }
\n
\n  .col-sm-9
\n  {
\n    width:75%
\n  }
\n
\n  .col-sm-8
\n  {
\n    width:66.666666666667%
\n  }
\n
\n  .col-sm-7
\n  {
\n    width:58.333333333333%
\n  }
\n
\n  .col-sm-6
\n  {
\n    width:50%
\n  }
\n
\n  .col-sm-5
\n  {
\n    width:41.666666666667%
\n  }
\n
\n  .col-sm-4
\n  {
\n    width:33.333333333333%
\n  }
\n
\n  .col-sm-3
\n  {
\n    width:25%
\n  }
\n
\n  .col-sm-2
\n  {
\n    width:16.666666666667%
\n  }
\n
\n  .col-sm-1
\n  {
\n    width:8.3333333333333%
\n  }
\n  .col-sm-offset-12
\n  {
\n    margin-left:100%
\n  }
\n
\n  .col-sm-offset-11
\n  {
\n    margin-left:91.666666666667%
\n  }
\n
\n  .col-sm-offset-10
\n  {
\n    margin-left:83.333333333333%
\n  }
\n
\n  .col-sm-offset-9
\n  {
\n    margin-left:75%
\n  }
\n
\n  .col-sm-offset-8
\n  {
\n    margin-left:66.666666666667%
\n  }
\n
\n  .col-sm-offset-7
\n  {
\n    margin-left:58.333333333333%
\n  }
\n
\n  .col-sm-offset-6
\n  {
\n    margin-left:50%
\n  }
\n
\n  .col-sm-offset-5
\n  {
\n    margin-left:41.666666666667%
\n  }
\n
\n  .col-sm-offset-4
\n  {
\n    margin-left:33.333333333333%
\n  }
\n
\n  .col-sm-offset-3
\n  {
\n    margin-left:25%
\n  }
\n
\n  .col-sm-offset-2
\n  {
\n    margin-left:16.666666666667%
\n  }
\n
\n  .col-sm-offset-1
\n  {
\n    margin-left:8.3333333333333%
\n  }
\n
\n  .col-sm-offset-0
\n  {
\n    margin-left:0
\n  }
\n}
\n
\n@media(min-width:992px) {
\n  .col-md-1,.col-md-2,.col-md-3,.col-md-4,.col-md-5,.col-md-6,.col-md-7,.col-md-8,.col-md-9,.col-md-10,.col-md-11,.col-md-12
\n  {
\n    float:left
\n  }
\n
\n  .col-md-12
\n  {
\n    width:100%
\n  }
\n
\n  .col-md-11
\n  {
\n    width:91.666666666667%
\n  }
\n
\n  .col-md-10
\n  {
\n    width:83.333333333333%
\n  }
\n
\n  .col-md-9
\n  {
\n    width:75%
\n  }
\n
\n  .col-md-8
\n  {
\n    width:66.666666666667%
\n  }
\n
\n  .col-md-7
\n  {
\n    width:58.333333333333%
\n  }
\n
\n  .col-md-6
\n  {
\n    width:50%
\n  }
\n
\n  .col-md-5
\n  {
\n    width:41.666666666667%
\n  }
\n
\n  .col-md-4
\n  {
\n    width:33.333333333333%
\n  }
\n
\n  .col-md-3
\n  {
\n    width:25%
\n  }
\n
\n  .col-md-2
\n  {
\n    width:16.666666666667%
\n  }
\n
\n  .col-md-1
\n  {
\n    width:8.3333333333333%
\n  }
\n  .col-md-offset-12
\n  {
\n    margin-left:100%
\n  }
\n
\n  .col-md-offset-11
\n  {
\n    margin-left:91.666666666667%
\n  }
\n
\n  .col-md-offset-10
\n  {
\n    margin-left:83.333333333333%
\n  }
\n
\n  .col-md-offset-9
\n  {
\n    margin-left:75%
\n  }
\n
\n  .col-md-offset-8
\n  {
\n    margin-left:66.666666666667%
\n  }
\n
\n  .col-md-offset-7
\n  {
\n    margin-left:58.333333333333%
\n  }
\n
\n  .col-md-offset-6
\n  {
\n    margin-left:50%
\n  }
\n
\n  .col-md-offset-5
\n  {
\n    margin-left:41.666666666667%
\n  }
\n
\n  .col-md-offset-4
\n  {
\n    margin-left:33.333333333333%
\n  }
\n
\n  .col-md-offset-3
\n  {
\n    margin-left:25%
\n  }
\n
\n  .col-md-offset-2
\n  {
\n    margin-left:16.666666666667%
\n  }
\n
\n  .col-md-offset-1
\n  {
\n    margin-left:8.3333333333333%
\n  }
\n
\n  .col-md-offset-0
\n  {
\n    margin-left:0
\n  }
\n}
\n
\n@media(min-width:1200px) {
\n  .col-lg-1,.col-lg-2,.col-lg-3,.col-lg-4,.col-lg-5,.col-lg-6,.col-lg-7,.col-lg-8,.col-lg-9,.col-lg-10,.col-lg-11,.col-lg-12
\n  {
\n    float:left
\n  }
\n
\n  .col-lg-12
\n  {
\n    width:100%
\n  }
\n
\n  .col-lg-11
\n  {
\n    width:91.666666666667%
\n  }
\n
\n  .col-lg-10
\n  {
\n    width:83.333333333333%
\n  }
\n
\n  .col-lg-9
\n  {
\n    width:75%
\n  }
\n
\n  .col-lg-8
\n  {
\n    width:66.666666666667%
\n  }
\n
\n  .col-lg-7
\n  {
\n    width:58.333333333333%
\n  }
\n
\n  .col-lg-6
\n  {
\n    width:50%
\n  }
\n
\n  .col-lg-5
\n  {
\n    width:41.666666666667%
\n  }
\n
\n  .col-lg-4
\n  {
\n    width:33.333333333333%
\n  }
\n
\n  .col-lg-3
\n  {
\n    width:25%
\n  }
\n
\n  .col-lg-2
\n  {
\n    width:16.666666666667%
\n  }
\n
\n  .col-lg-1
\n  {
\n    width:8.3333333333333%
\n  }
\n  .col-lg-offset-12
\n  {
\n    margin-left:100%
\n  }
\n
\n  .col-lg-offset-11
\n  {
\n    margin-left:91.666666666667%
\n  }
\n
\n  .col-lg-offset-10
\n  {
\n    margin-left:83.333333333333%
\n  }
\n
\n  .col-lg-offset-9
\n  {
\n    margin-left:75%
\n  }
\n
\n  .col-lg-offset-8
\n  {
\n    margin-left:66.666666666667%
\n  }
\n
\n  .col-lg-offset-7
\n  {
\n    margin-left:58.333333333333%
\n  }
\n
\n  .col-lg-offset-6
\n  {
\n    margin-left:50%
\n  }
\n
\n  .col-lg-offset-5
\n  {
\n    margin-left:41.666666666667%
\n  }
\n
\n  .col-lg-offset-4
\n  {
\n    margin-left:33.333333333333%
\n  }
\n
\n  .col-lg-offset-3
\n  {
\n    margin-left:25%
\n  }
\n
\n  .col-lg-offset-2
\n  {
\n    margin-left:16.666666666667%
\n  }
\n
\n  .col-lg-offset-1
\n  {
\n    margin-left:8.3333333333333%
\n  }
\n
\n  .col-lg-offset-0
\n  {
\n    margin-left:0
\n  }
\n}
\n
\n.contact\-\-left .ui-tabs-nav li{
\n  display: inline-block;
\n  font-size: 16px;
\n  font-weight: 300;
\n  text-align: center;
\n}
\n
\n.contact\-\-left .ui-tabs-nav a {
\n  background: #f8fbff;
\n  border: 1px solid #e4e4e4;
\n  display: block;
\n  padding: 10px 5px;
\n  color: #222;
\n}
\n
\n@media (min-width: 768px) {
\n    .contact\-\-left .ui-tabs-nav li a {
\n        width: 172px;
\n    }
\n}
\n
\n@media (max-width: 767px){
\n    .contact\-\-left .ui-tabs-nav li {
\n        float: left;
\n        margin-bottom: 20px;
\n        width: 50%;
\n    }
\n
\n    .contact\-\-left .ui-tabs-nav li a {
\n        padding: 10px 15px;
\n        width: 100%;
\n    }
\n}
\n
\n.contact\-\-left .ui-tabs-nav .ui-tabs-active a {
\n    font-weight: bolder;
\n}
\n
\n.term-privacy > h1:first-child,
\n.term-privacy > h2:first-child {
\n    display: none;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .contact\-\-left .ui-tabs-panel {
\n        padding-top: 25px;
\n    }
\n}
\n
\n@media screen and (min-width: 768px){
\n    .contact\-\-left .ui-tabs-panel {
\n        border: 1px solid #e4e4e4;
\n        margin-bottom: 20px;
\n        padding: 25px;
\n    }
\n}
\n
\n.contact\-\-left .ui-widget-content p{
\n  margin: 0px;
\n  padding: 0px 0 25px 0;
\n  font-weight: 300;
\n}
\n
\n.contact\-\-left .form-group{
\n  margin-left:-15px;
\n  margin-right:-15px;
\n}
\n
\n.contact\-\-left .form-group:after,
\n.contact\-\-left .form-group:before{
\n  content:\" \";
\n  display:table;
\n  clear: both;
\n}
\n
\n#displayWrapperAndIframe{
\n    position: fixed;
\n    top: 0;
\n    right: 0;
\n    left: 0;
\n    z-index: 11;
\n    bottom: 0;
\n    text-align: center;
\n    background: rgba(255,255,255, 0.8);
\n}
\n
\n@media (min-width:768px){
\n  .contact\-\-left  .button-action{
\n    text-align: center;
\n    padding: 50px 0;
\n  }
\n}
\n@media (max-width:767px){
\n  .contact\-\-left  .button-action{
\n    text-align: center;
\n    padding: 20px 0;
\n  }
\n}
\n
\n.fixed-top {
\n    position: fixed;
\n    top: 0;
\n    z-index: 1;
\n    float: right;
\n}
\n
\n.fixed-bottom {
\n    position: fixed;
\n    bottom: 0;
\n    z-index: 1;
\n    float: right;
\n}
\n
\n.checkout-right-sect .item-summary-head {
\n  display: inline-block;
\n  vertical-align: top;
\n  width: 100%;
\n  padding: 9px 15px;
\n  border-bottom: 1px solid #d8d8d8;
\n}
\n
\n.checkout-right-sect .item-summary-head button {
\n    color: inherit;
\n}
\n
\n.checkout-right-sect .btn-close {
\n    background: none;
\n    border: none;
\n    color: #222;
\n    display: inline-block;
\n    font-size: 12px;
\n    line-height: 1.25em;
\n    margin: auto;
\n    padding: 0;
\n    position: absolute;
\n    top:0;
\n    bottom: 0;
\n    left: 0;
\n    text-align: center;
\n    width: 1.5em;
\n    height: 1.5em;
\n}
\n
\n.discounts_container .right,
\n.checkout-right-sect  .right{
\n  margin-left: auto;
\n  float: right;
\n}
\n
\n.checkout-right-sect .button,
\n.checkout-complete_booking {
\n    text-transform: uppercase;
\n    width: 100%;
\n}
\n
\n.checkout-right-sect .total-pay {
\n    border-top: 1px solid #cacddc;
\n    padding: 0 15px;
\n}
\n
\n.checkout-right-sect .total-pay li {
\n    font-weight: 400;
\n    padding: 5px 0;
\n}
\n
\n.checkout-right-sect .total-pay li.sub-total, .checkout-right-sect .total-pay li.group-total {
\n    font-weight: 500;
\n    margin: 10px 0;
\n    text-transform: uppercase;
\n}
\n
\n.checkout-right-sect .panel {
\n    border-radius: 0;
\n    border-width: 0 0 1px;
\n}
\n
\n.checkout-right-sect .panel-body {
\n    font-size: .875em;
\n}
\n
\n.checkout-group_booking-pay_more .form-group {
\n    font-size: 10px;
\n}
\n
\n.checkout-group_booking-pay_more .gutters {
\n    margin-left: -.5em;
\n    margin-right: -.5em;
\n}
\n
\n.checkout-group_booking-pay_more [class*=\"col-\"] {
\n    padding-left: .5em;
\n    padding-right: .5em;
\n}
\n
\n.checkout-coupon-wrapper {
\n    align-items: center;
\n    display: flex;
\n    margin: -5px;
\n}
\n
\n.checkout-coupon-wrapper > div {
\n    padding: 0 5px;
\n}
\n
\n.checkout-coupon-wrapper [type=\"text\"] {
\n    height: 40px;
\n    padding-left: 13px;
\n    padding-right: 13px;
\n}
\n
\n.checkout-coupon-wrapper .form-input\-\-active .form-input\-\-pseudo-label {
\n    left: 13px;
\n}
\n
\n.checkout-coupon-wrapper .button {
\n    font-size: 15px;
\n    height: auto;
\n    line-height: 1.375;
\n    min-width: 80px;
\n    padding: 7px 5px 11px;
\n}
\n
\n.terms-txt {
\n    font-size: 12px;
\n    font-weight: 300;
\n    line-height: 1.5;
\n    padding: 0 15px;
\n}
\n
\n.terms-txt.terms-txt p {
\n    font-size: inherit;
\n    margin: 0;
\n}
\n
\n.checkout-processed_by {
\n    align-items: center;
\n    display: flex;
\n    font-size: 15px;
\n    padding: 5px 15px;
\n}
\n
\n.checkout-processed_by > :first-child {
\n    padding-right: .5em;
\n}
\n
\n.checkout-processed_by img {
\n    float: left;
\n}
\n
\n.layout-checkout_with_overlay .header{
\n  position: relative;
\n  z-index: 55;
\n}
\n
\n.layout-checkout_with_overlay .wrapper {
\n  position: relative;
\n}
\n
\n.guest-user-bg {
\n    background: no-repeat center bottom #fff;
\n    padding: 90px 0 45px 0;
\n    text-align: center;
\n}
\n.guest-user-wrapper h3 {
\n  font-size: 30px;
\n  margin: 0;
\n  padding:10px 0;
\n}
\n.guest-user-wrapper .button {
\n  text-transform: uppercase;
\n  font-size: 18px;
\n  font-weight: normal;
\n  border-radius: 5px;
\n  padding:10px 20px;
\n  min-width: 218px;
\n  margin:3px;
\n}
\n
\n
\n.fade{
\n  opacity:0;
\n  -webkit-transition:opacity .15s linear;
\n  -o-transition:opacity .15s linear;
\n  transition:opacity .15s linear;
\n  }
\n.fade.in{
\n  opacity:1;
\n}
\n.tab-content > .active {
\n    display: block;
\n}
\n
\n.toggleable_height {
\n    overflow-y: auto; /* So we have consistent margin collapsing between \"show more\" and \"show less\" states. */
\n}
\n
\n.toggleable_height > :first-child {
\n    margin-top: 0; /* Since there is no margin collapse, remove the extra margin. */
\n}
\n
\n.toggleable_height:not(.show_less):not(.show_more) + .toggleable_height-toggles {
\n    visibility: hidden;
\n}
\n
\n.toggleable_height.show_more + .toggleable_height-toggles .toggleable_height-show_more,
\n.toggleable_height.show_less + .toggleable_height-toggles .toggleable_height-show_less {
\n    display: none;
\n}
\n
\n.toggleable_height.show_less {
\n    max-height: 200px;
\n    overflow-y: hidden;
\n    position: relative;
\n}
\n
\n.toggleable_height.show_less:after {
\n    content: \'\';
\n    background: linear-gradient(transparent 60%, #fff);
\n    position: absolute;
\n    left: 0;
\n    top: 0;
\n    width: 100%;
\n    height: 100%;
\n}
\n
\n.event-description:not(.show_less):not(.show_more) + .event-description-toggles {
\n    visibility: hidden;
\n}
\n
\n.event-description.show_more + .event-description-toggles .event-description-show_more,
\n.event-description.show_less + .event-description-toggles .event-description-show_less {
\n    display: none;
\n}
\n
\n
\n
\n
\n\/\*==============================
\n  Packages available
\n================================\*\/
\n.checkout-item {
\n    padding: .5em 0 1em;
\n    position: relative;
\n}
\n
\n.checkout-item + .checkout-item {
\n    border-top: 1px solid #ddd;
\n}
\n
\n.checkout-item .row {
\n    -webkit-box-align: center;
\n    -ms-flex-align: center;
\n    align-items: center;
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    padding-left: 0;
\n    padding-right: 0;
\n}
\n
\n.checkout-item .col-xs-1 {
\n    padding: 0;
\n}
\n
\n.checkout-item .row > :last-child {
\n    padding-left: 5px;
\n    padding-right: 15px;
\n    text-align: right;
\n}
\n
\n.checkout-item-title {
\n    font-size: 1.25em;
\n}
\n
\n.checkout-item-date,
\n.checkout-item-count {
\n    display: block;
\n    font-size: .8em;
\n    font-weight: 300;
\n}
\n
\n.checkout-item-timeslots {
\n    font-size: .8em;
\n    font-weight: 300;
\n}
\n
\n.checkout-item-date {
\n    font-weight: 500;
\n    white-space: nowrap;
\n    overflow-wrap: break-word;
\n}
\n
\n.checkout-item-fee-wrapper {
\n    display: block;
\n    font-weight: 500;
\n    text-align: right;
\n
\n}
\n
\n.checkout-item-fee-wrapper small {
\n    display: block;
\n    font-size: 12px;
\n    font-weight: 300;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .right-section {
\n        margin-left: -19px;
\n        margin-right: -19px;
\n        width: calc(100% + 38px);
\n    }
\n
\n    .checkout-countdown {
\n        margin: 1em auto 0;
\n        width: calc(100% - 2em);
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .checkout-right-sect {
\n        border-left:1px solid #cacddc;
\n        border-right:1px solid #cacddc;
\n        border-bottom:1px solid #cacddc;
\n    }
\n
\n    .right-section .gray-box {
\n        background: #f8f8f8;
\n        border: 1px solid #d8d8d8;
\n        border-radius: 5px;
\n    }
\n
\n    .checkout-heading {
\n        border-top-left-radius: 7px;
\n        border-top-right-radius: 7px;
\n        color: #fff;
\n        font-size: 24px;
\n        margin: 0;
\n        padding: .5em 20px;
\n    }
\n
\n    .contact\-\-left .theme-form-inner-content,
\n    .billing-inner-content,
\n    .privacy-inner-content {
\n        margin: 20px;
\n    }
\n
\n    .contact\-\-left .theme-form-content,
\n    .billing-content,
\n    .privacy-content {
\n        border: 1px solid #cdcdcd;
\n        margin-bottom: 1.25em;
\n    }
\n}
\n
\n@media(min-width:1200px){
\n  .left-section{
\n    float: left;
\n    width: 800px;
\n  }
\n}
\n@media(min-width: 992px) and (max-width: 1199px){
\n  .left-section{
\n    float: left;
\n    width: 75%;
\n  }
\n}
\n@media(min-width:992px){
\n  .right-section,
\n  .right-section .gray-box {
\n    float: right;
\n    width: 225px;
\n  }
\n
\n}
\n@media (min-width: 768px) and (max-width:991px){
\n  .left-section{
\n    width: 100%;
\n  }
\n  .right-section{
\n    width: 100%;
\n    margin: 5px 0 15px;
\n  }
\n}
\n
\n@media screen and (max-width: 1024px) {
\n    .event-details .page-content,
\n    .event-details .event-organizer_and_venue {
\n        padding-left: 1rem;
\n        padding-right: 1rem;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .event-left {
\n        float: left;
\n        padding-right: 1.125rem;
\n        width: calc(100% - 300px);
\n    }
\n
\n    .event-right {
\n        float: left;
\n        width: 300px;
\n    }
\n}
\n
\n.last-search {
\n    min-height: 30px;
\n    display: block;
\n    width: auto;
\n}
\n
\n.search-filters {
\n    font-weight: 300;
\n    width: 100%;
\n}
\n
\n.search-filter-dropdown {
\n    font-size: 14px;
\n    position: relative;
\n}
\n
\n.search-filter-dropdown > button,
\n.search-filters-clear {
\n    border: none;
\n    background: none;
\n    color: #222;
\n    font-size: 14px;
\n    font-weight: 400;
\n    padding: 0;
\n    white-space: nowrap;
\n}
\n
\n.search-filters-clear {
\n    visibility: hidden;
\n}
\n
\n.search-filter-dropdown.open .search-filter-dropdown-icon {
\n    transform: rotate(180deg);
\n}
\n
\n.search-filter-dropdown-icon {
\n    margin-right: .5em;
\n    position: relative;
\n    top: -.15em;
\n}
\n
\n.search-filter-total:not(:empty):before,
\n.filter-active .search-filter-amount:before {
\n    content: \'(\';
\n}
\n
\n.search-filter-total:not(:empty):after,
\n.filter-active .search-filter-amount:after {
\n    content: \')\';
\n}
\n
\n.search-filter-dropdown .dropdown-menu {
\n    background: #fff;
\n    border: 1px solid rgba(0, 0, 0, .15);
\n    display: none;
\n    max-height: 300px;
\n    overflow: auto;
\n    margin-top: .7em;
\n    padding: .5em;
\n    z-index: 3;
\n}
\n
\n.search-filter-dropdown.open .dropdown-menu {
\n    display: block;
\n}
\n
\n.search-filter-dropdown .dropdown-menu > li {
\n    line-height: 1.5;
\n    padding: .35em 0;
\n    font-size: 14px;
\n}
\n
\n.search-filters .form-checkbox-helper {
\n    border-radius: 2px;
\n    border-color: #eee;
\n}
\n
\n.search-filters :checked + .form-checkbox-helper:after {
\n    color: inherit;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .availability-result_counters.availability-result_counters {
\n        background: #f5f5f5;
\n        border: none;
\n        margin: 0;
\n        padding: .75rem 1.25rem;
\n    }
\n
\n    .available_results-filters-wrapper {
\n        position: relative;
\n        z-index: 2;
\n    }
\n
\n    .search-filters {
\n        background: #fff;
\n        border: 1px solid #f2f2f2;
\n        max-width: 260px;
\n        position: absolute;
\n        top: 1rem;
\n        right: 0;
\n        z-index: 1;
\n    }
\n
\n    .search-filters-blackout {
\n        background: rgba(0,0,0,.1);
\n        position: fixed;
\n        top: 0;
\n        right: 0;
\n        bottom: 0;
\n        left: 0;
\n    }
\n
\n    .search-filters-heading,
\n    .search-filter-dropdown > button {
\n        border-bottom: 1px solid #eee;
\n        font-size: 13px;
\n        padding: 1em;
\n    }
\n
\n    .search-filter-dropdown {
\n        font-size: 13px;
\n    }
\n
\n    .search-filters-heading {
\n        background: #f5f5f5;
\n        font-weight: bold;
\n    }
\n
\n    .search-filters-heading:before {
\n        content: \'\';
\n        display: block;
\n        width: 2em;
\n        height: 2em;
\n        background: #f5f5f5;
\n        transform: rotate(45deg);
\n        position: absolute;
\n        top: -.8em;
\n        right: 2em;
\n        z-index: -1;
\n    }
\n
\n    .search-filters-heading .row {
\n        padding: 0;
\n    }
\n
\n    .search-filter-dropdown > button {
\n        text-align: left;
\n        width: 100%;
\n    }
\n
\n    .search-filter-dropdown .dropdown-menu {
\n        border: none;
\n        margin: 0;
\n        padding: 0;
\n        position: static;
\n    }
\n
\n    .search-filter-dropdown .dropdown-menu > li {
\n        padding: 1em 1em 1em 2.7em;
\n        border-bottom: 1px solid #eee;
\n    }
\n
\n    .search-filter-item\-\-divider {
\n        border-bottom: .5em solid #eee;
\n    }
\n
\n    .search-filter-label {
\n        font-weight: bold;
\n    }
\n
\n    .filter-active .search-filter-label {
\n        position: relative;
\n        top: -.5em
\n    }
\n
\n    .search-filter-selected_items {
\n        display: block;
\n        font-size: .75em;
\n        overflow-x: hidden;
\n        position: absolute;
\n        top: 2.5em;
\n        text-overflow: ellipsis;
\n        width: 80%;
\n        width: calc(100% - 3rem);
\n    }
\n
\n    .search-filter-selected_items > span:after {
\n        content: \', \';
\n    }
\n
\n    .search-filter-selected_items > span:last-child:after {
\n        content: none;
\n    }
\n
\n    .search-filter-dropdown-icon {
\n        float: right;
\n    }
\n
\n    .search-filter-dropdown-item .form-checkbox-helper,
\n    .search-filter-dropdown-item .form-radio-helper {
\n        float: right;
\n        margin: 0;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-filters {
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        -webkit-box-pack: justify;
\n        -ms-flex-pack: justify;
\n        justify-content: space-between;
\n    }
\n
\n    .search-filter-dropdown .dropdown-menu {
\n        position: absolute;
\n        top: 100%;
\n        right: unset;
\n        bottom: unset;
\n        width: 200%;
\n    }
\n
\n    .search-filter-item {
\n        -webkit-box-flex: 1;
\n        -ms-flex: 1;
\n        flex: 1;
\n        float: left;
\n    }
\n}
\n
\n
\n@media screen and (max-width: 767px) {
\n    .availability-course {
\n        margin-bottom: 20px;
\n    }
\n
\n    .availability-course-toggle.expanded {
\n        background: #fff;
\n        border: 1px solid #ebebeb;
\n        color: #000;
\n    }
\n
\n    .availability-course-toggle.expanded .availability-course-toggle-icon {
\n        display: block;
\n        transform: rotate(180deg);
\n    }
\n
\n    .availability-course-details {
\n        font-weight: 300;
\n    }
\n
\n    .availability-course-summary {
\n        color: #333;
\n        line-height: 1.5;
\n    }
\n
\n    .availability-course-summary a,
\n    .availability-date-read_more {
\n        text-decoration: underline;
\n    }
\n
\n    .availability-date-price_range {
\n        font-weight: 300;
\n    }
\n
\n    .availability-timeslot {
\n        background: #f4f4f4;
\n        border: solid #d7d7d7;
\n        border-width: 1px 1px 0 0;
\n        font-size: .8em;
\n        margin: 1em 0;
\n        padding: 1em;
\n        overflow: auto;
\n    }
\n
\n    .availability-timeslot.booked {
\n        background-color: #fff;
\n    }
\n
\n    .availability-timeslot.booked .booked-hide {
\n        display: none !important;
\n    }
\n
\n    .availability-timeslot:not(.booked) .unbooked-hide {
\n        display: none !important;
\n    }
\n
\n    .availability-timeslot-date {
\n        margin: 0;
\n    }
\n
\n    .availability-timeslot-details {
\n        margin: 2em 0
\n    }
\n
\n    .availability-timeslot-details > div,
\n    .availability-timeslot-payment_type {
\n        margin: .25em 0;
\n    }
\n
\n    .availability-timeslot .highlight {
\n        font-size: 1rem;
\n        font-weight: bolder;
\n    }
\n
\n    .availability-timeslot-per_timeslot {
\n        padding: 0;
\n    }
\n
\n    .availability-per_timeslot_price {
\n        font-size: 2em;
\n    }
\n
\n    .availability-book,
\n    .availability-unbook {
\n        height: 36px;
\n        font-size: 12px;
\n        font-weight: 500;
\n        padding-top: 0px;
\n        padding-bottom: 0px;
\n    }
\n
\n    .availability-unbook {
\n        text-decoration: underline;
\n    }
\n}
\n
\n
\n
\n@media(min-width:768px){
\n.border-top-bottom .last-search{
\n    float: left;
\n  }
\n  .border-top-bottom .pagination-new{
\n    float: right;
\n  }
\n}
\n@media(max-width:767px){
\n.border-top-bottom .last-search{
\n    width:100%;
\n    float: left;
\n  }
\n  .left-section .border-top-bottom .pagination-new{
\n    width: 100%;
\n    margin-top: 10px;
\n  }
\n  .pagination-new ul{
\n    float: left;
\n  }
\n  .contact\-\-left{
\n    margin-top: 15px;
\n  }
\n}
\n
\n.border-top-bottom{
\n  border-top: 1px solid #c5cecd;
\n  border-bottom: 1px solid #c5cecd;
\n  margin-bottom: -1px;
\n  padding: 8px 0;
\n  display: inline-block;
\n  vertical-align: top;
\n  width: 100%;
\n}
\n
\n.pagination-new ul{
\n  float: right;
\n  background: #f8f8f8;
\n  border: 1px solid #d7d7d7;
\n   border-radius: 3px;
\n   margin: 3px 0;
\n}
\n
\n#number_of_courses select{
\n    float: right;
\n    margin: 3px 0px 0px 10px;
\n    border: 1px solid #d7d7d7;
\n    background: #f8f8f8;
\n    height: 30px;
\n    border-radius: 3px;
\n    font-size: 14px;
\n    color: #222222;
\n}
\n
\n#number_of_courses ul li
\n{
\n    width: 100%;
\n    padding: 0 10px;
\n    float: right;
\n    background: #f8f8f8;
\n    border-radius: 3px;
\n}
\n
\n#number_of_courses{
\n    float: left;
\n}
\n.pagination-new ul li{
\n  float: left;
\n  width: 28px;
\n  text-align: center;
\n  border-right:1px solid #d7d7d7;
\n  height: 28px;
\n  line-height: 28px;
\n  font-size: 14px;
\n}
\n.pagination-new ul li:last-child{
\n  border: none;
\n}
\n.pagination-new ul li a{
\n  color:#000000;
\n  display: block;
\n}
\n
\nli.selected-active-page, .pagination-new ul li a:hover{
\n  background: #c8c8c8;
\n  color: #fff;
\n}
\n.pagination-new ul li a.active{
\n  color: #000;
\n}
\n
\n.checkout-progress {
\n    position: relative;
\n    margin-bottom: 1rem;
\n}
\n
\n.checkout-progress ul {
\n    display: table;
\n    margin: auto;
\n    table-layout: fixed;
\n    text-align: center;
\n    width: 100%;
\n    max-width: 914px;
\n}
\n
\n.checkout-progress ul li {
\n    display: table-cell;
\n    text-align: center;
\n    font-weight: 300;
\n    color: #222;
\n    position: relative;
\n}
\n
\n.checkout-progress li a:after {
\n    content: \'\';
\n    background: #C8C8C8;
\n    border: 1px solid #C8C8C8;
\n    border-radius: 50%;
\n    box-shadow: 0 0 3px #ccc;
\n    display: inline-block;
\n    position: relative;
\n    vertical-align: top;
\n    width: 1.375rem;
\n    height: 1.37rem;
\n    z-index: 1;
\n}
\n
\n.checkout-progress li + li:before {
\n    content: \'\';
\n    display: block;
\n    border-top: 1px solid #00c6ee;
\n    position: absolute;
\n    left: calc(-50% + .625rem);
\n    bottom: 10px;
\n    width: calc(100% - 1.25rem);
\n}
\n
\n.checkout-progress .curr ~ li:before {
\n    border-color: #c8c8c8;
\n}
\n
\n@media(min-width:768px){
\n    .checkout-progress ul li{
\n        font-size: 16px;
\n    }
\n
\n    .checkout-progress ul li p {
\n        margin: 0 0 7px;
\n    }
\n}
\n@media(max-width:767px){
\n    .checkout-progress {
\n        display: none;
\n    }
\n
\n/* For sites that make this bar visible */
\n/* Bar is divided into five parts. Half of each part is cut off either side. (100% / 5 / 2 = 10%) */
\n   .checkout-progress ul {
\n      margin-left: -10%;
\n      margin-left: -10%;
\n      width: calc(100% + 20%);
\n}
\n
\n    .checkout-progress {
\n        padding-left: 10px;
\n        padding-right: 10px;
\n    }
\n
\n    .checkout-progress ul li,
\n    .checkout-progress ul li a {
\n        font-size: 10px;
\n        line-height: 14px;
\n    }
\n
\n    .checkout-progress ul li p {
\n        margin: 0 0 5px;
\n    }
\n}
\n
\n.checkout-progress ul li a {
\n    color: #222;
\n    cursor: pointer;
\n    display: block;
\n}
\n
\n.checkout-progress .curr {
\n    font-weight: 400;
\n}
\n
\n.checkout-progress .curr ~ li {
\n    cursor: not-allowed;
\n}
\n
\n.checkout-progress .curr ~ li a {
\n    cursor: default;
\n    pointer-events: none;
\n}
\n
\n.checkout-progress .curr ~ li > a:after {
\n    background-color: #f2f2f2;
\n    border-color: #c8c8c8;
\n}
\n
\n@media (min-width: 768px){
\n  .search-package-available h2 {
\n      font-size: 17px;
\n      text-transform: uppercase;
\n      font-weight: 600;
\n
\n  }
\n}
\n@media (max-width: 767px){
\n  .search-package-available h2 {
\n    font-size:20px;
\n  }
\n}
\n
\n.search-package-available {
\n    display: inline-block;
\n    margin-bottom: 30px;
\n    position: relative;
\n    vertical-align: top;
\n    width: 100%;
\n}
\n.search-package-available .table-box{
\n  display: table;
\n  width: 100%;
\n}
\n@media (min-width:478px){
\n  .search-package-available .imgbox {
\n      height: 172px;
\n      position: relative;
\n      width: 172px;
\n      display: table-cell;
\n      vertical-align: top;
\n  }
\n  .search-package-available .available-text {
\n      padding: 0 0 40px 15px;
\n      display: table-cell;
\n      position: relative;
\n  }
\n}
\n@media (max-width:479px){
\n  .search-package-available .imgbox {
\n      display:block;
\n      vertical-align: top;
\n      text-align: center;
\n  }
\n  .search-package-available .available-text {
\n      padding: 10px 0 40px 10px;
\n      position: relative;
\n  }
\n}
\n.search-package-available .available-text  h4 {
\n    border-bottom: 1px solid;
\n    margin: 0 0 10px;
\n    padding-bottom: 5px;
\n}
\n.search-package-available .available-text p {
\n    font-size: 16px;
\n    font-weight: 300;
\n    margin: 0 0 10px;
\n    padding-right: 0;
\n    text-align: justify;
\n    line-height: 30px;
\n}
\n.search-package-available .show-more {
\n    border-radius: 5px;
\n    bottom: 10px;
\n    font-size: 16px;
\n    padding: 6px 15px 6px 15px;
\n    position: absolute;
\n    right: 0;
\n}
\n.search-package-available .show-more .hide-txt{
\n  display: none;
\n
\n}
\n.search-package-available .show-more.active .show-txt{
\n   display: none;
\n
\n}
\n.search-package-available .show-more.active .hide-txt{
\n  display:block;
\n}
\n.search-package-available .show-more:hover{
\n  cursor: pointer;
\n}
\n
\n.select-package{
\n  display: inline-block;
\n  width: 100%;
\n  vertical-align: top;
\n}
\n
\n.right-section .gray-box h4 {
\n    font-weight: 500;
\n    margin: 0;
\n    padding: 7px 0;
\n}
\n
\n.right-section .gray-box > h4 {
\n    padding-left: 15px;
\n    padding-right: 15px;
\n}
\n
\n.right-section .purchase-packages{
\n    border-bottom: 1px solid #d8d8d8;
\n    border-top: 1px solid #d8d8d8;
\n}
\n#booking-cart-notices {
\n  box-sizing: border-box;
\n  padding: 10px;
\n}
\n.booking-cart-empty {
\n  text-align: center;
\n  font-size: 14px;
\n  color: #787878;
\n  font-weight: 300;
\n  padding: 15px;
\n}
\n
\n.booking-cart-icon {
\n    font-size: 40px;
\n    margin-bottom: 10px;
\n    color: #acacac;
\n    text-align: center;
\n    margin-top: 15px;
\n}
\n
\n.right-section .purchase-packages p{
\n  margin: 0;
\n}
\n
\n.prepay-box {
\n    font-size: .9em;
\n    font-weight: 200;
\n    max-height: 400px;
\n    overflow-y: auto;
\n    padding: 1em;
\n}
\n
\n.prepay-box h5{
\n  margin: 0;
\n  padding: 7px 0;
\n}
\n
\n.prepay-box h6{
\n  margin: 0;
\n  font-size: 16px;
\n}
\n
\n.prepay-box p {
\n    margin: 0 0 1em;
\n}
\n
\n.prepay-box li .left{
\n  float: left;
\n  width: 70%;
\n}
\n.prepay-box li .right{
\n  float: right;
\n}
\n.prepay-box li {
\n  padding: 0 1px 10px 0;
\n}
\n.prepay-box li p {
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    font-size: 14px;
\n    font-weight: 300;
\n    line-height: 21px;
\n    margin: 0;
\n    width: 100%;
\n}
\n.checkout-right-sect .prepay-box li {
\n  position: relative;
\n}
\n
\n.prepay-box li .left.discount{
\n  background: url(\"..\/img\/dis-img.png\")  no-repeat left center;
\n  padding-left: 50px;
\n  min-height: 40px;
\n}
\n
\n.checkout-right-sect .discountItemPlaceholder{
\n    padding-left: 10px;
\n    padding-top: 10px;
\n    border-top: 1px solid #d8d8d8;
\n}
\n
\n.prepay-box li.discountItemPlaceholder .left{
\n    width: 100%;
\n    margin: auto;
\n    line-height: 20px;
\n    padding-right: 15px;
\n}
\n
\n.prepay-box li.discountItemPlaceholder .right{
\n    margin: auto;
\n}
\n
\n.discountItemPlaceholder img {
\n    margin: auto;
\n    max-height: 36px;
\n    padding-right: 10px;
\n}
\n
\n
\n.prepay-box li.total{
\n  padding: 12px 15px;
\n  color: #112866;
\n  text-transform:uppercase;
\n}
\n.prepay-box li.total p{
\n   font-size: 20px;
\n    font-weight: 700;
\n}
\n.prepay-box li.total .left{
\n  width: auto;
\n}
\n
\n.right-section .continue .button{
\n  width: 100%;
\n  border-radius: 0;
\n  text-transform: uppercase;
\n  font-weight: normal;
\n}
\n.right-section .search-wrap{
\n  display: block;
\n  margin: 15px 15px;
\n  position: relative;
\n}
\n
\n.sidebar-search {
\n    overflow: hidden;
\n}
\n
\n[data-target=\"#sidebar-search\"] .expand-icon {
\n    float: right;
\n    padding: .25em 0;
\n}
\n
\n[data-target=\"#sidebar-search\"][aria-expanded=\"false\"] .expand-icon {
\n    transform: rotate(180deg);
\n}
\n
\n.package-offers-wrap{
\n    display: none;
\n}
\n.right-section .search-wrap input[type=\"text\"],
\n.right-section .search-wrap input[type=\"search\"]{
\n  background: #fff;
\n  border: 1px solid #d7d7d7;
\n  border-radius: 3px;
\n  height: 38px;
\n  padding: 3px 40px 3px 5px;
\n  color: #787878;
\n  font-size: 14px;
\n  font-weight: 300;
\n  width: 100%;
\n}
\n.right-section .search-wrap .fa{
\n  position: absolute;
\n  color: #222222;
\n  font-size: 20px;
\n  top:9px;
\n  right: 10px;
\n}
\n
\n.course-txt{
\n  padding: 1em;
\n}
\n.course-txt p, .course-txt{
\n  font-size: 14px;
\n  color: #4f4e4f;
\n  line-height: 24px;
\n  font-weight: 300;
\n}
\n.select-package{
\n  position: relative;
\n  height: 1px;
\n  overflow: hidden;
\n  -webkit-transition: all 300ms;
\n  -o-transition: all 300ms;
\n  transition: all 300ms;
\n}
\n
\n.search-package-available .package-wrap.open .select-package{
\n -webkit-transform: scale(1);
\n  -ms-transform: scale(1);
\n  -o-transform: scale(1);
\n  transform: scale(1);
\n  opacity: 1;
\n  filter: alpha(opacity=100);
\n   position: relative;
\n  height:auto;
\n  overflow: visible;
\n  -webkit-transition: all 300ms;
\n  -o-transition: all 300ms;
\n  transition: all 300ms;
\n  z-index: 2
\n}
\n
\n.package-wrap{
\n    border: 1px solid #C5CECD;
\n    margin-bottom: 20px;
\n}
\n
\n.package-wrap-inner{
\n    margin: 10px;
\n}
\n
\n\/\*==============================
\ncustom calender
\n================================\*\/
\n.search-calendar-wrapper {
\n    overflow: hidden;
\n    padding: 10px;
\n}
\n
\n.custom-calendar {
\n    overflow: visible;
\n    position: relative;
\n    table-layout: fixed;
\n    width: 100%;
\n}
\n
\n.custom-calendar td {
\n    border: 1px solid #c5cecd;
\n    font-weight: 400;
\n    padding: 0;
\n    width: auto;
\n    height: 80px;
\n    text-align: center;
\n    vertical-align: middle;
\n}
\n
\n.custom-calendar .booking-date-button {
\n    border: none;
\n    width: 100%;
\n    height: 100%;
\n}
\n
\n.course-activity-alert,
\n.number-of-people-viewing {
\n    font-weight: 400;
\n}
\n
\n.custom-calendar .search-calendar-course-data {
\n    background: none;
\n    border: none;
\n    color: #222;
\n    font-weight: 400;
\n    padding-left: 0;
\n    text-align: left;
\n    width: 180px;
\n    height: 80px;
\n    vertical-align: top;
\n}
\n
\n.search-calendar-course-data p {
\n    margin: 0;
\n}
\n
\n.search-calendar-course-image {
\n    float: left;
\n    position: relative;
\n    margin-right: 5px;
\n}
\n
\n.search-calendar-course-image img {
\n    display: block;
\n}
\n
\n.search-calendar-course-image .fa {
\n    line-height: 24px;
\n    text-align: center;
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n    width: 24px;
\n    height: 24px;
\n}
\n
\n.custom-calendar  table {
\n    position: relative;
\n    width: 100%;
\n    table-layout: fixed;
\n}
\n
\n.date-and-package .pending-pack{
\n    font-size: 14px;
\n
\n}
\n.date-and-package .custom-calendar tbody td{
\n   font-size: 15px;
\n}
\n.alternative-dates-wrap .custom-calendar tbody td{
\n  font-size: 15px;
\n}
\n
\n.alternative-dates-wrap{
\n  display: none;
\n}
\n.alternative-dates-wrap .custom-calendar tbody td span,
\n.alternative-dates-wrap .pending-pack{
\n  font-size: 13px;
\n}
\n.alternative-dates-wrap .subdropdwon-alternative-date{
\n  left: 0;
\n  right: 0;
\n  z-index: 5;
\n  border-top: 5px solid #fff;
\n  border-bottom: 5px solid #fff;
\n}
\n
\n.alt-date-book,
\n.booking-date-button{ cursor: pointer;}
\n
\n.custom-calendar tbody tr:first-child td {
\n    border: none;
\n    height: auto;
\n    padding-bottom: 5px;
\n    position: relative;
\n}
\n.custom-calendar tbody tr:first-child td:hover{
\n  background: no-repeat;
\n}
\n.custom-calendar tbody td span{
\n  display:block;
\n}
\n.select-package .prv-price {
\n     text-decoration: line-through;
\n}
\n
\n.custom-calendar .not-allowed,
\n.custom-calendar .not-allowed:hover{
\n  background: #e7e7e7;
\n  color: #787878;
\n  cursor: not-allowed;
\n  border-color:#fff;
\n  z-index: 1000;
\n}
\n
\n.custom-calendar .not-allowed [data-tooltip] {
\n    width: 100%;
\n    height: 100%;
\n    padding-top: 50%;
\n    padding-top: calc(50% - .5em);
\n    z-index: 2;
\n}
\n
\n.continue{
\n    position: relative;
\n}
\n
\n.custom-calendar .pack-purchase,
\n.custom-calendar .pack-purchase:hover{
\n    background: #00c6ee;
\n}
\n
\n.package-offers-tr > td {
\n    border: none;
\n    height: 0;
\n    padding: 0;
\n}
\n
\n.package-offers-wrap {
\n  padding: 15px;
\n  margin: 1px 0;
\n  font-weight: 300;
\n  position: relative;
\n  background: white;
\n  z-index: 1;
\n  border: 1px solid #E8E8E8;
\n}
\n.package-offers-wrap .close-package{
\n  position: absolute;
\n  right: 20px;
\n  top:20px;
\n  color: #222;
\n}
\n
\n.package-offers-wrap h2{
\n  margin: 0;
\n  padding-bottom:5px;
\n  border-bottom: 1px solid;
\n}
\n.package-offers-wrap h3{
\n  font-size: 18px;
\n  clear:both;
\n  padding-top: 10px;
\n}
\n
\n.check-bullets li{
\n  position: relative;
\n  font-size: 16px;
\n  color: #222;
\n  margin-top: 10px;
\n  padding-left: 20px;
\n  white-space:nowrap;
\n  width: 100%;
\n
\n}
\n.check-bullets li:after{
\n  content: \"\f00c\";
\n  position: absolute;
\n  color: #222;
\n  left: 0;
\n  top:0;
\n  font-family: \'FontAwesome\';
\n}
\n
\n.package-offers-wrap .summary-wrap{
\n    text-align: left;
\n}
\n
\n.package-offers-wrap .course-title-text {
\n    text-transform: inherit;
\n    text-align: left;
\n    font-size: 22px;
\n    font-weight: 500;
\n}
\n
\n.course-title-badges {
\n    float: right;
\n}
\n
\n.package-offers-wrap .class-date-text {
\n    text-align: left;
\n}
\n
\n.package-offers-wrap i.fa.fa-book {
\n    font-size: 30px;
\n}
\n
\n.package-offers-wrap .amendable-text{
\n    margin-top: -10px;
\n    font-size: 12px !important;
\n}
\n
\n.package-offers-wrap .alternativen-dates-btn{
\n    text-align: left;
\n}
\n
\n.package-offers-wrap .summary-wrap p{
\n  line-height: 25px;
\n  margin: 0;
\n}
\n.package-offers-wrap .summary-wrap .more{
\n  float: right;
\n  font-size: 14px;
\n  text-decoration: underline;
\n}
\n.package-offers-wrap .summary-wrap .more:hover{
\n  text-decoration: none;
\n}
\n.classes-details-wrap .details-wrap {
\n    background: #fff;
\n    border: 1px solid #d8d8d8;
\n    display: table;
\n    width: 100%;
\n    min-height: 7.5em;
\n}
\n
\n@media(min-width: 768px){
\n  .classes-details-wrap .details-wrap li{
\n    display: table-cell;
\n    vertical-align: middle;
\n  }
\n  .classes-details-wrap .details-wrap li:first-child{
\n
\n    border-right: 1px solid #d8d8d8;
\n    width: 45px;
\n    text-align: center;
\n    padding: 0;
\n  }
\n  .classes-details-wrap .details-wrap li:last-child {
\n    width: 200px;
\n
\n  }
\n  .classes-details-wrap .details-wrap li::nth-child(5):nth-last-child(2) {
\n      width: 175px;
\n  }
\n  .classes-details-wrap .details-wrap li:first-child span{
\n    padding: 20px 0;
\n    color: #fff;
\n  }
\n  .classes-details-wrap .details-wrap li:first-child span:not(:last-child) {
\n    border-bottom: 1px solid #d8d8d8;
\n  }
\n}
\n.classes-details-wrap .details-wrap li{
\n    text-align: center;
\n    padding: 0 7px;
\n}
\n.classes-details-wrap .details-wrap li:first-child{
\n  background: #12387f;
\n   text-align: center;
\n}
\n
\n@media(max-width: 767px){
\n  .classes-details-wrap.full\-\-view .details-wrap li:first-child{
\n    border-bottom: 1px solid #d8d8d8;
\n    padding: 0px;
\n  }
\n  .classes-details-wrap.full\-\-view .details-wrap li{
\n    padding:20px;
\n  }
\n  .classes-details-wrap.full\-\-view .details-wrap li:first-child span{
\n    padding: 10px 0;
\n    color: #fff;
\n    width: 49%;
\n    display: inline-block;
\n    border-bottom: none;
\n    vertical-align: top;
\n  }
\n  .classes-details-wrap.full\-\-view .details-wrap li:first-child span:first-child{
\n    border-right: 1px solid #d8d8d8;
\n  }
\n
\n
\n.custom-calendar .classes-details-wrap .details-wrap li{
\n    display: table-cell;
\n    vertical-align: middle;
\n  }
\n  .custom-calendar  .classes-details-wrap .details-wrap li:first-child{
\n
\n    border-right: 1px solid #d8d8d8;
\n    width: 45px;
\n    text-align: center;
\n  }
\n  .custom-calendar  .classes-details-wrap .details-wrap li:last-child {
\n    width: 200px;
\n
\n  }
\n  .custom-calendar  .classes-details-wrap .details-wrap li:nth-last-child(2) {
\n      width: 175px;
\n  }
\n  .custom-calendar  .classes-details-wrap .details-wrap li:first-child span{
\n    padding: 20px 0;
\n    color: #fff;
\n  }
\n  .custom-calendar  .classes-details-wrap .details-wrap li:first-child span:not(:last-child) {
\n    border-bottom: 1px solid #d8d8d8;
\n  }
\n}
\n
\n.classes-details-wrap .details-wrap li {
\n    font-size: 14px;
\n    font-weight: 400;
\n    color: #565656;
\n}
\n
\n.classes-details-wrap .details-wrap li span {
\n    text-overflow: ellipsis;
\n    white-space: nowrap;
\n    overflow: hidden;
\n    display: block;
\n}
\n
\n.classes-details-wrap .time,
\n.classes-details-wrap .price,
\n.classes-details-wrap .fa {
\n    font-size: 18px;
\n    color: #12387f;
\n}
\n
\n.details-wrap .fa {
\n    font-size: 30px;
\n}
\n
\n.details-wrap [class\*=\"flaticon-\"] {
\n    font-size: 25px;
\n}
\n
\n.classes-details-wrap .details-wrap .icon {
\n  position: relative;
\n  margin: 5px 0;
\n}
\n
\n.sidelines {
\n    position: relative;
\n}
\n
\n.sidelines:before,
\n.sidelines:after {
\n    content: \'\';
\n    border-top: 1px solid #e4e4e4;
\n    display: block;
\n    position: absolute;
\n    bottom: 50%;
\n    width: 30%;
\n}
\n
\n.sidelines:before { left: .25em; }
\n.sidelines:after { right: .25em; }
\n
\n.details-wrap  .left-place{
\n  font-size: 14px;
\n}
\n
\n@media(min-width: 768px){
\n  .details-wrap .price-wrap{
\n    border-left: 1px solid #e4e4e4;
\n    border-right: 1px solid #e4e4e4;
\n  }
\n}
\n@media(max-width: 767px){
\n  .details-wrap .price-wrap{
\n    border-top: 1px solid #e4e4e4;
\n    border-bottom: 1px solid #e4e4e4;
\n    padding: 15px 0;
\n  }
\n}
\n.classes-details-wrap .details-wrap .button\-\-book {
\n  cursor: pointer;
\n  font-weight: 600;
\n  border-radius: 7px;
\n}
\n
\n.classes-details-wrap .details-wrap  .remove-booking{
\n  font-size: 18px;
\n  font-weight: 400;
\n  text-decoration: underline;
\n}
\n.classes-details-wrap .details-wrap  .remove-booking:hover{
\n  text-decoration: none;
\n}
\n
\n.classes-details-wrap .details-wrap  .wishlist.remove{
\n    font-size: 18px;
\n    font-weight: 400;
\n    text-decoration: underline;
\n}
\n.classes-details-wrap .details-wrap  .wishlist.remove:hover{
\n    text-decoration: none;
\n}
\n
\n.classes-details-wrap .alert-wrap {
\n  padding: 10px;
\n  margin-bottom: 20px;
\n  border: 1px solid transparent;
\n  border-radius: 4px;
\n  float: right;
\n}
\n
\n.alternativen-dates-btn{
\n  text-decoration: underline;
\n  cursor: pointer;
\n}
\n
\n.custom-slider-arrow a{
\n  font-size: 20px;
\n}
\n.date-and-package {
\n    display: inline-block;
\n    width: 100%;
\n}
\n
\n.select-package .swiper-button-next,
\n.select-package .swiper-button-prev{
\n  background-size: 15px auto;
\n  height: 20px;
\n  margin-top: 0;
\n  top: 8px;
\n  width: 20px;
\n}
\n
\n@media(min-width: 768px){
\n  .select-package .swiper-button-next,
\n  .select-package .swiper-container-rtl .swiper-button-prev{
\n    right: 0;
\n  }
\n  .select-package .swiper-button-prev,
\n  .select-package .swiper-container-rtl .swiper-button-next{
\n    left: 0;
\n  }
\n}
\n
\n.select-package .swiper-button-prev,
\n.select-package .swiper-container-rtl .swiper-button-next{
\n  left: 0;
\n}
\n
\n@media(max-width: 767px){
\n.date-and-package{
\n    display: -webkit-box;
\n    display: -moz-box;
\n    display: -ms-flexbox;
\n    display: -webkit-flex;
\n    display: flex;
\n  }
\n
\n  .swiper-container-mob .custom-calendar{
\n    width: 910px;
\n  }
\n
\n  .swiper-container-mob{
\n    overflow-x: auto;
\n  }
\n
\n .select-package .swiper-button-next,
\n .select-package .swiper-button-prev{
\n    display: none;
\n  }
\n
\n}
\n
\n@-webkit-keyframes slideInRight {
\n  from {
\n    -webkit-transform: translate3d(100%, 0, 0);
\n    transform: translate3d(100%, 0, 0);
\n    visibility: visible;
\n  }
\n
\n  to {
\n    -webkit-transform: translate3d(0, 0, 0);
\n    transform: translate3d(0, 0, 0);
\n  }
\n}
\n
\n@keyframes slideInRight {
\n  from {
\n    -webkit-transform: translate3d(100%, 0, 0);
\n    transform: translate3d(100%, 0, 0);
\n    visibility: visible;
\n  }
\n
\n  to {
\n    -webkit-transform: translate3d(0, 0, 0);
\n    transform: translate3d(0, 0, 0);
\n  }
\n}
\n
\n.slideInRight {
\n  -webkit-animation-name: slideInRight;
\n  animation-name: slideInRight;
\n   -webkit-animation-duration: 0.5s;
\n  animation-duration: 0.5s;
\n  -webkit-animation-fill-mode: both;
\n  animation-fill-mode: both;
\n}
\n\/\*==============================
\n  thank you page style
\n================================\*\/
\n.just_booked .simplebox-columns {
\n    background: var(\-\-primary);
\n    box-shadow: 0 4px 25px rgba(0, 0, 0, .11);
\n    color: #fff;
\n    max-width: 800px;
\n    padding: 15px;
\n}
\n
\n.just_booked .simplebox-columns a:not([class]),
\n.just_booked .simplebox-columns a:not([class]):visited {
\n    color: inherit;
\n}
\n
\n.just_booked .simplebox-content > :first-child {
\n    margin-top: 0;
\n}
\n
\n.just_booked .simplebox-content > :last-child {
\n    margin-bottom: 0;
\n}
\n
\n.share_button {
\n    cursor: pointer;
\n    display: inline-block;
\n}
\n
\n.share_button:hover {
\n    text-decoration: none;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .share_button {
\n        background: #eee;
\n        background-size: 100%;
\n        border-radius: 50%;
\n        width: 68px;
\n        height: 68px;
\n        min-width: 0;
\n        text-indent: -9999px;
\n        overflow: hidden;
\n    }
\n
\n    .share_button\-\-facebook  { background-image: url(\'\/engine\/shared\/img\/social\/facebook-icon.png\');  }
\n    .share_button\-\-twitter   { background-image: url(\'\/engine\/shared\/img\/social\/twitter-icon.png\');   }
\n    .share_button\-\-instagram { background-image: url(\'\/engine\/shared\/img\/social\/instagram-icon.png\'); }
\n    .share_button\-\-snapchat  { background-image: url(\'\/engine\/shared\/img\/social\/snapchat-icon.png\');  }
\n    .share_button\-\-youtube   { background-image: url(\'\/engine\/shared\/img\/social\/youtube-icon.png\');   }
\n    .share_button\-\-email     { background-image: url(\'\/engine\/shared\/img\/social\/email-icon.svg\');     }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .just_booked .simplebox-columns {
\n        padding: 40px 48px;
\n    }
\n
\n    .share_button {
\n        background: #3259a6;
\n        border: 1px solid #3259a6;
\n        border-radius: .4444em;
\n        box-shadow: inset 0 -1px 1px rgba(0, 0, 0, .5);
\n        box-shadow: inset 1px 1px 1px rgba(255, 255, 255, .5), inset -1px -1px 1px rgba(0, 0, 0, .5);
\n        font-size: 1.125rem;
\n        height: 3em;
\n        line-height: 1.6666667em;
\n        margin: 0 .5em;
\n        min-width: 13em;
\n        padding: .555555em 0 .555555em 2.444444em;
\n        position: relative;
\n        text-align: center;
\n        text-decoration: none;
\n        text-shadow:1px 0 0 #a5a5a5;
\n    }
\n
\n    .share_button.share_button.share_button {
\n        color: #fff;
\n    }
\n
\n    .share_button:before {
\n        content: \'\';
\n        background: no-repeat center center;
\n        border-right: 1px solid  rgba(255, 255, 255, .2);
\n        font-size: 1.25em;
\n        position: absolute;
\n        top: .5em;
\n        left: 0;
\n        width: 2em;
\n        height: 1.25em;
\n    }
\n
\n    .share_button\-\-facebook {
\n        background: rgb(86, 129, 207);
\n        background: -moz-linear-gradient(top,  rgba(86,129,207,1) 0%, rgba(46,86,174,1) 100%);
\n        background: -webkit-linear-gradient(top,  rgba(86,129,207,1) 0%,rgba(46,86,174,1) 100%);
\n        background: linear-gradient(to bottom,  rgba(86,129,207,1) 0%,rgba(46,86,174,1) 100%);
\n        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#5681cf\', endColorstr=\'#2e56ae\',GradientType=0 );
\n        color: #fff;
\n    }
\n
\n    .share_button\-\-facebook:before {
\n        content: \'\\f09a\';
\n        font-family: fontAwesome;
\n    }
\n
\n    .share_button\-\-facebook:hover {
\n        background: rgb(86, 129, 207);
\n    }
\n
\n    .share_button\-\-twitter {
\n        background: rgb(92, 195, 243);
\n        background: -moz-linear-gradient(top,  rgba(92,195,243,1) 0%, rgba(63,173,224,1) 100%);
\n        background: -webkit-linear-gradient(top,  rgba(92,195,243,1) 0%,rgba(63,173,224,1) 100%);
\n        background: linear-gradient(to bottom,  rgba(92,195,243,1) 0%,rgba(63,173,224,1) 100%);
\n        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#5cc3f3\', endColorstr=\'#3fade0\',GradientType=0 );
\n        border-color: #329fd6;
\n        color: #fff;
\n    }
\n
\n    .share_button\-\-twitter:before {
\n        content: \'\\f099\';
\n        font-family: fontAwesome;
\n    }
\n
\n    .share_button\-\-twitter:hover {
\n        background: rgb(92, 195, 243);
\n    }
\n
\n    .share_button\-\-email {
\n        background: rgb(170, 170, 170);
\n        background: linear-gradient(to bottom, rgba(170,170,170,1) 0%, rgba(124,124,124,1) 100%);
\n        border-color: #858585;
\n        color: #222;
\n    }
\n
\n    .share_button\-\-email:before {
\n        content: \'\\f003\';
\n        font-family: fontAwesome;
\n    }
\n
\n    .share_button\-\-email:hover{
\n        background: rgb(170, 170, 170);
\n    }
\n
\n    .share_button\-\-addthis:before {
\n        background-image: url(\'..\/images\/arrow.png\');
\n    }
\n}
\n
\n
\n
\n
\n\/\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\
\n  #Our Platform page
\n\\*\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\-\*\/
\n.simplebox > .our-platform-banner {
\n    height: 35vw;
\n    min-height: 35vw;
\n}
\n
\n.our-platform-banner > h1 {
\n    position: relative;
\n    top: 50%;
\n    transform: translateY(-50%);
\n    text-align: center;
\n}
\n
\n@media screen and (min-width: 480px) {
\n    .our-platform-icon-list {
\n        margin-left: -1em;
\n        margin-right: -1em;
\n    }
\n
\n    .our-platform-icon-list > li {
\n        width: 14rem;
\n    }
\n}
\n
\n@media screen and (min-width: 880px) {
\n    .our-platform-icon-list > li {
\n        width: 17.875rem;
\n    }
\n}
\n
\n.our-platform-icon-list {
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -ms-flex-wrap: wrap;
\n    flex-wrap: wrap;
\n}
\n
\n.our-platform-icon-list > li {
\n    display: flex;
\n    font-size: 1.5rem;
\n    -webkit-box-orient: vertical;
\n    -webkit-box-direction: normal;
\n    -ms-flex-direction: column;
\n    flex-direction: column;
\n    list-style: none;
\n    margin: 1.5em 0;
\n    padding: 0 .5em;
\n    text-align: center;
\n}
\n
\n.our-platform-icon-list > li:before {
\n    display: none;
\n}
\n
\n.our-platform-icon-list > li > img {
\n    display: block;
\n    margin: 0 auto auto;
\n}
\n
\n.our-platform-icon-list > li > span {
\n    display: block;
\n    margin-top: 1em;
\n    min-height: 2.5em;
\n}
\n
\n.our-platform-anchor-list {
\n    display: block;
\n    text-align: center;
\n}
\n
\n.our-platform-anchor-list > li {
\n    list-style:none;
\n    display:inline;
\n}
\n
\n.our-platform-anchor-list > li:before {
\n    display:none;
\n}
\n
\n.our-platform-anchor-btn {
\n    border:1px solid #3c86b3;
\n    background:#f6f6f6;
\n    padding: .5em 1em;
\n    margin:.5em .3em;
\n    box-shadow: 1px 1px 3px #ccc;
\n    font-size:24px;
\n    display: inline-block;
\n    color: inherit;
\n    -webkit-transition:.4s all ease-in-out;
\n    -moz-transition:.4s all ease-in-out;
\n    -o-transition:.4s all ease-in-out;
\n    transition:.4s all ease-in-out;
\n}
\n
\n.our-platform-anchor-btn:hover {
\n    background: #3c86b3;
\n    box-shadow: none;
\n    color: #fff;
\n}
\n
\n
\n.search_courses_right,
\n.search_courses_left {
\n    border-radius: 50%;
\n    height: 1.5em;
\n    font-size:28px;
\n    z-index: -1;
\n}
\n
\n.search_courses_right:focus,
\n.search_courses_left:focus {
\n    outline: 0;
\n}
\n
\n.arrow-right {
\n    position: absolute;
\n    right: 0;
\n    top: -4px;
\n    z-index: 100;
\n    cursor: pointer;
\n}
\n
\n.arrow-left {
\n    position: absolute;
\n    left: 0;
\n    top: -4px;
\n    z-index: 100;
\n    cursor: pointer;
\n}
\n
\n.arrow-left.for-time-slots,
\n.arrow-right.for-time-slots {
\n    z-index: 100;
\n    cursor: pointer;
\n    border-radius: 50%;
\n    height: 1.5em;
\n    font-size: 28px;
\n    top: 1px;
\n}
\n
\nbutton.button\-\-plain.arrow-left,
\nbutton.button\-\-plain.arrow-right{
\n    z-index: 1;
\n    cursor: pointer;
\n    border-radius: 50%;
\n    height: 1.5em;
\n    font-size: 28px;
\n    top: 1px;
\n}
\n
\nbutton.button\-\-plain.arrow-left:active,
\nbutton.button\-\-plain.arrow-right:active{
\n    outline:0;
\n}
\n
\ni.remove_from_cart{
\n    cursor: pointer;
\n}
\n
\n#msg_area > div > a {
\n    display: block;
\n}
\n
\n.search_history {
\n    background-color: rgba(255, 255, 255,.4);
\n    padding: 5px;
\n    border-radius: 5px;
\n    text-decoration: none;
\n}
\n
\n.search_history > a {
\n    color: #fff;
\n    margin-right: 5px;
\n    margin-left: 5px;
\n}
\n
\n.search_history .remove_search_history {
\n    color: #fff;
\n    cursor: pointer;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .previous_search_text,
\n    .search_history {
\n        color: #fff;
\n        display: block;
\n        margin: 5px 15px;
\n    }
\n
\n    .search_history {
\n        padding: .5em;
\n    }
\n
\n    .remove_search_history {
\n        float: right;
\n        padding: .15em;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .previous_search_text {
\n        float: left;
\n        margin-top: 15px;
\n        margin-right: 5px;
\n    }
\n
\n    .search_history {
\n        float: left;
\n        margin: 10px 10px 0 0;
\n    }
\n}
\n
\n.tooltip {
\n    position: relative;
\n}
\n
\n.tooltip-text {
\n    display: none;
\n    position: absolute;
\n    background: #ffffff;
\n    color: #222222;
\n    width: 150px;
\n    line-height: 1.4;
\n    min-width: 100px;
\n    border-radius: 4px;
\n    padding: 5px;
\n}
\n
\n[data-tooltip-position=\"top\"] .tooltip-text ,
\n[data-tooltip-position=\"bottom\"] .tooltip-text {
\n    left: 50%;
\n    transform: translateX(-50%);
\n}
\n
\n[data-tooltip-position=\"top\"] .tooltip-text {
\n    bottom: 100%;
\n    margin-bottom: 6px;
\n}
\n[data-tooltip-position=\"bottom\"] .tooltip-text {
\n    top: 60%;
\n    margin-top: 6px;
\n}
\n
\n.tooltip-text:after {
\n    content: \'\';
\n    display: none;
\n    position: absolute;
\n    width: 0;
\n    height: 0;
\n    border-color: transparent;
\n    border-style: solid;
\n}
\n
\n[data-tooltip-position=\"top\"] .tooltip-text:after,
\n[data-tooltip-position=\"bottom\"] .tooltip-text:after {
\n    left: 50%;
\n    margin-left: -6px;
\n}
\n
\n[data-tooltip-position=\"right\"] .tooltip-text:after,
\n[data-tooltip-position=\"left\"] .tooltip-text:after {
\n    top: 50%;
\n    margin-top: -6px;
\n}
\n
\n[data-tooltip-position=\"top\"] .tooltip-text:after {
\n    top: 100%;
\n    border-width: 6px 6px 0;
\n    border-top-color: #fff;
\n}
\n
\n.tooltip-trigger:hover + .tooltip-text,
\n.tooltip-trigger:hover + .tooltip-text:after {
\n    display: block;
\n    z-index: 50;
\n}
\n "
WHERE
  `stub`          = '04';;





/* Add the "31" theme, if it does not already exist */
INSERT INTO
  `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '31', '31', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_themes`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_themes` WHERE `stub` = '31')
    LIMIT 1
;;

/* Add the '31' theme styles */
DELIMITER  ;;
UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = "@import url(\'https:\/\/fonts.googleapis.com\/css?family=Roboto:300,300i,400,400i,700,700i\');
\n
\n:root {
\n    \-\-primary: #00c6ee;   \-\-primary-hover: #4ed1ec;   \-\-primary-active: #01b3d7;
\n    \-\-secondary: #f5f5f5; \-\-secondary-hover: #d3d3d3; \-\-secondary-active: #e6e6e6;
\n    \-\-success: #0e2a6b;   \-\-success-hover: #1f4894;   \-\-success-active: #0e2a6b;
\n    \-\-info: #17a2b8;      \-\-info-hover: #2f96b4;      \-\-info-active: #31b0d5;
\n    \-\-warning: #ffc107;   \-\-warning-hover: #f89406;   \-\-warning-active: #ec971f;
\n    \-\-danger: #f00;       \-\-danger-hover: #f62727;    \-\-danger-active: #f10303;
\n
\n    \/\* Non-standard \*\/
\n    \-\-theme-green: #95C813; \-\-theme-green-hover: #b8d12f; \-\-theme-green-active: #91A351;
\n}
\n
\nhtml,
\nbutton {
\n    font-family: Roboto, Helvetica, Arila, sans-serif;
\n}
\n
\nbody {
\n    color: #212121;
\n}
\n
\n.table thead {
\n    background: #00c6ee;
\n    color: #FFF;
\n}
\n
\n.badge {
\n    background: #00c6ee;
\n    color: #fff;
\n}
\n
\n.db-sidebar .sidebar-menu li a:hover,
\n.db-sidebar .sidebar-menu li a.active {
\n    background-color: #b8d12f;
\n    color: #fff;
\n}
\n
\n.popup-header {
\n    background: #b8d12f;
\n    color: #fff;
\n}
\n
\n.button.course-banner-button.cl_bg {
\n    background-color: #b7d12f;
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
\n    color: #b7d12f;
\n}
\n
\n.seating-selector-option-radio:checked + .button {
\n    background: #b7d12f;
\n    color: #fff;
\n}
\n
\n.seating-selector-option-hover {
\n    background-color: #00c6ee;
\n    color: #fff;
\n}
\n
\n\/\* Forms \*\/
\n.input_group-icon,
\n.login-form-container.login-form-container .modal-header {
\n    background: #00c6ee;
\n    color: #FFF;
\n}
\n
\n.select:before {
\n    border-left-color: #00c6ee;
\n}
\n
\n.select:after {
\n    border-top-color: #00c6ee;
\n}
\n
\n.form-select:before {
\n    background-image: -webkit-linear-gradient(left, transparent 0, transparent calc(100% - 2.75em), #00c6ee calc(100% - 2.75em), #00c6ee 100%);
\n    background-image: linear-gradient(to right, transparent 0, transparent calc(100% - 2.75em), #00c6ee calc(100% - 2.75em), #00c6ee 100%);
\n}
\n
\n.button,
\n.formrt button,
\n.formrt [type=\"submit\"],
\n.formrt [type=\"reset\"] {
\n    background-color: #12387f;
\n}
\n
\n
\n.button\-\-continue {
\n    background-color: #12387f;
\n}
\n
\n.button\-\-continue.inverse {
\n    background: #FFF;
\n    border: 1px solid #12387f;
\n    color: #12387f;
\n}
\n
\n.button\-\-cancel {
\n    background: #FFF;
\n    border: 1px solid #F00;
\n    color: #F00;
\n}
\n
\n.button\-\-pay {
\n    background-color: #b8d12f;
\n}
\n
\n.button\-\-pay.inverse {
\n    background: #fff;
\n    border: 1px solid #b8d12f;
\n    color: #b8d12f;
\n}
\n
\n.button\-\-book {
\n    background-color: #b8d12f;
\n}
\n
\n.button\-\-book.inverse {
\n    background: #fff;
\n    border-color: #b8d12f;
\n    color: #b8d12f;
\n}
\n
\n.button\-\-send,
\n.btn-primary {
\n    background: #00c6ee;
\n    color: #fff;
\n}
\n
\n.button\-\-send.inverse {
\n    background: #FFF;
\n    border-color: #00c6ee;
\n    color: #00c6ee;
\n}
\n
\n.button\-\-enquire {
\n    background-color: #b8d12f;
\n    color: #fff;
\n}
\n
\n.header-action:nth-child(odd) .button {
\n    background: #12387f;
\n    color: #fff;
\n}
\n
\n.header-action:nth-child(even) .button {
\n    background: #95c812;
\n    color: #fff;
\n}
\n
\n.formErrorContent,
\n.formErrorArrow div {
\n    background: #00c6ee;
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
\n.popup_box.alert-add     { border-color: #b8d12f; }
\n.popup_box.alert-remove  { border-color: #b4b4b4; }
\n
\n.popup_box .alert-icon [fill]   {   fill: #00c6ee; }
\n.popup_box .alert-icon [stroke] { stroke: #00c6ee; }
\n
\n
\n
\n\/\* Header \*\/
\n.header,
\n.mobile-breadcrumbs,
\n.dropdown-menu-header {
\n    background: #00c6ee;
\n    color: #fff;
\n}
\n
\n.header-menu .level_2 a:hover,
\n.header-menu .level_2:hover > a {
\n    color: #00c6ee;
\n}
\n
\n.header-menu .level_2 a:hover:before,
\n.header-menu .level_2:hover > a:before {
\n    border-color: #00c6ee;
\n}
\n
\n.header-item > a:not(.button) {
\n    color: #fff;
\n}
\n
\n.header-menu-section > a {
\n    border-color: #3DADCD;
\n}
\n
\n.header-menu .level_1 > a,
\n.mobile-menu .level_1 > a,
\n.mobile-menu .level_1 > button,
\n.mobile-menu-level3-section .mobile-menu-list > a {
\n    color: #b8d12f;
\n}
\n
\n.header-menu .level_2 a:hover:before,
\n.header-menu .level_2:hover > a:hover {
\n    border-left-color: #FFF;
\n}
\n
\n.header-menu .level_3 {
\n    border-bottom-color: #b8d12f;
\n}
\n
\n.mobile-menu-top strong,
\n.mobile-menu-top-avatar,
\n.mobile-menu-button-group-icon,
\n.header-cart-amount,
\n.header-cart-breakdown,
\n.final_price_value {
\n    color: #12387f;
\n}
\n
\n.mobile-menu li.active > a,
\n.checkout-item-title {
\n    color: #00c6ee;
\n}
\n
\n\/\* Quick Contact \*\/
\n@media screen and (max-width: 767px) {
\n    .quick_contact-item > a.active,
\n    .quick_contact-item > a:hover,
\n    .quick_contact-item > a:active {
\n        color: #00C6EE;
\n    }
\n}
\n
\n\/\* Sidebar \*\/
\n.sidebar-section > h2 {
\n    background: #00c6ee;
\n    color: #fff;
\n}
\n
\n.sidebar-news-list li {
\n    border-bottom: 1px solid #95C511;
\n    list-style: none;
\n    margin-bottom: 1em;
\n    padding: .4em 1.5em .15em;
\n}
\n
\na.sidebar-news-link,
\n.eventTitle {
\n    color: #12387f;
\n}
\n
\n.search-criteria-remove .fa {
\n    color: #f60000;
\n}
\n
\n\/\* Page content \*\/
\n.page-content h1 { color: #00c6ee; }
\n.page-content h2 { color: #00c6ee; }
\n.page-content h3 { color: #12387f; }
\n.page-content h4 { color: #12387f; }
\n.page-content h5 { color: #12387f; }
\n.page-content h6 { color: #12387f; }
\n
\n.page-content li:before {
\n    color: #95C511;
\n}
\n
\n.page-content a:not([class]),
\n.page-content .button\-\-link {
\n    color: #00c6ee;
\n}
\n
\n.page-content a:not([class]):visited {
\n    color: #551a8b;
\n}
\n
\n.page-content hr {
\n    border-color: #00c6ee;
\n}
\n
\n\/\* Banner search \*\/
\n.banner-search-title {
\n    background: #12387f;
\n    color: #fff;
\n}
\n
\n.banner-search .fa {
\n    color: #00c6ee;
\n}
\n
\n.banner-search form {
\n    background: #b8d12f;
\n}
\n
\n.banner-overlay-content h1 {
\n    color: #12387f;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .previous_search_text,
\n    .search_history {
\n        color: #fff;
\n        display: block;
\n        margin: 5px 15px;
\n    }
\n
\n    .search_history {
\n        padding: .5em;
\n    }
\n
\n    .remove_search_history {
\n        float: right;
\n        padding: .15em;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .previous_search_text {
\n        float: left;
\n        margin-top: 15px;
\n        margin-right: 5px;
\n    }
\n
\n    .search_history {
\n        float: left;
\n        margin: 10px 10px 0 0;
\n    }
\n}
\n
\n    .banner-search-title {
\n        border-bottom-color: #FFF;
\n    }
\n}
\n
\n.search-drilldown h3 {
\n    color: #B7D12F;
\n}
\n
\n.search-drilldown-column p {
\n    color: #12387f;
\n}
\n
\n.search-drilldown-column a.active {
\n    background: #00c6ee;
\n    color: #fff;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .search-drilldown-close:before,
\n    .search-drilldown-close:after {
\n        background-color: #12387f;
\n    }
\n
\n    .search-drilldown-column\-\-category li {
\n        border-top-color: #b8d12f;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-drilldown-column {
\n        border-color: #12387f;
\n    }
\n}
\n
\n\/\* Calendar \*\/
\n.eventsCalendar-slider {
\n    background: #00c6ee;
\n    background: -webkit-linear-gradient(#02a6c8, #00c6ee);
\n    background: linear-gradient(#02a6c8, #00c6ee);
\n}
\n
\n.eventsCalendar-currentTitle a,
\n.eventsCalendar-day-header,
\n.eventsCalendar-daysList,
\n.eventCalendar-wrap .arrow {
\n    color: #fff;
\n}
\n
\n.eventCalendar-wrap .arrow span {
\n    border-color: #fff;
\n}
\n
\n
\n.eventsCalendar-day.today {
\n    background: #fff;
\n    color: #00c6ee;
\n}
\n
\n.eventsCalendar-subtitle,
\n.eventsCalendar-list > li > time {
\n    color: #00c6ee;
\n}
\n
\n.eventsCalendar-list .eventTitle,
\n.sidebar-news-link {
\n    color: #12387f;
\n}
\n
\n\/\* News feeds \*\/
\n.news-section {
\n    background: #00c6ee;
\n}
\n
\n.news-slider-title {
\n    color: #12387f;
\n    background-color: #00c6ee;
\n}
\n
\n.swiper-pagination-bullet {
\n    background-color: #b8d12f;
\n    border-color: #A6AEAD;
\n    box-shadow: inset 0 1px 1px #aaa;
\n}
\n
\n.swiper-pagination-bullet-active {
\n    background-color: #FFF;
\n}
\n
\n.news-result-date {
\n    background-color: #b8d12f;
\n    color: #FFF;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .news-result + .news-result:before {
\n        background: linear-gradient(to right, transparent 0, #b8d12f 10%, #b8d12f 90%, transparent 100%);
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .news-result + .news-result {
\n        border-color: #44C4EE;
\n    }
\n}
\n
\n.news-story-navigation a {
\n    color: #b8d12f;
\n}
\n
\n.news-story-social {
\n    border-color: #00c6ee;
\n}
\n
\n.news-story-share_icon {
\n    color: #b8d12f;
\n}
\n
\n.news-story-social-link svg {
\n    background: #b8d12f;
\n}
\n
\n.testimonial-signature {
\n    color: #00c6ee;
\n}
\n
\n\/\* Panels \*\/
\n.carousel-section .panel {
\n    border-color: #b8d12f;
\n}
\n
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .bars-section {
\n        background: #b8d12f url(\'..\/images\/school-icons.png\');
\n    }
\n
\n    .panels-feed\-\-home_content > [class*=\"col-\"]:after {
\n        background: #b8d12f;
\n        background: linear-gradient(to right, #E6F3C8 0%, #b8d12f 20%, #b8d12f 80%, #E6F3C8 100%);
\n    }
\n}
\n
\n
\n.bar {
\n    background: #F3F5F5;
\n    background: rgba(243, 245, 245, .8);
\n    box-shadow: 0 1px 1px #aaa;
\n}
\n
\n
\n.bar-icon {
\n    background: #00c6ee;
\n    color: #FFF;
\n}
\n
\n.bar-icon svg {
\n    fill: #fff;
\n}
\n
\n.bar-text {
\n    color: #12387f;
\n}
\n
\n.panel-item.has_form {
\n    background: #b7d12f;
\n    color: #fff;
\n}
\n
\n.panel-item.has_form .button {
\n    background: #12387f;
\n    color: #fff;
\n}
\n
\n.panel-item-image:after {
\n    background-image: url(\'\/shared_media\/kilmartin\/media\/photos\/content\/panel_overlay.png\');
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
\n.course-list-header h1 {
\n    color: #12387f;
\n}
\n
\n.course-list-display-option:after {
\n    background: #b8d12f;
\n}
\n
\n.course-list\-\-grid .course-widget {
\n    border-color: #b8d12f;
\n}
\n
\n.course-widget-category {
\n    background: #00c6ee;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-grid .course-widget-price {
\n    background-color: #00c6ee;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-list .course-widget-price-original,
\n.course-list\-\-list .course-widget-price-current {
\n    color: #00c6ee;
\n}
\n
\n.course-list\-\-grid .course-widget-level,
\n.course-list\-\-grid .course-widget-time_and_date {
\n    border-color: #b8d12f;
\n}
\n
\n.course-widget-location[data-location=\"Limerick\"] { background-color: #b8d12f; color: #fff; }
\n.course-widget-location[data-location=\"Ennis\"]    { background-color: #44C6ED; color: #fff; }
\n.course-widget-location[data-location=\"all\"]      { background-color: #12387f; color: #fff; }
\n
\n.pagination-prev a,
\n.pagination-next a {
\n    background: #00c6ee;
\n}
\n
\n.pagination-prev a:before,
\n.pagination-next a:before {
\n    border-color: #fff;
\n}
\n
\n.course-header .fa {
\n    color: #00c6ee;
\n}
\n
\n.course-banner-overlay {
\n    background-color: rgba(0, 197, 237, .8);
\n    color: #fff;
\n}
\n
\n.fixed_sidebar-header {
\n    background: #198ebe;
\n    color: #fff;
\n}
\n
\n.booking-form h2 {
\n    border-color: #b8d12f;
\n    color: #12387f;
\n}
\n
\n.booking-required_field-note {
\n    color: #FE0000;
\n}
\n
\n.booking-required_field-note span,
\n.contact-form-required_note > span {
\n    color: #00c6ee;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .contact-map-overlay {
\n        background-color: #00c6ee;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .contact-map-overlay-content {
\n        background: #00c6ee;
\n        background: rgba(0, 198, 238, .85);
\n    }
\n}
\n
\n.availability-course-summary a,
\n.availability-date-read_more,
\n.availability-timeslot .highlight {
\n    color: #12387f;
\n}
\n
\n.availability-timeslot.booked {
\n    border-color: #00c6ee;
\n}
\n
\n.availability-timeslot.booked .highlight {
\n    color: #00c6ee;
\n}
\n
\n.timeline-swiper .swiper-slide.selected {
\n    background: #12387f;
\n    color: #fff;
\n}
\n
\n.timeline-swiper-highlight {
\n    color: #00c6ee;
\n}
\n
\n.timeline-swiper .swiper-slide.selected .availability-date-price_range {
\n    color: #b8d12f;
\n}
\n
\n.timeline-swiper-prev,
\n.timeline-swiper-next {
\n    color: #12387f;
\n}
\n
\n\/\* Footer \*\/
\n.footer {
\n    margin-top: 1em;
\n}
\n
\n.footer-stats {
\n    padding-top: 0;
\n    padding-bottom: 0;
\n}
\n
\n.footer-logo {
\n    padding-left: 20px;
\n    padding-right: 20px;
\n    text-align: left;
\n}
\n
\n.footer-stats-list {
\n    color: #12387f;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .footer-logo {
\n        padding-top: 20.8vw;
\n    }
\n
\n    .footer-stats {
\n        background: url(\'\/assets\/kes1/\/images\/people.png\') -123px calc(100% + 50px) repeat-x,
\n        url(\'\/assets\/kes1\/images\/clouds.png\') -280px calc(100% + 53px) repeat-x,
\n        -webkit-linear-gradient(transparent 0%, transparent 100px, #b8d12f 100px, #b8d12f 100%),
\n        url(\'\/assets\/kes1\/images\/footer-background.png\') no-repeat;
\n        background: url(\'\/assets\/kes1\/images\/people.png\') -123px calc(100% + 50px) repeat-x,
\n        url(\'\/assets\/kes1\/images\/clouds.png\') -280px calc(100% + 53px) repeat-x,
\n        linear-gradient(transparent 0%, transparent 100px, #b8d12f 100px, #b8d12f 100%),
\n        url(\'\/assets\/kes1\/images\/footer-background.png\') no-repeat;
\n        background-size: 850px, 850px, 100%, 100%;
\n    }
\n
\n    .footer-stats:after {
\n        content: \'\';
\n        clear: both;
\n        display: table;
\n        padding-top: 30px;
\n    }
\n}
\n
\n@media screen and (min-width: 700px)
\n{
\n    .footer-logo {
\n        padding-left: 80px;
\n    }
\n}
\n
\n@media screen and (min-width: 1024px) {
\n
\n    .footer-logo {
\n        padding-top: 124px;
\n    }
\n
\n    .footer-stats {
\n        background: url(\'\/assets\/kes1/\/images\/people.png\') no-repeat left calc(100% + 50px),
\n        url(\'\/assets\/kes1/\/images\/clouds.png\') -2px calc(100% + 50px) repeat-x,
\n        -webkit-linear-gradient(transparent 0%, 240px, #b8d12f 240px),
\n        url(\'\/assets\/kes1/\/images\/footer-background.png\') no-repeat;
\n        background: url(\'\/assets\/kes1/\/images\/people.png\') no-repeat left calc(100% + 50px),
\n        url(\'\/assets\/kes1/\/images\/clouds.png\') -2px calc(100% + 50px) repeat-x,
\n        linear-gradient(transparent 0%, 240px, #b8d12f 240px),
\n        url(\'\/assets\/kes1/\/images\/footer-background.png\') no-repeat;
\n    }
\n
\n    .footer-stats:after {
\n        content: \'\';
\n        background: #fff;
\n        display: block;
\n        position: absolute;
\n        bottom: 0;
\n        width: 100%;
\n        height: 39px;
\n    }
\n}
\n
\n.footer-social h2 {
\n    color: #12387f;
\n}
\n
\n
\n.footer-column-title {
\n    color: #b8d12f;
\n}
\n
\n.footer-column h4 {
\n    color: #00c6ee;
\n}
\n
\n.newsletter-signup-form .button {
\n    background-color: #00c6ee;
\n}
\n
\n\/\* Dropdown filters \*\/
\n.search-filter-total {
\n    color: #00c6ee;
\n}
\n
\n.search-filters :checked ~ .form-checkbox-helper,
\n.search-filters :checked ~ .form-radio-helper,
\n.search-filters :checked ~ .form-checkbox-label,
\n.search-filters :checked ~ .form-radio-label {
\n    border-color: #00c6ee;
\n    color: #00c6ee;
\n}
\n
\n.search-filters :checked + .form-radio-helper:after {
\n    background-color: #00c6ee;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-filter-dropdown.filter-active > button,
\n    .search-filters-clear {
\n        color: #00c6ee;
\n    }
\n
\n    .checkout-heading {
\n        background-color: #00c6ee;
\n        color: #fff;
\n    }
\n}
\n
\n\/\* Misc \*\/
\n.contact\-\-left .ui-tabs-active a {
\n    background: #00c6ee;
\n    border-color:#00c6ee;
\n    color: #fff;
\n}
\n
\n.guest-user-bg {
\n    background-image: url(\'\/assets\/kes1\/img\/checkout-login-bg.png\');
\n}
\n
\n.checkout-right-sect .btn-close:hover {
\n    color: #b8d12f;
\n    border-color: #b8d12f;
\n}
\n.checkout-progress li a:after {
\n    background-color: #fff;
\n    border-color: #00c6ee;
\n}
\n
\n.checkout-progress li.curr a:after {
\n    background: #00c6ee;
\n    background: linear-gradient(to right, rgba(2,182,218,1) 0%,rgba(0,198,238,1) 100%);
\n}
\n
\n.checkout-progress li + li:before {
\n    border-color: #00c6ee;
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
\n    color: #12387f;
\n}
\n
\n.search-package-available .show-more {
\n    background: #b8d12f;
\n    color: #fff;
\n}
\n
\n.prepay-box h6 {
\n    color: #00c6ee;
\n}
\n
\n.custom-calendar .booking-date-button {
\n    background-color: #12387f;
\n    color: #fff;
\n}
\n
\n.custom-calendar .booking-date-button:hover {
\n    background-color: #b8d12f;
\n}
\n
\n.custom-calendar button.booking-date-button.active {
\n    background-color: #fff;
\n    color: #50598D;
\n}
\n
\n.course-activity-alert,
\n.details-wrap .left-place {
\n    color: #F75A5F;
\n}
\n
\n.number-of-people-viewing {
\n    color: #b8d12f;
\n}
\n
\n.search-calendar-course-image .fa {
\n    background-color: #00c6ee;
\n    color: #fff;
\n}
\n
\n.custom-calendar tbody td.active,
\n.custom-calendar tbody td.active:hover {
\n    background-color: #fff;
\n    color: #12387f;
\n}
\n
\n.custom-calendar tbody tr:first-child td {
\n    color: #222;
\n}
\n
\n.package-offers-wrap h2 {
\n    color: #00c6ee;
\n    border-color: #c5cecd;
\n}
\n
\n.package-offers-wrap h3 {
\n    color: #00c6ee;
\n}
\n
\n.package-offers-wrap .summary-wrap .more,
\n.classes-details-wrap .details-wrap li:first-child {
\n    color: #12387f;
\n}
\n
\n.classes-details-wrap .details-wrap li:first-child {
\n    background-color: #12387f;
\n}
\n
\n.details-wrap .remove-booking,
\n.details-wrap .wishlist.remove{
\n    color: #00c6ee;
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
\n    color: #12387f;
\n}
\n
\n\/\* course results hover \*\/
\n.details-wrap:hover {
\n    background-color: #f9f9f9;
\n    border-color:#12387f ;
\n}
\n
\n.details-wrap:hover .time,
\n.details-wrap:hover .price,
\n.details-wrap:hover .fa-book {
\n    color: #12387f;
\n}
\n
\n.details-wrap:hover li:first-child {
\n    background-color: #12387f;
\n}
\n
\n.details-wrap:hover .sidelines::before,
\n.details-wrap:hover .sidelines::after,
\n.details-wrap:hover .price-wrap {
\n    border-color:#12387f;
\n}
\n
\n
\n\/\* course results booked \*\/
\n.details-wrap.booked {
\n    border-color:#00c6ee;
\n    background-color: #f9f9f9;
\n}
\n
\n.details-wrap.booked .time,
\n.details-wrap.booked .price,
\n.details-wrap.booked .fa-book {
\n    color: #00c6ee;
\n}
\n.details-wrap.booked li:first-child {
\n    background-color: #00c6ee;
\n}
\n
\n.details-wrap.booked .sidelines::before,
\n.details-wrap.booked .sidelines::after,
\n.details-wrap.booked .price-wrap {
\n    border-color:#00c6ee;
\n}
\n
\n.classes-details-wrap .alert-wrap {
\n    background-color: #00c6ee;
\n    color: #fff;
\n}
\n
\n.custom-slider-arrow a {
\n    color: #12387f;
\n}
\n
\n.search_courses_right:hover,
\n.search_courses_left:hover,
\n.arrow-left.for-time-slots:hover,
\n.arrow-right.for-time-slots:hover{
\n    color: #b8d12f;
\n}
\n
\n.custom-calendar .booking-date-button.already_booked {
\n    background-color: #00c6ee;
\n    color: #fff;
\n}
\n
\n.search_history .remove_search_history {
\n    color: #e9a075;
\n    border-color: #e9a075;
\n}
"
WHERE
  `stub` = '31'
;;






/* Add the 32 theme */
INSERT INTO
  `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '32', '32', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_themes`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_themes` WHERE `stub` = '32')
    LIMIT 1
;;


/* Add the '32' theme styles */
DELIMITER  ;;
UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles`        = "@import url(\'https:\/\/fonts.googleapis.com\/css?family=Roboto:300,300i,400,400i,700,700i,900\');
\n
\n:root {
\n    \-\-primary: #5f1026;   \-\-primary-hover: #7e1331;   \-\-primary-active: #410b19;
\n    \-\-secondary: #f5f5f5; \-\-secondary-hover: #d3d3d3; \-\-secondary-active: #e6e6e6;
\n    \-\-success: #bfb8bf;   \-\-success-hover: #a69fa6;   \-\-success-active: #847d84;
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
\n    color: #212121;
\n}
\n.layout-home {
\n    background-color: #efeeed;
\n}
\n
\n.table thead {
\n    background: #5f0f26;
\n    color: #FFF;
\n}
\n
\n.badge {
\n    background: #5f0f26;
\n    color: #fff;
\n}
\n
\n.db-sidebar .sidebar-menu li a:hover,
\n.db-sidebar .sidebar-menu li a.active {
\n    background-color: #5f0f26;
\n    color: #fff;
\n}
\n
\n.popup-header {
\n    background: #5f0f26;
\n    color: #fff;
\n}
\n
\n.button.course-banner-button.cl_bg {
\n    background-color: #5f0f26;
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
\n    color: #5f0f26;
\n}
\n
\n.seating-selector-option-radio:checked + .button {
\n    background: #5f0f26;
\n    color: #fff;
\n}
\n
\n.seating-selector-option-hover {
\n    background-color: #5f0f26;
\n    color: #fff;
\n}
\n
\n\/\* Forms \*\/
\n.input_group-icon,
\n.login-form-container.login-form-container .modal-header {
\n    background: #5f0f26;
\n    color: #FFF;
\n}
\n
\n.select:before {
\n    border-left-color: #5f0f26;
\n}
\n
\n.select:after {
\n    border-top-color: #5f0f26;
\n}
\n
\n.form-select:before {
\n    background-image: -webkit-linear-gradient(left, transparent 0, transparent calc(100% - 2.75em), #5f0f26 calc(100% - 2.75em), #5f0f26 100%);
\n    background-image: linear-gradient(to right, transparent 0, transparent calc(100% - 2.75em), #5f0f26 calc(100% - 2.75em), #5f0f26 100%);
\n}
\n
\n.button,
\n.formrt button,
\n.formrt [type=\"submit\"],
\n.formrt [type=\"reset\"],
\n:checked + .checkbox-switch-helper:before {
\n    background-color: #5f0f26;
\n}
\n
\n.button\-\-continue {
\n    background-color: #fff;
\n    border: 1px solid #5f0f26;
\n    color: #5f0f26;
\n}
\n
\n.button\-\-continue.inverse {
\n    background: #5f0f26;
\n    color: #fff;
\n}
\n
\n.button\-\-cancel {
\n    background: #eedfe4;
\n    border: 1px solid #5f0f26;
\n    color: #5f0f26;
\n    font-weight: normal;
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
\n    background-color: #bfb8bf;
\n}
\n
\n.button\-\-book.inverse {
\n    background: #FFF;
\n    border-color: #bfb8bf;
\n    color: #bfb8bf;
\n}
\n
\n.button\-\-send,
\n.btn-primary {
\n    background: #5e0e27;
\n    color: #fff;
\n}
\n
\n.button\-\-send.inverse {
\n    background: #FFF;
\n    border-color: #5e0e27;
\n    color: #5e0e27;
\n}
\n
\n.button\-\-enquire {
\n    background-color: #fff;
\n    border-color: #5e0e27;
\n    color: #5e0e27;
\n}
\n
\n.header-action:nth-child(odd) .button {
\n    background: #fff;
\n    border-color: #5e0e27;
\n    color: #5e0e27;
\n}
\n
\n.header-action:nth-child(even) .button {
\n    background: #bfb8bf;
\n    color: #fff;
\n}
\n
\n.formErrorContent,
\n.formErrorArrow div {
\n    background: #5f0f26;
\n}
\n
\n.login-form-container.login-form-container a {
\n    color: #5f0f26;
\n}
\n
\n.login-form-container.login-form-container .nav-tabs > .active > a:after {
\n    background-color: #bfb8bf;
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
\n.popup_box.alert-add     { border-color: #e0e0e0; }
\n.popup_box.alert-remove  { border-color: #b4b4b4; }
\n
\n.popup_box .alert-icon [fill]   {   fill: #5f0f26; }
\n.popup_box .alert-icon [stroke] { stroke: #5f0f26; }
\n
\n
\n\/\* Header \*\/
\n.header,
\n.mobile-breadcrumbs,
\n.dropdown-menu-header {
\n    background: #5f0f26;
\n    color: #fff;
\n}
\n
\n.header-logo img {
\n    max-height: none;
\n    width: 216px;
\n}
\n
\n.header-menu .level_2 a:hover,
\n.header-menu .level_2:hover > a {
\n    color: #5f0f26;
\n}
\n
\n.header-menu .level_2 a:hover:before,
\n.header-menu .level_2:hover > a:before {
\n    border-color: #5f0f26;
\n}
\n
\n.header-item > a:not(.button) {
\n    color: #fff;
\n}
\n
\n.header-menu-section > a {
\n    border-color: #7f4051;
\n}
\n
\n.header-menu .level_1 > a,
\n.mobile-menu .level_1 > a,
\n.mobile-menu .level_1 > button,
\n.mobile-menu-level3-section .mobile-menu-list > a {
\n    color: #5f0f26;
\n}
\n
\n.header-menu .level_2 a:hover:before,
\n.header-menu .level_2:hover > a:hover {
\n    border-left-color: #FFF;
\n}
\n
\n.header-menu .level_3 {
\n    border-bottom-color: #5f0f26;
\n}
\n
\n.mobile-menu-top strong,
\n.mobile-menu-top-avatar,
\n.mobile-menu-button-group-icon,
\n.header-cart-breakdown,
\n.final_price_value {
\n    color: #5f0f26;
\n}
\n
\n.header-cart-amount {
\n    color: #fff;
\n}
\n
\n.mobile-menu li.active > a,
\n.checkout-item-title {
\n    color: #5f0f26;
\n}
\n
\n\/\* Quick Contact \*\/
\n@media screen and (max-width: 767px) {
\n    .quick_contact-item > a.active,
\n    .quick_contact-item > a:hover,
\n    .quick_contact-item > a:active {
\n        color: #5f0f26;
\n    }
\n}
\n
\n\/\* Sidebar \*\/
\n.sidebar-section > h2 {
\n    background: #5f0f26;
\n    color: #fff;
\n}
\n
\n.sidebar-news-list li {
\n    border-bottom: 1px solid #5f0f26;
\n    padding: .4em 1.5em .15em;
\n    margin-bottom: 1em;
\n}
\n
\na.sidebar-news-link,
\n.eventTitle {
\n    color: #12387f;
\n}
\n
\n.search-criteria-remove .fa {
\n    color: #f60000;
\n}
\n
\n\/\* Page content \*\/
\n.page-content h1 { color: #5f0f26; }
\n.page-content h2 { color: #5f0f26; }
\n.page-content h3 { color: #5f0f26; }
\n.page-content h4 { color: #5f0f26; }
\n.page-content h5 { color: #5f0f26; }
\n.page-content h6 { color: #5f0f26; }
\n
\n.page-content li:before {
\n    color: #5f0f26;
\n}
\n
\n.page-content a:not([class]),
\n.page-content .button\-\-link {
\n    color: #5f0f26;
\n}
\n
\n.page-content a:not([class]):visited {
\n    color: #551a8b;
\n}
\n
\n.page-content hr {
\n    border-color: #5f0f26;
\n}
\n
\n\/\* Banner search \*\/
\n.banner-search-title {
\n    background: #bfb8bf;
\n    color: #5f0f26;
\n}
\n
\n.banner-search .fa {
\n    color: #5f0f26;
\n}
\n
\n.banner-search form {
\n    background: #5f0f26;
\n}
\n
\n.previous_search_text {
\n    color: #fff;
\n}
\n
\n.search_history a {
\n    color: #fff;
\n}
\n
\n.search_history .remove_search_history {
\n    border-color: #fff;
\n    color: #fff;
\n}
\n
\n.banner-overlay-content h1 {
\n    color: #5f0f26;
\n    font-weight: 900;
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
\n    .banner-slide\-\-left .banner-overlay .row {
\n        background-image: url(\'\/shared_media\/brookfieldcollege\/media\/photos\/content\/banner_overlay_left.png\');
\n        background-position-x: left;
\n        background-position-y: -1px;
\n    }
\n
\n    .banner-slide\-\-right .banner-overlay .row {
\n        background-image: url(\'\/shared_media\/brookfieldcollege\/media\/photos\/content\/banner_overlay_right.png\');
\n        background-position-x: right;
\n        background-position-y: -1px;
\n    }
\n
\n    .banner-slide\-\-center .banner-overlay {
\n        background: rgba(255, 255, 255, .5);
\n    }
\n}
\n
\n.search-drilldown h3 {
\n    color: #5f0f26;
\n}
\n
\n.search-drilldown-column p {
\n    color: #12387f;
\n}
\n
\n.search-drilldown-column a.active {
\n    background: #5f0f26;
\n    color: #fff;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .search-drilldown-close:before,
\n    .search-drilldown-close:after {
\n        background-color: #12387f;
\n    }
\n
\n    .search-drilldown-column\-\-category li {
\n        border-top-color: #5f0f26;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-drilldown-column {
\n        border-color: #12387f;
\n    }
\n}
\n
\n\/\* Calendar \*\/
\n.eventCalendar-wrap {
\n    border-color: #5f0f26;
\n}
\n
\n.eventsCalendar-slider {
\n    background: #bfb8bf;
\n    background: -webkit-linear-gradient(#bfb8bf, #FFF);
\n    background: linear-gradient(#bfb8bf, #FFF);
\n    border-bottom-color: #5f0f26;
\n}
\n
\n
\n.eventsCalendar-currentTitle {
\n    border-bottom-color: #5f0f26;
\n}
\n
\n.eventsCalendar-currentTitle a {
\n    color: #12387f;
\n}
\n
\n.eventCalendar-wrap .arrow span {
\n    border-color: #12387f;
\n}
\n
\n.eventsCalendar-day-header {
\n    color: #12387f;
\n}
\n
\n
\n.eventsCalendar-day.today {
\n    background-color: #fff;
\n    color: #5f0f26;
\n}
\n
\n.eventsCalendar-day.today.dayWithEvent {
\n    background-color: rgba(255, 255, 255, .5);
\n}
\n
\n.eventsCalendar-day.today.current {
\n    background-color: rgba(255, 255, 255, .25);
\n}
\n
\n.eventsCalendar-subtitle,
\n.eventsCalendar-list > li > time {
\n    color: #5f0f26;
\n}
\n
\n.eventsCalendar-list > li {
\n    border-bottom-color: #5f0f26;
\n}
\n
\n\/\* News feeds \*\/
\n.news-section {
\n    background: #fff;
\n    box-shadow: 1px 1px 10px #ccc;
\n}
\n
\n.news-slider-link {
\n  color: #5f0f26;
\n}
\n
\n.news-slider-title {
\n    color: #5f0f26;
\n    background-color: #fff;
\n}
\n
\n.swiper-pagination-bullet {
\n    background-color: #5f0f26;
\n    border-color: #A6AEAD;
\n    box-shadow: inset 0 1px 1px #aaa;
\n}
\n
\n.swiper-pagination-bullet-active {
\n    background-color: #FFF;
\n}
\n
\n.news-result-date {
\n    background-color: #5f0f26;
\n    color: #FFF;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .news-result + .news-result:before {
\n        background: linear-gradient(to right, transparent 0, #5f0f26 10%, #5f0f26 90%, transparent 100%);
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .news-result + .news-result {
\n        border-color: #5f0f26;
\n    }
\n}
\n
\n.summary_item_summary .read-more,
\n.item_tile .return_link {
\n    color: #5f0f26;
\n}
\n
\n.news-story-navigation a {
\n    color: #5f0f26;
\n}
\n
\n.news-story-social {
\n    border-color: #5f0f26;
\n}
\n
\n.news-story-share_icon {
\n    color: #5f0f26;
\n}
\n
\n.news-story-social-link svg {
\n    background: #5f0f26;
\n}
\n
\n.testimonial-signature {
\n    color: #5f0f26;
\n}
\n
\n\/\* Panels \*\/
\n.panel {
\n  background-color: #fff;
\n}
\n
\n.carousel-section .panel {
\n    border-color: #bfb8bf;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .panels-feed\-\-home_content > [class*=\"col-\"]:after {
\n        background: #bfb8bf;
\n        background: linear-gradient(to right, #E6F3C8 0%, #bfb8bf 20%, #bfb8bf 80%, #E6F3C8 100%);
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
\n    background: #bfb8bf;
\n    color: #FFF;
\n}
\n
\n.bar-icon svg {
\n  fill: #fff;
\n}
\n
\n.bar-text {
\n    color: #12387f;
\n}
\n
\n.panel-item.has_form {
\n    background: #5e0e27;
\n    color: #fff;
\n}
\n
\n.panel-item.has_form .button {
\n    background-color: #fff;
\n    border-color: #5e0e27;
\n    color: #5e0e27;
\n}
\n
\n.panel-item-image:after {
\n    background-image: url(\'\/shared_media\/brookfieldcollege\/media\/photos\/content\/panel_overlay.png\');
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
\n.course-list-header h1 {
\n    color: #12387f;
\n}
\n
\n.course-list-display-option:after {
\n    background: #5f0f26;
\n}
\n
\n.course-list\-\-grid .course-widget {
\n    border-color: #5f0f26;
\n}
\n
\n.course-widget-category {
\n    background: #5f0f26;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-grid .course-widget-price {
\n    background-color: #5f0f26;
\n    color: #FFF;
\n}
\n
\n.course-list\-\-list .course-widget-price-original,
\n.course-list\-\-list .course-widget-price-current {
\n    color: #5f0f26;
\n}
\n
\n.course-list\-\-grid .course-widget-level,
\n.course-list\-\-grid .course-widget-time_and_date {
\n    border-color: #5f0f26;
\n}
\n
\n.course-widget-location[data-location=\"Limerick\"] { background-color: #b8d12f; color: #fff; }
\n.course-widget-location[data-location=\"Ennis\"]    { background-color: #44C6ED; color: #fff; }
\n.course-widget-location[data-location=\"all\"]      { background-color: #12387f; color: #fff; }
\n
\n.pagination-prev a,
\n.pagination-next a {
\n    background: #5f0f26;
\n}
\n
\n.pagination-prev a:before,
\n.pagination-next a:before {
\n    border-color: #fff;
\n}
\n
\n.course-header .fa {
\n    color: #5f0f26;
\n}
\n
\n.course-banner-overlay {
\n    background-color: rgba(0, 197, 237, .8);
\n    color: #fff;
\n}
\n
\n.fixed_sidebar-header {
\n    background: #5f0f26;
\n    color: #fff;
\n}
\n
\n.booking-form h2 {
\n    border-color: #5f0f26;
\n    color: #12387f;
\n}
\n
\n.booking-required_field-note {
\n    color: #FE0000;
\n}
\n
\n.booking-required_field-note span,
\n.contact-form-required_note > span {
\n    color: #5f0f26;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .contact-map-overlay {
\n        background-color: #5f0f26;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .contact-map-overlay-content {
\n        background: #5f0f26;
\n        background: rgba(68,197,236,.85);
\n    }
\n}
\n
\n.availability-timeslot .highlight {
\n    color: #5f0f26;
\n}
\n
\n.availability-timeslot.booked {
\n    border-color: #5f0f26;
\n}
\n
\n.availability-timeslot.booked .highlight {
\n    color: #212121;
\n}
\n
\n.timeline-swiper .swiper-slide.selected {
\n    background: #5f0f26;
\n    color: #fff;
\n}
\n
\n.timeline-swiper-highlight {
\n    color: #5f0f26;
\n}
\n
\n.timeline-swiper-prev,
\n.timeline-swiper-next {
\n    color: #5f0f26;
\n}
\n
\n\/\* Footer \*\/
\n.page-footer {
\n    background-color: #5f0f26;
\n    color: #fff;
\n    margin: 2.75em 0 -2.75em;
\n    padding: 3em;
\n    text-align: center;
\n}
\n
\n.page-footer h1 {
\n    color: inherit;
\n    border: none;
\n    font-weight: normal;
\n    margin: 0;
\n}
\n.footer-stats-list {
\n    color: #5f0f26;
\n}
\n
\n.footer-stats {
\n    background: #fff url(\'\/shared_media\/brookfieldcollege\/media\/photos\/\/content\/footer_background.png\') top center repeat-x;
\n    min-height: 0;
\n}
\n
\n@media screen and (max-width: 1023px) {
\n    .footer-stats {
\n        background-position: 25% 0;
\n        background-size: cover;
\n    }
\n}
\n
\n.footer-stat h2:after {
\n    border-color: #5f0f26;
\n}
\n
\n.footer-social,
\n.footer-columns,
\n.footer-copyright {
\n  background-color: #e0e0e0;
\n}
\n
\n.footer-social {
\n  border-top: 1px solid #535353;
\n}
\n
\n.footer-social h2 {
\n    color: #5f0f26;
\n}
\n
\n.footer-column-title {
\n    color: #5f0f26;
\n}
\n
\n.footer-column h4 {
\n    font-weight: bold;
\n}
\n
\n.footer .form-input { background-color: transparent; }
\n
\n.footer .form-input::-webkit-input-placeholder { color: #000; font-weight: 300; }
\n.footer .form-input::-moz-placeholder          { color: #000; font-weight: 300; }
\n.footer .form-input:-ms-input-placeholder      { color: #000; font-weight: 300; }
\n
\n.newsletter-signup-form .button {
\n    background-color: #5f0f26;
\n}
\n
\n\/\* Dropdown filters \*\/
\n.search-filter-total {
\n    color: #5f0f26;
\n}
\n
\n.search-filters :checked ~ .form-checkbox-helper,
\n.search-filters :checked ~ .form-radio-helper,
\n.search-filters :checked ~ .form-checkbox-label,
\n.search-filters :checked ~ .form-radio-label {
\n    border-color: #5f0f26;
\n    color: #5f0f26;
\n}
\n
\n.search-filters :checked + .form-radio-helper:after {
\n    background-color: #5f0f26;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-filter-dropdown.filter-active > button,
\n    .search-filters-clear {
\n        color: #5f0f26;
\n    }
\n
\n    .checkout-heading {
\n        background-color: #5f0f26;
\n        color: #fff;
\n    }
\n}
\n
\n\/\* Misc \*\/
\n.contact\-\-left .ui-tabs-nav .ui-tabs-active a {
\n    background: #5f0f26;
\n    color: #fff;
\n    border-color:#5f0f26;
\n}
\n
\n.checkout-right-sect .btn-close:hover {
\n    color: #5f0f26;
\n    border-color: #5f0f26;
\n}
\n
\n.checkout-progress li a:after {
\n    background-color: #fff;
\n    border-color: #5f0f26;
\n}
\n
\n.checkout-progress li.curr a:after {
\n    background: #5f0f26;
\n}
\n
\n.checkout-progress li + li:before {
\n    border-color: #5f0f26;
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
\n    color: #12387f;
\n}
\n
\n.search-package-available .show-more {
\n    background: #5f0f26;
\n    color: #fff;
\n}
\n
\n.prepay-box h6 {
\n    color: #5f0f26;
\n}
\n
\n.custom-calendar .booking-date-button {
\n    background-color: #5f0f26;
\n    color: #fff;
\n}
\n
\n.custom-calendar .booking-date-button:hover {
\n    background-color: #0a8ba9;
\n}
\n
\n.custom-calendar button.booking-date-button.active {
\n    background-color: #fff;
\n    color: #50598D;
\n}
\n
\n.course-activity-alert,
\n.details-wrap .left-place {
\n    color: #F75A5F;
\n}
\n
\n.number-of-people-viewing {
\n    color: #5f0f26;
\n}
\n
\n.search-calendar-course-image .fa {
\n    background-color: #5f0f26;
\n    color: #fff;
\n}
\n
\n.custom-calendar tbody td.active,
\n.custom-calendar tbody td.active:hover {
\n    background-color: #fff;
\n    color: #5f0f26;
\n}
\n
\n.custom-calendar tbody tr:first-child td {
\n    color: #222;
\n}
\n
\n.package-offers-wrap h2 {
\n    color: #5f0f26;
\n    border-color: #c5cecd;
\n}
\n
\n.package-offers-wrap h3 {
\n    color: #5f0f26;
\n}
\n
\n.package-offers-wrap .summary-wrap .more,
\n.classes-details-wrap .details-wrap li:first-child {
\n    color: #5f0f26;
\n}
\n
\n.classes-details-wrap .details-wrap li:first-child {
\n  background-color: #5f0f26;
\n}
\n
\n.details-wrap .remove-booking,
\n.details-wrap .wishlist.remove{
\n    color: #5f0f26;
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
\n    color: #5f0f26;
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
\n    color: #5f0f26;
\n}
\n
\n.details-wrap:hover li:first-child {
\n    background-color: #5f0f26;
\n}
\n
\n.details-wrap:hover .sidelines::before,
\n.details-wrap:hover .sidelines::after,
\n.details-wrap:hover .price-wrap {
\n    border-color:#5f0f26;
\n}
\n
\n
\n\/\* course results booked \*\/
\n.details-wrap.booked {
\n    border-color:#5f0f26;
\n    background-color: #f3f3f3;
\n}
\n
\n.details-wrap.booked .time,
\n.details-wrap.booked .price,
\n.details-wrap.booked .fa-book {
\n    color: #5f0f26;
\n}
\n.details-wrap.booked li:first-child {
\n    background-color: #5f0f26;
\n}
\n
\n.details-wrap.booked .sidelines::before,
\n.details-wrap.booked .sidelines::after,
\n.details-wrap.booked .price-wrap {
\n    border-color:#5f0f26;
\n}
\n
\n.classes-details-wrap .alert-wrap {
\n    background-color: #5f0f26;
\n    color: #fff;
\n}
\n
\n.custom-slider-arrow a {
\n    color: #12387f;
\n}
\n
\n.search_courses_right:hover,
\n.search_courses_left:hover,
\n.arrow-left.for-time-slots:hover,
\n.arrow-right.for-time-slots:hover{
\n    color: #5f0f26;
\n}
\n
\n.custom-calendar .booking-date-button.already_booked {
\n    background-color: #bfb8bf;
\n    color: #fff;
\n}
\n
\n.swiper-button-prev {
\n    background-image: url(\"data:image\/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M0%2C22L22%2C0l2.1%2C2.1L4.2%2C22l19.9%2C19.9L22%2C44L0%2C22L0%2C22L0%2C22z\'%20fill%3D\'%235f1026\'%2F%3E%3C%2Fsvg%3E\");
\n}
\n
\n.swiper-button-next {
\n    background-image: url(\"data:image\/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2027%2044\'%3E%3Cpath%20d%3D\'M27%2C22L27%2C22L5%2C44l-2.1-2.1L22.8%2C22L2.9%2C2.1L5%2C0L27%2C22L27%2C22z\'%20fill%3D\'%235f1026\'%2F%3E%3C%2Fsvg%3E\");
\n} "
WHERE
  `stub` = '32'
;;


-- Add extra address settings
INSERT INTO
  `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `type`, `readonly`, `group`)
VALUES
('address2_line_1', 'Address 2, line 1', '', '', '', '', '', 'both', 'text', '0', 'Contact Us'),
('address2_line_2', 'Address 2, line 2', '', '', '', '', '', 'both', 'text', '0', 'Contact Us'),
('address2_line_3', 'Address 2, line 3', '', '', '', '', '', 'both', 'text', '0', 'Contact Us'),
('address3_line_1', 'Address 3, line 1', '', '', '', '', '', 'both', 'text', '0', 'Contact Us'),
('address3_line_2', 'Address 3, line 2', '', '', '', '', '', 'both', 'text', '0', 'Contact Us'),
('address3_line_3', 'Address 3, line 3', '', '', '', '', '', 'both', 'text', '0', 'Contact Us')
;;
