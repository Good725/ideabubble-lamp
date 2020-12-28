/*
ts:2020-05-27 16:53:00
*/
DELIMITER ;;

UPDATE
    `plugin_messaging_notification_templates`
SET
    `date_updated` = CURRENT_TIMESTAMP,
    `usable_parameters_in_template` = '$end_date, $name, $primary_color, $project_name, $registration_link, $schedule_name, $site_link',
    `message` = '<p>Hello, $name.</p>
\n
\n  <p>You are receiving this email because you are attending the <b>$schedule_name</b> schedule, which was booked with <a href="$site_link">$project_name</a>.</p>
\n
\n  <p>As that course is nearing its end, <b>$end_date</b>, we would like to invite you to apply for accreditation from TU Dublin. You can do so using the form at the link below.</p>
\n
\n  <p>Regards</p>
\n
\n  <p style="text-align: center;"><a href="$registration_link" style="background: #0074cc;
        background: $primary_color;
        border-radius: 3px;
        color: #fff;
        display: inline-block;
        min-width: 4em;
        padding: .75em 1.5em;
        text-align: center;
        text-decoration: none;
    ">Register for accreditation</a></p>
  '
WHERE
        `name` = 'course_accreditation_application';;

UPDATE
    `plugin_formbuilder_forms`
SET
    `action` = '/frontend/bookings/submit_application/',
    `class` = 'bookings_form',
    `fields` = '
