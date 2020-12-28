<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dale
 * Date: 17/01/2014
 * Time: 12:59
 * To change this template use File | Settings | File Templates.
 */
$disabled = (isset($_GET['product'])) ? $_GET['product']:'disabled="disabled"';
?>
<form id="enquiry_form" method="post" class="formrt enquiry_form" action="<?=URL::site();?>/frontend/formprocessor">
    <input type="hidden" name="subject" value="Enquiry Form"/>
    <input type="hidden" name="redirect" value="thank-you.html"/>
    <input type="hidden" name="event" value="post_enquiryForm"/>
    <input type="hidden" name="trigger" value="enquiry_form"/>

	<ul>
		<li>
			<label for="enquiry_name">Your name:</label>
			<input type="text" name="name" id="enquiry_name" class="validate[required]" placeholder="Your name..."/>
		</li>

		<li>
			<label for="enquiry_phone">Your phone:</label>
			<input type="text" name="phone" id="enquiry_phone" class="validate[required]" placeholder="Your phone number..."/>
		</li>

		<li>
			<label for="enquiry_email">Your email:</label>
			<input type="text" name="email" id="enquiry_email" class="validate[required]" placeholder="Your email address..."/>
		</li>

		<li>
			<label for="enquiry_enquiry">Enquiry for:</label>
			<input type="text" value="<?=(isset($_GET['product']))?$_GET['product']:'';?>" name="enquiry" id="enquiry_enquiry" readonly="readonly"/>
		</li>

		<li>
			<label for="enquiry_message">Message:</label>
			<textarea rows="5" name="message" id="enquiry_message" class="validate[required]" cols="40" placeholder="Your message..."></textarea>
		</li>

		<li>
			<input type="submit" <?=$disabled;?> value="Enquire Now" class="primary_button" />
		</li>
	</ul>
</form>