<script type="text/javascript">
    function ValidateConsultationForm(){
        var form = $('#form-consultation');
        var valid = form.validationEngine('validate');
        if (valid)
            form.submit();
    }
</script>
<?php $form_identifier = 'consultation_' ?>
<form id="form-consultation" method="post" action="<?= URL::site() ?>frontend/formprocessor">
    <input type="hidden" name="subject" value="Website Consultation Form" />
    <input type="hidden" name="business_name" value="<?= Settings::instance()->get('company_title'); ?>" />
    <input type="hidden" name="redirect" value="thank-you.html" />
    <input type="hidden" name="event" value="post_consultationForm" />
    <input type="hidden" name="trigger" value="consultation" />

    <h3>Your details:</h3>
    <ul>
        <li>
            <label for="<?= $form_identifier ?>name" class="label">Name</label>
            <input type="text" name="name" id="<?= $form_identifier ?>name" value="" class="validate[required,custom[onlyLetterSp],length[0,100]],length[0,100]] text-input" title="Please enter your Name" />
        </li>

        <li>
            <label for="<?= $form_identifier ?>tel" class="label">Phone</label>
            <input type="text" name="tel" id="<?= $form_identifier ?>tel" value="" class="validate[required] text-input" />
        </li>

        <li>
            <label for="<?= $form_identifier ?>email" class="label">Email</label>
            <input type="text" name="email" id="<?= $form_identifier ?>email" value="" class="validate[required,custom[email]] text-input" title="Please enter your Email" />
        </li>

        <li>
            <label for="<?= $form_identifier ?>country" class="label">Country</label>
            <select name="country" id="<?= $form_identifier ?>country" class="validate[required]">
                <option value="">Please select</option>
                <option value="UK">UK</option>
                <option value="IRE">IRE</option>
                <option value="Rest of World">Rest of World</option>
            </select>
        </li>

        <li>
            <label for="<?= $form_identifier ?>dob" class="label">Date of birth</label>
            <input type="text" name="dob" id="<?= $form_identifier ?>dob" value="" class="hasDatepicker" />
        </li>

        <li>
            <span class="label">Sex/Gender</span>
            <input style="width: 20px;" type="radio" name="gender" value="male" id="<?= $form_identifier ?>gender-male" class="validate[required]" />
            <label for="<?= $form_identifier ?>gender-male">male</label>
            <input style="width: 20px;" type="radio" name="gender" value="female" id="<?= $form_identifier ?>gender-female" />
            <label for="<?= $form_identifier ?>gender-female">female</label>

        </li>

        <li>
            <label for="<?= $form_identifier ?>from_where" class="label">&nbsp;</label>
            <select name="from_where" id="<?= $form_identifier ?>from_where" class="validate[required]">
                <option value="">Where did you hear about us?</option>
                <option value="Friend">Friend</option>
                <option value="Google">Google</option>
                <option value="TV">TV</option>
                <option value="Radio">Radio</option>
                <option value="Newspaper">Newspaper</option>
                <option value="Other">Other</option>
            </select>
        </li>
    </ul>


    <h3>Some medical history:</h3>
    <ul>
        <li>
            <p>Are you currently taking any medication?</p>
            <input style="width: 20px;" type="radio" name="takingM" value="yes" class="validate[required]" id="<?= $form_identifier ?>takingM-yes" />
            <label for="<?= $form_identifier ?>takingM-yes">Yes</label>
            <input style="width: 20px;" type="radio" name="takingM" value="no" id="<?= $form_identifier ?>takingM-no" />
            <label for="<?= $form_identifier ?>takingM-no">No</label>
        </li>

        <li>
            <p><label for="<?= $form_identifier ?>takingM2">If yes, please provide us with some more information:</label></p>
            <textarea cols="60" rows="3" name="takingM2" id="<?= $form_identifier ?>takingM2"></textarea>
        </li>

    </ul>

    <h3>Hair Condition:</h3>
    <ul>
        <li>
            <p><label for="<?= $form_identifier ?>hairlossA">Approximately at what age did your hair loss begin:</label></p>
            <input type="text" name="hairlossA" id="<?= $form_identifier ?>hairlossA" value="" style="width: 50px;" class="validate[required]" /></li>

        <li>
            <p>At what rate has your hair loss developed:</p>
            <input style="width: 20px;" type="radio" name="hairlossB" value="slow" id="<?= $form_identifier ?>hairlossB-slow" class="validate[required]" />
            <label for="<?= $form_identifier ?>hairlossB-slow">Slow</label>
            <input style="width: 20px;" type="radio" name="hairlossB" value="gradual" id="<?= $form_identifier ?>hairlossB-gradual" />
            <label for="<?= $form_identifier ?>hairlossB-gradual">Gradual</label>
            <input style="width: 20px;" type="radio" name="hairlossB" value="fast" id="<?= $form_identifier ?>hairlossB-fast" />
            <label for="<?= $form_identifier ?>hairlossB-fast">Fast</label>
        </li>

        <li>
            <p>Have you had hair restoration before:</p>
            <input style="width: 20px;" type="radio" name="hairlossC" value="yes" class="validate[required]" id="<?= $form_identifier ?>hairlossC-yes" />
            <label for="<?= $form_identifier ?>hairlossC-yes">Yes</label>
            <input style="width: 20px;" type="radio" name="hairlossC" value="no" id="<?= $form_identifier ?>hairlossC-no" />
            <label for="<?= $form_identifier ?>hairlossC-no">No</label>
        </li>

        <li>
            <p>What would you like to achieve?</p>
            <ul>
                <li>
                    <input style="width: 20px;" type="radio" name="achieve" value="Restore hairline" class="validate[required]" id="<?= $form_identifier ?>achieve-restore" />
                    <label for="<?= $form_identifier ?>achieve-restore">Restore hairline</label>
                </li>
                <li>
                    <input style="width: 20px;" type="radio" name="achieve" value="Crown coverage" id="<?= $form_identifier ?>achieve-crown" />
                    <label for="<?= $form_identifier ?>achieve-crown">Crown coverage</label>
                </li>
                <li>
                    <input style="width: 20px;" type="radio" name="achieve" value="Lower hairline" id="<?= $form_identifier ?>achieve-headline" />
                    <label for="<?= $form_identifier ?>achieve-headline">Lower headline</label>
                </li>
                <li>
                    <input style="width: 20px;" type="radio" name="achieve" value="Close temples" id="<?= $form_identifier ?>achieve-temple" />
                    <label for="<?= $form_identifier ?>achieve-temple">Close temples</label>
                </li>
                <li>
                    <input style="width: 20px;" type="radio" name="achieve" value="Mid scalp coverage" id="<?= $form_identifier ?>achieve-scalp" />
                    <label for="<?= $form_identifier ?>achieve-scalp">Mid-scalp coverage</label>
                </li>
                <li>
                    <input style="width: 20px;" type="radio" name="achieve" value="Other" id="<?= $form_identifier ?>achieve-other" />
                    <label for="<?= $form_identifier ?>achieve-other">Other</label>
                </li>
            </ul>

        </li>

        <li>
            <p><label for="<?= $form_identifier ?>when">When would you like to have a procedure?</label></p>
            <input type="text" name="when" value="" id="<?= $form_identifier ?>when" />
        </li>

        <li>
            <p><label for="<?= $form_identifier ?>info">Please leave any extra information you think is important:</label></p>
            <textarea cols="60" rows="3" name="info" id="<?= $form_identifier ?>info"></textarea>
        </li>

        <?  /* Captcha
        <li>
            <label for="<?= $form_identifier ?>captcha">Captcha</label>
            <p>Enter the letters and numbers as they appear in the above image below:</p>
            <img src="<??>" width="150" height="50" style="border:0;" alt="" />
            <input type="text" name="captcha" id="<?= $form_identifier ?>captcha" value="" class="validate[required,custom[onlyLetterNumber],funcCall[checkConsultationFormCaptcha]] text-input" title="P" />
        </li>
        */ ?>

        <li>
            <div id="subscribetext">
                <input type="checkbox" name="subscribe" id="<?= $form_identifier ?>subscribe" value="Yes" />
                <label for="<?= $form_identifier ?>subscribe">Check to add your email address so we can contact you</label>
            </div>
        </li>

        <li>
            <input type="button" name="submit_cf" id="submit_cf" value="Send form" class="button" onclick="ValidateConsultationForm();" />
        </li>

    </ul>
</form>