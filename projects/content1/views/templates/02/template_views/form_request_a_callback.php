<div id="req_callback">
    <fieldset>
        <legend>Request a callback</legend>
        <form method="post" action="<?= URL::site() ?>frontend/formprocessor">
            <div class="req_callback_lables">Your Name:<br/>Your Telephone Number:</div>
            <div class="req_callback_fields">
                <input type="text" name="callback_form_name" id="req_form_name"/><br/>
                <input type="text" name="callback_form_tel" id="req_form_telephone"/><br/>
                <input type="hidden" value="Request Callback" name="subject" />
                <input type="hidden" value="Request Callback" name="business_name" />
                <input type="hidden" value="contact-us-thank-you.html" name="redirect" />
                <input type="hidden" value="call_back_request" name="trigger" />
            </div>
            <br/><br/>
            <input type="submit" class="submit" value="Send"/>
        </form>
    </fieldset>
</div>