<input type=\"hidden\" name=\"schedule_id\" id=\"accreditation-schedule_id\" />
<input type=\"hidden\" name=\"contact_id\"  id=\"accreditation-contact_id\"  />
<input type=\"hidden\" name=\"booking_id\"  id=\"accreditation-booking_id\"  />
<div class=\"form-group\">
  <div class=\"col-sm-12\">
        <label class=\"form-input form-input\-\-text form-input\-\-pseudo\" for=\"accreditation-first_name\">
            <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">First name</span>
            <input type=\"text\" name=\"first_name\" placeholder=\"First name: *\" class=\"validate[required]\" id=\"accreditation-first_name\"/>
        </label>
  </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class=\"form-input form-input\-\-text form-input\-\-pseudo\" for=\"accreditation-last_name\">
            <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">Last name</span>
            <input type=\"text\" name=\"last_name\" placeholder=\"Last name: *\"class=\"validate[required]\" id=\"accreditation-last_name\">
        </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <span class=\"form-datepicker-wrapper\">
            <label class=\"form-input form-input\-\-text form-input\-\-pseudo\" for=\"accreditation-dob\">
                <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">Date of Birth</span>
                <input type=\"text\" autocomplete=\"off\" class=\"validate[required] dob form-datepicker\" name=\"dob\" placeholder=\"Date of birth: *\" class=\"validate[required]\" id=\"accreditation-dob\" data-date_format=\"d/m/Y\">
            </label>
    </span>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class="form-select" for=\"accreditation-country\">
            <span class=\"form-input form-input\-\-select form-input\-\-pseudo\">
                <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">Gender</span>
                <select name=\"gender\" class=\"validate[required]\" id=\"accreditation-gender\">
                    <option value=\"\"></option>
                    <option value=\"Male\">Male</option>
                    <option value=\"Female\">Female</option>
                    <option value=\"None\">None</option>
                </select>
            </span>
        </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class="form-select" for=\"accreditation-nationality\">
            <span class=\"form-input form-input\-\-select form-input\-\-pseudo\">
                <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">Nationality</span>
                <select class=\"validate[required]\" id=\"accreditation-nationality\" placeholder=\"Nationality: *\" name=\"nationality\"></select>
            </span>
        </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class=\"form-input form-input\-\-text form-input\-\-pseudo\" for=\"accreditation-pps\">
            <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">PPS</span>
            <input type=\"text\" name=\"pps\" class=\"validate[funcCall[validate_pps]]\" placeholder=\"PPS: \" id=\"accreditation-pps\">
        </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class=\"form-input form-input\-\-text form-input\-\-pseudo\" for=\"accreditation-address1\">
            <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">Address 1</span>
            <input type=\"text\" name=\"address1\" placeholder="Address 1: *" class=\"validate[required]\" id=\"accreditation-address1\">
        </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class=\"form-input form-input\-\-text form-input\-\-pseudo\" for=\"accreditation-address2\">
            <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">Address 2</span>
            <input type=\"text\" name=\"address2\" placeholder="Address 2: *" class=\"validate[required]\" id=\"accreditation-address2\">
        </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class=\"form-input form-input\-\-text form-input\-\-pseudo\" for=\"accreditation-address3\">
            <span class=\"form-input\-\-pseudo-label\">Address 3</span>
            <input type=\"text\" name=\"address3\" placeholder="Address 3: " id=\"accreditation-address3\">
        </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class=\"form-input form-input\-\-text form-input\-\-pseudo\" for=\"accreditation-city\">
            <span class=\"form-input\-\-pseudo-label\">City</span>
            <input type=\"text\" name=\"city\" placeholder="City: " id=\"accreditation-city\"/>
        </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class="form-select" for=\"accreditation-county\">
            <span class=\"form-input form-input\-\-select form-input\-\-pseudo\">
                <span class=\"form-input\-\-pseudo-label\">County</span>
                <select type=\"text\" id=\"accreditation-county\" name=\"county\"></select>
            </span>
        </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class="form-select" for=\"accreditation-country\">
            <span class=\"form-input form-input\-\-select form-input\-\-pseudo\">
                <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">Country</span>
                <select type=\"text\" class=\"validate[required]\" id=\"accreditation-country\" name=\"country\"></select>
            </span>
        </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class=\"form-input form-input\-\-text form-input\-\-pseudo\" for=\"accreditation-phone\">
            <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">Phone</span>
            <input type=\"text\" name=\"phone\" class=\"validate[required]\" placeholder=\"Phone: *\" id=\"accreditation-phone\">
        </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class=\"form-input form-input\-\-text form-input\-\-pseudo\" for=\"accreditation-email\">
            <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">Email</span>
            <input type=\"text\" name=\"email\" placeholder=\"Email: *\" class=\"validate[required,custom[email]]\" id=\"accreditation-email\">
         </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class="form-select" for=\"accreditation-country\">
            <span class=\"form-input form-input\-\-select form-input\-\-pseudo\">
                <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">Programme</span>
                    <select name=\"schedule_name\" class=\"validate[required]\" id=\"accreditation-schedule\">
                        <option value=\"\"></option>
                        <option value=\"DT632 - CPD Diploma in Project Management\">DT632 - CPD Diploma in Project Management</option>
                        <option value=\"DT605 - Diploma in Management\">DT605 - Diploma in Management</option>
                        <option value=\"DT608 - Diploma in Human Resources Management\">DT608 - Diploma in Human Resources Management</option>
                        <option value=\"DT610 - Cert in Managing People\">DT610 - Cert in Managing People</option>
                        <option value=\"DT610A - Cert in Managing People Skills\">DT610A - Cert in Managing People Skills</option>
                        <option value=\"DT611 - Cert in Training and Development\">DT611 - Cert in Training and Development</option>
                        <option value=\"DT6001 - Cert in Employment Law\">DT6001 - Cert in Employment Law</option>
                        <option value=\"DT6002 - Diploma in Employment Law\">DT6002 - Diploma in Employment Law</option>
                        <option value=\"DT6003 - Cert in Industrial Relations\">DT6003 - Cert in Industrial Relations</option>
                        <option value=\"DT6004 - Diploma in Industrial Relations\">DT6004 - Diploma in Industrial Relations</option>
                        <option value=\"DT6007 - Cert in Occupational Health & Safety\">DT6007 - Cert in Occupational Health & Safety</option>
                        <option value=\"DT6013 - Cert in Human Resources\">DT6013 - Cert in Human Resources</option>
                        <option value=\"DT6017 - Cert in Employability\">DT6017 - Cert in Employability</option>
                        <option value=\"DT6018 - Diploma in Employability\">DT6018 - Diploma in Employability</option>
                        <option value=\"DT6020 - Diploma in Mediation\">DT6020 - Diploma in Mediation</option>
                        <option value=\"DT7001 - Diploma in Occupational Health & Safety\">DT7001 - Diploma in Occupational Health & Safety</option>
                        <option value=\"CPD BN02 32628 - Cert in Managing Safety\">CPD BN02 32628 - Cert in Managing Safety</option>
                        <option value=\"27672 - Cert in Employee Relations\">27672 - Cert in Employee Relations</option>
                        <option value=\"CPD BN02 32627 - Diploma in Professional Competence\">CPD BN02 32627 - Diploma in Professional Competence</option>
                        <option value=\"27919 - Cert in Professional Competence\">27919 - Cert in Professional Competence</option>
                        <option value=\"CPD BN02 32626 - Diploma in Leadership\">CPD BN02 32626 - Diploma in Leadership</option>
                        <option value=\"33388 - CPD Diploma in Coaching\">33388 - CPD Diploma in Coaching</option>
                        <option value=\"DT631 - CPD Certificate in Lean Sigma Six\">DT631 - CPD Certificate in Lean Sigma Six</option>
                    </select>
             </span>
        </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class=\"form-input form-input\-\-textarea form-input\-\-pseudo\" for=\"accreditation-highest_qualification\">
            <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">Details of highest qualifications obtained</span>
            <textarea  name=\"highest_qualification\" class=\"w-100 validate[required]\" placeholder=\"Details of highest qualifications obtained\" id=\"accreditation-highest_qualification\"></textarea>
        </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class=\"form-input form-input\-\-textarea form-input\-\-pseudo\" for=\"accreditation-employment_history\">
            <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">Please provide brief details of employment history</span>
            <textarea name=\"employment_history\" class=\"w-100 validate[required]\" placeholder=\"Please provide brief details of employment history\" id=\"accreditation-employment_history\"></textarea>
        </label>
    </div>
