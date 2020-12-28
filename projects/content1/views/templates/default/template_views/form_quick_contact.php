<div id="enqury_form">
    <?php $form_identifier = 'contact_' ?>
    <form id="form-quick-contact" method="post">
        <input type="hidden" value="Quick Contact Form" name="subject" />
        <input type="hidden" value="<?= Settings::instance()->get('company_title'); ?>" name="business_name" />
        <input type="hidden" value="thank-you.html" name="redirect" />
        <input type="hidden" value="contact_us" name="trigger" />
        <input type="hidden" value="post_contactForm" name="event" />
        <h2><?= Kohana::$config->load('config')->get('db_id')== 'saoirsetreatmentcenter' ? 'Contact Us' : 'Enquire Now';?></h2>
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
                            <option value="More information">More information</option>
                            <?php if (Kohana::$config->load('config')->get('db_id') != 'lsomusic'): ?>
                                <option value="Call me back">Call me back</option>
                            <?php endif; ?>
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
                                       name="<?= $form_identifier ?>form_add_to_list" /><span class="newsletter_text">Check this box so we can contact you.</span></div>
                            <div style="clear:left;"></div>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="catpcha_popup_box">
                    <?php
                    $captcha_enabled = Settings::instance()->get('captcha_enabled');
                    if ($captcha_enabled)
                    {
						require_once ENGINEPATH . '/plugins/formprocessor/development/classes/model/recaptchalib.php';
                        $captcha_public_key = Settings::instance()->get('captcha_public_key');
                    }
                    ?>
                    <?php if ($captcha_enabled and $captcha_public_key != ''): ?>
                        <script type="text/javascript">
                            var RecaptchaOptions = {
                                theme : 'custom',
                                custom_theme_widget: 'recaptcha_widget'
                            };
                        </script>

                        <div id="recaptcha_widget" style="display:none">

                            <div id="recaptcha_image"></div>
                            <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>

                            <span class="recaptcha_only_if_image">Enter the words above:</span>
                            <span class="recaptcha_only_if_audio">Enter the numbers you hear:</span>

                            <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />

                            <div><a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a></div>
                            <div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')">Get an audio CAPTCHA</a></div>
                            <div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')">Get an image CAPTCHA</a></div>

                            <div><a href="javascript:Recaptcha.showhelp()">Help</a></div>

                        </div>

                        <script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=<?= $captcha_public_key ?>">
                        </script>
                        <noscript>
                            <iframe src="http://www.google.com/recaptcha/api/noscript?k=<?= $captcha_public_key ?>" height="300" width="300" frameborder="0"></iframe><br>
                            <textarea name="recaptcha_challenge_field" rows="3" cols="40">
                            </textarea>
                            <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
                        </noscript>
                    <?php endif; ?>
                </div>
            </fieldset>
            <?php if ($captcha_enabled):?>
                <input name="submit-quick-contact" id="submit-quick-contact" type="button" onclick="enquiry_captcha()" class="submit" value="Enquire Now"/>
            <?php else: ?>
                <input name="submit-quick-contact" id="submit-quick-contact" type="submit" class="submit" value="Enquire Now"/>
            <?php endif; ?>
        </div>
    </form>
</div>
<?php if ($captcha_enabled):?>
    <script type="text/javascript">
        function enquiry_captcha() {
            if ('<?=$captcha_enabled?>' == '1')
            {
                // validate
                var valid = true;
                var email_regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                var required = $('#enqury_form_form').find('[class*=validate]');
                if (!email_regex.test($('#req_form_email').val())) {
                    valid = false;
                }

                for (var i = 0; i < required.length && valid; i++) {
                    if ($('[class*=validate]')[i].value == '') {
                        valid = false;
                    }
                }

                // CAPTCHA box after validation succeeds
                if (valid) {
                    $('.catpcha_popup_box').show();
                }
            }
            else {
                $('#enqury_form').attr('action', '<?=url::site()?>frontend/formprocessor').submit();
            }
        }
    </script>
    <style type="text/css">
        .catpcha_popup_box { display: none; clear: both; position: relative; z-index: 60; }
        .catpcha_popup_box a { color: #FFF; fonts-size: 11px; font-weight: normal; text-decoration: none; }
        .catpcha_popup_box a:hover { text-decoration: underline; }
        #recaptcha_challenge_image { width: 210px; }
    </style>
<?php endif; ?>

