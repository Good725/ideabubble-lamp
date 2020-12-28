<?php
$settings = Settings::instance()->get();
$assets_folder_path = Kohana::$config->load('config')->assets_folder_path;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script type="text/javascript">window.NREUM||(NREUM={}),__nr_require=function(n,e,t){function r(t){if(!e[t]){var o=e[t]={exports:{}};n[t][0].call(o.exports,function(e){var o=n[t][1][e];return r(o?o:e)},o,o.exports)}return e[t].exports}if("function"==typeof __nr_require)return __nr_require;for(var o=0;o<t.length;o++)r(t[o]);return r}({QJf3ax:[function(n,e){function t(n){function e(e,t,a){n&&n(e,t,a),a||(a={});for(var u=c(e),f=u.length,s=i(a,o,r),p=0;f>p;p++)u[p].apply(s,t);return s}function a(n,e){f[n]=c(n).concat(e)}function c(n){return f[n]||[]}function u(){return t(e)}var f={};return{on:a,emit:e,create:u,listeners:c,_events:f}}function r(){return{}}var o="nr@context",i=n("gos");e.exports=t()},{gos:"7eSDFh"}],ee:[function(n,e){e.exports=n("QJf3ax")},{}],3:[function(n,e){function t(n){return function(){r(n,[(new Date).getTime()].concat(i(arguments)))}}var r=n("handle"),o=n(1),i=n(2);"undefined"==typeof window.newrelic&&(newrelic=window.NREUM);var a=["setPageViewName","trackUserAction","finished","traceEvent","inlineHit","noticeError"];o(a,function(n,e){window.NREUM[e]=t("api-"+e)}),e.exports=window.NREUM},{1:12,2:13,handle:"D5DuLP"}],gos:[function(n,e){e.exports=n("7eSDFh")},{}],"7eSDFh":[function(n,e){function t(n,e,t){if(r.call(n,e))return n[e];var o=t();if(Object.defineProperty&&Object.keys)try{return Object.defineProperty(n,e,{value:o,writable:!0,enumerable:!1}),o}catch(i){}return n[e]=o,o}var r=Object.prototype.hasOwnProperty;e.exports=t},{}],D5DuLP:[function(n,e){function t(n,e,t){return r.listeners(n).length?r.emit(n,e,t):(o[n]||(o[n]=[]),void o[n].push(e))}var r=n("ee").create(),o={};e.exports=t,t.ee=r,r.q=o},{ee:"QJf3ax"}],handle:[function(n,e){e.exports=n("D5DuLP")},{}],XL7HBI:[function(n,e){function t(n){var e=typeof n;return!n||"object"!==e&&"function"!==e?-1:n===window?0:i(n,o,function(){return r++})}var r=1,o="nr@id",i=n("gos");e.exports=t},{gos:"7eSDFh"}],id:[function(n,e){e.exports=n("XL7HBI")},{}],G9z0Bl:[function(n,e){function t(){var n=v.info=NREUM.info;if(n&&n.licenseKey&&n.applicationID&&f&&f.body){c(d,function(e,t){e in n||(n[e]=t)}),v.proto="https"===l.split(":")[0]||n.sslForHttp?"https://":"http://",a("mark",["onload",i()]);var e=f.createElement("script");e.src=v.proto+n.agent,f.body.appendChild(e)}}function r(){"complete"===f.readyState&&o()}function o(){a("mark",["domContent",i()])}function i(){return(new Date).getTime()}var a=n("handle"),c=n(1),u=(n(2),window),f=u.document,s="addEventListener",p="attachEvent",l=(""+location).split("?")[0],d={beacon:"bam.nr-data.net",errorBeacon:"bam.nr-data.net",agent:"js-agent.newrelic.com/nr-536.min.js"},v=e.exports={offset:i(),origin:l,features:{}};f[s]?(f[s]("DOMContentLoaded",o,!1),u[s]("load",t,!1)):(f[p]("onreadystatechange",r),u[p]("onload",t)),a("mark",["firstbyte",i()])},{1:12,2:3,handle:"D5DuLP"}],loader:[function(n,e){e.exports=n("G9z0Bl")},{}],12:[function(n,e){function t(n,e){var t=[],o="",i=0;for(o in n)r.call(n,o)&&(t[i]=e(o,n[o]),i+=1);return t}var r=Object.prototype.hasOwnProperty;e.exports=t},{}],13:[function(n,e){function t(n,e,t){e||(e=0),"undefined"==typeof t&&(t=n?n.length:0);for(var r=-1,o=t-e||0,i=Array(0>o?0:o);++r<o;)i[r]=n[e+r];return i}e.exports=t},{}]},{},["G9z0Bl"]);</script>
		<meta name="google-site-verification" content="rcy64_pYl5xgfIgPNc0DE2O4gJs-wHmDrGiEsSVZgvI" />

        <meta name="description" content="<?php echo $page_data['seo_description'];?>">
        <meta name="keywords" content="<?php echo $page_data['seo_keywords'];?>">
        <meta name="google-site-verification" content="<?= settings::instance()->get('google_webmaster_code') ?>"/>
        <meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
		<title><?= $page_data['title']; ?></title>

		<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/validation.css" rel="stylesheet" type="text/css" />
		<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/superfish.css" rel="stylesheet" type="text/css" media="screen" />
		<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/styles.css" rel="stylesheet" type="text/css" media="all" />
		<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/images/favicon.ico" rel="SHORTCUT ICON" />
        <?php include APPPATH.'views/background_switcher.php' ?>
		<?= Settings::instance()->get('head_html'); ?>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/js/jquery-1.7.2.min.js"><\/script>')</script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2-en.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/common.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/hoverIntent.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/superfish.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/supersubs.js"></script>

		<script type="text/javascript">
			//<![CDATA[
			$("ul.sf-menu").superfish();
			//]]>
		</script>
		<?php if (Settings::instance()->get('cookie_enabled') === 'TRUE'): ?>
			<!-- Cookie consent plugin by Silktide - http://silktide.com/cookieconsent -->
			<script type="text/javascript">
				<?php
                $cookie_message      = Settings::instance()->get('cookie_text');
                $cookie_dismiss_text = Settings::instance()->get('hide_notice_message');
                $cookie_link         = Settings::instance()->get('cookie_page');
                $cookie_link_text    = Settings::instance()->get('link_text');
                $cookie_message      = $cookie_message      ? $cookie_message      : 'This website uses cookies to ensure you get the best experience on our website';
                $cookie_dismiss_text = $cookie_dismiss_text ? $cookie_dismiss_text : 'Got it!';
                $cookie_link_text    = $cookie_link_text    ? $cookie_link_text    : 'More info';
                $cookie_consent_options = array(
                    "message" => $cookie_message,
                    "dismiss" => $cookie_dismiss_text,
                    "learnMore" => $cookie_link_text,
                    "link" => $cookie_link ? Model_Pages::get_page_by_id($cookie_link) : null,
                    "theme" => "dark-bottom"
                );
                ?>
				window.cookieconsent_options = <?=json_encode($cookie_consent_options)?>; // use proper js encoding to handle special characters ' " \ / etc...
			</script>
			<script src="<?= URL::site() ?>assets/shared/js/cookieconsent/cookieconsent.min.js"></script>
		<?php endif; ?>
	</head>