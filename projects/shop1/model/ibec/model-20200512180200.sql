/*
ts:2020-05-12 18:02:00
*/

-- Contact us

DELIMITER ;;

-- Insert the "contact-us" page, if it doesn't already exist
INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'contact-us',
  'Contact us',
  '<h1>Contact us</h1>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '1',
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('contact-us', 'contact-us.html') AND `deleted` = 0)
LIMIT 1;;

-- Update the "contact-us" page
UPDATE
  `plugin_pages_pages`
SET
  `name_tag`      = 'contact-us',
  `title`         = 'Contact us',
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = 1,
  `content`       = '<h2 style="margin-top: 1.125rem; margin-bottom: 1rem;">Contact our team</h2>

<div style="font-weight: normal;letter-spacing: -.025em">
<p style="margin-bottom: 18px;">We will be happy to answer any questions you have.</p>

<p style="margin-bottom: 18px;">Fill out the form and we will get back to you shortly.</p>
</div>

<div class="formrt formrt-vertical form-contact_us">{form-Contact Us}</div>

<div class="bg-lighter simplebox office-intro">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h1>Our office locations</h1>

				<p>Ibec has over 230 professional services staff in six locations around Ireland and in our office in Brussels.
				Check out our office locations here.</p>

				<p>During the current COVID-19 period, our offices will be physically closed whilst our staff work remotely. All
				our phone numbers remain the same and in operation. Don&#39;t forget to visit the website of your trade
				associations for information in relation to your industry.</p>
			</div>
		</div>
	</div>
</div>

<div class="bg-lighter simplebox simplebox-align-top simplebox-office">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Dublin</h2>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p>
					<strong>Ibec Head Office</strong><br />
					84/86 Lower Baggot Street<br />
					Dublin 2<br />
					D02 H720<br /><br />
					Tel: (01) 605 1645<br />
					<a href="mailto:training@ibec.ie">training@ibec.ie</a>
				</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d2382.4138049073204!2d-6.2500393!3d53.3358484!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x3fd2f08c04fdf6f9!2sIbec%20-%20Regional%20offices!5e0!3m2!1sen!2sie!4v1589979046509!5m2!1sen!2sie" height="200" frameborder="0" style="border:0; width: 100%" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top simplebox-office">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Galway</h2>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p>
					<strong>Ibec West</strong><br />
					Ross House<br />
					Victoria Place<br />
					Galway<br />
					H91 FPK5<br /><br />
					Tel: (091) 561 109
				</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2385.9292119662805!2d-9.05105304893994!3d53.272887979865445!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x485b96e59776b1a3%3A0x2ad60293bc423c0a!2sIbec%20-%20Western%20office!5e0!3m2!1sen!2sie!4v1589979329461!5m2!1sen!2sie" height="200" frameborder="0" style="border:0; width: 100%;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
			</div>
		</div>
	</div>
</div>

<div class="bg-lighter simplebox simplebox-align-top simplebox-office">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Cork</h2>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p>
					<strong>Ibec Cork</strong><br />
					Knockrea House<br />
					Douglas Road<br />
					Cork<br />
					T12 XR58<br /><br />
					Tel: (021) 429 5511
				</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2462.566142625178!2d-8.455838149003112!3d51.887131679599676!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x484485544ae2ee87%3A0xc93a1baa332cd511!2sIbec!5e0!3m2!1sen!2sie!4v1589979387604!5m2!1sen!2sie" height="200" frameborder="0" style="border:0; width: 100%;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top simplebox-office">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Waterford</h2>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p>
					<strong>Ibec South East</strong><br />
					Confederation House<br />
					Waterford Business Park<br />
					Cork Road<br />
					Waterford<br />
					X91 E9TV<br /><br />
					Tel: (051) 331 260
				</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2442.956559752171!2d-7.161266748986951!3d52.2441726796636!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4842c464623ea165%3A0xe2dc264d8436bff!2sIbec%20-%20South%20East%20regional%20office!5e0!3m2!1sen!2sie!4v1589979426305!5m2!1sen!2sie" height="200" frameborder="0" style="border:0; width: 100%;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
			</div>
		</div>
	</div>