</div>
<div class=\"form-group\">
    <div class=\"col-sm-12\">
        <label class=\"form-input form-input\-\-textarea form-input\-\-pseudo\" for=\"accreditation-other_information\">
            <span class=\"form-input\-\-pseudo-label label\-\-mandatory\">Please provide details of any other relevant information/qualifications/work experience that may be relevant</span>
            <textarea name=\"other_information\" class=\"w-100 validate[required]\" placeholder=\"Please provide details of any other relevant information/qualifications/work experience that may be relevant\" id=\"accreditation-other_information\"></textarea>
        </label>
    </div>
</div>
<div class=\"form-group\">
    <label class=\"form-checkbox\" for=\"accreditation-declaration\">
        <input type=\"checkbox\" name=\"declaration\" class=\"m-2 validate[required]\" id=\"accreditation-declaration\">
        <span class=\"form-checkbox-helper\"></span>
        <span class=\"form-checkbox-label label\-\-mandatory\">I declare the information given by me on this form is true and accurate.</span>
    </label>
</div>
<div class=\"form-group\">
    <label for=\"accreditation-submit\"></label>
    <button class=\"button button-action\" id=\"accreditation-submit\">Submit</button>
</div>'
WHERE
        `form_id` = 'accreditation_application';;

UPDATE plugin_formbuilder_forms SET `action`='frontend/bookings/submit_application' WHERE `form_id`='accreditation_application';;

UPDATE
    `plugin_pages_pages`
SET `publish` = 1 WHERE
 `name_tag` = 'application-thank-you';;

UPDATE `plugin_pages_pages`
SET `content` = '<h1>Accreditation application</h1>\n\n<div class="form-contact_us">{form-accreditation_application}</div>'
WHERE `name_tag` = 'accreditation-application';;

