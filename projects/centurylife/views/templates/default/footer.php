<div id="footer">
    <div id="footer_content_left" class="left">
        Century Life &amp; Pensions Limited t/a Century Financial Services is regulated by the Central Bank of Ireland
        <dl>
            <?php if (trim(Settings::instance()->get('addres_line_1')) != ''): ?>
                <dt>Registered Office</dt>
                <dd>
                    <?=Settings::instance()->get('addres_line_1');?>
                    <?php if (trim(Settings::instance()->get('addres_line_2')) != ''): ?>
                    <br />
                    <?=Settings::instance()->get('addres_line_2');?>
                    <?php endif; ?>
                    <?php if (trim(Settings::instance()->get('addres_line_3')) != ''): ?>
                    <br />
                    <?=Settings::instance()->get('addres_line_3');?>
                    <?php endif; ?>
                </dd>
            <?php endif; ?>
            <dt>Directors</dt>
            <dd>Tom McGuinness, B.Comm., FCA, QFA, CTax.<br />Joan Cowman, BCL, LL.M, BL.</dd>
        </dl>
        <span style="bottom:0;">&copy; CENTURY LIFE &amp; PENSIONS</span>
    </div>
    <div id="footer_menu" class="left">
        <?php menuhelper::add_menu_editable_heading('Footer');?>
    </div>
    <div id="newsletter_wrapper" class="right">
        <h3>NEWSLETTER SIGN UP</h3>
        <div id="footer_newsletter_inputs">
            <form action="<?=URL::Site();?>frontend/formprocessor" method="post">
                <input type="hidden" value="Newsletter Subscription Form" name="subject">
                <input type="hidden" value="Centurylife Newsletter Signup" name="business_name">
                <input type="hidden" value="subscription-thank-you.html" name="redirect">
                <input type="hidden" value="add_to_list" name="trigger">
                <input type="text" placeholder="YOUR NAME:" class="width-191 validate[required]" name="contact_form_name" id="contact_form_name"/><br/>
                <input type="text" placeholder="YOUR EMAIL:" class="width-191 validate[required]" name="contact_form_email_address" id="contact_form_email_address"/><br/>
                <input type="submit" class="submit right" value=""/>
            </form>
        </div>
    </div>
</div>