Course: <?=@$data['course_code'] . ' - ' . Model_Courses::get_course_title_from_code($data['course_code'])?><br />
<br />
<b>Student</b><br />
Name: <?=@$data['student_first_name'] . ' ' . $data['student_last_name']?><br />
Email: <?=@$data['student_email']?><br />
Year: <?=@$data['student_year_id']?><br />
Mobile: <?=@$data['student_mobile_country_code'].' '.@$data['student_mobile_code'] . ' ' . $data['student_mobile_number']?><br />
<br />
<b>Next Of Kin</b><br />
Name: <?=@$data['first_name'] . ' ' . $data['last_name']?><br />
Address 1: <?=@$data['address1']?><br />
Address 2: <?=@$data['address2']?><br />
City: <?=@$data['town']?><br />
Postal Code: <?=@$data['postcode']?><br />
Email: <?=@$data['email']?><br />
Mobile: <?=@$data['mobile_country_code'].' '.@$data['mobile_code'] . ' ' . $data['mobile_number']?><br />
Relationship to student: <?=@$data['relationship_to_student']?><br />
<br />
<b>Student Profile and Address</b><br />
Date Of Birth: <?=@$data['student']['dob']?><br />
Gender: <?=@$data['student']['gender']?><br />
Nationality: <?=@$data['student']['nationality_id']?><br />
Country Of Birth: <?=@$data['student']['birth_country_id']?><br />
PPS: <?=@$data['student']['pps']?><br />
<?php if (is_array(@$data['student']['preferences_medical'])) { ?>
Medical Conditions: <?=implode(', ', @$data['student']['preferences_medical'])?><br />
<?php } ?>
Address 1: <?=@$data['student']['address1']?><br />
Address 2: <?=@$data['student']['address2']?><br />
City: <?=@$data['student']['town']?><br />
Postal Code: <?=@$data['student']['postcode']?><br />
Country: <?=@$data['student']['country']?><br />
<br />
<b>Education</b><br />
Current School: <?=@$data['current_school'] ?><br />
School roll Number: <?=@$data['school_roll_number'] ?><br />
Leaving Cert: <?=@$data['leaving_cert_type']?><br />
Year: <?=@$data['year']?><br />
Subjects:
<?php if (@$data['subjects']){
foreach ($data['subjects'] as $subject) { ?>
<?=$subject['name'] . ' - ' . $subject['level'] . ' - ' . $subject['grade']?>
<?php } ?>
<?php } ?>
<br />
<br />
<b>College and career details</b>
Last college attended: <?=@$data['last_college_attended']?><br />
Course taken: <?=@$data['college_course_taken']?><br />
Year of entry: <?=@$data['college_entry_year']?><br />
Year of leaving: <?=@$data['college_leaving_year']?><br />
<br />
<b>Work Experience</b><br />
Employment Status: <?=@$data['employment_status']?><br />
<?php if (@$data['work_experience']) { ?>
<?php foreach ($data['work_experience'] as $wexp) { ?>
Year: <?=$wexp['year'] . ' - ' . $wexp['details']?><br />
<?php } ?>
<?php } ?>
<br />
<b>Other</b><br />
Other Certificates: <?=@$data['certificates_other']?><br />
Leisure activities: <?=@$data['leisure_activities']?><br />
Other relevant information: <?=@$data['application']['other']?><br />
Special Needs: <?=@$data['has_special_needs'] ? 'Yes' : 'No'?><br />
<?=@$data['special_needs_details']?><br />
<br />




