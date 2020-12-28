<div class="formrt">
    <form id="ContactForm" name="ContactForm" class="vertical" method="post" action="">
        <input type="hidden" value="Contact form" name="subject">
        <input type="hidden" value="<?= Settings::instance()->get('company_title'); ?>" name="business_name">
        <input type="hidden" value="thank-you.html" name="redirect">
        <input type="hidden" value="post_contactForm" name="event">
        <input type="hidden" value="contact_us" name="trigger">
        <fieldset>
            <ul>
                <li>
                    <label for="contact_form_name">Name</label>
                    <input type="text" title="Please enter your Name"
                           class="validate[required,custom[onlyLetterSp],length[0,100]] text-input" value=""
                           id="contact_form_name" name="contact_form_name">
                </li>
                <li>
                    <label for="contact_form_address">Address</label>
                    <textarea cols="20" rows="3" id="contact_form_address" name="contact_form_address"></textarea>
                </li>
                <li>
                    <label for="contact_form_tel">What are you enquiring about?</label>
                    <input type="text" value="" id="contact_form_tel" name="contact_form_tel">
                </li>
                <li>
                    <label for="contact_form_email_address">Email</label>
                    <input type="text" title="Please enter your Email"
                           class="validate[required,custom[email]] text-input" value="" id="contact_form_email_address"
                           name="contact_form_email_address">
                </li>
                <li>
                    <label for="contact_form_message">Message</label>
                    <textarea cols="20" class="messagearea" rows="6" id="contact_form_message"
                              name="contact_form_message"></textarea>
                </li>
                <?php if (Settings::instance()->get('subscription_checkbox') != 'FALSE'): ?>
                    <li>
                        <div id="subscribetext">
                            <input type="checkbox" value="yes" id="subscribe" name="contact_form_add_to_list"> Check
                            this box if you want to subscribe to our Mailing List.
                        </div>
                        <div style="clear:left;"></div>
                    </li>
                <?php endif; ?>
                <li>
                    <div class="catpcha_popup_box">
                        <?php
                        $captcha_enabled = Settings::instance()->get('captcha_enabled');
                        if ($captcha_enabled) {
							require_once ENGINEPATH . '/plugins/formprocessor/development/classes/model/recaptchalib.php';
                            $captcha_public_key = Settings::instance()->get('captcha_public_key');
                            echo recaptcha_get_html($captcha_public_key);
                        }
                        ?>
                        <button id="captcha_submit_button" class="btn">Submit</button>
                    </div>
                </li>
                <li>
                    <label for="submit1">&nbsp;</label>
                    <input type="button" onclick="x_Validate();" class="button" value="Send Email" id="submit1"
                           name="submit1">
                </li>
            </ul>
            <input type="hidden" value="Contact Form" id="form_type" name="form_type">
            <input type="hidden" value="contact_" name="form_identifier">
        </fieldset>
    </form>
</div>
<script type="text/javascript">
    function x_Validate() {
        if ($('#contact_form_name').val() != "" && ($('#contact_form_tel').val() != "" || $('#contact_form_email_address').val() != "")) {
            if ("<?=$captcha_enabled?>" == "1") {
                $(".catpcha_popup_box").show();
            }
            else {
                $('#ContactForm').attr('action', '<?=url::site()?>frontend/formprocessor').submit();
            }
        }
        else {
            alert('Please fill the Name\nPlease fill the email address or telephone number');
        }
    }
    $(document).ready(function () {
        $("#captcha_submit_button").click(function () {
            $('#ContactForm').attr('action', '<?=url::site()?>frontend/formprocessor').submit();
        });
    });
</script>
<style>
    .catpcha_popup_box {
        display: none;
    }
</style>