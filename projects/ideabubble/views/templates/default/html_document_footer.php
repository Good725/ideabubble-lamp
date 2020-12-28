<link href="<?= URL::site() ?>assets/default/css/validationEngine.jquery.css" rel="stylesheet" type="text/css"/>
<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700,300italic' rel='stylesheet' type='text/css'>
<div id="footer">

	<div class="footer-column" id="contact">
		<h1>Contact</h1>

		<p>Thomcor House, Mungret Street, Limerick, Ireland<br/>
			Tel: + 353 (0)61 513030<br/>
			Email: hello@ideabubble.ie
		</p>
	</div>

    <div class="footer-column" id="case-studies">
        <!--<h1>Case Studies</h1>-->
        <?php menuhelper::add_menu_editable_heading('footer_menu') ?>
    </div>

    <div class="footer-column" id="recent-projects">
        <!--<h1>Recent Projects</h1>-->
        <!--        --><?php //menuhelper::add_menu_editable_heading('Recent Projects') ?>
        <a class="twitter-timeline" href="https://twitter.com/ideabubble/lists/website-development" data-widget-id="315080980249583616" data-link-color="#3ba9b9" data-related="twitterapi,twitter" data-aria-polite="assertive" width="300" height="260" lang="EN" data-chrome="nofooter noborders transparent">Tweets from https://twitter.com/ideabubble/lists/website-development</a>
        <script>!function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                if (!d.getElementById(id)) {
                    js = d.createElement(s);
                    js.id = id;
                    js.src = p + "://platform.twitter.com/widgets.js";
                    fjs.parentNode.insertBefore(js, fjs);
                }
            }(document, "script", "twitter-wjs");</script>


    </div>

	<div class="footer-column">
		<h1>Stay in Touch</h1>

		<form action="subcription_form" method="post" id='subcription_frm'>
			<input type="email" name="subcription_email" id="email" data-validation-engine="validate[required,custom[email]]" data-errormessage-value-missing="Email is required!" data-errormessage-custom-error="Let me give you a hint: someone@nowhere.com" data-errormessage="This is the fall-back error message."/>
			<input id="submit_form" class="submit" type="submit" value="Send">
		</form>

		<div class="social-media-icons">
			<a href="https://twitter.com/ideabubble" target="_blank"><img src="<?= URL::site() ?>assets/default/images/twitter_icon.png"/></a>
			<a href="http://www.linkedin.com/company/261335" target="_blank"><img src="<?= URL::site() ?>assets/default/images/linkedin_icon.png"/></a>
			<!-- <a href="http://www.facebook.com" target="_blank"><img src="<?=URL::site()?>assets/default/images/facebook_icon.png" /></a> -->
		</div>

	</div>

    <div id="social">
    </div>

    <div id="copyright">
        <span class="left">© 2009 - <?= date("Y") ?> Idea Bubble, Ltd. All Rights Reserved.</span>
        <span class="right"><?= (Settings::instance()->get('cms_copyright') == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>' : Settings::instance()->get('cms_copyright'); ?></span>
    </div>

</div>
<style type="text/css">
    .formError .formErrorContent, .formError .formErrorArrow div {
        background: none repeat scroll 0 0 #008A9F;
    }
</style>
<script type="text/javascript" src="<?= URL::site() ?>assets/default/js/jquery.validationEngine-en.js"></script>
<script type="text/javascript" src="<?= URL::site() ?>assets/default/js/jquery.validationEngine.js"></script>
<script type="text/javascript" src="<?= URL::site() ?>assets/default/js/forms.js"></script><!--Start of Zopim Live Chat Script-->
<script type="text/javascript">
    window.$zopim || (function (d, s) {
        var z = $zopim = function (c) {
            z._.push(c)
        }, $ = z.s =
            d.createElement(s), e = d.getElementsByTagName(s)[0];
        z.set = function (o) {
            z.set.
                _.push(o)
        };
        z._ = [];
        z.set._ = [];
        $.async = !0;
        $.setAttribute('charset', 'utf-8');
        $.src = '//cdn.zopim.com/?1F7kNFORV59s1GrOHJVfnGlVR3QmYtEk';
        z.t = +new Date;
        $.
            type = 'text/javascript';
        e.parentNode.insertBefore($, e)
    })(document, 'script');
</script><!--End of Zopim Live Chat Script-->
