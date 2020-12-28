<!-- footer -->
<footer id="footer">

    <section class="links-section">
        <section class="address">
            <h3>Contact Details</h3>
            <h4>LIMERICK </h4>

            <p><?=Settings::instance()->get('addres_line_1');?></p>
            <h4>ENNIS </h4>

            <p><?=Settings::instance()->get('addres_line_2');?></p>

            <p>
                <span>TEL:</span><?=Settings::instance()->get('telephone');?><br>
                <span>EMAIL:</span> <a
                    href="mailto:<?= Settings::instance()->get('email'); ?>"><?=Settings::instance()->get('email');?></a>
            </p>
        </section>
        <?php menuhelper::add_menu_editable_heading('footer');?>

        <section class="newsletter">
            <h3>Newsletter Signup</h3>
            <? $form_identifier = 'newsletter_signup_'; ?>
            <form id="form-newsletter" method="post">
                <input type="hidden" value="Newsletter Subscription Form" name="subject"/>
                <input type="hidden" value="Kilmartin Education Services" name="business_name"/>
                <input type="hidden" value="subscription-thank-you.html" name="redirect"/>
                <input type="hidden" value="add_to_list" name="trigger"/>
                <input type="hidden" value="<?= $form_identifier ?>" name="form_identifier"/>
				<span class="txtbox">
					<input name="<?= $form_identifier ?>form_name" id="<?= $form_identifier ?>form_name" type="text"
                           class="validate[required]" placeholder="Name"/>
				</span>
				<span class="txtbox">
					<input name="<?= $form_identifier ?>form_email_address"
                           id="<?= $form_identifier ?>form_email_address" class="validate[required,custom[email]]"
                           type="text" placeholder="Email"/>
				</span>
                <input name="submit-newsletter" id="submit-newsletter" type="submit" value="SUBMIT »"/>
            </form>
        </section>

        <div id="icons">
            <img src="<?= URL::site() ?>assets/default/images/visa.png" alt=""/>
            <img src="<?= URL::site() ?>assets/default/images/master_card.png" alt=""/>
        </div>

    </section>


    <p class="footer-bottom">
        <span class="left">&copy; Kilmartin Educational Services</span>
        <span class="right"><?= (Settings::instance()->get('cms_copyright') == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>' : Settings::instance()->get('cms_copyright'); ?></span>
    </p>
</footer>
<!-- /footer -->