/*
ts:2019-05-15 10:00:00
*/

DELIMITER ;;

/* Add the "SLS" template, if it does not already exist. */
INSERT INTO
  `engine_site_templates` (`title`, `stub`, `type`, `date_created`, `date_modified`)
  SELECT 'SLS', 'SLS', 'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_templates`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_templates` WHERE `title` = 'SLS')
    LIMIT 1
;;


/* Update the template */
UPDATE
  `engine_site_templates`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `header`        = '<!DOCTYPE html>
\n<html>
\n    <head>
\n        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
\n        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
\n        <title><?= $page_data[\'title\'] ? $page_data[\'title\'] : $page_data[\'name_tag\'] ?> - SLS</title>
\n        <base href="<?= URL::base() ?>" />
\n
\n        <meta property="og:locale" content="en_GB" />
\n        <meta property="og:type" content="article" />
\n        <meta property="og:title" content="<?= $page_data[\'title\'] ? $page_data[\'title\'] : $page_data[\'name_tag\'] ?> - SLS" />
\n        <meta property="og:url" content="<?= URL::base() ?>/<?= $page_data[\'name_tag\'] ?>/" />
\n        <meta property="og:site_name" content="SLS" />
\n
\n        <link rel=\'stylesheet\' id=\'wp-block-library-css\' href=\'https://www.slsireland.ie/wp-includes/css/dist/block-library/style.min.css?ver=5.1.1\' type=\'text/css\' media=\'all\'/>
\n        <link rel=\'stylesheet\' id=\'sb-font-awesome-css\' href=\'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css\' type=\'text/css\' media=\'all\'/>
\n
\n        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
\n        <script type=\'text/javascript\' src=\'https://www.slsireland.ie/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.1\'></script>
\n        <script type=\'text/javascript\' src=\'https://www.slsireland.ie/wp-content/plugins/wp-google-maps/wpgmza_data.js?ver=5.1.1\'></script>
\n        <script type=\'text/javascript\' src=\'https://www.slsireland.ie/wp-content/plugins/gravityforms/js/jquery.json.min.js?ver=2.3.5\'></script>
\n        <link type="text/css" rel="stylesheet" href="https://www.slsireland.ie/wp-content/themes/shadon-language-solution/bs/css/bootstrap.min.css?v=1"/>
\n        <link type="text/css" rel="stylesheet" href="https://www.slsireland.ie/wp-content/themes/shadon-language-solution/fa/css/font-awesome.min.css?v=1"/>
\n        <link type="text/css" rel="stylesheet" href="https://www.slsireland.ie/wp-content/themes/shadon-language-solution/sb/slidebars.min.css?v=1"/>
\n        <link type="text/css" rel="stylesheet" href="https://www.slsireland.ie/wp-content/themes/shadon-language-solution/owl/owl.carousel.css?v=1"/>
\n        <link type="text/css" rel="stylesheet" href="https://www.slsireland.ie/wp-content/themes/shadon-language-solution/style.css?v=1"/>
\n        <?php if (Settings::instance()->get(\'site_favicon\')): ?>
\n            <link rel="shortcut icon" href="<?= Model_Media::get_image_path(Settings::instance()->get(\'site_favicon\'), \'favicons\', array(\'cachebust\' => true)); ?>" type="image/ico" />
\n        <?php endif; ?>
\n        <script src="https://www.slsireland.ie/wp-content/themes/shadon-language-solution/js/modernizr-2.6.2.min.js"  type="text/javascript"></script>
\n        <script src="https://www.slsireland.ie/wp-content/themes/shadon-language-solution/bs/js/bootstrap.min.js" type="text/javascript"></script>
\n
\n        <!\-\- Idea Bubble assets \-\->
\n        <link rel="stylesheet" type="text/css" href="<?= URL::get_engine_assets_base() ?>/css/bootstrap-multiselect.css" />
\n        <link rel="stylesheet" type="text/css" href="<?= URL::get_engine_assets_base() ?>/css/validation.css" />
\n        <link rel="stylesheet" type="text/css" href="<?= URL::get_engine_assets_base() ?>/css/jquery.datetimepicker.css" media="screen" />
\n        <link rel="stylesheet" type="text/css" href="<?= URL::get_engine_plugin_assets_base(\'courses\') ?>css/eventCalendar.css" media="screen" />
\n        <link rel="stylesheet" type="text/css" href="<?= URL::get_engine_assets_base() ?>/css/swiper.min.css" />
\n        <link rel="stylesheet" type="text/css" href="<?= URL::overload_asset(\'css/forms.css\', [\'cachebust\' => true]) ?>" />
\n
\n        <?php if (isset($theme) && trim($theme->styles)): ?>
\n            <link rel="stylesheet" href="<?=$theme->get_url() ?>" />
\n        <?php else: ?>
\n            <link rel="stylesheet" type="text/css" href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/styles.css?ts=<?= @filemtime($assets_folder_code_path.\'/css/styles.css\') ?>" />
\n            <link rel="stylesheet" type="text/css" href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/print.css" media="print" />
\n        <?php endif; ?>
\n
\n        <?php if (Settings::instance()->get(\'cookie_enabled\') === \'TRUE\'): ?>
\n            <!\-\- Cookie consent plugin by Silktide - http://silktide.com/cookieconsent \-\->
\n            <script type="text/javascript">
\n                <?php
\n                $cookie_message      = Settings::instance()->get(\'cookie_text\');
\n                $cookie_dismiss_text = Settings::instance()->get(\'hide_notice_message\');
\n                $cookie_link         = Settings::instance()->get(\'cookie_page\');
\n                $cookie_link_text    = Settings::instance()->get(\'link_text\');
\n                $cookie_message      = $cookie_message      ? $cookie_message      : \'This website uses cookies to ensure you get the best experience on our website\';
\n                $cookie_dismiss_text = $cookie_dismiss_text ? $cookie_dismiss_text : \'Got it!\';
\n                $cookie_link_text    = $cookie_link_text    ? $cookie_link_text    : \'More info\';
\n                $cookie_consent_options = array(
\n                    "message" => $cookie_message,
\n                    "dismiss" => $cookie_dismiss_text,
\n                    "learnMore" => $cookie_link_text,
\n                    "link" => $cookie_link ? Model_Pages::get_page_by_id($cookie_link) : null,
\n                    "theme" => "dark-bottom"
\n                );
\n                ?>
\n                window.cookieconsent_options = <?=json_encode($cookie_consent_options)?>;
\n            </script>
\n            <script src="<?= URL::site() ?>assets/shared/js/cookieconsent/cookieconsent.min.js"></script>
\n        <?php endif; ?>
\n
\n        <style type="text/css">
\n            body {
\n                overflow: initial;
\n                overflow: unset;
\n                position: static;
\n            }
\n
\n            label {
\n                font-weight: inherit;
\n            }
\n
\n            .container {
\n                padding-left: 0;
\n                padding-right: 0;
\n            }
\n
\n            #header,
\n            #footer {
\n                font-size: 14px;
\n            }
\n
\n            #header {
\n                position: relative;
\n                z-index: 2;
\n            }
\n
\n            #header .container,
\n            #footer .container,
\n            #header .row,
\n            #footer .row {
\n                max-width: 1170px;
\n                width: 100%
\n            }
\n
\n            #header .container {
\n                padding-left: 15px;
\n                padding-right: 15px;
\n            }
\n
\n            #main2 {
\n                margin-top: 32px;
\n            }
\n
\n            .multiselect.dropdown-toggle {
\n                color: inherit;
\n            }
\n
\n            /* Theme */
\n            .input_group-icon {
\n                background: #4b86c0;
\n            }
\n
\n            .btn-primary {
\n                background: #4b86c0;
\n                color: #fff;
\n            }
\n
\n            .btn-success {
\n                background: #f067a6;
\n                color: #fff;
\n            }
\n
\n            @media screen and (min-width: 768px) {
\n                .checkout-heading {
\n                    background-color: #4b86c0;
\n                }
\n            }
\n        </style>
\n
\n        <?= Settings::instance()->get(\'head_html\'); ?>
\n        <!\-\- Idea Bubble assets - end \-\->
\n    </head>
\n    <body>
\n        <div id="sb-site">
\n            <button id="toggleLeftMenu" class="sb-toggle-left">MENU</button>
\n            <header id="header">
\n                <div class="container">
\n                    <div class="row">
\n                        <div class="col-sm-4">
\n                            <div class="logo">
\n                                <a href="https://www.slsireland.ie" title="SLS - Shandon Language Solutions">
\n                                    <img class="img-responsive" src="https://www.slsireland.ie/wp-content/themes/shadon-language-solution/images/logox2.png" />
\n                                </a>
\n                            </div>
\n                        </div>
\n                        <div class="col-sm-8">
\n                            <div class="wrap pull-right" style="margin-top:10px;">
\n                                <ul id="menu-top" class="top-nav clearfix">
\n                                    <li id="menu-item-448"
\n                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-448">
\n                                        <a href="https://www.slsireland.ie/blog/">Latest News</a>
\n                                    </li>
\n                                </ul>
\n                                <div class="clear"></div>
\n                                <div class="social pull-right">
\n                                    <ul id="menu-topsocial" class="top-nav clearfix">
\n                                        <li id="menu-item-274" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-274 has-image">
\n                                            <a target="_blank" href="https://www.facebook.com/SLSIreland?fref=ts">
\n                                                <img
\n                                                    width="35" height="35"
\n                                                    src="https://www.slsireland.ie/wp-content/uploads/2015/04/headerFB1.png"
\n                                                    class="attachment-full size-full wp-post-image"
\n                                                    alt="Facebook" title="Facebook" />
\n                                            </a>
\n                                        </li>
\n                                        <li id="menu-item-275" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-275 has-image">
\n                                            <a target="_blank" href="https://twitter.com/slsireland">
\n                                                <img
\n                                                    width="35" height="35"
\n                                                    src="https://www.slsireland.ie/wp-content/uploads/2015/04/headerT.png"
\n                                                    class="attachment-full size-full wp-post-image"
\n                                                    alt="Twitter" title="Twitter" />
\n                                            </a>
\n                                        </li>
\n                                        <li id="menu-item-276" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-276 has-image">
\n                                            <a target="_blank" href="https://www.youtube.com/user/SLSIreland">
\n                                                <img
\n                                                    width="35" height="35"
\n                                                    src="https://www.slsireland.ie/wp-content/uploads/2015/04/headerS.png"
\n                                                    class="attachment-full size-full wp-post-image"
\n                                                    alt="Youtube" title="Youtube" />
\n                                            </a>
\n                                        </li>
\n                                        <li id="menu-item-1020" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1020 has-image">
\n                                            <a target="_blank" href="https://www.instagram.com/slsireland/">
\n                                                <img
\n                                                    width="35"
\n                                                    height="35"
\n                                                    src="https://www.slsireland.ie/wp-content/uploads/2017/04/b786b751d98d30a44b955efdfdb26422-e1491492628277.png"
\n                                                    class="attachment-full size-full wp-post-image"
\n                                                    alt="Instagram" title="Instagram" />
\n                                            </a>
\n                                        </li>
\n                                    </ul>
\n                                </div>
\n                                <div class="clearfix"></div>
\n                                <div class="buttons clearfix">
\n                                    <ul id="menu-topbuttons" class="top-nav clearfix">
\n                                        <li id="menu-item-435" class="btn yellow menu-item menu-item-type-post_type menu-item-object-page menu-item-435">
\n                                            <a href="https://www.slsireland.ie/contact/">Contact Us</a>
\n                                        </li>
\n                                        <li id="menu-item-279" class="btn pink menu-item menu-item-type-custom menu-item-object-custom menu-item-279">
\n                                            <a href="tel:+35312883354">+353 1 288 3354</a>
\n                                        </li>
\n                                        <li id="menu-item-436" class="btn blue block menu-item menu-item-type-post_type menu-item-object-page menu-item-436">
\n                                            <a href="https://www.slsireland.ie/applications/">Apply Now</a>
\n                                        </li>
\n                                    </ul>
\n                                </div>
\n                            </div>
\n                        </div>
\n                    </div>
\n                </div>
\n
\n                <div class="container-fluid" style="background-color:rgba(0,84,166,.7);position:relative;  z-index:1;">
\n                    <div class="row">
\n                        <div class="container">
\n                            <div class="row">
\n                                <div class="col-sm-12 hidden-xs" style="padding:0;">
\n                                    <ul id="menu-main" class="main-nav">
\n                                        <li id="menu-item-573" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-573">
\n                                            <a href="#">About SLS<span class="glyphicon glyphicon-menu-down"></span></a>
\n                                            <ul class="sub-menu">
\n                                                <li id="menu-item-147" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-147">
\n                                                    <a href="https://www.slsireland.ie/history/">History</a>
\n                                                </li>
\n                                                <li id="menu-item-93" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-93">
\n                                                    <a href="https://www.slsireland.ie/staff/">Staff</a>
\n                                                </li>
\n                                                <li id="menu-item-1077" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1077">
\n                                                    <a href="https://www.slsireland.ie/careers/">Careers</a>
\n                                                </li>
\n                                            </ul>
\n                                        </li>
\n                                        <li id="menu-item-86" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-86">
\n                                            <a href="https://www.slsireland.ie/courses/">Courses<span class="glyphicon glyphicon-menu-down"></span></a>
\n
\n                                            <ul class="sub-menu">
\n                                                <li id="menu-item-88" class="redB menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-88">
\n                                                    <a href="https://www.slsireland.ie/courses/academic-year/">High School Programmes</a>
\n                                                    <ul class="sub-menu">
\n                                                        <li id="menu-item-89" class="redB menu-item menu-item-type-post_type menu-item-object-page menu-item-89">
\n                                                            <a href="https://www.slsireland.ie/courses/academic-year/full-academic-year/">Full Academic Year</a>
\n                                                        </li>
\n                                                        <li id="menu-item-258" class="redB menu-item menu-item-type-post_type menu-item-object-page menu-item-258">
\n                                                            <a href="https://www.slsireland.ie/courses/academic-year/short-stay-immersion/">Short Stay Immersion</a>
\n                                                        </li>
\n                                                        <li id="menu-item-257" class="redB menu-item menu-item-type-post_type menu-item-object-page menu-item-257">
\n                                                            <a href="https://www.slsireland.ie/courses/academic-year/guardianship-support-programme/">Guardianship &#038; Support</a>
\n                                                        </li>
\n                                                    </ul>
\n                                                </li>
\n
\n                                                <li id="menu-item-256" class="blueB menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-256">
\n                                                    <a href="https://www.slsireland.ie/courses/junior-summer-school/">Junior Summer School</a>
\n                                                    <ul class="sub-menu">
\n                                                        <li id="menu-item-255" class="blueB menu-item menu-item-type-post_type menu-item-object-page menu-item-255">
\n                                                            <a href="https://www.slsireland.ie/courses/junior-summer-school/general-english-course/">General English Course</a>
\n                                                        </li>
\n                                                        <li id="menu-item-254" class="blueB menu-item menu-item-type-post_type menu-item-object-page menu-item-254">
\n                                                            <a href="https://www.slsireland.ie/courses/junior-summer-school/4to1-intensive-course/">4to1 Intensive Course</a>
\n                                                        </li>
\n                                                        <li id="menu-item-253" class="blueB menu-item menu-item-type-post_type menu-item-object-page menu-item-253">
\n                                                            <a href="https://www.slsireland.ie/courses/junior-summer-school/trinity-preparation-course/">Trinity Preparation Course</a>
\n                                                        </li>
\n                                                        <li id="menu-item-251" class="blueB menu-item menu-item-type-post_type menu-item-object-page menu-item-251">
\n                                                            <a href="https://www.slsireland.ie/courses/junior-summer-school/english-basketball-course/">English &#038; Basketball Course</a>
\n                                                        </li>
\n                                                        <li id="menu-item-252" class="blueB menu-item menu-item-type-post_type menu-item-object-page menu-item-252">
\n                                                            <a href="https://www.slsireland.ie/courses/junior-summer-school/english-rugby-course/">English &#038; Rugby Course</a>
\n                                                        </li>
\n                                                    </ul>
\n                                                </li>
\n                                                <li id="menu-item-250" class="purpleB menu-item menu-item-type-post_type menu-item-object-page menu-item-250">
\n                                                    <a href="https://www.slsireland.ie/courses/work-experience-programme/">Work Experience Programme</a>
\n                                                </li>
\n                                                <li id="menu-item-249" class="greenB menu-item menu-item-type-post_type menu-item-object-page menu-item-249">
\n                                                    <a href="https://www.slsireland.ie/courses/short-stay-groups/">Short Stay Groups</a>
\n                                                </li>
\n                                            </ul>
\n                                        </li>
\n                                        <li id="menu-item-417" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-417">
\n                                            <a href="https://www.slsireland.ie/applications/">Applications</a>
\n                                        </li>
\n                                        <li id="menu-item-574" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-574">
\n                                            <a href="https://www.slsireland.ie/resources/">Resources<span class="glyphicon glyphicon-menu-down"></span></a>
\n                                            <ul class="sub-menu">
\n                                                <li id="menu-item-248" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-248">
\n                                                    <a href="https://www.slsireland.ie/accommodation/">Accommodation</a>
\n                                                </li>
\n                                                <li id="menu-item-247" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-247">
\n                                                    <a href="https://www.slsireland.ie/airport-transfers/">Airport Transfers</a>
\n                                                </li>
\n                                                <li id="menu-item-92" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-92">
\n                                                    <a href="https://www.slsireland.ie/public-transport-info/">Public Transport
\n                                                        Info</a>
\n                                                </li>
\n                                                <li id="menu-item-246" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-246">
\n                                                    <a href="https://www.slsireland.ie/visa-information/">Visa Information</a>
\n                                                </li>
\n                                                <li id="menu-item-245" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-245">
\n                                                    <a href="https://www.slsireland.ie/irish-weather/">Irish Weather</a>
\n                                                </li>
\n                                                <li id="menu-item-244" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-244">
\n                                                    <a href="https://www.slsireland.ie/1st-day/">Summer School – 1st Day</a>
\n                                                </li>
\n                                            </ul>
\n                                        </li>
\n                                        <li id="menu-item-1121" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1121">
\n                                            <a href="https://www.slsireland.ie/become-a-host-family/">Become a Host Family</a>
\n                                        </li>
\n                                        <li id="menu-item-260" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-260">
\n                                            <a href="https://www.slsireland.ie/contact/">Contact Us</a>
\n                                        </li>
\n                                    </ul>
\n                                </div>
\n                            </div>
\n                        </div>
\n                    </div>
\n                </div>
\n            </header>
\n
\n            <section id="main2">',
  `footer`        = '            </section>
\n
\n            <footer class="footer" id="footer">
\n                <div class="container-fluid" style="background-color:#4b86c0;">
\n                    <div class="container">
\n                        <div class="row">
\n                            <div class="col-xs-9 col-xs-offset-3 col-sm-6 col-sm-offset-0" style="padding:0;">
\n                                <form style="margin-top:10px; width:47%" role="search" method="get" class="search-form" action="https://www.slsireland.ie/">
\n                                    <label style="width:100%; font-weight:400;">
\n                                        <input style="width:100%;" type="search" class="search-field" placeholder="Search…" value="" name="s" title="Search for:"/>
\n                                    </label>
\n                                </form>
\n                                <div class="footer-contact">
\n                                    <div class="textwidget">
\n                                        <p>SLS Ireland</p>
\n
\n                                        <p>Heritage House</p>
\n
\n                                        <p>Dundrum Office Park</p>
\n
\n                                        <p>Dundrum, Dublin 14</p>
\n
\n                                        <p>Tel: +353 1 288 3354</p>
\n                                    </div>
\n                                </div>
\n                                <ul id="menu-footer" class="clearfix">
\n                                    <li id="menu-item-454" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-454">
\n                                        <a href="https://www.slsireland.ie/privacy/">Privacy</a>
\n                                    </li>
\n                                    <li id="menu-item-1237" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1237">
\n                                        <a href="https://www.slsireland.ie/cookie-policy/">Cookie Policy</a>
\n                                    </li>
\n                                    <li id="menu-item-261" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-261">
\n                                        <a href="https://www.slsireland.ie/terms-conditions/">Terms &#038; Conditions</a>
\n                                    </li>
\n                                    <li id="menu-item-1217" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1217">
\n                                        <a href="https://www.slsireland.ie/careers/">Careers</a>
\n                                    </li>
\n                                </ul>
\n                            </div>
\n
\n                            <div class="col-xs-6 col-xs-offset-3 col-sm-5 col-sm-offset-0" style="padding:1%;">
\n                                <div class="col-sm-12">
\n                                    <div class="wrap pull-right">
\n                                        <a href="http://acels.ie/" target="_blank">
\n                                            <img src="https://www.slsireland.ie/wp-content/uploads/2016/09/ACELS_logo.png" alt="ACELS" height="90" width="55" />
\n                                        </a>
\n                                        <a href="http://mei.ie/" target="_blank">
\n                                            <img src="https://www.slsireland.ie/wp-content/uploads/2016/09/MEI_logo.png" alt="MEI" height="90" width="180" style="padding-left:3%;" />
\n                                        </a>
\n                                        <a href="http://www.educationinireland.com/en/" target="_blank">
\n                                            <img src="https://www.slsireland.ie/wp-content/uploads/2017/03/1490715416.png" alt="EDI" height="90" width="180" style="padding-left:3%;" />
\n                                        </a>
\n                                    </div>
\n                                </div>
\n                                <div class="col-sm-12" style="padding-top: 13%;">
\n                                    <div class="wrap pull-right">
\n                                        <div class="social clearfix pull-right">
\n                                            <ul id="menu-footersocial" class="clearfix">
\n                                                <li id="menu-item-281"
\n                                                    class="menu-item menu-item-type-custom menu-item-object-custom menu-item-281 has-image">
\n                                                    <a href="https://www.facebook.com/SLSIreland?fref=ts">
\n                                                        <img
\n                                                            width="35" height="35"
\n                                                            src="https://www.slsireland.ie/wp-content/uploads/2015/03/footerFB.png"
\n                                                            class="attachment-full size-full wp-post-image"
\n                                                            alt="Facebook" title="Facebook"
\n                                                            />
\n                                                    </a>
\n                                                </li>
\n                                                <li id="menu-item-282" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-282 has-image">
\n                                                    <a href="https://twitter.com/slsireland">
\n                                                        <img
\n                                                            width="35" height="35"
\n                                                            src="https://www.slsireland.ie/wp-content/uploads/2015/03/footerT.png"
\n                                                            class="attachment-full size-full wp-post-image"
\n                                                            alt="Twitter" title="Twitter"/>
\n                                                    </a>
\n                                                </li>
\n                                                <li id="menu-item-283" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-283 has-image">
\n                                                    <a href="https://www.youtube.com/user/SLSIreland">
\n                                                        <img
\n                                                            width="35" height="35"
\n                                                            src="https://www.slsireland.ie/wp-content/uploads/2015/03/footerS.png"
\n                                                            class="attachment-full size-full wp-post-image"
\n                                                            alt="Youtube" title="Youtube"/>
\n                                                    </a>
\n                                                </li>
\n                                            </ul>
\n                                        </div>
\n                                        <div class="clearfix"></div>
\n                                        <p class=" pull-right">Web design by <a href="http://www.enhance.ie">Enhance.ie</a></p>
\n                                    </div>
\n                                </div>
\n                            </div>
\n                        </div>
\n                    </div>
\n                </div>
\n            </footer>
\n        </div>
\n        <div id="nav-mobile" class="sb-slidebar sb-left">
\n            <button id="closeLeftMenu" class="sb-close btn btn-default"><i class="fa fa-times"></i> Close</button>
\n            <br>
\n            <ul id="menu-main-1" class="main-nav-mobile">
\n                <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-573">
\n                    <a href="#">About SLS<span class="glyphicon glyphicon-menu-down"></span></a>
\n                    <ul class="sub-menu">
\n                        <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-147">
\n                            <a href="https://www.slsireland.ie/history/">History</a>
\n                        </li>
\n                        <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-93">
\n                            <a href="https://www.slsireland.ie/staff/">Staff</a>
\n                        </li>
\n                        <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1077">
\n                            <a href="https://www.slsireland.ie/careers/">Careers</a>
\n                        </li>
\n                    </ul>
\n                </li>
\n                <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-86">
\n                    <a href="https://www.slsireland.ie/courses/">Courses<span class="glyphicon glyphicon-menu-down"></span></a>
\n                    <ul class="sub-menu">
\n                        <li class="redB menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-88">
\n                            <a href="https://www.slsireland.ie/courses/academic-year/">High School Programmes</a>
\n                        </li>
\n                        <li class="blueB menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-256">
\n                            <a href="https://www.slsireland.ie/courses/junior-summer-school/">Junior Summer School</a>
\n                        </li>
\n                        <li class="purpleB menu-item menu-item-type-post_type menu-item-object-page menu-item-250">
\n                            <a href="https://www.slsireland.ie/courses/work-experience-programme/">Work Experience Programme</a>
\n                        </li>
\n                        <li class="greenB menu-item menu-item-type-post_type menu-item-object-page menu-item-249">
\n                            <a href="https://www.slsireland.ie/courses/short-stay-groups/">Short Stay Groups</a>
\n                        </li>
\n                    </ul>
\n                </li>
\n                <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-417">
\n                    <a href="https://www.slsireland.ie/applications/">Applications</a>
\n                </li>
\n                <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-574">
\n                    <a href="https://www.slsireland.ie/resources/">Resources<span class="glyphicon glyphicon-menu-down"></span></a>
\n                    <ul class="sub-menu">
\n                        <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-248">
\n                            <a href="https://www.slsireland.ie/accommodation/">Accommodation</a>
\n                        </li>
\n                        <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-247">
\n                            <a href="https://www.slsireland.ie/airport-transfers/">Airport Transfers</a>
\n                        </li>
\n                        <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-92">
\n                            <a href="https://www.slsireland.ie/public-transport-info/">Public Transport Info</a>
\n                        </li>
\n                        <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-246">
\n                            <a href="https://www.slsireland.ie/visa-information/">Visa Information</a>
\n                        </li>
\n                        <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-245">
\n                            <a href="https://www.slsireland.ie/irish-weather/">Irish Weather</a>
\n                        </li>
\n                        <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-244">
\n                            <a href="https://www.slsireland.ie/1st-day/">Summer School – 1st Day</a>
\n                        </li>
\n                    </ul>
\n                </li>
\n                <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1121">
\n                    <a href="https://www.slsireland.ie/become-a-host-family/">Become a Host Family</a>
\n                </li>
\n                <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-260">
\n                    <a href="https://www.slsireland.ie/contact/">Contact Us</a>
\n                </li>
\n            </ul>
\n        </div>
\n        <script type="text/javascript">WebFontConfig = {
\n            google: {families: [\'Open+Sans:600,800,400:latin\']}
\n        };
\n        (function () {
\n            var wf = document.createElement(\'script\');
\n            wf.src = (\'https:\' == document.location.protocol ? \'https\' : \'http\') +
\n            \'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js\';
\n            wf.type = \'text/javascript\';
\n            wf.async = \'true\';
\n            var s = document.getElementsByTagName(\'script\')[0];
\n            s.parentNode.insertBefore(wf, s);
\n        })();</script>
\n
\n        <script src="https://www.slsireland.ie/wp-content/themes/shadon-language-solution/sb/slidebars.min.js" type="text/javascript"></script>
\n
\n        <?php $is_backend = (isset($is_backend) && $is_backend === true); ?>
\n
\n        <?php if (!$is_backend): ?>
\n            <script src="<?= URL::get_engine_assets_base() ?>js/bootstrap-3.3.5.min.js"></script>
\n            <script src="<?= URL::get_engine_assets_base() ?>js/bootstrap-toggle/bootstrap-toggle.min.js"></script>
\n            <script src="<?= URL::get_engine_assets_base() ?>js/bootstrap-multiselect.js"></script>
\n            <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/forms.js"></script>
\n        <?php endif; ?>
\n        <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/jquery.validationEngine2.js"></script>
\n        <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/jquery.validationEngine2-en.js"></script>
\n        <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/daterangepicker/jquery.datetimepicker.js"></script>
\n        <script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base(\'courses\') ?>js/jquery.eventCalendar.js"></script>
\n        <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/swiper.min.js?ts=<?= @filemtime(APPPATH.\'assets/shared/js/swiper.min.js\') ?>"></script>
\n        <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/jquery.bootpag.min.js"></script>
\n        <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/js.cookie.js"></script>
\n        <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/educate_template.js"></script>
\n        <script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base(\'payments\') ?>js/front_end/payments.js"></script>
\n        <script src="<?= URL::get_engine_assets_base() ?>js/jquery.dataTables.min.js"></script>
\n        <script src="<?= URL::get_engine_assets_base() ?>js/plugins.js"></script>
\n    </body>
\n</html>
\n'
WHERE
  `title`         = 'SLS'
;;