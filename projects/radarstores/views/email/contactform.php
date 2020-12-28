<div class="formrt">
    <form id="ContactForm" name="ContactForm" class="vertical" method="post" action="">
        <input type="hidden" value="Contact form" name="subject">
        <input type="hidden" value="Military and Camping" name="business_name">
        <input type="hidden" value="thank-you.html" name="redirect">
        <input type="hidden" value="post_contactForm" name="event">
        <input type="hidden" value="contact_us" name="trigger">
        <fieldset>
            <legend>CONTACT DETAILS</legend>
            <ul>
                <li>
                    <label for="contact_form_name">NAME</label>
                    <input type="text" title="Please enter your Name" class="validate[required,custom[onlyLetterSp],length[0,100]] text-input" value="" id="contact_form_name" name="contact_form_name">
                </li>
                <li>
                    <label for="contact_form_address">ADDRES</label>
                    <input id="contact_form_address" name="contact_form_address">
                </li>
                <li>
                    <label for="contact_form_tel">PHONE</label>
                    <input type="text" value="" id="contact_form_tel" name="contact_form_tel">
                </li>
                <li>
                    <label for="contact_form_email_address">EMAIL</label>
                    <input type="text" title="Please enter your Email" class="validate[required,custom[email]] text-input" value="" id="contact_form_email_address" name="contact_form_email_address">
                </li>
                <li>
                    <label for="contact_form_message">MESSAGE</label>
                    <textarea class="messagearea" id="contact_form_message" name="contact_form_message"></textarea>
                </li>
                <li>
                    <div onclick="x_Validate();" class="button" id="submit1"><h1>SUBMIT</h1></div>
                </li>
            </ul>
            <input type="hidden" value="Contact Form" id="form_type" name="form_type">
            <input type="hidden" value="contact_" name="form_identifier">
        </fieldset>
    </form>
</div>
<script type="text/javascript">
    function x_Validate(){
        if($('#contact_form_name').val() != "" && ($('#contact_form_tel').val() != "" || $('#contact_form_email_address').val() != "")){
            $('#ContactForm').attr('action', '<?=URL::site()?>frontend/formprocessor').submit();
        }
        else{
            alert('Please fill the Name\nPlease fill the email address or telephone number');
        }
    }
</script>