<?php $post = $form; ?>

<div class="booking-form-message">
<h2>Student Details</h2>
<table>
	<tbody>
		<?php if ((isset($post['name']) AND $post['name']) OR (isset($post['surname']) AND $post['surname'])): ?>
			<tr>
				<th scope="row">Name</th>
				<td><?= trim((isset($post['name'])?$post['name']:'').' '.(isset($post['surname'])?$post['surname']:'')) ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['dob']) AND $post['dob']): ?>
			<tr>
				<th scope="row">Date of birth</th>
				<td><?= $post['dob'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['gender']) AND $post['gender']): ?>
			<tr>
				<th scope="row">Gender</th>
				<td><?= ucfirst($post['gender']) ?></td>
			</tr>
		<?php endif; ?>

		<tr>
			<th scope="row">Address</th>
			<td>
				<?= isset($post['address_1']) ? $post['address_1'].'<br />' : '' ?>
				<?= isset($post['address_2']) ? $post['address_2'].'<br />' : '' ?>
				<?= isset($post['address_3']) ? $post['address_3'].'<br />' : '' ?>
				<?= isset($post['address_4']) ? $post['address_4'].'<br />' : '' ?>
				<?= isset($post['address_5']) ? $post['address_5'] : '' ?>
			</td>
		</tr>

		<?php if (isset($post['school_level']) AND $post['school_level']): ?>
			<tr>
				<th scope="row">School Level</th>
				<td><?= $post['school_level'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['current_school']) AND $post['current_school']): ?>
			<tr>
				<th scope="row">Current school</th>
				<td><?= $post['current_school'] ?></td>
			</tr>
		<?php endif; ?>

	</tbody>
</table>

<h2>Course Details</h2>
<table>
	<tbody>
		<?php if (isset($post['course_name']) AND $post['course_name']): ?>
			<tr>
				<th scope="row">Course</th>
				<td><?= $post['course_name'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if ((isset($post['schedule_name']) AND $post['schedule_name']) OR (isset($post['schedule']) AND $post['schedule'])): ?>
			<tr>
				<th scope="row">Schedule</th>
				<td><?= isset($post['schedule_id']) ? $post['schedule_id'].': ' : '' ?><?= isset($post['schedule_name']) ? $post['schedule_name'] : '' ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['course_location']) AND $post['course_location']): ?>
			<tr>
				<th scope="row">Location</th>
				<td><?= $post['course_location'] ?></td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>

<h2>Application Information</h2>

<table>
	<tbody>

		<tr>
			<th scope="row">Beginner</th>
			<td><?= (isset($post['is_beginner']) AND $post['is_beginner']) ? 'Yes' : 'No' ?></td>
		</tr>

		<?php if (isset($post['family_members_attending'])): ?>
			<tr>
				<th scope="row">Family members attending</th>
				<td><?= ($post['current_school'] == 1) ? 'Yes' : 'No' ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['has_piano'])): ?>
			<tr>
				<th scope="row">Piano at home</th>
				<td><?= ($post['has_piano'] == 1) ? 'Yes' : 'No' ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['has_previous_tuition'])): ?>
			<tr>
				<th scope="row">Previous tuition</th>
				<td><?= ($post['has_previous_tuition'] == 1) ? 'Yes' : 'No' ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['previous_tuition_details']) AND trim($post['previous_tuition_details'])): ?>
			<tr>
				<th scope="row">Details on previous tuition</th>
				<td><?= $post['previous_tuition_details'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['other_details']) AND trim($post['other_details'])): ?>
			<tr>
				<th scope="row">Other details</th>
				<td><?= $post['other_details'] ?></td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>

<h2>Parent/Guardian details</h2>
<h3>Parent/Guardian 1</h3>

<table>
	<tbody>
		<?php if ((isset($post['guardian_first_name']) AND $post['guardian_first_name']) OR (isset($post['guardian_surname']) AND $post['guardian_surname'])): ?>
			<tr>
				<th scope="row">Name</th>
				<td><?= trim(
						(isset($post['guardian_title'])?$post['guardian_title']:'').' '.
						(isset($post['guardian_first_name'])?$post['guardian_first_name']:'').' '.
						(isset($post['guardian_surname'])?$post['guardian_surname']:'')
					) ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['guardian_phone']) AND trim($post['guardian_phone'])): ?>
			<tr>
				<th scope="row">Phone</th>
				<td><?= $post['guardian_phone'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['guardian_mobile']) AND trim($post['guardian_mobile'])): ?>
			<tr>
				<th scope="row">Mobile</th>
				<td><?= $post['guardian_mobile'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['guardian_email']) AND trim($post['guardian_email'])): ?>
			<tr>
				<th scope="row">Email</th>
				<td><?= $post['guardian_email'] ?></td>
			</tr>
		<?php endif; ?>


		<?php if (isset($post['guardian_county']) AND trim($post['guardian_county'])): ?>
			<tr>
				<th scope="row">Address</th>
				<td><?= $post['guardian_county'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['guardian_emergency_number']) AND trim($post['guardian_emergency_number'])): ?>
			<tr>
				<th scope="row">Emergency Number</th>
				<td><?= $post['guardian_emergency_number'] ?></td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>


<h3>Parent/Guardian 2</h3>

<table>
	<tbody>
		<?php if ((isset($post['guardian2_first_name']) AND $post['guardian2_first_name']) OR (isset($post['guardian2_surname']) AND $post['guardian2_surname'])): ?>
			<tr>
				<th scope="row">Name</th>
				<td><?= trim(
						(isset($post['guardian2_title'])?$post['guardian2_title']:'').' '.
						(isset($post['guardian2_first_name'])?$post['guardian2_first_name']:'').' '.
						(isset($post['guardian2_surname'])?$post['guardian2_surname']:'')
					) ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['guardian2_phone']) AND trim($post['guardian2_phone'])): ?>
			<tr>
				<th scope="row">Phone</th>
				<td><?= $post['guardian2_phone'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['guardian2_mobile']) AND trim($post['guardian2_mobile'])): ?>
			<tr>
				<th scope="row">Mobile</th>
				<td><?= $post['guardian2_mobile'] ?></td>
			</tr>
		<?php endif; ?>

		<?php if (isset($post['guardian2_email']) AND trim($post['guardian2_email'])): ?>
			<tr>
				<th scope="row">Email</th>
				<td><?= $post['guardian2_email'] ?></td>
			</tr>
		<?php endif; ?>


		<?php if (isset($post['guardian2_county']) AND trim($post['guardian2_county'])): ?>
			<tr>
				<th scope="row">Address</th>
				<td><?= $post['guardian2_county'] ?></td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>

<?php if (isset($post['hear_about']) AND $post['hear_about']): ?>
	<h3>Heard about us via</h3>
	<p><?= $post['hear_about'] ?></p>
	<p><?= isset($post['hear_about_'.$post['hear_about']]) ? $post['hear_about_'.$post['hear_about']] : '' ?></p>
<?php endif; ?>
</div>