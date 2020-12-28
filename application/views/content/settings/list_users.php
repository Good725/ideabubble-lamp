<?php
if (isset($alert))
{
	echo $alert;
}
?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<table id="users_table" class='table table-striped dataTable'>
	<thead>
	<tr>
		<th>User ID</th>
		<th>Email</th>
        <th>Role</th>
        <th>Created</th>
        <th>Access</th>
		<th>Last Login</th>
		<th>Email Verified</th>
		<th>Verify Email</th>
		<?php if($isPruductsLoaded){ ?>
			<th>Discount approval</th>
		<?php } ?>
		<th>&nbsp;</th>
		<?php if(Auth::instance()->has_access('login_as')): ?>
			<th>Login As User</th>
		<?php endif; ?>
		
	</tr>
	</thead>
	<tbody>
	<? foreach ($users as $user):

		// Work out what should be displayed.
        $date = Date::less_fuzzy_span((int)$user->last_login);

        $user->last_login = ! empty($user->last_login)  ?  $date   : "Never Logged In.";
        $user->can_login  = ! empty($user->can_login)   ?  "Yes"   : "No";

	?>

	<tr>
		<td><a href="<?php echo URL::Site('admin/users/edit/' . $user->id); ?>"><?php echo $user->id; ?></a></td>
		<td><a href="<?php echo URL::Site('admin/users/edit/' . $user->id); ?>"><?php echo $user->email; ?></a></td>
        <td><a href="<?php echo URL::Site('admin/users/edit/' . $user->id); ?>"><?php echo $user->role->role; ?></a></td>
        <td><a href="<?php echo URL::Site('admin/users/edit/' . $user->id); ?>"><?php echo $user->registered; ?></a></td>
        <td><a href="<?php echo URL::Site('admin/users/edit/' . $user->id); ?>"><?php echo $user->can_login; ?></a></td>
		<td><a href="<?php echo URL::Site('admin/users/edit/' . $user->id); ?>"><?php echo $user->last_login; ?></a></td>
		<td><a href="<?php echo URL::Site('admin/users/edit/' . $user->id); ?>"><?php echo $user->email_verified == 1 ? 'Yes' : 'No'; ?></a></td>
		<td><a class="resend-verify" data-user-id="<?php echo $user->id?>">resend</a></td>
		<?php if($isPruductsLoaded){ ?>
			<td><a href="<?php echo URL::Site('admin/users/edit/' . $user->id); ?>"><?php echo $user->discount_format_id ? 'Yes' : 'No'; ?></a></td>
		<?php } ?>
		<td><a class="reset-password" data-user-id="<?php echo $user->id?>">reset password</a></td>
		<?php if(Auth::instance()->has_access('login_as')): ?>
        	<td><a href="/admin/users/login_as?user_id=<?php echo $user->id?>" class="login-as-user" data-user-id="<?php echo $user->id?>">login as</a></td>
		<?php endif; ?>
	</tr>
		<? endforeach?>
	</tbody>
</table>
<script>
$(document).ready(function() {
	// Server-side datatable
	var $table = $('#users_table');

	$table.ready(function () {
		if ($table.size() > 0) {
			try {
				$table.dataTable().fnDestroy(); // Destroy the autoloaded table.
			} catch (exc) {

			}

			$table.dataTable(
					{
						"bSort": false,
						"bDestroy": false,
						"bAutoWidth": false,
						"oLanguage": {"sInfoFiltered": ""},
						"bProcessing": false,
						"aLengthMenu": [10],
						"bServerSide": true,
						"sAjaxSource": '/admin/users/datatable',
						"sPaginationType": "bootstrap",
						"aoColumnDefs": [{
							"aTargets": [1],
							"fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
								// Add data attribute, with the contact ID to each row
								$(nTd).parent().attr({'data-id': oData[0]});
								$(nTd).find('[data-toggle="popover"]').popover();
							}
						}]
					}
			);
		}
	});
});
$(document).on("click", "a.reset-password", function(){
	$.post("<?php echo URL::Site('admin/users/ajax_send_password_reset');?>",
			{user_id:$(this).data("user-id")},
			function(response){
				alert("reset_cms_password has been emailed to " + response.email);
			});
	return false;
});
$(document).on("click", "a.resend-verify", function(){
	$.post("<?php echo URL::Site('admin/users/ajax_resend_verify');?>",
			{user_id:$(this).data("user-id")},
			function(response){
				alert("Verification has been emailed to " + response.email);
			});
	return false;
});
</script>
