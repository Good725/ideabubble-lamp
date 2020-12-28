<div id="request_callback_header">
    <h3>REQUEST CALL BACK</h3>
</div>
<div id="request_callback_content">
    <div id="request_callback_form">
        <form action="<?= URL::Site() ?>/frontend/formprocessor" method="post">
            <input type="hidden" value="Request a callback" name="subject">
            <input type="hidden" value="Request a callback - Century Life &amp; Pensions" name="business_name">
            <input type="hidden" value="request-callback-thank-you.html" name="redirect">
            <input type="hidden" value="call_back_request" name="trigger">
            <br/>
            NAME:
            <input type="text" name="req_form_name" id="req_form_name" class="width-191 validate[required]"/>
            <br/><br/>
            PHONE:
            <input type="text" name="req_form_phone" id="req_form_name" class="width-191 validate[required]"/>
            <br/><br/>
            <input type="submit" class="submit" id="submit" value=""/>
        </form>
    </div>
</div>