<div id="enqury_form">
    <a href="<?=URL::site();?>request-a-callback.html"><h1>Call me  back »</h1></a>
    <?php $form_identifier = 'contact_' ?>
    <form id="form-quick-contact" method="post" action="<?= URL::site() ?>frontend/formprocessor">
        <input type="hidden" value="Quick Contact Form" name="subject" />
        <input type="hidden" value="<?= Settings::instance()->get('company_title'); ?>" name="business_name" />
        <input type="hidden" value="thank-you.html" name="redirect" />
        <input type="hidden" value="contact_us" name="trigger" />
        <input type="hidden" value="post_contactForm" name="event" />
        <h2>Enquire Now</h2>
        <div id="enqury_form_form">
            <fieldset>
                <ul>
                    <li>
                        <input type="text" name="<?= $form_identifier ?>form_name" id="req_form_name"
                               placeholder="Enter your Name" class="width-206 height-20 validate[required]"/>
                    </li>
                    <li>
                        <input type="text" name="<?= $form_identifier ?>form_tel" id="req_form_telephone"
                               placeholder="Enter your Phone Number" class="width-206 height-20 validate[required]"/>
                    </li>
                    <li>
                        <input type="text" name="<?= $form_identifier ?>form_email_address" id="req_form_email"
                               placeholder="Enter your Email Address"
                               class="width-206 height-20 validate[required,custom[email]]"/>
                    </li>
                    <li>
                        <select name="<?=$form_identifier;?>request_for" id="<?=$form_identifier;?>request_for" class="validate[required]">
                            <option value="">Request for...</option>
                            <option value="More informatoion">More informatioin</option>
                            <option value="Call me back">Call me back</option>
                            <option value="Arrange a consultation">Arrange a consultation</option>
                        </select>
                    </li>
                    <li>
                        <select name="<?=$form_identifier;?>hear_from" id="<?=$form_identifier;?>hear_from" class="validate[required]">
                            <option value="">You heard about us from...</option>
                            <option value="Friend">Friend</option>
                            <option value="Google">Google</option>
                            <option value="TV">TV</option>
                            <option value="Radio">Radio</option>
                            <option value="Newspaper">Newspaper</option>
                            <option value="Other">Other</option>
                        </select>
                    </li>
                    <?php if (Settings::instance()->get('subscription_checkbox') != 'FALSE'): ?>
                        <li>
                            <div id="subscribetext">
                                <input type="checkbox" value="yes" id="subscribe"
                                       name="<?= $form_identifier ?>form_add_to_list"><span class="newsletter_text">Check this box so we can contact you.</span></div>
                            <div style="clear:left;"></div>
                        </li>
                    <?php endif; ?>
                </ul>
            </fieldset>
            <input name="submit-quick-contact" id="submit-quick-contact" type="submit" class="submit"
                   value="Enquire Now"/>
        </div>
    </form>
</div>


