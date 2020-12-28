<div id="request_callback_form">
    <form action="<?=URL::site()?>frontend/formprocessor/" method="post">
        <input type="hidden" name="trigger" value="call_back_request">
        <input type="hidden" name="event" value="request_callback">
        <input type="hidden" name="redirect" value="thank-you.html">
        <input type="hidden" name="subject" value="Request a callback">
        <ul>
            <li>
                <label for="callback_form_name">Name:</label><input type="text" name="callback_form_name" id="callback_form_name"/>
            </li>
            <li>
                <label for="callback_form_tel">Telephone Number:</label><input type="text" name="callback_form_tel" id="callback_form_tel"/>
            </li>
            <li>
                <input type="submit" value="submit" class="button"/>
            </li>
        </ul>
    </form>
</div>