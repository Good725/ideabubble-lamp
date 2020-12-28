Training: <?= @$form['training'] ?><br/>
Schedule: <?= @$form['schedule'] ?><br/><br/>
Course Name.: <?= @$form['course_name'] ?><br/>
Guardian details:<br/><br/>
Name: <?= @$form['guardian_first_name'] ?>&nbsp;<?= @$form['guardian_last_name'] ?><br/>
Address: <?= @$form['guardian_address1'] ?>, <?= @$form['guardian_address2'] ?>, <?= @$form['guardian_address3'] ?><br/>
City: <?= @$form['guardian_city'] ?><br/>
County: <?= @$form['guardian_county'] ?><br/>
Relationship to Student: <?= (@$form['guardian_relationship_to_student'] == 'other') ? @$form['guardian_relationship_to_student_other'] : @$form['guardian_relationship_to_student'] ?>
<br/>
Mobile: <?= @$form['guardian_mobile'] ?><br/>
Email: <?= @$form['guardian_email'] ?><br/>
Phone: <?= @$form['guardian_phone'] ?><br/>
Preferred contact methods: <?= (isset($form['guardian_preffered_sms'])) ? 'SMS,' : '' ?><?= (isset($form['guardian_preffered_email'])) ? 'Email,' : '' ?><?= (isset($form['guardian_preffered_phone'])) ? 'Phone' : '' ?><br />

Student details:<br/><br/>
Name: <?= @$form['student_first_name'] ?>&nbsp;<?= @$form['student_last_name'] ?><br/>
Address: <?= @$form['student_address1'] ?>, <?= @$form['student_address2'] ?>, <?= @$form['student_address3'] ?><br/>
City: <?= @$form['student_city'] ?><br/>
County: <?= @$form['student_county'] ?><br/>
Relationship to Student: <?= (@$form['student_relationship_to_student'] == 'other') ? @$form['student_relationship_to_student_other'] : @$form['student_relationship_to_student'] ?>
<br/>
Mobile: <?= @$form['student_mobile'] ?><br/>
Email: <?= @$form['student_email'] ?><br/>
Phone: <?= @$form['student_phone'] ?><br/>
Preferred contact methods: <?= (isset($form['student_preffered_sms'])) ? 'SMS,' : '' ?><?= (isset($form['student_preffered_email'])) ? 'Email,' : '' ?><?= (isset($form['student_preffered_phone'])) ? 'Phone' : '' ?><br />
