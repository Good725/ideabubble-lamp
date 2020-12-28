<div class="formrt">
    <form id="ContactForm" name="ContactForm" class="vertical" method="post" action="<?=URL::Site();?>frontend/formprocessor/">
        <input type="hidden" value="Contact form" name="subject">
        <input type="hidden" value="Photo Post" name="business_name">
        <input type="hidden" value="thank-you.html" name="redirect">
        <input type="hidden" value="post_contactForm" name="event">
        <input type="hidden" value="contact_us" name="trigger">
        <fieldset>
            <ul>
                <li>
                    <label for="contact_form_name">Name</label>
                    <input type="text" title="Please enter your Name" class="validate[required,custom[onlyLetterSp],length[0,100]] text-input" value="" id="req_form_name" name="req_form_name">
                </li>
                <li>
                    <label for="contact_form_address">Address</label>
                    <textarea cols="20" rows="3" id="req_form_address" name="req_form_address"></textarea>
                </li>
                <li>
                    <label for="contact_form_tel">Phone</label>
                    <input type="text" value="" id="req_form_phone" name="req_form_phone">
                </li>
                <li>
                    <label for="contact_form_email_address">Email</label>
                    <input type="text" title="Please enter your Email" class="validate[required,custom[email]] text-input" value="" id="req_form_email" name="req_form_email">
                </li>
                <li>
                    <label for="contact_form_message">Message</label>
                    <textarea cols="20" class="messagearea" rows="6" id="req_form_message" name="req_form_message"></textarea>
                </li>
                <li>
                    <div id="subscribetext">
                        <input type="checkbox" value="yes" id="subscribe" name="contact_form_add_to_list">
                        Check this box if you want to subscribe to our Mailing List.</div>
                    <div style="clear:left;"></div>
                </li>
                <li>
                    <label for="submit1">&nbsp;</label>
                    <input type="submit" class="button" value="Send Email" id="submit1" name="submit1">
                </li>
            </ul>
            <input type="hidden" value="Contact Form" id="form_type" name="form_type">
            <input type="hidden" value="contact_" name="form_identifier">
        </fieldset>
    </form>
</div>