</div>

<div class="bg-lighter simplebox simplebox-align-top simplebox-office">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Limerick</h2>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p>
					<strong>Ibec Mid-West</strong><br />
					Gardner House<br />
					Bank Place<br />
					Charlotte Quay<br />
					Limerick<br />
					V94 HT2Y<br /><br />
					Tel: (061) 410411
				</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2419.6545050857417!2d-8.623366048967714!3d52.66621777974318!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x485b5c684c66e1c3%3A0x48223f457d305917!2sIBEC%20(Mid%20West)!5e0!3m2!1sen!2sie!4v1589979474600!5m2!1sen!2sie" height="200" frameborder="0" style="border:0; width: 100%;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top simplebox-office">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Donegal</h2>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p>
					<strong>Ibec North West</strong><br />
					3rd Floor, Pier One<br />
					Quay Street<br />
					Donegal Town<br />
					Donegal<br />
					F94 KN96<br />
					Tel: (074) 972 4280
				</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2308.2783127362723!2d-8.113908348875869!3d54.651925280176464!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x485fa83143470f5b%3A0x686c9691fadebcc7!2sIbec%20-%20North%20West%20regional%20office!5e0!3m2!1sen!2sie!4v1589979522962!5m2!1sen!2sie" height="200" frameborder="0" style="border:0; width: 100%;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
			</div>
		</div>
	</div>
</div>
  '
WHERE
  `name_tag` IN ('contact-us', 'contact-us.html');;


-- Update the form
UPDATE
  `plugin_formbuilder_forms`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `fields` = '<input type=\"hidden\" name=\"subject\" value=\"Contact form\">
