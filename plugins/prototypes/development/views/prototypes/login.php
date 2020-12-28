<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

		<?php foreach ($styles as $file => $type): ?>
			<link rel="stylesheet" type="text/css" href="<?= $file ?>" media="<?= $type ?>" />
		<?php endforeach; ?>
	</head>

	<body>
		<div class="wrapper">

			<div class="login-wrapper">
				<div class="login-header">
					<div class="login-logo">
						<img src="https://static.ideabubble.ie/engine/img/client-logo.png" height="51" />
					</div>
					<p>Please log in to continue.</p>
				</div>

				<form action="#">
					<div class="input-group input-group-m">
						<label for="login_form-email" class="input-group-addon" title="Email">
							<span class="glyphicon glyphicon-envelope"></span>
						</label>
						<input id="login_form-email" class="form-control" type="text" required placeholder="Email address" />
					</div>

					<div class="input-group input-group-m">
						<label for="login_form-password" class="input-group-addon" title="Password">
							<span class="glyphicon glyphicon-lock"></span>
						</label>
						<input id="login_form-password" class="form-control" type="password" required placeholder="Password" />
					</div>

					<div class="input-group">
						<button type="submit" class="btn btn-primary btn-login">Log In</button>
					</div>

					<div class="input-group">
						<label>
							<input type="checkbox" /> Keep me signed in for two weeks.
						</label>
					</div>
				</form>

				<hr />
				<div class="login-footer">
					<p>Don't have an account? <a href="#">Apply for one now.</a></p>
					<p><a href="#">Having problems logging in?</a></p>
				</div>
			</div>

		</div>
	</body>
</html>