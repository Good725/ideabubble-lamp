<div class="grid-12">
	<div class="db-breadcrumb">
		<ul>
			<li><a href="javascript">Home / </a></li>		
			<li>Profile</li>
		</ul>
	</div>
</div>
<section class="dashboard-wrapper <?php echo $child_class;?>">
	<!-- left sidebar here -->
	<?php require_once 'sidebar_logged_in.php'; ?>
	<article class="right-section-content">
		<div class="page-title">
			<div class="title-left">
				<h1>Bernadette Thompson Profile</h1>
			</div>
		</div>
		<form action="" method="POST">
			<div class="full_colm two-colm">
				<div class="contct-dt">
					<h3>Contact Details</h3>
				</div>			
				<div class="db-form-wrap">
					<label class="lbl">First Name:</label>
					<label class="lbl-value">Bernadette</label>
				</div>
				<div class="db-form-wrap">
					<label class="lbl">Last Name:</label>
					<label class="lbl-value">Thompson</label>
				</div>
				<div class="db-form-wrap">
					<label class="lbl">Date of Birth:</label>
					<label class="lbl-value">13/06/2016</label>
				</div>
				<div class="db-form-wrap">
					<label class="lbl">Mobile:</label>
					<label class="lbl-value">+384 78 45 12</label>
				</div>
			</div>				
			<div class="full_colm">
				<div class="contct-dt">
					<h3>Contact Preferences</h3>			
				</div>
				<div class="selected-values">
					<label>Text Messaging</label>			
				</div>
				<div class="selected-values">
					<label>Phone call</label>			
				</div>
			</div>
			<div class="full_colm">	
				<div class="contct-dt">
					<h3>Account</h3>
					
				</div>
				<div class="db-form-wrap">
					<label class="lbl">Account Type :</label>
					<label class="lbl-value">Use Main Address</label>
				</div>
			</div>	

			<div class="full_colm noti-cntct-pf">
				<div class="contct-dt">
					<h3>Notification Preferences</h3>			
				</div>
				<div class="selected-values">
					<label>Emergency</label>			
				</div>
				<div class="selected-values">
					<label>Accounts</label>
				</div>
				<div class="selected-values">			
					<label>Reminders</label>			
				</div>
				<div class="selected-values">			
					<label>Time Sheet Alerts</label>			
				</div>		
			</div>
			<div class="full_colm stu-info">
				<div class="page-title">
					<div class="title-left">
						<h1>Student info</h1>
					</div>
				</div>
				<div class="left-sect">
					<div class="contct-dt">
						<h3>Current Educational Details</h3>			
					</div>
					<div class="db-form-wrap">
						<label class="lbl">Academic Year :</label>			
						<label class="lbl-value">2016 - 2017</label>
					</div>
					<div class="db-form-wrap">
						<label class="lbl">School :</label>
						<label class="lbl-value">Tralee Comunity</label>			
					</div>
					<div class="db-form-wrap">
						<label class="lbl">School Year :</label>
						<label class="lbl-value">5th Year</label>			
					</div>
					<div class="db-form-wrap">
						<label class="lbl">Subjects studied :</label>
						<label class="lbl-value">English</label>			
					</div>
				</div>
				<div class="right-sect">
					<div class="contct-dt">
						<h3>Student Considerations</h3>			
					</div>
					<div class="selected-values">
						<label>Fainting</label>
					</div>
					<div class="db-form-wrap">
						<label class="lbl">College course they want :</label>
						<label class="lbl-value">3rd level course</label>			
					</div>
					<div class="db-form-wrap">
						<label class="lbl">Points required :</label>
						<label class="lbl-value">1</label>			
					</div>
					<div class="db-form-wrap">
						<label class="lbl">Send Notification when absent :</label>
						<label class="lbl-value">Yes</label>	
		
					</div>		
				</div>
			</div>
			<div class="page-title">
				<div class="db-options-btn">
					<a class="db-save-btn" href="javascript:void">SAVE</a>
					<a class="db-cancel-btn" href="javascript:void">Cancel</a>
				</div>
			</div>	
		</form>
	</article>
</section><!-- section end -->