\n<input type=\"hidden\" name=\"business_name\" value=\"\">
\n<input type=\"hidden\" name=\"redirect\" value=\"thank-you.html\">
\n<input type=\"hidden\" name=\"event\" value=\"contact-form\">
\n<input type=\"hidden\" name=\"trigger\" value=\"custom_form\">
\n<input type=\"hidden\" name=\"form_type\" value=\"Contact Form\">
\n<input type=\"hidden\" name=\"form_identifier\" value=\"contact_\">
\n<input type=\"hidden\" name=\"email_template\" value=\"contactformmail\">
\n<li class="contact_form-li\-\-first_name">
\n    <label class=\"sr-only\" for=\"contact_form_first_name\">First name*</label>
\n    <input type=\"text\" name=\"contact_form_first_name\" id=\"contact_form_name\" class=\"validate[required]\" placeholder=\"First name*\">
\n</li>
\n<li class="contact_form-li\-\-last_name">
\n    <label class=\"sr-only\" for=\"contact_form_last_name\">Last name*</label>
\n    <input type=\"text\" name=\"contact_form_last_name\" id=\"contact_form_last_name\" style=\"width:px;\" class=\"validate[required]\" placeholder=\"Last name*\">
\n</li>
\n<li>
\n    <label class=\"sr-only\" for=\"contact_form_email_address\">Email*</label>
\n    <input type=\"email\" class=\"validate[required,custom[email]]\" name=\"contact_form_email_address\" id=\"contact_form_email_address\" placeholder=\"Email*\">
\n</li>
\n<li class=\"pb-0\">
\n    <label for=\"contact_form_mobile\">Mobile Number*</label>
\n</li>
\n<li class="contact_form-li\-\-country_code">
\n    <label class=\"sr-only\" for=\"contact_form_mobile\">Country code</label>
\n    <label class="\form-select\">
\n        <select name=\"contact_form_mobile_country_code\" id=\"contact_form_mobile_country_code\" class=\"validate[required]\">
\n            <option value=\"\">Country*</option>
\n            <option value=\"353\">+353 Ireland</option>
\n            <option value=\"44\">+44 United Kingdom</option>
\n            <option value=\"93\">+93 Afghanistan</option>
\n            <option value=\"355\">+355 Albania</option>
\n            <option value=\"213\">+213 Algeria</option>
\n            <option value=\"1684\">+1684 American Samoa</option>
\n            <option value=\"376\">+376 Andorra</option>
\n            <option value=\"244\">+244 Angola</option>
\n            <option value=\"1264\">+1264 Anguilla</option>
\n            <option value=\"672\">+672 Antarctica</option>
\n            <option value=\"1268\">+1268 Antigua And Barbuda</option>
\n            <option value=\"54\">+54 Argentina</option>
\n            <option value=\"374\">+374 Armenia</option>
\n            <option value=\"297\">+297 Aruba</option>
\n            <option value=\"61\">+61 Cocos (keeling) Islands</option>
\n            <option value=\"43\">+43 Austria</option>
\n            <option value=\"994\">+994 Azerbaijan</option>
\n            <option value=\"1242\">+1242 Bahamas</option>
\n            <option value=\"973\">+973 Bahrain</option>
\n            <option value=\"880\">+880 Bangladesh</option>
\n            <option value=\"1246\">+1246 Barbados</option>
\n            <option value=\"375\">+375 Belarus</option>
\n            <option value=\"32\">+32 Belgium</option>
\n            <option value=\"501\">+501 Belize</option>
\n            <option value=\"229\">+229 Benin</option>
\n            <option value=\"1441\">+1441 Bermuda</option>
\n            <option value=\"975\">+975 Bhutan</option>
\n            <option value=\"591\">+591 Bolivia</option>
\n            <option value=\"387\">+387 Bosnia And Herzegovina</option>
\n            <option value=\"267\">+267 Botswana</option>
\n            <option value=\"55\">+55 Brazil</option>
\n            <option value=\"673\">+673 Brunei Darussalam</option>
\n            <option value=\"359\">+359 Bulgaria</option>
\n            <option value=\"226\">+226 Burkina Faso</option>
\n            <option value=\"257\">+257 Burundi</option>
\n            <option value=\"855\">+855 Cambodia</option>
\n            <option value=\"237\">+237 Cameroon</option>
\n            <option value=\"1\">+1 United States</option>
\n            <option value=\"238\">+238 Cape Verde</option>
\n            <option value=\"1345\">+1345 Cayman Islands</option>
\n            <option value=\"236\">+236 Central African Republic</option>
\n            <option value=\"235\">+235 Chad</option>
\n            <option value=\"56\">+56 Chile</option>
\n            <option value=\"86\">+86 China</option>
\n            <option value=\"57\">+57 Colombia</option>
\n            <option value=\"269\">+269 Comoros</option>
\n            <option value=\"242\">+242 Congo</option>
\n            <option value=\"243\">+243 Congo, The Democratic Republic Of The</option>
\n            <option value=\"682\">+682 Cook Islands</option>
\n            <option value=\"506\">+506 Costa Rica</option>
\n            <option value=\"225\">+225 Cote D Ivoire</option>
\n            <option value=\"385\">+385 Croatia</option>
\n            <option value=\"53\">+53 Cuba</option>
\n            <option value=\"357\">+357 Cyprus</option>
\n            <option value=\"420\">+420 Czech Republic</option>
\n            <option value=\"45\">+45 Denmark</option>
\n            <option value=\"253\">+253 Djibouti</option>
\n            <option value=\"1767\">+1767 Dominica</option>
\n            <option value=\"1809\">+1809 Dominican Republic</option>
\n            <option value=\"593\">+593 Ecuador</option>
\n            <option value=\"20\">+20 Egypt</option>
\n            <option value=\"503\">+503 El Salvador</option>
\n            <option value=\"240\">+240 Equatorial Guinea</option>
\n            <option value=\"291\">+291 Eritrea</option>
\n            <option value=\"372\">+372 Estonia</option>
\n            <option value=\"251\">+251 Ethiopia</option>
\n            <option value=\"500\">+500 Falkland Islands (malvinas)</option>
\n            <option value=\"298\">+298 Faroe Islands</option>
\n            <option value=\"679\">+679 Fiji</option>
\n            <option value=\"358\">+358 Finland</option>
\n            <option value=\"33\">+33 France</option>
\n            <option value=\"689\">+689 French Polynesia</option>
\n            <option value=\"241\">+241 Gabon</option>
\n            <option value=\"220\">+220 Gambia</option>
\n            <option value=\"995\">+995 Georgia</option>
\n            <option value=\"49\">+49 Germany</option>
\n            <option value=\"233\">+233 Ghana</option>
\n            <option value=\"350\">+350 Gibraltar</option>
\n            <option value=\"30\">+30 Greece</option>
\n            <option value=\"299\">+299 Greenland</option>
\n            <option value=\"1473\">+1473 Grenada</option>
\n            <option value=\"1671\">+1671 Guam</option>
\n            <option value=\"502\">+502 Guatemala</option>
\n            <option value=\"224\">+224 Guinea</option>
\n            <option value=\"245\">+245 Guinea-bissau</option>
\n            <option value=\"592\">+592 Guyana</option>
\n            <option value=\"509\">+509 Haiti</option>
\n            <option value=\"39\">+39 Italy</option>
\n            <option value=\"504\">+504 Honduras</option>
\n            <option value=\"852\">+852 Hong Kong</option>
\n            <option value=\"36\">+36 Hungary</option>
\n            <option value=\"354\">+354 Iceland</option>
\n            <option value=\"91\">+91 India</option>
\n            <option value=\"62\">+62 Indonesia</option>
\n            <option value=\"98\">+98 Iran, Islamic Republic Of</option>
\n            <option value=\"964\">+964 Iraq</option>
\n            <option value=\"972\">+972 Israel</option>
\n            <option value=\"1876\">+1876 Jamaica</option>
\n            <option value=\"81\">+81 Japan</option>
\n            <option value=\"962\">+962 Jordan</option>
\n            <option value=\"7\">+7 Russian Federation</option>
\n            <option value=\"254\">+254 Kenya</option>
\n            <option value=\"686\">+686 Kiribati</option>
\n            <option value=\"850\">+850 Korea Democratic Peoples Republic Of</option>
\n            <option value=\"82\">+82 Korea Republic Of</option>
\n            <option value=\"965\">+965 Kuwait</option>
\n            <option value=\"996\">+996 Kyrgyzstan</option>
\n            <option value=\"856\">+856 Lao Peoples Democratic Republic</option>
\n            <option value=\"371\">+371 Latvia</option>
\n            <option value=\"961\">+961 Lebanon</option>
\n            <option value=\"266\">+266 Lesotho</option>
\n            <option value=\"231\">+231 Liberia</option>
\n            <option value=\"218\">+218 Libyan Arab Jamahiriya</option>
\n            <option value=\"423\">+423 Liechtenstein</option>
\n            <option value=\"370\">+370 Lithuania</option>
\n            <option value=\"352\">+352 Luxembourg</option>
\n            <option value=\"853\">+853 Macau</option>
\n            <option value=\"389\">+389 Macedonia, The Former Yugoslav Republic Of</option>
\n            <option value=\"261\">+261 Madagascar</option>
\n            <option value=\"265\">+265 Malawi</option>
\n            <option value=\"60\">+60 Malaysia</option>
\n            <option value=\"960\">+960 Maldives</option>
\n            <option value=\"223\">+223 Mali</option>
\n            <option value=\"356\">+356 Malta</option>
\n            <option value=\"692\">+692 Marshall Islands</option>
\n            <option value=\"222\">+222 Mauritania</option>
\n            <option value=\"230\">+230 Mauritius</option>
\n            <option value=\"262\">+262 Mayotte</option>
\n            <option value=\"52\">+52 Mexico</option>
\n            <option value=\"691\">+691 Micronesia, Federated States Of</option>
\n            <option value=\"373\">+373 Moldova, Republic Of</option>
\n            <option value=\"377\">+377 Monaco</option>
\n            <option value=\"976\">+976 Mongolia</option>
\n            <option value=\"382\">+382 Montenegro</option>
\n            <option value=\"1664\">+1664 Montserrat</option>
\n            <option value=\"212\">+212 Morocco</option>
\n            <option value=\"258\">+258 Mozambique</option>
\n            <option value=\"95\">+95 Myanmar</option>
\n            <option value=\"264\">+264 Namibia</option>
\n            <option value=\"674\">+674 Nauru</option>
\n            <option value=\"977\">+977 Nepal</option>
\n            <option value=\"31\">+31 Netherlands</option>
\n            <option value=\"599\">+599 Netherlands Antilles</option>
\n            <option value=\"687\">+687 New Caledonia</option>
\n            <option value=\"64\">+64 New Zealand</option>
\n            <option value=\"505\">+505 Nicaragua</option>
\n            <option value=\"227\">+227 Niger</option>
\n            <option value=\"234\">+234 Nigeria</option>
\n            <option value=\"683\">+683 Niue</option>
\n            <option value=\"1670\">+1670 Northern Mariana Islands</option>
\n            <option value=\"47\">+47 Norway</option>
\n            <option value=\"968\">+968 Oman</option>
\n            <option value=\"92\">+92 Pakistan</option>
\n            <option value=\"680\">+680 Palau</option>
\n            <option value=\"507\">+507 Panama</option>
\n            <option value=\"675\">+675 Papua New Guinea</option>
\n            <option value=\"595\">+595 Paraguay</option>
\n            <option value=\"51\">+51 Peru</option>
\n            <option value=\"63\">+63 Philippines</option>
\n            <option value=\"870\">+870 Pitcairn</option>
\n            <option value=\"48\">+48 Poland</option>
\n            <option value=\"351\">+351 Portugal</option>
\n            <option value=\"974\">+974 Qatar</option>
\n            <option value=\"40\">+40 Romania</option>
\n            <option value=\"250\">+250 Rwanda</option>
\n            <option value=\"590\">+590 Saint Barthelemy</option>
\n            <option value=\"290\">+290 Saint Helena</option>
\n            <option value=\"1869\">+1869 Saint Kitts And Nevis</option>
\n            <option value=\"1758\">+1758 Saint Lucia</option>
\n            <option value=\"1599\">+1599 Saint Martin</option>
\n            <option value=\"508\">+508 Saint Pierre And Miquelon</option>
\n            <option value=\"1784\">+1784 Saint Vincent And The Grenadines</option>
\n            <option value=\"685\">+685 Samoa</option>
\n            <option value=\"378\">+378 San Marino</option>
\n            <option value=\"239\">+239 Sao Tome And Principe</option>
\n            <option value=\"966\">+966 Saudi Arabia</option>
\n            <option value=\"221\">+221 Senegal</option>
\n            <option value=\"381\">+381 Serbia</option>
\n            <option value=\"248\">+248 Seychelles</option>
\n            <option value=\"232\">+232 Sierra Leone</option>
\n            <option value=\"65\">+65 Singapore</option>
\n            <option value=\"421\">+421 Slovakia</option>
\n            <option value=\"386\">+386 Slovenia</option>
\n            <option value=\"677\">+677 Solomon Islands</option>
\n            <option value=\"252\">+252 Somalia</option>
\n            <option value=\"27\">+27 South Africa</option>
\n            <option value=\"34\">+34 Spain</option>
\n            <option value=\"94\">+94 Sri Lanka</option>
\n            <option value=\"249\">+249 Sudan</option>
\n            <option value=\"597\">+597 Suriname</option>
\n            <option value=\"268\">+268 Swaziland</option>
\n            <option value=\"46\">+46 Sweden</option>
\n            <option value=\"41\">+41 Switzerland</option>
\n            <option value=\"963\">+963 Syrian Arab Republic</option>
\n            <option value=\"886\">+886 Taiwan, Province Of China</option>
\n            <option value=\"992\">+992 Tajikistan</option>
\n            <option value=\"255\">+255 Tanzania, United Republic Of</option>
\n            <option value=\"66\">+66 Thailand</option>
\n            <option value=\"670\">+670 Timor-leste</option>
\n            <option value=\"228\">+228 Togo</option>
\n            <option value=\"690\">+690 Tokelau</option>
\n            <option value=\"676\">+676 Tonga</option>
\n            <option value=\"1868\">+1868 Trinidad And Tobago</option>
\n            <option value=\"216\">+216 Tunisia</option>
\n            <option value=\"90\">+90 Turkey</option>
\n            <option value=\"993\">+993 Turkmenistan</option>
\n            <option value=\"1649\">+1649 Turks And Caicos Islands</option>
\n            <option value=\"688\">+688 Tuvalu</option>
\n            <option value=\"256\">+256 Uganda</option>
\n            <option value=\"380\">+380 Ukraine</option>
\n            <option value=\"971\">+971 United Arab Emirates</option>
\n            <option value=\"598\">+598 Uruguay</option>
\n            <option value=\"998\">+998 Uzbekistan</option>
\n            <option value=\"678\">+678 Vanuatu</option>
\n            <option value=\"58\">+58 Venezuela</option>
\n            <option value=\"84\">+84 Viet Nam</option>
\n            <option value=\"1284\">+1284 Virgin Islands, British</option>
\n            <option value=\"1340\">+1340 Virgin Islands, U.s.</option>
\n            <option value=\"681\">+681 Wallis And Futuna</option>
\n            <option value=\"967\">+967 Yemen</option>
\n            <option value=\"260\">+260 Zambia</option>
\n            <option value=\"263\">+263 Zimbabwe</option>
\n        </select>
\n    </label>
\n</li>
\n<li class="contact_form-li\-\-mobile_code">
\n    <label class=\"sr-only\" for=\"contact_form_mobile_code\">Mobile code</label>
\n    <input type=\"text\" name=\"contact_form_mobile_code\" id=\"contact_form_mobile_code\" class=\"validate[required]\" placeholder=\"Code*\">
\n</li>
\n<li class="contact_form-li\-\-mobile_number">
\n    <label for=\"contact_form_mobile\" class=\"sr-only\">Mobile number</label>
\n    <input type=\"text\" name=\"contact_form_mobile\" id=\"contact_form_mobile\" class=\"validate[required]\" placeholder=\"Number*\">
\n</li>
\n<li>
\n    <label for=\"contact_form_message\">Message*</label>
\n    <textarea name=\"contact_form_message\" class=\"validate[required]\" id=\"contact_form_message\"></textarea>
\n</li>
\n<li>
\n    <label for=\"subscribe\" style=\"display: inline-block; float: none; width: calc(100% - 3rem);\">Tick this box to let us get in touch with you*</label>
\n    <label class=\"form-checkbox\" style=\"float: left; margin-right: .8rem;\">
\n        <input type=\"checkbox\" class=\"validate[required]\" id=\"subscribe\" name=\"contact_form_add_to_list\" />
\n        <span class=\"form-checkbox-helper\"></span>
\n    </label>
\n</li>
\n<li>
\n    <label for=\"undefined\"></label><span>[CAPTCHA]</span>
\n</li>
\n<li>
\n    <!\-\- formnovalidate disables the HTML5 validation, so that the JS validation can be used instead. \-\->
\n    <label for=\"\"></label><input type=\"submit\" formnovalidate="formnovalidate">
\n</li>'
WHERE
  `form_name` = 'Contact Us'
;;

